<?php

namespace Theme\Mail\Appointments\Customer;

use Saurus\App\Modules\Mail\Mailable;
use Theme\Mail\Appointments\AppointmentEmail;
use Theme\Models\Appointment\Appointment;

class AppointmentRemindCustomer extends AppointmentEmail
{
    public function setup() : void
    {
        $this->subject = "Pripomienka rezervácie";
    }

    public function html(): string
    {
        return templates()->generate('emails.appointments.customer.remind-appointment-customer', $this->data());
    }
}