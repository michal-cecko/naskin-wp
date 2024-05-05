<?php

namespace Theme\Mail;

use Saurus\App\Modules\Mail\Mailable;
use Theme\Models\Appointment\Appointment;
use Theme\PostTypes\Customer;

abstract class AppointmentEmail extends Mailable
{
    protected string $address;

    public function __construct(protected Appointment $appointment)
    {
        $address = config("appointments.address");
        $this->address = "{$address['street']}, {$address['zip']} {$address['city']}";
    }

    public function data(): array
    {
        return [
            'appointment' => $this->appointment,
            'address' => $this->address,
        ];
    }
}