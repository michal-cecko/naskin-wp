<?php

namespace Theme\Models\Appointment;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Saurus\App\Modules\Wordpress\Models\Model;
use Theme\Enum\AppointmentStatus;
use Theme\Enum\AppointmentType;
use Theme\PostTypes\Customer;
use Theme\Users\Employee;

class Appointment extends Model
{
    protected $table = 'appointments';

    protected $fillable = [
        'employee_id',
        'start_at',
        'end_at',
        'customer_id',
        'note',
        'type',
        'cancel_token',
        'has_been_reminded',
        'status',
    ];

    protected $casts = [
        'type' => AppointmentType::class,
        'status' => AppointmentStatus::class,
    ];

    public function getCancelUrlAttribute() : ?string {
        if($this->status !== AppointmentStatus::OK) {
            return null;
        }

        return main()->api()->getApiEndpointUrl(routeName: "appointment.customer-cancel", routeParams: ['id' => $this->id, 'token' => $this->cancel_token]);
    }

    public function getIcsUrlAttribute() : ?string {
        $date = $this->date;

        if($this->date->isPast()) {
            return null;
        }

        $servicesDuration = $this->services->sum('duration');

        $start = $date->format("Y-m-d H:i");
        $date = $date->modify("+" . $servicesDuration . " minutes");
        $end = $date->format("Y-m-d H:i");

        $title = get_bloginfo("name") . " - " . "rezervácia";

        return main()->api()->getApiEndpointUrl(routeName: "appointment.generate-ics", routeParams: ['start' => $start, 'end' => $end, 'title' => $title]);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(AppointmentService::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}