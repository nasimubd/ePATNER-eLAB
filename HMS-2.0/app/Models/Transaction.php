<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'transaction_type',
        'transaction_date',
        'amount',
        'narration',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the business that owns the transaction.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the transaction lines for the transaction.
     */
    public function transactionLines(): HasMany
    {
        return $this->hasMany(TransactionLine::class);
    }

    /**
     * Scope to filter transactions by business.
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Get the transaction type options.
     */
    public static function getTransactionTypes()
    {
        return [
            'Payment' => 'Payment',
            'Receipt' => 'Receipt',
            'Journal' => 'Journal',
            'Contra' => 'Contra',
        ];
    }
}
