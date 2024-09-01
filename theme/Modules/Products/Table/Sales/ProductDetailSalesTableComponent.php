<?php

namespace Theme\Modules\Products\Table\Sales;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Saurus\App\Interfaces\IFilterComponent;
use Saurus\App\Modules\Templates\Table\TableComponent;
use Theme\PostTypes\Customer;
use Theme\PostTypes\Product;

class ProductDetailSalesTableComponent extends TableComponent
{
    public string $view = 'parts.dashboard.products.tables.product-sales-table';

    public function __construct(protected Product $product, string $id, ?IFilterComponent $filter = null){
        parent::__construct(id: $id, filter: $filter);
    }

    public function recordsQuery() : HasMany
    {
        return $this->product->productSales();
    }

    public function columns(): array
    {
        return [
            'id' => [
                'label' => __('ID', THEME_DOMAIN),
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

    public function rowActions(mixed $rowData): array
    {
        $actions = [];

        /*//TODO: add edit and delete actions
        $actions['edit'] = get_edit_post_link($rowData->id);
        $actions['delete'] = get_edit_post_link($rowData->id);*/

        return $actions;
    }

    public function rowData(mixed $rowData): array
    {
        $sale = $rowData;
        $rowDataToReturn = [];

        $rowDataToReturn['id'] = $sale->id;
        $rowDataToReturn['sold_at'] = $sale->sold_at?->format("d.m.y") ?? "<i>Nezadané</i>";
        $rowDataToReturn['appointment'] = $sale->appointment ? self::anchor($sale->appointment->short_log_string, $sale->appointment->log_link) : "<i>Samostatný predaj</i>";
        $rowDataToReturn['price'] = $sale->price . " €";
        $rowDataToReturn['total_price'] = $sale->total_price . " €";
        $rowDataToReturn['quantity'] = $sale->quantity;
        $rowDataToReturn['note'] = $sale->note ?? "<i>Bez poznámky</i>";
        $rowDataToReturn['created_at'] = $sale->created_at->format("d.m.y H:i");
        $rowDataToReturn['updated_at'] = $sale->updated_at->format("d.m.y H:i");

        return ['id' => $sale->id, 'data' => $rowDataToReturn];
    }
}