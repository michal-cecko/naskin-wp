<?php

namespace Theme\Models\Appointment;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Saurus\App\Modules\Wordpress\Models\Model;
use Theme\PostTypes\Service;
use Theme\Taxonomies\ServiceCategory;

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

    protected $appends = [
        "service_category",
        "service_category_id",
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, "service_id", "ID");
    }

    public function getServiceCategoryAttribute() {
        if(!$this->relationLoaded("service")) {
            $this->load("service");
        }

        return $this->service?->service_category;
    }

    public function getServiceCategoryIdAttribute() {
        return $this->service?->service_category_id;
    }
}