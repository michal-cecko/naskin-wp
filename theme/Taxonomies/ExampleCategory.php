<?php

namespace Theme\Taxonomies;

use Saurus\App\Modules\Wordpress\Taxonomies\TaxonomyType;
use Theme\PostTypes\Service;

class ExampleCategory extends TaxonomyType
{
    public static function getTaxonomySlug(): string
    {
        return "example-category";
    }

    public static function registerCustomTaxonomy(): ?array
    {
        $labels = [
            'name' => __('Kategórie kontaktov', THEME_DOMAIN),
            'singular_name' => __('Kategória kontaktov', THEME_DOMAIN),
            'search_items' => __('Vyhľadať kategóriu', THEME_DOMAIN),
            'popular_items' => __('Populárne kategórie', THEME_DOMAIN),
            'all_items' => __('Všetky kategórie', THEME_DOMAIN),
            'parent_item' => __('Nadradená kategória', THEME_DOMAIN),
            'parent_item_colon' => __('Nadradená kategória:', THEME_DOMAIN),
            'edit_item' => __('Upraviť kategóriu', THEME_DOMAIN),
            'view_item' => __('Zobraziť kategóriu', THEME_DOMAIN),
            'update_item' => __('Zmeniť kategóriu', THEME_DOMAIN),
            'add_new_item' => __('Pridať novú kategóriu', THEME_DOMAIN),
            'new_item_name' => __('Pridať názov novej kategórie', THEME_DOMAIN),
            'separate_items_with_commas' => __('Oddeľte kategórie čiarkou', THEME_DOMAIN),
            'add_or_remove_items' => __('Pridať alebo odstrániť kategórie', THEME_DOMAIN),
            'choose_from_most_used' => __('Vyberte z najpoužívanejších kategórii', THEME_DOMAIN),
            'not_found' => __('Neboli nájdené žiadne kategórie', THEME_DOMAIN),
        ];

        $args = [
            'description' => __("Kategórie kontaktov", THEME_DOMAIN),
            'public' => TRUE,
            'show_ui' => TRUE,
            'show_in_nav_menus' => TRUE,
            'show_tagcloud' => TRUE,
            'meta_box_cb' => NULL,
            'show_admin_column' => FALSE,
            'hierarchical' => FALSE,
            'query_var' => self::getTaxonomySlug(),
            'rewrite' => [
                'slug' => self::getTaxonomySlug(),
                'with_front' => TRUE,
                'hierarchical' => FALSE,
            ],
            'capabilities' => [],
            'labels' => $labels,
        ];

        return [
            'post_types' => [Service::getPostTypeSlug()],
            'slug' => self::getTaxonomySlug(),
            'args' => $args,
        ];
    }
}