<?php

namespace Theme\PostTypes;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Saurus\App\Modules\Wordpress\Posts\PostType;
use Theme\Models\Appointment\Appointment;

class Customer extends PostType
{
    public function appointments(): HasMany {
        return $this->hasMany(Appointment::class, 'customer_id', 'ID');
    }

    public function appointmentsInLatestOrder() : HasMany {
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
            'has_archive' => FALSE,
            'show_in_rest' => FALSE,
            'taxonomy' => [],
            'menu_icon' => 'dashicons-admin-users',
            'rewrite' => ['slug' => self::getPostTypeSlug()],
        );

        return [
            'slug' => self::getPostTypeSlug(),
            'args' => $args,
        ];
    }
}