<?php

namespace Theme\Models\Product;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Saurus\App\Modules\Log\ILoggable;
use Saurus\App\Modules\Wordpress\Models\Model;
use Theme\Enum\ProductSale\ProductSalePaymentType;
use Theme\Models\Appointment\Appointment;
use Theme\PostTypes\Product;

class ProductSale extends Model implements ILoggable
{
    protected $table = 'product_sales';

    protected $fillable = [
        'appointment_id',
        'product_id',
        'price',
        'sold_at',
        'quantity',
        'note',
        'payment_type',
    ];

    protected $casts = [
        'sold_at' => "datetime",
        'payment_type' => ProductSalePaymentType::class,
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "product_id", "ID");
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, "appointment_id", "id");
    }

    public function getTotalPriceAttribute(): float
    {
        return round($this->price * $this->quantity, 2);
    }

    public function getLogLinkAttribute(): ?string
    {
        return admin_url("admin.php?page=product_sale_detail&id={$this->id}");
    }

    public function getLogTitleAttribute(): string
    {
        return "Predaný {$this->product->title} ({$this->quantity}, {$this->total_price}€)";
    }

    public function getLogStringAttribute(): string
    {
        return "{$this->product?->title} ({$this->quantity} x {$this->price}€ = {$this->total_price}€) / {$this->sold_at?->format("d.m.y")}";
    }
}

