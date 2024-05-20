<?php

namespace Theme\Modules\Services;

use Theme\Taxonomies\ServiceCategory;

class ServicesDashboardTable {
    /**
     * Adds custom columns to the services table
     *
     * @filter manage_service_posts_columns
     */
    public function custom_services_columns($columns)
    {
        unset($columns['date']);

        $columns['category'] = 'Kategória';
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
            case 'category':
                echo get_the_terms($post_id, ServiceCategory::getTaxonomySlug())[0]?->name ?? "Bez kategórie";
                break;
            case 'price':
                echo !empty($price = get_field('serv-price', $post_id)) ? $price . "€" : "Bez ceny";
                break;
            case 'description':
                echo !empty($popis = get_field('serv-description', $post_id)) ? $popis : "Bez popisu";
                break;
        }
    }
}