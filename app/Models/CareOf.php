<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareOf extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone_number',
        'commission_rate',
        'commission_type',
        'fixed_commission_amount',
        'business_id',
        'ledger_id',
        'status',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'fixed_commission_amount' => 'decimal:2',
    ];

    /**
     * Get the business that owns the care of.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the associated ledger.
     */
    public function ledger(): BelongsTo
    {
        return $this->belongsTo(Ledger::class);
    }

    /**
     * Scope to filter care ofs by business.
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Scope to filter active care ofs.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Calculate commission amount based on invoice amount.
     */
    public function calculateCommission($invoiceAmount)
    {
        if ($this->commission_type === 'percentage') {
            return ($invoiceAmount * $this->commission_rate) / 100;
        } else {
            return $this->fixed_commission_amount ?? 0;
        }
    }

    /**
     * Get formatted commission display.
     */
    public function getCommissionDisplayAttribute()
    {
        if ($this->commission_type === 'percentage') {
            return $this->commission_rate . '%';
        } else {
            return '₹' . number_format($this->fixed_commission_amount, 2);
        }
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($careOf) {
            $careOf->createLedger();
        });

        static::updated(function ($careOf) {
            if ($careOf->ledger) {
                $careOf->updateLedger();
            }
        });
    }

    /**
     * Create a ledger for this care of.
     */
    public function createLedger()
    {
        $ledger = Ledger::create([
            'name' => $this->name,
            'business_id' => $this->business_id,
            'current_balance' => 0.00,
            'balance_type' => 'Cr',
            'contact' => $this->phone_number,
            'location' => $this->address,
            'status' => 'active',
            'ledger_type' => 'commission agent',
            'opening_balance' => 0.00,
        ]);

        $this->update(['ledger_id' => $ledger->id]);
    }

    /**
     * Update the associated ledger.
     */
    public function updateLedger()
    {
        $this->ledger->update([
            'name' => $this->name,
            'contact' => $this->phone_number,
            'location' => $this->address,
            'status' => $this->status,
        ]);
    }
}
