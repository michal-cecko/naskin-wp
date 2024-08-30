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
        return (int)get_field("serv-duration", $this->id);
    }

    public function getPriceAttribute(): float
    {
        return (float)get_field("serv-price", $this->id);
    }

    public function getDescriptionAttribute()
    {
        return get_field("serv-description", $this->id);
    }

    public function getImageAttribute()
    {
        return get_field("serv-image", $this->id);
    }

    public function getServiceCategoryAttribute()
    {
        if (!$this->relationLoaded("taxonomies.term")) {
            $this->load("taxonomies.term");
        }

        return ServiceCategory::find($this->taxonomies->firstWhere("taxonomy", ServiceCategory::getTaxonomySlug())?->term_id);
    }

    public function getIsHiddenFromWebsiteAttribute(): string
    {
        $isHidden = get_field("is_hidden_from_website", $this->acf_id);
        return !empty($isHidden);
    }

    public function getServiceCategoryIdAttribute()
    {
        return $this->service_category?->term?->term_id;
    }

    public function getEmployeesAttribute(): Collection
    {
        if ($this->employeesArr !== null) {
            return $this->employeesArr;
        }

        $this->employeesArr = Employee::whereHas('meta', function ($query) {
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
        $slug = self::getPostTypeSlug();

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
            'publicly_queryable' => FALSE,
            'has_archive' => FALSE,
            'show_in_rest' => FALSE,
            'taxonomy' => [],

            'capability_type' => ['service', 'services'],
            'capabilities' => [
                'edit_post'                 => 'edit_service',
                'read_post'                 => 'read_service',
                'delete_post'               => 'delete_service',
                'create_posts'              => 'create_services',
                'delete_posts'              => 'delete_services',
                'delete_others_posts'       => 'delete_others_services',
                'delete_private_posts'      => 'delete_private_services',
                'delete_published_posts'    => 'delete_published_services',
                'edit_posts'                => 'edit_services',
                'edit_others_posts'         => 'edit_others_services',
                'edit_private_posts'        => 'edit_private_services',
                'edit_published_posts'      => 'edit_published_services',
                'publish_posts'             => 'publish_services',
                'read_private_posts'        => 'read_private_services'
            ],
            'map_meta_cap' => true,

            'menu_icon' => 'dashicons-admin-tools',
            'rewrite' => ['slug' => $slug],
        );

        return [
            'slug' => $slug,
            'args' => $args,
        ];
    }
}