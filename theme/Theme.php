<?php

namespace Theme;

use Saurus\App\Main;
use Saurus\App\Modules\Singleton\Singleton;
use Theme\Modules\Assets\Assets;
use Theme\Modules\Ics\Ics;
use Theme\Modules\Plugins\Acf;
use Theme\Modules\Setup\BladeDirectives;
use Theme\Modules\Setup\ThemeSetup;

class Theme extends Singleton
{
    private Assets $assets;
    private ThemeSetup $themeSetup;
    private Ics $ics;
    private object $acf;
    private object $bladeDirectives;

    protected function __construct()
    {
        //Modules initialization
        $this->assets = Main::initModule(new Assets());
        $this->themeSetup = Main::initModule(new ThemeSetup());
        $this->acf = Main::initModule(new Acf());
        $this->ics = Main::initModule(new Ics());
        $this->bladeDirectives = Main::initModule(new BladeDirectives());
    }


    // Modules getters

    public function acf(): Acf
    {
        return $this->acf;
    }

    public function ics(): Ics
    {
        return $this->ics;
    }
}