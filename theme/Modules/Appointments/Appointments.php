<?php

namespace Theme\Modules\Appointments;

use Exception;
use Saurus\App\Enums\ApiMethod;
use Saurus\App\Traits\Validation;
use Theme\Enum\AppointmentType;
use Theme\PostTypes\Service;
use Theme\Requests\Appointments\AppointmentAdminStoreRequest;
use Theme\Requests\Appointments\AppointmentListAvailableDates;
use Theme\Requests\Appointments\AppointmentStoreRequest;
use Theme\Services\Appointments\AppointmentService;
use Theme\Users\Employee;

class Appointments
{
    use Validation;

    public function __construct()
    {
        $this->initRest();
    }

    private function initRest(): void
    {
        main()->api()->addApiEndpoint(ApiMethod::POST, "/appointment/available-dates", "appointment.available_dates", [$this, 'getAvailableDates']);
        main()->api()->addApiEndpoint(ApiMethod::POST, "/appointment/store-admin", "appointment.store-admin", [$this, 'storeAdmin']);
        main()->api()->addApiEndpoint(ApiMethod::POST, "/appointment/store", "appointment.store", [$this, 'store']);
    }

    public function getAvailableDates(AppointmentListAvailableDates $request) : void {
        $data = $request->validated();

        $employee = $data['employee_id'] === -1 ? "ANY" : Employee::find($data['employee_id']);
        $services = Service::whereIn("id", $data['services'])->get();

        if(!$employee || empty($services)) {
            wp_send_json_error(__('Nebol nájdený pracovník alebo služby.', THEME_DOMAIN), 404);
        }

        $dates = AppointmentService::getAvailableDates($services, $employee);

        wp_send_json_success($dates);
    }

    /**
     * @throws Exception
     */
    public function storeAdmin(AppointmentAdminStoreRequest $request): void
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

    public function store(AppointmentStoreRequest $request): void
    {
        $data = $request->validated();

        wp_send_json_success($appointment);
    }
}