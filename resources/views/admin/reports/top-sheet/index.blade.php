@extends('admin.layouts.app')

@section('title', 'Top Sheet Report')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-lg rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center print-hide">
            <h3 class="text-xl font-semibold text-gray-900">Top Sheet Report</h3>
            <div class="flex gap-2">
                <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed print-hide" id="printBtn" disabled>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Report
                </button>
            </div>
        </div>
        <div class="p-6">
            <!-- Date Filter Form -->
            <form id="filterForm" class="mb-6 print-hide">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <div class="md:col-span-3">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="start_date" name="start_date" value="{{ $startDate }}" required>
                    </div>
                    <div class="md:col-span-3">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="end_date" name="end_date" value="{{ $endDate }}" required>
                    </div>
                    <div class="md:col-span-6">
                        <div class="flex flex-wrap gap-2">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                Filter
                            </button>
                            <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md flex items-center gap-2" id="resetBtn">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Reset
                            </button>
                        </div>

                        <!-- Quick Date Presets -->
                        <div class="flex flex-wrap gap-1 mt-2">
                            <button type="button" class="text-xs px-2 py-1 border border-blue-300 text-blue-600 hover:bg-blue-50 rounded" id="todayBtn">Today</button>
                            <button type="button" class="text-xs px-2 py-1 border border-blue-300 text-blue-600 hover:bg-blue-50 rounded" id="yesterdayBtn">Yesterday</button>
                            <button type="button" class="text-xs px-2 py-1 border border-blue-300 text-blue-600 hover:bg-blue-50 rounded" id="thisWeekBtn">This Week</button>
                            <button type="button" class="text-xs px-2 py-1 border border-blue-300 text-blue-600 hover:bg-blue-50 rounded" id="lastWeekBtn">Last Week</button>
                            <button type="button" class="text-xs px-2 py-1 border border-blue-300 text-blue-600 hover:bg-blue-50 rounded" id="thisMonthBtn">This Month</button>
                            <button type="button" class="text-xs px-2 py-1 border border-blue-300 text-blue-600 hover:bg-blue-50 rounded" id="lastMonthBtn">Last Month</button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="text-center py-8 print-hide" style="display: none;">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="mt-2 text-gray-600">Loading report data...</p>
            </div>

            <!-- Report Content -->
            <div id="reportContent" style="{{ $reportData ? '' : 'display: none;' }}">
                <!-- Date Range Display -->
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6" id="dateRangeDisplay">
                    @if($reportData)
                    <span class="font-semibold text-blue-800">Report Period:</span>
                    <span class="text-blue-700">{{ $reportData['date_range']['start'] }} to {{ $reportData['date_range']['end'] }}</span>
                    @endif
                </div>

                <!-- Top Sheet Table -->
                <div class="overflow-x-auto shadow-sm border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="topSheetTable">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                @if($reportData && isset($reportData['is_single_date']) && $reportData['is_single_date'])
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Patient/Care Name</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Sales</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Collected</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Expenses</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Commission</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Discount</th>
                                @else
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Sales</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Collected</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Expenses</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Commission</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="reportTableBody">
                            @if($reportData)
                            @foreach($reportData['breakdown'] as $row)
                            <tr class="hover:bg-gray-50 {{ isset($row['type']) ? 'row-' . $row['type'] : '' }}">
                                @if(isset($reportData['is_single_date']) && $reportData['is_single_date'])
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row['name'] ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ $row['sales'] > 0 ? '৳' . number_format($row['sales'], 2) : '' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ $row['collected_sales'] > 0 ? '৳' . number_format($row['collected_sales'], 2) : '' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ $row['expenses'] > 0 ? '৳' . number_format($row['expenses'], 2) : '' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ $row['commission'] > 0 ? '৳' . number_format($row['commission'], 2) : '' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ $row['discount'] > 0 ? '৳' . number_format($row['discount'], 2) : '' }}</td>
                                @else
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row['date_formatted'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">৳{{ number_format($row['sales'], 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">৳{{ number_format($row['collected_sales'], 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">৳{{ number_format($row['expenses'], 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">৳{{ number_format($row['commission'], 2) }}</td>
                                @endif
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                        <tfoot class="bg-gray-800 text-white">
                            <tr id="totalsRow">
                                @if($reportData)
                                @if(isset($reportData['is_single_date']) && $reportData['is_single_date'])
                                <th class="px-6 py-4 text-left text-sm font-bold uppercase">TOTAL</th>
                                <th class="px-6 py-4 text-right text-sm font-bold">৳{{ number_format($reportData['totals']['sales'], 2) }}</th>
                                <th class="px-6 py-4 text-right text-sm font-bold">৳{{ number_format($reportData['totals']['collected_sales'], 2) }}</th>
                                <th class="px-6 py-4 text-right text-sm font-bold">৳{{ number_format($reportData['totals']['expenses'], 2) }}</th>
                                <th class="px-6 py-4 text-right text-sm font-bold">৳{{ number_format($reportData['totals']['commission'], 2) }}</th>
                                <th class="px-6 py-4 text-right text-sm font-bold">৳{{ number_format($reportData['totals']['discount'], 2) }}</th>
                                @else
                                <th class="px-6 py-4 text-left text-sm font-bold uppercase">TOTAL</th>
                                <th class="px-6 py-4 text-right text-sm font-bold">৳{{ number_format($reportData['totals']['sales'], 2) }}</th>
                                <th class="px-6 py-4 text-right text-sm font-bold">৳{{ number_format($reportData['totals']['collected_sales'], 2) }}</th>
                                <th class="px-6 py-4 text-right text-sm font-bold">৳{{ number_format($reportData['totals']['expenses'], 2) }}</th>
                                <th class="px-6 py-4 text-right text-sm font-bold">৳{{ number_format($reportData['totals']['commission'], 2) }}</th>
                                @endif
                                @else
                                <th class="px-6 py-4 text-left text-sm font-bold uppercase">TOTAL</th>
                                <th class="px-6 py-4 text-right text-sm font-bold">৳0.00</th>
                                <th class="px-6 py-4 text-right text-sm font-bold">৳0.00</th>
                                <th class="px-6 py-4 text-right text-sm font-bold">৳0.00</th>
                                <th class="px-6 py-4 text-right text-sm font-bold">৳0.00</th>
                                @endif
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mt-6" id="summaryCards">
                    @if($reportData)
                    <div class="bg-blue-600 text-white rounded-lg p-4 text-center">
                        <h5 class="text-sm font-medium mb-2">Total Sales</h5>
                        <h3 class="text-xl font-bold">৳{{ number_format($reportData['totals']['sales'], 0) }}</h3>
                    </div>
                    <div class="bg-green-600 text-white rounded-lg p-4 text-center">
                        <h5 class="text-sm font-medium mb-2">Collected</h5>
                        <h3 class="text-xl font-bold">৳{{ number_format($reportData['totals']['collected_sales'], 0) }}</h3>
                    </div>
                    <div class="bg-red-600 text-white rounded-lg p-4 text-center">
                        <h5 class="text-sm font-medium mb-2">Expenses</h5>
                        <h3 class="text-xl font-bold">৳{{ number_format($reportData['totals']['expenses'], 0) }}</h3>
                    </div>
                    <div class="bg-yellow-600 text-white rounded-lg p-4 text-center">
                        <h5 class="text-sm font-medium mb-2">Commission</h5>
                        <h3 class="text-xl font-bold">৳{{ number_format($reportData['totals']['commission'], 0) }}</h3>
                    </div>
                    <div class="bg-indigo-600 text-white rounded-lg p-4 text-center">
                        <h5 class="text-sm font-medium mb-2">Collection %</h5>
                        <h3 class="text-xl font-bold">{{ $reportData['totals']['sales'] > 0 ? number_format(($reportData['totals']['collected_sales'] / $reportData['totals']['sales']) * 100, 1) : 0 }}%</h3>
                    </div>
                    @endif
                </div>
            </div>

            <!-- No Data Message -->
            <div id="noDataMessage" class="text-center py-12 print-hide" style="{{ $reportData ? 'display: none;' : '' }}">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h4 class="text-xl font-medium text-gray-600 mb-2">No Data Available</h4>
                <p class="text-gray-500">Please select a date range and click Filter to generate the report.</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Form submission
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            loadReportData();
        });

        // Reset button
        $('#resetBtn').on('click', function() {
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

            $('#start_date').val(firstDay.toISOString().split('T')[0]);
            $('#end_date').val(lastDay.toISOString().split('T')[0]);

            loadReportData();
        });

        // Print button
        $('#printBtn').on('click', function() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            if (startDate && endDate) {
                const printUrl = `{{ route('admin.reports.top-sheet.print') }}?start_date=${startDate}&end_date=${endDate}`;
                window.open(printUrl, '_blank');
            }
        });

        // Quick date preset buttons
        $('#todayBtn').on('click', function() {
            const today = new Date().toISOString().split('T')[0];
            $('#start_date').val(today);
            $('#end_date').val(today);
            loadReportData();
        });

        $('#yesterdayBtn').on('click', function() {
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            const yesterdayStr = yesterday.toISOString().split('T')[0];
            $('#start_date').val(yesterdayStr);
            $('#end_date').val(yesterdayStr);
            loadReportData();
        });

        $('#thisWeekBtn').on('click', function() {
            const today = new Date();
            const firstDay = new Date(today.setDate(today.getDate() - today.getDay()));
            const lastDay = new Date(today.setDate(today.getDate() - today.getDay() + 6));
            $('#start_date').val(firstDay.toISOString().split('T')[0]);
            $('#end_date').val(lastDay.toISOString().split('T')[0]);
            loadReportData();
        });

        $('#lastWeekBtn').on('click', function() {
            const today = new Date();
            const firstDay = new Date(today.setDate(today.getDate() - today.getDay() - 7));
            const lastDay = new Date(today.setDate(today.getDate() - today.getDay() - 1));
            $('#start_date').val(firstDay.toISOString().split('T')[0]);
            $('#end_date').val(lastDay.toISOString().split('T')[0]);
            loadReportData();
        });

        $('#thisMonthBtn').on('click', function() {
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            $('#start_date').val(firstDay.toISOString().split('T')[0]);
            $('#end_date').val(lastDay.toISOString().split('T')[0]);
            loadReportData();
        });

        $('#lastMonthBtn').on('click', function() {
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            const lastDay = new Date(today.getFullYear(), today.getMonth(), 0);
            $('#start_date').val(firstDay.toISOString().split('T')[0]);
            $('#end_date').val(lastDay.toISOString().split('T')[0]);
            loadReportData();
        });

        function loadReportData() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            if (!startDate || !endDate) {
                alert('Please select both start and end dates.');
                return;
            }

            if (new Date(startDate) > new Date(endDate)) {
                alert('Start date cannot be after end date.');
                return;
            }

            // Show loading
            $('#loadingIndicator').show();
            $('#reportContent').hide();
            $('#noDataMessage').hide();
            $('#printBtn').prop('disabled', true);

            // Make AJAX request
            $.ajax({
                url: '{{ route("admin.reports.top-sheet.data") }}',
                method: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    updateReportDisplay(response);
                    $('#printBtn').prop('disabled', false);
                },
                error: function(xhr) {
                    console.error('Error loading report data:', xhr);
                    let errorMessage = 'Error loading report data. Please try again.';

                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    } else if (xhr.status === 422) {
                        errorMessage = 'Invalid date range selected.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error occurred. Please try again later.';
                    }

                    alert(errorMessage);
                    $('#noDataMessage').show();
                },
                complete: function() {
                    $('#loadingIndicator').hide();
                }
            });
        }

        function updateReportDisplay(data) {
            // Update date range display
            $('#dateRangeDisplay').html(`<span class="font-semibold text-blue-800">Report Period:</span> <span class="text-blue-700">${data.date_range.start} to ${data.date_range.end}</span>`);

            // Update table header and body based on single date or date range
            let tableHeader = '';
            let tableBody = '';
            let totalsRow = '';

            if (data.is_single_date) {
                // Single date detailed view
                tableHeader = `
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Patient/Care Name</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Sales</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Collected</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Expenses</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Commission</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Discount</th>
                `;

                data.breakdown.forEach(function(row) {
                    const rowClass = row.type ? `row-${row.type}` : '';
                    tableBody += `
                    <tr class="hover:bg-gray-50 ${rowClass}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${row.name || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">${row.sales > 0 ? '৳' + numberFormat(row.sales) : ''}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">${row.collected_sales > 0 ? '৳' + numberFormat(row.collected_sales) : ''}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">${row.expenses > 0 ? '৳' + numberFormat(row.expenses) : ''}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">${row.commission > 0 ? '৳' + numberFormat(row.commission) : ''}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">${row.discount > 0 ? '৳' + numberFormat(row.discount) : ''}</td>
                    </tr>
                `;
                });

                totalsRow = `
                    <th class="px-6 py-4 text-left text-sm font-bold uppercase">TOTAL</th>
                    <th class="px-6 py-4 text-right text-sm font-bold">৳${numberFormat(data.totals.sales)}</th>
                    <th class="px-6 py-4 text-right text-sm font-bold">৳${numberFormat(data.totals.collected_sales)}</th>
                    <th class="px-6 py-4 text-right text-sm font-bold">৳${numberFormat(data.totals.expenses)}</th>
                    <th class="px-6 py-4 text-right text-sm font-bold">৳${numberFormat(data.totals.commission)}</th>
                    <th class="px-6 py-4 text-right text-sm font-bold">৳${numberFormat(data.totals.discount || 0)}</th>
                `;
            } else {
                // Date range summary view
                tableHeader = `
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Sales</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Collected</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Expenses</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Commission</th>
                `;

                data.breakdown.forEach(function(row) {
                    tableBody += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${row.date_formatted}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">৳${numberFormat(row.sales)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">৳${numberFormat(row.collected_sales)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">৳${numberFormat(row.expenses)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">৳${numberFormat(row.commission)}</td>
                    </tr>
                `;
                });

                totalsRow = `
                    <th class="px-6 py-4 text-left text-sm font-bold uppercase">TOTAL</th>
                    <th class="px-6 py-4 text-right text-sm font-bold">৳${numberFormat(data.totals.sales)}</th>
                    <th class="px-6 py-4 text-right text-sm font-bold">৳${numberFormat(data.totals.collected_sales)}</th>
                    <th class="px-6 py-4 text-right text-sm font-bold">৳${numberFormat(data.totals.expenses)}</th>
                    <th class="px-6 py-4 text-right text-sm font-bold">৳${numberFormat(data.totals.commission)}</th>
                `;
            }

            // Update table header
            $('#topSheetTable thead tr').html(tableHeader);
            $('#reportTableBody').html(tableBody);
            $('#totalsRow').html(totalsRow);

            // Update summary cards
            const collectionPercentage = data.totals.sales > 0 ? ((data.totals.collected_sales / data.totals.sales) * 100).toFixed(1) : 0;

            let summaryCards = `
            <div class="bg-blue-600 text-white rounded-lg p-4 text-center">
                <h5 class="text-sm font-medium mb-2">Total Sales</h5>
                <h3 class="text-xl font-bold">৳${numberFormat(data.totals.sales, 0)}</h3>
            </div>
            <div class="bg-green-600 text-white rounded-lg p-4 text-center">
                <h5 class="text-sm font-medium mb-2">Collected</h5>
                <h3 class="text-xl font-bold">৳${numberFormat(data.totals.collected_sales, 0)}</h3>
            </div>
            <div class="bg-red-600 text-white rounded-lg p-4 text-center">
                <h5 class="text-sm font-medium mb-2">Expenses</h5>
                <h3 class="text-xl font-bold">৳${numberFormat(data.totals.expenses, 0)}</h3>
            </div>
            <div class="bg-yellow-600 text-white rounded-lg p-4 text-center">
                <h5 class="text-sm font-medium mb-2">Commission</h5>
                <h3 class="text-xl font-bold">৳${numberFormat(data.totals.commission, 0)}</h3>
            </div>
            <div class="bg-indigo-600 text-white rounded-lg p-4 text-center">
                <h5 class="text-sm font-medium mb-2">Collection %</h5>
                <h3 class="text-xl font-bold">${collectionPercentage}%</h3>
            </div>
        `;

            // Add discount card for single date view
            if (data.is_single_date && data.totals.discount > 0) {
                summaryCards += `
                <div class="bg-purple-600 text-white rounded-lg p-4 text-center">
                    <h5 class="text-sm font-medium mb-2">Total Discount</h5>
                    <h3 class="text-xl font-bold">৳${numberFormat(data.totals.discount, 0)}</h3>
                </div>
            `;
            }

            $('#summaryCards').html(summaryCards);
            $('#reportContent').show();
        }

        function numberFormat(number, decimals = 2) {
            return parseFloat(number).toLocaleString('en-IN', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        }
    });
</script>
@endpush

@push('styles')
<style>
    @media print {
        .print-hide {
            display: none !important;
        }
    }

    /* Single date detailed view row styling */
    .row-invoice {
        background-color: #f8fafc !important;
    }

    .row-commission {
        background-color: #fef3c7 !important;
    }

    .row-expense {
        background-color: #fee2e2 !important;
    }

    .row-invoice:hover {
        background-color: #f1f5f9 !important;
    }

    .row-commission:hover {
        background-color: #fde68a !important;
    }

    .row-expense:hover {
        background-color: #fecaca !important;
    }
</style>
@endpush
@endsection