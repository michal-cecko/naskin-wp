<?php

namespace Theme\Modules\Views;

use Saurus\App\Modules\Templates\Views\TaxonomyView;

class TaxonomyServiceCategory extends TaxonomyView
{
    protected bool $eagerLoadPosts = true;

    public function __construct()
    {
        parent::__construct();
    }

    private function getServices() {
        return $this->term->publishedPostsGroupedByTitle;
    }

    private function getAcfValues(): bool|array
    {
        return get_fields("{$this->taxonomy::getTaxonomySlug()}_{$this->term->term_id}") ?? [];
    }

    public function provideData(): array
    {
        $data = $this->returnData;

        $data['services'] = $this->getServices();
        $data['acf'] = $this->getAcfValues();

        return $data;
    }
}