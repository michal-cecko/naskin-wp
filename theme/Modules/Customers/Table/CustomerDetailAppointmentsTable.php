<?php

namespace Theme\Modules\Customers\Table;

use Saurus\App\Modules\Templates\Table\CustomTable;
use Theme\PostTypes\Customer;

class CustomerDetailAppointmentsTable extends CustomTable
{
    public string $tableView = 'parts.dashboard.customer.customer-appointments-table';

    public function __construct(protected $customer){}

    public function columns(): array
    {
        return [
            'id' => [
                'label' => __('ID', THEME_DOMAIN),
            ],
            'start_at' => [
                'label' => __('Start at', THEME_DOMAIN),
                'sortable' => true,
                'has_row_actions' => true,
            ],
            'end_at' => [
                'label' => __('End at', THEME_DOMAIN),
                'sortable' => true,
            ],
            'services' => [
                'label' => __('Services', THEME_DOMAIN),
                'sortable' => true,
            ],
            'total' => [
                'label' => __('Total', THEME_DOMAIN),
                'sortable' => true,
            ],
            'status' => [
                'label' => __('Status', THEME_DOMAIN),
                'sortable' => true,
            ],
            'source' => [
                'label' => __('Source', THEME_DOMAIN),
                'sortable' => true,
            ],
            'created_at' => [
                'label' => __('Created at', THEME_DOMAIN),
                'sortable' => true,
            ],
            'updated_at' => [
                'label' => __('Updated at', THEME_DOMAIN),
                'sortable' => true,
            ],
        ];
    }

    public function rows() : iterable {
        $finalRows = [];
        $rows = $this->customer->appointmentsInLatestOrder;
        foreach ($rows as $appointment) {
            $rowData = $this->rowData($appointment);
            $finalRows[$rowData['id']] = $rowData['data'];
        }
        return $finalRows;
    }

    public function rowActions(mixed $rowData): array
    {
        $actions = [];

        //TODO: add edit and delete actions
        $actions['edit'] = get_edit_post_link($rowData->id);
        $actions['delete'] = get_edit_post_link($rowData->id);

        return $actions;
    }

    public function rowData(mixed $rowData): array
    {
        $appointment = $rowData;
        $rowDataToReturn = [];

        $rowDataToReturn['id'] = $appointment->id;
        $rowDataToReturn['start_at'] = $appointment->start_at->format("d.m.y H:i");
        $rowDataToReturn['end_at'] = $appointment->end_at->format("d.m.y H:i");
        $rowDataToReturn['services'] = $this->formatServices($appointment->services);
        $rowDataToReturn['total'] = $appointment->total . " €";
        $rowDataToReturn['status'] = $appointment->status?->translated();
        $rowDataToReturn['source'] = $appointment->source?->translated();
        $rowDataToReturn['created_at'] = $appointment->created_at->format("d.m.y H:i");
        $rowDataToReturn['updated_at'] = $appointment->updated_at->format("d.m.y H:i");

        return ['id' => $appointment->id, 'data' => $rowDataToReturn];
    }

    private function formatServices(iterable $services): string
    {
        $formattedServices = [];
        foreach ($services as $service) {
            $formattedServices[] = $this->anchor($service->name, get_edit_post_link($service->service_id)) . " | " . $service->price . " €";
        }
        return implode("<br>", $formattedServices);
    }
}