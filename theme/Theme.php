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
use Theme\Modules\Expenses\Expenses;
use Theme\Modules\Expenses\ExpensesDashboardView;
use Theme\Modules\Notifications\Notifications;
use Theme\Modules\Plugins\Acf;
use Theme\Modules\Products\Products;
use Theme\Modules\ProductSales\ProductSales;
use Theme\Modules\ProductSales\ProductSalesDashboardView;
use Theme\Modules\Role\Roles;
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
    private Acf $acf;
    private ServicesTransient $servicesTransient;
    private ServicesDashboardTable $servicesDashboardTable;
    private Services $services;
    private Appointments $appointments;
    private AppointmentsDashboardView $appointmentsDashboardView;
    private BladeDirectives $bladeDirectives;
    private Customers $customers;
    private CustomersDashboardTable $customersDashboardTable;
    private Roles $roles;
    private Expenses $expenses;
    private Products $products;
    private ProductSalesDashboardView $productSalesDashboardView;
    private ProductSales $productSales;
    private Notifications $notifications;
    private AdminAppointments $adminAppointments;
    private ExpensesDashboardView $expensesDashboardView;

    protected function __construct()
    {
        //Modules initialization
        $this->assets = Main::initModule(new Assets());
        $this->themeSetup = Main::initModule(new ThemeSetup());
        $this->acf = Main::initModule(new Acf());
        $this->roles = Main::initModule(new Roles());
        $this->servicesTransient = Main::initModule(new ServicesTransient());
        $this->services = Main::initModule(new Services());
        $this->appointments = Main::initModule(new Appointments());
        $this->appointmentsDashboardView = Main::initModule(new AppointmentsDashboardView());
        $this->servicesDashboardTable = Main::initModule(new ServicesDashboardTable());
        $this->bladeDirectives = Main::initModule(new BladeDirectives());
        $this->customers = Main::initModule(new Customers());
        $this->customersDashboardTable = Main::initModule(new CustomersDashboardTable());
        $this->adminAppointments = Main::initModule(new AdminAppointments());
        $this->expensesDashboardView = Main::initModule(new ExpensesDashboardView());
        $this->expenses = Main::initModule(new Expenses());
        $this->products = Main::initModule(new Products());
        $this->productSalesDashboardView = Main::initModule(new ProductSalesDashboardView());
        $this->productSales = Main::initModule(new ProductSales());
        $this->notifications = Main::initModule(new Notifications());
    }


    // Modules getters

    public function acf(): Acf
    {
        return $this->acf;
    }

    public function roles(): Roles
    {
        return $this->roles;
    }
}