<?php

namespace Theme\PostTypes;

use Illuminate\Support\Collection;
use Saurus\App\Modules\Wordpress\Posts\PostType;
use Theme\Taxonomies\ServiceCategory;
use Theme\Users\Employee;

class Service extends PostType
{
    public function getDurationAttribute(): int
    {
        return (int) get_field("serv-duration", $this->id);
    }

    public function getPriceAttribute(): float
    {
        return (float) get_field("serv-price", $this->id);
    }

    public function getDescriptionAttribute() {
        return get_field("serv-description", $this->id);
    }

    public function getImageAttribute() {
        return get_field("serv-image", $this->id);
    }

    public function getServiceCategoryAttribute() {
        if(!$this->relationLoaded("taxonomies.term")) {
            $this->load("taxonomies.term");
        }

        return ServiceCategory::find($this->taxonomies->firstWhere("taxonomy", ServiceCategory::getTaxonomySlug())?->term_id);
    }

    public function getServiceCategoryIdAttribute() {
        return $this->service_category?->term?->term_id;
    }

    public function getEmployeesAttribute(): Collection
    {
        if ($this->employeesArr !== null) {
            return $this->employeesArr;
        }

        $this->employeesArr = Employee::whereHas('meta', function($query) {
            $query->where('meta_key', 'services')->where('meta_value', 'LIKE', "%:\"{$this->id}\";%");
        })->get();

        return $this->employeesArr;
    }

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