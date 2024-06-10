<?php

namespace Theme\Exceptions\Employee;

use Saurus\App\Exceptions\Request\RequestException;

class EmployeeNotFoundException extends RequestException
{
    public function __construct($message = "Pracovník nebol nájdený.") {
        parent::__construct($message, 400);
    }
}