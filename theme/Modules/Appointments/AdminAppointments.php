<?php

namespace Theme\Modules\Appointments;

use Carbon\Carbon;
use Exception;
use Saurus\App\Enums\ApiMethod;
use Saurus\App\Traits\Validation;
use Theme\Enum\AppointmentType;
use Theme\Models\Appointment\Appointment;
use Theme\PostTypes\Service;
use Theme\Requests\Appointments\Admin\AppointmentAdminCancelRequest;
use Theme\Requests\Appointments\Admin\AppointmentAdminStoreRequest;
use Theme\Services\Appointments\AppointmentService;

class AdminAppointments {
    use Validation;

    public function __construct()
    {
        $this->initRest();
    }

    private function initRest(): void
    {
        main()->api()->addApiEndpoint(ApiMethod::GET, "/appointment/cancel-admin", "appointment.admin-cancel", [$this, 'cancelAdmin']);
        main()->api()->addApiEndpoint(ApiMethod::POST, "/appointment/store-admin", "appointment.admin-store", [$this, 'storeAdmin']);
    }

    /**
     * @throws Exception
     */
    public function storeAdmin(AppointmentAdminStoreRequest $request): void
    {
        $data = $request->validated();

        if ($data['type'] === AppointmentType::RESERVATION->value) {

            $services = Service::whereIn("id", $data['services'])->get();

            $appointment = AppointmentService::createReservation(
                employeeID: $data['employee_id'],
                startAt: Carbon::parse($data['date']['start']),
                customer: $data['customer'],
                note: $data['note'],
                services: $services,
                notifyCustomer: true,
                notifyEmployee: false
            );

        } else {

            $appointment = AppointmentService::createVacation(
                employeeID: $data['employee_id'],
                startAt: Carbon::parse($data['date']['start']),
                endAt: Carbon::parse($data['date']['start']),
                note: $data['note'],
            );

        }

        wp_send_json_success(['message' => 'Termín bol úspešne vytvorený.']);
    }

    public function removeAppointment(AppointmentAdminCancelRequest $request): void
    {
        $data = $request->validated();

        $notify = !empty((int) $data['notify']);

        $appointment = Appointment::find($data['id']);

        AppointmentService::cancelAppointment(appointment: $appointment, notifyCustomer: $notify);
    }
}
