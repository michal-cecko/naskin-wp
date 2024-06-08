<?php

namespace Theme\Requests\Appointments\Admin;

use Theme\Requests\AuthenticatedAdminRequest;

class AppointmentAdminTableRequest extends AuthenticatedAdminRequest {

    public function rules(): array {

        return [
            'employeeID' => ['sometimes', 'nullable', 'integer'],
            'date' => ['required', 'date'],
            'dateRange' => ['required', 'string']
        ];

    }

}