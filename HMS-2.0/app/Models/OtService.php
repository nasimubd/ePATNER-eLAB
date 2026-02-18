<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class OtService extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'base_fee',
        'room_fee',
        'equipment_fee',
        'estimated_duration_minutes',
        'preparation_time_minutes',
        'cleanup_time_minutes',
        'required_equipment',
        'required_staff',
        'complexity_level',
        'status',
    ];

    protected $casts = [
        'base_fee' => 'decimal:2',
        'room_fee' => 'decimal:2',
        'equipment_fee' => 'decimal:2',
        'estimated_duration_minutes' => 'integer',
        'preparation_time_minutes' => 'integer',
        'cleanup_time_minutes' => 'integer',
        'required_equipment' => 'array',
        'required_staff' => 'array',
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

    public function scopeByComplexity($query, $complexity)
    {
        return $query->where('complexity_level', $complexity);
    }

    // Accessors & Mutators
    public function getTotalFeeAttribute(): float
    {
        return $this->base_fee + $this->room_fee + $this->equipment_fee;
    }

    public function getFormattedTotalFeeAttribute(): string
    {
        return '৳' . number_format($this->total_fee, 2);
    }

    public function getTotalDurationMinutesAttribute(): int
    {
        return $this->estimated_duration_minutes +
            $this->preparation_time_minutes +
            $this->cleanup_time_minutes;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    // Helper Methods
    public function calculateEndTime(string $startTime): string
    {
        $start = \Carbon\Carbon::createFromFormat('H:i:s', $startTime);
        return $start->addMinutes($this->total_duration_minutes)->format('H:i:s');
    }

    public function hasRequiredEquipment(array $availableEquipment): bool
    {
        if (empty($this->required_equipment)) {
            return true;
        }

        return empty(array_diff($this->required_equipment, $availableEquipment));
    }
}
