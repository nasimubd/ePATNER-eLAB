<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'business_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'marital_status',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'medical_history',
        'allergies',
        'current_medications',
        'insurance_provider',
        'insurance_number',
        'occupation',
        'national_id',
        'notes',
        'profile_image',
        'is_active'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
    ];

    protected $appends = ['age', 'profile_image_url', 'full_name'];

    /**
     * Boot method to generate patient ID
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($patient) {
            if (empty($patient->patient_id)) {
                $patient->patient_id = static::generatePatientId($patient->business_id);
            }
        });
    }

    /**
     * Generate unique patient ID based on business name
     */
    public static function generatePatientId($businessId)
    {
        $business = Business::find($businessId);

        if (!$business) {
            throw new \Exception('Business not found');
        }

        // Extract initials from business name
        $words = explode(' ', $business->hospital_name);
        $initials = '';

        foreach ($words as $word) {
            $initials .= strtoupper(substr(trim($word), 0, 1));
        }

        // Get the next sequence number for this business
        $lastPatient = static::where('business_id', $businessId)
            ->where('patient_id', 'like', $initials . '-%')
            ->orderBy('patient_id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastPatient) {
            $lastNumber = (int) substr($lastPatient->patient_id, strlen($initials) + 1);
            $nextNumber = $lastNumber + 1;
        }

        return $initials . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * RELATIONSHIPS
     */

    /**
     * Get the business that owns the patient
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get all appointments for this patient
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id', 'id');
    }

    /**
     * Get recent appointments for this patient
     */
    public function recentAppointments($limit = 5)
    {
        return $this->appointments()
            ->with('doctor')
            ->latest('appointment_date')
            ->latest('appointment_time')
            ->limit($limit);
    }

    /**
     * Get upcoming appointments for this patient
     */
    public function upcomingAppointments()
    {
        return $this->appointments()
            ->with('doctor')
            ->where('appointment_date', '>=', today())
            ->whereNotIn('status', ['completed', 'cancelled', 'no_show'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time');
    }

    /**
     * Get completed appointments for this patient
     */
    public function completedAppointments()
    {
        return $this->appointments()
            ->with('doctor')
            ->where('status', 'completed')
            ->latest('appointment_date')
            ->latest('appointment_time');
    }

    /**
     * ACCESSORS
     */

    /**
     * Get patient's age
     */
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Get patient's full name
     */
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get profile image URL
     */
    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image && Storage::exists($this->profile_image)) {
            return Storage::url($this->profile_image);
        }

        // Return default avatar based on gender
        $gender = $this->gender === 'female' ? 'female' : 'male';
        return asset("images/avatars/default-{$gender}.png");
    }

    /**
     * Get patient's initials
     */
    public function getInitialsAttribute()
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    /**
     * Get formatted address
     */
    public function getFormattedAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]);

        return implode(', ', $parts);
    }

    /**
     * SCOPES
     */

    /**
     * Scope for business patients
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Scope for active patients
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('patient_id', 'like', "%{$search}%")
                ->orWhere('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$search}%"]);
        });
    }

    /**
     * Scope for gender filter
     */
    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }

    /**
     * Scope for blood group filter
     */
    public function scopeByBloodGroup($query, $bloodGroup)
    {
        return $query->where('blood_group', $bloodGroup);
    }

    /**
     * Scope for age range filter
     */
    public function scopeByAgeRange($query, $minAge, $maxAge)
    {
        $minDate = Carbon::now()->subYears($maxAge)->startOfYear();
        $maxDate = Carbon::now()->subYears($minAge)->endOfYear();

        return $query->whereBetween('date_of_birth', [$minDate, $maxDate]);
    }

    /**
     * Scope for search by patient details
     */
    public function scopeSearchForReports($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('patient_id', 'like', "%{$search}%")
                ->orWhere('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$search}%"]);
        });
    }

    /**
     * HELPER METHODS
     */

    /**
     * Check if patient has profile image
     */
    public function hasProfileImage()
    {
        return !empty($this->profile_image) && Storage::exists($this->profile_image);
    }

    /**
     * Get total number of appointments
     */
    public function getTotalAppointmentsAttribute()
    {
        return $this->appointments()->count();
    }

    /**
     * Get total completed appointments
     */
    public function getCompletedAppointmentsCountAttribute()
    {
        return $this->appointments()->where('status', 'completed')->count();
    }

    /**
     * Get total upcoming appointments
     */
    public function getUpcomingAppointmentsCountAttribute()
    {
        return $this->upcomingAppointments()->count();
    }

    /**
     * Get last appointment date
     */
    public function getLastAppointmentDateAttribute()
    {
        $lastAppointment = $this->appointments()
            ->latest('appointment_date')
            ->latest('appointment_time')
            ->first();

        return $lastAppointment ? $lastAppointment->appointment_date : null;
    }

    /**
     * Get next appointment date
     */
    public function getNextAppointmentDateAttribute()
    {
        $nextAppointment = $this->upcomingAppointments()->first();
        return $nextAppointment ? $nextAppointment->appointment_date : null;
    }

    /**
     * LAB REPORTS RELATIONSHIPS (if you have lab reports)
     */

    /**
     * Get lab reports for this patient
     */
    public function labReports()
    {
        return $this->hasMany(LabReport::class);
    }

    /**
     * Get recent lab reports
     */
    public function getRecentLabReportsAttribute()
    {
        return $this->labReports()
            ->with('labTest')
            ->latest()
            ->limit(5)
            ->get();
    }

    // Add these relationships to your existing Patient model

    /**
     * Get all bookings for this patient (both ward and OT)
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get ward bookings for this patient
     */
    public function wardBookings(): HasMany
    {
        return $this->hasMany(Booking::class)->where('booking_type', 'ward');
    }

    /**
     * Get OT bookings for this patient
     */
    public function otBookings(): HasMany
    {
        return $this->hasMany(Booking::class)->where('booking_type', 'ot');
    }

    /**
     * Get upcoming bookings for this patient
     */
    public function upcomingBookings()
    {
        return $this->bookings()
            ->where('booking_date', '>=', today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('booking_date')
            ->orderBy('booking_time');
    }

    /**
     * Get completed bookings for this patient
     */
    public function completedBookings()
    {
        return $this->bookings()
            ->where('status', 'completed')
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc');
    }
}
