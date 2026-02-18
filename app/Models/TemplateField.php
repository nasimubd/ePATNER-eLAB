<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateField extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'field_name',
        'field_label',
        'field_type',
        'field_options',
        'default_value',
        'unit',
        'normal_range',
        'is_required',
        'field_order'
    ];

    protected $casts = [
        'field_options' => 'array',
        'is_required' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(TemplateSection::class, 'section_id');
    }
}
