<?php

namespace Theme\Requests\Appointments;

use Illuminate\Validation\Rule;
use Saurus\App\Requests\Request;
use Saurus\App\Rules\Exists;
use Saurus\App\Rules\PostExists;
use Saurus\App\Rules\RecaptchaPasses;
use Theme\Enum\AppointmentType;
use Theme\Models\Appointment\Appointment;
use Theme\PostTypes\Customer;
use Theme\PostTypes\Service;

class AppointmentICSRequest extends Request {
    public function authorize(): bool
    {
        $data = $this->validated();

        if($data['t'] !== config("appointments.cron-ics-token")) {
            return false;
        }

        return true;
    }

    public function rules(): array {

        return [
            'id' => ['required', new Exists(Appointment::getTableName(), "id")],
            't' => ['required'],
        ];

    }
}