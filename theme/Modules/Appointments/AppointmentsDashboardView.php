<?php

namespace Theme\Modules\Appointments;

use Theme\PostTypes\Service;
use Theme\Taxonomies\ServiceCategory;
use Theme\Users\Employee;
use Theme\Users\User;

class AppointmentsDashboardView
{
    public ?User $currentUser;

    public function __construct()
    {
        $this->currentUser = $this->getCurrentUser();
    }

    /**
     * Redirects all employees to appointments
     *
     * @action admin_init
     *
     * @return void
     */
    public function redirect_to_appointments(): void
    {
        if( in_array($this->currentUser?->role, ['together-employee', 'employee']) ) {
            global $pagenow;
            if ( $pagenow === 'index.php' ) {
                wp_redirect( admin_url( 'admin.php?page=appointments' ) );
                exit();
            }
        }
    }

    /**
     * Add custom dashboard page link to sidebar menu
     *
     * @action admin_menu
     * @return void
     */
    public function add_appointments_to_menu(): void
    {
        add_menu_page(
            page_title: __('Termíny', THEME_DOMAIN),
            menu_title: __('Termíny', THEME_DOMAIN),
            capability: 'read',
            menu_slug: 'appointments',
            callback: [$this, 'render_appointments_table'],
            icon_url: 'dashicons-calendar-alt',
            position: 2
        );
    }

    public function render_appointments_table(): void
    {
        templates()->render("parts.dashboard.appointments.calendar", $this->getAppointmentViewData());
    }

    private function getAppointmentViewData(): array
    {
        $data = [];

        $data['services'] = $this->getServices();
        $data['employees'] = $this->getEmployees();
        $data['currentUser'] = $this->currentUser;

        return $data;
    }

    private function getCurrentUser() : ?User {
        return User::where("ID", get_current_user_id())->first();
    }

    private function getServices(): array
    {
        $servicesArr = Service::with("taxonomies.term")->get();

        $serviceCategories = [];
        $colorsArray = [];
        $durationsArray = [];

        foreach ($servicesArr as $service) {
            $cat = $service->service_category;
            $catID = $cat?->term_id ?? "uncategorized";
            if(!isset($serviceCategories[$catID])) $serviceCategories[$catID] = [
                'name' => $cat?->term?->name ?? "Bez kategórie",
                'services' => []
            ];
            $services[$service->id] = [
                'id' => $service->id,
                'title' => $service->title,
                'duration' => $service->duration,
                'price' => $service->price,
                'category_id' => $catID
            ];
            $serviceCategories[$catID]['services'][$service->id] = $services[$service->id];
            $colorsArray[$catID] = $cat?->color ?? "#000000";
            $durationsArray[$service->id] = $service->duration;
        }

        return [
            'services' => $services,
            'service_categories' => $serviceCategories,
            'colors' => $colorsArray,
            'durations' => $durationsArray
        ];
    }

    private function getEmployees(): iterable
    {
        if( $this->currentUser?->role !== 'employee') {
            $arr = Employee::all();
        } else {
            $arr = [Employee::where($this->currentUser?->id)->first()];
        }

        $employeesFinal = collect([]);

        foreach ($arr as $employee) {
            $employeesFinal->put($employee->ID, [
                'id' => $employee->ID,
                'name' => $employee->first_name,
                'profileImage' => $employee->profile_picture,
                'allowed_services' => $employee->allowed_service_ids
            ]);
        }

        return $employeesFinal;
    }
}