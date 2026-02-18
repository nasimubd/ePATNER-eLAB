<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'old_status',
        'new_status',
        'reason',
        'changed_fields',
        'changed_by',
        'changed_at',
    ];

    protected $casts = [
        'changed_fields' => 'array',
        'changed_at' => 'datetime',
    ];

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Scopes
    public function scopeForBooking($query, $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('changed_by', $userId);
    }

    // Accessors
    public function getFormattedChangedAtAttribute(): string
    {
        return $this->changed_at->format('M d, Y H:i A');
    }

    public function getStatusChangeDescriptionAttribute(): string
    {
        if ($this->old_status) {
            return "Changed from {$this->old_status} to {$this->new_status}";
        }
        return "Set status to {$this->new_status}";
    }
}
