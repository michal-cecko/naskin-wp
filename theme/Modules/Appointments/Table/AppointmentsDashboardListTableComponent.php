<?php

namespace Theme\Modules\Appointments\Table;

use Illuminate\Database\Eloquent\Builder;
use Saurus\App\Modules\Templates\Table\TableComponent;
use Theme\Enum\AppointmentStatus;
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

    public function recordsQuery() : Builder
    {
        return Appointment::with(["services", "employee", "customer", "payments", "productSales.product"])->orderBy("id", "DESC");
    }

    public function hasDetailRow(): bool
    {
        return true;
    }

    public function rowActions(mixed $rowData): array
    {
        $actions = [];

        $actions['edit'] = ['url' => $rowData['appointment']?->edit_link ?? null, 'label' => __('Detail', THEME_DOMAIN)];

        return $actions;
    }

    public function rowData(mixed $rowData): array
    {
        $appointment = $rowData;
        $rowDataToReturn = [];

        $rowDataToReturn['appointment'] = $appointment;
        $rowDataToReturn['id'] = $appointment->id;
        $rowDataToReturn['customer'] = $appointment->type === AppointmentType::VACATION ? "<i>---</i>" : self::anchor($appointment->customer?->title, $appointment->customer?->log_link);
        $rowDataToReturn['employee'] = self::anchor($appointment->employee->first_name, $appointment->employee->log_link);
        $rowDataToReturn['type'] = $appointment->type?->translated();
        $rowDataToReturn['start_at'] = $appointment->start_at->format("d.m.y H:i");
        $rowDataToReturn['end_at'] = $appointment->end_at->format("d.m.y H:i");
        $rowDataToReturn['services'] = $appointment->type === AppointmentType::VACATION ? "<i>---</i>" : $appointment->formatted_services;
        $rowDataToReturn['total'] = $appointment->type === AppointmentType::VACATION || $appointment->status === AppointmentStatus::CANCELLED ? "<i>---</i>" : $appointment->formatted_total;
        $rowDataToReturn['note'] = !empty($appointment->note) ? $appointment->note : "<i>Bez poznámky</i>";
        $rowDataToReturn['status'] = $appointment->status?->translated();
        $rowDataToReturn['source'] = $appointment->type === AppointmentType::VACATION ? "<i>---</i>" : $appointment->source?->translated();
        $rowDataToReturn['created_at'] = $appointment->created_at->format("d.m.y H:i");
        $rowDataToReturn['updated_at'] = $appointment->updated_at->format("d.m.y H:i");
        $rowDataToReturn['detail_row'] = $appointment->type === AppointmentType::VACATION ? false : $this->detailRowContent($appointment);

        return ['id' => $appointment->id, 'data' => $rowDataToReturn];
    }

    private function detailRowContent(mixed $appointment) : string
    {
        return templates()->generate('parts.dashboard.appointments.tables.dashboard-appointments-list-table-detail-row', [
            'appointment' => $appointment,
        ]);
    }
}