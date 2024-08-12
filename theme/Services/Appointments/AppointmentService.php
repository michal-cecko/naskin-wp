<?php

namespace Theme\Services\Appointments;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Saurus\App\Traits\ModelDirtyPropsTracker;
use Theme\Enum\AppointmentEmailType;
use Theme\Enum\AppointmentSource;
use Theme\Enum\AppointmentStatus;
use Theme\Enum\AppointmentType;

use Theme\Exceptions\Appointment\AppointmentEmailNotificationNotImplementedException;
use Theme\Exceptions\Appointment\AppointmentInvalidDatetimeDifferenceException;
use Theme\Exceptions\Appointment\AppointmentNotFoundException;
use Theme\Exceptions\Email\EmailFailedToSendException;
use Theme\Exceptions\Employee\EmployeeNotFoundException;
use Theme\Helpers\ModelRelationsHelper;
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
    public static function createReservation(int $employeeID, Carbon $startAt, iterable $customer, Carbon $endAt = null, ?string $note = null, iterable $services = [], iterable $payments = [], AppointmentSource $source = AppointmentSource::IN_PERSON, bool $notifyCustomer = false, bool $notifyEmployee = false): Appointment
    {
        if (empty($customer['id'])) {
            $customer = CustomerService::createCustomer($customer['name'], $customer['email'], $customer['phone'] ?? null);
        } else {
            $customer = Customer::where("ID", $customer['id'])->first();
        }

        self::checkAppointmentTimeDifference($startAt, $endAt);

        [$duration, $endAt] = self::calculateDuration($startAt, $endAt, $services);

        $appointment = Appointment::create([
            'employee_id' => $employeeID,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'total' => self::calculateTotal($services),
            'break' => AppointmentService::getBreakForDuration($duration),
            'customer_id' => $customer->id,
            'note' => $note,
            'source' => $source,
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

        $appointment->load(["employee", "services", "payments", "customer"]);

        self::syncPaymentsWithAppointment($appointment, $payments);

        if ($notifyCustomer) {
            if (!self::notifyCustomer($appointment, AppointmentEmailType::CREATED)) {
                main()->log()->errorDB("Nepodarilo sa odoslať email o vytvorení rezervácie zákazníkovi na email: {$appointment->customer->email}, Rezervácia: {$appointment->log_string}", resources: [$appointment, $appointment->customer]);
            }
        }

        if ($notifyEmployee) {
            if (!self::notifyEmployee($appointment, AppointmentEmailType::CREATED)) {
                main()->log()->errorDB("Nepodarilo sa odoslať email o vytvorení rezervácie na pracovníkov email: {$appointment->employee->email}, Rezervácia: {$appointment->log_string}", resources: [$appointment, $appointment->employee]);
            }
        }

        return $appointment;

    }

    /**
     * @throws Exception
     */
    public static function updateReservation(Appointment|int $appointment, Employee|int $employee, Carbon $startAt, Carbon $endAt = null, ?string $note = null, iterable $services = [], iterable $payments = [], AppointmentSource $source = AppointmentSource::IN_PERSON, bool $notifyCustomer = false): array
    {
        $eager = ["services", "customer", "payments", "employee"];

        if(is_int($appointment)) {
            $appointment = Appointment::where("id", $appointment)->where("status", AppointmentStatus::OK)->with($eager)->first();
            if(!$appointment) throw new AppointmentNotFoundException();
        } else {
            $appointment->load($eager);
        }

        $employee = self::getEmployee($employee);

        [$duration, $endAt] = self::calculateDuration($startAt, $endAt, $services);

        $appointment->fill([
            'employee_id' => $employee->ID,
            'total' => self::calculateTotal($services),
            'break' => self::getBreakForDuration($duration),
            'start_at' => $startAt,
            'source' => $source,
            'end_at' => $endAt,
            'note' => $note,
        ]);
        $changes = $appointment->getChangedColumns();
        $appointment->save();

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

        self::syncPaymentsWithAppointment($appointment, $payments);

        if ($notifyCustomer) {
            if(!self::notifyCustomer($appointment, AppointmentEmailType::UPDATED)) {
                main()->log()->errorDB("Nepodarilo sa odoslať email o upravení rezervácie zákazníkovi na email: {$appointment->customer->email}, Rezervácia: {$appointment->log_string}", resources: [$appointment, $appointment->customer]);
            }
        }

        return ['appointment' => $appointment, 'changes' => $changes];

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

    /**
     * @throws AppointmentInvalidDatetimeDifferenceException
     */
    public static function createVacation(int $employeeID, Carbon $startAt, Carbon $endAt, ?string $note = null): Appointment
    {
        self::checkAppointmentTimeDifference($startAt, $endAt);

        $appointment = Appointment::create([
            'employee_id' => $employeeID,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'status' => AppointmentStatus::OK,
            'note' => $note,
            'type' => AppointmentType::VACATION,
        ]);

        return $appointment;
    }

    /**
     * @throws Exception
     */
    public static function updateVacation(Appointment|int $appointment, int|Employee $employee, Carbon $startAt, Carbon $endAt = null, ?string $note = null): array
    {
        if(is_int($appointment)) {
            $appointment = Appointment::where("id", $appointment)->where("status", AppointmentStatus::OK)->first();
            if(!$appointment) throw new AppointmentNotFoundException();
        }

        if(is_int($employee)) {
            $employee = Employee::where("id", $employee)->first();
            if(!$employee) throw new EmployeeNotFoundException();
        }

        if ($endAt !== null && $startAt->diffInMinutes($endAt, false) < 5) {
            throw new AppointmentInvalidDatetimeDifferenceException();
        }

        $appointment->fill([
            'employee_id' => $employee->ID,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'note' => $note,
        ]);
        $changes = $appointment->getChangedColumns();
        $appointment->save();

        return ['appointment' => $appointment, 'changes' => $changes];
    }

    public static function generateCancelToken(): string
    {
        do {
            $token = Str::random(128);
        } while(Appointment::where("cancel_token", $token)->first());

        return $token;
    }

    /**
     * @throws Exception
     */
    public static function notifyCustomer(Appointment $appointment, AppointmentEmailType $type, array $additionalData = []): bool
    {
        $mailable = match ($type) {
            AppointmentEmailType::CREATED => new AppointmentCreatedCustomer($appointment, $additionalData),
            AppointmentEmailType::UPDATED => new AppointmentUpdatedCustomer($appointment, $additionalData),
            AppointmentEmailType::CANCELLED => new AppointmentCancelledCustomer($appointment, $additionalData),
            AppointmentEmailType::REMIND => new AppointmentRemindCustomer($appointment, $additionalData),
            default => throw new AppointmentEmailNotificationNotImplementedException($type->value)
        };

        $email = CustomerService::checkCustomerEmail($appointment->customer);

        if(!main()->mail()->send($mailable, $email)) {
            throw new EmailFailedToSendException($email);
        }

        return true;
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
                $query->whereDate('start_at', '>=', $currentDate->toDateString())
                    ->orWhereDate('end_at', '>=', $currentDate->toDateString());
            })->whereIn('employee_id', [$currentEmployee->id, ...$currentEmployee->mutual_calendar_blocking_employees])
                ->where("status", AppointmentStatus::OK)
                ->orderBy("id", "DESC")
                ->get();

            $obsadeneArr = [];
            foreach ($appointments as $appointment) {

                if($appointment->type === AppointmentType::VACATION && $appointment->employee_id !== $currentEmployee->id) {
                    continue;
                }

                $startAt = $appointment->start_at;
                $endAt = $appointment->end_at_with_break;

                if (!$startAt->isSameDay($endAt)) {
                    $currentDay = $startAt->copy();
                    $endOfDay = $currentDay->copy()->endOfDay();
                    $startOfEndDay = $endAt->copy()->startOfDay();

                    // First day
                    $obsadeneArr[$currentDay->format("Y-m-d")][] = [
                        'start' => $startAt->format("H:i"),
                        'end' => $endOfDay->format("H:i")
                    ];

                    // Intermediate days
                    while ($currentDay->addDay()->startOfDay()->isBefore($startOfEndDay)) {
                        $obsadeneArr[$currentDay->format("Y-m-d")][] = [
                            'start' => '00:00',
                            'end' => '23:59'
                        ];
                    }

                    // Last day
                    $obsadeneArr[$endAt->format("Y-m-d")][] = [
                        'start' => '00:00',
                        'end' => $endAt->format("H:i")
                    ];
                } else {
                    $obsadeneArr[$startAt->format("Y-m-d")][] = [
                        'start' => $startAt->format("H:i"),
                        'end' => $endAt->format("H:i")
                    ];
                }
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

                    //2hrs added bcs of Slovakia timezone
                    if($currentTime->lt(Carbon::now()->addHours(2))) {
                        continue;
                    }

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

    /**
     * @throws Exception
     */
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

    /**
     * @throws Exception
     */
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

        main()->ics()->setData(
            start: $appointment->start_at,
            end: $appointment->end_at,
            name: $name,
            location: get_field("address", "options")
        );

        main()->ics()->show();
    }

    /**
     * @throws AppointmentInvalidDatetimeDifferenceException
     */
    private static function checkAppointmentTimeDifference(Carbon $startAt, ?Carbon $endAt): void
    {
        if ($endAt !== null && $startAt->diffInMinutes($endAt) < 5) {
            throw new AppointmentInvalidDatetimeDifferenceException();
        }
    }

    /**
     * @throws AppointmentInvalidDatetimeDifferenceException
     */
    private static function durationAtleast5Minutes(float $duration): void
    {
        if ($duration < 5) {
            throw new AppointmentInvalidDatetimeDifferenceException();
        }
    }

    /**
     * @throws AppointmentInvalidDatetimeDifferenceException
     */
    private static function calculateDuration(Carbon $startAt, ?Carbon $endAt, iterable $services): array
    {
        if(!$endAt) {
            $duration = $services->sum("duration");
            $endAt = $startAt->copy()->addMinutes($duration);
        } else {
            $duration = $startAt->diffInMinutes($endAt);
        }

        self::durationAtleast5Minutes($duration);

        return [$duration, $endAt];
    }

    /**
     * @throws EmployeeNotFoundException
     */
    private static function getEmployee(Employee|int $employee): Employee|int
    {
        if(is_int($employee)) {
            $employee = Employee::where("id", $employee)->first();

            if(!$employee) throw new EmployeeNotFoundException();
        }

        return $employee;
    }

    public static function syncPaymentsWithAppointment($appointment, iterable $payments = []) : void
    {
        $submittedPaymentIDs = collect($payments)->pluck("id")->filter();

        $appointment->loadMissing("payments");

        if (!empty($payments)) {
            foreach ($payments as $paymentData) {
                if (isset($paymentData['id'])) {
                    if ($currentPayment = $appointment->payments->where("id", $paymentData['id'])->first()) {
                        $currentPayment->update($paymentData);
                    }
                } else {
                    $currentPayment = $appointment->payments()->create($paymentData);
                    $submittedPaymentIDs->push($currentPayment->id);
                }
            }
        }

        $appointment->touch();

        // Delete any options that were not present in the submitted data -- they have been deleted on FE
        ModelRelationsHelper::deleteNotSubmittedRelatedRecords($appointment, 'payments', $submittedPaymentIDs);
    }

    public static function calculateTotal(iterable $appointmentServices): int {
        return collect($appointmentServices)->sum("price");
    }
}