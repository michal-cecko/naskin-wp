<?php

namespace Theme\Requests\Appointments\Admin;

use Saurus\App\Rules\Exists;
use Theme\Models\Appointment\Appointment;
use Theme\Requests\AuthenticatedAdminRequest;


class AppointmentAdminCancelRequest extends AuthenticatedAdminRequest {

    public function rules(): array {

        return [
            'id' => ['required', 'integer', new Exists(Appointment::getTableName(), "id")],

            'notify_customer' => ['required', 'boolean'],
            'notify_employee' => ['required', 'boolean'],
        ];

    }

}