<?php

namespace Theme\Modules\Services;

use Theme\Helpers\WordpressHelper;
use Theme\PostTypes\Service;
use Theme\Services\Services\ServiceCategoryService;
use Theme\Taxonomies\ServiceCategory;
use Saurus\App\Modules\Wordpress\Models\User;
use Theme\Users\Employee;

class ServicesTransient {
    public function __construct()
    {
        //Defined manually because multiple hooks on single method.
        add_action('created_term', [$this, 'invalidate_cache_on_service_category_save'], 10, 3);
        add_action('edited_term', [$this, 'invalidate_cache_on_service_category_save'], 10, 3);
        add_action('user_register', [$this, 'invalidate_cache_on_user_save']);
        add_action('profile_update', [$this, 'invalidate_cache_on_user_save']);
    }

    /**
     * Invalidates the cache when a service is saved.
     *
     * @action save_post 10 3
     */
    function invalidate_cache_on_service_save($post_id, $post, $update): void
    {
        if (main()->wpHelper()->isAutoSave()) {
            return;
        }

        if ($post->post_type !== Service::getPostTypeSlug()) {
            return;
        }

        ServiceCategoryService::invalidateServiceCategoryCache();
    }

    /**
     * Invalidates the cache when a service category is saved.
     */
    function invalidate_cache_on_service_category_save($term_id, $tt_id, $taxonomy): void
    {
        if ($taxonomy !== ServiceCategory::getTaxonomySlug()) {
            return;
        }

        ServiceCategoryService::invalidateServiceCategoryCache();
    }

    // User registration function
    function invalidate_cache_on_user_save($user_id): void
    {
        $user = Employee::where("ID", $user_id)->first();
        if(!$user) {
            return;
        }

        ServiceCategoryService::invalidateServiceCategoryCache();
    }

}