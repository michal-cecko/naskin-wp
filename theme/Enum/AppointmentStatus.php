<?php

namespace Theme\Enum;

use Saurus\App\Traits\Helpers\EnumHelper;

enum AppointmentStatus: string {

    use EnumHelper;

    case OK = "ok";
    case CANCELLED = "cancelled";

}