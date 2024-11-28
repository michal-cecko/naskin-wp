<?php

namespace Theme\Exceptions\Appointment;

use Saurus\App\Exceptions\Request\RequestException;

class AppointmentOverlapException extends RequestException
{
    public function __construct($message = "Vybraný termín je už obsadený.") {
        parent::__construct($message, 400);
    }
}