<?php

namespace Theme\Mail\Appointments\Customer;

use Saurus\App\Modules\Mail\Mailable;
use Theme\Mail\Appointments\AppointmentEmail;
use Theme\Models\Appointment\Appointment;

class AppointmentCancelledCustomer extends AppointmentEmail
{
    public function setup() : void
    {
        $this->subject = "Zrušenie rezervácii";
    }

    public function html(): string
    {
        return templates()->generate('emails.appointments.customer.cancelled-appointment-customer', $this->data());
    }
}