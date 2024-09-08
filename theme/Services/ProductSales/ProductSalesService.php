<?php

namespace Theme\Services\ProductSales;

use Carbon\Carbon;
use Saurus\App\Exceptions\Request\RequestException;
use Theme\Enum\ProductSale\ProductSalePaymentType;
use Theme\Models\Appointment\Appointment;
use Theme\Models\Product\ProductSale;
use Theme\PostTypes\Product;

class ProductSalesService
{
    /**
     * @throws RequestException
     */
    public static function store(int|Product $product, null|int|Appointment $reservation, float $price, int $quantity, ?string $note = null, ?Carbon $sold_at = null, ?ProductSalePaymentType $paymentType = null): ProductSale
    {

        if (is_int($product)) {
            $product = Product::find($product);
            if (!$product) throw new RequestException("Produkt nebol nájdený.", 404);
        }

        if (is_int($reservation)) {
            $reservation = Appointment::find($reservation);
            if (!$reservation) throw new RequestException("Rezervácia nebola nájdená.", 404);
        }

        return ProductSale::create([
            'product_id' => $product->id,
            'appointment_id' => $reservation->id,
            'quantity' => $quantity,
            'note' => $note,
            'payment_type' => $paymentType,
            'sold_at' => $sold_at,
            'price' => $price,
        ]);
    }

    /**
     * @throws RequestException
     */
    public static function update(int|ProductSale $productSale, int|Product $product, null|int|Appointment $reservation, float $price, int $quantity, ?string $note = null, ?Carbon $sold_at = null, ?ProductSalePaymentType $paymentType = null): array
    {

        if (is_int($productSale)) {
            $productSale = ProductSale::find($productSale);
            if (!$productSale) throw new RequestException("Predanie produktu nebolo nájdené.", 404);
        }

        if (is_int($product)) {
            $product = Product::find($product);
            if (!$product) throw new RequestException("Produkt nebol nájdený.", 404);
        }

        if (is_int($reservation)) {
            $reservation = Appointment::find($reservation);
            if (!$reservation) throw new RequestException("Rezervácia nebola nájdená.", 404);
        }

        $productSale->fill([
            'product_id' => $product->id,
            'appointment_id' => $reservation->id,
            'quantity' => $quantity,
            'note' => $note,
            'payment_type' => $paymentType,
            'sold_at' => $sold_at,
            'price' => $price,
        ]);
        $changes = $productSale->getChangedColumns();
        $productSale->save();

        return ['productSale' => $productSale, 'changes' => $changes];
    }

    /**
     * @throws RequestException
     */
    public static function delete(ProductSale|int $productSale): ?bool
    {
        if (is_int($productSale)) {
            $productSale = ProductSale::find($productSale);
            if (!$productSale) throw new RequestException("Predanie produktu nebolo nájdené.", 404);
        }

        return $productSale->delete();
    }
}