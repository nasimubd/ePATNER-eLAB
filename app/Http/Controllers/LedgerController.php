<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Ledger;
use App\Models\TransactionLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LedgerController extends Controller
{
    /**
     * Display a listing of ledgers for the authenticated user's business.
     */
    public function index(Request $request)
    {
        $query = Ledger::query();

        // Get the authenticated user's business ID from users table
        $user = Auth::user();
        $businessId = $user->business_id;

        if (!$businessId) {
            return redirect()->back()->with('error', 'Access denied. No business association found.');
        }

        // Check if we need to refresh ledger balances
        if ($request->has('refresh_ledgers')) {
            // Get all ledgers for the current business
            $ledgers = Ledger::where('business_id', $businessId)->get();

            // Recalculate balance for each ledger
            foreach ($ledgers as $ledger) {
                $this->recalcLedgerBalance($ledger);
            }

            return redirect()->route('admin.ledgers.index')
                ->with('success', 'All ledger balances have been refreshed successfully!');
        }

        // Apply search filters if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Apply status filter if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply ledger type filter if provided
        if ($request->filled('type')) {
            $query->where('ledger_type', $request->type);
        }

        // Filter ledgers by business_id
        $query->where('business_id', $businessId);

        $ledgers = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.ledgers.index', compact('ledgers'));
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
        $transactionLines = \App\Models\TransactionLine::where('ledger_id', $ledger->id)->get();

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
    }

    /**
     * Show the form for creating a new ledger.
     */
    public function create()
    {
        return view('admin.ledgers.create');
    }

    /**
     * Store a newly created ledger in storage.
     */
    public function store(Request $request)
    {
        // Get business ID directly from authenticated user
        $user = Auth::user();
        $businessId = $user->business_id;

        if (!$businessId) {
            return redirect()->back()->with('error', 'Access denied. No business association found.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ledger_type' => 'required|string',
            'balance_type' => 'required|in:Dr,Cr',
            'contact' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,default',
            'opening_balance' => 'nullable|numeric|min:0',
        ]);

        $drLedgers = [
            'Bank Accounts',
            'Cash-in-Hand',
            'Stock-in-Hand',
            'Expenses',
            'Fixed Assets',
            'Investments',
            'Loans & Advances (Asset)',
            'Purchase Accounts',
            'Sundry Debtors (Customer)'
        ];

        // Automatically set balance_type based on ledger_type
        $validated['balance_type'] = in_array($validated['ledger_type'], $drLedgers) ? 'Dr' : 'Cr';
        $validated['business_id'] = $businessId;
        $validated['current_balance'] = $validated['opening_balance'] ?? 0;

        // Handle default status - only one ledger can be default per business
        if ($validated['status'] === 'default') {
            Ledger::where('business_id', $businessId)
                ->where('ledger_type', $validated['ledger_type'])
                ->where('status', 'default')
                ->update(['status' => 'active']);
        }

        $ledger = Ledger::create($validated);

        return redirect()->route('admin.ledgers.index')
            ->with('success', 'Ledger created successfully!');
    }


    public function show(Request $request, Ledger $ledger)
    {
        // Determine the month to display, default to current month
        $month = $request->query('month');
        $currentDate = $month ? Carbon::parse($month . '-01') : Carbon::now();

        $previousMonth = $currentDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentDate->copy()->addMonth()->format('Y-m');
        $currentMonthName = $currentDate->format('F');
        $currentYear = $currentDate->format('Y');

        // Get transaction lines for the ledger filtered by month if needed
        $transactionLines = $ledger->transactionLines()
            ->with('transaction')
            ->join('transactions', 'transaction_lines.transaction_id', '=', 'transactions.id')
            ->select('transaction_lines.*')
            ->whereYear('transactions.transaction_date', $currentDate->year)
            ->whereMonth('transactions.transaction_date', $currentDate->month)
            ->orderBy('transactions.transaction_date', 'desc')
            ->orderBy('transactions.id', 'desc')
            ->paginate(20);

        // Calculate totals (you may want to calculate totals for the filtered month or overall)
        $totalDebits = $ledger->transactionLines()->sum('debit_amount');
        $totalCredits = $ledger->transactionLines()->sum('credit_amount');

        // Prepare transactions data with running balance for the view
        $runningBalance = 0;
        $transactions = $transactionLines->map(function ($line) use (&$runningBalance, $ledger) {
            if ($ledger->balance_type === 'Dr') {
                $runningBalance += $line->debit_amount;
                $runningBalance -= $line->credit_amount;
            } else {
                $runningBalance -= $line->debit_amount;
                $runningBalance += $line->credit_amount;
            }

            return (object)[
                'date' => $line->transaction->transaction_date,
                'particulars' => $line->transaction->particulars ?? '',
                'debit' => $line->debit_amount,
                'credit' => $line->credit_amount,
                'balance' => $runningBalance,
            ];
        });

        return view('admin.ledgers.show', compact(
            'ledger',
            'transactionLines',
            'totalDebits',
            'totalCredits',
            'transactions',
            'previousMonth',
            'nextMonth',
            'currentMonthName',
            'currentYear'
        ));
    }

    /**
     * Show the form for editing the specified ledger.
     */
    public function edit(Ledger $ledger)
    {
        // Check if ledger belongs to current user's business
        if ($ledger->business_id !== $this->getBusinessId()) {
            abort(403);
        }

        return view('admin.ledgers.edit', compact('ledger'));
    }

    /**
     * Update the specified ledger in storage.
     */
    public function update(Request $request, Ledger $ledger)
    {
        $businessId = $this->getBusinessId();

        // Check if ledger belongs to current user's business
        if ($ledger->business_id !== $businessId) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'balance_type' => 'required|in:Dr,Cr',
            'contact' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,default',
        ]);

        // Handle default status
        if ($validated['status'] === 'default') {
            Ledger::where('business_id', $businessId)
                ->where('status', 'default')
                ->where('id', '!=', $ledger->id)
                ->update(['status' => 'active']);
        }

        $ledger->update($validated);

        return redirect()->route('admin.ledgers.index')
            ->with('success', 'Ledger updated successfully!');
    }

    /**
     * Remove the specified ledger from storage.
     */
    public function destroy(Ledger $ledger)
    {
        // Check if ledger belongs to current user's business
        if ($ledger->business_id !== $this->getBusinessId()) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            // Check if ledger has any transaction lines
            $transactionCount = TransactionLine::where('ledger_id', $ledger->id)->count();

            if ($transactionCount > 0) {
                return redirect()->route('admin.ledgers.index')
                    ->with('error', 'Cannot delete ledger. It has associated transactions.');
            }

            $ledger->delete();

            DB::commit();
            return redirect()->route('admin.ledgers.index')
                ->with('success', 'Ledger deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.ledgers.index')
                ->with('error', 'Failed to delete ledger: ' . $e->getMessage());
        }
    }

    /**
     * Recalculate the current balance of the specified ledger.
     */
    public function recalculateBalance(Ledger $ledger)
    {
        // Check if ledger belongs to current user's business
        if ($ledger->business_id !== $this->getBusinessId()) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            // Get all transaction lines for this ledger
            $transactionLines = TransactionLine::where('ledger_id', $ledger->id)
                ->orderBy('created_at', 'asc')
                ->get();

            // Calculate running balance based on transaction lines
            $currentBalance = 0;
            foreach ($transactionLines as $line) {
                if ($ledger->balance_type === 'Dr') {
                    $currentBalance += $line->debit_amount;
                    $currentBalance -= $line->credit_amount;
                } else {
                    $currentBalance -= $line->debit_amount;
                    $currentBalance += $line->credit_amount;
                }
            }

            $ledger->current_balance = $currentBalance;
            $ledger->save();

            DB::commit();
            return redirect()->back()->with('success', 'Ledger balance recalculated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to recalculate ledger balance: ' . $e->getMessage());
        }
    }

    /**
     * Get the business ID for the authenticated user.
     */
    private function getBusinessId()
    {
        $user = Auth::user();

        // Check if user is staff
        if ($user->roles->contains('name', 'staff')) {
            $staff = $user->staff; // Assuming you have a staff relationship in User model
            return $staff ? $staff->business_id : null;
        }

        // Check if user is admin
        if ($user->roles->contains('name', 'admin')) {
            $admin = Admin::where('user_id', $user->id)->first();
            return $admin ? $admin->business_id : null;
        }

        return null;
    }
}
