<?php

namespace Theme\Requests\ProductSales;

use Saurus\App\Rules\Exists;
use Saurus\App\Rules\PostExists;
use Theme\Enum\ProductSale\ProductSalePaymentType;
use Theme\Models\Appointment\Appointment;
use Theme\Models\Expense\Expense;
use Theme\PostTypes\Product;
use Theme\Requests\AuthenticatedAdminRequest;

class ProductSalesEditRequest extends AuthenticatedAdminRequest
{
    public function authorize() : bool
    {
        $user = main()->api()->getUserViaSessionCookie();
        return user_can($user, "edit_product_sales");
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', new Exists(Expense::getTableName(), "id")],

            'product_id' => ['required', 'integer', new PostExists(postModel: Product::class)],
            'appointment_id' => ['sometimes', 'nullable', 'integer', new PostExists(postModel: Appointment::class)],

            'sold_at' => 'sometimes|nullable|date',

            'quantity' => 'required|decimal:0,2|min:0.01',
            'note' => 'sometimes|nullable|string',

            'payment_type' => 'sometimes|nullable|in:' . implode(",", ProductSalePaymentType::stringCases()),

            'price' => 'required|decimal:0,2',
        ];
    }
}