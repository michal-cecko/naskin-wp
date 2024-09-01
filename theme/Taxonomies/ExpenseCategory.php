<?php

namespace Theme\Taxonomies;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Saurus\App\Modules\Log\ILoggable;
use Saurus\App\Modules\Wordpress\Taxonomies\TaxonomyType;
use Theme\Models\Expense\Expense;
use Theme\PostTypes\Service;

class ExpenseCategory extends TaxonomyType implements ILoggable
{
    public function expenses() : BelongsToMany
    {
        return $this->belongsToMany(
            Expense::class,
            'term_relationships',
            'term_taxonomy_id',
            'object_id'
        );
    }

    public static function getTaxonomySlug(): string
    {
        return "expense-category";
    }

    public static function registerCustomTaxonomy(): ?array
    {
        $labels = [
            'name' => __('Kategórie výdavkov', THEME_DOMAIN),
            'singular_name' => __('Kategória výdavkov', THEME_DOMAIN),
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
            'description' => __("Kategórie výdavkov", THEME_DOMAIN),
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
            'post_types' => [],
            'slug' => self::getTaxonomySlug(),
            'args' => $args,
        ];
    }

    public function getLogLinkAttribute(): ?string
    {
        return get_edit_term_link($this->term_id, self::getTaxonomySlug());
    }

    public function getLogTitleAttribute(): string
    {
        return "{$this->term->name}";
    }
}