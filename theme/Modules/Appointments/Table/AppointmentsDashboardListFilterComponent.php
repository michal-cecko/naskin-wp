<?php

namespace Theme\Modules\Appointments\Table;

use Illuminate\Support\Collection;
use Saurus\App\Enums\FilterOperator;
use Saurus\App\Enums\FilterType;
use Saurus\App\Modules\Templates\Filter\FilterComponent;
use Theme\Enum\AppointmentSource;
use Theme\Enum\AppointmentStatus;
use Theme\Enum\AppointmentType;
use Theme\PostTypes\Customer;
use Theme\PostTypes\Service;
use Theme\Users\Employee;

class AppointmentsDashboardListFilterComponent extends FilterComponent
{
    public function fields() : array {
        $fields = [];

        $fields['start_at'] = [
            'label' => __('Začiatok termínu', THEME_DOMAIN),
            'type' => FilterType::DATETIME_RANGE,
            'operator' => FilterOperator::RANGE,
        ];

        $fields['customer_id'] = [
            'label' => __('Zákazník', THEME_DOMAIN),
            'type' => FilterType::MULTISELECT,
            'datatype' => "int",
            'operator' => FilterOperator::IN,
            'show_options_filter' => true,
            'options' => $this->getSelectOptionsFromModels(Customer::all(),valueFn: fn($customer) => $customer->title),
        ];

        $fields['employee_id'] = [
            'label' => __('Pracovníčka', THEME_DOMAIN),
            'type' => FilterType::MULTISELECT,
            'datatype' => "int",
            'operator' => FilterOperator::IN,
            'show_options_filter' => true,
            'options' => $this->getSelectOptionsFromModels(Employee::all(), valueFn: fn($employee) => $employee->first_name),
        ];

        $fields["services{$this->relationSeparator}service_id"] = [
            'label' => __('Služby', THEME_DOMAIN),
            'type' => FilterType::MULTISELECT,
            'datatype' => "int",
            'operator' => FilterOperator::IN,
            'show_options_filter' => true,
            'options' => $this->getSelectOptionsFromModels(Service::all(), valueFn: fn ($service) => "{$service->title} ({$service->price}€)"),
        ];

        $fields["type"] = [
            'label' => __('Typ termínu', THEME_DOMAIN),
            'datatype' => "string",
            'type' => FilterType::SELECT,
            'operator' => FilterOperator::EQUAL,
            'options' => $this->getSelectOptionsFromEnum(AppointmentType::class),
        ];

        $fields["status"] = [
            'label' => __('Status', THEME_DOMAIN),
            'datatype' => "string",
            'type' => FilterType::SELECT,
            'operator' => FilterOperator::EQUAL,
            'options' => $this->getSelectOptionsFromEnum(AppointmentStatus::class),
        ];

        $fields["source"] = [
            'label' => __('Zdroj', THEME_DOMAIN),
            'datatype' => "string",
            'type' => FilterType::MULTISELECT,
            'operator' => FilterOperator::IN,
            'options' => $this->getSelectOptionsFromEnum(AppointmentSource::class),
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