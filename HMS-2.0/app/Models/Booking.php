<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'user_id',
        'patient_id',
        'bookable_type',
        'bookable_id',
        'ot_room_id',
        'booking_date',
        'booking_time',
        'end_time',
        'preparation_time_minutes',
        'cleanup_time_minutes',
        'service_fee',
        'room_fee',
        'equipment_fee',
        'total_fee',
        'booking_type',
        'status',
        'complexity_level',
        'notes',
        'special_requirements',
        'confirmed_at',
        'cancelled_at',
        'completed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'booking_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'service_fee' => 'decimal:2',
        'room_fee' => 'decimal:2',
        'equipment_fee' => 'decimal:2',
        'total_fee' => 'decimal:2',
        'preparation_time_minutes' => 'integer',
        'cleanup_time_minutes' => 'integer',
        'special_requirements' => 'array',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function bookable(): MorphTo
    {
        return $this->morphTo();
    }

    public function otRoom(): BelongsTo
    {
        return $this->belongsTo(OtRoom::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(BookingStatusHistory::class);
    }

    // Scopes
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('booking_type', $type);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('booking_date', $date);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', now()->toDateString());
    }

    // Accessors
    public function getFormattedTotalFeeAttribute(): string
    {
        return '৳' . number_format($this->total_fee, 2);
    }

    public function getIsWardBookingAttribute(): bool
    {
        return $this->booking_type === 'ward';
    }

    public function getIsOtBookingAttribute(): bool
    {
        return $this->booking_type === 'ot';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'no_show' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
