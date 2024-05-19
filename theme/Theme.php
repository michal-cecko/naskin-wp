<?php

namespace Theme;

use Saurus\App\Main;
use Saurus\App\Modules\Singleton\Singleton;
use Theme\Modules\Appointments\Appointments;
use Theme\Modules\Assets\Assets;
use Theme\Modules\Ics\Ics;
use Theme\Modules\Plugins\Acf;
use Theme\Modules\Services\ServicesTransient;
use Theme\Modules\Setup\BladeDirectives;
use Theme\Modules\Setup\ThemeSetup;

class Theme extends Singleton
{
    private Assets $assets;
    private ThemeSetup $themeSetup;
    private Ics $ics;
    private Acf $acf;
    private ServicesTransient $servicesTransient;
    private Appointments $appointments;
    private BladeDirectives $bladeDirectives;

    protected function __construct()
    {
        //Modules initialization
        $this->assets = Main::initModule(new Assets());
        $this->themeSetup = Main::initModule(new ThemeSetup());
        $this->acf = Main::initModule(new Acf());
        $this->ics = Main::initModule(new Ics());
        $this->servicesTransient = Main::initModule(new ServicesTransient());
        $this->appointments = Main::initModule(new Appointments());
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