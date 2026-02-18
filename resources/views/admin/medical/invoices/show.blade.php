@extends('admin.layouts.app')

@section('content')
@php
$printingUnpaidEnabled = \App\Models\Setting::get('printing_invoice_unpaid', false);
$printingUnpaidEnabled = filter_var($printingUnpaidEnabled, FILTER_VALIDATE_BOOLEAN);
$isUnpaid = $invoice->status !== 'paid';
$approvedPosRequest = $invoice->printRequests()->where('request_type', 'pos')->where('status', 'approved')->first();
$approvedA5Request = $invoice->printRequests()->where('request_type', 'a5')->where('status', 'approved')->first();
$pendingPosRequest = $invoice->printRequests()->where('request_type', 'pos')->where('status', 'pending')->first();
$pendingA5Request = $invoice->printRequests()->where('request_type', 'a5')->where('status', 'pending')->first();
@endphp
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-2 sm:p-4">
    <div class="max-w-4xl mx-auto">
        {{-- Header with Action Buttons --}}
        <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl overflow-hidden border border-white/20 mb-4">
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-center sm:text-left">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">
                            <svg class="inline-block w-8 h-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Invoice Details
                        </h1>
                        <p class="text-blue-100 text-sm">{{ $invoice->invoice_number }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <!-- Back Button -->
                        <a href="{{ route('admin.medical.invoices.index') }}"
                            class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to List
                        </a>

                        <!-- Print Buttons -->

                        @if(!$isUnpaid || !$printingUnpaidEnabled || ($approvedPosRequest && $approvedPosRequest->allowed_prints > 0 && $approvedPosRequest->prints_used < $approvedPosRequest->allowed_prints) || auth()->user()->hasRole('admin'))
                            <a href="{{ route('admin.medical.invoices.print', $invoice) }}" target="_blank"
                                class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Print-Pos
                            </a>
                            @elseif($pendingPosRequest)
                            <button disabled
                                class="bg-gradient-to-r from-gray-400 to-gray-500 text-white font-bold py-2 px-4 rounded-lg shadow-lg flex items-center cursor-not-allowed">
                                <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                POS Request Pending
                            </button>
                            @else
                            <button onclick="requestPrint('pos')"
                                class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Request POS Print
                            </button>
                            @endif

                            @if(!$isUnpaid || !$printingUnpaidEnabled || ($approvedA5Request && $approvedA5Request->allowed_prints > 0 && $approvedA5Request->prints_used < $approvedA5Request->allowed_prints) || auth()->user()->hasRole('admin'))
                                <a href="{{ route('admin.medical.invoices.print-a5', $invoice) }}" target="_blank"
                                    class="ml-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    Print A5
                                </a>
                                @elseif($pendingA5Request)
                                <button disabled
                                    class="ml-2 bg-gradient-to-r from-gray-400 to-gray-500 text-white font-bold py-2 px-4 rounded-lg shadow-lg flex items-center cursor-not-allowed">
                                    <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    A5 Request Pending
                                </button>
                                @else
                                <button onclick="requestPrint('a5')"
                                    class="ml-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Request A5 Print
                                </button>
                                @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Invoice Content --}}
        <div class="bg-white/90 backdrop-blur-sm shadow-xl rounded-2xl overflow-hidden border border-white/20">
            <div class="invoice-content p-6">
                <!-- Hospital/Clinic Header -->
                <div class="text-center mb-6">
                    @if(isset($hospital) && $hospital)
                    <h2 class="font-bold text-2xl text-gray-800 mb-1">{{ $hospital->name }}</h2>
                    <p class="text-gray-600 mb-1">{{ $hospital->address ?? 'Hospital Address' }}</p>
                    <p class="text-sm text-gray-500">Phone: {{ $hospital->phone ?? 'N/A' }} | Email: {{ $hospital->email ?? 'N/A' }}</p>
                    @else
                    <h2 class="font-bold text-2xl text-gray-800 mb-1">Medical Center</h2>
                    <p class="text-gray-600 mb-1">Hospital Address</p>
                    <p class="text-sm text-gray-500">Phone: N/A | Email: N/A</p>
                    @endif

                    <!-- Invoice Header -->
                    <h3 class="font-bold text-xl text-blue-700 mt-4">MEDICAL INVOICE</h3>
                    <p class="text-gray-600">Invoice #: <span class="font-bold text-gray-800">{{ $invoice->invoice_number }}</span></p>
                </div>

                <div class="border-t-2 border-gray-300 my-4"></div>

                <!-- Patient Information -->
                <div class="mb-6">
                    <h4 class="font-bold text-lg text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        PATIENT INFORMATION
                    </h4>
                    <div class="border-b border-gray-200 mb-3"></div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex justify-between py-1">
                            <span class="font-medium text-gray-700">Name:</span>
                            <span class="text-gray-900">{{ $invoice->patient->full_name }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="font-medium text-gray-700">Patient ID:</span>
                            <span class="text-gray-900">{{ $invoice->patient->patient_id }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="font-medium text-gray-700">Phone:</span>
                            <span class="text-gray-900">{{ $invoice->patient->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="font-medium text-gray-700">Age:</span>
                            <span class="text-gray-900">{{ $invoice->patient->age ?? 'N/A' }} years</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="font-medium text-gray-700">Gender:</span>
                            <span class="text-gray-900">{{ ucfirst($invoice->patient->gender ?? 'N/A') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Invoice Details -->
                <div class="mb-6">
                    <div class="border-b border-gray-200 mb-3"></div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex justify-between py-1">
                            <span class="font-medium text-gray-700">Invoice Date:</span>
                            <span class="text-gray-900">{{ $invoice->invoice_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="font-medium text-gray-700">Time:</span>
                            <span class="text-gray-900">{{ $invoice->invoice_date->format('h:i A') }}</span>
                        </div>
                        @if($invoice->doctor)
                        <div class="flex justify-between py-1">
                            <span class="font-medium text-gray-700">Doctor:</span>
                            <span class="text-gray-900">{{ $invoice->doctor->name }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between py-1">
                            <span class="font-medium text-gray-700">Status:</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ 
                                $invoice->status == 'paid' ? 'bg-green-100 text-green-800' : 
                                ($invoice->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') 
                            }}">
                                {{ strtoupper($invoice->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="border-t-2 border-gray-300 my-4"></div>

                <!-- Tests/Services -->
                <div class="mb-6">
                    <h4 class="font-bold text-lg text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        TESTS/SERVICES
                    </h4>
                    <div class="border-b border-gray-200 mb-3"></div>

                    @if($items && $items->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $item->test->name ?? 'Medical Service' }}</div>
                                            @if(isset($item->test->description) && $item->test->description)
                                            <div class="text-sm text-gray-500">{{ Str::limit($item->test->description, 80) }}</div>
                                            @endif
                                            @if(isset($item->test->test_code) && $item->test->test_code)
                                            <div class="text-xs text-blue-600">Code: {{ $item->test->test_code }}</div>
                                            @endif
                                            @if(isset($item->test->department) && $item->test->department)
                                            <div class="text-xs text-gray-500">Dept: {{ $item->test->department }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center text-sm text-gray-900">{{ $item->quantity ?? 1 }}</td>
                                    <td class="px-4 py-4 text-right text-sm text-gray-900">৳{{ number_format($item->unit_price ?? 0, 2) }}</td>
                                    <td class="px-4 py-4 text-right text-sm font-medium text-gray-900">৳{{ number_format($item->total_price ?? 0, 2) }}</td>
                                </tr>
                                @if(isset($item->line_discount) && $item->line_discount > 0)
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-right text-sm text-gray-600">Line Discount:</td>
                                    <td class="px-4 py-2 text-right text-sm text-red-600">-৳{{ number_format($item->line_discount, 2) }}</td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-gray-500">No services found for this invoice</p>
                    </div>
                    @endif
                </div>

                <div class="border-t-2 border-gray-300 my-4"></div>

                <!-- Totals -->
                <div class="mb-6">
                    <h4 class="font-bold text-lg text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        PAYMENT SUMMARY
                    </h4>
                    <div class="border-b border-gray-200 mb-3"></div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="space-y-2">
                            <div class="flex justify-between py-2">
                                <span class="text-gray-700">Subtotal:</span>
                                <span class="font-medium text-gray-900">৳{{ number_format($subtotal ?? $invoice->subtotal ?? 0, 2) }}</span>
                            </div>

                            @if(($discountAmount ?? $invoice->discount_amount ?? 0) > 0)
                            <div class="flex justify-between py-2">
                                <span class="text-gray-700">Discount ({{ $invoice->discount_percentage ?? 0 }}%):</span>
                                <span class="text-red-600">-৳{{ number_format($discountAmount ?? $invoice->discount_amount ?? 0, 2) }}</span>
                            </div>
                            @endif

                            @if(($taxAmount ?? $invoice->tax_amount ?? 0) > 0)
                            <div class="flex justify-between py-2">
                                <span class="text-gray-700">Tax ({{ $invoice->tax_percentage ?? 0 }}%):</span>
                                <span class="text-gray-900">৳{{ number_format($taxAmount ?? $invoice->tax_amount ?? 0, 2) }}</span>
                            </div>
                            @endif

                            <div class="border-t border-gray-300 pt-2">
                                <div class="flex justify-between py-2">
                                    <span class="text-lg font-bold text-gray-900">Grand Total:</span>
                                    <span class="text-lg font-bold text-gray-900">৳{{ number_format($grandTotal ?? $invoice->grand_total ?? 0, 2) }}</span>
                                </div>
                            </div>

                            @if(($paidAmount ?? $invoice->paid_amount ?? 0) > 0)
                            <div class="flex justify-between py-2">
                                <span class="text-gray-700">Paid Amount:</span>
                                <span class="text-green-600 font-medium">৳{{ number_format($paidAmount ?? $invoice->paid_amount ?? 0, 2) }}</span>
                            </div>
                            @endif

                            @php
                            $remainingAmount = ($grandTotal ?? $invoice->grand_total ?? 0) - ($paidAmount ?? $invoice->paid_amount ?? 0);
                            @endphp

                            @if($remainingAmount > 0)
                            <div class="flex justify-between py-2">
                                <span class="text-gray-700">Remaining:</span>
                                <span class="text-red-600 font-medium">৳{{ number_format($remainingAmount, 2) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                @if($invoice->payment_method || $invoice->payment_notes)
                <div class="mb-6">
                    <h4 class="font-bold text-lg text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        PAYMENT INFORMATION
                    </h4>
                    <div class="border-b border-gray-200 mb-3"></div>
                    <div class="bg-blue-50 rounded-lg p-4">
                        @if($invoice->payment_method)
                        <div class="flex justify-between py-1">
                            <span class="font-medium text-gray-700">Payment Method:</span>
                            <span class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $invoice->payment_method)) }}</span>
                        </div>
                        @endif
                        @if($invoice->payment_notes)
                        <div class="mt-2">
                            <span class="font-medium text-gray-700">Notes:</span>
                            <p class="text-gray-900 mt-1">{{ $invoice->payment_notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Footer -->
                <div class="border-t-2 border-gray-300 pt-4">
                    <div class="text-center text-gray-600">
                        <p class="mb-2">Thank you for choosing our services!</p>
                        <p class="text-sm">For any queries, please contact us.</p>
                        @if($invoice->status === 'paid' || $remainingAmount <= 0)
                            <p class="text-green-600 font-bold mt-2">✓ Payment Completed</p>
                            @else
                            <p class="text-red-600 font-bold mt-2">⚠ Payment Pending</p>
                            @endif
                    </div>
                </div>

                <!-- Print Information -->
                <div class="text-center mt-6 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500">Viewed on: {{ \Carbon\Carbon::now('Asia/Dhaka')->format('d/m/Y h:i A') }}</p>
                    <p class="text-sm text-gray-500">Viewed by: {{ Auth::user()->name }}</p>
                    <p class="text-sm font-bold text-blue-600 mt-2">
                        POWERED BY <span class="text-red-600">ePATNER</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom Styles --}}
@push('styles')
<style>
    .invoice-content {
        background: white;
        color: #000;
    }

    .invoice-content h2,
    .invoice-content h3,
    .invoice-content h4 {
        color: inherit;
    }

    .invoice-content table {
        background: white;
    }

    .invoice-content .bg-gray-50 {
        background-color: #f9fafb;
    }

    .invoice-content .bg-blue-50 {
        background-color: #eff6ff;
    }

    /* Print styles */
    @media print {
        body {
            background: white !important;
        }

        .bg-gradient-to-br {
            background: white !important;
        }

        .print-hide {
            display: none !important;
        }

        .invoice-content {
            box-shadow: none !important;
            border: none !important;
        }
    }
</style>
@endpush
{{-- Print Request Modal --}}
<div id="printRequestModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Request Print Access</h3>
            <form id="printRequestForm">
                @csrf
                <input type="hidden" name="request_type" id="requestType">
                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
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

@push('scripts')
<script>
    function requestPrint(type) {
        document.getElementById('requestType').value = type;
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
</script>
@endpush

@endsection