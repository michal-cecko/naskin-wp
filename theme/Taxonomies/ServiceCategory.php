<?php

namespace Theme\Taxonomies;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Saurus\App\Modules\Wordpress\Taxonomies\TaxonomyType;
use Theme\PostTypes\Service;

class ServiceCategory extends TaxonomyType
{
    const DEFAULT_COLOR = "#000000";

    public function getImageAttribute(): ?string
    {
        $color = get_field("image", $this->acf_id);
        return !empty($color) ? $color : null;
    }

    public function getShortDescAttribute(): ?string
    {
        $shortDesc = get_field("short_desc", $this->acf_id);
        return !empty($shortDesc) ? $shortDesc : null;
    }

    public function getStaticBreakAttribute(): ?string
    {
        $break = get_field("static_break", $this->acf_id);
        return !empty($break) ? intval($break) : null;
    }

    public function getColorAttribute(): string
    {
        $color = get_field("color", $this->acf_id);
        return !empty($color) ? $color : self::DEFAULT_COLOR;
    }

    public function posts() : BelongsToMany
    {
        return $this->belongsToMany(
            Service::class,
            'term_relationships',
            'term_taxonomy_id',
            'object_id'
        );
    }

    public function publishedPostsPluginOrdered() : BelongsToMany
    {
        return $this->publishedPosts()->orderBy('menu_order', "ASC")->orderBy("post_title", "ASC");
    }

    public function publishedPostsGroupedByTitle() : BelongsToMany
    {
        return $this->publishedPosts()->groupBy('post_title');
    }

    public static function getTaxonomySlug(): string
    {
        return "service-category";
    }

    public static function registerCustomTaxonomy(): ?array
    {
        $labels = [
            'name' => __('Kategórie služieb', THEME_DOMAIN),
            'singular_name' => __('Kategória služieb', THEME_DOMAIN),
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
            'description' => __("Kategórie služieb", THEME_DOMAIN),
            'public' => TRUE,
            'show_ui' => TRUE,
            'show_in_nav_menus' => TRUE,
            'show_tagcloud' => TRUE,
            'meta_box_cb' => NULL,
            'show_admin_column' => FALSE,
            'hierarchical' => FALSE,
            'query_var' => self::getTaxonomySlug(),
            'rewrite' => [
                'slug' => "sluzby",
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