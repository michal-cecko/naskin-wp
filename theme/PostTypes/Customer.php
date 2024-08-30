<?php

namespace Theme\PostTypes;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Saurus\App\Modules\Wordpress\Posts\PostType;
use Theme\Models\Appointment\Appointment;
use Saurus\App\Modules\Log\ILoggable;

class Customer extends PostType implements ILoggable
{
    public function appointments(): HasMany {
        return $this->hasMany(Appointment::class, 'customer_id', 'ID');
    }

    public function latestAppointments() : HasMany {
        return $this->appointments()->orderBy('start_at', 'DESC');
    }

    public function getNameAttribute() : string {
        return $this->title;
    }

    public function getPhoneAttribute() : ?string {
        return get_field("cust_phone", $this->id);
    }

    public function getEmailAttribute() : ?string {
        return get_field("cust_email", $this->id);
    }

    public function getLogLinkAttribute(): ?string
    {
        return $this->edit_link;
    }

    public function getLogTitleAttribute(): string
    {
        return $this->title;
    }

    public static function getPostTypeSlug(): string
    {
        return "customer";
    }

    public static function registerCustomPostType(): ?array
    {

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

            'capability_type' => ['customer', 'customers'],
            'capabilities' => [
                'edit_post'                 => 'edit_customer',
                'read_post'                 => 'read_customer',
                'delete_post'               => 'delete_customer',
                'create_posts'              => 'create_customers',
                'delete_posts'              => 'delete_customers',
                'delete_others_posts'       => 'delete_others_customers',
                'delete_private_posts'      => 'delete_private_customers',
                'delete_published_posts'    => 'delete_published_customers',
                'edit_posts'                => 'edit_customers',
                'edit_others_posts'         => 'edit_others_customers',
                'edit_private_posts'        => 'edit_private_customers',
                'edit_published_posts'      => 'edit_published_customers',
                'publish_posts'             => 'publish_customers',
                'read_private_posts'        => 'read_private_customers'
            ],
            'map_meta_cap' => true,

            'menu_icon' => 'dashicons-admin-users',
            'rewrite' => ['slug' => self::getPostTypeSlug()],
        );

        return [
            'slug' => self::getPostTypeSlug(),
            'args' => $args,
        ];
    }
}