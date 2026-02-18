<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Letterhead extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_name_bangla',
        'business_name_english',
        'location',
        'contacts',
        'emails',
        'type',
        'status',
        'business_id',
    ];

    protected $casts = [
        'contacts' => 'array',
        'emails' => 'array',
        'status' => 'string',
        'type' => 'string',
    ];

    /**
     * Get the business that owns the letterhead.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Scope to get active letterheads.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope to get letterheads by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get active letterhead for a business and type.
     */
    public static function getActiveForBusiness($businessId, $type)
    {
        return static::where('business_id', $businessId)
            ->where('type', $type)
            ->where('status', 'Active')
            ->first();
    }

    /**
     * Check if this letterhead is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'Active';
    }

    /**
     * Get formatted contacts as array.
     */
    public function getContactsArray(): array
    {
        return $this->contacts ?? [];
    }

    /**
     * Get formatted emails as array.
     */
    public function getEmailsArray(): array
    {
        return $this->emails ?? [];
    }

    /**
     * Get primary contact (first one).
     */
    public function getPrimaryContact(): ?string
    {
        $contacts = $this->getContactsArray();
        return !empty($contacts) ? $contacts[0] : null;
    }

    /**
     * Get primary email (first one).
     */
    public function getPrimaryEmail(): ?string
    {
        $emails = $this->getEmailsArray();
        return !empty($emails) ? $emails[0] : null;
    }
}
