<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ledger extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'business_id',
        'current_balance',
        'balance_type',
        'contact',
        'location',
        'status',
        'ledger_type',
        'opening_balance',
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
        'opening_balance' => 'decimal:2',
    ];

    /**
     * Get the business that owns the ledger.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the transaction lines for the ledger.
     */
    public function transactionLines(): HasMany
    {
        return $this->hasMany(TransactionLine::class);
    }

    /**
     * Scope to filter ledgers by business.
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Scope to filter active ledgers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the ledger types for HMS.
     */
    public static function getLedgerTypes()
    {
        return [
            'Bank Accounts' => 'Bank Accounts',
            'Cash-in-Hand' => 'Cash-in-Hand',
            'Expenses' => 'Expenses',
            'Fixed Assets' => 'Fixed Assets',
            'Investments' => 'Investments',
            'Loans & Advances (Asset)' => 'Loans & Advances (Asset)',
            'Purchase Accounts' => 'Purchase Accounts',
            'Sundry Debtors (Customer)' => 'Sundry Debtors (Customer)',
            'Sales Accounts' => 'Sales Accounts',
            'Capital Accounts' => 'Capital Accounts',
            'Duties & Taxes' => 'Duties & Taxes',
            'Loans A/c' => 'Loans A/c',
            'Sundry Creditors (Supplier)' => 'Sundry Creditors (Supplier)',
            'Bank OD A/c' => 'Bank OD A/c',
            'Incomes' => 'Incomes',
            'commission agent' => 'commission agent',
        ];
    }

    /**
     * Get balance type options (using your enum values)
     */
    public static function getBalanceTypes()
    {
        return [
            'Dr' => 'Debit',
            'Cr' => 'Credit',
        ];
    }

    /**
     * Get the default balance type for a ledger type
     */
    public static function getDefaultBalanceType($ledgerType)
    {
        $creditTypes = [
            'Sales Accounts',
            'Capital Accounts',
            'Loans A/c',
            'Sundry Creditors (Supplier)',
            'Bank OD A/c',
            'Incomes',
        ];

        return in_array($ledgerType, $creditTypes) ? 'Cr' : 'Dr';
    }

    /**
     * Check if this ledger type is normally a debit balance
     */
    public function isDebitType()
    {
        $debitTypes = [
            'Bank Accounts',
            'Cash-in-Hand',
            'Expenses',
            'Fixed Assets',
            'Investments',
            'Loans & Advances (Asset)',
            'Purchase Accounts',
            'Sundry Debtors (Customer)',
            'commission agent',
        ];

        return in_array($this->ledger_type, $debitTypes);
    }

    /**
     * Check if this ledger type is normally a credit balance
     */
    public function isCreditType()
    {
        return !$this->isDebitType();
    }
}
