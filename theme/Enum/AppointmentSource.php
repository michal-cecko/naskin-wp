<?php

namespace Theme\Enum;

use Saurus\App\Traits\Helpers\EnumHelper;

enum AppointmentSource: string {

    use EnumHelper;

    case IN_PERSON = "in_person";
    case PHONE = "phone";
    case WEB = "web";

}