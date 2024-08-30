<?php

namespace Theme\Modules\Plugins;

class Acf
{
    public function __construct()
    {
        $this->registerOptionsPage();
    }

    public function registerOptionsPage(): void
    {
        if (!function_exists("acf_add_options_page")) return;

        acf_add_options_page([
            'page_title' => 'Nastavenia stránky',
            'menu_title' => 'Nastavenia stránky',
            'menu_slug' => 'theme-general-settings',
            'capability' => 'view_acf_web_options',
            'redirect' => FALSE
        ]);

    }
}
