<?php

namespace Theme\Requests\Appointments\Admin;

use Illuminate\Validation\Rule;
use Saurus\App\Rules\Exists;
use Saurus\App\Rules\PostExists;
use Theme\Enum\AppointmentSource;
use Theme\Enum\AppointmentType;
use Theme\PostTypes\Customer;
use Theme\PostTypes\Service;
use Theme\Requests\AuthenticatedAdminRequest;

class AppointmentAdminStoreRequest extends AuthenticatedAdminRequest {

    public function rules(): array {

        return [
            'employeeID' => ['required', 'integer', new Exists("users", "ID")],

            'date' => 'required|array',
            'date.start' => 'required|date',
            'date.end' => 'required|date',

            'type' => ['required', 'in:' . implode(",", AppointmentType::stringCases())],

            'source' => ['required', 'in:' . implode(",", AppointmentSource::stringCases())],

            'services' => ['required_if:type,' . AppointmentType::RESERVATION->value, 'array'],
            'services.*' => ['required_if:type,' . AppointmentType::RESERVATION->value, 'integer', new PostExists(postModel: Service::class)],

            'customer' => ['required_if:type,' . AppointmentType::RESERVATION->value, 'array'],
            'customer.id' => ['sometimes', 'nullable', 'integer', new PostExists(postModel: Customer::class)],
            'customer.name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'customer.email' => ['sometimes', 'nullable', 'max:255'],
            'customer.phone' => ['sometimes', 'nullable', 'string', 'max:255'],

            'note' => 'sometimes|nullable|string|max:1000',

            'notify' => ['required', 'boolean']
        ];

    }

}