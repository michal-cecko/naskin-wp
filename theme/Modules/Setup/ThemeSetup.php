<?php 

namespace Theme\Modules\Setup;

class ThemeSetup
{
    /**
     * @action after_setup_theme
     */
    public function themeSupports(): void
    {
        add_theme_support('menus');
        add_theme_support('post-thumbnails');
        add_theme_support('title-tag');
        add_theme_support('html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption']);
        add_theme_support('custom-logo', ['class' => 'custom-logo']);
        remove_theme_support("core-block-patterns");
    }

    /**
     * @action after_setup_theme
     */
    public function registerMenus(): void
    {
        register_nav_menus([
            //...
        ]);
    }
}
