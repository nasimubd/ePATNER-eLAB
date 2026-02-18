<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OtRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'room_number',
        'description',
        'equipment_available',
        'status',
        'capacity',
    ];

    protected $casts = [
        'equipment_available' => 'array',
        'capacity' => 'integer',
    ];

    // Relationships
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
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

    public function scopeAvailable($query)
    {
        return $query->whereIn('status', ['active']);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Accessors
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getIsAvailableAttribute(): bool
    {
        return in_array($this->status, ['active']);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'active' => 'bg-green-100 text-green-800',
            'inactive' => 'bg-gray-100 text-gray-800',
            'maintenance' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getEquipmentListAttribute(): string
    {
        if (empty($this->equipment_available)) {
            return 'No equipment listed';
        }
        return implode(', ', $this->equipment_available);
    }

    // Helper Methods
    public function isAvailableAt(string $date, string $time, ?int $excludeBookingId = null): bool
    {
        $query = $this->bookings()
            ->where('booking_date', $date)
            ->where('booking_time', '<=', $time)
            ->where('end_time', '>', $time)
            ->whereIn('status', ['pending', 'confirmed']);

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->count() === 0;
    }

    public function hasEquipment(array $requiredEquipment): bool
    {
        if (empty($requiredEquipment)) {
            return true;
        }

        if (empty($this->equipment_available)) {
            return false;
        }

        return empty(array_diff($requiredEquipment, $this->equipment_available));
    }

    public function getBookingsForDate(string $date)
    {
        return $this->bookings()
            ->where('booking_date', $date)
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->orderBy('booking_time')
            ->get();
    }

    public function getTodayBookings()
    {
        return $this->getBookingsForDate(now()->toDateString());
    }

    public function getUpcomingBookings(int $days = 7)
    {
        return $this->bookings()
            ->where('booking_date', '>=', now()->toDateString())
            ->where('booking_date', '<=', now()->addDays($days)->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->get();
    }
}
