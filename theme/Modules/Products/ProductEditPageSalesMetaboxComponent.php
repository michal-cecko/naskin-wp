<?php

namespace Theme\Modules\Products;

use Saurus\App\Main;
use Theme\Modules\Products\Table\Sales\ProductDetailSalesFilterComponent;
use Theme\Modules\Products\Table\Sales\ProductDetailSalesTableComponent;

class ProductEditPageSalesMetaboxComponent {

    private ProductDetailSalesTableComponent $salesTable;
    private ProductDetailSalesFilterComponent $salesFilter;

    public function __construct(protected $product) {
        $this->salesFilter = Main::initModule(new ProductDetailSalesFilterComponent("fltr_sales"));
        $this->salesTable = Main::initModule(new ProductDetailSalesTableComponent(product: $product, id: "tbl_sales", filter: $this->salesFilter));
    }

    public function generate(): string
    {
        $html = $this->salesFilter->generate();
        $html .= $this->salesTable->generate();

        return $html;
    }
}