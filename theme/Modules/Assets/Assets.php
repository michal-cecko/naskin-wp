<?php
namespace Theme\Modules\Assets;

use Saurus\App\Modules\Assets\Assets as SaurusAssets;
use Theme\Modules\Views\TaxonomyServiceCategory;
use Theme\Taxonomies\ServiceCategory;

class Assets extends SaurusAssets
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @action wp_enqueue_scripts
     */
    public function frontendStyles(): void
    {
        global $post;

        wp_enqueue_style(handle: 'general-scss', src: $this->dynamic('styles/imports/general.scss'), ver: $this->ver);

        if(is_front_page()) {
            wp_enqueue_style(handle: 'homepage-scss', src: $this->dynamic('styles/pages/front-page.scss'), ver: $this->ver);
        }

        if(is_tax( ServiceCategory::getTaxonomySlug() )) {
            wp_enqueue_style(handle: 'service-scss', src: $this->dynamic('styles/pages/service-taxonomy-single.scss'), ver: $this->ver);
        }

        if(str_ends_with(get_page_template(), "pages/template-cennik.blade.php")) {
            wp_enqueue_style(handle: 'cennik-scss', src: $this->dynamic('styles/pages/template-cennik.scss'), ver: $this->ver);
        }
    }

    /**
    * @action wp_enqueue_scripts
    */
    public function frontendScripts(): void
    {
        $this->enqueueCommonsScript();

        wp_enqueue_script(handle: 'general-js', src: $this->dynamic('scripts/general.js'), ver: $this->ver);

        wp_enqueue_script('vue-js', 'https://cdn.jsdelivr.net/npm/vue/dist/vue.min.js');
        wp_enqueue_script('moment-js', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js');
        wp_enqueue_script('moment-js-locale', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/sk.min.js');

        wp_enqueue_script(handle: 'reservation-js', src: $this->dynamic('scripts/components/reservation-form.js'), ver: $this->ver);
        wp_enqueue_script(handle: 'header-js', src: $this->dynamic('scripts/components/header.js'), ver: $this->ver);


        if(is_tax( ServiceCategory::getTaxonomySlug() )) {
            wp_enqueue_script('swiper-js', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/9.0.5/swiper-bundle.min.js');
            wp_enqueue_style('swiper-css', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/9.0.5/swiper-bundle.css');

            wp_enqueue_script(handle: 'service-js', src: $this->dynamic('scripts/components/service-taxonomy-single.js'), ver: $this->ver);
        }

        wp_enqueue_script(handle: "recaptcha-js", src: "https://www.google.com/recaptcha/api.js?render=" . config("integrations.recaptcha.key"), ver: $this->ver);
    }

    /**
     * @action admin_enqueue_scripts
     */
    public function adminAssets(): void
    {
        $this->enqueueCommonsScript();

        wp_enqueue_script(handle: 'admin-js', src: $this->dynamic('scripts/admin.js'), ver: $this->ver);
        wp_enqueue_style(handle: 'admin-css', src: $this->dynamic('styles/admin/admin.scss'), ver: $this->ver);
    }
}






















