<?php

namespace Theme\Modules\Appointments;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Exception;
use Saurus\App\Enums\ApiMethod;
use Saurus\App\Traits\Validation;
use Theme\Enum\AppointmentStatus;
use Theme\Enum\AppointmentType;
use Theme\Models\Appointment\Appointment;
use Theme\PostTypes\Service;
use Theme\Requests\Appointments\Admin\AppointmentAdminCancelRequest;
use Theme\Requests\Appointments\Admin\AppointmentAdminStoreRequest;
use Theme\Requests\Appointments\Admin\AppointmentAdminTableRequest;
use Theme\Requests\Appointments\Admin\AppointmentAdminUpdateRequest;
use Theme\Services\Appointments\AppointmentService;

class AdminAppointments {
    use Validation;

    public function __construct()
    {
        $this->initRest();
    }

    private function initRest(): void
    {
        main()->api()->addApiEndpoint(ApiMethod::GET, "/appointment/table", "appointment.admin-table", [$this, 'tableAdmin']);
        main()->api()->addApiEndpoint(ApiMethod::POST, "/appointment/cancel-admin", "appointment.admin-cancel", [$this, 'cancelAdmin']);
        main()->api()->addApiEndpoint(ApiMethod::POST, "/appointment/store-admin", "appointment.admin-store", [$this, 'storeAdmin']);
        main()->api()->addApiEndpoint(ApiMethod::POST, "/appointment/edit-admin", "appointment.admin-edit", [$this, 'editAdmin']);
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
                employeeID: $data['employeeID'],
                startAt: Carbon::parse($data['date']['start']),
                endAt: Carbon::parse($data['date']['end']),
                customer: $data['customer'],
                note: $data['note'],
                services: $services,
                notifyCustomer: $data['notify'] === 1,
                notifyEmployee: false,
            );

        } else {

            $appointment = AppointmentService::createVacation(
                employeeID: $data['employeeID'],
                startAt: Carbon::parse($data['date']['start']),
                endAt: Carbon::parse($data['date']['end']),
                note: $data['note'],
            );

        }

        wp_send_json_success(['message' => 'Termín bol úspešne vytvorený.', 'id' => $appointment->id]);
    }


    public function editAdmin(AppointmentAdminUpdateRequest $request): void
    {
        $data = $request->validated();

        if ($data['type'] === AppointmentType::RESERVATION->value) {

            $services = Service::whereIn("id", $data['services'])->get();

            $appointment = AppointmentService::updateReservation(
                appointment: (int)$data['id'],
                startAt: Carbon::parse($data['date']['start']),
                endAt: Carbon::parse($data['date']['end']),
                note: $data['note'],
                services: $services,
                notifyCustomer: $data['notify'] === 1,
            );

        } else {

            $appointment = AppointmentService::updateVacation(
                appointment: (int)$data['id'],
                startAt: Carbon::parse($data['date']['start']),
                endAt: Carbon::parse($data['date']['end']),
                note: $data['note'],
            );

        }

        wp_send_json_success(['message' => 'Termín bol úspešne upravený.', 'appointment' => $appointment, 'type' => $data['type']]);
    }


    public function cancelAdmin(AppointmentAdminCancelRequest $request): void
    {
        $data = $request->validated();

        $notify = !empty((int) $data['notify']);

        $appointment = Appointment::find($data['id']);

        AppointmentService::cancelAppointment(appointment: $appointment, notifyCustomer: $notify);

        wp_send_json_success(['message' => 'Termín bol úspešne zrušený.']);
    }



    public function tableAdmin(AppointmentAdminTableRequest $request): void
    {
        $data = $request->validated();

        $datetime = Carbon::createFromTimestamp(floor($data['timestamp'] / 1000))->modify("+1 hour");

        //Week
        if ($data['dateRange'] === "timeGridWeek") {
            $dateToFetchFrom = $datetime->startOfWeek(CarbonInterface::MONDAY)->format("Y-m-d H:i:s");
            $dateToFetchTo = $datetime->endOfWeek(CarbonInterface::SUNDAY)->format("Y-m-d H:i:s");
        } //Day
        else {
            $dateToFetchFrom = $datetime->startOfDay()->format("Y-m-d H:i:s");
            $dateToFetchTo = $datetime->endOfDay()->format("Y-m-d H:i:s");
        }

        $appointments = Appointment::where(function ($query) use ($dateToFetchFrom, $dateToFetchTo) {
            $query->whereBetween("start_at", [$dateToFetchFrom, $dateToFetchTo])
                ->orWhereBetween("end_at", [$dateToFetchFrom, $dateToFetchTo]);
        })->where("status", AppointmentStatus::OK)->when($data['employeeID'] > 0, function ($query) use ($data) {
            $query->where("employee_id", $data['employeeID']);
        })->with(["employee", 'services.service', 'customer'])->get();

        $return = [];

        foreach ($appointments ?? [] as $appointment) {

                if (!$appointment->employee) continue;

                if ($appointment->type === AppointmentType::VACATION) {
                    $return[$appointment->id] = [
                        'type' => $appointment->type->value,
                        'employee' => $appointment->employee->first_name,
                        'employeeID' => $appointment->employee->ID,
                        'note' => $appointment->note,
                        'datetime' => [
                            'from' => $appointment->start_at->format("Y-m-d H:i:s"),
                            'to' => $appointment->end_at->format("Y-m-d H:i:s")
                        ]
                    ];
                } else {
                    $return[$appointment->id] = [
                        'type' => $appointment->type->value,
                        'employee' => $appointment->employee->first_name,
                        'employeeID' => $appointment->employee->ID,
                        'services' => $appointment->services->append("service_category_id"),
                        'datetime' => [
                            'from' => $appointment->start_at->format("Y-m-d H:i:s"),
                            'to' => $appointment->end_at->format("Y-m-d H:i:s")
                        ],
                        'customer' => [
                            'id' => $appointment->customer_id,
                            'name' => $appointment->customer->name,
                            'email' => $appointment->customer->email,
                            'phone' => $appointment->customer->phone,
                        ],
                        'note' => $appointment->note,
                    ];
                }
            }

        wp_send_json_success(["appointments" => $return], 200);
    }
}
