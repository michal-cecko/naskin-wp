<?php

/**
 * Powered by Bulkgate - https://bulkgate.com
 *
 * Define NASKIN_BULKGATE_APP_TOKEN, NASKIN_BULKGATE_APP_ID
 * and NASKIN_BULKGATE_ANDROID_KEY in wp-config.php.
 */

return [
    'app_token' => defined('NASKIN_BULKGATE_APP_TOKEN') ? NASKIN_BULKGATE_APP_TOKEN : '',
    'android_app_key' => defined('NASKIN_BULKGATE_ANDROID_KEY') ? NASKIN_BULKGATE_ANDROID_KEY : '',
    'app_id' => defined('NASKIN_BULKGATE_APP_ID') ? (int) NASKIN_BULKGATE_APP_ID : 0,
];
