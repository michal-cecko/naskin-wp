<?php

use Theme\PostTypes\Customer;

class Customers {
    /**
     * Mutator for customer name ACF field on post title update
     *
     * @action save_post_customer 10 3
     */
    public function update_cust_name_meta_on_title_update($post_id, $post, $update): int|bool
    {
        $new_title = $post->post_title;

        if ($update) {
            return update_post_meta($post_id, 'cust_name', $new_title);
        }

        return add_post_meta($post_id, 'cust_name', $new_title, true);
    }
}