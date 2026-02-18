<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class WardService extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'daily_fee',
        'duration_minutes',
        'max_patients_per_slot',
        'status',
        'available_days',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'daily_fee' => 'decimal:2',
        'duration_minutes' => 'integer',
        'max_patients_per_slot' => 'integer',
        'available_days' => 'array',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    // Relationships
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function bookings(): MorphMany
    {
        return $this->morphMany(Booking::class, 'bookable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    // Accessors & Mutators
    public function getFormattedFeeAttribute(): string
    {
        return '৳' . number_format($this->daily_fee, 2);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    // Helper Methods
    public function isAvailableOnDay(string $day): bool
    {
        return in_array(strtolower($day), $this->available_days ?? []);
    }

    public function canAccommodateMorePatients(string $date, string $time): bool
    {
        $currentBookings = $this->bookings()
            ->where('booking_date', $date)
            ->where('booking_time', $time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        return $currentBookings < $this->max_patients_per_slot;
    }
}
