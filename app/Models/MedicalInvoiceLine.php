<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalInvoiceLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_invoice_id',
        'service_type',
        'service_name',
        'lab_test_id',
        'appointment_id',
        'booking_id',
        'quantity',
        'unit_price',
        'line_discount',
        'line_total',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'line_discount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    // Relationships
    public function medicalInvoice()
    {
        return $this->belongsTo(MedicalInvoice::class);
    }

    public function labTest()
    {
        return $this->belongsTo(LabTest::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Scope to exclude commission lines from display
    public function scopeVisibleInInvoice($query)
    {
        return $query->where('service_type', '!=', 'commission')
            ->orWhereNull('service_type');
    }

    // Scope to get only commission lines
    public function scopeCommissionOnly($query)
    {
        return $query->where('service_type', 'commission');
    }

    // Accessor to get the service display name
    public function getServiceDisplayNameAttribute()
    {
        switch ($this->service_type) {
            case 'lab_test':
                return $this->labTest ? $this->labTest->test_name : $this->service_name;
            case 'consultation':
                return $this->service_name;
            case 'booking':
                return $this->service_name;
            case 'commission':
                return $this->service_name; // Keep for backend processing
            default:
                return $this->service_name;
        }
    }

    // Accessor to get service details
    public function getServiceDetailsAttribute()
    {
        switch ($this->service_type) {
            case 'lab_test':
                return $this->labTest ? [
                    'test_code' => $this->labTest->test_code,
                    'department' => $this->labTest->department,
                    'sample_type' => $this->labTest->sample_type,
                ] : null;
            case 'consultation':
                return $this->appointment ? [
                    'appointment_date' => $this->appointment->appointment_date,
                    'appointment_time' => $this->appointment->appointment_time,
                    'doctor_name' => $this->getDoctorNameSafely(),
                ] : null;
            case 'booking':
                return $this->booking ? [
                    'booking_date' => $this->booking->booking_date,
                    'booking_time' => $this->booking->booking_time,
                    'booking_type' => $this->booking->booking_type,
                ] : null;
            default:
                return null;
        }
    }

    // Helper method to safely get doctor name
    private function getDoctorNameSafely()
    {
        try {
            if ($this->appointment && method_exists($this->appointment, 'doctor') && $this->appointment->doctor) {
                return $this->appointment->doctor->name;
            }
        } catch (\Exception $e) {
            // Ignore errors and return default
        }
        return 'Unknown Doctor';
    }
}
