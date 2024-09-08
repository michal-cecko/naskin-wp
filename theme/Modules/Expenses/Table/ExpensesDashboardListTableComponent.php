<?php

namespace Theme\Modules\Expenses\Table;

use Illuminate\Database\Eloquent\Builder;
use Saurus\App\Modules\Templates\Table\TableComponent;
use Theme\Enum\AppointmentType;
use Theme\Models\Appointment\Appointment;
use Theme\Models\Expense\Expense;

class ExpensesDashboardListTableComponent extends TableComponent
{
    public string $view = 'parts.dashboard.expenses.tables.dashboard-expenses-list-table';

    public function columns(): array
    {
        return [
            'id' => [
                'label' => __('ID', THEME_DOMAIN),
            ],
            'description' => [
                'label' => __('Popis', THEME_DOMAIN),
                'sortable' => true,
            ],
            'category' => [
                'label' => __('Kategória', THEME_DOMAIN),
                'sortable' => true,
            ],
            'price' => [
                'label' => __('Suma', THEME_DOMAIN),
                'sortable' => true,
            ],
            'product' => [
                'label' => __('Produkt', THEME_DOMAIN),
                'sortable' => true,
            ],
            'supplier' => [
                'label' => __('Dodávateľ', THEME_DOMAIN),
                'sortable' => true,
            ],
            'note' => [
                'label' => __('Poznámka', THEME_DOMAIN),
                'sortable' => true,
            ],
            'bought_at' => [
                'label' => __('Z dňa', THEME_DOMAIN),
                'sortable' => true,
            ],
            'created_at' => [
                'label' => __('Vytvorené', THEME_DOMAIN),
                'sortable' => true,
            ],
            'updated_at' => [
                'label' => __('Posledná zmena', THEME_DOMAIN),
                'sortable' => true,
            ],
        ];
    }

    public function recordsQuery(): Builder
    {
        return Expense::with(["category.term", "product"])->orderBy("id", "DESC");
    }

    public function rowActions(mixed $rowData): array
    {
        $actions = [];

        if(current_user_can('edit_expenses')) {
            $actions['edit'] = ['url' => admin_url("admin.php?page=expense_detail&id=" . ($rowData['id'] ?? 0)), 'label' => __('Upraviť', THEME_DOMAIN)];
        }

        return $actions;
    }

    public function rowData(mixed $rowData): array
    {
        $expense = $rowData;
        $rowDataToReturn = [];

        $rowDataToReturn['id'] = $expense->id;
        $rowDataToReturn['category'] = $expense->category ? self::anchor($expense->category->term->name, $expense->category->edit_link) : "<i>Nenájdená</i>";
        $rowDataToReturn['product'] = $expense->product ? self::anchor($expense->product->title, $expense->product->edit_link) : "<i>Nepriradené</i>";
        $rowDataToReturn['description'] = $expense->description;
        $rowDataToReturn['supplier'] = $expense->supplier ?? "<i>Nezadané</i>";
        $rowDataToReturn['price'] = $expense->price . " €";
        $rowDataToReturn['note'] = $expense->note ?? "<i>Bez poznámky</i>";
        $rowDataToReturn['bought_at'] = $expense->bought_at?->format("d.m.y") ?? "<i>Nezadané</i>";
        $rowDataToReturn['created_at'] = $expense->created_at->format("d.m.y H:i");
        $rowDataToReturn['updated_at'] = $expense->updated_at->format("d.m.y H:i");

        return ['id' => $expense->id, 'data' => $rowDataToReturn];
    }
}