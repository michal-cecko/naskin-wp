<?php

namespace Theme\Requests\Appointments;

use Saurus\App\Requests\Request;
use Saurus\App\Rules\PostExists;
use Theme\PostTypes\Service;

class AppointmentListAvailableDates extends Request {
    public function rules(): array {
        return [
            'employee_id' => ['required', 'integer'],
            'services' => ['required', 'array', 'min:1'],
            'services.*' => [new PostExists(postModel: Service::class)],
        ];
    }
}