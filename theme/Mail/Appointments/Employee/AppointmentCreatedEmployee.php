<?php

namespace Theme\Mail;

use Saurus\App\Modules\Mail\Mailable;
use Theme\Models\Appointment\Appointment;

class AppointmentCreatedEmployee extends AppointmentEmail
{
    public function setup() : void
    {
        $this->subject = "Nová prijatá rezervácia";
    }

    public function html(): string
    {
        return templates()->generate('emails.appointments.employee.created-appointment-employee', $this->data());
    }
}