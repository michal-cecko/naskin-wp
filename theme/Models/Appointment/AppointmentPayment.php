<?php

namespace Theme\Models\Appointment;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Saurus\App\Modules\Wordpress\Models\Model;
use Theme\Enum\AppointmentPaymentType;
use Theme\PostTypes\Service;
use Theme\Taxonomies\ServiceCategory;

class AppointmentPayment extends Model
{
    protected $table = 'appointment_payments';

    protected $fillable = [
        'appointment_id',
        'amount',
        'note',
        'type',
    ];

    protected $casts = [
        'type' => AppointmentPaymentType::class,
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}