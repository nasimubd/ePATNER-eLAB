<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'lab_test_id',
        'template_name',
        'description',
        'is_default',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function labTest()
    {
        return $this->belongsTo(LabTest::class);
    }

    public function sections()
    {
        return $this->hasMany(TemplateSection::class, 'template_id')->orderBy('section_order');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reports()
    {
        return $this->hasMany(LabReport::class, 'template_id');
    }

    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTest($query, $testId)
    {
        return $query->where('lab_test_id', $testId);
    }
}
