<?php

namespace Theme\Mail\Appointments\Employee\Vacation;

use Saurus\App\Modules\Mail\Mailable;
use Theme\Mail\Appointments\AppointmentEmail;
use Theme\Models\Appointment\Appointment;

class VacationCreated extends AppointmentEmail
{
    public function setup() : void
    {
        $this->subject = "Vytvorené voľno";
    }

    public function html(): string
    {
        return templates()->generate('emails.appointments.employee.vacation.created-vacation', $this->data());
    }
}