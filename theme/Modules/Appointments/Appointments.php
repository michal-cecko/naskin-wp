<?php

namespace Theme\Modules\Appointments;

use Exception;
use Saurus\App\Enums\ApiMethod;
use Saurus\App\Requests\Appointments\AppointmentCreateRequest;
use Saurus\App\Traits\Validation;
use Theme\Enum\AppointmentType;
use Theme\Services\Appointments\AppointmentService;

class Appointments
{
    use Validation;

    public function __construct()
    {
        $this->initRest();
    }

    private function initRest(): void
    {
        main()->api()->addApiEndpoint(ApiMethod::POST, "/appointment", "appointment.store-admin", [$this, 'storeAdmin']);
    }

    /**
     * @throws Exception
     */
    public function storeAdmin(AppointmentCreateRequest $request): void
    {
        $data = $request->validated();

        if ($data['type'] === AppointmentType::RESERVATION->value) {

            $appointment = AppointmentService::createReservation(
                employeeID: $data['employee_id'],
                date: $data['date']['start'],
                customer: $data['customer'],
                note: $data['note'],
                services: $data['services'],
                notifyCustomer: true,
                notifyEmployee: false
            );

        } else {

            $appointment = AppointmentService::createVacation(
                employeeID: $data['employee_id'],
                date: $data['date']['start'],
                note: $data['note'],
            );

        }
    }
}