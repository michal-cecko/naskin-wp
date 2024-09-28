<?php

namespace Theme\Modules\Customers;

use Saurus\App\Enums\ApiMethod;
use Saurus\App\Traits\Validation;
use Theme\Modules\Customers\Table\ProductDetailAppointmentsTableComponent;
use Theme\PostTypes\Customer;
use Theme\Requests\Customers\CustomerSearchRequest;

class Customers {
    use Validation;

    public function __construct()
    {
        $this->initRest();
    }

    private function initRest(): void
    {
        main()->api()->addApiEndpoint(ApiMethod::GET, "/customers/search", "customer.search", [$this, 'searchCustomers']);
    }

    /**
     * @action add_meta_boxes
     * @return void
     */
    public function registerMetaboxForShowingCustomersAppointmentsTable(): void
    {
        global $post;
        $customer = Customer::with("latestAppointments.services")->where("id", $post?->ID)->first();
        if(!$customer) return;

        $wrapper = new CustomerEditPageMetaboxComponent($customer);
        $content = $wrapper->generate();

        main()->metaboxes()->registerMetabox(id: "customer_appointments_table", title: "Rezervácie", viewOrHtml: $content, postType: Customer::getPostTypeSlug(), passedHtmlToViewParam: true, context: "normal", priority: "high");
    }

    /**
     * Mutator for customer name ACF field on post title update
     *
     * @action save_post_customer 10 3
     */
    public function update_cust_name_meta_on_title_update($post_id, $post, $update): int|bool
    {
        $new_title = $post->post_title;

        if ($update) {
            return update_post_meta($post_id, 'cust_name', $new_title);
        }

        return add_post_meta($post_id, 'cust_name', $new_title, true);
    }

    public function searchCustomers(CustomerSearchRequest $request): void
    {
        $data = $request->validated();

        $search = strtolower($data['search']);

        $customers = Customer::whereHas('meta', function ($q) use ($search) {
            $q->where('meta_value', 'LIKE', "%$search%")->whereIn("meta_key", ['cust_name', 'cust_phone', 'cust_email']);
        })->get();

        $finalPosts = [];

        foreach ($customers as $customer) {
            $finalPosts[] = [
                'id' => $customer->id,
                'edit_link' => $customer->edit_link,
                'name' => $customer->title,
                'email' => $customer->email,
                'phone' => $customer->phone,
            ];
        }

        wp_send_json_success($finalPosts);
    }
}