<?php

namespace Theme\Modules\ProductSales\Table;

use Illuminate\Support\Collection;
use Saurus\App\Enums\FilterOperator;
use Saurus\App\Enums\FilterType;
use Saurus\App\Modules\Templates\Filter\FilterComponent;
use Theme\Enum\AppointmentSource;
use Theme\Enum\AppointmentStatus;
use Theme\Enum\AppointmentType;
use Theme\Models\Appointment\Appointment;
use Theme\PostTypes\Customer;
use Theme\PostTypes\Product;
use Theme\PostTypes\Service;
use Theme\Taxonomies\ExpenseCategory;
use Theme\Taxonomies\ServiceCategory;
use Theme\Users\Employee;

class ProductSalesDashboardListFilterComponent extends FilterComponent
{
    public function fields() : array {
        $fields = [];

        $fields['product_id'] = [
            'label' => __('Produkt', THEME_DOMAIN),
            'type' => FilterType::MULTISELECT,
            'operator' => FilterOperator::IN,
            'datatype' => "int",
            'show_options_filter' => true,
            'options' => Product::published()->get()->map(fn($app) => ['key' => $app->id, 'value' => $app->title . " (" . $app->price . "€)"])->toArray(),
        ];

        $fields['appointment_id'] = [
            'label' => __('Rezervácia', THEME_DOMAIN),
            'type' => FilterType::MULTISELECT,
            'datatype' => "int",
            'operator' => FilterOperator::IN,
            'show_options_filter' => true,
            'options' => Appointment::whereHas("productSales")->get()->map(fn($app) => ['key' => $app->id, 'value' => $app->short_log_string])->toArray(),
        ];

        $fields["price"] = [
            'label' => __('Jednotková cena (€)', THEME_DOMAIN),
            'datatype' => "float",
            'operator' => FilterOperator::RANGE,
            'type' => FilterType::CURRENCY_RANGE,
        ];

        $fields["quantity"] = [
            'label' => __('Množstvo', THEME_DOMAIN),
            'datatype' => "integer",
            'operator' => FilterOperator::RANGE,
            'type' => FilterType::NUMBER_RANGE,
        ];

        $fields['sold_at'] = [
            'label' => __('Dátum predaja', THEME_DOMAIN),
            'type' => FilterType::DATETIME_RANGE,
            'operator' => FilterOperator::RANGE,
        ];

        $fields["note"] = [
            'label' => __('Poznámka', THEME_DOMAIN),
            'datatype' => "string",
            'type' => FilterType::TEXT,
            'operator' => FilterOperator::LIKE,
            'value_prefix' => "%",
            'value_suffix' => "%",
        ];

        return $fields;
    }
}