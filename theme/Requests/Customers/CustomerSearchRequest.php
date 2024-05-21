<?php

namespace Theme\Requests\Customers;

use Saurus\App\Rules\Exists;
use Theme\Requests\AuthenticatedAdminRequest;


class CustomerSearchRequest extends AuthenticatedAdminRequest {

    public function rules(): array {

        return [
            'search' => ['required', 'string'],
        ];

    }

}