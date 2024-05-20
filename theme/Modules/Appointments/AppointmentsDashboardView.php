<?php

namespace Theme\Modules\Appointments;

use Theme\PostTypes\Service;
use Theme\Users\Employee;
use Theme\Users\User;

class AppointmentsDashboardView
{
    public User $currentUser;

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
                wp_redirect( admin_url( 'edit.php?post_type=appointment' ) );
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
            capability: 'administrator',
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
        return User::find(get_current_user_id());
    }

    private function getServices(): array
    {
        $services = Service::all();

        $servicesArray = [];
        $colorsArray = [];
        $durationsArray = [];

        foreach ($services as $service) {
            $servicesArray[$service->id] = $service->name;
            $colorsArray[$service->id] = $service->color;
            $durationsArray[$service->id] = $service->duration;
        }

        return [
            'services' => $servicesArray,
            'colors' => $colorsArray,
            'durations' => $durationsArray
        ];
    }

    private function getEmployees(): iterable
    {
        if( $this->currentUser?->role !== 'employee') {
            $arr = Employee::all();
        } else {
            $arr = [Employee::find($this->currentUser?->id)];
        }

        $employeesFinal = collect([]);

        foreach ($arr as $employee) {
            $employeesFinal->put($employee->ID, [
                'id' => $employee->ID,
                'name' => $employee->first_name,
                'profileImage' => $employee->profile_picture
            ]);
        }

        return $employeesFinal;
    }
}