<?php

namespace Theme\Requests\Expenses;

use Saurus\App\Rules\Exists;
use Saurus\App\Rules\PostExists;
use Theme\Models\Expense\Expense;
use Theme\PostTypes\Product;
use Theme\Requests\AuthenticatedAdminRequest;

class ExpenseDeleteRequest extends AuthenticatedAdminRequest
{
    public function authorize() : bool
    {
        $user = main()->api()->getUserViaSessionCookie();
        return user_can($user, "delete_expenses");
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', new Exists(Expense::getTableName(), "id")],
        ];
    }
}