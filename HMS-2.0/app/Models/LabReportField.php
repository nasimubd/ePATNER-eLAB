<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabReportField extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_section_id',
        'template_field_id',
        'field_name',
        'field_label',
        'field_value',
        'unit',
        'normal_range',
        'is_abnormal',
        'field_order'
    ];

    protected $casts = [
        'is_abnormal' => 'boolean',
    ];

    public function reportSection()
    {
        return $this->belongsTo(LabReportSection::class, 'report_section_id');
    }

    public function templateField()
    {
        return $this->belongsTo(TemplateField::class, 'template_field_id');
    }

    public function getFormattedValueAttribute()
    {
        $value = $this->field_value;
        if ($this->unit) {
            $value .= ' ' . $this->unit;
        }
        return $value;
    }
}
