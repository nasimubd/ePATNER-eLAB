<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'patient_id',
        'doctor_id',
        'appointment_date',
        'appointment_time',
        'end_time',
        'duration',
        'status',
        'appointment_type',
        'chief_complaint',
        'notes',
        'priority',
        'consultation_fee',
        'payment_status',
        'created_by',
        'cancelled_by',
        'cancellation_reason',
        'cancelled_at',
        'confirmed_at',
        'completed_at'
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'duration' => 'integer', // Ensure duration is cast to integer
        'consultation_fee' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
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
        return $this->belongsTo(\App\Models\Patient::class, 'patient_id', 'id');
    }

    // Add a method to get patient by patient_id if needed
    public function getPatientByPatientId($patientId)
    {
        return Patient::where('patient_id', $patientId)->first();
    }
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Scopes - Using direct conditions instead of active() scope
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeActiveStatus($query)
    {
        return $query->whereIn('status', ['scheduled', 'confirmed', 'in_progress']);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', today())
            ->whereNotIn('status', ['completed', 'cancelled', 'no_show']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('appointment_date', [$startDate, $endDate]);
    }

    public function scopeByPriority($query, $priority = null)
    {
        if ($priority) {
            return $query->where('priority', $priority);
        }

        return $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')");
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->whereHas('patient', function ($pq) use ($search) {
                $pq->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            })
                ->orWhereHas('doctor', function ($dq) use ($search) {
                    $dq->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhere('chief_complaint', 'LIKE', "%{$search}%")
                ->orWhere('appointment_type', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Static methods to replace active() calls
     */
    public static function getActiveAppointments()
    {
        return self::whereIn('status', ['scheduled', 'confirmed', 'in_progress']);
    }

    public static function getActiveForBusiness($businessId)
    {
        return self::getActiveAppointments()->where('business_id', $businessId);
    }

    public static function getActiveForDoctor($doctorId)
    {
        return self::getActiveAppointments()->where('doctor_id', $doctorId);
    }

    /**
     * Accessors
     */
    public function getFormattedDateAttribute()
    {
        return $this->appointment_date->format('M d, Y');
    }

    public function getFormattedTimeAttribute()
    {
        return $this->appointment_time->format('h:i A');
    }

    public function getFormattedEndTimeAttribute()
    {
        return $this->end_time ? $this->end_time->format('h:i A') : null;
    }

    public function getDurationInHoursAttribute()
    {
        return round($this->duration / 60, 2);
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'scheduled' => 'blue',
            'confirmed' => 'green',
            'in_progress' => 'yellow',
            'completed' => 'gray',
            'cancelled' => 'red',
            'no_show' => 'orange',
            default => 'gray'
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'scheduled' => '<span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">Scheduled</span>',
            'confirmed' => '<span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Confirmed</span>',
            'in_progress' => '<span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">In Progress</span>',
            'completed' => '<span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">Completed</span>',
            'cancelled' => '<span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Cancelled</span>',
            'no_show' => '<span class="px-2 py-1 text-xs font-semibold text-orange-800 bg-orange-100 rounded-full">No Show</span>',
            default => '<span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">Unknown</span>'
        };
    }

    public function getPriorityBadgeAttribute()
    {
        return match ($this->priority) {
            'urgent' => '<span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Urgent</span>',
            'high' => '<span class="px-2 py-1 text-xs font-semibold text-orange-800 bg-orange-100 rounded-full">High</span>',
            'medium' => '<span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Medium</span>',
            'low' => '<span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Low</span>',
            default => '<span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">Unknown</span>'
        };
    }

    public function getDaysUntilAppointmentAttribute()
    {
        return $this->appointment_date->diffInDays(now(), false);
    }

    public function getIsUpcomingAttribute()
    {
        return $this->appointment_date >= today() &&
            !in_array($this->status, ['completed', 'cancelled', 'no_show']);
    }

    public function getIsTodayAttribute()
    {
        return $this->appointment_date->isToday();
    }

    public function getIsPastAttribute()
    {
        return $this->appointment_date->isPast();
    }

    /**
     * Methods
     */
    public function isActive()
    {
        return in_array($this->status, ['scheduled', 'confirmed', 'in_progress']);
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['scheduled', 'confirmed']) &&
            $this->appointment_date >= today();
    }

    public function canBeRescheduled()
    {
        return in_array($this->status, ['scheduled', 'confirmed']) &&
            $this->appointment_date >= today();
    }

    public function canBeCompleted()
    {
        return in_array($this->status, ['confirmed', 'in_progress']);
    }

    public function canBeEdited()
    {
        return in_array($this->status, ['scheduled', 'confirmed']) &&
            $this->appointment_date >= today();
    }

    public function canBeDeleted()
    {
        return in_array($this->status, ['cancelled', 'no_show']) ||
            ($this->status === 'completed' && $this->appointment_date->lt(now()->subDays(30)));
    }

    public function markAsCompleted($completedBy = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function cancel($reason = null, $cancelledBy = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_by' => $cancelledBy,
            'cancelled_at' => now(),
        ]);
    }

    public function confirm()
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function markAsNoShow()
    {
        $this->update([
            'status' => 'no_show'
        ]);
    }

    public function startProgress()
    {
        $this->update([
            'status' => 'in_progress'
        ]);
    }

    /**
     * Boot method to handle automatic end_time calculation
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($appointment) {
            if ($appointment->appointment_time && $appointment->duration && !$appointment->end_time) {
                $startTime = Carbon::parse($appointment->appointment_time);
                $appointment->end_time = $startTime->addMinutes($appointment->duration)->format('H:i');
            }
        });
    }
}
