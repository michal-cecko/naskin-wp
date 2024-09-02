<?php

namespace Theme\Modules\Appointments\Table;

use Illuminate\Database\Eloquent\Builder;
use Saurus\App\Modules\Templates\Table\TableComponent;
use Theme\Enum\AppointmentType;
use Theme\Models\Appointment\Appointment;

class AppointmentsDashboardListTableComponent extends TableComponent
{
    public string $view = 'parts.dashboard.appointments.tables.dashboard-appointments-list-table';

    public function columns(): array
    {
        return [
            'id' => [
                'label' => __('ID', THEME_DOMAIN),
            ],
            'type' => [
                'label' => __('Typ termínu', THEME_DOMAIN),
                'sortable' => true,
            ],
            'customer' => [
                'label' => __('Zákazník', THEME_DOMAIN),
                'sortable' => true,
            ],
            'employee' => [
                'label' => __('Pracovník', THEME_DOMAIN),
                'sortable' => true,
            ],
            'start_at' => [
                'label' => __('Začiatok', THEME_DOMAIN),
                'sortable' => true,
            ],
            'end_at' => [
                'label' => __('Koniec', THEME_DOMAIN),
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

    public function recordsQuery() : Builder
    {
        return Appointment::with(["services", "employee", "customer"])->orderBy("id", "DESC");
    }

    public function rowActions(mixed $rowData): array
    {
        $actions = [];

        //TODO: add edit and delete actions
        //$actions['edit'] = get_edit_post_link($rowData->id);
        //$actions['delete'] = get_edit_post_link($rowData->id);

        return $actions;
    }

    public function rowData(mixed $rowData): array
    {
        $appointment = $rowData;
        $rowDataToReturn = [];

        $rowDataToReturn['id'] = $appointment->id;
        $rowDataToReturn['customer'] = $appointment->type === AppointmentType::VACATION ? "<i>---</i>" : self::anchor($appointment->customer?->title, $appointment->customer?->log_link);
        $rowDataToReturn['employee'] = self::anchor($appointment->employee->first_name, $appointment->employee->log_link);
        $rowDataToReturn['type'] = $appointment->type?->translated();
        $rowDataToReturn['start_at'] = $appointment->start_at->format("d.m.y H:i");
        $rowDataToReturn['end_at'] = $appointment->end_at->format("d.m.y H:i");
        $rowDataToReturn['services'] = $appointment->type === AppointmentType::VACATION ? "<i>---</i>" : $appointment->formatted_services;
        $rowDataToReturn['total'] = $appointment->total . " €";
        $rowDataToReturn['status'] = $appointment->status?->translated();
        $rowDataToReturn['source'] = $appointment->type === AppointmentType::VACATION ? "<i>---</i>" : $appointment->source?->translated();
        $rowDataToReturn['created_at'] = $appointment->created_at->format("d.m.y H:i");
        $rowDataToReturn['updated_at'] = $appointment->updated_at->format("d.m.y H:i");

        return ['id' => $appointment->id, 'data' => $rowDataToReturn];
    }
}