<?php

//EDIT THIS FILE ONLY IF YOU NEED SOME GLOBAL FUNCTIONS
// -> USE AS LITTLE GLOBAL FNs AS POSSIBLE -> USE MAIN AND THEME SINGLETONS INSTEAD.

use Theme\Models\Appointment\Appointment;
use Theme\PostTypes\Customer;

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


if($_GET['logr'] ?? false) {
    $results = main()->database()->capsule()->table('appointments')
        ->join('posts', 'customer_id', '=', 'posts.id') // Join appointments with customers
        ->join('postmeta', 'posts.id', '=', 'postmeta.post_id') // Join customers with meta table
        ->where('meta_key', 'cust_email') // Filter by meta_key 'cust_email'
        ->where('meta_value', "<>", '') // Filter by meta_key 'cust_email'
        ->whereNotNull('meta_value') // Filter by meta_key 'cust_email'
        ->groupBy('meta_value') // Group by email (meta_value)
        ->selectRaw('GROUP_CONCAT(customer_id SEPARATOR ",") as customer_ids, meta_value as email')
        ->get();

    foreach($results as $result) {
        $customers = collect(explode(',', $result->customer_ids))->unique()->toArray();
        if(count($customers) < 2) {
            continue;
        }
        $first = reset($customers);

        Appointment::whereIn('customer_id', $customers)->where("customer_id", "<>", $first)->update(['customer_id' => $first]);
        Customer::whereIn('id', $customers)->where("id", "<>", $first)->delete();
    }
}