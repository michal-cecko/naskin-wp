<?php

namespace Theme\Services\Appointments;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Saurus\App\Services\Customers\CustomerService;
use Theme\Enum\AppointmentEmailType;
use Theme\Enum\AppointmentType;
use Theme\Mail\AppointmentCancelledCustomer;
use Theme\Mail\AppointmentCancelledEmployee;
use Theme\Mail\AppointmentCreatedCustomer;
use Theme\Mail\AppointmentCreatedEmployee;
use Theme\Mail\AppointmentRemindCustomer;
use Theme\Mail\AppointmentUpdatedCustomer;
use Theme\Models\Appointment\Appointment;
use Theme\PostTypes\Customer;

class AppointmentService
{
    /**
     * @throws Exception
     */
    public static function createReservation(int $employeeID, string $date, iterable $customer, ?string $note = null, iterable $services = [], bool $notifyCustomer = false, bool $notifyEmployee = false): Appointment {

        if(empty($customer['id'])) {
            $customer = CustomerService::createCustomer($customer['name'], $customer['email'], $customer['phone'] ?? null);
        } else {
            $customer = Customer::find($customer['id']);
        }

        $appointment = Appointment::create([
            'employee_id' => $employeeID,
            'date' => $date,
            'customer_id' => $customer->id,
            'note' => $note,
            'type' => AppointmentType::RESERVATION,
            'cancel_token' => self::generateCancelToken()
        ]);

        $appointment->load(["employee", "services"]);

        $lastAppointment = Carbon::parse(get_field('cust_last-appointment', $customer->ID));
        if($lastAppointment->lt($appointment->date)) {
            CustomerService::updateLastAppointmentDate($customer, $appointment->date->format("Y-m-d H:i"));
        }

        $appointment->services()->attach($services);

        if($notifyCustomer) {
            if(!self::notifyCustomer($appointment, AppointmentEmailType::CREATED)) {
                main()->log()->warning("Failed to send type::CREATED email to customer for appointment with ID: {$appointment->id}");
            }
        }

        if($notifyEmployee) {
            if(!self::notifyCustomer($appointment, AppointmentEmailType::CREATED)) {
                main()->log()->warning("Failed to send type::CREATED email to customer for appointment with ID: {$appointment->id}");
            }
        }

        return $appointment;

    }

    public static function createVacation(int $employeeID, string $date, ?string $note = null) : Appointment
    {
        $appointment = Appointment::create([
            'employee_id' => $employeeID,
            'date' => $date,
            'note' => $note,
            'type' => AppointmentType::VACATION,
        ]);

        return $appointment;
    }

    private static function generateCancelToken(): string
    {
        return Str::random(12);
    }

    /**
     * @throws Exception
     */
    public static function notifyCustomer(Appointment $appointment, AppointmentEmailType $type) : bool
    {
        try {
            $mailable = match ($type) {
                AppointmentEmailType::CREATED => new AppointmentCreatedCustomer($appointment),
                AppointmentEmailType::UPDATED => new AppointmentUpdatedCustomer($appointment),
                AppointmentEmailType::CANCELLED => new AppointmentCancelledCustomer($appointment),
                AppointmentEmailType::REMIND => new AppointmentRemindCustomer($appointment),
                default => throw new Exception('Unsupported email type for customer notification: ' . $type->value)
            };
        } catch (Exception $th) {
            main()->log()->error($th->getMessage());
            return false;
        }

        $email = $appointment->customer?->email;

        if(empty($email)) {
            return false;
        }

        return main()->mail()->send($mailable, $email);
    }

    /**
     * @throws Exception
     */
    public static function notifyEmployee(Appointment $appointment, AppointmentEmailType $type) : bool
    {
        try {
            $mailable = match ($type) {
                AppointmentEmailType::CREATED => new AppointmentCreatedEmployee($appointment),
                AppointmentEmailType::CANCELLED => new AppointmentCancelledEmployee($appointment),
                default => throw new Exception('Unsupported email type for employee notification: ' . $type->value)
            };
        } catch (Exception $th) {
            main()->log()->error($th->getMessage());
            return false;
        }

        $email = $appointment->customer?->email;

        if(empty($email)) {
            return false;
        }

        return main()->mail()->send($mailable, $email);
    }
}