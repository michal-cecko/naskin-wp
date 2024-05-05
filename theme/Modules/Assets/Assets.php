<?php
namespace Theme\Modules\Assets;

use Saurus\App\Modules\Assets\Assets as SaurusAssets;

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
        wp_enqueue_style(handle: 'example-static-css', src: $this->static('styles/vendor/bootstrap.css'), ver: $this->ver);
        wp_enqueue_style(handle: 'example-page-dynamic-scss', src: $this->dynamic('styles/pages/some-page.scss'), ver: $this->ver);
        wp_enqueue_style(handle: 'example-general-dynamic-scss', src: $this->dynamic('styles/general/general.scss'), ver: $this->ver);
    }

    /**
    * @action wp_enqueue_scripts
    */
    public function frontendScripts(): void
    {
        $this->enqueueCommonsScript();

        wp_enqueue_script(handle: 'general-js', src: $this->dynamic('scripts/general.js'), ver: $this->ver);

        wp_enqueue_script(handle: "recaptcha-js", src: "https://www.google.com/recaptcha/api.js?render=" . config("recaptcha.key"), ver: $this->ver);

        if(is_page_template("template-example.blade.php")) {
            wp_enqueue_script(handle: 'example-js', src: $this->dynamic('scripts/components/example.js'), ver: $this->ver);
        }
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






















