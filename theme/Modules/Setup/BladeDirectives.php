<?php

namespace Theme\Modules\Setup;

use Saurus\App\Modules\Templates\BladeDirectives as SaurusBladeDirectives;

class BladeDirectives extends SaurusBladeDirectives
{
    public function __construct()
    {
        //Add your theme-based directives before calling the parent constructor
        parent::__construct();

        $this->setupDirectives();
    }

    /**
     * Add custom THEME-BASED directives.
     *
     * @see SaurusBladeDirectives
     * @return void
     */
    private function setupDirectives(): void
    {
        // Add here, if you dont know how
    }
}