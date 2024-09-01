<?php

namespace Theme\Modules\Products\Table\Sales;

use Saurus\App\Enums\FilterType;
use Saurus\App\Modules\Templates\Filter\FilterComponent;
use Theme\Models\Appointment\Appointment;

class ProductDetailSalesFilterComponent extends FilterComponent
{
    public function fields(): array
    {
        $fields = [];

        $fields['appointment_id'] = [
            'label' => __('Rezervácia', THEME_DOMAIN),
            'type' => FilterType::SELECT,
            'datatype' => "int",
            'show_options_filter' => true,
            'options' => Appointment::whereHas("productSales")->get()->map(fn($app) => ['key' => $app->id, 'value' => $app->short_log_string])->toArray(),
        ];

        $fields["price"] = [
            'label' => __('Jednotková cena (€)', THEME_DOMAIN),
            'datatype' => "float",
            'type' => FilterType::CURRENCY,
        ];

        $fields["quantity"] = [
            'label' => __('Množstvo', THEME_DOMAIN),
            'datatype' => "integer",
            'type' => FilterType::NUMBER,
        ];

        $fields['sold_at'] = [
            'label' => __('Predané dňa', THEME_DOMAIN),
            'type' => FilterType::DATETIME,
            'operator' => "="
        ];

        $fields["note"] = [
            'label' => __('Poznámka', THEME_DOMAIN),
            'datatype' => "string",
            'type' => FilterType::TEXT,
            'operator' => "LIKE",
            'value_prefix' => "%",
            'value_suffix' => "%",
        ];

        return $fields;
    }
}