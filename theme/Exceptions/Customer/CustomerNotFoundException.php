<?php

namespace Theme\Exceptions\Customer;

use Exception;
use Saurus\App\Exceptions\Request\RequestException;

class CustomerNotFoundException extends RequestException
{
    public function __construct($message = "Zákazník nebol nájdený.") {
        parent::__construct($message, 400);
    }
}