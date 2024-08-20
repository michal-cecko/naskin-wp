<?php

namespace Theme\Enum;

use Saurus\App\Traits\Helpers\EnumHelper;

enum AppointmentCustomerNotificationType: string {

    use EnumHelper;

    case CREATED = "created";
    case UPDATED = "updated";
    case CANCELLED = "cancelled";
    case REMIND = "remind";

}