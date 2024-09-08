<?php

namespace Theme\Modules\ProductSales;

use Carbon\Carbon;
use Saurus\App\Interfaces\IFilterComponent;
use Saurus\App\Main;
use Saurus\App\Modules\Templates\Card\MetricCard;
use Theme\Enum\AppointmentStatus;
use Theme\Enum\AppointmentType;
use Theme\Enum\ProductSale\ProductSalePaymentType;
use Theme\Models\Appointment\Appointment;
use Theme\Models\Product\ProductSale;
use Theme\Modules\ProductSales\Table\ProductSalesDashboardListFilterComponent;
use Theme\Modules\ProductSales\Table\ProductSalesDashboardListTableComponent;
use Theme\PostTypes\Product;

class ProductSalesDashboardView
{
    /**
     * Add appointment calendar + table views links to sidebar menu
     *
     * @action admin_menu
     * @return void
     */
    public function addProductSalesToMenu(): void
    {
        add_submenu_page(
            'edit.php?post_type=' . Product::getPostTypeSlug(),
            __('Predaje', THEME_DOMAIN),
            __('Predaje', THEME_DOMAIN),
            'view_product_sales',
            'product_sales',
            [$this, 'renderProductSalesListPage']
        );

        add_submenu_page(
            'edit.php?post_type=' . Product::getPostTypeSlug(),
            __('Pridať/upraviť predaj', THEME_DOMAIN),
            __('Pridať predaj', THEME_DOMAIN),
            'view_product_sale_detail_page',
            'product_sale_detail',
            [$this, 'renderProductSalesCreatePage']
        );
    }

    public function renderProductSalesListPage(): void
    {
        $tableFilter = Main::initModule(new ProductSalesDashboardListFilterComponent("f_1"));
        $table = Main::initModule(new ProductSalesDashboardListTableComponent(id: "t", filter: $tableFilter));
        $metrics = $this->metrics($tableFilter);

        templates()->render("pages.dashboard.product-sales.product-sales-list", [
            'table' => $table,
            'metrics' => $metrics,
            'filter' => $tableFilter
        ]);
    }

    public function renderProductSalesCreatePage(): void
    {
        $hasID = $_GET['id'] ?? null;
        $resource = $hasID ? ProductSale::find($hasID) : null;

        $products = Product::published()->get()->map(fn($prod) => [
            'id' => $prod->id,
            'price' => $prod->price,
            'title' => $prod->title . ' (' . $prod->price . ' €)',
        ]);

        $paymentTypes = collect(ProductSalePaymentType::translatedCases())->map(fn($type, $key) => [
            'id' => $key,
            'title' => $type,
        ])->values();

        $reservations = Appointment::where("type", AppointmentType::RESERVATION)->where("status", AppointmentStatus::OK)
            ->where("start_at", "<", Carbon::now()->addDay())->orderBy("id", "DESC")->get()->map(fn($app) => [
                'id' => $app->id,
                'title' => $app->short_log_string,
            ]);

        templates()->render("pages.dashboard.product-sales.product-sales-single", compact('resource', 'reservations', 'products', 'paymentTypes'));
    }

    private function metrics(IFilterComponent $filter): array
    {
        $metrics = [];

        if(current_user_can("view_product_sales")) {
            $metrics[] = Main::initModule(new MetricCard(
                id: "product_sale_count_metric",
                heading: "Počet predajov",
                query: ProductSale::query(),
                icon: 'dashicons-chart-bar',
                operator: "count",
                filter: $filter,
            ));
        }

        if(current_user_can("view_product_sale_money_statistics")) {
            $metrics[] = Main::initModule(new MetricCard(
                id: "product_sale_quantity_metric",
                heading: "Predaných kusov",
                query: ProductSale::query(),
                targetAttribute: "quantity",
                icon: 'dashicons-screenoptions',
                operator: "sum",
                filter: $filter,
            ));
        }

        if(current_user_can("view_product_sale_money_statistics")) {
            $metrics[] = Main::initModule(new MetricCard(
                id: "product_sale_sum_metric",
                heading: "Predaj celkom",
                query: ProductSale::query(),
                targetAttribute: "total_price",
                icon: 'dashicons-money',
                operator: "sum",
                filter: $filter,
                formatter: function($value) {
                    return $value . "€";
                }
            ));
        }

        return $metrics;
    }
}