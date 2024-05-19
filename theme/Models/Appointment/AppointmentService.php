<?php

namespace Theme\Models\Appointment;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Saurus\App\Modules\Wordpress\Models\Model;
use Theme\PostTypes\Service;

class AppointmentService extends Model
{
    protected $table = 'appointment_services';

    protected $fillable = [
        'service_id',
        'appointment_id',
        'duration',
        'name',
        'price',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}