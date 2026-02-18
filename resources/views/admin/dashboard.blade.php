@extends('admin.layouts.app')

@section('page-title', 'Admin Dashboard')
@section('page-description', 'Hospital management overview and statistics')

@section('content')
<!-- Date Range Filter -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
    <form id="dateFilterForm" class="flex flex-wrap items-center gap-4">
        <div class="flex-grow">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Dashboard Statistics</h3>
            <p class="text-sm text-gray-600">Filter statistics by date range</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <div class="self-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Apply Filter
                </button>
            </div>
            <div class="self-end">
                <button type="button" id="resetFilter" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Reset
                </button>
            </div>
        </div>
    </form>

    <!-- Quick Date Filters -->
    <div class="mt-4 flex flex-wrap gap-2">
        <button type="button" id="filterToday" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Today
        </button>
        <button type="button" id="filterYesterday" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Yesterday
        </button>
        <button type="button" id="filterThisMonth" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            This Month
        </button>
        <button type="button" id="filterThisYear" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            This Year
        </button>
    </div>
</div>

<!-- Dashboard Cards Container -->
<div class="space-y-6">
    @if(auth()->user()->hasRole(['admin', 'Manager']))
    <!-- Financial Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Total Sales (Due + Collection + Discount) -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center h-full">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">TOTAL SALES</p>
                    <p class="text-2xl font-semibold text-gray-900" data-stat="total_sales_card">{{ isset($stats['total_sales_card']) ? number_format($stats['total_sales_card'], 2) : '0.00' }}</p>
                    <p class="text-xs text-gray-500">Due + Collection + Discount</p>
                </div>
            </div>
        </div>

        <!-- Total Due -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center h-full">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">TOTAL DUE</p>
                    <p class="text-2xl font-semibold text-gray-900" data-stat="pending_payments">{{ isset($stats['pending_payments']) ? number_format($stats['pending_payments'], 2) : '0.00' }}</p>
                </div>
            </div>
        </div>

        <!-- Total Discounts -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center h-full">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-amber-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">TOTAL DISCOUNTS</p>
                    <p class="text-2xl font-semibold text-gray-900" data-stat="total_discounts">{{ isset($stats['total_discounts']) ? number_format($stats['total_discounts'], 2) : '0.00' }}</p>
                    <p class="text-xs text-gray-500">From filtered period</p>
                </div>
            </div>
        </div>

        <!-- Today's collections -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center h-full">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-emerald-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">TOTAL COLLECTION</p>
                    <p class="text-2xl font-semibold text-gray-900" data-stat="total_collection">{{ isset($stats['total_collection']) ? number_format($stats['total_collection'], 2) : '0.00' }}</p>
                    <p class="text-xs text-gray-500">Paid amount in filtered period</p>
                </div>
            </div>
        </div>

        <!-- Total Expenses -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center h-full">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">TOTAL EXPENSES</p>
                    <p class="text-2xl font-semibold text-gray-900" data-stat="total_expenses">{{ isset($stats['total_expenses']) ? number_format($stats['total_expenses'], 2) : '0.00' }}</p>
                    <p class="text-xs text-gray-500">From filtered period</p>
                </div>
            </div>
        </div>

        <!-- Cash in Hand -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center h-full">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-teal-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">NET CASH IN HAND</p>
                    <p class="text-2xl font-semibold text-gray-900 {{ isset($stats['cash_in_hand']) && $stats['cash_in_hand'] < 0 ? 'text-red-600' : 'text-gray-900' }}" data-stat="cash_in_hand">{{ isset($stats['cash_in_hand']) ? number_format($stats['cash_in_hand'], 2) : '0.00' }}</p>
                    <p class="text-xs text-gray-500">Collections - Expenses</p>
                </div>
            </div>
        </div>

        <!-- Total Patients -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center h-full">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">TOTAL PATIENTS</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_patients'] ?? '0' }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Auto-refresh dashboard data and handle date filtering -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-refresh stats every 5 minutes
        setInterval(function() {
            // Get current filter values
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            fetch(`{{ route("admin.dashboard.stats") }}?start_date=${startDate}&end_date=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    // Update stats dynamically
                    updateDashboardStats(data);
                    console.log('Dashboard stats updated:', data);
                })
                .catch(error => console.error('Error updating stats:', error));
        }, 300000); // 5 minutes

        // Handle date filter form submission
        const dateFilterForm = document.getElementById('dateFilterForm');
        dateFilterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            // Redirect to same page with query parameters
            window.location.href = `{{ route('admin.dashboard') }}?start_date=${startDate}&end_date=${endDate}`;
        });

        // Handle reset button
        const resetButton = document.getElementById('resetFilter');
        resetButton.addEventListener('click', function() {
            window.location.href = "{{ route('admin.dashboard') }}";
        });

        // Handle quick filter buttons
        // Today filter
        document.getElementById('filterToday').addEventListener('click', function() {
            const today = new Date().toISOString().split('T')[0]; // Format: YYYY-MM-DD
            document.getElementById('start_date').value = today;
            document.getElementById('end_date').value = today;
            dateFilterForm.dispatchEvent(new Event('submit'));
        });

        // Yesterday filter
        document.getElementById('filterYesterday').addEventListener('click', function() {
            const today = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(today.getDate() - 1);
            const yesterdayFormatted = yesterday.toISOString().split('T')[0]; // Format: YYYY-MM-DD

            document.getElementById('start_date').value = yesterdayFormatted;
            document.getElementById('end_date').value = yesterdayFormatted;
            dateFilterForm.dispatchEvent(new Event('submit'));
        });

        // This Month filter
        document.getElementById('filterThisMonth').addEventListener('click', function() {
            const today = new Date();
            const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

            document.getElementById('start_date').value = firstDayOfMonth.toISOString().split('T')[0];
            document.getElementById('end_date').value = today.toISOString().split('T')[0];
            dateFilterForm.dispatchEvent(new Event('submit'));
        });

        // This Year filter
        document.getElementById('filterThisYear').addEventListener('click', function() {
            const today = new Date();
            const firstDayOfYear = new Date(today.getFullYear(), 0, 1);

            document.getElementById('start_date').value = firstDayOfYear.toISOString().split('T')[0];
            document.getElementById('end_date').value = today.toISOString().split('T')[0];
            dateFilterForm.dispatchEvent(new Event('submit'));
        });

        // Function to update dashboard stats dynamically
        function updateDashboardStats(data) {
            // Update financial stats
            if (data.total_collection !== undefined) {
                const el = document.querySelector('[data-stat="total_collection"]');
                if (el) {
                    el.textContent = new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(data.total_collection);
                }
            }

            if (data.total_discounts !== undefined) {
                document.querySelector('[data-stat="total_discounts"]').textContent =
                    new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(data.total_discounts);
            }

            if (data.total_appointments !== undefined) {
                document.querySelector('[data-stat="total_appointments"]').textContent = data.total_appointments;
            }

            if (data.total_reports !== undefined) {
                document.querySelector('[data-stat="total_reports"]').textContent = data.total_reports;
            }

            if (data.total_expenses !== undefined) {
                document.querySelector('[data-stat="total_expenses"]').textContent =
                    new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(data.total_expenses);
            }

            if (data.cash_in_hand !== undefined) {
                const cashInHandElement = document.querySelector('[data-stat="cash_in_hand"]');
                cashInHandElement.textContent =
                    new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(data.cash_in_hand);

                // Update text color based on positive/negative value
                if (data.cash_in_hand < 0) {
                    cashInHandElement.className = cashInHandElement.className.replace('text-gray-900', 'text-red-600');
                } else {
                    cashInHandElement.className = cashInHandElement.className.replace('text-red-600', 'text-gray-900');
                }
            }

            // Total Sales Card (Due + Collection + Discount)
            if (data.total_sales_card !== undefined) {
                document.querySelector('[data-stat="total_sales_card"]').textContent =
                    new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(data.total_sales_card);
            }

            // Update other stats as needed
            if (data.pending_payments !== undefined) {
                document.querySelector('[data-stat="pending_payments"]').textContent =
                    new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(data.pending_payments);
            }

            if (data.active_waiting_list !== undefined) {
                document.querySelector('[data-stat="active_waiting_list"]').textContent = data.active_waiting_list;
            }
        }
    });
</script>
@endsection