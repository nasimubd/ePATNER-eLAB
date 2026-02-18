<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabReportSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'lab_report_id',
        'template_section_id',
        'section_name',
        'section_description',
        'section_order'
    ];

    public function labReport()
    {
        return $this->belongsTo(LabReport::class);
    }

    public function templateSection()
    {
        return $this->belongsTo(TemplateSection::class, 'template_section_id');
    }

    public function fields()
    {
        return $this->hasMany(LabReportField::class, 'report_section_id')->orderBy('field_order');
    }
}
