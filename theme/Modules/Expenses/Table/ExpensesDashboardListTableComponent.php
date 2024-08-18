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
            'category' => [
                'label' => __('Kategória', THEME_DOMAIN),
                'sortable' => true,
            ],
            'description' => [
                'label' => __('Popis', THEME_DOMAIN),
                'sortable' => true,
            ],
            'price' => [
                'label' => __('Suma', THEME_DOMAIN),
                'sortable' => true,
            ],
            'note' => [
                'label' => __('Poznámka', THEME_DOMAIN),
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
        return Expense::with(["category.term"])->orderBy("id", "DESC");
    }

    public function rowActions(mixed $rowData): array
    {
        $actions = [];

        //TODO: add edit and delete actions
        $actions['edit'] = get_edit_post_link($rowData->id);
        $actions['delete'] = get_edit_post_link($rowData->id);

        return $actions;
    }

    public function rowData(mixed $rowData): array
    {
        $expense = $rowData;
        $rowDataToReturn = [];

        $rowDataToReturn['id'] = $expense->id;
        $rowDataToReturn['category'] = self::anchor($expense->category->term->name, $expense->category->edit_link);
        $rowDataToReturn['description'] = $expense->description;
        $rowDataToReturn['supplier'] = $expense->supplier;
        $rowDataToReturn['price'] = $expense->price . " €";
        $rowDataToReturn['note'] = $expense->note;
        $rowDataToReturn['created_at'] = $expense->created_at->format("d.m.y H:i");
        $rowDataToReturn['updated_at'] = $expense->updated_at->format("d.m.y H:i");

        return ['id' => $expense->id, 'data' => $rowDataToReturn];
    }
}