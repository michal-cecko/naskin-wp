<?php

namespace Theme\Mail;

use Saurus\App\Modules\Mail\Mailable;
use Theme\Models\Appointment\Appointment;

class AppointmentCancelledEmployee extends AppointmentEmail
{
    public function setup() : void
    {
        $this->subject = "Zrušená rezervácia";
    }

    public function html(): string
    {
        return templates()->generate('emails.appointments.employee.cancelled-appointment-employee', $this->data());
    }
}