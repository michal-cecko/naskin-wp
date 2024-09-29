<?php

namespace Theme\Modules\Appointments;

use Saurus\App\Interfaces\IFilterComponent;
use Saurus\App\Main;
use Saurus\App\Modules\Templates\Card\MetricCard;
use Theme\Enum\AppointmentStatus;
use Theme\Enum\AppointmentType;
use Theme\Enum\User\Role;
use Theme\Models\Appointment\Appointment;
use Theme\Modules\Appointments\Table\AppointmentsDashboardListFilterComponent;
use Theme\Modules\Appointments\Table\AppointmentsDashboardListTableComponent;
use Theme\Modules\Expenses\Table\ExpensesDashboardListFilterComponent;
use Theme\Modules\Expenses\Table\ExpensesDashboardListTableComponent;
use Theme\PostTypes\Product;
use Theme\PostTypes\Service;
use Theme\Services\Appointments\AppointmentService;
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
        if ($this->currentUser?->role !== Role::ADMIN) {
            global $pagenow;
            if ($pagenow === 'index.php') {
                wp_redirect(admin_url('admin.php?page=appointments'));
                exit();
            }
        }
    }

    /**
     * Add appointment calendar + table views links to sidebar menu
     *
     * @action admin_menu
     * @return void
     */
    public function add_appointments_to_menu(): void
    {
        add_menu_page(
            page_title: __('Kalendár termínov', THEME_DOMAIN),
            menu_title: __('Kalendár termínov', THEME_DOMAIN),
            capability: 'view_appointments_calendar',
            menu_slug: 'appointments',
            callback: [$this, 'renderAppointmentsCalendar'],
            icon_url: 'dashicons-calendar-alt',
            position: 2
        );

        add_submenu_page(
            null,
            __('Termín', THEME_DOMAIN),
            null,
            'view_appointments_calendar',
            'appointment_detail',
            [$this, 'renderAppointmentDetailPage']
        );

        add_submenu_page(
            parent_slug: 'appointments',
            page_title: __('Zoznam termínov', THEME_DOMAIN),
            menu_title: __('Zoznam termínov', THEME_DOMAIN),
            capability: 'view_appointments_list',
            menu_slug: 'appointments-list',
            callback: [$this, 'renderAppointmentsListPage']
        );
    }


    public function renderAppointmentsCalendar(): void
    {
        templates()->render("pages.dashboard.appointments.appointments-calendar", $this->getAppointmentViewData());
    }

    public function renderAppointmentsListPage(): void
    {
        $appointmentsTableFilter = Main::initModule(new AppointmentsDashboardListFilterComponent("f_1"));
        $appointmentsTable = Main::initModule(new AppointmentsDashboardListTableComponent(id: "t", filter: $appointmentsTableFilter));
        $metrics = $this->metrics($appointmentsTableFilter);

        templates()->render("pages.dashboard.appointments.appointments-list", [
            'table' => $appointmentsTable,
            'metrics' => $metrics,
            'filter' => $appointmentsTableFilter
        ]);
    }

    public function renderAppointmentDetailPage(): void
    {
        $hasID = $_GET['id'] ?? null;
        $appointment = $hasID ? Appointment::with(["services", "employee", "customer", "payments", "productSales.product"])->find($hasID) : null;

        if(!$appointment) {
            main()->wpHelper()->throw404AdminError();
        }

        templates()->render("pages.dashboard.appointments.appointment-single", compact('appointment'));
    }

    private function getAppointmentViewData(): array
    {
        $data = [];

        $data['products'] = $this->getProducts();
        $data['services'] = $this->getServices();
        $data['employees'] = $this->getEmployees();
        $data['currentUser'] = $this->currentUser;
        $data['breaks'] = AppointmentService::getBreaks();

        return $data;
    }

    private function getCurrentUser(): ?User
    {
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
            if (!isset($serviceCategories[$catID])) $serviceCategories[$catID] = [
                'name' => $cat?->term?->name ?? "Bez kategórie",
                'static_break' => $cat?->static_break,
                'services' => []
            ];
            $services[$service->id] = [
                'id' => $service->id,
                'title' => $service->title,
                'duration' => $service->duration,
                'price' => $service->price,
                'category_id' => $catID,
                'static_break' => $serviceCategories[$catID]['static_break'] ?? null,
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


    private function getProducts() : iterable
    {
        $productsFinal = collect();

        foreach (Product::published()->get() as $product) {
            $productsFinal->put($product->id, [
                'id' => $product->id,
                'title' => $product->title,
                'price' => $product->price,
            ]);
        }

        return $productsFinal;
    }

    private function getEmployees(): iterable
    {
        if ($this->currentUser?->role !== 'employee') {
            $arr = Employee::all();
        } else {
            $this->currentUser = Employee::where("ID", $this->currentUser->id)->first();
            $arr = Employee::whereIn("ID", [$this->currentUser?->id, ...$this->currentUser->mutual_calendar_blocking_employees])->get();
        }

        $employeesFinal = collect();

        foreach ($arr as $employee) {
            $employeesFinal->put($employee->ID, [
                'id' => $employee->ID,
                'name' => $employee->first_name,
                'profileImage' => $employee->profile_picture,
                'allowed_services' => $employee->allowed_service_ids,
                'vacation_color' => $employee->vacation_color
            ]);
        }

        return $employeesFinal;
    }

    private function metrics(IFilterComponent $filter): array
    {
        $metrics = [];

        if(current_user_can("view_appointments_list")) {
            $metrics[] = Main::initModule(new MetricCard(
                id: "reservations_count_metric",
                heading: "Počet termínov",
                query: Appointment::query(),
                icon: 'dashicons-groups',
                operator: "count",
                filter: $filter,
            ));
        }

        if(current_user_can("view_appointments_money_statistics")) {
            $metrics[] = Main::initModule(new MetricCard(
                id: "income_metric",
                heading: "Príjem celkom",
                query: Appointment::query()->with(['payments'])->where("status", AppointmentStatus::OK)->where("type", AppointmentType::RESERVATION),
                targetAttribute: "payments.amount",
                icon: 'dashicons-arrow-up-alt',
                operator: "sum",
                filter: $filter,
                formatter: function ($value) {
                    return $value . "€";
                },
            ));
        }

        return $metrics;
    }
}