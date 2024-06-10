<?php

namespace Theme\Exceptions\Appointment;

use Exception;
use Saurus\App\Exceptions\Request\RequestException;

class AppointmentEmailNotificationNotImplementedException extends RequestException
{
    public function __construct($type) {
        $message = "Typ notifikácie \"$type\" nieje implementovaný.";
        parent::__construct($message, 501);
    }
}