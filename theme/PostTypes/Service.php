<?php

namespace Theme\PostTypes;

use Saurus\App\Modules\Wordpress\Posts\PostType;

class Service extends PostType
{
    public static function getPostTypeSlug(): string
    {
        return "service";
    }

    public static function registerCustomPostType(): ?array
    {

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
            'rewrite' => ['slug' => self::getPostTypeSlug()],
        );

        return [
            'slug' => self::getPostTypeSlug(),
            'args' => $args,
        ];
    }
}