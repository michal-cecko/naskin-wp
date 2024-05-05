<?php

namespace Theme\Modules\Views;

use Saurus\App\Modules\Templates\Views\SingleView;

class SingleExample extends SingleView
{
    public function __construct()
    {
        parent::__construct();
    }

    public function provideData(): array
    {
        return $this->returnData;
    }
}