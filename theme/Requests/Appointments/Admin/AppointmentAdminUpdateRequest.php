<?php

namespace Theme\Requests\Appointments\Admin;

use Saurus\App\Rules\Exists;
use Saurus\App\Rules\PostExists;
use Theme\Enum\AppointmentPaymentType;
use Theme\Enum\AppointmentSource;
use Theme\Enum\AppointmentType;
use Theme\Models\Appointment\Appointment;
use Theme\Models\Appointment\AppointmentPayment;
use Theme\Models\Product\ProductSale;
use Theme\PostTypes\Product;
use Theme\PostTypes\Service;
use Theme\Requests\AuthenticatedAdminRequest;

class AppointmentAdminUpdateRequest extends AuthenticatedAdminRequest
{

    public function rules(): array
    {

        $reservation = AppointmentType::RESERVATION->value;

        return [
            'id' => ['required', 'integer', new Exists(table: Appointment::getTableName())],

            'employeeID' => ['required', 'integer', new Exists(table: "users", column: "ID")],

            'date' => 'required|array',
            'date.start' => 'required|date',
            'date.end' => 'required|date',

            'type' => ['required', 'in:' . implode(",", AppointmentType::stringCases())],

            'source' => ['required_if:type,' . $reservation, 'in:' . implode(",", AppointmentSource::stringCases())],

            'services' => ['required_if:type,' . $reservation, 'array'],
            'services.*' => ['required_if:services,array', 'integer', new PostExists(postModel: Service::class)],

            'payments' => ['sometimes', 'array', "min:0", "nullable"],
            'payments.*.id' => ['sometimes', 'integer', new Exists(table: AppointmentPayment::getTableName())],
            'payments.*.amount' => ['required_if:type,' . $reservation, 'decimal:0,2'],
            'payments.*.type' => ['required_if:type,' . $reservation, 'in:' . implode(",", AppointmentPaymentType::stringCases())],
            'payments.*.note' => ['sometimes', 'nullable', 'string', 'max:255'],

            'productSales' => ['sometimes', 'array', "min:0", "nullable"],
            'productSales.*.id' => ['sometimes', 'integer', new Exists(table: ProductSale::getTableName())],
            'productSales.*.product_id' => ['required_if:type,' . $reservation, 'integer', new PostExists(postModel: Product::class)],
            'productSales.*.price' => ['required_if:type,' . $reservation, 'decimal:0,2'],
            'productSales.*.quantity' => ['required_if:type,' . $reservation, 'integer', 'min:1'],
            'productSales.*.note' => ['sometimes', 'nullable', 'string', 'max:255'],

            'note' => 'sometimes|nullable|string|max:1000',

            'notify_customer' => ['required', 'boolean'],
            'notify_employee' => ['required', 'boolean'],
        ];

    }

}