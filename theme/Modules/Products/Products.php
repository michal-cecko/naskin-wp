<?php

namespace Theme\Modules\Products;

use Theme\Modules\Customers\CustomerEditPageMetaboxComponent;
use Theme\PostTypes\Customer;
use Theme\PostTypes\Product;

class Products {
    /**
     * @action add_meta_boxes
     * @return void
     */
    public function registerMetaboxForShowingProductAppointmentsTable(): void
    {
        global $post;
        $product = Product::with(["productSales.appointment", "productSales.appointment", "appointments.employee", "appointments.customer", "appointments.services"])->where("id", $post?->ID)->first();
        if(!$product) return;

        $this->salesMetabox($product);
        $this->appointmentsMetabox($product);
    }

    private function appointmentsMetabox($product): void
    {
        $wrapper = new ProductEditPageAppointmentsMetaboxComponent($product);
        $content = $wrapper->generate();

        main()->metaboxes()->registerMetabox(id: "product_appointments_table", title: "Predaný v rezerváciach", viewOrHtml: $content, postType: Product::getPostTypeSlug(), passedHtmlToViewParam: true, context: "normal", priority: "high");
    }

    private function salesMetabox($product): void
    {
        $wrapper = new ProductEditPageSalesMetaboxComponent($product);
        $content = $wrapper->generate();

        main()->metaboxes()->registerMetabox(id: "product_sales_table", title: "Celkové predania", viewOrHtml: $content, postType: Product::getPostTypeSlug(), passedHtmlToViewParam: true, context: "normal", priority: "high");
    }
}