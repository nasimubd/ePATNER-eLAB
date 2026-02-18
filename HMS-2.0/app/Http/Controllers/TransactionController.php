<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Models\Ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Get business ID directly from authenticated user
        $user = Auth::user();
        $businessId = $user->business_id;

        if (!$businessId) {
            return redirect()->back()->with('error', 'Access denied. No business association found.');
        }

        $query = Transaction::with('transactionLines.ledger')
            ->where('business_id', $businessId);

        // Add search by transaction ID
        if ($request->filled('search')) {
            $query->where('id', 'like', "%{$request->search}%");
        }

        // Add transaction type filter
        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }

        // Add date filtering
        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        // If only date_from is provided, filter for that specific date
        if ($request->filled('date_from') && !$request->filled('date_to')) {
            $query->whereDate('transaction_date', $request->date_from);
        }

        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.transactions.index', compact('transactions'));
    }

    public function create()
    {
        // Get business ID directly from authenticated user
        $user = Auth::user();
        $businessId = $user->business_id;

        if (!$businessId) {
            return redirect()->back()->with('error', 'Access denied. No business association found.');
        }

        $ledgers = Ledger::where('business_id', $businessId)->get();
        return view('admin.transactions.create', compact('ledgers'));
    }

    public function store(Request $request)
    {
        // Get business ID directly from authenticated user
        $user = Auth::user();
        $businessId = $user->business_id;

        if (!$businessId) {
            return redirect()->back()->with('error', 'Access denied. No business association found.');
        }

        // Custom validation rules
        $rules = [
            'transaction_type'  => 'required|in:Payment,Receipt,Journal,Contra',
            'transaction_date'  => 'required|date',
            'narration'         => 'nullable|string|max:1000',
            'lines'             => 'required|array|min:2',
        ];

        // Dynamic validation for lines based on transaction type
        if ($request->transaction_type === 'Journal') {
            $rules['lines.*.ledger_id'] = 'required|integer|exists:ledgers,id';
            $rules['lines.*.debit_amount'] = 'nullable|numeric|min:0';
            $rules['lines.*.credit_amount'] = 'nullable|numeric|min:0';
        } else {
            $rules['lines.*.ledger_id'] = 'required|integer|exists:ledgers,id';
            $rules['lines.0.debit_amount'] = 'required|numeric|min:0.01';
            $rules['lines.1.credit_amount'] = 'required|numeric|min:0.01';
        }

        $data = $request->validate($rules);

        // Additional validation for journal entries
        if ($data['transaction_type'] === 'Journal') {
            $this->validateJournalEntries($data['lines']);
        }

        DB::beginTransaction();

        try {
            $transaction = Transaction::create([
                'business_id'      => $businessId,
                'transaction_type' => $data['transaction_type'],
                'transaction_date' => $data['transaction_date'],
                'amount'           => 0, // Initially set to 0, will be updated after processing lines
                'narration'        => $data['narration'] ?? null,
            ]);

            $totalDebit  = 0;
            $totalCredit = 0;
            $processedLines = 0;

            foreach ($data['lines'] as $lineData) {
                // Skip lines with both amounts as 0
                $debitAmount = floatval($lineData['debit_amount'] ?? 0);
                $creditAmount = floatval($lineData['credit_amount'] ?? 0);

                if ($debitAmount == 0 && $creditAmount == 0) {
                    continue;
                }

                // Validate ledger exists and belongs to business
                $ledger = Ledger::where('id', $lineData['ledger_id'])
                    ->where('business_id', $businessId)
                    ->first();

                if (!$ledger) {
                    throw new \Exception('Invalid ledger selected.');
                }

                $line = new TransactionLine();
                $line->transaction_id = $transaction->id;
                $line->ledger_id      = $lineData['ledger_id'];
                $line->debit_amount   = $debitAmount;
                $line->credit_amount  = $creditAmount;
                $line->narration = $data['narration'] ?? null;
                $line->save();

                $totalDebit  += $line->debit_amount;
                $totalCredit += $line->credit_amount;
                $processedLines++;

                // Update ledger balances
                $this->recalcLedgerBalance($ledger);
            }

            // Ensure we have at least 2 lines processed
            if ($processedLines < 2) {
                throw new \Exception('Transaction must have at least 2 valid lines.');
            }

            // Double-entry check
            if (abs($totalDebit - $totalCredit) > 0.01) {
                throw new \Exception('Total Debit (' . number_format($totalDebit, 2) . ') and Credit (' . number_format($totalCredit, 2) . ') do not match.');
            }

            // Calculate and update the transaction amount
            $transaction->amount = $totalDebit;
            $transaction->save();

            DB::commit();

            Log::info('Transaction created successfully', [
                'transaction_id' => $transaction->id,
                'type' => $transaction->transaction_type,
                'amount' => $transaction->amount,
                'lines_count' => $processedLines
            ]);

            return redirect()->route('admin.transactions.index')
                ->with('success', 'Transaction saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Transaction creation failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Validate journal entries to ensure proper debit/credit structure
     */
    private function validateJournalEntries($lines)
    {
        $hasDebit = false;
        $hasCredit = false;
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($lines as $line) {
            $debitAmount = floatval($line['debit_amount'] ?? 0);
            $creditAmount = floatval($line['credit_amount'] ?? 0);

            // Skip empty lines
            if ($debitAmount == 0 && $creditAmount == 0) {
                continue;
            }

            // Ensure a line doesn't have both debit and credit
            if ($debitAmount > 0 && $creditAmount > 0) {
                throw new \Exception('A transaction line cannot have both debit and credit amounts.');
            }

            if ($debitAmount > 0) {
                $hasDebit = true;
                $totalDebit += $debitAmount;
            }

            if ($creditAmount > 0) {
                $hasCredit = true;
                $totalCredit += $creditAmount;
            }
        }

        if (!$hasDebit || !$hasCredit) {
            throw new \Exception('Journal entries must have at least one debit line and one credit line.');
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            throw new \Exception('Total debit amount (' . number_format($totalDebit, 2) . ') must equal total credit amount (' . number_format($totalCredit, 2) . ').');
        }
    }

    public function show(Transaction $transaction)
    {
        // Check if transaction belongs to current user's business
        $user = Auth::user();
        if ($transaction->business_id !== $user->business_id) {
            abort(403);
        }

        $transaction->load('transactionLines.ledger');
        return view('admin.transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        // Get business ID directly from authenticated user
        $user = Auth::user();
        $businessId = $user->business_id;

        // Check if transaction belongs to current user's business
        if ($transaction->business_id !== $businessId) {
            abort(403);
        }

        $ledgers = Ledger::where('business_id', $businessId)->get();
        $transaction->load('transactionLines.ledger');

        return view('admin.transactions.edit', compact('transaction', 'ledgers'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        // Get business ID directly from authenticated user
        $user = Auth::user();
        $businessId = $user->business_id;

        // Check if transaction belongs to current user's business
        if ($transaction->business_id !== $businessId) {
            abort(403);
        }

        // Custom validation rules
        $rules = [
            'transaction_type' => 'required|in:Payment,Receipt,Journal,Contra',
            'transaction_date' => 'required|date',
            'narration' => 'nullable|string|max:1000',
            'lines' => 'required|array|min:2',
        ];

        // Dynamic validation for lines based on transaction type
        if ($request->transaction_type === 'Journal') {
            $rules['lines.*.ledger_id'] = 'required|integer|exists:ledgers,id';
            $rules['lines.*.debit_amount'] = 'nullable|numeric|min:0';
            $rules['lines.*.credit_amount'] = 'nullable|numeric|min:0';
        } else {
            $rules['lines.*.ledger_id'] = 'required|integer|exists:ledgers,id';
            $rules['lines.0.debit_amount'] = 'required|numeric|min:0.01';
            $rules['lines.1.credit_amount'] = 'required|numeric|min:0.01';
        }

        $data = $request->validate($rules);

        // Additional validation for journal entries
        if ($data['transaction_type'] === 'Journal') {
            $this->validateJournalEntries($data['lines']);
        }

        DB::beginTransaction();

        try {
            // Get affected ledger IDs before updating
            $originalLedgerIds = $transaction->transactionLines()
                ->pluck('ledger_id')
                ->unique()
                ->toArray();

            // Update main transaction (without amount for now)
            $transaction->update([
                'transaction_type' => $data['transaction_type'],
                'transaction_date' => $data['transaction_date'],
                'narration' => $data['narration'] ?? null,
            ]);

            // Delete existing lines
            $transaction->transactionLines()->delete();

            // Add new lines
            $totalDebit = 0;
            $totalCredit = 0;
            $newLedgerIds = [];
            $processedLines = 0;

            foreach ($data['lines'] as $lineData) {
                // Skip lines with both amounts as 0
                $debitAmount = floatval($lineData['debit_amount'] ?? 0);
                $creditAmount = floatval($lineData['credit_amount'] ?? 0);

                if ($debitAmount == 0 && $creditAmount == 0) {
                    continue;
                }

                // Validate ledger exists and belongs to business
                $ledger = Ledger::where('id', $lineData['ledger_id'])
                    ->where('business_id', $businessId)
                    ->first();

                if (!$ledger) {
                    throw new \Exception('Invalid ledger selected.');
                }

                $line = new TransactionLine([
                    'transaction_id' => $transaction->id,
                    'ledger_id' => $lineData['ledger_id'],
                    'debit_amount' => $debitAmount,
                    'credit_amount' => $creditAmount,
                    'narration' => $data['narration'] ?? null,
                ]);
                $line->save();

                $totalDebit += $line->debit_amount;
                $totalCredit += $line->credit_amount;
                $newLedgerIds[] = $line->ledger_id;
                $processedLines++;
            }

            // Ensure we have at least 2 lines processed
            if ($processedLines < 2) {
                throw new \Exception('Transaction must have at least 2 valid lines.');
            }

            // Double-entry validation
            if (abs($totalDebit - $totalCredit) > 0.01) {
                throw new \Exception('Total Debit (' . number_format($totalDebit, 2) . ') and Credit (' . number_format($totalCredit, 2) . ') amounts must be equal');
            }

            // Update the transaction amount based on the sum of transaction lines
            $transaction->amount = $totalDebit;
            $transaction->save();

            // Recalculate balances for all affected ledgers (original + new)
            $allAffectedLedgerIds = array_unique(array_merge($originalLedgerIds, $newLedgerIds));
            foreach ($allAffectedLedgerIds as $ledgerId) {
                $ledger = Ledger::find($ledgerId);
                if ($ledger) {
                    $this->recalcLedgerBalance($ledger);
                }
            }

            DB::commit();

            Log::info('Transaction updated successfully', [
                'transaction_id' => $transaction->id,
                'type' => $transaction->transaction_type,
                'amount' => $transaction->amount,
                'lines_count' => $processedLines
            ]);

            return redirect()->route('admin.transactions.index')
                ->with('success', 'Transaction updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Transaction update failed', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(Transaction $transaction)
    {
        // Check if transaction belongs to current user's business
        $user = Auth::user();
        if ($transaction->business_id !== $user->business_id) {
            abort(403);
        }

        DB::beginTransaction();

        try {

            // Get all affected ledger IDs before deleting the transaction
            $affectedLedgerIds = $transaction->transactionLines()
                ->pluck('ledger_id')
                ->unique()
                ->toArray();

            // Delete the transaction (this will cascade to transaction lines)
            $transaction->delete();

            // Recalculate balances for all affected ledgers
            foreach ($affectedLedgerIds as $ledgerId) {
                $ledger = Ledger::find($ledgerId);
                if ($ledger) {
                    $this->recalcLedgerBalance($ledger);
                }
            }

            DB::commit();

            Log::info('Transaction deleted successfully', [
                'transaction_id' => $transaction->id,
                'affected_ledgers' => count($affectedLedgerIds)
            ]);

            return redirect()->route('admin.transactions.index')
                ->with('success', 'Transaction deleted successfully and ledger balances updated!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Transaction deletion failed', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('admin.transactions.index')
                ->with('error', 'Failed to delete transaction: ' . $e->getMessage());
        }
    }

    public function print(Transaction $transaction)
    {
        // Check if transaction belongs to current user's business
        $user = Auth::user();
        if ($transaction->business_id !== $user->business_id) {
            abort(403);
        }

        try {
            // Load transaction relationships
            $transaction->load('transactionLines.ledger');

            // Get business/hospital information using the same approach as medical invoice
            $hospital = null;

            // First try to get business from transaction
            if ($transaction->business) {
                $hospital = (object) [
                    'name' => $transaction->business->hospital_name ?? $transaction->business->name,
                    'address' => $transaction->business->address ?? 'Business Address',
                    'phone' => $transaction->business->contact_number ?? $transaction->business->phone,
                    'emergency_contact' => $transaction->business->emergency_contact ?? null,
                    'email' => $transaction->business->email ?? null,
                ];
            }
            // Then try to get from authenticated user's business
            elseif ($user->business) {
                $hospital = (object) [
                    'name' => $user->business->hospital_name ?? $user->business->name,
                    'address' => $user->business->address ?? 'Business Address',
                    'phone' => $user->business->contact_number ?? $user->business->phone,
                    'emergency_contact' => $user->business->emergency_contact ?? null,
                    'email' => $user->business->email ?? null,
                ];
            }
            // Finally, try to get any active business
            else {
                $hospital = $this->getDefaultBusinessInfo();
            }

            return view('admin.transactions.print', compact('transaction', 'hospital'));
        } catch (\Exception $e) {
            // Log the error and return with minimal data
            Log::error('Error printing transaction: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'error' => $e->getTraceAsString()
            ]);

            // Get fallback business info
            $hospital = $this->getDefaultBusinessInfo();

            return view('admin.transactions.print', compact('transaction', 'hospital'));
        }
    }

    public function printA5(Transaction $transaction)
    {
        // Check if transaction belongs to current user's business
        $user = Auth::user();
        if ($transaction->business_id !== $user->business_id) {
            abort(403);
        }

        // Check if A5 printing is enabled for the business
        if (!$user->business || !$user->business->enable_a5_printing) {
            return redirect()->back()->with('error', 'A5 printing is not enabled for your business.');
        }

        try {
            // Load transaction relationships
            $transaction->load('transactionLines.ledger');

            // Get business/hospital information using the same approach as medical invoice
            $hospital = null;

            // First try to get business from transaction
            if ($transaction->business) {
                $hospital = (object) [
                    'name' => $transaction->business->hospital_name ?? $transaction->business->name,
                    'address' => $transaction->business->address ?? 'Business Address',
                    'phone' => $transaction->business->contact_number ?? $transaction->business->phone,
                    'emergency_contact' => $transaction->business->emergency_contact ?? null,
                    'email' => $transaction->business->email ?? null,
                ];
            }
            // Then try to get from authenticated user's business
            elseif ($user->business) {
                $hospital = (object) [
                    'name' => $user->business->hospital_name ?? $user->business->name,
                    'address' => $user->business->address ?? 'Business Address',
                    'phone' => $user->business->contact_number ?? $user->business->phone,
                    'emergency_contact' => $user->business->emergency_contact ?? null,
                    'email' => $user->business->email ?? null,
                ];
            }
            // Finally, try to get any active business
            else {
                $hospital = $this->getDefaultBusinessInfo();
            }

            return view('admin.transactions.print-a5', compact('transaction', 'hospital'));
        } catch (\Exception $e) {
            // Log the error and return with minimal data
            Log::error('Error printing A5 transaction: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'error' => $e->getTraceAsString()
            ]);

            // Get fallback business info
            $hospital = $this->getDefaultBusinessInfo();

            return view('admin.transactions.print-a5', compact('transaction', 'hospital'));
        }
    }

    /**
     * Get default business information for fallback
     */
    private function getDefaultBusinessInfo()
    {
        // Try to get any active business first
        $activeBusiness = \App\Models\Business::where('is_active', true)->first();
        if ($activeBusiness) {
            return (object) [
                'name' => $activeBusiness->hospital_name ?? $activeBusiness->name,
                'address' => $activeBusiness->address,
                'phone' => $activeBusiness->contact_number ?? $activeBusiness->phone,
                'emergency_contact' => $activeBusiness->emergency_contact ?? null,
                'email' => $activeBusiness->email,
            ];
        }

        // Absolute fallback to config values
        return (object) [
            'name' => config('app.hospital_name', config('app.name', 'Business Center')),
            'address' => config('app.hospital_address', 'Business Address'),
            'phone' => config('app.hospital_phone', '+880-XXXXXXXXX'),
            'emergency_contact' => config('app.hospital_emergency', null),
            'email' => config('app.hospital_email', 'info@business.com'),
        ];
    }


    /**
     * Recalculate and update the given Ledger's current_balance
     * by combining its opening_balance plus/minus all debits/credits
     * from its transaction lines.
     */
    private function recalcLedgerBalance(Ledger $ledger): void
    {
        $drLedgers = [
            'Bank Accounts',
            'Cash-in-Hand',
            'Expenses',
            'Fixed Assets',
            'Investments',
            'Loans & Advances (Asset)',
            'Purchase Accounts',
            'Sundry Debtors (Customer)'
        ];

        // Start with opening balance
        $currentBalance = $ledger->opening_balance ?? 0;

        // Get all transaction lines for this ledger
        $transactionLines = TransactionLine::where('ledger_id', $ledger->id)->get();

        // Calculate running balance based on transaction lines
        foreach ($transactionLines as $line) {
            if (in_array($ledger->ledger_type, $drLedgers)) {
                $currentBalance += $line->debit_amount;
                $currentBalance -= $line->credit_amount;
            } else {
                $currentBalance -= $line->debit_amount;
                $currentBalance += $line->credit_amount;
            }
        }

        $ledger->current_balance = $currentBalance;
        $ledger->save();

        Log::debug('Ledger balance recalculated', [
            'ledger_id' => $ledger->id,
            'ledger_name' => $ledger->name,
            'opening_balance' => $ledger->opening_balance,
            'current_balance' => $currentBalance
        ]);
    }
}
