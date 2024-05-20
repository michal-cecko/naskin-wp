<?php 

namespace Theme\Modules\Setup;

class ThemeSetup
{
    public function __construct()
    {
        $this->removeComments();
    }

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

    public function removeComments(): void
    {
        // Disable support for comments and trackbacks in post types
        foreach (get_post_types() as $post_type) {
            if (post_type_supports($post_type, 'comments')) {
                remove_post_type_support($post_type, 'comments');
                remove_post_type_support($post_type, 'trackbacks');
            }
        }

        //Close comments on the front-end
        add_filter('comments_open', '__return_false', 20, 2);
        add_filter('pings_open', '__return_false', 20, 2);

        // Hide existing comments
        add_filter('comments_array', '__return_empty_array', 10, 2);

        // Remove comments page in menu
        add_action('admin_menu', function () {
            remove_menu_page('edit-comments.php');
        });

        // Remove comments links from admin bar
        add_action('init', function () {
            if (is_admin_bar_showing()) {
                remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
            }
        });

        //TURN OFF POSTS
        add_action('admin_menu', function () {
            remove_menu_page('edit.php');
        });
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
