<?php

namespace Theme\Mail\Appointments\Customer;

use Saurus\App\Modules\Mail\Mailable;
use Theme\Mail\Appointments\AppointmentEmail;

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