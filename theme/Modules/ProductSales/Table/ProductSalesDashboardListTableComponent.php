<?php

namespace Theme\Modules\ProductSales\Table;

use Illuminate\Database\Eloquent\Builder;
use Saurus\App\Modules\Templates\Table\TableComponent;
use Theme\Enum\AppointmentType;
use Theme\Models\Appointment\Appointment;
use Theme\Models\Expense\Expense;
use Theme\Models\Product\ProductSale;

class ProductSalesDashboardListTableComponent extends TableComponent
{
    public string $view = 'parts.dashboard.product-sales.tables.dashboard-product-sales-list-table';

    public function columns(): array
    {
        return [
            'id' => [
                'label' => __('ID', THEME_DOMAIN),
            ],
            'product' => [
                'label' => __('Produkt', THEME_DOMAIN),
                'sortable' => true,
            ],
            'price' => [
                'label' => __('Jednotková cena', THEME_DOMAIN),
                'sortable' => true,
            ],
            'quantity' => [
                'label' => __('Množstvo', THEME_DOMAIN),
                'sortable' => true,
            ],
            'total_price' => [
                'label' => __('Celková cena', THEME_DOMAIN),
                'sortable' => true,
            ],
            'sold_at' => [
                'label' => __('Predané dňa', THEME_DOMAIN),
                'sortable' => true,
            ],
            'appointment' => [
                'label' => __('Z rezervácie', THEME_DOMAIN),
                'sortable' => true,
            ],
            'note' => [
                'label' => __('Poznámka', THEME_DOMAIN),
                'sortable' => true,
            ],
            'payment_type' => [
                'label' => __('Spôsob platby', THEME_DOMAIN),
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
        return ProductSale::with(["product", "appointment"])->orderBy("id", "DESC");
    }

    public function rowActions(mixed $rowData): array
    {
        $actions = [];

        if(current_user_can('edit_product_sales')) {
            $actions['edit'] = ['url' => admin_url("admin.php?page=product_sale_detail&id=" . ($rowData['id'] ?? 0)), 'label' => __('Upraviť', THEME_DOMAIN)];
        }

        return $actions;
    }

    public function rowData(mixed $rowData): array
    {
        $sale = $rowData;
        $rowDataToReturn = [];

        $rowDataToReturn['id'] = $sale->id;
        $rowDataToReturn['sold_at'] = $sale->sold_at?->format("d.m.y") ?? "<i>Nezadané</i>";
        $rowDataToReturn['product'] = self::anchor($sale->product->title, $sale->product->log_link);
        $rowDataToReturn['appointment'] = $sale->appointment ? self::anchor($sale->appointment->short_log_string, $sale->appointment->log_link) : "<i>Samostatný predaj</i>";
        $rowDataToReturn['price'] = $sale->price . " €";
        $rowDataToReturn['total_price'] = $sale->total_price . " €";
        $rowDataToReturn['quantity'] = $sale->quantity;
        $rowDataToReturn['note'] = $sale->note ?? "<i>Bez poznámky</i>";
        $rowDataToReturn['payment_type'] = $sale->paymentType?->value ?? "<i>Nezadané</i>";
        $rowDataToReturn['created_at'] = $sale->created_at->format("d.m.y H:i");
        $rowDataToReturn['updated_at'] = $sale->updated_at->format("d.m.y H:i");

        return ['id' => $sale->id, 'data' => $rowDataToReturn];
    }
}