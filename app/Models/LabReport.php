<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_number',
        'business_id',
        'lab_id',
        'patient_id',
        'lab_test_id',
        'template_id',
        'report_date',
        'care_of_id',
        'advised_by',
        'investigation_details',
        'technical_notes',
        'doctor_comments',
        'status',
        'verified_at',
        'verified_by',
        'created_by'
    ];

    protected $casts = [
        'report_date' => 'date',
        'verified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->report_number)) {
                // Load patient relationship if not already loaded
                if (!$model->relationLoaded('patient')) {
                    $model->load('patient');
                }
                $model->report_number = static::generateReportNumber($model->business_id, $model->patient, $model->lab_id);
            }
        });
    }

    public static function generateReportNumber($businessId, $patient = null, $labId = null)
    {
        // Get the last report number for this business to determine sequence
        $lastReport = static::where('business_id', $businessId)
            ->latest()
            ->first();

        $sequence = $lastReport ? ((int)substr($lastReport->report_number, -4) + 1) : 1;

        // Sanitize patient name (remove spaces and special characters)
        $patientName = $patient ? preg_replace('/[^A-Za-z0-9]/', '', $patient->first_name . $patient->last_name) : 'Unknown';
        $patientId = $patient ? $patient->patient_id : 'Unknown';
        $labId = $labId ?: 'Unknown';

        return $patientName . '_' . $patientId . '_' . $labId . '_' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function patient() // Changed from customer()
    {
        return $this->belongsTo(Patient::class);
    }

    public function labTest()
    {
        return $this->belongsTo(LabTest::class);
    }

    public function template()
    {
        return $this->belongsTo(ReportTemplate::class, 'template_id');
    }

    public function sections()
    {
        return $this->hasMany(LabReportSection::class)->orderBy('section_order');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForPatient($query, $patientId) // Changed from scopeForCustomer
    {
        return $query->where('patient_id', $patientId);
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'draft' => 'gray',
            'completed' => 'blue',
            'verified' => 'green',
            'delivered' => 'purple',
            default => 'gray'
        };
    }
}
