@extends('admin.layouts.app')

@section('title', 'Appointment Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-gray-200 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-4 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-check text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Appointment Details</h1>
                    <p class="text-sm text-gray-500">#{{ str_pad($appointment->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="printAppointment()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-all duration-200">
                    <i class="fas fa-print mr-2"></i>
                    Print Receipt
                </button>
                <a href="{{ route('admin.appointments.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-all duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100" id="appointmentReceipt">
            <div class="p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="mb-4">
                        @if(Auth::user()->business && Auth::user()->business->logo)
                        <div class="w-20 h-20 mx-auto rounded-full overflow-hidden">
                            <img src="{{ Storage::url(Auth::user()->business->logo) }}" alt="Logo" class="w-full h-full object-cover">
                        </div>
                        @else
                        <div class="w-20 h-20 mx-auto bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-hospital-alt text-blue-600 text-3xl"></i>
                        </div>
                        @endif
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ Auth::user()->business->name ?? 'Medical Practice' }}</h2>
                    @if(Auth::user()->business)
                    <div class="text-gray-600 space-y-1">
                        <p>{{ Auth::user()->business->address ?? '' }}</p>
                        <p>{{ Auth::user()->business->phone ?? '' }}</p>
                        <p>{{ Auth::user()->business->email ?? '' }}</p>
                    </div>
                    @endif
                </div>

                <hr class="border-gray-300 mb-8">

                <!-- Appointment Number Highlight -->
                <div class="bg-blue-600 text-white p-6 rounded-lg text-center mb-8">
                    <h3 class="text-lg font-semibold mb-2">APPOINTMENT RECEIPT</h3>
                    <div class="text-4xl font-bold font-mono tracking-wider">
                        #{{ str_pad($appointment->id, 6, '0', STR_PAD_LEFT) }}
                    </div>
                    <div class="mt-4 text-lg">
                        {{ $appointment->appointment_date->format('F d, Y') }} at {{ date('g:i A', strtotime($appointment->appointment_time)) }}
                    </div>
                </div>

                <!-- Patient Information -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-user text-blue-600 mr-2"></i>
                        Patient Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">Name</div>
                            <div class="font-semibold">{{ $appointment->patient->name ?? 'N/A' }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">Phone</div>
                            <div class="font-semibold">{{ $appointment->patient->phone ?? 'N/A' }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">Email</div>
                            <div class="font-semibold">{{ $appointment->patient->email ?? 'N/A' }}</div>
                        </div>
                        @if($appointment->patient && $appointment->patient->age)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">Age</div>
                            <div class="font-semibold">{{ $appointment->patient->age }} years</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Doctor Information -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-user-md text-green-600 mr-2"></i>
                        Doctor Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">Doctor</div>
                            <div class="font-semibold">{{ $appointment->doctor->name ?? 'N/A' }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">Specialization</div>
                            <div class="font-semibold">{{ $appointment->doctor->specialization ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Appointment Details -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>
                        Appointment Details
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">Type</div>
                            <div class="font-semibold">{{ ucfirst(str_replace('_', ' ', $appointment->appointment_type)) }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">Duration</div>
                            <div class="font-semibold">{{ $appointment->duration }} minutes</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">Status</div>
                            <div class="font-semibold">{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">Priority</div>
                            <div class="font-semibold">{{ ucfirst($appointment->priority) }}</div>
                        </div>
                    </div>
                </div>

                @if($appointment->chief_complaint)
                <!-- Chief Complaint -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-stethoscope text-red-600 mr-2"></i>
                        Chief Complaint
                    </h4>
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
                        <p class="text-gray-700">{{ $appointment->chief_complaint }}</p>
                    </div>
                </div>
                @endif

                @if($appointment->notes)
                <!-- Notes -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>
                        Notes
                    </h4>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                        <p class="text-gray-700">{{ $appointment->notes }}</p>
                    </div>
                </div>
                @endif

                <!-- Payment Information -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-dollar-sign text-green-600 mr-2"></i>
                        Payment Information
                    </h4>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Consultation Fee:</span>
                            <span class="text-2xl font-bold text-green-600">${{ number_format($appointment->consultation_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Payment Status:</span>
                            <span class="font-semibold">{{ ucfirst($appointment->payment_status) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <hr class="border-gray-300 mb-6">
                <div class="text-center text-gray-600 space-y-2">
                    <p class="font-semibold">Thank you for choosing our medical services!</p>
                    <p class="text-sm">Please arrive 15 minutes before your appointment time.</p>
                    <p class="text-sm">For any changes, please contact us at least 24 hours in advance.</p>
                    <div class="mt-4 text-xs">
                        <p>Printed on: {{ now()->format('F d, Y g:i A') }}</p>
                        <p>Printed by: {{ Auth::user()->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function printAppointment() {
        const printWindow = window.open('', '_blank');
        const receiptContent = document.getElementById('appointmentReceipt').innerHTML;

        const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Appointment Receipt - #{{ str_pad($appointment->id, 6, '0', STR_PAD_LEFT) }}</title>
            <style>
                @page {
                    size: A4;
                    margin: 1in;
                }
                
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: 'Arial', 'Helvetica', sans-serif;
                    font-size: 14px;
                    line-height: 1.6;
                    color: #333;
                    background: #fff;
                }
                
                .appointment-receipt {
                    max-width: 100%;
                    margin: 0 auto;
                    padding: 20px;
                }
                
                /* Header */
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 2px solid #333;
                }
                
                .logo {
                    width: 80px;
                    height: 80px;
                    margin: 0 auto 15px;
                    border-radius: 50%;
                    background: #f0f0f0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 30px;
                    color: #666;
                }
                
                .logo img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    border-radius: 50%;
                }
                
                .business-name {
                    font-size: 24px;
                    font-weight: bold;
                    margin-bottom: 10px;
                    color: #333;
                }
                
                .business-info {
                    color: #666;
                    font-size: 12px;
                }
                
                /* Appointment Highlight */
                .appointment-highlight {
                    background: #000;
                    color: #fff;
                    padding: 30px;
                    text-align: center;
                    margin: 30px 0;
                    border-radius: 8px;
                }
                
                .receipt-title {
                    font-size: 18px;
                    font-weight: bold;
                    margin-bottom: 15px;
                    letter-spacing: 2px;
                }
                
                                .appointment-number {
                    font-size: 48px;
                    font-weight: bold;
                    font-family: 'Courier New', monospace;
                    letter-spacing: 4px;
                    margin: 15px 0;
                }
                
                .appointment-datetime {
                    font-size: 16px;
                    font-weight: 600;
                    margin-top: 10px;
                }
                
                /* Sections */
                .section {
                    margin-bottom: 25px;
                    page-break-inside: avoid;
                }
                
                .section-title {
                    font-size: 16px;
                    font-weight: bold;
                    color: #333;
                    margin-bottom: 15px;
                    padding-bottom: 5px;
                    border-bottom: 1px solid #ddd;
                    display: flex;
                    align-items: center;
                }
                
                .section-title i {
                    margin-right: 8px;
                    width: 20px;
                }
                
                .info-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 15px;
                    margin-bottom: 15px;
                }
                
                .info-item {
                    background: #f8f9fa;
                    padding: 12px;
                    border-radius: 4px;
                    border-left: 3px solid #007bff;
                }
                
                .info-label {
                    font-size: 11px;
                    color: #666;
                    text-transform: uppercase;
                    font-weight: 600;
                    margin-bottom: 4px;
                }
                
                .info-value {
                    font-size: 14px;
                    font-weight: 600;
                    color: #333;
                }
                
                /* Special sections */
                .complaint-section, .notes-section {
                    background: #fff8dc;
                    padding: 15px;
                    border-left: 4px solid #ffc107;
                    border-radius: 0 4px 4px 0;
                    margin: 15px 0;
                }
                
                .payment-section {
                    background: #f0fff4;
                    padding: 15px;
                    border-left: 4px solid #28a745;
                    border-radius: 0 4px 4px 0;
                    margin: 15px 0;
                }
                
                .payment-fee {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    font-size: 16px;
                    font-weight: bold;
                    margin-bottom: 10px;
                    padding: 10px 0;
                    border-bottom: 1px solid #ddd;
                }
                
                .payment-amount {
                    font-size: 24px;
                    color: #28a745;
                    font-family: 'Courier New', monospace;
                }
                
                .payment-status {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    font-size: 14px;
                }
                
                /* Footer */
                .footer {
                    margin-top: 40px;
                    padding-top: 20px;
                    border-top: 2px solid #333;
                    text-align: center;
                }
                
                .thank-you {
                    font-size: 16px;
                    font-weight: bold;
                    margin-bottom: 15px;
                    color: #333;
                }
                
                .instructions {
                    font-size: 12px;
                    color: #666;
                    margin-bottom: 20px;
                }
                
                .print-info {
                    font-size: 10px;
                    color: #999;
                    margin-top: 20px;
                }
                
                /* Print optimizations */
                @media print {
                    body {
                        -webkit-print-color-adjust: exact;
                        color-adjust: exact;
                    }
                    
                    .appointment-highlight {
                        background: #000 !important;
                        color: #fff !important;
                    }
                    
                    .info-item {
                        background: #f8f9fa !important;
                        border-left: 3px solid #007bff !important;
                    }
                    
                    .complaint-section, .notes-section {
                        background: #fff8dc !important;
                        border-left: 4px solid #ffc107 !important;
                    }
                    
                    .payment-section {
                        background: #f0fff4 !important;
                        border-left: 4px solid #28a745 !important;
                    }
                    
                    .section {
                        page-break-inside: avoid;
                    }
                    
                    .appointment-highlight {
                        page-break-inside: avoid;
                    }
                }
            </style>
        </head>
        <body>
            <div class="appointment-receipt">
                <div class="header">
                    <div class="logo">
                        @if(Auth::user()->business && Auth::user()->business->logo)
                        <img src="{{ Storage::url(Auth::user()->business->logo) }}" alt="Logo">
                        @else
                        <i class="fas fa-hospital-alt"></i>
                        @endif
                    </div>
                    <div class="business-name">{{ Auth::user()->business->name ?? 'Medical Practice' }}</div>
                    <div class="business-info">
                        @if(Auth::user()->business)
                        <div>{{ Auth::user()->business->address ?? '' }}</div>
                        <div>{{ Auth::user()->business->phone ?? '' }}</div>
                        <div>{{ Auth::user()->business->email ?? '' }}</div>
                        @endif
                    </div>
                </div>
                
                <div class="appointment-highlight">
                    <div class="receipt-title">APPOINTMENT RECEIPT</div>
                    <div class="appointment-number">#{{ str_pad($appointment->id, 6, '0', STR_PAD_LEFT) }}</div>
                    <div class="appointment-datetime">{{ $appointment->appointment_date->format('F d, Y') }} at {{ date('g:i A', strtotime($appointment->appointment_time)) }}</div>
                </div>
                
                <div class="section">
                    <div class="section-title">
                        <i>👤</i> Patient Information
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Name</div>
                            <div class="info-value">{{ $appointment->patient->name ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Phone</div>
                            <div class="info-value">{{ $appointment->patient->phone ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value">{{ $appointment->patient->email ?? 'N/A' }}</div>
                        </div>
                        @if($appointment->patient && $appointment->patient->age)
                        <div class="info-item">
                            <div class="info-label">Age</div>
                            <div class="info-value">{{ $appointment->patient->age }} years</div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="section">
                    <div class="section-title">
                        <i>👨‍⚕️</i> Doctor Information
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Doctor</div>
                            <div class="info-value">{{ $appointment->doctor->name ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Specialization</div>
                            <div class="info-value">{{ $appointment->doctor->specialization ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="section">
                    <div class="section-title">
                        <i>📅</i> Appointment Details
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Type</div>
                            <div class="info-value">{{ ucfirst(str_replace('_', ' ', $appointment->appointment_type)) }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Duration</div>
                            <div class="info-value">{{ $appointment->duration }} minutes</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Status</div>
                            <div class="info-value">{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Priority</div>
                            <div class="info-value">{{ ucfirst($appointment->priority) }}</div>
                        </div>
                    </div>
                </div>
                
                @if($appointment->chief_complaint)
                <div class="section">
                    <div class="section-title">
                        <i>🩺</i> Chief Complaint
                    </div>
                    <div class="complaint-section">
                        {{ $appointment->chief_complaint }}
                    </div>
                </div>
                @endif
                
                @if($appointment->notes)
                <div class="section">
                    <div class="section-title">
                        <i>📝</i> Notes
                    </div>
                    <div class="notes-section">
                        {{ $appointment->notes }}
                    </div>
                </div>
                @endif
                
                <div class="section">
                    <div class="section-title">
                        <i>💰</i> Payment Information
                    </div>
                    <div class="payment-section">
                        <div class="payment-fee">
                            <span>Consultation Fee:</span>
                            <span class="payment-amount">\${{ number_format($appointment->consultation_fee, 2) }}</span>
                        </div>
                        <div class="payment-status">
                            <span>Payment Status:</span>
                            <span>{{ ucfirst($appointment->payment_status) }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="footer">
                    <div class="thank-you">Thank you for choosing our medical services!</div>
                    <div class="instructions">
                        Please arrive 15 minutes before your appointment time.<br>
                        For any changes, please contact us at least 24 hours in advance.
                    </div>
                    <div class="print-info">
                        Printed on: {{ now()->format('F d, Y g:i A') }}<br>
                        Printed by: {{ Auth::user()->name }}
                    </div>
                </div>
            </div>
        </body>
        </html>
    `;

        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.focus();

        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    }

    // Auto-print functionality
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === 'true') {
            setTimeout(() => {
                printAppointment();
            }, 1000);
        }
    });
</script>
@endpush

@push('styles')
<style>
    /* Simple screen styles */
    .appointment-receipt {
        font-family: 'Arial', 'Helvetica', sans-serif;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .grid {
            grid-template-columns: 1fr;
        }

        .flex {
            flex-direction: column;
            gap: 1rem;
        }

        .text-4xl {
            font-size: 2rem;
        }

        .px-8 {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }

    @media (max-width: 640px) {
        .text-2xl {
            font-size: 1.25rem;
        }

        .text-lg {
            font-size: 1rem;
        }

        .p-6 {
            padding: 1rem;
        }

        .gap-4 {
            gap: 0.75rem;
        }
    }
</style>
@endpush