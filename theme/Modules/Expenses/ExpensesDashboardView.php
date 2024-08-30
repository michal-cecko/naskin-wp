<?php

namespace Theme\Modules\Expenses;

use Saurus\App\Interfaces\IFilterComponent;
use Saurus\App\Main;
use Saurus\App\Modules\Templates\Card\MetricCard;
use Theme\Models\Appointment\Appointment;
use Theme\Models\Expense\Expense;
use Theme\Modules\Appointments\Table\AppointmentsDashboardListFilterComponent;
use Theme\Modules\Appointments\Table\AppointmentsDashboardListTableComponent;
use Theme\Modules\Customers\Table\CustomerDetailAppointmentsFilterComponent;
use Theme\Modules\Customers\Table\CustomerDetailAppointmentsTableComponent;
use Theme\Modules\Expenses\Table\ExpensesDashboardListFilterComponent;
use Theme\Modules\Expenses\Table\ExpensesDashboardListTableComponent;
use Theme\PostTypes\Service;
use Theme\Services\Appointments\AppointmentService;
use Theme\Taxonomies\ExpenseCategory;
use Theme\Taxonomies\ServiceCategory;
use Theme\Users\Employee;
use Theme\Users\User;

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

        templates()->render("pages.dashboard.appointments.appointments-list", [
            'table' => $expenseTable,
            'metrics' => $metrics,
            'filter' => $expenseTableFilter
        ]);
    }

    private function metrics(IFilterComponent $filter): array
    {
        $metrics = [];

        if(current_user_can("view_expenses")) {
            $metrics[] = Main::initModule(new MetricCard(
                id: "expense_count_metric",
                heading: "Počet výdavkov",
                query: Expense::query(),
                icon: 'dashicons-screenoptions',
                operator: "count",
                filter: $filter,
            ));
        }

        if(current_user_can("view_expense_money_statistics")) {
            $metrics[] = Main::initModule(new MetricCard(
                id: "expense_sum_metric",
                heading: "Výdaj celkom",
                query: Expense::query(),
                targetAttribute: "total",
                icon: 'dashicons-money-alt',
                operator: "sum",
                filter: $filter,
                formatter: function($value) {
                    return $value . " €";
                }
            ));
        }

        return $metrics;
    }
}