@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 sm:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="p-4 sm:p-6">
                <!-- Title and Actions -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $ledger->name }}</h1>
                            <p class="text-sm text-gray-500 mt-1">Ledger Details & Transactions</p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <!-- <button onclick="printLedger()"
                            class="inline-flex items-center justify-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-print mr-2"></i> Print
                        </button> -->
                        <!-- <a href="{{ route('admin.ledgers.edit', $ledger) }}"
                            class="inline-flex items-center justify-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-edit mr-2"></i> Edit
                        </a> -->
                        <a href="{{ route('admin.ledgers.index') }}"
                            class="inline-flex items-center justify-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-arrow-left mr-2"></i> Back
                        </a>
                    </div>
                </div>

                <!-- Month Navigation -->
                <div class="flex items-center justify-center space-x-6 mb-6 p-4 bg-gray-50 rounded-lg">
                    <a href="{{ route('admin.ledgers.show', ['ledger' => $ledger->id, 'month' => $previousMonth ?? date('Y-m', strtotime('-1 month'))]) }}"
                        class="flex items-center justify-center w-10 h-10 bg-white hover:bg-blue-50 text-blue-600 hover:text-blue-700 rounded-full shadow-sm border border-gray-200 transition-all duration-200">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <div class="text-center">
                        <span class="text-lg font-semibold text-gray-900">{{ $currentMonthName ?? date('F') }} {{ $currentYear ?? date('Y') }}</span>
                        <p class="text-xs text-gray-500 mt-1">Transaction Period</p>
                    </div>
                    <a href="{{ route('admin.ledgers.show', ['ledger' => $ledger->id, 'month' => $nextMonth ?? date('Y-m', strtotime('+1 month'))]) }}"
                        class="flex items-center justify-center w-10 h-10 bg-white hover:bg-blue-50 text-blue-600 hover:text-blue-700 rounded-full shadow-sm border border-gray-200 transition-all duration-200">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>

                <!-- Ledger Information Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Current Balance Card -->
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Current Balance</p>
                                <p class="text-2xl font-bold">{{ number_format($ledger->current_balance, 2) }}</p>
                            </div>
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-wallet text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Opening Balance Card -->
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-600 rounded-lg p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Opening Balance</p>
                                <p class="text-2xl font-bold">{{ number_format($ledger->opening_balance ?? 0, 2) }}</p>
                            </div>
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-play text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Debits Card -->
                    <div class="bg-gradient-to-r from-red-500 to-pink-600 rounded-lg p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-red-100 text-sm font-medium">Total Debits</p>
                                <p class="text-2xl font-bold">{{ number_format($totalDebits ?? 0, 2) }}</p>
                            </div>
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-arrow-up text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Credits Card -->
                    <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Total Credits</p>
                                <p class="text-2xl font-bold">{{ number_format($totalCredits ?? 0, 2) }}</p>
                            </div>
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-arrow-down text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-tag text-gray-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Ledger Type</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $ledger->ledger_type ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-balance-scale text-gray-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Balance Type</p>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $ledger->balance_type == 'Dr' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $ledger->balance_type }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                    <i class="fas fa-info-circle text-gray-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Status</p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $ledger->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($ledger->status) }}
                                    </span>
                                </div>
                            </div>
                            <button id="refreshBalanceBtn"
                                class="flex items-center justify-center w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-full transition-colors duration-200"
                                onclick="refreshBalance()" title="Refresh Balance">
                                <i class="fas fa-sync-alt text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-history text-white text-sm"></i>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900">Transaction History</h2>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $transactions->count() ?? 0 }} transactions
                    </div>
                </div>

                @if(isset($transactions) && $transactions->count() > 0)
                <!-- Desktop Table View -->
                <div class="hidden lg:block">
                    <div class="overflow-hidden rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Particulars</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Debit</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Credit</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Balance</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($transactions as $transaction)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $transaction->date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs">
                                            <p class="font-medium truncate" title="{{ $transaction->particulars }}">
                                                {{ Str::limit($transaction->particulars, 50) }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        @if($transaction->debit)
                                        <span class="font-semibold text-red-600">{{ number_format($transaction->debit, 2) }}</span>
                                        @else
                                        <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        @if($transaction->credit)
                                        <span class="font-semibold text-green-600">{{ number_format($transaction->credit, 2) }}</span>
                                        @else
                                        <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-bold {{ $transaction->balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($transaction->balance, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-4">
                    @foreach($transactions as $transaction)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 mb-1">
                                    {{ Str::limit($transaction->particulars, 60) }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $transaction->date->format('M d, Y') }}
                                </p>
                            </div>
                            <div class="text-right ml-4">
                                <p class="text-sm font-bold {{ $transaction->balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Balance: {{ number_format($transaction->balance, 2) }}
                                </p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-3 border-t border-gray-200">
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-1">Debit</p>
                                @if($transaction->debit)
                                <p class="text-sm font-semibold text-red-600">{{ number_format($transaction->debit, 2) }}</p>
                                @else
                                <p class="text-sm text-gray-400">-</p>
                                @endif
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-1">Credit</p>
                                @if($transaction->credit)
                                <p class="text-sm font-semibold text-green-600">{{ number_format($transaction->credit, 2) }}</p>
                                @else
                                <p class="text-sm text-gray-400">-</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-receipt text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Transactions Found</h3>
                    <p class="text-gray-500">There are no transactions for this ledger in the selected period.</p>
                </div>
                @endif

                <!-- Pagination -->
                @if(isset($transactionLines) && $transactionLines->hasPages())
                <div class="mt-6 pt-6 border-t border-gray-200">
                    {{ $transactionLines->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Hidden Print Template -->
<div id="printTemplate" class="hidden">
    <div class="print-container">
        <style>
            @media print {
                @page {
                    size: A4;
                    margin: 1in 0.5in 0.75in 0.5in;
                }

                body {
                    font-family: 'Times New Roman', serif;
                    font-size: 12px;
                    line-height: 1.4;
                    color: #000;
                    background: white;
                }

                .print-container {
                    width: 100%;
                    max-width: none;
                    margin: 0;
                    padding: 0;
                }

                .print-header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 2px solid #000;
                    padding-bottom: 15px;
                }

                .print-title {
                    font-size: 24px;
                    font-weight: bold;
                    margin-bottom: 5px;
                }

                .print-subtitle {
                    font-size: 16px;
                    margin-bottom: 10px;
                }

                .print-period {
                    font-size: 14px;
                    font-style: italic;
                }

                .print-info-section {
                    margin-bottom: 25px;
                }

                .print-info-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                    margin-bottom: 20px;
                }

                .print-info-item {
                    display: flex;
                    justify-content: space-between;
                    padding: 5px 0;
                    border-bottom: 1px dotted #ccc;
                }

                .print-info-label {
                    font-weight: bold;
                }

                .print-balance-summary {
                    background: #f8f9fa;
                    padding: 15px;
                    border: 1px solid #000;
                    margin-bottom: 25px;
                }

                .print-balance-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr 1fr 1fr;
                    gap: 15px;
                    text-align: center;
                }

                .print-balance-item {
                    padding: 10px;
                    border: 1px solid #ddd;
                }

                .print-balance-label {
                    font-size: 10px;
                    text-transform: uppercase;
                    margin-bottom: 5px;
                }

                .print-balance-value {
                    font-size: 14px;
                    font-weight: bold;
                }

                .print-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }

                .print-table th,
                .print-table td {
                    border: 1px solid #000;
                    padding: 8px;
                    text-align: left;
                }

                .print-table th {
                    background: #f0f0f0;
                    font-weight: bold;
                    text-align: center;
                    font-size: 11px;
                }

                .print-table td {
                    font-size: 10px;
                }

                .print-table .text-right {
                    text-align: right;
                }

                .print-table .text-center {
                    text-align: center;
                }

                .print-footer {
                    margin-top: 30px;
                    padding-top: 15px;
                    border-top: 1px solid #000;
                    font-size: 10px;
                    text-align: center;
                }

                .page-break {
                    page-break-before: always;
                }

                .no-print {
                    display: none !important;
                }

                .print-page-header {
                    position: fixed;
                    top: -0.75in;
                    left: 0;
                    right: 0;
                    height: 0.5in;
                    font-size: 10px;
                    font-family: 'Times New Roman', serif;
                    border-bottom: 1px solid #000;
                    padding-bottom: 5px;
                    background: white;
                    z-index: 1000;
                }

                .print-page-footer {
                    position: fixed;
                    bottom: -0.5in;
                    left: 0;
                    right: 0;
                    height: 0.3in;
                    font-size: 10px;
                    font-family: 'Times New Roman', serif;
                    text-align: center;
                    border-top: 1px solid #000;
                    padding-top: 5px;
                    background: white;
                    z-index: 1000;
                }
            }
        </style>

        <!-- Print Page Header (appears on every page) -->
        <div class="print-page-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span>{{ date('n/j/y, g:i A') }}</span>
                <span style="font-weight: bold;">Ledger Statement - {{ $ledger->name }}</span>
                <span>Page <span class="page-number"></span></span>
            </div>
        </div>

        <!-- Print Page Footer (appears on every page) -->
        <div class="print-page-footer">
            <div>Generated on: {{ date('d/m/Y H:i:s') }} | Total Transactions: {{ $transactions->count() ?? 0 }}</div>
        </div>

        <!-- Print Header -->
        <div class="print-header">
            <div class="print-title">LEDGER STATEMENT</div>
            <div class="print-subtitle">{{ $ledger->name }}</div>
            <div class="print-period">Period: {{ $currentMonthName ?? date('F') }} {{ $currentYear ?? date('Y') }}</div>
        </div>

        <!-- Ledger Information -->
        <div class="print-info-section">
            <h3 style="margin-bottom: 15px; font-size: 16px; border-bottom: 1px solid #000; padding-bottom: 5px;">Ledger Information</h3>
            <div class="print-info-grid">
                <div>
                    <div class="print-info-item">
                        <span class="print-info-label">Ledger ID:</span>
                        <span>{{ $ledger->id }}</span>
                    </div>
                    <div class="print-info-item">
                        <span class="print-info-label">Ledger Type:</span>
                        <span>{{ $ledger->ledger_type ?? 'N/A' }}</span>
                    </div>
                </div>
                <div>
                    <div class="print-info-item">
                        <span class="print-info-label">Balance Type:</span>
                        <span>{{ $ledger->balance_type }}</span>
                    </div>
                    <div class="print-info-item">
                        <span class="print-info-label">Status:</span>
                        <span>{{ ucfirst($ledger->status) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Balance Summary -->
        <div class="print-balance-summary">
            <h3 style="margin-bottom: 15px; font-size: 16px; text-align: center;">Balance Summary</h3>
            <div class="print-balance-grid">
                <div class="print-balance-item">
                    <div class="print-balance-label">Opening Balance</div>
                    <div class="print-balance-value">{{ number_format($ledger->opening_balance ?? 0, 2) }}</div>
                </div>
                <div class="print-balance-item">
                    <div class="print-balance-label">Total Debits</div>
                    <div class="print-balance-value">{{ number_format($totalDebits ?? 0, 2) }}</div>
                </div>
                <div class="print-balance-item">
                    <div class="print-balance-label">Total Credits</div>
                    <div class="print-balance-value">{{ number_format($totalCredits ?? 0, 2) }}</div>
                </div>
                <div class="print-balance-item">
                    <div class="print-balance-label">Current Balance</div>
                    <div class="print-balance-value">{{ number_format($ledger->current_balance, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Transaction History -->
        @if(isset($transactions) && $transactions->count() > 0)
        <div>
            <h3 style="margin-bottom: 15px; font-size: 16px; border-bottom: 1px solid #000; padding-bottom: 5px;">Transaction History</h3>
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">Date</th>
                        <th style="width: 45%;">Particulars</th>
                        <th style="width: 14%;">Debit</th>
                        <th style="width: 14%;">Credit</th>
                        <th style="width: 15%;">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $index => $transaction)
                    <tr>
                        <td class="text-center">{{ $transaction->date->format('d/m/Y') }}</td>
                        <td>{{ $transaction->particulars }}</td>
                        <td class="text-right">
                            @if($transaction->debit)
                            {{ number_format($transaction->debit, 2) }}
                            @else
                            -
                            @endif
                        </td>
                        <td class="text-right">
                            @if($transaction->credit)
                            {{ number_format($transaction->credit, 2) }}
                            @else
                            -
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($transaction->balance, 2) }}</td>
                    </tr>
                    @if(($index + 1) % 15 == 0 && $index + 1 < $transactions->count())
                </tbody>
            </table>
            <div class="page-break"></div>
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">Date</th>
                        <th style="width: 45%;">Particulars</th>
                        <th style="width: 14%;">Debit</th>
                        <th style="width: 14%;">Credit</th>
                        <th style="width: 15%;">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 40px; border: 1px solid #ddd;">
            <h3>No Transactions Found</h3>
            <p>There are no transactions for this ledger in the selected period.</p>
        </div>
        @endif

        <!-- Print Footer -->
        <div class="print-footer">
            <p>Generated on: {{ date('d/m/Y H:i:s') }} | Total Transactions: {{ $transactions->count() ?? 0 }}</p>
            <p>This is a computer-generated document and does not require a signature.</p>
        </div>
    </div>
</div>

<script>
    function refreshBalance() {
        const btn = document.getElementById('refreshBalanceBtn');
        const icon = btn.querySelector('i');

        // Disable button and show loading state
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        icon.classList.add('fa-spin');

        fetch("{{ route('admin.ledgers.recalculate', $ledger) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Reset button state
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                icon.classList.remove('fa-spin');

                if (data.success) {
                    // Show success message briefly before reload
                    showNotification('Balance refreshed successfully!', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification('Failed to refresh balance: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                // Reset button state
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                icon.classList.remove('fa-spin');

                console.error('Error:', error);
                showNotification('Error refreshing balance. Please try again.', 'error');
            });
    }

    function printLedger() {
        // Get the print template content
        const printContent = document.getElementById('printTemplate').innerHTML;

        // Create a new window for printing
        const printWindow = window.open('', '_blank', 'width=800,height=600');

        // Write the content to the new window
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Ledger Statement - {{ $ledger->name }}</title>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
            </head>
            <body>
                ${printContent}
            </body>
            </html>
        `);

        // Close the document and focus on the window
        printWindow.document.close();
        printWindow.focus();

        // Wait for content to load then print
        setTimeout(() => {
            printWindow.print();

            // Close the window after printing (optional)
            printWindow.onafterprint = function() {
                printWindow.close();
            };
        }, 500);
    }

    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white font-medium transition-all duration-300 transform translate-x-full ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            'bg-blue-500'
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Add smooth scrolling for better UX
    document.addEventListener('DOMContentLoaded', function() {
        // Add loading animation to month navigation links
        const monthNavLinks = document.querySelectorAll('a[href*="month="]');
        monthNavLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.add('fa-spin');
                }
            });
        });
    });
</script>

@endsection