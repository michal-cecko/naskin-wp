<?php

namespace Theme\Services\Expenses;

use Exception;
use Carbon\Carbon;
use Saurus\App\Exceptions\Request\RequestException;
use Theme\Models\Expense\Expense;
use Theme\PostTypes\Product;
use Theme\Taxonomies\ExpenseCategory;

class ExpenseService{
    /**
     * @throws Exception
     */
    public static function store(ExpenseCategory $category, float $price, string $description, ?Product $product = null, ?string $note = null, ?string $supplier = null, ?Carbon $bought_at = null) : Expense {
        return Expense::create([
            'category_id' => $category->term_id,
            'product_id' => $product?->ID,
            'description' => $description,
            'note' => $note,
            'bought_at' => $bought_at,
            'supplier' => $supplier,
            'price' => $price,
        ]);
    }

    /**
     * @throws RequestException
     */
    public static function update(Expense|int $expense, ExpenseCategory $category, float $price, string $description, ?Product $product = null, ?string $note = null, ?string $supplier = null, ?Carbon $bought_at = null): array
    {
        if (is_int($expense)) {
            $expense = Expense::find($expense);
            if (!$expense) throw new RequestException("Výdavok nebol nájdený.", 404);
        }

        $expense->fill([
            'category_id' => $category->term_id,
            'product_id' => $product?->ID,
            'description' => $description,
            'note' => $note,
            'bought_at' => $bought_at,
            'supplier' => $supplier,
            'price' => $price,
        ]);
        $changes = $expense->getChangedColumns();
        $expense->save();

        return ['expense' => $expense, 'changes' => $changes];
    }

    /**
     * @throws RequestException
     */
    public static function delete(Expense|int $expense): ?bool
    {
        if (is_int($expense)) {
            $expense = Expense::find($expense);
            if (!$expense) throw new RequestException("Expense was not found.", 404);
        }

        return $expense->delete();
    }
}