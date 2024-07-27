<?php

namespace Theme\Modules\Customers\Table;

use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Saurus\App\Interfaces\IFilterComponent;
use Saurus\App\Modules\Templates\Table\TableComponent;
use Theme\PostTypes\Customer;

class CustomerDetailAppointmentsTableComponent extends TableComponent
{
    public string $pageView = 'parts.dashboard.appointments.tables.customer-appointments-table';

    public function __construct(protected $customer, string $id, ?IFilterComponent $filter = null){
        parent::__construct(id: $id, filter: $filter);
    }

    public function recordsQuery() {
        return $this->customer->latestAppointments();
    }

    public function columns(): array
    {
        return [
            'id' => [
                'label' => __('ID', THEME_DOMAIN),
            ],
            'start_at' => [
                'label' => __('Začiatok', THEME_DOMAIN),
                'sortable' => true,
                'has_row_actions' => true,
            ],
            'end_at' => [
                'label' => __('Koniec', THEME_DOMAIN),
                'sortable' => true,
            ],
            'employee' => [
                'label' => __('Pracovník', THEME_DOMAIN),
                'sortable' => true,
            ],
            'services' => [
                'label' => __('Služby', THEME_DOMAIN),
                'sortable' => true,
            ],
            'total' => [
                'label' => __('Celkom (€)', THEME_DOMAIN),
                'sortable' => true,
            ],
            'status' => [
                'label' => __('Status', THEME_DOMAIN),
                'sortable' => true,
            ],
            'source' => [
                'label' => __('Zdroj', THEME_DOMAIN),
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
        $appointment = $rowData;
        $rowDataToReturn = [];

        $rowDataToReturn['id'] = $appointment->id;
        $rowDataToReturn['start_at'] = $appointment->start_at->format("d.m.y H:i");
        $rowDataToReturn['end_at'] = $appointment->end_at->format("d.m.y H:i");
        $rowDataToReturn['employee'] = self::anchor($appointment->employee->first_name, $appointment->employee->log_link);
        $rowDataToReturn['services'] = $appointment->formatted_services;
        $rowDataToReturn['total'] = $appointment->total . " €";
        $rowDataToReturn['status'] = $appointment->status?->translated();
        $rowDataToReturn['source'] = $appointment->source?->translated();
        $rowDataToReturn['created_at'] = $appointment->created_at->format("d.m.y H:i");
        $rowDataToReturn['updated_at'] = $appointment->updated_at->format("d.m.y H:i");

        return ['id' => $appointment->id, 'data' => $rowDataToReturn];
    }
}