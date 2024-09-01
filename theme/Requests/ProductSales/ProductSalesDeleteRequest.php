<?php

namespace Theme\Requests\ProductSales;

use Saurus\App\Rules\Exists;
use Saurus\App\Rules\PostExists;
use Theme\Models\Expense\Expense;
use Theme\Models\Product\ProductSale;
use Theme\PostTypes\Product;
use Theme\Requests\AuthenticatedAdminRequest;

class ProductSalesDeleteRequest extends AuthenticatedAdminRequest
{
    public function authorize() : bool
    {
        $user = main()->api()->getUserViaSessionCookie();
        return user_can($user, "delete_product_sales");
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', new Exists(ProductSale::getTableName(), "id")],
        ];
    }
}