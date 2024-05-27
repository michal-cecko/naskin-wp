<?php

namespace Theme\Requests\Appointments\Admin;

use Illuminate\Validation\Rule;
use Saurus\App\Rules\Exists;
use Saurus\App\Rules\PostExists;
use Theme\Enum\AppointmentType;
use Theme\PostTypes\Customer;
use Theme\PostTypes\Service;
use Theme\Requests\AuthenticatedAdminRequest;

class AppointmentAdminUpdateRequest extends AuthenticatedAdminRequest {

    public function rules(): array {

        return [
            'id' => ['required', 'integer', new Exists("appointments", "id")],

            'employeeID' => ['required', 'integer', new Exists("users", "ID")],

            'date' => 'required|array',
            'date.start' => 'required|date',
            'date.end' => 'required|date',

            'type' => ['required', 'in:' . implode(",", AppointmentType::stringCases())],

            'services' => ['sometimes', 'array'],
            'services.*' => ['required_if:services,array', 'integer', new PostExists(postModel: Service::class)],

            'note' => 'sometimes|nullable|string|max:1000',

            'notify' => ['required', 'boolean']
        ];

    }

}