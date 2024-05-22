<?php

//EDIT THIS FILE ONLY IF YOU NEED SOME GLOBAL FUNCTIONS
// -> USE AS LITTLE GLOBAL FNs AS POSSIBLE -> USE MAIN AND THEME SINGLETONS INSTEAD.
define('SAURUS_PATH', plugin_dir_path(dirname(__FILE__, 2)) . 'mu-plugins/wordpresaurus');

// Include Saurus and Theme bootstrap
require_once(SAURUS_PATH . '/inc/bootstrap.php');
require_once(THEME_PATH . '/inc/bootstrap.php');

// Create config singleton
configModule();

// Create main
main();

// Create theme
theme();

// Create templates
templates();

// Register REST routes -> has to be after init of main and theme bcs we define all routes during constructs of modules
main()->api()->registerRoutes();
