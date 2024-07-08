<?php

namespace Theme;

use Saurus\App\Main;
use Saurus\App\Modules\Singleton\Singleton;
use Theme\Modules\Appointments\AdminAppointments;
use Theme\Modules\Appointments\Appointments;
use Theme\Modules\Appointments\AppointmentsDashboardView;
use Theme\Modules\Assets\Assets;
use Theme\Modules\Customers\Customers;
use Theme\Modules\Customers\CustomersDashboardTable;
use Theme\Modules\EmployeeRole\EmployeeRole;
use Theme\Modules\Ics\Ics;
use Theme\Modules\Plugins\Acf;
use Theme\Modules\Services\Services;
use Theme\Modules\Services\ServicesDashboardTable;
use Theme\Modules\Services\ServicesTransient;
use Theme\Modules\Setup\BladeDirectives;
use Theme\Modules\Setup\ThemeSetup;
use Theme\PostTypes\Customer;

class Theme extends Singleton
{
    private Assets $assets;
    private ThemeSetup $themeSetup;
    private Ics $ics;
    private Acf $acf;
    private ServicesTransient $servicesTransient;
    private ServicesDashboardTable $servicesDashboardTable;
    private Services $services;
    private Appointments $appointments;
    private AppointmentsDashboardView $appointmentsDashboardView;
    private BladeDirectives $bladeDirectives;
    private Customers $customers;
    private CustomersDashboardTable $customersDashboardTable;
    private EmployeeRole $employeeRole;

    protected function __construct()
    {
        //Modules initialization
        $this->assets = Main::initModule(new Assets());
        $this->themeSetup = Main::initModule(new ThemeSetup());
        $this->acf = Main::initModule(new Acf());
        $this->ics = Main::initModule(new Ics());
        $this->servicesTransient = Main::initModule(new ServicesTransient());
        $this->services = Main::initModule(new Services());
        $this->appointments = Main::initModule(new Appointments());
        $this->appointmentsDashboardView = Main::initModule(new AppointmentsDashboardView());
        $this->servicesDashboardTable = Main::initModule(new ServicesDashboardTable());
        $this->bladeDirectives = Main::initModule(new BladeDirectives());
        $this->customers = Main::initModule(new Customers());
        $this->customersDashboardTable = Main::initModule(new CustomersDashboardTable());
        $this->employeeRole = Main::initModule(new EmployeeRole());
        $this->adminAppointments = Main::initModule(new AdminAppointments());
    }


    // Modules getters

    public function acf(): Acf
    {
        return $this->acf;
    }

    public function ics(): Ics
    {
        return $this->ics;
    }
}