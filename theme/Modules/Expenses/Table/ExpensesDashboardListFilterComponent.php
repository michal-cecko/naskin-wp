<?php

namespace Theme\Modules\Expenses\Table;

use Illuminate\Support\Collection;
use Saurus\App\Enums\FilterType;
use Saurus\App\Modules\Templates\Filter\FilterComponent;
use Theme\Enum\AppointmentSource;
use Theme\Enum\AppointmentStatus;
use Theme\Enum\AppointmentType;
use Theme\PostTypes\Customer;
use Theme\PostTypes\Service;
use Theme\Taxonomies\ExpenseCategory;
use Theme\Taxonomies\ServiceCategory;
use Theme\Users\Employee;

class ExpensesDashboardListFilterComponent extends FilterComponent
{
    public function fields() : array {
        $fields = [];

        $fields["category_id"] = [
            'label' => __('Kategória', THEME_DOMAIN),
            'type' => FilterType::MULTISELECT,
            'datatype' => "int",
            'multiple' => true,
            'show_options_filter' => true,
            'options' => $this->getSelectOptionsFromModels(ExpenseCategory::with("term")->get(), keyFn: fn ($cat) => $cat->term_id, valueFn: fn ($cat) => $cat->term->name),
        ];

        $fields["description"] = [
            'label' => __('Popis', THEME_DOMAIN),
            'datatype' => "string",
            'type' => FilterType::TEXT,
            'operator' => "LIKE",
            'value_prefix' => "%",
            'value_suffix' => "%",
        ];

        $fields["supplier"] = [
            'label' => __('Dodávateľ', THEME_DOMAIN),
            'datatype' => "string",
            'type' => FilterType::TEXT,
            'operator' => "LIKE",
            'value_prefix' => "%",
            'value_suffix' => "%",
        ];

        $fields["price"] = [
            'label' => __('Suma', THEME_DOMAIN),
            'datatype' => "float",
            'type' => FilterType::CURRENCY,
        ];

        return $fields;
    }
}