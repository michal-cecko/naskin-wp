<?php

namespace Theme\Requests\Appointments\Admin;

use Theme\Requests\AuthenticatedAdminRequest;

class AppointmentAdminTableRequest extends AuthenticatedAdminRequest {

    public function rules(): array {

        return [
            'employeeID' => ['sometimes', 'nullable', 'integer'],
            'timestamp' => ['required', 'integer'],
            'dateRange' => ['required', 'string']
        ];

    }

}