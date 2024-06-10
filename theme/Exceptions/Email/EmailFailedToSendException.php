<?php

namespace Theme\Exceptions\Email;

use Saurus\App\Exceptions\Request\RequestException;

class EmailFailedToSendException extends RequestException
{
    public function __construct($email)
    {
        $message = "Nepodarilo sa odoslať email na email \"$email\".";
        parent::__construct($message, 400);
    }
}