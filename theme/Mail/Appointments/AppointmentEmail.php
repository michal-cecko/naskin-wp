<?php

namespace Theme\Mail\Appointments;

use Saurus\App\Modules\Mail\Mailable;
use Theme\Models\Appointment\Appointment;
use Theme\PostTypes\Customer;

abstract class AppointmentEmail extends Mailable
{
    protected string $address;
    private array $eagerLoad = [];

    public function __construct(protected Appointment $appointment, protected array $additionalData = [])
    {
        $this->addEagerLoads();

        $address = config("appointments.address");
        $this->address = "{$address['street']}, {$address['zip']} {$address['city']}";

        $this->setup();
    }

    public function data(): array
    {
        return array_merge($this->additionalData, [
            'appointment' => $this->appointment,
            'address' => $this->address,
        ]);
    }

    private function addEagerLoads()
    {
        if(!$this->appointment->relationLoaded('customer')) {
            $this->eagerLoad[] = 'customer';
        }

        if(!$this->appointment->relationLoaded('services')) {
            $this->eagerLoad[] = 'services';
        }

        if(!$this->appointment->relationLoaded('employee')) {
            $this->eagerLoad[] = 'employee';
        }

        if(!empty($this->eagerLoad)) {
            $this->appointment->load($this->eagerLoad);
        }
    }
}