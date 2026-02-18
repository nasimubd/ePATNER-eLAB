<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'user_id',
        'request_type',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'rejected_reason',
        'allowed_prints',
        'prints_used',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * Get the invoice that owns the print request.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(MedicalInvoice::class, 'invoice_id');
    }

    /**
     * Get the user that owns the print request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the approver user.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by request type.
     */
    public function scopeByRequestType($query, $type)
    {
        return $query->where('request_type', $type);
    }
}
