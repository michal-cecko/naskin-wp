<?php

namespace Theme\Requests\Appointments\Admin;

use Saurus\App\Rules\Exists;
use Theme\Requests\AuthenticatedAdminRequest;


class AppointmentAdminCancelRequest extends AuthenticatedAdminRequest {

    public function rules(): array {

        return [
            'id' => ['required', 'integer', new Exists("appointments", "id")],

            'notify' => ['required', 'boolean']
        ];

    }

}