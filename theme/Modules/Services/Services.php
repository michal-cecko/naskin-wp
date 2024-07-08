<?php

namespace Theme\Modules\Services;

use Theme\Services\Services\ServiceCategoryService;

class Services {
    public function __construct()
    {
        add_action('PTO/order_update_complete', [$this, 'invalidate_frontend_cache_when_reordering'], 10, 3);
    }

    public function invalidate_frontend_cache_when_reordering(): void
    {
        ServiceCategoryService::invalidateServiceCategoryCache();
    }
}