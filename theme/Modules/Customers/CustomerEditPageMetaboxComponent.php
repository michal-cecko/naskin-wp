<?php

namespace Theme\Modules\Customers;

use Saurus\App\Main;
use Theme\Modules\Customers\Table\CustomerDetailAppointmentsFilterComponent;
use Theme\Modules\Customers\Table\CustomerDetailAppointmentsTableComponent;

class CustomerEditPageMetaboxComponent {

    private CustomerDetailAppointmentsTableComponent $appointmentTable;
    private CustomerDetailAppointmentsFilterComponent $appointmentTableFilter;

    public function __construct(protected $customer) {
        $this->appointmentTableFilter = Main::initModule(new CustomerDetailAppointmentsFilterComponent("filter_1"));
        $this->appointmentTable = Main::initModule(new CustomerDetailAppointmentsTableComponent(customer: $customer, id: "table_1", filter: $this->appointmentTableFilter));
        $this->appointmentTableFilter->setConnectedComponents($this->appointmentTable);
    }

    public function generate(): string
    {
        $html = $this->appointmentTableFilter->generate();
        $html .= $this->appointmentTable->generate();

        return $html;
    }
}