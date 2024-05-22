<?php

namespace Theme\Services\Services;

use Theme\Enum\AppointmentTransient;
use Theme\Mappers\ServiceCategoryMapper;
use Theme\Taxonomies\ServiceCategory;

class ServiceCategoryService {
    public static function getServiceCategories()
    {
        $transient_key = AppointmentTransient::FRONTEND_GETTER->value;
        $expiration_time = 30 * 24 * 60 * 60; // 30 days

        $categories = get_transient($transient_key);

        if ($categories === false) {
            $categories = ServiceCategoryMapper::collection(ServiceCategory::with(['publishedPosts', 'term'])->get());
            set_transient($transient_key, $categories, $expiration_time);
        }

        return $categories;
    }

    public static function invalidateServiceCategoryCache(): void
    {
        $transient_key = AppointmentTransient::FRONTEND_GETTER->value;
        delete_transient($transient_key);
    }
}