<?php

namespace Theme\Requests\Appointments\Admin;

use Illuminate\Validation\Rule;
use Saurus\App\Requests\Request;
use Saurus\App\Rules\Exists;
use Saurus\App\Rules\PostExists;
use Saurus\App\Rules\RecaptchaPasses;
use Theme\Enum\AppointmentType;
use Theme\PostTypes\Customer;
use Theme\PostTypes\Service;

class AppointmentAdminCancelRequest extends AuthenticatedAdminRequest {

    public function rules(): array {

        return [
            'id' => ['required', 'integer', new Exists("appointments", "id")],

            'notify' => ['required', 'boolean']
        ];

    }

}