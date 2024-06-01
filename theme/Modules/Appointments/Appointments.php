<?php

namespace Theme\Modules\Appointments;

use Carbon\Carbon;
use Exception;
use Saurus\App\Enums\ApiMethod;
use Saurus\App\Traits\Validation;
use Theme\Enum\AppointmentSource;
use Theme\Enum\AppointmentType;
use Theme\Models\Appointment\Appointment;
use Theme\PostTypes\Service;
use Theme\Requests\Appointments\Admin\AppointmentAdminStoreRequest;
use Theme\Requests\Appointments\AppointmentCancelRequest;
use Theme\Requests\Appointments\AppointmentICSRequest;
use Theme\Requests\Appointments\AppointmentListAvailableDates;
use Theme\Requests\Appointments\AppointmentSendNotificationsRequest;
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
        main()->api()->addApiEndpoint(ApiMethod::GET, "/appointment/ics", "appointment.ics", [$this, 'generateAppointmentICS']);
        main()->api()->addApiEndpoint(ApiMethod::GET, "/appointment/send-notifications", "appointment.send-notifications", [$this, 'sendNotifications']);
        main()->api()->addApiEndpoint(ApiMethod::GET, "/appointment/cancel", "appointment.customer-cancel", [$this, 'cancelAppointment']);
        main()->api()->addApiEndpoint(ApiMethod::POST, "/appointment/available-dates", "appointment.available_dates", [$this, 'getAvailableDates']);
        main()->api()->addApiEndpoint(ApiMethod::POST, "/appointment/store", "appointment.store", [$this, 'store']);
    }

    public function getAvailableDates(AppointmentListAvailableDates $request) : void {
        $data = $request->validated();

        $employee = $data['employee_id'] === -1 ? "ANY" : Employee::where("ID", $data['employee_id'])->first();
        $services = Service::whereIn("id", $data['services'])->get();

        if(!$employee || empty($services)) {
            wp_send_json_error(__('Nebol nájdený pracovník alebo služby.', THEME_DOMAIN), 404);
        }

        $dates = AppointmentService::getAvailableDates($services, $employee);

        wp_send_json_success($dates);
    }

    public function store(AppointmentStoreRequest $request): void
    {
        $data = $request->validated();

        $services = Service::whereIn("id", $data['services'])->get();
        $startAt = Carbon::parse($data['date'] . " " . $data['time']);

        //Pick one employee if random
        $employeeID = $data['employees'][array_rand($data['employees'])];

        $appointment = AppointmentService::createReservation(
            employeeID: $employeeID,
            startAt: $startAt,
            customer: $data['customer'],
            note: $data['note'] ?? null,
            services: $services,
            source: AppointmentSource::WEB,
            notifyCustomer: true,
            notifyEmployee: true
        );

        if(!$appointment) {
            wp_send_json_error(__('Nastala chyba pri vytváraní rezervácie. Dajte nám o tom vedieť, prosím.', THEME_DOMAIN), 500);
        }

        wp_send_json_success("Rezervácia bola úspešne vytvorená. Ďakujeme.");
    }

    public function cancelAppointment(AppointmentCancelRequest $request): void
    {
        $data = $request->validated();

        $appointment = Appointment::find($data['i']);

        if(!AppointmentService::checkCancelToken($appointment, $data['t'])) {
            wp_redirect(home_url(). "?c=0");
            exit();
        }

        AppointmentService::cancelAppointment(appointment: $appointment, notifyCustomer: true, notifyEmployee: true, isCancelledByEmployee: true);

        wp_redirect(home_url(). "?c=1");
        exit();
    }

    public function sendNotifications(AppointmentSendNotificationsRequest $request): void
    {
        $countSent = AppointmentService::notifyAllAppointments();

        if($countSent > 0) {
            wp_send_json_success("Notifikácie boli odoslané. Počet odoslaných: $countSent");
        }

        wp_send_json_success("Neboli odoslané žiadne notifikácie.");
    }

    public function generateAppointmentICS(AppointmentICSRequest $request): void
    {
        $data = $request->validated();

        $appointment = Appointment::find($data['id']);

        if(!$appointment) {
            wp_send_json_error("Termín nebol nájdený.");
        }

        AppointmentService::generateICS($appointment);
    }
}