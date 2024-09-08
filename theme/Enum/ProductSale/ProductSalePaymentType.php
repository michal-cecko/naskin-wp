<?php

namespace Theme\Enum\ProductSale;

use Saurus\App\Traits\Helpers\EnumHelper;

enum ProductSalePaymentType : string
{
    use EnumHelper;

    case CASH = 'cash';
    case BANK_CARD = 'bank_card';
}
