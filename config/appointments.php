<?php

return [
    'settings' => [
        'multiple_services' => true,
    ],
    'address' => [
        'street' => 'Nákupné centrum M-park, Centrum 8, 1. poschodie',
        'city' => 'Považská Bystrica',
        'zip' => '017 01',
    ],
    'cron-notifications-token' => defined('NASKIN_CRON_NOTIFICATIONS_TOKEN') ? NASKIN_CRON_NOTIFICATIONS_TOKEN : '',
    'cron-ics-token' => defined('NASKIN_CRON_ICS_TOKEN') ? NASKIN_CRON_ICS_TOKEN : '',
];