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

class AppointmentStoreRequest extends Request {
    public function rules(): array {

        return [
            'employees' => ['required', 'array'],
            'employees.*' => ['required', 'integer', new Exists("wp_users", "ID")],

            'date' => 'required|date|max:255',
            'time' => 'required|string|max:255',

            'services' => ['required', 'array'],
            'services.*' => ['required', 'integer', new PostExists(postModel: Service::getPostTypeSlug())],

            'customer' => ['required', 'array'],
            'customer.name' => ['required', 'string', 'max:255'],
            'customer.email' => ['required', 'email:rfc,dns', 'max:255'],
            'customer.phone' => ['required', 'string', 'max:255'],
            'customer.note' => ['sometimes', 'nullable', 'string', 'max:255'],

            'recaptcha' => [new RecaptchaPasses],
        ];

    }

}