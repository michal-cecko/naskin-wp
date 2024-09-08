<?php

namespace Theme\Services\Notifications\Email;

use Exception;
use Theme\Enum\AppointmentCustomerNotificationType;
use Theme\Enum\AppointmentEmployeeNotificationType;
use Theme\Enum\AppointmentType;
use Theme\Exceptions\Appointment\AppointmentEmailNotificationNotImplementedException;
use Theme\Exceptions\Email\EmailFailedToSendException;
use Theme\Interfaces\INotificationService;
use Theme\Mail\Appointments\Customer\AppointmentCancelledCustomer;
use Theme\Mail\Appointments\Customer\AppointmentCreatedCustomer;
use Theme\Mail\Appointments\Customer\AppointmentRemindCustomer;
use Theme\Mail\Appointments\Customer\AppointmentUpdatedCustomer;
use Theme\Mail\Appointments\Employee\Reservation\AppointmentCancelledEmployee;
use Theme\Mail\Appointments\Employee\Reservation\AppointmentCreatedEmployee;
use Theme\Mail\Appointments\Employee\Reservation\AppointmentUpdatedEmployee;
use Theme\Mail\Appointments\Employee\Vacation\VacationCancelled;
use Theme\Mail\Appointments\Employee\Vacation\VacationCreated;
use Theme\Mail\Appointments\Employee\Vacation\VacationUpdated;
use Theme\Models\Appointment\Appointment;
use Theme\Services\Customers\CustomerService;

class EmailService implements INotificationService
{
    /**
     * @throws Exception
     */
    public static function notifyCustomer(Appointment $appointment, AppointmentCustomerNotificationType $type, array $additionalData = []): bool
    {
        $mailable = match ($type) {
            AppointmentCustomerNotificationType::CREATED => new AppointmentCreatedCustomer($appointment, $additionalData),
            AppointmentCustomerNotificationType::UPDATED => new AppointmentUpdatedCustomer($appointment, $additionalData),
            AppointmentCustomerNotificationType::CANCELLED => new AppointmentCancelledCustomer($appointment, $additionalData),
            AppointmentCustomerNotificationType::REMIND => new AppointmentRemindCustomer($appointment, $additionalData),
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
    public static function notifyEmployee(Appointment $appointment, AppointmentEmployeeNotificationType $type, array $additionalData = []): bool
    {
        $isReservation = $appointment->type === AppointmentType::RESERVATION;

        $mailable = match ($type) {
            AppointmentEmployeeNotificationType::CREATED => $isReservation ? new AppointmentCreatedEmployee($appointment, $additionalData) : new VacationCreated($appointment, $additionalData),
            AppointmentEmployeeNotificationType::UPDATED => $isReservation ? new AppointmentUpdatedEmployee($appointment, $additionalData) : new VacationUpdated($appointment, $additionalData),
            AppointmentEmployeeNotificationType::CANCELLED => $isReservation ? new AppointmentCancelledEmployee($appointment, $additionalData) : new VacationCancelled($appointment, $additionalData),
            default => throw new Exception('Unsupported email type for employee notification: ' . $type->value)
        };

        $email = $appointment->employee?->email;
        if (empty($email)) {
            return false;
        }

        $adminEmail = get_field('reservations_email', 'option');

        $adminSent = empty($adminEmail) || main()->mail()->send($mailable, $adminEmail);
        $employeeSent = main()->mail()->send($mailable, $email);

        return $adminSent && $employeeSent;
    }
}