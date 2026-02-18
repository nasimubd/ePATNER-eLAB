<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WaitingList extends Model
{
    use HasFactory;

    protected $table = 'waiting_list';

    protected $fillable = [
        'business_id',
        'patient_id',
        'doctor_id',
        'preferred_date_start',
        'preferred_date_end',
        'preferred_time_start',
        'preferred_time_end',
        'appointment_type',
        'priority',
        'status',
        'notes',
        'notified_at',
        'expires_at'
    ];

    protected $casts = [
        'preferred_date_start' => 'date',
        'preferred_date_end' => 'date',
        'preferred_time_start' => 'datetime:H:i',
        'preferred_time_end' => 'datetime:H:i',
        'notified_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    /**
     * Relationships
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Scopes - Using direct conditions instead of active() scope
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeWaitingStatus($query)
    {
        return $query->where('status', 'waiting')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now())
            ->where('status', 'waiting');
    }

    public function scopeByPriority($query, $priority = null)
    {
        if ($priority) {
            return $query->where('priority', $priority);
        }

        return $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')");
    }

    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeNotified($query)
    {
        return $query->where('status', 'notified');
    }

    /**
     * Static methods to replace active() calls
     */
    public static function getActiveEntries()
    {
        return self::where('status', 'waiting')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public static function getActiveForBusiness($businessId)
    {
        return self::getActiveEntries()->where('business_id', $businessId);
    }

    public static function getActiveForDoctor($doctorId)
    {
        return self::getActiveEntries()->where('doctor_id', $doctorId);
    }

    /**
     * Accessors
     */
    public function getFormattedPreferredDateRangeAttribute()
    {
        if ($this->preferred_date_start && $this->preferred_date_end) {
            if ($this->preferred_date_start->eq($this->preferred_date_end)) {
                return $this->preferred_date_start->format('M d, Y');
            }
            return $this->preferred_date_start->format('M d, Y') . ' - ' .
                $this->preferred_date_end->format('M d, Y');
        }
        return 'Any date';
    }

    public function getFormattedPreferredTimeRangeAttribute()
    {
        if (!$this->preferred_time_start || !$this->preferred_time_end) {
            return 'Any time';
        }

        return $this->preferred_time_start->format('h:i A') . ' - ' .
            $this->preferred_time_end->format('h:i A');
    }

    public function getDaysWaitingAttribute()
    {
        return $this->created_at->diffInDays(now());
    }

    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Methods
     */
    public function isActive()
    {
        return $this->status === 'waiting' &&
            (is_null($this->expires_at) || $this->expires_at->isFuture());
    }

    public function notify()
    {
        $this->update([
            'status' => 'notified',
            'notified_at' => now(),
            'expires_at' => now()->addHours(24)
        ]);
    }

    public function schedule()
    {
        $this->update(['status' => 'scheduled']);
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function expire()
    {
        $this->update(['status' => 'expired']);
    }

    public function matchesSlot($date, $time)
    {
        if ($this->preferred_date_start && $this->preferred_date_end) {
            $slotDate = Carbon::parse($date);
            if ($slotDate->lt($this->preferred_date_start) || $slotDate->gt($this->preferred_date_end)) {
                return false;
            }
        }

        if (!$this->preferred_time_start || !$this->preferred_time_end) {
            return true;
        }

        $timeString = is_string($time) ? $time : $time->format('H:i');
        $preferredStart = $this->preferred_time_start->format('H:i');
        $preferredEnd = $this->preferred_time_end->format('H:i');

        return $timeString >= $preferredStart && $timeString <= $preferredEnd;
    }

    public function canBeNotified()
    {
        return $this->status === 'waiting' && !$this->is_expired;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($waitingList) {
            if (!$waitingList->expires_at) {
                $waitingList->expires_at = now()->addDays(30);
            }
        });
    }
}
