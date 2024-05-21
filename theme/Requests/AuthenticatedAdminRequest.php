<?php

namespace Theme\Requests;

use Illuminate\Validation\Rule;
use Saurus\App\Requests\Request;
use Saurus\App\Rules\Exists;
use Saurus\App\Rules\PostExists;
use Saurus\App\Rules\RecaptchaPasses;
use Theme\Enum\AppointmentType;
use Theme\PostTypes\Customer;
use Theme\PostTypes\Service;

abstract class AuthenticatedAdminRequest extends Request {
    public function authorize() : bool
    {
        if (is_wp_error(main()->api()->getUserViaSessionCookie())) {
            return false;
        }

        return true;
    }
}