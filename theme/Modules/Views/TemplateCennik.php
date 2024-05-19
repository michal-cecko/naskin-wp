<?php

namespace Theme\Modules\Views;

use Saurus\App\Interfaces\IViewModule;
use Theme\Taxonomies\ServiceCategory;

class TemplateCennik implements IViewModule
{
    private function getServiceCategories(): iterable
    {
        return ServiceCategory::with("publishedPosts")->get();

    }

    public function provideData() : array
    {
        $toReturn = [];

        $toReturn['categories'] = $this->getServiceCategories();

        return $toReturn;
    }
}