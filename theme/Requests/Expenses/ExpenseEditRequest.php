<?php

namespace Theme\Requests\Expenses;

use Saurus\App\Rules\Exists;
use Saurus\App\Rules\PostExists;
use Theme\Models\Expense\Expense;
use Theme\PostTypes\Product;
use Theme\Requests\AuthenticatedAdminRequest;

class ExpenseEditRequest extends AuthenticatedAdminRequest
{
    public function authorize() : bool
    {
        $user = main()->api()->getUserViaSessionCookie();
        return user_can($user, "edit_expenses");
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', new Exists(Expense::getTableName(), "id")],

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