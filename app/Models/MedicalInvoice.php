<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'invoice_number',
        'lab_id',
        'patient_id',
        'care_of_id',
        'doctor_id',
        'invoice_date',
        'payment_method',
        'subtotal',
        'discount',
        'round_off',
        'grand_total',
        'paid_amount',
        'status',
        'notes',
        'payment_notes',
        'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    /**
     * Get the business that owns the medical invoice.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the patient that owns the medical invoice.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get all invoice lines for the medical invoice.
     */
    public function lines(): HasMany
    {
        return $this->hasMany(MedicalInvoiceLine::class);
    }

    /**
     * Get only visible invoice lines (excluding commission lines).
     */
    public function visibleLines(): HasMany
    {
        return $this->hasMany(MedicalInvoiceLine::class)->visibleInInvoice();
    }

    /**
     * Get only commission lines.
     */
    public function commissionLines(): HasMany
    {
        return $this->hasMany(MedicalInvoiceLine::class)->commissionOnly();
    }

    /**
     * Get the invoice lines with their related lab tests (excluding commission).
     */
    public function linesWithTests(): HasMany
    {
        return $this->hasMany(MedicalInvoiceLine::class)
            ->visibleInInvoice()
            ->with('labTest');
    }

    /**
     * Calculate the remaining amount to be paid.
     */
    public function getRemainingAmountAttribute(): float
    {
        return $this->grand_total - $this->paid_amount;
    }

    /**
     * Check if the invoice is fully paid.
     */
    public function getIsFullyPaidAttribute(): bool
    {
        return $this->remaining_amount <= 0;
    }

    /**
     * Check if the invoice has partial payment.
     */
    public function getHasPartialPaymentAttribute(): bool
    {
        return $this->paid_amount > 0 && $this->remaining_amount > 0;
    }

    /**
     * Get the payment status text.
     */
    public function getPaymentStatusAttribute(): string
    {
        if ($this->is_fully_paid) {
            return 'Fully Paid';
        } elseif ($this->has_partial_payment) {
            return 'Partial Payment';
        } else {
            return 'No Payment';
        }
    }

    /**
     * Get the subtotal excluding commission lines.
     */
    public function getDisplaySubtotalAttribute(): float
    {
        return $this->visibleLines()->sum('line_total');
    }

    /**
     * Get the total commission amount.
     */
    public function getCommissionAmountAttribute(): float
    {
        return $this->commissionLines()->sum('line_total');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by payment method.
     */
    public function scopeByPaymentMethod($query, $paymentMethod)
    {
        return $query->where('payment_method', $paymentMethod);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('invoice_date', [$startDate, $endDate]);
    }

    /**
     * Get the care of associated with the invoice.
     */
    public function careOf(): BelongsTo
    {
        return $this->belongsTo(CareOf::class, 'care_of_id');
    }

    /**
     * Scope to search by invoice number or patient name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('invoice_number', 'like', "%{$search}%")
                ->orWhereHas('patient', function ($patientQuery) use ($search) {
                    $patientQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('patient_id', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Scope to filter by business ID.
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Get the print requests for the invoice.
     */
    public function printRequests(): HasMany
    {
        return $this->hasMany(PrintRequest::class, 'invoice_id');
    }

    /**
     * Get the payment method options.
     */
    public static function getPaymentMethodOptions()
    {
        return [
            'cash' => 'Cash',
            'credit' => 'Credit',
        ];
    }

    /**
     * Get the status options.
     */
    public static function getStatusOptions()
    {
        return [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
        ];
    }

    /**
     * Get the doctor associated with the invoice.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    /**
     * Alias for createdBy relationship
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Helper method to get doctor from consultation appointments
     */
    public function getDoctorFromAppointments()
    {
        try {
            $consultationLine = $this->visibleLines()
                ->where('service_type', 'consultation')
                ->whereNotNull('appointment_id')
                ->first();

            if ($consultationLine && $consultationLine->appointment) {
                // Try to get doctor safely
                if (method_exists($consultationLine->appointment, 'doctor') && $consultationLine->appointment->doctor) {
                    return $consultationLine->appointment->doctor;
                }
            }
        } catch (\Exception $e) {
            // Ignore errors and return null
        }

        return null;
    }

    /**
     * Get hospital/business info safely
     */
    public function getHospitalInfo()
    {
        // First try direct business relationship
        if ($this->business) {
            return (object) [
                'name' => $this->business->hospital_name,
                'address' => $this->business->address,
                'phone' => $this->business->contact_number,
                'emergency_contact' => $this->business->emergency_contact ?? null,
                'email' => $this->business->email,
            ];
        }

        // Try to get business from created by user
        if ($this->createdBy && method_exists($this->createdBy, 'business') && $this->createdBy->business) {
            return (object) [
                'name' => $this->createdBy->business->hospital_name,
                'address' => $this->createdBy->business->address,
                'phone' => $this->createdBy->business->contact_number,
                'emergency_contact' => $this->createdBy->business->emergency_contact ?? null,
                'email' => $this->createdBy->business->email,
            ];
        }

        // Last resort - get any active business
        $activeBusiness = \App\Models\Business::where('is_active', true)->first();
        if ($activeBusiness) {
            return (object) [
                'name' => $activeBusiness->hospital_name,
                'address' => $activeBusiness->address,
                'phone' => $activeBusiness->contact_number,
                'emergency_contact' => $activeBusiness->emergency_contact ?? null,
                'email' => $activeBusiness->email,
            ];
        }

        // Absolute fallback
        return (object) [
            'name' => config('app.hospital_name', 'Medical Center'),
            'address' => config('app.hospital_address', 'Healthcare Address'),
            'phone' => config('app.hospital_phone', '+880-XXXXXXXXX'),
            'emergency_contact' => config('app.hospital_emergency', null),
            'email' => config('app.hospital_email', 'info@medicalcenter.com'),
        ];
    }
}
