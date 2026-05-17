<?php

return [
    'recaptcha' => [
        'url' => 'https://www.google.com/recaptcha/api/siteverify',
        'secret' => defined('NASKIN_RECAPTCHA_SECRET') ? NASKIN_RECAPTCHA_SECRET : '',
        'key' => defined('NASKIN_RECAPTCHA_SITE_KEY') ? NASKIN_RECAPTCHA_SITE_KEY : '',
    ],
];