<?php

namespace Theme\Requests\ProductSales;

use Saurus\App\Requests\Request;
use Saurus\App\Rules\PostExists;
use Theme\Enum\AppointmentType;
use Theme\Enum\ProductSale\ProductSalePaymentType;
use Theme\Models\Appointment\Appointment;
use Theme\PostTypes\Product;

class ProductSalesCreateRequest extends Request
{
    public function authorize() : bool
    {
        $user = main()->api()->getUserViaSessionCookie();
        return user_can($user, "create_product_sales");
    }

    public function rules(): array
    {
        return [
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