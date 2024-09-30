<?php

namespace Theme\Services\Customers;

use Saurus\App\Traits\Validation;
use Theme\Exceptions\Customer\CustomerHasEmptyEmailException;
use Theme\PostTypes\Customer;

class CustomerService {

    use Validation;

    public static function findOrCreateCustomer(string $name, ?string $email, ?string $phone = null) {

        $customer = Customer::whereHas("meta", function ($q) use ($email) {
            $q->where("meta_key", "cust_email")->where("meta_value", $email);
        })->first();

        if(!$customer) {

            $id = wp_insert_post([
                'post_title' => $name,
                'post_type' => Customer::getPostTypeSlug(),
                'post_status' => 'publish',
            ]);

            $customer = Customer::find($id);

            if (is_wp_error($id)) {
                wp_send_json_error([
                    'message' => __("Failed to create a customer.", THEME_DOMAIN),
                ], 500);
            }

            update_field('cust_name', $name, $id);

            if (!empty($email)) {
                update_field('cust_email', $email, $id);
            }

            if (!empty($phone)) {
                update_field('cust_phone', $phone, $id);
            }

        }

        return $customer;
    }

    public static function updateLastAppointmentDate(Customer|int $customer, string $date): bool
    {
        return update_field('cust_last-appointment', $date, is_int($customer) ? $customer : $customer->ID);
    }

    /**
     * @throws CustomerHasEmptyEmailException
     */
    public static function checkCustomerEmail(Customer|int $customer): string
    {
        $customer = is_int($customer) ? Customer::where("ID", $customer)->first() : $customer;

        if(empty($customer->email)) {
            throw new CustomerHasEmptyEmailException($customer);
        }

        return $customer->email;
    }

}