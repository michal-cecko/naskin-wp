<?php

namespace Theme\Mail\Appointments\Employee\Reservation;

use Saurus\App\Modules\Mail\Mailable;
use Theme\Mail\Appointments\AppointmentEmail;
use Theme\Models\Appointment\Appointment;

class AppointmentCreatedEmployee extends AppointmentEmail
{
    public function setup() : void
    {
        $this->subject = "Nová prijatá rezervácia";
    }

    public function html(): string
    {
        return templates()->generate('emails.appointments.employee.reservation.created-appointment-employee', $this->data());
    }
}