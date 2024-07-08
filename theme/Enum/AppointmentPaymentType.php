<?php

namespace Theme\Enum;

use Saurus\App\Traits\Helpers\EnumHelper;

enum AppointmentPaymentType : string
{
    use EnumHelper;

    case CASH = 'c';
    case GIFTCARD = 'g';
    case BANK_CARD = 'b';
}
