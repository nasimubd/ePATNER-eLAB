<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorScheduleException extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'date',
        'start_time',
        'end_time',
        'is_available',
        'reason',
        'is_recurring',
        'recurrence_pattern'
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_available' => 'boolean',
        'is_recurring' => 'boolean',
        'recurrence_pattern' => 'array'
    ];

    /**
     * Relationships
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Scopes - Using direct conditions instead of active() scope
     */
    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeUnavailable($query)
    {
        return $query->where('is_available', false);
    }

    public function scopeExtraAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', today());
    }

    public function scopeActiveExceptions($query)
    {
        return $query->where('date', '>=', today());
    }

    /**
     * Static methods to replace active() calls
     */
    public static function getActiveExceptions()
    {
        return self::where('date', '>=', today());
    }

    public static function getActiveForDoctor($doctorId)
    {
        return self::getActiveExceptions()->where('doctor_id', $doctorId);
    }

    /**
     * Accessors
     */
    public function getFormattedDateAttribute()
    {
        return $this->date ? $this->date->format('M d, Y') : 'Recurring';
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time ? $this->start_time->format('h:i A') : 'All Day';
    }

    public function getFormattedEndTimeAttribute()
    {
        return $this->end_time ? $this->end_time->format('h:i A') : 'All Day';
    }

    public function getTypeAttribute()
    {
        return $this->is_available ? 'Extra Availability' : 'Unavailable';
    }

    public function getTypeBadgeAttribute()
    {
        if ($this->is_available) {
            return '<span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Extra Available</span>';
        } else {
            return '<span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Unavailable</span>';
        }
    }

    /**
     * Methods
     */
    public function isActive()
    {
        return $this->date >= today();
    }

    public function isFullDayException()
    {
        return is_null($this->start_time) && is_null($this->end_time);
    }

    public function affectsTime($time)
    {
        if ($this->isFullDayException()) {
            return true;
        }

        if ($this->start_time && $this->end_time) {
            return $time >= $this->start_time->format('H:i') &&
                $time <= $this->end_time->format('H:i');
        }

        return false;
    }

    public function getRecurrenceDescription()
    {
        if (!$this->is_recurring || !$this->recurrence_pattern) {
            return 'One-time';
        }

        $pattern = $this->recurrence_pattern;

        if (isset($pattern['type'])) {
            switch ($pattern['type']) {
                case 'weekly':
                    $days = $pattern['days'] ?? [];
                    return 'Weekly on ' . implode(', ', $days);
                case 'monthly':
                    return 'Monthly on day ' . ($pattern['day'] ?? 1);
                case 'daily':
                    return 'Daily';
                default:
                    return 'Custom pattern';
            }
        }

        return 'Custom pattern';
    }
}
