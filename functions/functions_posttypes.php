<?php

// CREATING CUSTOM POST TYPES

function create_post_types()
{


    /*
     * SLUŽBY / SERVICES ---- START
     */

    $labels = array(
        'name' => __('Služby', 'naskin'),
        'singular_name' => __('Služba', 'naskin'),
        'add_new' => __('Pridať novú službu', 'naskin'),
        'add_new_item' => __('Pridať novú službu', 'naskin'),
        'edit_item' => __('Upraviť službu', 'naskin'),
        'new_item' => __('Nová služba', 'naskin'),
        'view_item' => __('Otvoriť službu', 'naskin'),
        'search_items' => __('Hľadať službu', 'naskin'),
        'not_found' => __('Služba nebolo nájdená', 'naskin'),
        'not_found_in_trash' => __('Služba nebola nájdená v koši', 'naskin')
    );

    $supports = array(
        'title',
    );

    $args = array(
        'labels' => $labels,
        'supports' => $supports,
        'public' => TRUE,
        'has_archive' => FALSE,
        'show_in_rest' => FALSE,
        'taxonomy' => [],
        'menu_icon' => 'dashicons-admin-tools',
        'rewrite' => ['slug' => 'service'],
    );

    register_post_type('service', $args);


    // Add custom columns
    function custom_services_columns($columns)
    {
        unset($columns['date']);
        $columns['description'] = 'Popis';
        $columns['price'] = 'Cena';
        $columns['date'] = 'Dátum';
        return $columns;
    }

    add_filter('manage_service_posts_columns', 'custom_services_columns');

    // Populate custom columns
    function custom_services_column_data($column, $post_id)
    {
        switch ($column) {
            case 'price':
                echo get_field('serv-price', $post_id) . "€";
                break;
            case 'description':
                echo get_field('serv-description', $post_id);
                break;
        }
    }

    add_action('manage_service_posts_custom_column', 'custom_services_column_data', 10, 2);


    /*
     * SLUZBY / SERVICES ---- END
     */


    //-----------------------------------------------------------------------------------------


    /*
     * TERMÍNY / APPOINTMENTS ---- START
     */

    $labels = array(
        'name' => __('Termíny', 'naskin'),
        'singular_name' => __('Termín', 'naskin'),
        'add_new' => __('Pridať nový termín', 'naskin'),
        'add_new_item' => __('Pridať nový termín', 'naskin'),
        'edit_item' => __('Upraviť termín', 'naskin'),
        'new_item' => __('Nový termín', 'naskin'),
        'view_item' => __('Otvoriť termín', 'naskin'),
        'search_items' => __('Hľadať termín', 'naskin'),
        'not_found' => __('Termín nebol nájdený', 'naskin'),
        'not_found_in_trash' => __('Termín nebol nájdený v koši', 'naskin')
    );

    $supports = [];

    $args = array(
        'labels' => $labels,
        'supports' => $supports,
        'public' => TRUE,
        'has_archive' => FALSE,
        'show_in_rest' => TRUE,
        'taxonomy' => [],
        'menu_icon' => 'dashicons-calendar-alt',
        'rewrite' => ['slug' => 'appointment'],
    );

    register_post_type('appointment', $args);
    register_post_meta('appointment', 'cancel_token', array(
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
    ));
    register_post_meta('appointment', 'has_been_reminded', array(
        'type' => 'boolean',
        'single' => true,
        'show_in_rest' => true,
    ));

    //
    add_action('load-edit.php', function () {
        $screen = get_current_screen();
        // Only edit post screen:
        if ('edit-appointment' === $screen->id) {
            add_action('all_admin_notices', function () {
                ob_start();
                include(get_template_directory() . "/template_parts/admin/calendar.php");
                echo ob_get_clean();
            });
        }
    });


    /*
     * TERMÍNY / APPOINTMENTS ---- END
     */


    //-----------------------------------------------------------------------------------------


    /*
     * ZÁKAZNÍCI / CUSTOMERS ---- START
     */

    $labels = array(
        'name' => __('Zákazníci', 'naskin'),
        'singular_name' => __('Zákazník', 'naskin'),
        'add_new' => __('Pridať nového zákazníka', 'naskin'),
        'add_new_item' => __('Pridať nového zákazníka', 'naskin'),
        'edit_item' => __('Upraviť zákazníka', 'naskin'),
        'new_item' => __('Nový zákazník', 'naskin'),
        'view_item' => __('Otvoriť zákazníka', 'naskin'),
        'search_items' => __('Hľadať zákazníka', 'naskin'),
        'not_found' => __('Zákazník nebol nájdený', 'naskin'),
        'not_found_in_trash' => __('Zákazník nebol nájdený v koši', 'naskin')
    );

    $supports = [
        'title'
    ];

    $args = array(
        'labels' => $labels,
        'supports' => $supports,
        'public' => TRUE,
        'has_archive' => FALSE,
        'show_in_rest' => FALSE,
        'taxonomy' => [],
        'menu_icon' => 'dashicons-admin-users',
        'rewrite' => ['slug' => 'customer'],
    );

    register_post_type('customer', $args);

    // Add custom columns
    function custom_customers_columns($columns)
    {
        $dateColumn = $columns['date'];
        unset($columns['date']);
        $columns['email'] = 'Email';
        $columns['phone'] = 'Telefón';
        $columns['last_appointment'] = 'Dátum posledného termínu';
        $columns['date'] = $dateColumn;
        return $columns;
    }

    add_filter('manage_customer_posts_columns', 'custom_customers_columns');

    // Populate custom columns
    function custom_customers_column_data($column, $post_id)
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
                echo !empty($date) ? $date : "-";
                break;
        }
    }

    add_action('manage_customer_posts_custom_column', 'custom_customers_column_data', 10, 2);


    function hide_title_slug_permalink_for_customer_post_type() {
        global $post;
        if ( 'customer' === $post->post_type ) {
            ?>
            <style type="text/css">
                #edit-slug-box,
                #sample-permalink,
                #slugdiv
                /*#post-body-content*/ {
                    display: none !important;
                }
            </style>
            <?php
        }
    }
    add_action( 'edit_form_after_title', 'hide_title_slug_permalink_for_customer_post_type' );



/*    add_filter( 'posts_join', 'customer_search_join' );
    function customer_search_join( $join ) {
        global $pagenow, $wpdb;

        // Only apply filter when performing a search on edit page of the "customer" post type.
        if ( is_admin() && 'edit.php' === $pagenow && 'customer' === $_GET['post_type'] && ! empty( $_GET['s'] ) ) {
            $join .= 'LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
        }
        return $join;
    }*/

    // Hook to update meta on post title update
    add_action('save_post', 'update_cust_name_meta_on_title_update', 10, 3);
    function update_cust_name_meta_on_title_update($post_id, $post, $update) {
        // Check if this is a post of the specific post type
        if ($post->post_type == 'customer') {
            // Get the new post title
            $new_title = $post->post_title;

            // Update the custom meta field 'cust_name' with the new title
            update_post_meta($post_id, 'cust_name', $new_title);
        }
    }

    // Hook to set meta on post creation
    add_action('wp_insert_post', 'set_cust_name_meta_on_post_creation', 10, 3);
    function set_cust_name_meta_on_post_creation($post_id, $post, $update) {
        // Check if this is a post of the specific post type
        if ($post->post_type == 'customer') {
            // Get the post title
            $post_title = $post->post_title;

            // Set the custom meta field 'cust_name' with the post title
            add_post_meta($post_id, 'cust_name', $post_title, true);
        }
    }

    /*
     * ZÁKAZNÍCI / CUSTOMERS ---- END
     */

    //-----------------------------------------------------------------------------------------
}

add_action('init', 'create_post_types');