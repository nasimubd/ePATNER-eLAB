<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CommonMedicine extends Model
{
    use HasFactory;

    protected $connection = 'medicine_db';
    protected $table = 'common_medicines';

    protected $fillable = [
        'medicine_id',
        'company_name',
        'dosage_form',
        'brand_name',
        'generic_name',
        'dosage_strength',
        'pack_info',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Auto-generate unique medicine ID on creation
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($medicine) {
            if (empty($medicine->medicine_id)) {
                $medicine->medicine_id = self::generateUniqueMedicineId();
            }
        });
    }

    /**
     * Generate unique medicine ID in format: MED-YYYY-NNNNNN
     */
    public static function generateUniqueMedicineId(): string
    {
        $year = date('Y');
        $prefix = "MED-{$year}-";

        // Get the last medicine ID for current year
        $lastMedicine = self::where('medicine_id', 'LIKE', $prefix . '%')
            ->orderBy('medicine_id', 'desc')
            ->first();

        if ($lastMedicine) {
            // Extract the number part and increment
            $lastNumber = (int) substr($lastMedicine->medicine_id, -6);
            $newNumber = $lastNumber + 1;
        } else {
            // First medicine of the year
            $newNumber = 1;
        }

        // Format with leading zeros (6 digits)
        $formattedNumber = str_pad($newNumber, 6, '0', STR_PAD_LEFT);

        return $prefix . $formattedNumber;
    }

    /**
     * Find medicine by unique ID
     */
    public static function findByMedicineId(string $medicineId): ?self
    {
        return self::where('medicine_id', $medicineId)->first();
    }

    // Lightning-fast search scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearchByMedicineId(Builder $query, string $term): Builder
    {
        return $query->where('medicine_id', 'LIKE', "%{$term}%");
    }

    public function scopeSearchByBrand(Builder $query, string $term): Builder
    {
        return $query->where('brand_name', 'LIKE', "%{$term}%");
    }

    public function scopeSearchByGeneric(Builder $query, string $term): Builder
    {
        return $query->where('generic_name', 'LIKE', "%{$term}%");
    }

    public function scopeSearchByCompany(Builder $query, string $term): Builder
    {
        return $query->where('company_name', 'LIKE', "%{$term}%");
    }

    public function scopeQuickSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('medicine_id', 'LIKE', "%{$term}%")
                ->orWhere('brand_name', 'LIKE', "%{$term}%")
                ->orWhere('generic_name', 'LIKE', "%{$term}%")
                ->orWhere('company_name', 'LIKE', "%{$term}%");
        });
    }

    // Full-text search for advanced queries
    public function scopeFullTextSearch(Builder $query, string $term): Builder
    {
        return $query->whereRaw(
            "MATCH(brand_name, generic_name, company_name) AGAINST(? IN NATURAL LANGUAGE MODE)",
            [$term]
        );
    }

    // Optimized search with pagination
    public static function fastSearch(string $term, int $perPage = 20)
    {
        return self::active()
            ->quickSearch($term)
            ->select(['id', 'medicine_id', 'brand_name', 'generic_name', 'company_name', 'dosage_form', 'dosage_strength', 'pack_info'])
            ->orderBy('brand_name')
            ->paginate($perPage);
    }

    /**
     * Get route key name for model binding
     */
    public function getRouteKeyName(): string
    {
        return 'medicine_id';
    }
}
