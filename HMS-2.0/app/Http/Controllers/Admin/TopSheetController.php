<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalInvoice;
use App\Models\Transaction;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TopSheetController extends Controller
{
    /**
     * Display the top sheet report page
     */
    public function index(Request $request)
    {
        $business = $this->getCurrentBusiness();

        // Set default date range (current month)
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Get the report data if dates are provided
        $reportData = null;
        if ($request->has('start_date') && $request->has('end_date')) {
            $reportData = $this->getTopSheetData($startDate, $endDate, $business->id);
        }

        return view('admin.reports.top-sheet.index', compact('startDate', 'endDate', 'reportData', 'business'));
    }

    /**
     * Get top sheet data via API
     */
    public function getData(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $business = $this->getCurrentBusiness();

            if (!$business) {
                return response()->json(['error' => 'No active business found'], 404);
            }

            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            // Check if date range is too large (more than 1 year)
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            if ($start->diffInDays($end) > 365) {
                return response()->json(['error' => 'Date range cannot exceed 365 days'], 400);
            }

            $data = $this->getTopSheetData($startDate, $endDate, $business->id);

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Top Sheet Report Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'user_id' => Auth::id(),
            ]);

            return response()->json(['error' => 'Failed to generate report'], 500);
        }
    }

    /**
     * Print the top sheet report
     */
    public function print(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $business = $this->getCurrentBusiness();
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $reportData = $this->getTopSheetData($startDate, $endDate, $business->id);

        // Choose the appropriate view based on data size
        $dataCount = count($reportData['breakdown']);

        if ($dataCount <= 35) {
            // Use single page view for small datasets
            return view('admin.reports.top-sheet.print-single', compact('startDate', 'endDate', 'reportData', 'business'));
        } else {
            // Use multi-page view for large datasets
            return view('admin.reports.top-sheet.print-multi', compact('startDate', 'endDate', 'reportData', 'business'));
        }
    }

    /**
     * Get the top sheet data for the given date range
     */
    private function getTopSheetData($startDate, $endDate, $businessId)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Check if it's a single date (start and end are the same)
        $isSingleDate = $start->format('Y-m-d') === $end->format('Y-m-d');

        // Create cache key
        $cacheKey = "top_sheet_data_{$businessId}_{$start->format('Y-m-d')}_{$end->format('Y-m-d')}";

        // Try to get from cache (cache for 5 minutes)
        return Cache::remember($cacheKey, 300, function () use ($start, $end, $businessId, $isSingleDate) {
            if ($isSingleDate) {
                return $this->getSingleDateDetailedData($start, $businessId);
            } else {
                return $this->getDateRangeData($start, $end, $businessId);
            }
        });
    }

    /**
     * Get detailed data for a single date (line items)
     */
    private function getSingleDateDetailedData($date, $businessId)
    {
        $dayStart = $date->copy()->startOfDay();
        $dayEnd = $date->copy()->endOfDay();
        $breakdown = [];

        // Get all invoices for the date
        $invoices = MedicalInvoice::where('business_id', $businessId)
            ->whereBetween('invoice_date', [$dayStart, $dayEnd])
            ->where('status', '!=', 'cancelled')
            ->with(['patient', 'careOf', 'visibleLines'])
            ->get();

        // Add invoice rows
        foreach ($invoices as $invoice) {
            $collectedAmount = $invoice->status === 'paid' ? $invoice->grand_total : $invoice->paid_amount;

            $breakdown[] = [
                'type' => 'invoice',
                'name' => $invoice->patient ? $invoice->patient->full_name : 'Unknown Patient',
                'sales' => (float) $invoice->grand_total,
                'collected_sales' => (float) $collectedAmount,
                'expenses' => 0,
                'commission' => 0,
                'discount' => (float) $invoice->discount,
            ];

            // Add commission row if invoice has care_of
            if ($invoice->careOf) {
                $subtotal = $invoice->visibleLines->sum('line_total');
                $commissionAmount = 0;

                if ($invoice->careOf->commission_type === 'percentage') {
                    $commissionAmount = ($subtotal * $invoice->careOf->commission_rate) / 100;
                } elseif ($invoice->careOf->commission_type === 'fixed') {
                    $commissionAmount = $invoice->careOf->commission_rate;
                }

                if ($commissionAmount > 0) {
                    $breakdown[] = [
                        'type' => 'commission',
                        'name' => $invoice->careOf->name,
                        'sales' => 0,
                        'collected_sales' => 0,
                        'expenses' => 0,
                        'commission' => (float) $commissionAmount,
                        'discount' => 0,
                    ];
                }
            }
        }

        // Get all expenses for the date
        $expenses = Transaction::where('business_id', $businessId)
            ->whereBetween('transaction_date', [$dayStart, $dayEnd])
            ->where('transaction_type', 'Payment')
            ->get();

        // Add expense rows
        foreach ($expenses as $expense) {
            $breakdown[] = [
                'type' => 'expense',
                'name' => $expense->narration ?: 'Expense',
                'sales' => 0,
                'collected_sales' => 0,
                'expenses' => (float) $expense->amount,
                'commission' => 0,
                'discount' => 0,
            ];
        }

        // Calculate totals
        $totals = [
            'sales' => array_sum(array_column($breakdown, 'sales')),
            'collected_sales' => array_sum(array_column($breakdown, 'collected_sales')),
            'expenses' => array_sum(array_column($breakdown, 'expenses')),
            'commission' => array_sum(array_column($breakdown, 'commission')),
            'discount' => array_sum(array_column($breakdown, 'discount')),
        ];
        $totals['net_profit'] = $totals['collected_sales'] - $totals['expenses'] - $totals['commission'];

        return [
            'breakdown' => $breakdown,
            'totals' => $totals,
            'is_single_date' => true,
            'date_range' => [
                'start' => $date->format('M d, Y'),
                'end' => $date->format('M d, Y'),
            ]
        ];
    }

    /**
     * Get summary data for date range (existing logic)
     */
    private function getDateRangeData($start, $end, $businessId)
    {
        // Get daily breakdown data
        $breakdown = [];
        $current = $start->copy();

        while ($current <= $end) {
            $dayStart = $current->copy()->startOfDay();
            $dayEnd = $current->copy()->endOfDay();

            // Get sales data from medical invoices with optimized query
            $salesData = MedicalInvoice::where('business_id', $businessId)
                ->whereBetween('invoice_date', [$dayStart, $dayEnd])
                ->where('status', '!=', 'cancelled')
                ->selectRaw('
                    SUM(grand_total) as total_sales,
                    SUM(CASE WHEN status = "paid" THEN grand_total ELSE paid_amount END) as collected_sales
                ')
                ->first();

            // Get expenses from transactions (Payment type)
            $expensesData = Transaction::where('business_id', $businessId)
                ->whereBetween('transaction_date', [$dayStart, $dayEnd])
                ->where('transaction_type', 'Payment')
                ->sum('amount');

            // Calculate commission from medical invoice lines more efficiently
            // Try commission lines first
            $commissionData = DB::table('medical_invoices')
                ->join('medical_invoice_lines', 'medical_invoices.id', '=', 'medical_invoice_lines.medical_invoice_id')
                ->where('medical_invoices.business_id', $businessId)
                ->whereBetween('medical_invoices.invoice_date', [$dayStart, $dayEnd])
                ->where('medical_invoices.status', '!=', 'cancelled')
                ->where('medical_invoice_lines.service_type', 'commission')
                ->sum('medical_invoice_lines.line_total');

            // If no commission lines found, try alternative calculation using care_of commission
            if ($commissionData == 0) {
                $commissionData = MedicalInvoice::where('business_id', $businessId)
                    ->whereBetween('invoice_date', [$dayStart, $dayEnd])
                    ->where('status', '!=', 'cancelled')
                    ->whereNotNull('care_of_id')
                    ->with(['careOf', 'visibleLines'])
                    ->get()
                    ->sum(function ($invoice) {
                        if (!$invoice->careOf) return 0;

                        $subtotal = $invoice->visibleLines->sum('line_total');
                        $commissionRate = $invoice->careOf->commission_rate ?? 0;

                        if ($invoice->careOf->commission_type === 'percentage') {
                            return ($subtotal * $commissionRate) / 100;
                        } elseif ($invoice->careOf->commission_type === 'fixed') {
                            return $commissionRate;
                        }

                        return 0;
                    });
            }

            $totalSales = (float) ($salesData->total_sales ?? 0);
            $collectedSales = (float) ($salesData->collected_sales ?? 0);
            $expenses = (float) $expensesData;
            $commission = (float) $commissionData;

            $breakdown[] = [
                'date' => $current->format('Y-m-d'),
                'date_formatted' => $current->format('M d, Y'),
                'sales' => $totalSales,
                'collected_sales' => $collectedSales,
                'expenses' => $expenses,
                'commission' => $commission,
                'net_profit' => $collectedSales - $expenses - $commission,
            ];

            $current->addDay();
        }

        // Calculate totals
        $totals = [
            'sales' => array_sum(array_column($breakdown, 'sales')),
            'collected_sales' => array_sum(array_column($breakdown, 'collected_sales')),
            'expenses' => array_sum(array_column($breakdown, 'expenses')),
            'commission' => array_sum(array_column($breakdown, 'commission')),
            'net_profit' => array_sum(array_column($breakdown, 'net_profit')),
        ];

        return [
            'breakdown' => $breakdown,
            'totals' => $totals,
            'is_single_date' => false,
            'date_range' => [
                'start' => $start->format('M d, Y'),
                'end' => $end->format('M d, Y'),
            ]
        ];
    }

    /**
     * Get the current business
     */
    private function getCurrentBusiness()
    {
        $user = Auth::user();

        if (!$user) {
            return Business::where('is_active', true)->first();
        }

        // Check if user has super-admin role using different possible implementations
        $isSuperAdmin = false;

        try {
            // Try Laravel Spatie Permission package method
            if (method_exists($user, 'hasRole')) {
                $isSuperAdmin = $user->hasRole('super-admin');
            }
            // Try direct role attribute check
            elseif (isset($user->role) && $user->role === 'super-admin') {
                $isSuperAdmin = true;
            }
            // Try roles collection check
            elseif (isset($user->roles)) {
                if ($user->roles instanceof \Illuminate\Database\Eloquent\Collection) {
                    $isSuperAdmin = $user->roles->pluck('name')->contains('super-admin');
                } elseif (is_array($user->roles)) {
                    $isSuperAdmin = in_array('super-admin', $user->roles);
                }
            }
        } catch (\Exception $e) {
            // If role checking fails, assume not super admin
            $isSuperAdmin = false;
        }

        if ($isSuperAdmin) {
            return Business::where('is_active', true)->first();
        }

        return $user->business ?? Business::where('is_active', true)->first();
    }
}
