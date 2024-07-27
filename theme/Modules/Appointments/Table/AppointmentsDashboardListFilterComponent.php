<?php

namespace Theme\Modules\Appointments\Table;

use Illuminate\Support\Collection;
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
            'label' => __('Začiatok termínu od', THEME_DOMAIN),
            'type' => FilterType::DATETIME,
            'operator' => ">="
        ];

        $fields['end_at'] = [
            'label' => __('Koniec termínu do', THEME_DOMAIN),
            'type' => FilterType::DATETIME,
            'operator' => "<="
        ];

        $fields['customer_id'] = [
            'label' => __('Zákazník', THEME_DOMAIN),
            'type' => FilterType::SELECT,
            'datatype' => "int",
            'show_options_filter' => true,
            'options' => $this->getSelectOptionsFromModels(Customer::all(),valueFn: fn($customer) => $customer->title),
        ];

        $fields['employee_id'] = [
            'label' => __('Pracovníčka', THEME_DOMAIN),
            'type' => FilterType::SELECT,
            'datatype' => "int",
            'show_options_filter' => true,
            'options' => $this->getSelectOptionsFromModels(Employee::all(), valueFn: fn($employee) => $employee->first_name),
        ];

        $fields["services{$this->relationSeparator}service_id"] = [
            'label' => __('Služby', THEME_DOMAIN),
            'type' => FilterType::MULTISELECT,
            'datatype' => "int",
            'multiple' => true,
            'show_options_filter' => true,
            'options' => $this->getSelectOptionsFromModels(Service::all(), valueFn: fn ($service) => "{$service->title} ({$service->price}€)"),
        ];

        $fields["type"] = [
            'label' => __('Typ termínu', THEME_DOMAIN),
            'datatype' => "string",
            'type' => FilterType::SELECT,
            'options' => $this->getSelectOptionsFromEnum(AppointmentType::class),
        ];

        $fields["status"] = [
            'label' => __('Status', THEME_DOMAIN),
            'datatype' => "string",
            'type' => FilterType::SELECT,
            'options' => $this->getSelectOptionsFromEnum(AppointmentStatus::class),
        ];

        $fields["source"] = [
            'label' => __('Zdroj', THEME_DOMAIN),
            'datatype' => "string",
            'type' => FilterType::SELECT,
            'options' => $this->getSelectOptionsFromEnum(AppointmentSource::class),
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