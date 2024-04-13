<?php

// Remove default roles except admin
function remove_existing_roles() {
    $roles = wp_roles()->get_names();
    foreach ($roles as $role => $name) {
        if ($role !== 'administrator') {
            remove_role($role);
        }
    }
}
add_action('init', 'remove_existing_roles');


// add employee role
function add_employee_role() {
    $admin_caps = get_role('administrator')->capabilities;
    add_role('employee', 'Pracovník', $admin_caps);
    add_role('together-employee', 'Spoločný pracovník', $admin_caps);
}
add_action('init', 'add_employee_role');


//Restrict role
function restrict_employee_role(){
    $user = wp_get_current_user();
    if( in_array('employee', $user->roles) || in_array('together-employee', $user->roles) ){
        remove_menu_page( 'upload.php' );         // Plugins
        remove_menu_page( 'users.php' );         // Plugins
        remove_menu_page( 'plugins.php' );         // Plugins
        remove_menu_page( 'options-general.php' ); // Settings
        remove_menu_page( 'edit.php?post_type=acf-field-group' ); // ACF Fields
        remove_menu_page( 'ai1wm_export' );         // All in one WP Migration
        remove_menu_page( 'tools.php'); // Site Health
        remove_menu_page( 'themes.php' );          // Appearance
    }
}
add_action( 'admin_menu', 'restrict_employee_role' );

function redirect_to_appointments() {
    $user = wp_get_current_user();
    if( in_array('employee', $user->roles) || in_array('together-employee', $user->roles) ){

    }
    global $pagenow;
    if ( $pagenow === 'index.php' ) {
        wp_redirect( admin_url( 'edit.php?post_type=appointment' ) );
        exit();
    }
}
add_action( 'admin_init', 'redirect_to_appointments' );