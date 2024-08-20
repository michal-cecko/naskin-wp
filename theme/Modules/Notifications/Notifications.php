<?php

namespace Theme\Modules\Notifications;

use Saurus\App\Enums\ApiMethod;
use Saurus\App\Exceptions\Route\ApiEndpointAlreadyExistException;
use Saurus\App\Traits\Validation;
use Theme\Requests\Appointments\AppointmentSendNotificationsRequest;
use Theme\Services\Appointments\AppointmentService;

class Notifications
{
    use Validation;

    /**
     * @throws ApiEndpointAlreadyExistException
     */
    public function __construct()
    {
        $this->initRest();
    }

    /**
     * @throws ApiEndpointAlreadyExistException
     */
    private function initRest(): void
    {
        main()->api()->addApiEndpoint(ApiMethod::GET, "/appointment/send-notifications", "appointment.send-notifications", [$this, 'sendNotifications']);
    }

    public function sendNotifications(AppointmentSendNotificationsRequest $request): void
    {
        $countSent = AppointmentService::notifyAllAppointments();

        if($countSent > 0) {
            wp_send_json_success("Notifikácie boli odoslané. Počet odoslaných: $countSent");
        }

        wp_send_json_success("Neboli odoslané žiadne notifikácie.");
    }
}