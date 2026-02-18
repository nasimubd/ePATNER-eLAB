<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTestMedicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'lab_test_id',
        'medicine_id',
        'quantity_required',
        'usage_instructions'
    ];

    /**
     * Get the lab test
     */
    public function labTest()
    {
        return $this->belongsTo(LabTest::class);
    }

    /**
     * Get the medicine
     */
    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
