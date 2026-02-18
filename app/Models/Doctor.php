<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'name',
        'email',
        'phone',
        'specialization',
        'license_number',
        'qualifications',
        'experience_years',
        'address',
        'date_of_birth',
        'gender',
        'consultation_fee',
        'available_days',
        'start_time',
        'end_time',
        'bio',
        'profile_image',
        'is_active'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'consultation_fee' => 'decimal:2',
        'available_days' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Existing relationships
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * NEW: Appointment-related relationships
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function timeSlots()
    {
        return $this->hasMany(DoctorTimeSlot::class);
    }

    public function scheduleExceptions()
    {
        return $this->hasMany(DoctorScheduleException::class);
    }

    public function waitingListEntries()
    {
        return $this->hasMany(WaitingList::class);
    }

    /**
     * Existing scopes
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->orWhere('specialization', 'LIKE', "%{$search}%")
                ->orWhere('license_number', 'LIKE', "%{$search}%");
        });
    }

    /**
     * NEW: Appointment-related scopes
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithTodayAppointments($query)
    {
        return $query->with(['appointments' => function ($q) {
            $q->whereDate('appointment_date', today())
                ->orderBy('appointment_time');
        }]);
    }

    /**
     * Existing accessors
     */
    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image && Storage::exists($this->profile_image)) {
            return Storage::url($this->profile_image);
        }

        $gender = $this->gender === 'female' ? 'female' : 'male';
        return asset("images/avatars/default-{$gender}.png");
    }

    public function getAgeAttribute()
    {
        if ($this->date_of_birth) {
            return $this->date_of_birth->age;
        }
        return null;
    }

    public function getFormattedAvailableDaysAttribute()
    {
        if (!$this->available_days) {
            return 'Not specified';
        }

        $days = [
            'monday' => 'Mon',
            'tuesday' => 'Tue',
            'wednesday' => 'Wed',
            'thursday' => 'Thu',
            'friday' => 'Fri',
            'saturday' => 'Sat',
            'sunday' => 'Sun'
        ];

        return collect($this->available_days)
            ->map(fn($day) => $days[$day] ?? $day)
            ->implode(', ');
    }

    /**
     * NEW: Appointment-related accessors
     */
    public function getTodayAppointmentsCountAttribute()
    {
        return $this->appointments()
            ->whereDate('appointment_date', today())
            ->where('status', '!=', 'cancelled')
            ->count();
    }

    public function getUpcomingAppointmentsCountAttribute()
    {
        return $this->appointments()
            ->where('appointment_date', '>=', today())
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->count();
    }

    public function getCompletedAppointmentsCountAttribute()
    {
        return $this->appointments()
            ->where('status', 'completed')
            ->count();
    }

    public function getWaitingListCountAttribute()
    {
        return $this->waitingListEntries()
            ->where('status', 'waiting')
            ->count();
    }

    /**
     * NEW: Appointment-related methods
     */
    public function getTodayAppointments()
    {
        return $this->appointments()
            ->with('patient')
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->get();
    }

    public function getUpcomingAppointments($limit = 5)
    {
        return $this->appointments()
            ->with('patient')
            ->where('appointment_date', '>=', today())
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit($limit)
            ->get();
    }

    public function isAvailableOn($date, $time = null)
    {
        $date = Carbon::parse($date);
        $dayOfWeek = strtolower($date->format('l'));

        // Check if doctor works on this day
        if (!in_array($dayOfWeek, $this->available_days ?? [])) {
            return false;
        }

        // Check schedule exceptions
        $exception = $this->scheduleExceptions()
            ->where('date', $date->format('Y-m-d'))
            ->first();

        if ($exception) {
            if (!$exception->is_available) {
                // Doctor is unavailable
                if ($time && !$exception->isFullDayException()) {
                    return !$exception->affectsTime($time);
                }
                return false;
            }
        }

        // If time is specified, check if it's within working hours
        if ($time) {
            $timeString = is_string($time) ? $time : $time->format('H:i');
            return $timeString >= $this->start_time && $timeString <= $this->end_time;
        }

        return true;
    }

    public function getAvailableSlotsForDate($date, $slotDuration = 30)
    {
        if (!$this->isAvailableOn($date)) {
            return collect();
        }

        $date = Carbon::parse($date);
        $slots = collect();

        $startTime = Carbon::parse($this->start_time);
        $endTime = Carbon::parse($this->end_time);

        // Generate time slots
        while ($startTime->lt($endTime)) {
            $timeSlot = $startTime->format('H:i');

            // Check if this specific time slot is available
            if ($this->isAvailableOn($date, $timeSlot)) {
                // Check if slot is already booked
                $isBooked = $this->appointments()
                    ->whereDate('appointment_date', $date)
                    ->whereTime('appointment_time', $timeSlot)
                    ->whereNotIn('status', ['cancelled', 'no_show'])
                    ->exists();

                if (!$isBooked) {
                    $slots->push([
                        'time' => $timeSlot,
                        'formatted_time' => $startTime->format('h:i A'),
                        'available' => true
                    ]);
                }
            }

            $startTime->addMinutes($slotDuration);
        }

        return $slots;
    }

    public function hasAppointmentAt($date, $time)
    {
        return $this->appointments()
            ->whereDate('appointment_date', $date)
            ->whereTime('appointment_time', $time)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->exists();
    }

    public function getNextAvailableSlot($fromDate = null, $slotDuration = 30)
    {
        $fromDate = $fromDate ? Carbon::parse($fromDate) : today();

        // Check next 30 days
        for ($i = 0; $i < 30; $i++) {
            $checkDate = $fromDate->copy()->addDays($i);
            $slots = $this->getAvailableSlotsForDate($checkDate, $slotDuration);

            if ($slots->isNotEmpty()) {
                return [
                    'date' => $checkDate->format('Y-m-d'),
                    'time' => $slots->first()['time'],
                    'formatted_date' => $checkDate->format('M d, Y'),
                    'formatted_time' => $slots->first()['formatted_time']
                ];
            }
        }

        return null;
    }

    public function generateTimeSlots($startDate, $endDate, $slotDuration = 30)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        $slotsCreated = 0;

        while ($startDate->lte($endDate)) {
            if ($this->isAvailableOn($startDate)) {
                $daySlots = $this->getAvailableSlotsForDate($startDate, $slotDuration);

                foreach ($daySlots as $slot) {
                    DoctorTimeSlot::updateOrCreate([
                        'doctor_id' => $this->id,
                        'date' => $startDate->format('Y-m-d'),
                        'time_slot' => $slot['time']
                    ], [
                        'is_available' => true,
                        'is_booked' => false,
                        'slot_type' => 'regular',
                        'max_appointments' => 1,
                        'current_appointments' => 0
                    ]);

                    $slotsCreated++;
                }
            }

            $startDate->addDay();
        }

        return $slotsCreated;
    }

    /**
     * Get doctor's statistics
     */
    public function getStatistics($period = 'month')
    {
        $startDate = match ($period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth()
        };

        return [
            'total_appointments' => $this->appointments()
                ->where('appointment_date', '>=', $startDate)
                ->count(),
            'completed_appointments' => $this->appointments()
                ->where('appointment_date', '>=', $startDate)
                ->where('status', 'completed')
                ->count(),
            'cancelled_appointments' => $this->appointments()
                ->where('appointment_date', '>=', $startDate)
                ->where('status', 'cancelled')
                ->count(),
            'no_show_appointments' => $this->appointments()
                ->where('appointment_date', '>=', $startDate)
                ->where('status', 'no_show')
                ->count(),
            'revenue' => $this->appointments()
                ->where('appointment_date', '>=', $startDate)
                ->where('status', 'completed')
                ->where('payment_status', 'paid')
                ->sum('consultation_fee'),
            'waiting_list_count' => $this->waitingListEntries()
                ->where('status', 'waiting')
                ->count()
        ];
    }
}
