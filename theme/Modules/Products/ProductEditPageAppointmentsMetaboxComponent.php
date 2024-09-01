<?php

namespace Theme\Modules\Products;

use Saurus\App\Main;
use Theme\Modules\Products\Table\Appointments\ProductDetailAppointmentsFilterComponent;
use Theme\Modules\Products\Table\Appointments\ProductDetailAppointmentsTableComponent;

class ProductEditPageAppointmentsMetaboxComponent {

    private ProductDetailAppointmentsTableComponent $appointmentTable;
    private ProductDetailAppointmentsFilterComponent $appointmentTableFilter;

    public function __construct(protected $product) {
        $this->appointmentTableFilter = Main::initModule(new ProductDetailAppointmentsFilterComponent("fltr_apps"));
        $this->appointmentTable = Main::initModule(new ProductDetailAppointmentsTableComponent(product: $product, id: "tbl_apps", filter: $this->appointmentTableFilter));
    }

    public function generate(): string
    {
        $html = $this->appointmentTableFilter->generate();
        $html .= $this->appointmentTable->generate();

        return $html;
    }
}