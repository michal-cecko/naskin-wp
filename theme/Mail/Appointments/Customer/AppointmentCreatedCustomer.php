<?php

namespace Theme\Mail;

use Saurus\App\Modules\Mail\Mailable;
use Theme\Models\Appointment\Appointment;

class AppointmentCreatedCustomer extends AppointmentEmail
{
    public function setup() : void
    {
        $this->subject = "Potvrdenie rezervácie";
    }

    public function html(): string
    {
        return templates()->generate('emails.appointments.customer.created-appointment-customer', $this->data());
    }
}