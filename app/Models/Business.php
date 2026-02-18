<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_name',
        'address',
        'contact_number',
        'logo',
        'logo_mime_type',
        'is_active',
        'enable_a5_printing',
        'due_date',
        'custom_monthly_fee'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'enable_a5_printing' => 'boolean',
        'due_date' => 'date',
        'custom_monthly_fee' => 'decimal:2',
    ];

    /**
     * Get all users for this business.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all staff members for this business.
     */
    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }

    /**
     * Set the logo attribute with automatic compression and resizing
     */
    public function setLogoAttribute($value)
    {
        if ($value && is_file($value)) {
            // Create image instance and resize/compress
            $image = Image::make($value)
                ->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('jpg', 80); // Compress to 80% quality

            $this->attributes['logo'] = $image->getEncoded();
            $this->attributes['logo_mime_type'] = 'image/jpeg';
        }
    }

    /**
     * Get the medical invoices for this business.
     */
    public function medicalInvoices()
    {
        return $this->hasMany(MedicalInvoice::class);
    }

    /**
     * Get the logo as base64 encoded string for display
     */
    public function getLogoBase64Attribute()
    {
        if ($this->logo && $this->logo_mime_type) {
            return 'data:' . $this->logo_mime_type . ';base64,' . base64_encode($this->logo);
        }
        return null;
    }

    /**
     * Check if business has logo
     */
    public function hasLogo()
    {
        return !empty($this->logo);
    }


    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get payments for this business
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Check if business subscription is active
     */
    public function isSubscriptionActive(): bool
    {
        return $this->due_date && ($this->due_date->isFuture() || $this->due_date->isToday());
    }

    /**
     * Check if business subscription is expired
     */
    public function isSubscriptionExpired(): bool
    {
        return !$this->isSubscriptionActive();
    }

    /**
     * Get the monthly fee for this business (custom or default)
     */
    public function getMonthlyFee(): float
    {
        return $this->custom_monthly_fee ?? Setting::getMonthlyFee();
    }

    /**
     * Calculate total amount for given months
     */
    public function calculatePaymentAmount(int $months): float
    {
        return $this->getMonthlyFee() * $months;
    }

    /**
     * Extend subscription by months
     */
    public function extendSubscription(int $months): void
    {
        $currentDueDate = $this->due_date ?? now();
        $this->due_date = $currentDueDate->addMonths($months);
        $this->is_active = true;
        $this->save();
    }

    /**
     * Mark business as inactive (subscription expired)
     */
    public function deactivateSubscription(): void
    {
        $this->is_active = false;
        $this->save();
    }

    /**
     * Get pending payments
     */
    public function pendingPayments()
    {
        return $this->payments()->pending();
    }

    /**
     * Get letterheads for this business
     */
    public function letterheads()
    {
        return $this->hasMany(\App\Models\Letterhead::class);
    }

    /**
     * Get active letterhead for a specific type
     */
    public function getActiveLetterhead($type)
    {
        return $this->letterheads()->where('type', $type)->where('status', 'Active')->first();
    }
}
