<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DoctorTimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'date',
        'time_slot',
        'is_available',
        'is_booked',
        'slot_type',
        'max_appointments',
        'current_appointments'
    ];

    protected $casts = [
        'date' => 'date',
        'time_slot' => 'datetime:H:i',
        'is_available' => 'boolean',
        'is_booked' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id', 'doctor_id')
            ->whereDate('appointment_date', $this->date)
            ->whereTime('appointment_time', $this->time_slot);
    }

    /**
     * Scopes - Using direct conditions instead of active() scope
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
            ->where('is_booked', false);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', today());
    }

    // Replace active() calls with this method
    public function scopeActiveSlots($query)
    {
        return $query->where('is_available', true)
            ->where('date', '>=', today());
    }

    /**
     * Accessors
     */
    public function getFormattedTimeAttribute()
    {
        return $this->time_slot->format('h:i A');
    }

    public function getFormattedDateAttribute()
    {
        return $this->date->format('M d, Y');
    }

    public function getIsFullyBookedAttribute()
    {
        return $this->current_appointments >= $this->max_appointments;
    }

    public function getAvailableSpotsAttribute()
    {
        return $this->max_appointments - $this->current_appointments;
    }

    /**
     * Methods
     */
    public function canBook()
    {
        return $this->is_available &&
            !$this->is_booked &&
            $this->current_appointments < $this->max_appointments &&
            $this->date >= today();
    }

    public function book()
    {
        $this->increment('current_appointments');

        if ($this->current_appointments >= $this->max_appointments) {
            $this->update(['is_booked' => true]);
        }
    }

    public function unbook()
    {
        if ($this->current_appointments > 0) {
            $this->decrement('current_appointments');
            $this->update(['is_booked' => false]);
        }
    }

    public function block($reason = null)
    {
        $this->update([
            'is_available' => false,
            'slot_type' => 'blocked'
        ]);
    }

    public function unblock()
    {
        $this->update([
            'is_available' => true,
            'slot_type' => 'regular'
        ]);
    }

    // Helper method to check if slot is active
    public function isActiveSlot()
    {
        return $this->is_available && $this->date >= today();
    }
}
