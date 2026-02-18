<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'section_name',
        'section_description',
        'section_order',
        'is_required'
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function template()
    {
        return $this->belongsTo(ReportTemplate::class, 'template_id');
    }

    public function fields()
    {
        return $this->hasMany(TemplateField::class, 'section_id')->orderBy('field_order');
    }
}
