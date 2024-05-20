<?php

namespace Theme\Requests\Appointments;

use Illuminate\Validation\Rule;
use Saurus\App\Requests\Request;
use Saurus\App\Rules\Exists;
use Saurus\App\Rules\PostExists;
use Saurus\App\Rules\RecaptchaPasses;
use Theme\Enum\AppointmentType;
use Theme\PostTypes\Customer;
use Theme\PostTypes\Service;

class AppointmentCancelRequest extends Request {
    public function rules(): array {

        return [
            'i' => ['required'],
            't' => ['required'],
        ];

    }

}