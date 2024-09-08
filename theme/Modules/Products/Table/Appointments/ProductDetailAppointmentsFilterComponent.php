<?php

namespace Theme\Modules\Products\Table\Appointments;

use Saurus\App\Enums\FilterOperator;
use Saurus\App\Enums\FilterType;
use Saurus\App\Modules\Templates\Filter\FilterComponent;
use Theme\Enum\AppointmentSource;
use Theme\Enum\AppointmentStatus;
use Theme\PostTypes\Service;
use Theme\Users\Employee;

class ProductDetailAppointmentsFilterComponent extends FilterComponent
{
    public function fields() : array {
        $fields = [];

        $fields['start_at'] = [
            'label' => __('Začiatok termínu', THEME_DOMAIN),
            'type' => FilterType::DATETIME_RANGE,
            'operator' => FilterOperator::RANGE
        ];

        $fields['employee_id'] = [
            'label' => __('Pracovníčka', THEME_DOMAIN),
            'type' => FilterType::MULTISELECT,
            'datatype' => "int",
            'operator' => FilterOperator::IN,
            'show_options_filter' => true,
            'options' => Employee::all()->map(fn($employee) => ['key' => $employee->id, 'value' => $employee->first_name])->toArray(),
        ];

        $fields["services{$this->relationSeparator}service_id"] = [
            'label' => __('Služby', THEME_DOMAIN),
            'type' => FilterType::MULTISELECT,
            'datatype' => "int",
            'operator' => FilterOperator::IN,
            'show_options_filter' => true,
            'options' => Service::all()->map(fn($service) => ['key' => $service->id, 'value' => "{$service->title} ({$service->price}€)"])->toArray(),
        ];

        $fields["status"] = [
            'label' => __('Status', THEME_DOMAIN),
            'datatype' => "string",
            'type' => FilterType::SELECT,
            'operator' => FilterOperator::EQUAL,
            'options' => collect(AppointmentStatus::translatedCases())->map(fn($value, $key) => ['key' => $key, 'value' => $value])->values()->toArray(),
        ];

        $fields["source"] = [
            'label' => __('Zdroj', THEME_DOMAIN),
            'datatype' => "string",
            'type' => FilterType::MULTISELECT,
            'operator' => FilterOperator::IN,
            'options' => collect(AppointmentSource::translatedCases())->map(fn($value, $key) => ['key' => $key, 'value' => $value])->values()->toArray(),
        ];

        return $fields;
    }
}