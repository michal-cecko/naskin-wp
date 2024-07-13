<?php

use Theme\Enum\AppointmentPaymentType;
use Theme\Enum\AppointmentSource;
use Theme\Enum\AppointmentStatus;
use Theme\Enum\AppointmentType;

return [
    'translations' => [
        AppointmentSource::class => [
            AppointmentSource::IN_PERSON->value => 'Osobne',
            AppointmentSource::PHONE->value => 'Telefonicky',
            AppointmentSource::WEB->value => 'Online',
        ],
        AppointmentStatus::class => [
            AppointmentStatus::OK->value => 'OK',
            AppointmentStatus::CANCELLED->value => 'Zrušená',
        ],
        AppointmentType::class => [
            AppointmentType::RESERVATION->value => 'Rezervácia',
            AppointmentType::VACATION->value => 'Voľno',
        ],
        AppointmentPaymentType::class => [
            AppointmentPaymentType::CASH->value => 'Hotovosť',
            AppointmentPaymentType::GIFTCARD->value => 'Darčeková karta',
            AppointmentPaymentType::BANK_CARD->value => 'Banková karta',
        ],
    ]
];