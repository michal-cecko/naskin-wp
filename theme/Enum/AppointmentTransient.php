<?php

namespace Theme\Enum;

use Saurus\App\Traits\Helpers\EnumHelper;

enum AppointmentTransient: string {

    use EnumHelper;

    case FRONTEND_GETTER = "frontend_appointment_getter";

}