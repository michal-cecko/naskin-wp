<?php

namespace Theme\PostTypes;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Saurus\App\Modules\Wordpress\Posts\PostType;
use Saurus\App\Modules\Log\ILoggable;
use Theme\Models\Appointment\Appointment;
use Theme\Models\Product\ProductSale;

class Product extends PostType implements ILoggable
{
    protected $appends = ['price'];

    public function getPriceAttribute() : ?float {
        return get_field("prod_price", $this->id);
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
        return "product";
    }

    public function productSales(): HasMany
    {
        return $this->hasMany(ProductSale::class, "product_id");
    }

    public function appointments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Appointment::class,
            ProductSale::class,
            "product_id",
            'id',
            'id',
            "appointment_id"
        )->orderBy("id", "DESC");
    }

    public static function registerCustomPostType(): ?array
    {
        $labels = array(
            'name' => __('Produkty', 'naskin'),
            'singular_name' => __('Produkt', 'naskin'),
            'add_new' => __('Pridať nový produkt', 'naskin'),
            'add_new_item' => __('Pridať nový produkt', 'naskin'),
            'edit_item' => __('Upraviť produkt', 'naskin'),
            'new_item' => __('Nový produkt', 'naskin'),
            'view_item' => __('Otvoriť produkt', 'naskin'),
            'search_items' => __('Hľadať produkt', 'naskin'),
            'not_found' => __('Produkt nebol nájdený', 'naskin'),
            'not_found_in_trash' => __('Produkt nebol nájdený v koši', 'naskin')
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

            'capability_type' => ['product', 'products'],
            'capabilities' => [
                'edit_post'                 => 'edit_product',
                'read_post'                 => 'read_product',
                'delete_post'               => 'delete_product',
                'create_posts'              => 'create_products',
                'delete_posts'              => 'delete_products',
                'delete_others_posts'       => 'delete_others_products',
                'delete_private_posts'      => 'delete_private_products',
                'delete_published_posts'    => 'delete_published_products',
                'edit_posts'                => 'edit_products',
                'edit_others_posts'         => 'edit_others_products',
                'edit_private_posts'        => 'edit_private_products',
                'edit_published_posts'      => 'edit_published_products',
                'publish_posts'             => 'publish_products',
                'read_private_posts'        => 'read_private_products'
            ],
            'map_meta_cap' => true,

            'menu_icon' => 'dashicons-cart',
            'rewrite' => ['slug' => self::getPostTypeSlug()],
        );

        return [
            'slug' => self::getPostTypeSlug(),
            'args' => $args,
        ];
    }
}