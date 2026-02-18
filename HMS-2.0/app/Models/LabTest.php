<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'test_name',
        'test_code',
        'description',
        'price',
        'duration_minutes',
        'instructions',
        'preparation_instructions',
        'sample_type',
        'department',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the business that owns the lab test
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
    public function invoiceLines()
    {
        return $this->hasMany(MedicalInvoiceLine::class);
    }
    /**
     * Get the medicines associated with this lab test
     */
    public function medicines()
    {
        return $this->belongsToMany(Medicine::class, 'lab_test_medicines')
            ->withPivot('quantity_required', 'usage_instructions')
            ->withTimestamps();
    }

    /**
     * Get the report templates for this lab test
     */
    public function reportTemplates()
    {
        return $this->hasMany(ReportTemplate::class, 'lab_test_id');
    }

    /**
     * Get the lab test medicines pivot records
     */
    public function labTestMedicines()
    {
        return $this->hasMany(LabTestMedicine::class);
    }

    /**
     * Scope to filter by business
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Scope to search lab tests
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('test_name', 'LIKE', "%{$search}%")
                ->orWhere('test_code', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%")
                ->orWhere('department', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope to filter by department
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Scope to filter by sample type
     */
    public function scopeBySampleType($query, $sampleType)
    {
        return $query->where('sample_type', $sampleType);
    }

    /**
     * Scope to filter active tests
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if test has sufficient medicine stock
     */
    public function hasSufficientStock()
    {
        foreach ($this->medicines as $medicine) {
            if ($medicine->stock_quantity < $medicine->pivot->quantity_required) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get insufficient stock medicines
     */
    public function getInsufficientStockMedicines()
    {
        return $this->medicines->filter(function ($medicine) {
            return $medicine->stock_quantity < $medicine->pivot->quantity_required;
        });
    }

    /**
     * Calculate total medicine cost for this test
     */
    public function getTotalMedicineCostAttribute()
    {
        return $this->medicines->sum(function ($medicine) {
            return $medicine->unit_price * $medicine->pivot->quantity_required;
        });
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration_minutes) {
            return 'Not specified';
        }

        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . ($minutes > 0 ? $minutes . 'm' : '');
        }

        return $minutes . 'm';
    }
}
