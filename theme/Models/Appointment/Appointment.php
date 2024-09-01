<?php

namespace Theme\Models\Appointment;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Saurus\App\Modules\Templates\Table\TableComponent;
use Saurus\App\Modules\Wordpress\Models\Model;
use Theme\Enum\AppointmentSource;
use Theme\Enum\AppointmentStatus;
use Theme\Enum\AppointmentType;
use Theme\Models\Product\ProductSale;
use Theme\PostTypes\Customer;
use Theme\Users\Employee;
use Saurus\App\Modules\Log\ILoggable;

class Appointment extends Model implements ILoggable
{
    protected $table = 'appointments';

    protected $fillable = [
        'employee_id',
        'start_at',
        'end_at',
        'break',
        'customer_id',
        'note',
        'type',
        'cancel_token',
        'has_been_reminded',
        'status',
        'source',
        'total',
    ];

    protected $casts = [
        'type' => AppointmentType::class,
        'status' => AppointmentStatus::class,
        'source' => AppointmentSource::class,
        'start_at' => "datetime",
        'end_at' => "datetime",
    ];

    public function getCancelUrlAttribute(): ?string
    {
        if ($this->status !== AppointmentStatus::OK) {
            return null;
        }

        return main()->api()->getApiEndpointUrl(routeName: "appointment.customer-cancel", routeParams: ['i' => $this->id, 't' => $this->cancel_token]);
    }

    public function getEndAtWithBreakAttribute(): Carbon
    {
        if (!$this->break) {
            return $this->end_at;
        }

        return $this->end_at->addMinutes($this->break);
    }

    public function getIcsUrlAttribute(): ?string
    {
        if ($this->start_at->isPast()) {
            return null;
        }

        return main()->api()->getApiEndpointUrl(routeName: "appointment.ics", routeParams: ['id' => $this->id, 't' => config("appointments.cron-ics-token")]);
    }

    public function getDurationWithoutBreakAttribute(): int
    {
        return $this->start_at->diffInMinutes($this->end_at);
    }

    public function getDurationWithBreakAttribute(): int
    {
        return $this->start_at->diffInMinutes($this->end_at_with_break);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, "employee_id", "ID");
    }

    public function services(): HasMany
    {
        return $this->hasMany(AppointmentService::class);
    }

    public function productSales(): HasMany
    {
        return $this->hasMany(ProductSale::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(AppointmentPayment::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, "customer_id", "ID");
    }

    public function getLogStringAttribute(): string
    {
        if ($this->type === AppointmentType::VACATION) {
            return "{$this->employee?->display_name}, od {$this->start_at->format('d.m.Y H:i')} do {$this->end_at->format('d.m.Y H:i')}" . (!empty($this->note) ? ", pozn.: {$this->note}" : "");
        }

        return "#{$this->id} {$this->customer->title}, od {$this->start_at->format('d.m.Y H:i')} do {$this->end_at->format('d.m.Y H:i')}, pod pracovníkom {$this->employee->first_name}" . (!empty($this->note) ? ", pozn.: {$this->note}" : "");
    }

    public function getShortLogStringAttribute(): string
    {
        if ($this->type === AppointmentType::VACATION) {
            return "Voľno dňa {$this->start_at->format('d.m.y H:i')}";
        }

        return "Rezervácia dňa {$this->start_at->format('d.m.y H:i')} - {$this->customer?->title}";
    }

    public function getLogServicesStringAttribute(): string
    {
        return $this->services->map(fn($service) => "{$service->name} ({$service->duration}min)")->implode(' + ');
    }

    public function getCustomerStringAttribute(): string
    {
        return "{$this->customer->title} - {$this->customer->email} - {$this->customer->phone}";
    }

    public function getLogLinkAttribute(): ?string
    {
        // TODO: Implement getLogLinkAttribute() method.
        return "";
    }

    public function getLogTitleAttribute(): string
    {
       return $this->short_log_string;
    }

    public function getFormattedServicesAttribute(): string
    {
        $formattedServices = [];
        foreach ($this->services as $service) {
            $formattedServices[] = TableComponent::anchor($service->name, get_edit_post_link($service->service_id)) . " | " . $service->price . " €";
        }
        return implode("<br>", $formattedServices);
    }
}