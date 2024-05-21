<?php

namespace Theme\Modules\Customers;

use Saurus\App\Enums\ApiMethod;
use Saurus\App\Traits\Validation;
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
            $q->where('meta_value', 'LIKE', "%$search%")->whereIn("meta_key", ['cust_name', 'cust_phone', 'cust_email4']);
        })->get();

        $finalPosts = [];

        foreach ($customers as $customer) {
            $finalPosts[] = [
                'id' => $customer->id,
                'name' => $customer->title,
                'email' => $customer->email,
                'phone' => $customer->phone,
            ];
        }

        wp_send_json_success($finalPosts);
    }
}