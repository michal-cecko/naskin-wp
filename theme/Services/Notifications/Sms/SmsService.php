<?php

namespace Theme\Services\Notifications\Sms;

use BulkGate\Sdk\Message\Sms;
use Exception;
use Theme\Enum\AppointmentCustomerNotificationType;
use Theme\Enum\AppointmentEmployeeNotificationType;
use Theme\Exceptions\Appointment\AppointmentEmailNotificationNotImplementedException;
use Theme\Exceptions\Email\EmailFailedToSendException;
use Theme\Interfaces\INotificationService;
use Theme\Mail\Appointments\Customer\AppointmentCancelledCustomer;
use Theme\Mail\Appointments\Customer\AppointmentCreatedCustomer;
use Theme\Mail\Appointments\Customer\AppointmentRemindCustomer;
use Theme\Mail\Appointments\Customer\AppointmentUpdatedCustomer;
use Theme\Mail\Appointments\Employee\AppointmentCancelledEmployee;
use Theme\Mail\Appointments\Employee\AppointmentCreatedEmployee;
use Theme\Models\Appointment\Appointment;
use Theme\Services\Customers\CustomerService;
use Theme\Services\Sms\SmsSender;

class SmsService implements INotificationService
{
    /**
     * @throws Exception
     */
    public static function notifyCustomer(Appointment $appointment, AppointmentCustomerNotificationType $type, array $additionalData = []): bool
    {
        $view = "sms.appointments.customer." . strtolower($type->value) . "-appointment-customer";

        $content = templates()->generate($view, [$appointment, $additionalData]);

        $sender = new SmsSender();

        $phoneNumber = $appointment->customer?->phone ?? null;

        if(empty($phoneNumber)) {
            return false;
        }

        return $sender->send($content, $phoneNumber);
    }

    /**
     * @throws Exception
     */
    public static function notifyEmployee(Appointment $appointment, AppointmentEmployeeNotificationType $type, array $additionalData = []): bool
    {
        $view = "sms.appointments.employee." . strtolower($type->value) . "-appointment-employee";

        $content = templates()->generate($view, [$appointment, $additionalData]);

        $sender = new SmsSender();

        $phoneNumber = $appointment->employee?->phone ?? null;

        return $sender->send($content, $phoneNumber);
    }
}