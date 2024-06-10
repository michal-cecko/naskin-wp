<?php

namespace Theme\Exceptions\Appointment;

use Exception;
use Saurus\App\Exceptions\Request\RequestException;

class AppointmentNotFoundException extends RequestException
{
    public function __construct($message = "Termín nebol nájdený.") {
        parent::__construct($message, 400);
    }
}