<?php

namespace Theme\Requests\Appointments\Admin;

use Illuminate\Validation\Rule;
use Saurus\App\Rules\Exists;
use Saurus\App\Rules\PostExists;
use Theme\Enum\AppointmentPaymentType;
use Theme\Enum\AppointmentSource;
use Theme\Enum\AppointmentType;
use Theme\PostTypes\Customer;
use Theme\PostTypes\Service;
use Theme\Requests\AuthenticatedAdminRequest;

class AppointmentAdminUpdateRequest extends AuthenticatedAdminRequest {

    public function rules(): array {

        $reservation = AppointmentType::RESERVATION->value;

        return [
            'id' => ['required', 'integer', new Exists(table: "appointments")],

            'employeeID' => ['required', 'integer', new Exists(table: "users", column: "ID")],

            'date' => 'required|array',
            'date.start' => 'required|date',
            'date.end' => 'required|date',

            'type' => ['required', 'in:' . implode(",", AppointmentType::stringCases())],

            'source' => ['required_if:type,' . $reservation, 'in:' . implode(",", AppointmentSource::stringCases())],

            'services' => ['required_if:type,' . $reservation, 'array'],
            'services.*' => ['required_if:services,array', 'integer', new PostExists(postModel: Service::class)],

            'payments' => ['sometimes', 'array', "min:0", "nullable"],
            'payments.*.id' => ['sometimes', 'integer', new Exists(table: "appointment_payments")],
            'payments.*.amount' => ['required_if:type,' . $reservation, 'decimal:0,2'],
            'payments.*.type' => ['required_if:type,' . $reservation, 'in:' . implode(",", AppointmentPaymentType::stringCases())],
            'payments.*.note' => ['sometimes', 'nullable', 'string', 'max:255'],

            'note' => 'sometimes|nullable|string|max:1000',

            'notify' => ['required', 'boolean']
        ];

    }

}