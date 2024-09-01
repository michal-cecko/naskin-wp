<?php

namespace Theme\Modules\ProductSales;

use Carbon\Carbon;
use Exception;
use Saurus\App\Enums\ApiMethod;
use Saurus\App\Exceptions\Request\RequestException;
use Saurus\App\Exceptions\Request\ValidationFailedException;
use Saurus\App\Exceptions\Route\ApiEndpointAlreadyExistException;
use Saurus\App\Traits\Validation;
use Theme\Enum\AppointmentSource;
use Theme\Models\Appointment\Appointment;
use Theme\Models\Expense\Expense;
use Theme\Models\Product\ProductSale;
use Theme\PostTypes\Product;
use Theme\Requests\ProductSales\ProductSalesCreateRequest;
use Theme\Requests\ProductSales\ProductSalesEditRequest;
use Theme\Requests\ProductSales\ProductSalesDeleteRequest;
use Theme\Services\Expenses\ExpenseService;
use Theme\Services\ProductSales\ProductSalesService;
use Theme\Taxonomies\ExpenseCategory;

class ProductSales
{
    use Validation;

    /**
     * @throws ApiEndpointAlreadyExistException
     */
    public function __construct()
    {
        $this->initRest();
    }

    /**
     * @throws ApiEndpointAlreadyExistException
     */
    private function initRest(): void
    {
        main()->api()->addApiEndpoint(ApiMethod::POST, "/product-sale/delete", "product_sale.delete", [$this, 'delete']);
        main()->api()->addApiEndpoint(ApiMethod::POST, "/product-sale/store", "product_sale.store", [$this, 'store']);
        main()->api()->addApiEndpoint(ApiMethod::POST, "/product-sale/edit", "product_sale.edit", [$this, 'edit']);
    }

    /**
     * @throws RequestException
     * @throws Exception
     */
    public function store(ProductSalesCreateRequest $request) : void {
        $data = $request->validated();

        $appointment = $this->getAppointment($data['appointment_id'] ?? null);
        $product = $this->getProduct($data['product_id'] ?? null);

        $productSale = ProductSalesService::store(
            product: $product,
            reservation: $appointment,
            price: $data['price'],
            quantity: $data['quantity'],
            note: $data['note'] ?? null,
            sold_at: !empty($data['sold_at']) ? Carbon::parse($data['sold_at']) : null,
        );

        main()->log()->infoDB("Nový predaj produktu: {$productSale->log_title}.", resources: [$productSale, $product, $appointment]);

        wp_send_json_success(['message' => 'Predaj produktu bol úspešne pridaný.', 'id' => $productSale->id]);
    }

    /**
     * @throws RequestException
     * @throws Exception
     */
    public function edit(ProductSalesEditRequest $request) : void {
        $data = $request->validated();

        $appointment = $this->getAppointment($data['appointment_id'] ?? null);
        $product = $this->getProduct($data['product_id'] ?? null);

        ['productSale' => $productSale, 'changes' => $changes] = ProductSalesService::update(
            productSale: (int) $data['id'],
            product: $product,
            reservation: $appointment,
            price: $data['price'],
            quantity: $data['quantity'],
            note: $data['note'] ?? null,
            sold_at: !empty($data['sold_at']) ? Carbon::parse($data['sold_at']) : null,
        );

        main()->log()->infoDB("Upravený predaj produktu: {$productSale->log_string}", changes: $changes, resources: [$productSale, $product, $appointment]);

        wp_send_json_success(['message' => 'Predaj produktu bol úspešne upravený.', 'id' => $productSale->id]);
    }

    /**
     * @throws RequestException
     * @throws ValidationFailedException
     * @throws Exception
     */
    public function delete(ProductSalesDeleteRequest $request) : void {
        $data = $request->validated();

        $productSale = ProductSale::find($data['id']);

        if(!$productSale) {
            throw new RequestException("Výdavok nebol nájdený.", 404);
        }

        ProductSalesService::delete($productSale);

        main()->log()->infoDB("Vymazaný predaj produktu: {$productSale->log_string}", resources: [$productSale]);

        wp_send_json_success(['message' => 'Predaj produktu bol úspešne vymazaný.']);
    }

    /**
     * @param int|null $id
     * @return Appointment|null
     * @throws RequestException
     */
    private function getAppointment(?int $id) : ?Appointment {
        if(!$id) return null;
        $app = Appointment::find($id);
        if(!$app) throw new RequestException("Rezervácia nebola nájdená.", 404);
        return $app;
    }

    /**
     * @throws RequestException
     */
    private function getProduct(int $id) : ?Product {
        $product = Product::where("ID", $id)->first();
        if(!$product) throw new RequestException("Produkt nebol nájdený.", 404);
        return $product;
    }
}