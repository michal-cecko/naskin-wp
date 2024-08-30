<?php

namespace Theme\Models\Product;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Saurus\App\Modules\Wordpress\Models\Model;
use Theme\Models\Appointment\Appointment;
use Theme\PostTypes\Product;

class ProductSale extends Model
{
    protected $table = 'product_sales';

    protected $fillable = [
        'appointment_id',
        'product_id',
        'price',
        'sold_at',
        'quantity',
        'note',
    ];

    protected $casts = [
        'sold_at' => "datetime",
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "product_id", "ID");
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, "appointment_id", "id");
    }
}

