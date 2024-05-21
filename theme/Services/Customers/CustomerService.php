<?php

namespace Theme\Services\Customers;

use Theme\PostTypes\Customer;

class CustomerService {

    public static function createOrFindCustomer(string $name, string $email, ?string $phone = null) {

        if($customer = Customer::hasMeta(['cust_email' => $email])->first()) {
            return $customer;
        }

        $id = wp_insert_post([
            'post_title' => $name,
            'post_type' => Customer::getPostTypeSlug(),
            'post_status' => 'publish',
        ]);

        if(is_wp_error($id)) {
            wp_send_json_error([
                'message' => __("Failed to create a customer.", THEME_DOMAIN),
            ], 500);
        }

        $customer = Customer::find($id);

        update_field('cust_name', $name, $id);
        update_field('cust_email', $email, $id);

        if (!empty($phone)) {
            update_field('cust_phone', $phone, $id);
        }

        return $customer;
    }

    public static function updateLastAppointmentDate(Customer|int $customer, string $date): bool
    {
        return update_field('cust_last-appointment', $date, is_int($customer) ? $customer : $customer->ID);
    }

}