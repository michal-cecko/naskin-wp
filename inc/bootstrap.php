<?php

// Initialize Composer
if (! file_exists($composer = THEME_PATH . '/vendor/autoload.php')) {
    wp_die('Error locating autoloader. Please run <code>composer install</code>.');
}
require_once($composer);