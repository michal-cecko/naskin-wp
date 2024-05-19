<?php

class ServicesDashboardTable {
    /**
     * Adds custom columns to the services table
     *
     * @filter manage_service_posts_columns
     */
    public function custom_services_columns($columns)
    {
        unset($columns['date']);

        $columns['description'] = 'Popis';
        $columns['price'] = 'Cena';
        $columns['date'] = 'Dátum';

        return $columns;
    }

    /**
     * Populates custom columns in the services table
     *
     * @action manage_service_posts_custom_column
     */
    public function custom_services_column_data($column, $post_id): void
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
}