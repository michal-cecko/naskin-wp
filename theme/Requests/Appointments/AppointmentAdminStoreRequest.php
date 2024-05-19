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

class AppointmentAdminStoreRequest extends Request {

    public function authorize() : bool {
        if(is_wp_error(main()->api()->getUserViaSessionCookie())) {
            return false;
        }

        return true;
    }

    public function rules(): array {

        return [
            'employee_id' => ['required', 'integer', new Exists("wp_users", "ID")],

            'date' => 'required|array',
            'date.start' => 'required|date',
            'date.end' => 'required|date',

            'type' => ['required', Rule::enum(AppointmentType::class)],

            'services' => ['required_if:type,' . AppointmentType::RESERVATION->value, 'array'],
            'services.*' => ['required_if:type,' . AppointmentType::RESERVATION->value, 'integer', new PostExists(postModel: Service::getPostTypeSlug())],

            'customer' => ['required_if:type,' . AppointmentType::RESERVATION->value, 'array'],
            'customer.id' => ['sometimes', 'nullable', 'integer', new PostExists(postModel: Customer::getPostTypeSlug())],
            'customer.name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'customer.email' => ['sometimes', 'nullable', 'email:rfc,dns', 'max:255'],
            'customer.phone' => ['sometimes', 'nullable', 'string', 'max:255'],

            'note' => 'required|string|max:1000',
        ];

    }

}