<?php

namespace Theme\Exceptions\Customer;

use Saurus\App\Exceptions\Request\RequestException;
use Theme\PostTypes\Customer;

class CustomerHasEmptyEmailException extends RequestException
{
    public function __construct(Customer $customer) {
        $message = "Zákazník {$customer->title} (ID: {$customer->id}) má prázdny email.";
        parent::__construct($message, 500);
    }
}