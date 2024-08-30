<?php

namespace Theme\Modules\Role;

use Saurus\App\Modules\Wordpress\Roles\UserRole;

class AdminRole extends UserRole
{
    public static function capabilities(): array
    {
        return [
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

            // Appointments
            'view_appointments_calendar' => true,
            'view_appointments_list' => true,
            'view_appointments_money_statistics' => true,

            // Expenses
            'view_expense_categories' => true,
            'view_expenses' => true,
            'view_expense_money_statistics' => true,

            // ACF Options
            'view_acf_web_options' => true,
        ];
    }
}