<?php

namespace Theme\Enum;

use Saurus\App\Traits\Helpers\EnumHelper;

enum AppointmentEmployeeNotificationType: string {

    use EnumHelper;

    case CREATED = "created";
    case CANCELLED = "cancelled";

}