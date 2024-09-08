<?php

namespace Theme\Mail\Appointments\Employee\Reservation;

use Saurus\App\Modules\Mail\Mailable;
use Theme\Mail\Appointments\AppointmentEmail;
use Theme\Models\Appointment\Appointment;

class AppointmentUpdatedEmployee extends AppointmentEmail
{
    public function setup() : void
    {
        $this->subject = "Zmena v rezervácii";
    }

    public function html(): string
    {
        return templates()->generate('emails.appointments.employee.reservation.updated-appointment-employee', $this->data());
    }
}