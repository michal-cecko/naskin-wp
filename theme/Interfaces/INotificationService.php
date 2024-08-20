<?php

namespace Theme\Interfaces;

use Theme\Enum\AppointmentCustomerNotificationType;
use Theme\Enum\AppointmentEmployeeNotificationType;
use Theme\Models\Appointment\Appointment;

interface INotificationService
{
    public static function notifyCustomer(Appointment $appointment, AppointmentCustomerNotificationType $type, array $additionalData = []): bool;
    public static function notifyEmployee(Appointment $appointment, AppointmentEmployeeNotificationType $type, array $additionalData = []): bool;
}