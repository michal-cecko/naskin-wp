<?php

namespace Theme\Modules\Expenses;

use Carbon\Carbon;
use Saurus\App\Enums\ApiMethod;
use Saurus\App\Exceptions\Request\RequestException;
use Saurus\App\Exceptions\Request\ValidationFailedException;
use Saurus\App\Exceptions\Route\ApiEndpointAlreadyExistException;
use Saurus\App\Traits\Validation;
use Theme\Enum\User\Role;
use Theme\Models\Expense\Expense;
use Theme\PostTypes\Product;
use Theme\Requests\Expenses\ExpenseCreateRequest;
use Theme\Requests\Expenses\ExpenseDeleteRequest;
use Theme\Requests\Expenses\ExpenseEditRequest;
use Theme\Services\Expenses\ExpenseService;
use Theme\Taxonomies\ExpenseCategory;

class Expenses
{
    use Validation;

    /**
     * @throws ApiEndpointAlreadyExistException
     */
    public function __construct()
    {
        $this->initRest();

    }

    /**
     * @throws ApiEndpointAlreadyExistException
     */
    private function initRest(): void
    {
        main()->api()->addApiEndpoint(ApiMethod::POST, "/expense/delete", "expense.delete", [$this, 'delete']);
        main()->api()->addApiEndpoint(ApiMethod::POST, "/expense/store", "expense.store", [$this, 'store']);
        main()->api()->addApiEndpoint(ApiMethod::POST, "/expense/edit", "expense.edit", [$this, 'edit']);
    }

    /**
     * @throws RequestException
     * @throws \Exception
     */
    public function store(ExpenseCreateRequest $request): void
    {
        $data = $request->validated();

        $category = $this->getCategory($data['category_id'] ?? null);
        $product = $this->getProduct($data['product_id'] ?? null);

        $expense = ExpenseService::store(
            category: $category,
            price: $data['price'],
            description: $data['description'] ?? null,
            product: $product,
            note: $data['note'] ?? null,
            supplier: $data['supplier'] ?? null,
            bought_at: !empty($data['bought_at']) ? Carbon::parse($data['bought_at']) : null,
        );

        main()->log()->infoDB("Vytvorený nový výdavok: {$expense->log_string}.", resources: [$expense, $product, $category], additionalData: ['owner_only' => $category->owner_only]);

        wp_send_json_success(['message' => 'Výdavok bol úspešne pridaný.', 'id' => $expense->id]);
    }

    /**
     * @throws RequestException
     * @throws \Exception
     */
    public function edit(ExpenseEditRequest $request): void
    {
        $data = $request->validated();

        $category = $this->getCategory($data['category_id'] ?? null);
        $product = $this->getProduct($data['product_id'] ?? null);

        [$expense, $changes] = ExpenseService::update(
            expense: (int)$data['id'],
            category: $category,
            price: $data['price'],
            description: $data['description'] ?? null,
            product: $product,
            note: $data['note'] ?? null,
            supplier: $data['supplier'] ?? null,
            bought_at: !empty($data['bought_at']) ? Carbon::parse($data['bought_at']) : null,
        );

        main()->log()->infoDB("Upravený výdavok: {$expense->log_string}", changes: $changes, resources: [$expense, $product, $category], additionalData: ['owner_only' => $category->owner_only]);

        wp_send_json_success(['message' => 'Výdavok bol úspešne upravený.', 'id' => $expense->id]);
    }

    /**
     * @throws RequestException
     * @throws ValidationFailedException
     * @throws \Exception
     */
    public function delete(ExpenseDeleteRequest $request): void
    {
        $data = $request->validated();

        $expense = Expense::find($data['id']);

        if (!$expense) {
            throw new RequestException("Výdavok nebol nájdený.", 404);
        }

        ExpenseService::delete($expense);

        main()->log()->infoDB("Vymazaný výdavok: {$expense->log_string}", resources: [$expense], additionalData: ['owner_only' => $category->owner_only]);

        wp_send_json_success(['message' => 'Výdavok bol úspešne vymazaný.']);
    }

    /**
     * @param int $id
     * @return ExpenseCategory
     * @throws RequestException
     */
    private function getCategory(int $id): ExpenseCategory
    {
        $category = ExpenseCategory::where("term_id", $id)->first();
        if (!$category) throw new RequestException("Kategória nebola nájdená.", 404);
        return $category;
    }

    /**
     * @throws RequestException
     */
    private function getProduct(?int $id): ?Product
    {
        if (!$id) return null;
        $product = Product::where("ID", $id)->first();
        if (!$product) throw new RequestException("Produkt nebol nájdený.", 404);
        return $product;
    }

    /**
     * Skrýva terms z taxonomie pre manažera, ktore maju owner_only na true
     *
     * @filter get_terms 10 2
     * @param $terms
     * @param $taxonomy
     * @return array
     */
    function exclude_owner_only_terms_form_manager($terms, $taxonomy): array
    {
        $taxonomy = $taxonomy[0] ?? null;
        $filtered_terms = [];

        $user = wp_get_current_user();
        $role = $user->roles[0] ?? null;

        if ($taxonomy !== ExpenseCategory::getTaxonomySlug() || $role !== Role::MANAGER->value)
            return $terms;

        // Filter out hidden terms
        foreach ($terms as $term) {
            // Get the ACF field value
            $hide_term = get_field('owner_only', $taxonomy . '_' . $term->term_id);

            // If the ACF field is not set to true, include the term in the filtered array
            if (!$hide_term) {
                $filtered_terms[] = $term;
            }
        }

        return $filtered_terms;
    }
}