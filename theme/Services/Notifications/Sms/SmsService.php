<?php

namespace Theme\Services\Notifications\Sms;

use Exception;
use Theme\Enum\AppointmentCustomerNotificationType;
use Theme\Enum\AppointmentEmployeeNotificationType;
use Theme\Interfaces\INotificationService;
use Theme\Models\Appointment\Appointment;

class SmsService implements INotificationService
{
    /**
     * @throws Exception
     */
    public static function notifyCustomer(Appointment $appointment, AppointmentCustomerNotificationType $type, array $additionalData = []): bool
    {
        $view = "sms.appointments.customer." . strtolower($type->value) . "-appointment-customer";

        $content = templates()->generate($view, array_merge($additionalData, ['appointment' => $appointment]));

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

        $content = templates()->generate($view, array_merge($additionalData, ['appointment' => $appointment]));

        $sender = new SmsSender();

        $phoneNumber = $appointment->employee?->phone ?? null;

        return $sender->send($content, $phoneNumber);
    }
}