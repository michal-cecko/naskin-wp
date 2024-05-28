<?php

namespace Theme\Services\Appointments;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Theme\Enum\AppointmentEmailType;
use Theme\Enum\AppointmentStatus;
use Theme\Enum\AppointmentType;

use Theme\Mail\Appointments\Customer\AppointmentCancelledCustomer;
use Theme\Mail\Appointments\Customer\AppointmentCreatedCustomer;
use Theme\Mail\Appointments\Customer\AppointmentRemindCustomer;
use Theme\Mail\Appointments\Customer\AppointmentUpdatedCustomer;
use Theme\Mail\Appointments\Employee\AppointmentCancelledEmployee;
use Theme\Mail\Appointments\Employee\AppointmentCreatedEmployee;
use Theme\Models\Appointment\Appointment;
use Theme\PostTypes\Customer;
use Theme\PostTypes\Service;
use Theme\Services\Customers\CustomerService;
use Theme\Services\Employees\EmployeeService;
use Theme\Users\Employee;

class AppointmentService
{
    /**
     * @throws Exception
     */
    public static function createReservation(int $employeeID, Carbon $startAt, iterable $customer, Carbon $endAt = null, ?string $note = null, iterable $services = [], bool $notifyCustomer = false, bool $notifyEmployee = false): Appointment
    {

        if (empty($customer['id'])) {
            $customer = CustomerService::createCustomer($customer['name'], $customer['email'], $customer['phone'] ?? null);
        } else {
            $customer = Customer::where("ID", $customer['id'])->first();
        }

        if(!$endAt) {
            $duration = $services->sum("duration");
            $endAt = $startAt->copy()->addMinutes($duration);
        } else {
            $duration = $startAt->diffInMinutes($endAt);
        }

        $appointment = Appointment::create([
            'employee_id' => $employeeID,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'break' => AppointmentService::getBreakForDuration($duration),
            'customer_id' => $customer->id,
            'note' => $note,
            'status' => AppointmentStatus::OK,
            'type' => AppointmentType::RESERVATION,
            'cancel_token' => self::generateCancelToken()
        ]);

        foreach ($services as $service) {
            self::addServiceToAppointment($appointment, $service);
        }

        $lastAppointment = get_field('cust_last-appointment', $customer->ID);
        $lastAppointmentC = Carbon::parse($lastAppointment);
        if (empty($lastAppointment) || $lastAppointmentC->lt($appointment->start_at)) {
            CustomerService::updateLastAppointmentDate($customer, $appointment->start_at->format("Y-m-d H:i"));
        }

        $appointment->load(["employee", "services"]);

        if ($notifyCustomer) {
            if (!self::notifyCustomer($appointment, AppointmentEmailType::CREATED)) {
                main()->log()->warning("Failed to send type::CREATED email to customer for appointment with ID: {$appointment->id}");
            }
        }

        if ($notifyEmployee) {
            if (!self::notifyEmployee($appointment, AppointmentEmailType::CREATED)) {
                main()->log()->warning("Failed to send type::CREATED email to customer for appointment with ID: {$appointment->id}");
            }
        }

        return $appointment;

    }

    /**
     * @throws Exception
     */
    public static function updateReservation(Appointment|int $appointment, Employee|int $employee, Carbon $startAt, Carbon $endAt = null, ?string $note = null, iterable $services = [], bool $notifyCustomer = false): ?Appointment
    {
        $eager = ["services", "customer"];

        if(is_int($appointment)) {
            $appointment = Appointment::where("id", $appointment)->where("status", AppointmentStatus::OK)->with($eager)->first();
            if(!$appointment) return null;
        } else {
            $appointment->load($eager);
        }

        if(is_int($employee)) {
            $employee = Employee::where("id", $employee)->first();
            if(!$employee) return null;
        }

        $appointment->update([
            'employee_id' => $employee->ID,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'note' => $note,
        ]);

        if(!$appointment->start_at->equalTo($startAt)) {
            $lastAppointment = get_field('cust_last-appointment', $appointment->customer->ID);
            $lastAppointmentC = Carbon::parse($lastAppointment);
            if (empty($lastAppointment) || $lastAppointmentC->lt($appointment->start_at)) {
                CustomerService::updateLastAppointmentDate($appointment->customer, $appointment->start_at->format("Y-m-d H:i"));
            }
        }

        $appointmentServiceIds = $appointment->services->pluck("service_id");
        $submittedServiceIds = $services->pluck("id");
        foreach ($services as $submittedService) {
            if(!$appointmentServiceIds->contains($submittedService->id)) {
                if(self::addServiceToAppointment($appointment, $submittedService)) {
                    $appointmentServiceIds->push($submittedService->id);
                }
            }
        }
        $appointment->services()->whereNotIn("appointment_services.service_id", $submittedServiceIds->toArray())->delete();

        if ($notifyCustomer) {
            if (!self::notifyCustomer($appointment, AppointmentEmailType::UPDATED)) {
                main()->log()->warning("Failed to send type::UPDATED email to customer for appointment with ID: {$appointment->id}");
            }
        }

        return $appointment;

    }

    public static function addServiceToAppointment(Appointment $appointment, Service $service): ?\Theme\Models\Appointment\AppointmentService
    {
        return \Theme\Models\Appointment\AppointmentService::create([
            'appointment_id' => $appointment->id,
            'service_id' => $service->id,
            'duration' => $service->duration,
            'name' => $service->title,
            'price' => $service->price,
        ]);
    }

    public static function createVacation(int $employeeID, Carbon $startAt, Carbon $endAt, ?string $note = null): Appointment
    {
        $appointment = Appointment::create([
            'employee_id' => $employeeID,
            'start_at' => $startAt,
            'end_at' => $startAt,
            'status' => AppointmentStatus::OK,
            'note' => $note,
            'type' => AppointmentType::VACATION,
        ]);

        return $appointment;
    }

    public static function updateVacation(Appointment|int $appointment, Carbon $startAt, Carbon $endAt = null, ?string $note = null): ?Appointment
    {
        if(is_int($appointment)) {
            $appointment = Appointment::where("id", $appointment)->where("status", AppointmentStatus::OK)->first();
            if(!$appointment) return null;
        }

        $appointment->update([
            'start_at' => $startAt,
            'end_at' => $endAt,
            'note' => $note,
        ]);

        return $appointment;
    }

    public static function generateCancelToken(): string
    {
        do {
            $token = Str::random(12);
        } while(Appointment::where("cancel_token", $token)->first());

        return $token;
    }

    /**
     * @throws Exception
     */
    public static function notifyCustomer(Appointment $appointment, AppointmentEmailType $type, array $additionalData = []): bool
    {
        try {
            $mailable = match ($type) {
                AppointmentEmailType::CREATED => new AppointmentCreatedCustomer($appointment, $additionalData),
                AppointmentEmailType::UPDATED => new AppointmentUpdatedCustomer($appointment, $additionalData),
                AppointmentEmailType::CANCELLED => new AppointmentCancelledCustomer($appointment, $additionalData),
                AppointmentEmailType::REMIND => new AppointmentRemindCustomer($appointment, $additionalData),
                default => throw new Exception('Unsupported email type for customer notification: ' . $type->value)
            };
        } catch (Exception $th) {
            main()->log()->error($th->getMessage());
            return false;
        }

        $email = $appointment->customer?->email;

        if (empty($email)) {
            return false;
        }

        return main()->mail()->send($mailable, $email);
    }

    /**
     * @throws Exception
     */
    public static function notifyEmployee(Appointment $appointment, AppointmentEmailType $type, array $additionalData = []): bool
    {
        try {
            $mailable = match ($type) {
                AppointmentEmailType::CREATED => new AppointmentCreatedEmployee($appointment, $additionalData),
                AppointmentEmailType::CANCELLED => new AppointmentCancelledEmployee($appointment, $additionalData),
                default => throw new Exception('Unsupported email type for employee notification: ' . $type->value)
            };
        } catch (Exception $th) {
            main()->log()->error($th->getMessage());
            return false;
        }

        $email = $appointment->employee?->email;
        if (empty($email)) {
            return false;
        }

        $adminEmail = get_field('reservations_email', 'option');

        $adminSent = empty($adminEmail) || main()->mail()->send($mailable, $adminEmail);
        $employeeSent = main()->mail()->send($mailable, $email);

        return $adminSent && $employeeSent;
    }

    public static function getAvailableDates(Collection $services, string|Employee $employee = "ANY"): Collection
    {
        $serviceDuration = $services->sum("duration");
        $employees = EmployeeService::getEmployeesWithServices($services);
        $weekenedWork = get_field('weekend_work', 'option') ?? ['saturday' => false, 'sunday' => false];
        $daysAvailableToReserve = get_field('days_available_to_reserve', 'option') ?? 60;
        $breaks = self::getBreaks();
        $finalDates = [];
        $b = 0;
        while (($employee === "ANY" && $b < count($employees)) || ($employee instanceof Employee && $b < 1)) :
            $currentDate = Carbon::now()->startOfDay();

            if ($employee === "ANY") {
                $currentEmployee = $employees[$b];
            } else {
                $currentEmployee = $employee;
            }

            $appointments = Appointment::where(function ($query) use ($currentEmployee, $currentDate) {
                $query->where('employee_id', $currentEmployee->id)
                    ->whereDate('start_at', '>=', $currentDate->toDateString())
                    ->orWhereDate('end_at', '>', $currentDate->toDateString());
            })->where("employee_id", $currentEmployee->id)
                ->where("status", AppointmentStatus::OK)
                ->get();

            $obsadeneArr = [];
            foreach ($appointments as $appointment) {
                $startAt = $appointment->start_at;
                $endAt = $appointment->end_at_with_break;

                $obsadeneArr[$startAt->format("Y-m-d")][] = ['start' => $startAt->format("H:i"), "end" => $endAt->format("H:i")];
            };

            //Decrementing here one day, so I can modify date +1 day at the start of the loop
            $date = $currentDate->modify("-1 day");

            for ($i = 0; $i < $daysAvailableToReserve; $i++) :
                $date = $currentDate->modify("+1 day");
                $dateFormat = $currentDate->format("Y-m-d");

                //echo "day" . $date->format("d.m.Y") . "\n";

                //Saturday work
                if (!($weekenedWork['saturday'] ?? false) && (int)$date->format("N") === 6) {
                    $finalDates[$currentDate->format("n")][$dateFormat] = [];
                    continue;
                };

                //Sunday work
                if (!($weekenedWork['sunday'] ?? false) && (int)$date->format("N") === 7) {
                    $finalDates[$currentDate->format("n")][$dateFormat] = [];
                    continue;
                };

                $currentDate = $date;

                $obsadene = $obsadeneArr[$dateFormat] ?? false;
                // Work start time is current time minus 30 minutes

                $currentTime = Carbon::createFromFormat('Y-m-d H:i', $dateFormat . " " . $currentEmployee->worktime['start']);
                // Work end time
                $workEnd = Carbon::createFromFormat('Y-m-d H:i', $dateFormat . " " . $currentEmployee->worktime['end']);
                // Work end time minus service duration
                $workEndWhile = Carbon::createFromFormat('Y-m-d H:i', $dateFormat . " " . $currentEmployee->worktime['end'])->subMinutes($serviceDuration)->format('H:i');
                // Lunch start time
                $lunchStart = !empty($currentEmployee->lunchtime['start']) ? Carbon::createFromFormat('Y-m-d H:i', $dateFormat . " " . $currentEmployee->lunchtime['start'])->format('H:i') : null;
                // Lunch end time
                $lunchEnd = !empty($currentEmployee->lunchtime['end']) ? Carbon::createFromFormat('Y-m-d H:i', $dateFormat . " " . $currentEmployee->lunchtime['end'])->format('H:i') : null;

                //echo "Work from: " . $currentEmployee->worktime['start'] . " to " . $currentEmployee->worktime['end'] . "\n";
                //echo "Lunch from: " . $lunchStart . " to " . $lunchEnd . "\n";

                while ($currentTime->format("H:i") < $workEndWhile) :

                    $currentStart = $currentTime->format("H:i");
                    $currentEnd = $currentTime->modify("+" . $serviceDuration . " minutes")->modify("+" . self::getBreakForDuration($serviceDuration, $breaks) . " minutes")->format("H:i");

                    //echo "termin: from" . $currentStart . " to $currentEnd\n";
                    if ($lunchStart && $lunchEnd) {
                        $canEnd = $currentEnd <= $lunchStart || $currentStart >= $lunchEnd;
                        //echo !$canEnd ? "lunch broke this. \n" : "";
                        if (!$canEnd) {
                            continue;
                        }
                    }

                    $ok = true;
                    if (!empty($obsadene)) {
                        foreach ($obsadene as $time) {
                            $terminStart = $time['start'];
                            $terminEnd = $time['end'];

                            $canEnd = $currentEnd <= $terminStart || $currentStart >= $terminEnd;
                            //echo "$currentEnd <= $terminStart " . " || " . " $currentStart >= $terminEnd \n";
                            //echo !$canEnd ? "obsadenie broke this. \n" : "";

                            if (!$canEnd) {
                                $ok = false;
                                break;
                            }
                        }
                    }

                    $timeToAdd = [];
                    $termin = $finalDates[$currentDate->format("n")][$dateFormat]['apps'][$currentStart] ?? [];

                    // termin is available
                    if ($ok) {
                        $timeToAdd['isAvailable'] = 1;
                        $timeToAdd['employees'] = [$currentEmployee->id];
                    } // termin is not available && is not set
                    else if (empty($termin)) {
                        $timeToAdd['isAvailable'] = 0;
                        $timeToAdd['employees'] = [];
                    }

                    if (!empty($timeToAdd)) {
                        if (!empty($termin) && !empty($termin['employees'])) {
                            if (($timeToAdd['employees'][0] ?? false) && !in_array($timeToAdd['employees'][0], $termin['employees'])) {
                                $timeToAdd['employees'] = array_merge($timeToAdd['employees'], $termin['employees']);
                            }
                        }

                        $finalDates[$currentDate->format("n")][$dateFormat]['apps'][$currentStart] = $timeToAdd;
                    }

                    if ($finalDates[$currentDate->format("n")][$dateFormat]['isAvailable'] != 1 && $ok) {
                        $finalDates[$currentDate->format("n")][$dateFormat]['isAvailable'] = 1;
                    }
                endwhile;

                if (!isset($finalDates[$currentDate->format("n")][$dateFormat]['isAvailable'])) {
                    $finalDates[$currentDate->format("n")][$dateFormat]['isAvailable'] = 0;
                }
            endfor;

            $b++;
        endwhile;

        return collect($finalDates);
    }

    public static function getBreaks() {
        $breaks = get_field("breaks_after_appointment", "option") ?? [];

        usort($breaks, function($a, $b) {
            return $a['duration'] <=> $b['duration'];
        });

        return $breaks;
    }

    public static function getBreakForDuration($duration, $breaks = []): int
    {
        if(empty($breaks)) {
            $breaks = self::getBreaks();
        }

        foreach ($breaks as $break) {
            if($duration <= $break['duration']) {
                return intval($break['break']);
            }
        }

        return 0;
    }

    public static function checkCancelToken(Appointment $appointment, string $token): bool
    {
        return $appointment->cancel_token === $token;
    }

    public static function cancelAppointment($appointment, bool $notifyCustomer = false, bool $notifyEmployee = false, $isCancelledByEmployee = false): void
    {
        if($appointment->type === AppointmentType::VACATION) {
            $appointment->delete();
            return;
        }

        $appointment->update(['status' => AppointmentStatus::CANCELLED]);

        if($notifyEmployee) {
            self::notifyEmployee($appointment, AppointmentEmailType::CANCELLED, ['isCancelledByEmployee' => $isCancelledByEmployee]);
        }

        if($notifyCustomer) {
            self::notifyCustomer($appointment, AppointmentEmailType::CANCELLED, ['isCancelledByEmployee' => $isCancelledByEmployee]);
        }
    }

    public static function notifyAllAppointments() : int {
        $appointments = Appointment::where("has_been_reminded", false)
            ->where("status", AppointmentStatus::OK)
            ->where("type", AppointmentType::RESERVATION)
            ->whereDate('start_at', '<=', Carbon::now()->addDay()->toDateString())
            ->whereDate('start_at', '>', Carbon::now()->subDay()->toDateString())
            ->get();

        if(!$appointments->count()) {
            return 0;
        }

        $notified = 0;
        foreach ($appointments as $appointment) {
            if(self::notifyCustomer($appointment, AppointmentEmailType::REMIND)) {
                $appointment->update(['has_been_reminded' => true]);
                $notified++;
            }
        }

        return $notified;
    }

    public static function generateICS(Appointment $appointment): void
    {
        $name = "NASKIN - Rezervácia";

        theme()->ics()->setData(
            start: $appointment->start_at,
            end: $appointment->end_at,
            name: $name,
            location: get_field("address", "options")
        );

        theme()->ics()->show();
    }
}