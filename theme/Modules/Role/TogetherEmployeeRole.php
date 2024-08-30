<?php

namespace Theme\Modules\Role;

use Saurus\App\Modules\Wordpress\Roles\UserRole;
use Theme\PostTypes\Service;

class TogetherEmployeeRole extends UserRole
{
    public static function capabilities(): array
    {
        return [
            // Appointments
            'view_appointments_list' => false,
            'view_appointments_calendar' => true,

            // Dashboard
            'edit_dashboard' => true,
            'read' => true,

            // Posts
            'read_posts' => false,

            // To read services
            'edit_service' => true,
            'read_service' => true,
            'create_services' => true, // Without this, the user can't see index view of a services
            'edit_services' => true,

            // Customers
            'edit_customer' => true,
            'read_customer' => true,
            'delete_customer' => true,
            'create_customers' => true,
            'delete_customers' => true,
            'delete_others_customers' => true,
            'delete_private_customers' => true,
            'delete_published_customers' => true,
            'edit_customers' => true,
            'edit_others_customers' => true,
            'edit_private_customers' => true,
            'edit_published_customers' => true,
            'read_private_customers' => true,
            'publish_customers' => true,
        ];
    }

    public static function dashboardRestrictions(): void
    {
        remove_menu_page('users.php');
        remove_menu_page('plugins.php');         // Plugins
        remove_menu_page('options-general.php'); // Settings
        remove_menu_page('edit.php?post_type=acf-field-group'); // ACF Fields
        remove_menu_page('edit.php?post_type=page'); // Pages
        remove_menu_page('ai1wm_export');         // All in one WP Migration
        remove_menu_page('tools.php'); // Site Health
        remove_menu_page('themes.php');          // Appearance
        remove_menu_page('admin.php?page=wpseo_dashboard'); // Yoast SEO
        remove_menu_page('admin.php?page=theme-general-settings'); // Theme settings
    }
}