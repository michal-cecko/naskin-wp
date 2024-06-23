<?php

namespace Theme\Modules\Appointments;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Exception;
use Saurus\App\Enums\ApiMethod;
use Saurus\App\Traits\Validation;
use Theme\Enum\AppointmentSource;
use Theme\Enum\AppointmentStatus;
use Theme\Enum\AppointmentType;
use Theme\Models\Appointment\Appointment;
use Theme\PostTypes\Service;
use Theme\Requests\Appointments\Admin\AppointmentAdminCancelRequest;
use Theme\Requests\Appointments\Admin\AppointmentAdminStoreRequest;
use Theme\Requests\Appointments\Admin\AppointmentAdminTableRequest;
use Theme\Requests\Appointments\Admin\AppointmentAdminUpdateRequest;
use Theme\Services\Appointments\AppointmentService;
use Theme\Users\Employee;

class AdminAppointments
{
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
                customer: $data['customer'],
                endAt: Carbon::parse($data['date']['end']),
                note: $data['note'],
                services: $services,
                source: AppointmentSource::getCaseFromValue($data['source']),
                notifyCustomer: $data['notify'],
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
                employee: (int)$data['employeeID'],
                startAt: Carbon::parse($data['date']['start']),
                endAt: Carbon::parse($data['date']['end']),
                note: $data['note'],
                services: $services,
                source: AppointmentSource::getCaseFromValue($data['source']),
                notifyCustomer: $data['notify'],
            );

        } else {

            $appointment = AppointmentService::updateVacation(
                appointment: (int)$data['id'],
                employee: (int)$data['employeeID'],
                startAt: Carbon::parse($data['date']['start']),
                endAt: Carbon::parse($data['date']['end']),
                note: $data['note'],
            );
        }

        wp_send_json_success(['message' => 'Termín bol úspešne upravený.', 'id' => $appointment->id]);
    }


    public function cancelAdmin(AppointmentAdminCancelRequest $request): void
    {
        $data = $request->validated();

        $notify = !empty((int)$data['notify']);

        $appointment = Appointment::find($data['id']);

        AppointmentService::cancelAppointment(appointment: $appointment, notifyCustomer: $notify, isCancelledByEmployee: true);

        wp_send_json_success(['message' => 'Termín bol úspešne zrušený.']);
    }


    public function tableAdmin(AppointmentAdminTableRequest $request): void
    {
        $data = $request->validated();

        $datetime = Carbon::parse($data['date'])->modify("+1 hour");

        //Week
        if ($data['dateRange'] === "timeGridWeek") {
            $dateToFetchFrom = $datetime->startOfWeek(CarbonInterface::MONDAY)->format("Y-m-d H:i:s");
            $dateToFetchTo = $datetime->endOfWeek(CarbonInterface::SUNDAY)->format("Y-m-d H:i:s");
        } //Day
        else {
            $dateToFetchFrom = $datetime->startOfDay()->format("Y-m-d H:i:s");
            $dateToFetchTo = $datetime->endOfDay()->format("Y-m-d H:i:s");
        }

        $employee = intval($data['employeeID']) > 0 ? Employee::where("ID", $data['employeeID'])->first() : null;

        $appointments = Appointment::where(function ($query) use ($dateToFetchFrom, $dateToFetchTo) {
            $query->whereBetween("start_at", [$dateToFetchFrom, $dateToFetchTo])
                ->orWhereBetween("end_at", [$dateToFetchFrom, $dateToFetchTo]);
        })->where("status", AppointmentStatus::OK)->when($employee, function ($query) use ($employee) {
            $query->whereIn("employee_id", [$employee->id, ...$employee->mutual_calendar_blocking_employees]);
        })->with(["employee", 'services.service', 'customer'])->get();

        $return = [];

        foreach ($appointments ?? [] as $appointment) {

            if (!$appointment->employee) continue;

            if ($employee && $appointment->employee_id !== $employee->id && $appointment->type === AppointmentType::VACATION) continue;

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

                if (!$appointment->customer) {
                    main()->log()->error("Customer for app: {$appointment->id} was not found");
                    continue;
                }

                $return[$appointment->id] = [
                    'type' => $appointment->type->value,
                    'source' => $appointment->source?->value ?? null,
                    'employee' => $appointment->employee->first_name,
                    'employeeID' => $appointment->employee->ID,
                    'services' => $appointment->services->append("service_category_id"),
                    'break' => $appointment->break,
                    'datetime' => [
                        'from' => $appointment->start_at->format("Y-m-d H:i:s"),
                        'to' => $appointment->end_at->format("Y-m-d H:i:s"),
                        'to_with_break' => $appointment->end_at_with_break->format("Y-m-d H:i:s"),
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
