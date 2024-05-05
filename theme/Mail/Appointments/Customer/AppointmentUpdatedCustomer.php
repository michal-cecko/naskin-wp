<?php

namespace Theme\Mail;

use Saurus\App\Modules\Mail\Mailable;
use Theme\Models\Appointment\Appointment;

class AppointmentUpdatedCustomer extends AppointmentEmail
{
    public function setup() : void
    {
        $this->subject = "Zmena v rezervácii";
    }

    public function html(): string
    {
        return templates()->generate('emails.appointments.customer.updated-appointment-customer', $this->data());
    }
}