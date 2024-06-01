<?php

use Theme\Enum\AppointmentSource;
use Theme\Enum\AppointmentStatus;
use Theme\Enum\AppointmentType;

return [
    'translations' => [
        AppointmentSource::class => [
            'in_person' => 'Osobne',
            'phone' => 'Telefonicky',
            'web' => 'Online',
        ],
        AppointmentStatus::class => [
            'ok' => 'OK',
            'cancelled' => 'Zrušená',
        ],
        AppointmentType::class => [
            'reservation' => 'Rezervácia',
            'free' => 'Voľno',
        ],
    ]
];