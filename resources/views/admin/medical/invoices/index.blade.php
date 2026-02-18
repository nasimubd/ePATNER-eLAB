@extends('admin.layouts.app')

@section('content')
@php
$user = Auth::user();
$printingUnpaidEnabled = \App\Models\Setting::get('printing_invoice_unpaid', false);
$printingUnpaidEnabled = filter_var($printingUnpaidEnabled, FILTER_VALIDATE_BOOLEAN);
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-2 sm:p-4">
    <div class="max-w-7xl mx-auto">
        {{-- Enhanced Header --}}
        <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl overflow-hidden border border-white/20 mb-4">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-center sm:text-left">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">
                            <svg class="inline-block w-8 h-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Medical Invoices Management
                        </h1>
                        <p class="text-blue-100 text-sm">Manage patient invoices and billing records</p>
                    </div>

                    <div class="w-full sm:w-auto">
                        <a href="{{ route('admin.medical.invoices.create') }}" id="createInvoiceBtn"
                            class="group relative overflow-hidden bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto flex items-center justify-center">
                            <div class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                            <span class="relative flex items-center justify-center">
                                <svg id="defaultPlusIcon" class="w-5 h-5 mr-2 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <svg id="spinnerIcon" class="hidden w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span id="buttonText">Create Invoice</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Enhanced Filter Section -->
            <div class="p-4 bg-gradient-to-r from-gray-50 to-blue-50 border-t border-gray-200">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" id="search" name="search" value="{{ request('search') }}"
                            placeholder="Invoice ID or Patient..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <!-- Status Filter -->
                    <div class="relative">
                        <select id="status" name="status" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </div>

                    <!-- Date Range -->
                    <div class="relative">
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" id="filterBtn"
                            class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md text-sm flex items-center justify-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <span class="hidden sm:inline">Filter</span>
                        </button>

                        <a href="{{ route('admin.medical.invoices.index') }}"
                            class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md text-sm text-center flex items-center justify-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span class="hidden sm:inline">Reset</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-r-xl shadow-lg" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        {{-- Mobile Cards - Only show on small screens --}}
        <div class="lg:hidden space-y-3">
            @forelse($invoices as $invoice)
            @php
            $remainingAmount = (float)($invoice->grand_total ?? 0) - (float)($invoice->paid_amount ?? 0);
            $canCollect = $remainingAmount > 0;
            $printingUnpaidEnabled = \App\Models\Setting::get('printing_invoice_unpaid', false);
            $printingUnpaidEnabled = filter_var($printingUnpaidEnabled, FILTER_VALIDATE_BOOLEAN);
            $isUnpaid = $invoice->status !== 'paid';
            $approvedPosRequest = $invoice->printRequests()->where('request_type', 'pos')->where('status', 'approved')->first();
            $approvedA5Request = $invoice->printRequests()->where('request_type', 'a5')->where('status', 'approved')->first();
            $pendingPosRequest = $invoice->printRequests()->where('request_type', 'pos')->where('status', 'pending')->first();
            $pendingA5Request = $invoice->printRequests()->where('request_type', 'a5')->where('status', 'pending')->first();
            @endphp

            <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-md border border-white/20 overflow-hidden hover:shadow-lg transition-all duration-300">
                <!-- Mobile Card Header -->
                <div class="bg-gradient-to-r from-slate-50 to-blue-50 px-4 py-3 border-b border-gray-100">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-sm">{{ $invoice->invoice_number }}</h3>
                                <p class="text-xs text-gray-500">{{ $invoice->patient->full_name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                    $invoice->status == 'paid' ? 'bg-green-100 text-green-800' : 
                                    ($invoice->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') 
                                }}">
                                @if($invoice->status == 'paid')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Paid
                                @elseif($invoice->status == 'pending')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                Pending
                                @else
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Cancelled
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Mobile Card Body -->
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Date</p>
                            <p class="text-sm font-medium text-gray-900">{{ $invoice->invoice_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Total Amount</p>
                            <p class="text-lg font-bold text-gray-900">৳{{ number_format($invoice->grand_total, 2) }}</p>
                        </div>
                    </div>

                    <!-- Amount Information -->
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg p-3 mb-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Paid Amount</p>
                                <p class="text-lg font-bold {{ $invoice->paid_amount > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    ৳{{ number_format($invoice->paid_amount, 2) }}
                                </p>
                            </div>
                            @if($remainingAmount > 0)
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Remaining</p>
                                <p class="text-sm font-semibold text-red-600">৳{{ number_format($remainingAmount, 2) }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Mobile Action Buttons -->
                    <div class="flex space-x-2">
                        <!-- View Button -->
                        <a href="{{ route('admin.medical.invoices.show', $invoice) }}"
                            class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-xs font-medium py-2 px-3 rounded-lg transition-all duration-300 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View
                        </a>

                        <!-- Discount Button -->
                        <button class="discount-btn flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white text-xs font-medium py-2 px-3 rounded-lg transition-all duration-300 flex items-center justify-center"
                            data-invoice-id="{{ $invoice->id }}"
                            data-invoice-number="{{ $invoice->invoice_number }}"
                            data-patient-name="{{ $invoice->patient->full_name }}"
                            data-remaining-amount="{{ $remainingAmount }}">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Discount
                        </button>

                        <button class="whatsapp-btn flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white text-xs font-medium py-2 px-3 rounded-lg transition-all duration-300 flex items-center justify-center"
                            data-invoice-id="{{ $invoice->id }}"
                            data-invoice-number="{{ $invoice->invoice_number }}"
                            data-patient-name="{{ $invoice->patient->full_name }}">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                            </svg>
                            WhatsApp
                        </button>

                        <!-- Print Button -->
                        @if($currentBusiness && $currentBusiness->enable_a5_printing)
                        @if(auth()->user()->hasRole('admin') || !$isUnpaid || !$printingUnpaidEnabled || ($approvedA5Request && ($approvedA5Request->allowed_prints == 0 || $approvedA5Request->prints_used < $approvedA5Request->allowed_prints)))
                            <a href="{{ route('admin.medical.invoices.print-a5', $invoice) }}" target="_blank"
                                class="flex-1 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white text-xs font-medium py-2 px-3 rounded-lg transition-all duration-300 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                A5 Print
                            </a>
                            @elseif($pendingA5Request)
                            <button disabled
                                class="flex-1 bg-gradient-to-r from-gray-400 to-gray-500 text-white text-xs font-medium py-2 px-3 rounded-lg shadow-lg flex items-center justify-center cursor-not-allowed">
                                <svg class="w-4 h-4 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                A5 Request Pending
                            </button>
                            @else
                            <button onclick="requestPrint('a5', '{{ $invoice->id }}')"
                                class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white text-xs font-medium py-2 px-3 rounded-lg transition-all duration-300 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Request A5 Print
                            </button>
                            @endif
                            @else
                            @if(auth()->user()->hasRole('admin') || !$isUnpaid || !$printingUnpaidEnabled || ($approvedPosRequest && ($approvedPosRequest->allowed_prints == 0 || $approvedPosRequest->prints_used < $approvedPosRequest->allowed_prints)))
                                <a href="{{ route('admin.medical.invoices.print', $invoice) }}" target="_blank"
                                    class="flex-1 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white text-xs font-medium py-2 px-3 rounded-lg transition-all duration-300 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    POS Print
                                </a>
                                @elseif($pendingPosRequest)
                                <button disabled
                                    class="flex-1 bg-gradient-to-r from-gray-400 to-gray-500 text-white text-xs font-medium py-2 px-3 rounded-lg shadow-lg flex items-center justify-center cursor-not-allowed">
                                    <svg class="w-4 h-4 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    POS Request Pending
                                </button>
                                @else
                                <button onclick="requestPrint('pos', '{{ $invoice->id }}')"
                                    class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white text-xs font-medium py-2 px-3 rounded-lg transition-all duration-300 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Request POS Print
                                </button>
                                @endif
                                @endif

                                <!-- Collection Button -->
                                @if($canCollect)
                                <button class="collection-btn flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white text-xs font-medium py-2 px-3 rounded-lg transition-all duration-300 flex items-center justify-center"
                                    data-invoice-id="{{ $invoice->id }}"
                                    data-invoice-number="{{ $invoice->invoice_number }}"
                                    data-patient-name="{{ $invoice->patient->full_name }}"
                                    data-remaining-amount="{{ $remainingAmount }}">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Collect
                                </button>
                                @else
                                <button class="flex-1 bg-gray-300 text-gray-500 text-xs font-medium py-2 px-3 rounded-lg cursor-not-allowed flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Paid
                                </button>
                                @endif

                                <!-- Delete Button -->
                                <button class="delete-btn bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white text-xs font-medium py-2 px-3 rounded-lg transition-all duration-300 flex items-center justify-center"
                                    data-invoice-id="{{ $invoice->id }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-md border border-white/20 p-8 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No invoices found</h3>
                <p class="text-gray-500 mb-4">Get started by creating your first medical invoice.</p>
                <a href="{{ route('admin.medical.invoices.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg transition-all duration-300">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Invoice
                </a>
            </div>
            @endforelse
        </div>

        {{-- Desktop Table - Only show on large screens --}}
        <div class="hidden lg:block bg-white/90 backdrop-blur-sm shadow-xl rounded-2xl overflow-hidden border border-white/20">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-blue-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>Invoice</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>Patient</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Date</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Status</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                    <span>Total Amount</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                    <span>Actions</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($invoices as $invoice)
                        @php
                        $remainingAmount = (float)($invoice->grand_total ?? 0) - (float)($invoice->paid_amount ?? 0);
                        $canCollect = $remainingAmount > 0;
                        $printingUnpaidEnabled = \App\Models\Setting::get('printing_invoice_unpaid', false);
                        $printingUnpaidEnabled = filter_var($printingUnpaidEnabled, FILTER_VALIDATE_BOOLEAN);
                        $isUnpaid = $invoice->status !== 'paid';
                        $approvedPosRequest = $invoice->printRequests()->where('request_type', 'pos')->where('status', 'approved')->first();
                        $approvedA5Request = $invoice->printRequests()->where('request_type', 'a5')->where('status', 'approved')->first();
                        $pendingPosRequest = $invoice->printRequests()->where('request_type', 'pos')->where('status', 'pending')->first();
                        $pendingA5Request = $invoice->printRequests()->where('request_type', 'a5')->where('status', 'pending')->first();
                        @endphp
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-300">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $invoice->invoice_number }}</div>
                                        <div class="text-xs text-gray-500">{{ $invoice->created_at->format('M d, Y H:i') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $invoice->patient->full_name }}</div>
                                        <div class="text-xs text-gray-500">ID: {{ $invoice->patient->patient_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-medium">{{ $invoice->invoice_date->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $invoice->invoice_date->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ 
                                        $invoice->status == 'paid' ? 'bg-green-100 text-green-800' : 
                                        ($invoice->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') 
                                    }}">
                                    @if($invoice->status == 'paid')
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Paid
                                    @elseif($invoice->status == 'pending')
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                    Pending
                                    @else
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    Cancelled
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">৳{{ number_format($invoice->grand_total, 2) }}</div>
                                @if($remainingAmount > 0)
                                <div class="text-xs text-red-600 font-medium">Remaining: ৳{{ number_format($remainingAmount, 2) }}</div>
                                @else
                                <div class="text-xs text-green-600 font-medium">Fully Paid</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- View Button -->
                                    <a href="{{ route('admin.medical.invoices.show', $invoice) }}"
                                        class="group relative inline-flex items-center justify-center p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-all duration-300"
                                        title="View Invoice">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    <!-- Print Button -->
                                    @if($currentBusiness && $currentBusiness->enable_a5_printing)
                                    @if(auth()->user()->hasRole('admin') || !$isUnpaid || !$printingUnpaidEnabled || ($approvedA5Request && $approvedA5Request->allowed_prints > 0 && $approvedA5Request->prints_used < $approvedA5Request->allowed_prints))
                                        <a href="{{ route('admin.medical.invoices.print-a5', $invoice) }}" target="_blank"
                                            class="group relative inline-flex items-center justify-center p-2 text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded-lg transition-all duration-300"
                                            title="A5 Print Invoice">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                        </a>
                                        @elseif($pendingA5Request)
                                        <span class="group relative inline-flex items-center justify-center p-2 text-gray-400 cursor-not-allowed rounded-lg"
                                            title="A5 Print Request Pending">
                                            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                        </span>
                                        @else
                                        <button onclick="requestPrint('a5', '{{ $invoice->id }}')"
                                            class="group relative inline-flex items-center justify-center p-2 text-orange-600 hover:text-orange-800 hover:bg-orange-50 rounded-lg transition-all duration-300"
                                            title="Request A5 Print Approval">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                        @endif
                                        @else
                                        @if(auth()->user()->hasRole('admin') || !$isUnpaid || !$printingUnpaidEnabled || ($approvedPosRequest && $approvedPosRequest->allowed_prints > 0 && $approvedPosRequest->prints_used < $approvedPosRequest->allowed_prints))
                                            <a href="{{ route('admin.medical.invoices.print', $invoice) }}" target="_blank"
                                                class="group relative inline-flex items-center justify-center p-2 text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded-lg transition-all duration-300"
                                                title="POS Print Invoice">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                            </a>
                                            @elseif($pendingPosRequest)
                                            <span class="group relative inline-flex items-center justify-center p-2 text-gray-400 cursor-not-allowed rounded-lg"
                                                title="POS Print Request Pending">
                                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                            </span>
                                            @else
                                            <button onclick="requestPrint('pos', '{{ $invoice->id }}')"
                                                class="group relative inline-flex items-center justify-center p-2 text-orange-600 hover:text-orange-800 hover:bg-orange-50 rounded-lg transition-all duration-300"
                                                title="Request POS Print Approval">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                            @endif
                                            @endif

                                            <button class="whatsapp-btn group relative inline-flex items-center justify-center p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-all duration-300"
                                                data-invoice-id="{{ $invoice->id }}"
                                                data-invoice-number="{{ $invoice->invoice_number }}"
                                                data-patient-name="{{ $invoice->patient->full_name }}"
                                                title="Share via WhatsApp">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                                                </svg>
                                            </button>

                                            <!-- Collection Button -->
                                            @if($canCollect)
                                            <button class="collection-btn group relative inline-flex items-center justify-center p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-all duration-300"
                                                data-invoice-id="{{ $invoice->id }}"
                                                data-invoice-number="{{ $invoice->invoice_number }}"
                                                data-patient-name="{{ $invoice->patient->full_name }}"
                                                data-remaining-amount="{{ $remainingAmount }}"
                                                title="Collect Payment">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </button>
                                            @else
                                            <span class="group relative inline-flex items-center justify-center p-2 text-gray-400 cursor-not-allowed rounded-lg"
                                                title="Fully Paid">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                            @endif

                                            <!-- Discount Button -->
                                            @if(auth()->user()->hasRole('admin'))
                                            <button class="discount-btn group relative inline-flex items-center justify-center p-2 text-orange-600 hover:text-orange-800 hover:bg-orange-50 rounded-lg transition-all duration-300"
                                                data-invoice-id="{{ $invoice->id }}"
                                                data-invoice-number="{{ $invoice->invoice_number }}"
                                                data-patient-name="{{ $invoice->patient->full_name }}"
                                                data-remaining-amount="{{ $remainingAmount }}"
                                                title="Apply Discount">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                </svg>
                                            </button>
                                            @endif

                                            <!-- Delete Button -->
                                            <button class="delete-btn group relative inline-flex items-center justify-center p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-all duration-300"
                                                data-invoice-id="{{ $invoice->id }}"
                                                title="Delete Invoice">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No invoices found</h3>
                                    <p class="text-gray-500 mb-4">Get started by creating your first medical invoice.</p>
                                    <a href="{{ route('admin.medical.invoices.create') }}"
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg transition-all duration-300">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Create Invoice
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($invoices->hasPages())
        <div class="mt-6 bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-white/20 px-6 py-4">
            {{ $invoices->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Centered Collection Modal with Discount Option --}}
<div id="collectionModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-auto my-8 transform transition-all">
        {{-- Modal Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-t-2xl p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Collect Payment</h3>
                        <p class="text-blue-100 text-sm">Process invoice payment</p>
                    </div>
                </div>
                <button type="button" id="closeCollectionModal" class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Modal Body --}}
        <div class="p-4 sm:p-6 max-h-[70vh] overflow-y-auto">
            <form id="collectionForm" class="space-y-4">
                <input type="hidden" id="invoiceId" name="invoice_id">

                {{-- Patient Info Card --}}
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900" id="patientName">Patient Name</h4>
                            <p class="text-sm text-gray-600" id="invoiceNumber">Invoice Number</p>
                        </div>
                    </div>
                </div>

                {{-- Amount Info --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-red-50 rounded-xl p-4 border border-red-100">
                        <label class="block text-sm font-medium text-red-700 mb-1">Remaining Amount</label>
                        <div class="text-xl font-bold text-red-600" id="remainingAmount">৳0.00</div>
                    </div>
                    <div class="bg-orange-50 rounded-xl p-4 border border-orange-100">
                        <label class="block text-sm font-medium text-orange-700 mb-1">Discount</label>
                        <div class="text-xl font-bold text-orange-600" id="discountAmount">৳0.00</div>
                    </div>
                    <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                        <label class="block text-sm font-medium text-green-700 mb-1">Collecting</label>
                        <div class="text-xl font-bold text-green-600" id="collectingAmount">৳0.00</div>
                    </div>
                </div>



                {{-- Collection Amount Input --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Collection Amount *</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-lg font-medium"></span>
                        </div>
                        <input type="number" id="collectionAmount" name="amount" step="0.01" min="0"
                            class="w-full pl-8 pr-4 py-3 text-lg font-semibold border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none [-moz-appearance:textfield]"
                            placeholder="0.00" required>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>Minimum: ৳0.01</span>
                        <button type="button" id="setMaxAmount" class="text-blue-600 hover:text-blue-800 font-medium">Set Maximum</button>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Payment Method *</label>
                    <select id="paymentMethod" name="payment_method" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
                        <option value="">Select Payment Method</option>
                        <option value="cash" selected>💵 Cash</option>
                        <option value="card">💳 Card</option>
                        <option value="bank_transfer">🏦 Bank Transfer</option>
                        <option value="mobile_banking">📱 Mobile Banking</option>
                    </select>
                </div>

                {{-- Notes --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Notes (Optional)</label>
                    <textarea id="notes" name="notes" rows="3"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"
                        placeholder="Add any notes about this payment..."></textarea>
                </div>
            </form>
        </div>

        {{-- Modal Footer --}}
        <div class="bg-gray-50 rounded-b-2xl p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="button" id="cancelCollectionBtn"
                    class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl transition-all duration-200 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </button>
                <button type="submit" form="collectionForm" id="submitCollection"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center">
                    <svg id="submitIcon" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg id="submitSpinner" class="hidden w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="submitText">Collect Payment</span>
                </button>
            </div>
        </div>
    </div>
</div>
</div>

{{-- Discount Modal --}}
<div id="discountModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-auto my-8 transform transition-all">
        {{-- Modal Header --}}
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-t-2xl p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Apply Discount</h3>
                        <p class="text-orange-100 text-sm">Apply discount to invoice without collecting payment</p>
                    </div>
                </div>
                <button type="button" id="closeDiscountModal" class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Modal Body --}}
        <div class="p-4 sm:p-6 max-h-[70vh] overflow-y-auto">
            <form id="discountForm" class="space-y-4">
                <input type="hidden" id="discountInvoiceId" name="invoice_id">

                {{-- Patient Info Card --}}
                <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-xl p-4 border border-orange-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900" id="discountPatientName">Patient Name</h4>
                            <p class="text-sm text-gray-600" id="discountInvoiceNumber">Invoice Number</p>
                        </div>
                    </div>
                </div>

                {{-- Amount Info --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-red-50 rounded-xl p-4 border border-red-100">
                        <label class="block text-sm font-medium text-red-700 mb-1">Current Remaining</label>
                        <div class="text-xl font-bold text-red-600" id="discountRemainingAmount">0.00</div>
                    </div>
                    <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                        <label class="block text-sm font-medium text-green-700 mb-1">After Discount</label>
                        <div class="text-xl font-bold text-green-600" id="discountNewAmount">৳0.00</div>
                    </div>
                </div>

                {{-- Discount Type --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Discount Type *</label>
                    <select id="discountTypeSelect" name="discount_type" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200" required>
                        <option value="">Select Discount Type</option>
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount ()</option>
                    </select>
                </div>

                {{-- Discount Value --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Discount Value *</label>
                    <div class="relative">
                        <input type="number" id="discountValueInput" name="discount_value" step="0.01" min="0"
                            class="w-full px-4 py-3 text-lg font-semibold border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none [-moz-appearance:textfield]"
                            placeholder="0" required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span id="discountUnit" class="text-gray-500 text-lg font-medium">%</span>
                        </div>
                    </div>
                </div>
                {{-- Discount Reason --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Discount Reason</label>
                    <input type="text" id="discountReasonInput" name="discount_reason"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                        placeholder="e.g., Senior citizen discount, Loyalty discount">
                </div>
            </form>
        </div>

        {{-- Modal Footer --}}
        <div class="bg-gray-50 rounded-b-2xl p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="button" id="cancelDiscountBtn"
                    class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl transition-all duration-200 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </button>
                <button type="submit" form="discountForm" id="submitDiscount"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center">
                    <svg id="discountSubmitIcon" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg id="discountSubmitSpinner" class="hidden w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="discountSubmitText">Apply Discount</span>
                </button>
            </div>
        </div>
    </div>
</div>
{{-- WhatsApp Share Modal --}}
<div id="whatsappModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto transform transition-all">
            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-t-2xl p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Share via WhatsApp</h3>
                            <p class="text-green-100 text-sm">Send invoice receipt</p>
                        </div>
                    </div>
                    <button type="button" id="closeWhatsappModal" class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="p-4 sm:p-6">
                <form id="whatsappForm" class="space-y-4">
                    <input type="hidden" id="whatsappInvoiceId" name="invoice_id">

                    {{-- Invoice Info Card --}}
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900" id="whatsappPatientName">Patient Name</h4>
                                <p class="text-sm text-gray-600" id="whatsappInvoiceNumber">Invoice Number</p>
                            </div>
                        </div>
                    </div>

                    {{-- WhatsApp Number Input --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">WhatsApp Number *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm font-medium">+880</span>
                            </div>
                            <input type="tel" id="whatsappNumber" name="whatsapp_number"
                                class="w-full pl-16 pr-4 py-3 text-lg font-semibold border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                placeholder="1XXXXXXXXX" pattern="[0-9]{10,11}" required>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Enter 10-11 digit number without +880</span>
                            <span class="text-green-600">Example: 1712345678</span>
                        </div>
                    </div>

                    {{-- Custom Message --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Custom Message (Optional)</label>
                        <textarea id="whatsappMessage" name="message" rows="3"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 resize-none"
                            placeholder="Add a personal message...">Hello! Here is your medical invoice receipt from our clinic. Thank you for choosing our services.</textarea>

                        <!-- Powered by ePATNER - Non-editable -->
                        <div class="mt-2 p-3 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <span class="text-sm text-blue-700 font-medium">Powered by</span>
                                    <a href="https://epatner.com" target="_blank" rel="noopener noreferrer"
                                        class="text-sm font-bold text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200">
                                        ePATNER
                                    </a>
                                </div>
                                <div class="text-xs text-blue-500">
                                    <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    Protected
                                </div>
                            </div>
                            <p class="text-xs text-blue-600 mt-1">This footer will be automatically added to all WhatsApp messages</p>
                        </div>
                    </div>


                    {{-- Share Options --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Share Options</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" id="includePDF" name="include_pdf" checked
                                    class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
                                <span class="ml-2 text-sm text-gray-700">Include PDF attachment link</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" id="includeDetails" name="include_details" checked
                                    class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
                                <span class="ml-2 text-sm text-gray-700">Include invoice summary</span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Modal Footer --}}
            <div class="bg-gray-50 rounded-b-2xl p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button" id="cancelWhatsappBtn"
                        class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl transition-all duration-200 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </button>
                    <button type="submit" form="whatsappForm" id="submitWhatsapp"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center">
                        <svg id="whatsappSubmitIcon" class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                        </svg>
                        <svg id="whatsappSubmitSpinner" class="hidden w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="whatsappSubmitText">Share via WhatsApp</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>



{{-- Print Request Modal --}}
<div id="printRequestModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Request Print Access</h3>
            <form id="printRequestForm">
                @csrf
                <input type="hidden" name="request_type" id="requestType">
                <input type="hidden" name="invoice_id" id="modalInvoiceId">
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason for Print Request</label>
                    <textarea name="reason" id="reason" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Please explain why you need to print this unpaid invoice..." required></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Scripts --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function requestPrint(type, invoiceId) {
        document.getElementById('requestType').value = type;
        document.getElementById('modalInvoiceId').value = invoiceId;
        document.getElementById('modalTitle').textContent = `Request ${type.toUpperCase()} Print Access`;
        document.getElementById('printRequestModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('printRequestModal').classList.add('hidden');
        document.getElementById('printRequestForm').reset();
    }

    document.getElementById('printRequestForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('{{ route("admin.print-requests.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    // Show success message
                    const successDiv = document.createElement('div');
                    successDiv.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4';
                    successDiv.textContent = data.message;
                    document.querySelector('.min-h-screen').insertBefore(successDiv, document.querySelector('.min-h-screen').firstChild);

                    // Reload page after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting the request.');
            });
    });

    // Close modal when clicking outside
    document.getElementById('printRequestModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        let currentInvoiceId = null;
        let maxCollectionAmount = 0;
        let isSubmitting = false;

        // DOM elements
        const collectionModal = document.getElementById('collectionModal');
        const collectionForm = document.getElementById('collectionForm');
        const invoiceIdInput = document.getElementById('invoiceId');
        const patientNameElement = document.getElementById('patientName');
        const invoiceNumberElement = document.getElementById('invoiceNumber');
        const remainingAmountElement = document.getElementById('remainingAmount');
        const collectingAmountElement = document.getElementById('collectingAmount');
        const collectionAmountInput = document.getElementById('collectionAmount');
        const paymentMethodSelect = document.getElementById('paymentMethod');
        const notesTextarea = document.getElementById('notes');
        const setMaxAmountBtn = document.getElementById('setMaxAmount');
        const submitBtn = document.getElementById('submitCollection');
        const submitIcon = document.getElementById('submitIcon');
        const submitSpinner = document.getElementById('submitSpinner');
        const submitText = document.getElementById('submitText');



        // Open collection modal function
        function openCollectionModal(invoiceId, invoiceNumber, patientName, remainingAmount) {
            currentInvoiceId = invoiceId;
            maxCollectionAmount = parseFloat(remainingAmount);

            // Populate modal data
            invoiceIdInput.value = invoiceId;
            patientNameElement.textContent = patientName;
            invoiceNumberElement.textContent = invoiceNumber;
            remainingAmountElement.textContent = '৳' + parseFloat(remainingAmount).toFixed(2);

            // Reset form
            collectionAmountInput.value = '';
            paymentMethodSelect.value = '';
            notesTextarea.value = '';
            collectingAmountElement.textContent = '৳0.00';



            // Show modal
            collectionModal.classList.remove('hidden');

            // Focus on amount input
            setTimeout(() => {
                collectionAmountInput.focus();
            }, 300);
        }

        // Close collection modal function
        function closeCollectionModal() {
            collectionModal.classList.add('hidden');
            currentInvoiceId = null;
            maxCollectionAmount = 0;
        }

        // Update collecting amount display
        function updateCollectingAmount() {
            const amount = parseFloat(collectionAmountInput.value) || 0;
            collectingAmountElement.textContent = '৳' + amount.toFixed(2);
        }



        // Collection button event listeners
        document.querySelectorAll('.collection-btn').forEach(button => {
            button.addEventListener('click', function() {
                const invoiceId = this.dataset.invoiceId;
                const invoiceNumber = this.dataset.invoiceNumber;
                const patientName = this.dataset.patientName;
                const remainingAmount = this.dataset.remainingAmount;
                openCollectionModal(invoiceId, invoiceNumber, patientName, remainingAmount);
            });
        });

        // Close modal event listeners
        document.getElementById('closeCollectionModal').addEventListener('click', closeCollectionModal);
        document.getElementById('cancelCollectionBtn').addEventListener('click', closeCollectionModal);

        // Close modal when clicking outside
        collectionModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCollectionModal();
            }
        });

        // Set max amount button
        setMaxAmountBtn.addEventListener('click', function() {
            collectionAmountInput.value = Math.max(0, maxCollectionAmount).toFixed(2);
            updateCollectingAmount();
        });

        // Update collecting amount on input
        collectionAmountInput.addEventListener('input', function() {
            // Format input to prevent precision issues
            if (this.value && !isNaN(this.value)) {
                const numValue = Math.round(parseFloat(this.value) * 100) / 100;
                if (numValue.toString() !== this.value && this.value.includes('.')) {
                    this.value = numValue.toString();
                }
            }

            updateCollectingAmount();

            // Real-time validation
            const amount = parseFloat(this.value);
            if (!isNaN(amount) && amount > maxCollectionAmount) {
                this.style.borderColor = '#ef4444';
                this.style.backgroundColor = '#fef2f2';
            } else {
                this.style.borderColor = '';
                this.style.backgroundColor = '';
            }
        });

        // Prevent mouse wheel scrolling on collection amount input
        collectionAmountInput.addEventListener('wheel', function(e) {
            e.preventDefault();
            this.blur();
        });

        // Validate collection amount
        collectionAmountInput.addEventListener('change', function() {
            const amount = parseFloat(this.value);
            if (isNaN(amount) || amount <= 0) {
                this.value = '';
                Swal.fire({
                    title: 'Invalid Amount',
                    text: 'Amount must be greater than 0',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#f59e0b'
                });
            } else if (amount > maxCollectionAmount) {
                this.value = maxCollectionAmount.toFixed(2);
                Swal.fire({
                    title: 'Amount Too High',
                    text: `Amount cannot exceed ৳${maxCollectionAmount.toFixed(2)}`,
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#f59e0b'
                });
            }
            updateCollectingAmount();
        });



        // Collection form submission
        collectionForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (isSubmitting) return;

            const amount = parseFloat(collectionAmountInput.value);
            const paymentMethod = paymentMethodSelect.value;

            // Validation
            if (!amount || amount <= 0) {
                Swal.fire({
                    title: 'Invalid Amount',
                    text: 'Please enter a valid amount greater than 0',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#f59e0b'
                });
                return;
            }

            if (amount > maxCollectionAmount) {
                Swal.fire({
                    title: 'Amount Too High',
                    text: `Amount cannot exceed ৳${maxCollectionAmount.toFixed(2)}`,
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#f59e0b'
                });
                return;
            }

            if (!paymentMethod) {
                Swal.fire({
                    title: 'Payment Method Required',
                    text: 'Please select a payment method',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#f59e0b'
                });
                return;
            }

            // Set loading state
            isSubmitting = true;
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            submitIcon.classList.add('hidden');
            submitSpinner.classList.remove('hidden');
            submitText.textContent = 'Processing...';

            // Disable form inputs
            collectionAmountInput.disabled = true;
            paymentMethodSelect.disabled = true;
            notesTextarea.disabled = true;

            // Prepare data
            const formData = {
                amount: amount,
                payment_method: paymentMethod,
                payment_notes: notesTextarea.value
            };

            // Send request
            fetch(`/admin/medical/invoices/${currentInvoiceId}/collect`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => Promise.reject(data));
                    }
                    return response.json();
                })
                .then(data => {
                    // Reset loading state
                    resetSubmitButton();

                    // Close modal
                    closeCollectionModal();

                    // Show success message
                    let successHtml = `
                        <div class="text-left space-y-2">
                            <div class="flex justify-between">
                                <span class="font-medium">Patient:</span>
                                <span>${patientNameElement.textContent}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Amount Collected:</span>
                                <span class="text-green-600 font-bold">৳${amount.toFixed(2)}</span>
                            </div>`;



                    successHtml += `
                            <div class="flex justify-between">
                                <span class="font-medium">Payment Method:</span>
                                <span>${paymentMethod.charAt(0).toUpperCase() + paymentMethod.slice(1).replace('_', ' ')}</span>
                            </div>
                            <div class="pt-2 border-t">
                                <p class="text-green-600 font-medium text-center">${data.message}</p>
                            </div>
                        </div>
                    `;

                    Swal.fire({
                        title: 'Payment Collected!',
                        html: successHtml,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#10b981',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(() => {
                        // Reload page to reflect changes
                        window.location.reload();
                    });
                })
                .catch(error => {
                    // Reset loading state
                    resetSubmitButton();

                    // Show error message
                    Swal.fire({
                        title: 'Collection Failed!',
                        html: `
                        <div class="text-left space-y-2">
                            <div class="flex justify-between">
                                <span class="font-medium">Patient:</span>
                                <span>${patientNameElement.textContent}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Attempted Amount:</span>
                                <span class="text-red-600 font-bold">৳${amount.toFixed(2)}</span>
                            </div>
                            <div class="pt-2 border-t">
                                <p class="text-red-600 font-medium">${error.message || 'Failed to process payment. Please try again.'}</p>
                            </div>
                            ${error.errors ? `
                                <div class="mt-3 p-3 bg-red-50 rounded-lg">
                                    <p class="font-medium text-red-700 mb-2">Details:</p>
                                    <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                                        ${Object.values(error.errors).flat().map(err => `<li>${err}</li>`).join('')}
                                    </ul>
                                </div>
                            ` : ''}
                        </div>
                    `,
                        icon: 'error',
                        confirmButtonText: 'Try Again',
                        confirmButtonColor: '#ef4444',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });

                    console.error('Collection error:', error);
                });
        });

        // Reset submit button function
        function resetSubmitButton() {
            isSubmitting = false;
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            submitIcon.classList.remove('hidden');
            submitSpinner.classList.add('hidden');
            submitText.textContent = 'Collect Payment';

            // Re-enable form inputs
            collectionAmountInput.disabled = false;
            paymentMethodSelect.disabled = false;
            notesTextarea.disabled = false;
        }

        // Delete button event listeners
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const invoiceId = this.dataset.invoiceId;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Deleting...',
                            text: 'Please wait while we delete the invoice.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch(`/admin/medical/invoices/${invoiceId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: data.message || 'Invoice has been deleted successfully.',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    throw new Error(data.message || 'Failed to delete invoice');
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: error.message || 'Failed to delete invoice'
                                });
                            });
                    }
                });
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Escape key to close modal
            if (e.key === 'Escape' && !collectionModal.classList.contains('hidden')) {
                closeCollectionModal();
            }
        });

        // Auto-focus on amount input when modal opens
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    if (!collectionModal.classList.contains('hidden')) {
                        setTimeout(() => {
                            collectionAmountInput.focus();
                        }, 300);
                    }
                }
            });
        });

        observer.observe(collectionModal, {
            attributes: true,
            attributeFilter: ['class']
        });

        // Add this to your existing script section

        // WhatsApp Modal functionality
        let currentWhatsappInvoiceId = null;
        let isWhatsappSubmitting = false;

        // DOM elements for WhatsApp
        const whatsappModal = document.getElementById('whatsappModal');
        const whatsappForm = document.getElementById('whatsappForm');
        const whatsappInvoiceIdInput = document.getElementById('whatsappInvoiceId');
        const whatsappPatientNameElement = document.getElementById('whatsappPatientName');
        const whatsappInvoiceNumberElement = document.getElementById('whatsappInvoiceNumber');
        const whatsappNumberInput = document.getElementById('whatsappNumber');
        const whatsappMessageTextarea = document.getElementById('whatsappMessage');
        const whatsappSubmitBtn = document.getElementById('submitWhatsapp');
        const whatsappSubmitIcon = document.getElementById('whatsappSubmitIcon');
        const whatsappSubmitSpinner = document.getElementById('whatsappSubmitSpinner');
        const whatsappSubmitText = document.getElementById('whatsappSubmitText');

        // Open WhatsApp modal function
        function openWhatsappModal(invoiceId, invoiceNumber, patientName) {
            currentWhatsappInvoiceId = invoiceId;

            // Populate modal data
            whatsappInvoiceIdInput.value = invoiceId;
            whatsappPatientNameElement.textContent = patientName;
            whatsappInvoiceNumberElement.textContent = invoiceNumber;

            // Reset form
            whatsappNumberInput.value = '';
            whatsappMessageTextarea.value = `Hello! Here is your medical invoice receipt from our clinic. Thank you for choosing our services.

Invoice: ${invoiceNumber}
Patient: ${patientName}
Date: ${new Date().toLocaleDateString()}`;

            // Show modal
            whatsappModal.classList.remove('hidden');
            whatsappModal.style.display = 'flex';

            // Focus on phone number input
            setTimeout(() => {
                whatsappNumberInput.focus();
            }, 300);
        }

        // Close WhatsApp modal function
        function closeWhatsappModal() {
            whatsappModal.classList.add('hidden');
            whatsappModal.style.display = 'none';
            currentWhatsappInvoiceId = null;
        }

        // WhatsApp button event listeners
        document.querySelectorAll('.whatsapp-btn').forEach(button => {
            button.addEventListener('click', function() {
                const invoiceId = this.dataset.invoiceId;
                const invoiceNumber = this.dataset.invoiceNumber;
                const patientName = this.dataset.patientName;
                openWhatsappModal(invoiceId, invoiceNumber, patientName);
            });
        });

        // Close modal event listeners
        document.getElementById('closeWhatsappModal').addEventListener('click', closeWhatsappModal);
        document.getElementById('cancelWhatsappBtn').addEventListener('click', closeWhatsappModal);

        // Close modal when clicking outside
        whatsappModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeWhatsappModal();
            }
        });

        // Phone number validation
        whatsappNumberInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, ''); // Remove non-digits

            // Limit to 11 digits
            if (value.length > 11) {
                value = value.substring(0, 11);
            }

            this.value = value;

            // Real-time validation
            if (value.length >= 10 && value.length <= 11) {
                this.style.borderColor = '#10b981';
                this.style.backgroundColor = '#f0fdf4';
            } else if (value.length > 0) {
                this.style.borderColor = '#ef4444';
                this.style.backgroundColor = '#fef2f2';
            } else {
                this.style.borderColor = '';
                this.style.backgroundColor = '';
            }
        });

        // WhatsApp form submission
        whatsappForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (isWhatsappSubmitting) return;

            const phoneNumber = whatsappNumberInput.value.trim();
            const message = whatsappMessageTextarea.value.trim();
            const includePDF = document.getElementById('includePDF').checked;
            const includeDetails = document.getElementById('includeDetails').checked;

            // Validation
            if (!phoneNumber || phoneNumber.length < 10 || phoneNumber.length > 11) {
                Swal.fire({
                    title: 'Invalid Phone Number',
                    text: 'Please enter a valid 10-11 digit WhatsApp number',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#f59e0b'
                });
                return;
            }

            if (!message) {
                Swal.fire({
                    title: 'Message Required',
                    text: 'Please enter a message to send',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#f59e0b'
                });
                return;
            }

            // Set loading state
            isWhatsappSubmitting = true;
            whatsappSubmitBtn.disabled = true;
            whatsappSubmitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            whatsappSubmitIcon.classList.add('hidden');
            whatsappSubmitSpinner.classList.remove('hidden');
            whatsappSubmitText.textContent = 'Preparing...';

            // Disable form inputs
            whatsappNumberInput.disabled = true;
            whatsappMessageTextarea.disabled = true;

            // Generate WhatsApp share
            generateWhatsAppShare(currentWhatsappInvoiceId, phoneNumber, message, includePDF, includeDetails);
        });

        // Generate WhatsApp share function
        function generateWhatsAppShare(invoiceId, phoneNumber, message, includePDF, includeDetails) {
            // First get the shareable link
            fetch(`/admin/medical/invoices/${invoiceId}/share-link`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let whatsappMessage = message;

                        if (includeDetails) {
                            whatsappMessage += `\n\n📋 *Invoice Details:*\n`;
                            whatsappMessage += `Invoice: ${whatsappInvoiceNumberElement.textContent}\n`;
                            whatsappMessage += `Patient: ${whatsappPatientNameElement.textContent}\n`;
                            whatsappMessage += `Date: ${new Date().toLocaleDateString()}\n`;
                        }

                        if (includePDF) {
                            whatsappMessage += `\n📄 *Download Receipt:*\n${data.pdf_url}\n`;
                            whatsappMessage += `\n🔗 *View Online:*\n${data.share_url}`;
                        }

                        whatsappMessage += `\n\n🏥 Thank you for choosing our medical services!`;

                        // Format phone number for WhatsApp
                        let formattedPhone = phoneNumber;
                        if (!formattedPhone.startsWith('880')) {
                            formattedPhone = '880' + formattedPhone;
                        }

                        // Create WhatsApp URL
                        const whatsappUrl = `https://wa.me/${formattedPhone}?text=${encodeURIComponent(whatsappMessage)}`;

                        // Reset loading state
                        resetWhatsappSubmitButton();

                        // Close modal
                        closeWhatsappModal();

                        Swal.fire({
                            title: 'WhatsApp Share Ready!',
                            html: `
                    <div class="text-left space-y-3">
                        <div class="flex justify-between">
                            <span class="font-medium">Patient:</span>
                            <span>${whatsappPatientNameElement.textContent}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">WhatsApp Number:</span>
                            <span>+880${phoneNumber}</span>
                        </div>
                        <div class="pt-2 border-t">
                            <p class="text-green-600 font-medium text-center">Click "Open WhatsApp" to send the receipt</p>
                        </div>
                    </div>
                `,
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Open WhatsApp',
                            cancelButtonText: 'Close',
                            confirmButtonColor: '#10b981',
                            cancelButtonColor: '#6b7280',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Open WhatsApp
                                window.open(whatsappUrl, '_blank');

                                // Show follow-up message
                                setTimeout(() => {
                                    Swal.fire({
                                        title: 'WhatsApp Opened!',
                                        text: 'The message has been prepared in WhatsApp. Please review and send it to the patient.',
                                        icon: 'info',
                                        confirmButtonText: 'Got it',
                                        confirmButtonColor: '#10b981',
                                        timer: 5000,
                                        timerProgressBar: true
                                    });
                                }, 1000);
                            }
                        });
                    } else {
                        throw new Error(data.message || 'Failed to generate shareable link');
                    }
                })
                .catch(error => {
                    // Reset loading state
                    resetWhatsappSubmitButton();

                    // Show error message
                    Swal.fire({
                        title: 'WhatsApp Share Failed!',
                        html: `
                <div class="text-left space-y-2">
                    <div class="flex justify-between">
                        <span class="font-medium">Patient:</span>
                        <span>${whatsappPatientNameElement.textContent}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">WhatsApp Number:</span>
                        <span>+880${phoneNumber}</span>
                    </div>
                    <div class="pt-2 border-t">
                        <p class="text-red-600 font-medium">${error.message || 'Failed to prepare WhatsApp share. Please try again.'}</p>
                    </div>
                </div>
            `,
                        icon: 'error',
                        confirmButtonText: 'Try Again',
                        confirmButtonColor: '#ef4444',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });

                    console.error('WhatsApp share error:', error);
                });
        }

        // Reset WhatsApp submit button function
        function resetWhatsappSubmitButton() {
            isWhatsappSubmitting = false;
            whatsappSubmitBtn.disabled = false;
            whatsappSubmitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            whatsappSubmitIcon.classList.remove('hidden');
            whatsappSubmitSpinner.classList.add('hidden');
            whatsappSubmitText.textContent = 'Share via WhatsApp';

            // Re-enable form inputs
            whatsappNumberInput.disabled = false;
            whatsappMessageTextarea.disabled = false;
        }

        // Keyboard shortcuts for WhatsApp modal
        document.addEventListener('keydown', function(e) {
            // Escape key to close WhatsApp modal
            if (e.key === 'Escape' && !whatsappModal.classList.contains('hidden')) {
                closeWhatsappModal();
            }
        });

        // Auto-focus on phone input when WhatsApp modal opens
        const whatsappObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    if (!whatsappModal.classList.contains('hidden')) {
                        setTimeout(() => {
                            whatsappNumberInput.focus();
                        }, 300);
                    }
                }
            });
        });

        whatsappObserver.observe(whatsappModal, {
            attributes: true,
            attributeFilter: ['class']
        });

        // Format phone number as user types
        whatsappNumberInput.addEventListener('keyup', function() {
            let value = this.value.replace(/\D/g, '');

            // Format the number for better readability
            if (value.length >= 4) {
                if (value.length <= 7) {
                    value = value.substring(0, 4) + '-' + value.substring(4);
                } else {
                    value = value.substring(0, 4) + '-' + value.substring(4, 7) + '-' + value.substring(7, 11);
                }
            }

            // Remove formatting for storage but keep for display
            const displayValue = value;
            this.setAttribute('data-display', displayValue);
        });

        // Quick phone number suggestions (common BD mobile prefixes)
        const phoneNumberSuggestions = ['017', '013', '014', '015', '016', '018', '019'];

        whatsappNumberInput.addEventListener('focus', function() {
            if (this.value === '') {
                // You could show a dropdown with suggestions here
                this.placeholder = 'e.g., 1712345678 (without +880)';
            }
        });

        whatsappNumberInput.addEventListener('blur', function() {
            this.placeholder = '1XXXXXXXXX';
        });

        // Auto-resize message textarea
        whatsappMessageTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Character counter for message
        whatsappMessageTextarea.addEventListener('input', function() {
            const maxLength = 1000;
            const currentLength = this.value.length;

            // Create or update character counter
            let counter = document.getElementById('messageCounter');
            if (!counter) {
                counter = document.createElement('div');
                counter.id = 'messageCounter';
                counter.className = 'text-xs text-gray-500 mt-1 text-right';
                this.parentNode.appendChild(counter);
            }

            counter.textContent = `${currentLength}/${maxLength} characters`;

            if (currentLength > maxLength * 0.9) {
                counter.className = 'text-xs text-orange-500 mt-1 text-right';
            } else if (currentLength > maxLength) {
                counter.className = 'text-xs text-red-500 mt-1 text-right';
                this.value = this.value.substring(0, maxLength);
            } else {
                counter.className = 'text-xs text-gray-500 mt-1 text-right';
            }
        });

        // Initialize message counter
        whatsappMessageTextarea.dispatchEvent(new Event('input'));


    });
</script>
@endpush

{{-- Custom Styles --}}
@push('styles')
<style>
    /* Custom scrollbar for modal */
    #collectionModal::-webkit-scrollbar {
        width: 8px;
    }

    #collectionModal::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    #collectionModal::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    #collectionModal::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Modal backdrop blur */
    .modal-backdrop {
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }

    /* Collection Modal Centering */
    #collectionModal {
        display: flex !important;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    #collectionModal.hidden {
        display: none !important;
    }

    /* Discount Section Styles */
    #discountSection {
        animation: slideDown 0.3s ease-out;
    }

    #discountSection.hidden {
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            max-height: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            max-height: 200px;
            transform: translateY(0);
        }
    }

    @keyframes slideUp {
        from {
            opacity: 1;
            max-height: 200px;
            transform: translateY(0);
        }

        to {
            opacity: 0;
            max-height: 0;
            transform: translateY(-10px);
        }
    }

    /* Discount input focus states */
    #discountValue:focus,
    #discountReason:focus,
    #discountType:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
    }

    /* Amount display cards hover effects */
    .bg-red-50:hover {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
    }

    .bg-orange-50:hover {
        background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.15);
    }

    .bg-green-50:hover {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.15);
    }

    /* Mobile responsive improvements */
    @media (max-width: 640px) {
        #collectionModal {
            padding: 0.5rem;
        }

        #collectionModal .max-w-lg {
            max-width: calc(100vw - 1rem);
            margin: 0;
        }

        #collectionModal .rounded-2xl {
            border-radius: 1rem;
        }

        /* Stack amount cards vertically on mobile */
        .sm\\:grid-cols-3 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
    }

    /* Input focus animations */
    input:focus,
    select:focus,
    textarea:focus {
        transform: scale(1.02);
        transition: all 0.2s ease-in-out;
    }

    /* Button hover effects */
    button:hover:not(:disabled) {
        transform: translateY(-1px);
        transition: all 0.2s ease-in-out;
    }

    /* Loading animation */
    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: .5;
        }
    }

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* Custom table responsive improvements */
    @media (max-width: 1280px) {
        .overflow-x-auto {
            padding-bottom: 1rem;
        }

        table {
            min-width: 900px;
        }
    }

    /* Custom scrollbar for table */
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Smooth transitions for cards */
    .invoice-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .invoice-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    /* Status badge animations */
    .status-badge {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Action button hover effects */
    .group:hover svg {
        transform: scale(1.1);
        transition: transform 0.2s ease-in-out;
    }

    /* Table row hover effect */
    tbody tr:hover {
        background: linear-gradient(to right, rgb(239 246 255 / 0.5), rgb(238 242 255 / 0.5));
        transform: scale(1.005);
        transition: all 0.2s ease-in-out;
    }

    /* Button loading state */
    .loading {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }

        body {
            background: white !important;
        }

        .bg-gradient-to-br {
            background: white !important;
        }
    }

    /* Desktop table responsive improvements */
    @media (min-width: 1024px) {
        .overflow-x-auto {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Sticky table header */
        thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background: linear-gradient(to right, rgb(249 250 251), rgb(239 246 255));
        }
    }

    /* Enhanced modal animations */
    #collectionModal {
        animation: modalFadeIn 0.3s ease-out;
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            backdrop-filter: blur(0px);
        }

        to {
            opacity: 1;
            backdrop-filter: blur(4px);
        }
    }

    /* Form validation styles */
    .invalid-input {
        border-color: #ef4444 !important;
        background-color: #fef2f2 !important;
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-5px);
        }

        75% {
            transform: translateX(5px);
        }
    }

    /* Success state styles */
    .success-input {
        border-color: #10b981 !important;
        background-color: #f0fdf4 !important;
    }

    /* Enhanced button states */
    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981, #059669);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #059669, #047857);
        box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
    }

    /* Enhanced card styles */
    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }

    /* Responsive text scaling */
    @media (max-width: 640px) {
        .text-responsive {
            font-size: 0.875rem;
        }
    }

    /* Enhanced focus states */
    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        transform: scale(1.02);
    }

    /* Improved accessibility */
    @media (prefers-reduced-motion: reduce) {

        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .dark-mode-support {
            background-color: #1f2937;
            color: #f9fafb;
        }
    }

    /* High contrast mode support */
    @media (prefers-contrast: high) {
        .high-contrast {
            border-width: 2px;
            border-color: currentColor;
        }
    }

    /* Custom selection styles */
    ::selection {
        background-color: #3b82f6;
        color: white;
    }

    ::-moz-selection {
        background-color: #3b82f6;
        color: white;
    }

    /* Enhanced loading states */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 50;
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #e5e7eb;
        border-top: 4px solid #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Improved mobile touch targets */
    @media (max-width: 768px) {

        button,
        .btn,
        a.btn {
            min-height: 44px;
            min-width: 44px;
        }
    }

    /* Enhanced error states */
    .error-message {
        color: #dc2626;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Success message styles */
    .success-message {
        color: #059669;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        animation: slideDown 0.3s ease-out;
    }

    /* Enhanced tooltip styles */
    .tooltip {
        position: relative;
        display: inline-block;
    }

    .tooltip .tooltiptext {
        visibility: hidden;
        width: 120px;
        background-color: #1f2937;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 8px;
        font-size: 0.75rem;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -60px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .tooltip:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }

    /* Enhanced focus indicators for accessibility */
    .focus-visible:focus {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }

    /* Improved contrast for better readability */
    .high-contrast-text {
        color: #111827;
        font-weight: 500;
    }

    /* Enhanced mobile modal styles */
    @media (max-width: 640px) {
        #collectionModal .transform {
            transform: none !important;
        }

        #collectionModal .min-h-screen {
            min-height: 100vh;
            padding: 1rem;
        }
    }

    /* WhatsApp Modal Styles */
    #whatsappModal {
        animation: modalFadeIn 0.3s ease-out;
    }

    .whatsapp-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
    }

    /* WhatsApp green theme */
    .whatsapp-gradient {
        background: linear-gradient(135deg, #25d366, #128c7e);
    }

    .whatsapp-gradient:hover {
        background: linear-gradient(135deg, #128c7e, #075e54);
    }

    /* Phone number input styling */
    #whatsappNumber:focus {
        border-color: #25d366 !important;
        box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.1) !important;
    }

    /* Message textarea styling */
    #whatsappMessage:focus {
        border-color: #25d366 !important;
        box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.1) !important;
    }

    /* WhatsApp icon animation */
    .whatsapp-btn svg {
        transition: transform 0.3s ease;
    }

    .whatsapp-btn:hover svg {
        transform: scale(1.1) rotate(5deg);
    }

    /* Success state for phone input */
    .phone-valid {
        border-color: #10b981 !important;
        background-color: #f0fdf4 !important;
    }

    /* Error state for phone input */
    .phone-invalid {
        border-color: #ef4444 !important;
        background-color: #fef2f2 !important;
    }

    /* Loading state for WhatsApp button */
    .whatsapp-loading {
        background: linear-gradient(135deg, #9ca3af, #6b7280) !important;
    }

    /* Character counter styles */
    .char-counter-warning {
        color: #f59e0b !important;
    }

    .char-counter-danger {
        color: #ef4444 !important;
    }

    /* Mobile responsive improvements for WhatsApp modal */
    @media (max-width: 640px) {
        #whatsappModal .max-w-md {
            max-width: calc(100vw - 1rem);
            margin: 0.5rem;
        }

        #whatsappModal .p-6 {
            padding: 1rem;
        }

        #whatsappModal .text-lg {
            font-size: 1rem;
        }
    }

    /* Smooth transitions for all WhatsApp elements */
    .whatsapp-transition {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* WhatsApp modal backdrop */
    #whatsappModal {
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }

    /* Enhanced focus states for accessibility */
    #whatsappModal input:focus,
    #whatsappModal textarea:focus,
    #whatsappModal button:focus {
        outline: 2px solid #25d366;
        outline-offset: 2px;
    }
</style>
@endpush
@endsection

<script>
    // Additional JavaScript for discount functionality
    document.addEventListener('DOMContentLoaded', function() {
        // DISCOUNT MODAL FUNCTIONALITY
        const discountModal = document.getElementById('discountModal');
        if (discountModal) {
            const discountForm = document.getElementById('discountForm');
            const discountInvoiceIdInput = document.getElementById('discountInvoiceId');
            const discountPatientNameElement = document.getElementById('discountPatientName');
            const discountInvoiceNumberElement = document.getElementById('discountInvoiceNumber');
            const discountRemainingAmountElement = document.getElementById('discountRemainingAmount');
            const discountNewAmountElement = document.getElementById('discountNewAmount');
            const discountTypeSelectElement = document.getElementById('discountTypeSelect');
            const discountValueInputElement = document.getElementById('discountValueInput');
            const discountReasonInputElement = document.getElementById('discountReasonInput');
            const discountUnitElement = document.getElementById('discountUnit');
            const discountSubmitBtn = document.getElementById('submitDiscount');
            const discountSubmitIcon = document.getElementById('discountSubmitIcon');
            const discountSubmitSpinner = document.getElementById('discountSubmitSpinner');
            const discountSubmitText = document.getElementById('discountSubmitText');

            let currentDiscountInvoiceId = null;
            let currentDiscountRemainingAmount = 0;
            let isDiscountSubmitting = false;

            // Open discount modal function
            function openDiscountModal(invoiceId, invoiceNumber, patientName, remainingAmount) {
                currentDiscountInvoiceId = invoiceId;
                currentDiscountRemainingAmount = parseFloat(remainingAmount);

                // Populate modal data
                discountInvoiceIdInput.value = invoiceId;
                discountPatientNameElement.textContent = patientName;
                discountInvoiceNumberElement.textContent = invoiceNumber;
                discountRemainingAmountElement.textContent = '৳' + parseFloat(remainingAmount).toFixed(2);

                // Reset form
                discountTypeSelectElement.value = '';
                discountValueInputElement.value = '';
                discountReasonInputElement.value = '';
                discountNewAmountElement.textContent = '৳' + parseFloat(remainingAmount).toFixed(2);
                discountUnitElement.textContent = '%';

                // Show modal
                discountModal.classList.remove('hidden');

                // Focus on discount type
                setTimeout(() => {
                    discountTypeSelectElement.focus();
                }, 300);
            }

            // Close discount modal function
            function closeDiscountModal() {
                discountModal.classList.add('hidden');
                currentDiscountInvoiceId = null;
                currentDiscountRemainingAmount = 0;
            }

            // Calculate discount for discount modal
            function calculateDiscountAmount() {
                const discountValue = parseFloat(discountValueInputElement.value) || 0;
                const discountType = discountTypeSelectElement.value;

                if (!discountType || discountValue <= 0) {
                    return 0;
                }

                let discountAmount = 0;
                if (discountType === 'percentage') {
                    discountAmount = (currentDiscountRemainingAmount * discountValue) / 100;
                } else {
                    discountAmount = Math.min(discountValue, currentDiscountRemainingAmount);
                }

                // Fix floating point precision issues
                return Math.round(discountAmount * 100) / 100;
            }

            // Update discount display
            function updateDiscountDisplay() {
                const discountAmount = calculateDiscountAmount();
                const newAmount = currentDiscountRemainingAmount - discountAmount;
                const finalAmount = Math.max(0, Math.round(newAmount * 100) / 100);
                discountNewAmountElement.textContent = '৳' + finalAmount.toFixed(2);
            }

            // Discount button event listeners
            document.querySelectorAll('.discount-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const invoiceId = this.dataset.invoiceId;
                    const invoiceNumber = this.dataset.invoiceNumber;
                    const patientName = this.dataset.patientName;
                    const remainingAmount = this.dataset.remainingAmount;
                    openDiscountModal(invoiceId, invoiceNumber, patientName, remainingAmount);
                });
            });

            // Close discount modal event listeners
            document.getElementById('closeDiscountModal').addEventListener('click', closeDiscountModal);
            document.getElementById('cancelDiscountBtn').addEventListener('click', closeDiscountModal);

            // Close modal when clicking outside
            discountModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeDiscountModal();
                }
            });

            // Discount type change event
            discountTypeSelectElement.addEventListener('change', function() {
                if (this.value === 'percentage') {
                    discountUnitElement.textContent = '%';
                    discountValueInputElement.placeholder = '0';
                    discountValueInputElement.max = '100';
                } else if (this.value === 'fixed') {
                    discountUnitElement.textContent = '৳';
                    discountValueInputElement.placeholder = '0.00';
                    discountValueInputElement.max = currentDiscountRemainingAmount;
                }
                updateDiscountDisplay();
            });

            // Discount value input event
            discountValueInputElement.addEventListener('input', function() {
                // Format input to prevent precision issues
                if (this.value && !isNaN(this.value)) {
                    const numValue = Math.round(parseFloat(this.value) * 100) / 100;
                    if (numValue.toString() !== this.value && this.value.includes('.')) {
                        this.value = numValue.toString();
                    }
                }

                updateDiscountDisplay();

                // Real-time validation for percentage
                if (discountTypeSelectElement.value === 'percentage') {
                    const value = parseFloat(this.value);
                    if (value > 100) {
                        this.value = '100';
                        updateDiscountDisplay();
                    }
                }
            });

            // Prevent mouse wheel scrolling on discount input
            discountValueInputElement.addEventListener('wheel', function(e) {
                e.preventDefault();
                this.blur();
            });

            // Discount form submission
            discountForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (isDiscountSubmitting) return;

                const discountType = discountTypeSelectElement.value;
                const discountValue = Math.round(parseFloat(discountValueInputElement.value) * 100) / 100;
                const discountReason = discountReasonInputElement.value;

                // Validation
                if (!discountType) {
                    Swal.fire({
                        title: 'Discount Type Required',
                        text: 'Please select a discount type',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#f59e0b'
                    });
                    return;
                }

                if (!discountValue || discountValue <= 0) {
                    Swal.fire({
                        title: 'Invalid Discount Value',
                        text: 'Please enter a valid discount value greater than 0',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#f59e0b'
                    });
                    return;
                }

                if (discountType === 'percentage' && discountValue > 100) {
                    Swal.fire({
                        title: 'Invalid Discount',
                        text: 'Percentage discount cannot exceed 100%',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#f59e0b'
                    });
                    return;
                }

                if (discountType === 'fixed' && discountValue > currentDiscountRemainingAmount) {
                    Swal.fire({
                        title: 'Invalid Discount',
                        text: `Fixed discount cannot exceed ৳${currentDiscountRemainingAmount.toFixed(2)}`,
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#f59e0b'
                    });
                    return;
                }

                // Set loading state
                isDiscountSubmitting = true;
                discountSubmitBtn.disabled = true;
                discountSubmitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                discountSubmitIcon.classList.add('hidden');
                discountSubmitSpinner.classList.remove('hidden');
                discountSubmitText.textContent = 'Applying...';

                // Disable form inputs
                discountTypeSelectElement.disabled = true;
                discountValueInputElement.disabled = true;
                discountReasonInputElement.disabled = true;

                // Prepare data
                const formData = {
                    discount_type: discountType,
                    discount_value: discountValue,
                    discount_reason: discountReason
                };

                // Send request
                fetch(`/admin/medical/invoices/${currentDiscountInvoiceId}/apply-discount`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(data => Promise.reject(data));
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Reset loading state
                        resetDiscountSubmitButton();

                        // Close modal
                        closeDiscountModal();

                        // Show success message
                        Swal.fire({
                            title: 'Discount Applied!',
                            html: `
                        <div class="text-left space-y-2">
                            <div class="flex justify-between">
                                <span class="font-medium">Patient:</span>
                                <span>${discountPatientNameElement.textContent}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Discount Applied:</span>
                                <span class="text-orange-600 font-bold">৳${data.data.discount_applied.toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">New Remaining Amount:</span>
                                <span class="text-green-600 font-bold">৳${data.data.remaining_amount.toFixed(2)}</span>
                            </div>
                            <div class="pt-2 border-t">
                                <p class="text-green-600 font-medium text-center">${data.message}</p>
                            </div>
                        </div>
                    `,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#10b981',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then(() => {
                            // Reload page to reflect changes
                            window.location.reload();
                        });
                    })
                    .catch(error => {
                        // Reset loading state
                        resetDiscountSubmitButton();

                        // Show error message
                        Swal.fire({
                            title: 'Discount Failed!',
                            html: `
                        <div class="text-left space-y-2">
                            <div class="flex justify-between">
                                <span class="font-medium">Patient:</span>
                                <span>${discountPatientNameElement.textContent}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Attempted Discount:</span>
                                <span class="text-red-600 font-bold">${discountType === 'percentage' ? discountValue + '%' : '৳' + discountValue.toFixed(2)}</span>
                            </div>
                            <div class="pt-2 border-t">
                                <p class="text-red-600 font-medium">${error.message || 'Failed to apply discount. Please try again.'}</p>
                            </div>
                            ${error.errors ? `
                                <div class="mt-3 p-3 bg-red-50 rounded-lg">
                                    <p class="font-medium text-red-700 mb-2">Details:</p>
                                    <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                                        ${Object.values(error.errors).flat().map(err => `<li>${err}</li>`).join('')}
                                    </ul>
                                </div>
                            ` : ''}
                        </div>
                    `,
                            icon: 'error',
                            confirmButtonText: 'Try Again',
                            confirmButtonColor: '#ef4444',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        });

                        console.error('Discount error:', error);
                    });
            });

            // Reset discount submit button function
            function resetDiscountSubmitButton() {
                isDiscountSubmitting = false;
                discountSubmitBtn.disabled = false;
                discountSubmitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                discountSubmitIcon.classList.remove('hidden');
                discountSubmitSpinner.classList.add('hidden');
                discountSubmitText.textContent = 'Apply Discount';

                // Re-enable form inputs
                discountTypeSelectElement.disabled = false;
                discountValueInputElement.disabled = false;
                discountReasonInputElement.disabled = false;
            }
        }
    });
</script>