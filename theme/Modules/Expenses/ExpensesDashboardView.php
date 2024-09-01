<?php

namespace Theme\Modules\Expenses;

use Saurus\App\Interfaces\IFilterComponent;
use Saurus\App\Main;
use Saurus\App\Modules\Templates\Card\MetricCard;
use Theme\Models\Expense\Expense;
use Theme\Modules\Expenses\Table\ExpensesDashboardListFilterComponent;
use Theme\Modules\Expenses\Table\ExpensesDashboardListTableComponent;
use Theme\PostTypes\Product;
use Theme\Taxonomies\ExpenseCategory;

class ExpensesDashboardView
{
    /**
     * Add appointment calendar + table views links to sidebar menu
     *
     * @action admin_menu
     * @return void
     */
    public function addExpensesToMenu(): void
    {
        add_menu_page(
            page_title: __('Výdavky', THEME_DOMAIN),
            menu_title: __('Výdavky', THEME_DOMAIN),
            capability: 'view_expenses',
            menu_slug: 'expenses',
            callback: [$this, 'renderExpensesListPage'],
            icon_url: 'dashicons-money-alt',
            position: 5
        );

        add_submenu_page(
            'expenses',
            __('Pridať/upraviť výdavok', THEME_DOMAIN),
            __('Pridať výdavok', THEME_DOMAIN),
            'view_expenses_detail_page',
            'expense_detail',
            [$this, 'renderExpensesCreatePage']
        );

        add_submenu_page(
            'expenses',
            __('Kategorie výdavkov', THEME_DOMAIN),
            __('Kategorie výdavkov', THEME_DOMAIN),
            'view_expense_categories',
            'edit-tags.php?taxonomy=' . ExpenseCategory::getTaxonomySlug(),
        );
    }

    public function renderExpensesListPage(): void
    {
        $expenseTableFilter = Main::initModule(new ExpensesDashboardListFilterComponent("f_1"));
        $expenseTable = Main::initModule(new ExpensesDashboardListTableComponent(id: "t", filter: $expenseTableFilter));
        $metrics = $this->metrics($expenseTableFilter);

        templates()->render("pages.dashboard.expenses.expenses-list", [
            'table' => $expenseTable,
            'metrics' => $metrics,
            'filter' => $expenseTableFilter
        ]);
    }

    public function renderExpensesCreatePage(): void
    {
        $hasID = $_GET['id'] ?? null;
        $resource = $hasID ? Expense::find($hasID) : null;

        $categories = ExpenseCategory::all()->map(fn($cat) => [
            'id' => $cat->term_id,
            'title' => $cat->term->name,
        ]);
        $products = Product::published()->get()->map(fn($prod) => [
            'id' => $prod->id,
            'title' => $prod->title . ' (' . $prod->price . ' €)',
        ]);

        templates()->render("pages.dashboard.expenses.expenses-single", compact('resource', 'categories', 'products'));
    }

    private function metrics(IFilterComponent $filter): array
    {
        $metrics = [];

        if (current_user_can("view_expenses")) {
            $metrics[] = Main::initModule(new MetricCard(
                id: "expense_count_metric",
                heading: "Počet výdavkov",
                query: Expense::query(),
                icon: 'dashicons-screenoptions',
                operator: "count",
                filter: $filter,
            ));
        }

        if (current_user_can("view_expense_money_statistics")) {
            $metrics[] = Main::initModule(new MetricCard(
                id: "expense_sum_metric",
                heading: "Výdaj celkom",
                query: Expense::query(),
                targetAttribute: "price",
                icon: 'dashicons-money-alt',
                operator: "sum",
                filter: $filter,
                formatter: function ($value) {
                    return $value . "€";
                }
            ));
        }

        return $metrics;
    }
}