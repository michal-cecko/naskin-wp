<?php

namespace Theme\Mail\Appointments\Customer;

use Saurus\App\Modules\Mail\Mailable;
use Theme\Mail\Appointments\AppointmentEmail;
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