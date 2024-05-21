<?php

namespace Theme\Modules\EmployeeRole;

use Theme\PostTypes\Service;
use Theme\Users\User;

class EmployeeRole {

    /**
     * @action admin_menu
     *
     * @return void
     */
    public function restrict_employee_role(): void
    {
        $user = User::find(get_current_user_id());

        if( in_array($user?->role, ['together-employee', 'employee']) ) {
            remove_menu_page( 'upload.php' );
            remove_menu_page( 'users.php' );
            remove_menu_page( 'plugins.php' );         //plugins
            remove_menu_page( 'options-general.php' ); // Settings
            remove_menu_page( 'edit.php?post_type=acf-field-group' ); // ACF Fields
            remove_menu_page( 'edit.php?post_type=page' ); // Pages
            remove_menu_page( 'ai1wm_export' );         // All in one WP Migration
            remove_menu_page( 'tools.php'); // Site Health
            remove_menu_page( 'themes.php' );          // Appearance
            remove_menu_page( 'admin.php?page=wpseo_dashboard' );          // Appearance
            remove_menu_page( 'admin.php?page=theme-general-settings' );          // Appearance

            remove_menu_page( 'edit.php?post_type=' . Service::getPostTypeSlug() ); // Services
        }
    }

    /**
     * @action admin_init
     *
     * @return void
     */
    public function remove_existing_roles(): void
    {
        $roles = wp_roles()->get_names();
        foreach ($roles as $role => $name) {
            if ($role !== 'administrator') {
                remove_role($role);
            }
        }
    }

    /**
     * @action admin_init
     *
     * @return void
     */
    public function add_employee_role(): void
    {
        $admin_caps = get_role('administrator')->capabilities;
        add_role('employee', 'Pracovník', $admin_caps);
        add_role('together-employee', 'Spoločný pracovník', $admin_caps);
    }
}