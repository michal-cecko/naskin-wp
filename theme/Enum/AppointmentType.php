<?php

namespace Theme\Enum;

use Saurus\App\Traits\Helpers\EnumHelper;

enum AppointmentType: string {

    use EnumHelper;

    case RESERVATION = "reservation";
    case VACATION = "free";

}