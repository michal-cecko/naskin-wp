<?php

namespace Theme\Modules\Role;

use Saurus\App\Modules\Wordpress\Roles\UserRole;

class OwnerRole extends UserRole
{
    public static function capabilities(): array
    {
        return [
            // Defaults
            "edit_users" => true,
            "edit_files" => true,
            "manage_options" => true,
            "moderate_comments" => true,
            "manage_categories" => true,
            "manage_links" => true,
            "upload_files" => true,
            "unfiltered_html" => true,
            "edit_posts" => true,
            "edit_others_posts" => true,
            "edit_published_posts" => true,
            "publish_posts" => true,
            "read" => true,
            "level_6" => true,
            "level_5" => true,
            "level_4" => true,
            "level_3" => true,
            "level_2" => true,
            "level_1" => true,
            "level_0" => true,
            "delete_posts" => true,
            "delete_others_posts" => true,
            "delete_published_posts" => true,
            "delete_private_posts" => true,
            "edit_private_posts" => true,
            "read_private_posts" => true,
            "delete_users" => true,
            "create_users" => true,
            "unfiltered_upload" => true,
            "edit_dashboard" => true,
            "update_core" => true,
            "list_users" => true,
            "remove_users" => true,
            "promote_users" => true,
            "edit_theme_options" => true,
            "publish_others_posts" => true,
            "edit_post" => true,
            "delete_post" => true,
            "read_post" => true,
            "read_others_posts" => true,
            "publish_others_services" => true,
            "read_others_services" => true,
            "create_posts" => true,

            // Services
            'edit_service' => true,
            'read_service' => true,
            'delete_service' => true,
            'create_services' => true,
            'delete_services' => true,
            'delete_others_services' => true,
            'delete_private_services' => true,
            'delete_published_services' => true,
            'edit_services' => true,
            'edit_others_services' => true,
            'edit_private_services' => true,
            'edit_published_services' => true,
            'read_private_services' => true,
            'publish_services' => true,

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

            // Products
            'edit_product' => true,
            'read_product' => true,
            'delete_product' => true,
            'create_products' => true,
            'delete_products' => true,
            'delete_others_products' => true,
            'delete_private_products' => true,
            'delete_published_products' => true,
            'edit_products' => true,
            'edit_others_products' => true,
            'edit_private_products' => true,
            'edit_published_products' => true,
            'read_private_products' => true,
            'publish_products' => true,

            //Appointments
            'view_appointments_calendar' => true,
            'view_appointments_list' => true,
            'view_appointments_money_statistics' => true,

            //Expenses
            'view_expense_categories' => true,
            'view_expenses' => true,
            'view_expense_money_statistics' => true,
            'view_expenses_detail_page' => true,
            'create_expenses' => true,
            'edit_expenses' => true,
            'delete_expenses' => true,

            // Product Sales
            'view_product_sales' => true,
            'view_product_sale_detail_page' => true,
            'view_product_sale_money_statistics' => true,
            'create_product_sales' => true,
            'edit_product_sales' => true,
            'delete_product_sales' => true,

            // ACF Options
            'view_acf_web_options' => true,
        ];
    }

    public static function dashboardRestrictions(): void
    {
        remove_menu_page('plugins.php');         // Plugins
        remove_menu_page('options-general.php'); // Settings
        remove_menu_page('edit.php?post_type=acf-field-group'); // ACF Fields
        remove_menu_page('edit.php?post_type=page'); // Pages
        remove_menu_page('ai1wm_export');         // All in one WP Migration
        remove_menu_page('tools.php'); // Site Health
        remove_menu_page('themes.php');          // Appearance
        remove_menu_page('admin.php?page=wpseo_dashboard'); // Yoast SEO
        remove_menu_page('admin.php?page=filebird-settings'); // Filebird
    }
}
