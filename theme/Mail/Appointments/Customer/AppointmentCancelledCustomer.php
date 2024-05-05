<?php

namespace Theme\Mail;

use Saurus\App\Modules\Mail\Mailable;
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