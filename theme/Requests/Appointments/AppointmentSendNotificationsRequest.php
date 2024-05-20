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

class AppointmentSendNotificationsRequest extends Request {
    public function authorize(): bool
    {
        $data = $this->validated();

        if($data['t'] !== config("appointments.cron-notifications-token")) {
            return false;
        }

        return true;
    }

    public function rules(): array {

        return [
            't' => ['required'],
        ];

    }

}