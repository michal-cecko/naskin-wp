<?php

namespace Theme\Exceptions\Appointment;

use Saurus\App\Exceptions\Request\RequestException;

class AppointmentInvalidDatetimeDifferenceException extends RequestException
{
    public function __construct($message = "Rozdiel medzi dátumami musí byť aspoň 5 minút.") {
        parent::__construct($message, 400);
    }
}