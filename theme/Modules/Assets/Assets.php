<?php
namespace Theme\Modules\Assets;

use Saurus\App\Modules\Assets\Assets as SaurusAssets;
use Theme\Modules\Views\TaxonomyServiceCategory;
use Theme\Taxonomies\ServiceCategory;
use Theme\Users\User;

class Assets extends SaurusAssets
{
    private ?User $user;

    public function __construct()
    {
        parent::__construct();

        $this->user = User::where("ID", get_current_user_id() ?? -1)->first();
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

        $this->enqueueVue();
        $this->enqueueMomentJS();
        $this->enqueueLordicon();

        wp_enqueue_script(handle: 'reservation-js', src: $this->dynamic('scripts/components/reservation-form.js'), ver: $this->ver);
        wp_enqueue_script(handle: 'header-js', src: $this->dynamic('scripts/components/header.js'), ver: $this->ver);

        if( in_array($this->user?->role, ['together-employee', 'employee']) ){
            wp_enqueue_style(handle: 'employee_role_web-scss', src: $this->dynamic('styles/admin/employee_role_web.scss'), ver: $this->ver);
        }

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

        $this->enqueueVue();
        $this->enqueueMomentJS();
        $this->enqueueLordicon();
        $this->enqueuePrimevue();

        wp_enqueue_script(handle: 'admin-js', src: $this->dynamic('scripts/admin.js'), ver: $this->ver);
        wp_enqueue_script(handle: 'calendar-js', src: $this->dynamic('scripts/components/admin/calendar.js'), ver: $this->ver);
        wp_enqueue_style(handle: 'admin-scss', src: $this->dynamic('styles/admin/admin.scss'), ver: $this->ver);
        wp_enqueue_style(handle: 'calendar-scss', src: $this->dynamic('styles/admin/calendar.scss'), ver: $this->ver);


        if( in_array($this->user?->role, ['together-employee', 'employee']) ){
            wp_enqueue_style(handle: 'employee_role_dashboard-scss', src: $this->dynamic('styles/admin/employee_role_dashboard.scss'), ver: $this->ver);
        }
    }





    // Helpers

    private function enqueueVue() : void {
        wp_enqueue_script('vue-js', 'https://unpkg.com/vue@3/dist/vue.global.js');
    }

    private function enqueueMomentJS() : void {
        wp_enqueue_script('moment-js', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js');
        wp_enqueue_script('moment-js-locale', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/sk.min.js');
    }

    private function enqueueLordicon() : void {
        wp_enqueue_script('lordicon-js', 'https://cdn.lordicon.com/libs/mssddfmo/lord-icon-2.1.0.js');
    }

    private function enqueuePrimevue() : void {
        wp_enqueue_script('primevue-js', 'https://unpkg.com/primevue/core/core.min.js');
        wp_enqueue_style('primevue-theme-css', 'https://unpkg.com/primevue/resources/themes/lara-light-blue/theme.css');

        wp_enqueue_script('pvc-confirm-dialog', 'https://unpkg.com/primevue/confirmdialog/confirmdialog.min.js');
        wp_enqueue_script('pvc-dialog', 'https://unpkg.com/primevue/dialog/dialog.min.js');
        wp_enqueue_script('pvc-input-text', 'https://unpkg.com/primevue/inputtext/inputtext.min.js');
        wp_enqueue_script('pvc-multiselect', 'https://unpkg.com/primevue/multiselect/multiselect.min.js');
        wp_enqueue_script('pvc-datepicker', 'https://unpkg.com/primevue/calendar/calendar.min.js');
    }
}






















