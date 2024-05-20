<?php

namespace Theme\Modules\Customers;

use Carbon\Carbon;
use Theme\PostTypes\Customer;

class CustomersDashboardTable
{

    /**
     * Adds custom columns to the customers table
     *
     * @filter manage_customer_posts_columns
     */
    public function custom_customers_columns($columns)
    {
        $dateColumn = $columns['date'];

        unset($columns['date']);

        $columns['email'] = 'Email';
        $columns['phone'] = 'Telefón';
        $columns['last_appointment'] = 'Dátum posledného termínu';
        $columns['date'] = $dateColumn;

        return $columns;
    }

    /**
     * Populates custom columns in the customers table
     *
     * @action manage_customer_posts_custom_column
     */
    public function custom_customers_column_data($column, $post_id): void
    {
        switch ($column) {
            case 'email':
                $email = get_field('cust_email', $post_id);
                echo !empty($email) ? $email : "-";
                break;
            case 'phone':
                $phone = get_field('cust_phone', $post_id);
                echo !empty($phone) ? $phone : "-";
                break;
            case 'last_appointment':
                $date = get_field('cust_last-appointment', $post_id);
                echo !empty($date) ? Carbon::parse($date)->format("j.n.Y") : "-";
                break;
        }
    }

    /**
     * Hides title, slug and permalink for customers, we dont need them.
     *
     * @action edit_form_after_title
     * @return void
     */
    public function hide_title_slug_permalink_for_customers(): void
    {
        global $post;

        if ( Customer::getPostTypeSlug() === $post->post_type ) :?>
            <style type="text/css">
                #edit-slug-box,
                #sample-permalink,
                #slugdiv
                    /*#post-body-content*/ {
                    display: none !important;
                }
            </style>
        <?php endif;
    }
}