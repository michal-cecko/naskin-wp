<?php

namespace Theme\Requests\Expenses;

use Saurus\App\Requests\Request;
use Saurus\App\Rules\PostExists;
use Theme\PostTypes\Product;

class ExpenseCreateRequest extends Request
{
    public function authorize() : bool
    {
        $user = main()->api()->getUserViaSessionCookie();
        return user_can($user, "create_expenses");
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'min:1'],
            'product_id' => ['sometimes', 'nullable', 'integer', new PostExists(postModel: Product::class)],

            'bought_at' => 'sometimes|nullable|date',

            'description' => 'required|string',
            'note' => 'sometimes|nullable|string',
            'supplier' => 'sometimes|nullable|string|max:255',

            'price' => 'required|decimal:0,2',
        ];
    }
}