<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Appointment;
use App\Models\WaitingList;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminsController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function index()
    {
        $businessId = Auth::user()->business_id;

        // Get date range from request or use default (last 30 days)
        $startDate = request('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));

        // Convert to Carbon instances for queries
        $startDateTime = \Carbon\Carbon::parse($startDate)->startOfDay();
        $endDateTime = \Carbon\Carbon::parse($endDate)->endOfDay();

        // Get basic statistics
        $stats = [
            'total_doctors' => Doctor::forBusiness($businessId)->count(),
            'active_doctors' => Doctor::forBusiness($businessId)->where('is_active', true)->count(),
            'inactive_doctors' => Doctor::forBusiness($businessId)->where('is_active', false)->count(),
            'total_staff' => User::where('business_id', $businessId)
                ->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['admin', 'staff']);
                })->count(),
            'total_patients' => \App\Models\Patient::forBusiness($businessId)->count(),
            'total_appointments' => Appointment::forBusiness($businessId)
                ->whereBetween('appointment_date', [$startDateTime, $endDateTime])
                ->count(),
            'today_appointments' => Appointment::forBusiness($businessId)->today()->count(),
            'upcoming_appointments' => Appointment::forBusiness($businessId)->upcoming()->count(),
            'active_waiting_list' => WaitingList::forBusiness($businessId)
                ->where('status', 'waiting')
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })->count(),
        ];

        // Get financial statistics for the selected period
        // Total sales (grand total of invoices)
        $stats['total_sales'] = \App\Models\MedicalInvoice::forBusiness($businessId)
            ->whereBetween('invoice_date', [$startDateTime, $endDateTime])
            ->sum('grand_total');

        // Total collection (paid amount only)
        $stats['total_collection'] = \App\Models\MedicalInvoice::forBusiness($businessId)
            ->whereBetween('invoice_date', [$startDateTime, $endDateTime])
            ->sum('paid_amount');

        $stats['total_discounts'] = \App\Models\MedicalInvoice::forBusiness($businessId)
            ->whereBetween('invoice_date', [$startDateTime, $endDateTime])
            ->sum('discount');

        $stats['total_reports'] = \App\Models\LabReport::forBusiness($businessId)
            ->whereBetween('report_date', [$startDateTime, $endDateTime])
            ->count();

        $stats['total_expenses'] = \App\Models\Transaction::forBusiness($businessId)
            ->where('transaction_type', 'Payment')
            ->whereBetween('transaction_date', [$startDateTime, $endDateTime])
            ->sum('amount');

        $stats['pending_payments'] = \App\Models\MedicalInvoice::forBusiness($businessId)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('invoice_date', [$startDateTime, $endDateTime])
            ->whereRaw('grand_total > paid_amount')
            ->sum(DB::raw('grand_total - paid_amount'));

        // Calculate cash in hand (collections - expenses)
        $stats['cash_in_hand'] = ($stats['total_collection'] ?? 0) - ($stats['total_expenses'] ?? 0);

        // Total Sales card = Total Due + Total Collection + Total Discount
        $stats['total_sales_card'] = ($stats['pending_payments'] ?? 0) + ($stats['total_collection'] ?? 0) + ($stats['total_discounts'] ?? 0);

        // Get recent doctors
        $recentDoctors = Doctor::forBusiness($businessId)
            ->latest()
            ->take(5)
            ->get();

        // Get recent appointments
        $recentAppointments = Appointment::with(['patient', 'doctor'])
            ->forBusiness($businessId)
            ->latest()
            ->take(5)
            ->get();

        // Get doctors by specialization
        $doctorsBySpecialization = Doctor::forBusiness($businessId)
            ->select('specialization', DB::raw('count(*) as count'))
            ->groupBy('specialization')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        // Get monthly doctor registrations (last 6 months)
        $monthlyRegistrations = Doctor::forBusiness($businessId)
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Get appointment statistics
        $appointmentStats = [
            'today_total' => Appointment::forBusiness($businessId)->today()->count(),
            'today_completed' => Appointment::forBusiness($businessId)->today()->where('status', 'completed')->count(),
            'today_scheduled' => Appointment::forBusiness($businessId)->today()->where('status', 'scheduled')->count(),
            'today_cancelled' => Appointment::forBusiness($businessId)->today()->where('status', 'cancelled')->count(),
            'this_week' => Appointment::forBusiness($businessId)
                ->whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'this_month' => Appointment::forBusiness($businessId)
                ->whereMonth('appointment_date', now()->month)
                ->whereYear('appointment_date', now()->year)
                ->count(),
        ];

        return view('admin.dashboard', compact(
            'stats',
            'recentDoctors',
            'recentAppointments',
            'doctorsBySpecialization',
            'monthlyRegistrations',
            'appointmentStats',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get dashboard statistics via AJAX
     */
    public function getStats()
    {
        $businessId = Auth::user()->business_id;

        // Get date range from request or use default (last 30 days)
        $startDate = request('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));

        // Convert to Carbon instances for queries
        $startDateTime = \Carbon\Carbon::parse($startDate)->startOfDay();
        $endDateTime = \Carbon\Carbon::parse($endDate)->endOfDay();

        $stats = [
            'total_doctors' => Doctor::forBusiness($businessId)->count(),
            'active_doctors' => Doctor::forBusiness($businessId)->where('is_active', true)->count(),
            'inactive_doctors' => Doctor::forBusiness($businessId)->where('is_active', false)->count(),
            'total_staff' => User::where('business_id', $businessId)
                ->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['admin', 'staff']);
                })->count(),
            'total_patients' => \App\Models\Patient::forBusiness($businessId)->count(),
            'avg_consultation_fee' => Doctor::forBusiness($businessId)->avg('consultation_fee'),
            'avg_experience' => Doctor::forBusiness($businessId)->avg('experience_years'),
            'total_appointments' => Appointment::forBusiness($businessId)
                ->whereBetween('appointment_date', [$startDateTime, $endDateTime])
                ->count(),
            'active_appointments' => Appointment::forBusiness($businessId)
                ->whereIn('status', ['scheduled', 'confirmed', 'in_progress'])
                ->count(),
            'completed_appointments' => Appointment::forBusiness($businessId)->where('status', 'completed')->count(),
            'cancelled_appointments' => Appointment::forBusiness($businessId)->where('status', 'cancelled')->count(),
            'active_waiting_list' => WaitingList::forBusiness($businessId)
                ->where('status', 'waiting')
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })->count(),
        ];

        // Add financial statistics for the selected period
        // Total sales (grand total of invoices)
        $stats['total_sales'] = \App\Models\MedicalInvoice::forBusiness($businessId)
            ->whereBetween('invoice_date', [$startDateTime, $endDateTime])
            ->sum('grand_total');

        // Total collection (paid amount only)
        $stats['total_collection'] = \App\Models\MedicalInvoice::forBusiness($businessId)
            ->whereBetween('invoice_date', [$startDateTime, $endDateTime])
            ->sum('paid_amount');

        $stats['total_discounts'] = \App\Models\MedicalInvoice::forBusiness($businessId)
            ->whereBetween('invoice_date', [$startDateTime, $endDateTime])
            ->sum('discount');

        $stats['total_reports'] = \App\Models\LabReport::forBusiness($businessId)
            ->whereBetween('report_date', [$startDateTime, $endDateTime])
            ->count();

        $stats['total_expenses'] = \App\Models\Transaction::forBusiness($businessId)
            ->where('transaction_type', 'Payment')
            ->whereBetween('transaction_date', [$startDateTime, $endDateTime])
            ->sum('amount');

        $stats['pending_payments'] = \App\Models\MedicalInvoice::forBusiness($businessId)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('invoice_date', [$startDateTime, $endDateTime])
            ->whereRaw('grand_total > paid_amount')
            ->sum(DB::raw('grand_total - paid_amount'));

        // Calculate cash in hand (collections - expenses)
        $stats['cash_in_hand'] = ($stats['total_collection'] ?? 0) - ($stats['total_expenses'] ?? 0);

        // Total Sales card = Total Due + Total Collection + Total Discount
        $stats['total_sales_card'] = ($stats['pending_payments'] ?? 0) + ($stats['total_collection'] ?? 0) + ($stats['total_discounts'] ?? 0);

        // Get appointment statistics for today
        $stats['today_appointments'] = Appointment::forBusiness($businessId)->today()->count();
        $stats['today_completed'] = Appointment::forBusiness($businessId)->today()->where('status', 'completed')->count();
        $stats['today_scheduled'] = Appointment::forBusiness($businessId)->today()->where('status', 'scheduled')->count();

        return response()->json($stats);
    }

    /**
     * Get chart data via AJAX
     */
    public function getCharts()
    {
        $businessId = Auth::user()->business_id;

        // Doctors by specialization
        $specializationData = Doctor::forBusiness($businessId)
            ->select('specialization', DB::raw('count(*) as count'))
            ->groupBy('specialization')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->specialization,
                    'value' => $item->count
                ];
            });

        // Monthly registrations
        $monthlyData = Doctor::forBusiness($businessId)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'count' => $item->count
                ];
            });

        // Experience distribution
        $experienceData = Doctor::forBusiness($businessId)
            ->select(
                DB::raw('CASE 
                    WHEN experience_years < 5 THEN "0-4 years"
                    WHEN experience_years < 10 THEN "5-9 years"
                    WHEN experience_years < 15 THEN "10-14 years"
                    WHEN experience_years < 20 THEN "15-19 years"
                    ELSE "20+ years"
                END as experience_range'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('experience_range')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->experience_range,
                    'value' => $item->count
                ];
            });

        // Appointment status distribution
        $appointmentStatusData = Appointment::forBusiness($businessId)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => ucfirst($item->status),
                    'value' => $item->count
                ];
            });

        // Monthly appointments (last 12 months)
        $monthlyAppointments = Appointment::forBusiness($businessId)
            ->select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('appointment_date', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'count' => $item->count
                ];
            });

        return response()->json([
            'specialization' => $specializationData,
            'monthly_registrations' => $monthlyData,
            'experience_distribution' => $experienceData,
            'appointment_status' => $appointmentStatusData,
            'monthly_appointments' => $monthlyAppointments
        ]);
    }
}
