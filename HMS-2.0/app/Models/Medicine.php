<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'generic_name',
        'brand_name',
        'manufacturer',
        'batch_number',
        'medicine_type',
        'strength',
        'description',
        'unit_price',
        'selling_price',
        'stock_quantity',
        'minimum_stock_level',
        'manufacturing_date',
        'expiry_date',
        'storage_conditions',
        'side_effects',
        'dosage_instructions',
        'prescription_required',
        'is_active',
        'barcode',
        'medicine_image'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'manufacturing_date' => 'date',
        'expiry_date' => 'date',
        'prescription_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the business that owns the medicine
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Scope to filter by business
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Scope to search medicines
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('generic_name', 'LIKE', "%{$search}%")
                ->orWhere('brand_name', 'LIKE', "%{$search}%")
                ->orWhere('manufacturer', 'LIKE', "%{$search}%")
                ->orWhere('batch_number', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope to filter active medicines
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by medicine type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('medicine_type', $type);
    }

    /**
     * Scope to get low stock medicines
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock_quantity <= minimum_stock_level');
    }

    /**
     * Scope to get expired medicines
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    /**
     * Scope to get medicines expiring soon (within 30 days)
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereBetween('expiry_date', [now(), now()->addDays($days)]);
    }

    /**
     * Get medicine image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->medicine_image && Storage::exists($this->medicine_image)) {
            return Storage::url($this->medicine_image);
        }
        return null;
    }

    /**
     * Check if medicine is expired
     */
    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if medicine is expiring soon
     */
    public function getIsExpiringSoonAttribute()
    {
        return $this->expiry_date && $this->expiry_date->isBefore(now()->addDays(30));
    }

    /**
     * Check if medicine is low in stock
     */
    public function getIsLowStockAttribute()
    {
        return $this->stock_quantity <= $this->minimum_stock_level;
    }

    /**
     * Get stock status
     */
    public function getStockStatusAttribute()
    {
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        } elseif ($this->is_low_stock) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    /**
     * Get profit margin
     */
    public function getProfitMarginAttribute()
    {
        if ($this->unit_price > 0) {
            return (($this->selling_price - $this->unit_price) / $this->unit_price) * 100;
        }
        return 0;
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiryAttribute()
    {
        if ($this->expiry_date) {
            return now()->diffInDays($this->expiry_date, false);
        }
        return null;
    }

    /**
     * Medicine types
     */
    public static function getMedicineTypes()
    {
        return [
            'tablet' => 'Tablet',
            'capsule' => 'Capsule',
            'syrup' => 'Syrup',
            'injection' => 'Injection',
            'drops' => 'Drops',
            'cream' => 'Cream',
            'ointment' => 'Ointment',
            'inhaler' => 'Inhaler',
            'powder' => 'Powder',
            'gel' => 'Gel',
            'lotion' => 'Lotion',
            'spray' => 'Spray',
            'other' => 'Other'
        ];
    }

    /**
     * Get the lab tests that use this medicine
     */
    public function labTests()
    {
        return $this->belongsToMany(LabTest::class, 'lab_test_medicines')
            ->withPivot('quantity_required', 'usage_instructions')
            ->withTimestamps();
    }
}
