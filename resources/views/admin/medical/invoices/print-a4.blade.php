<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Invoice - A4 Print</title>
    <style>
        @page {
            margin: 10mm;
            size: A4;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', 'Helvetica', sans-serif;
            background-color: white;
            color: #000000;
            line-height: 1.2;
            font-size: 10px;
        }

        .print-controls {
            text-align: center;
            margin: 15px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 6px;
        }

        .print-controls button {
            padding: 10px 20px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 6px;
            font-weight: bold;
            font-size: 13px;
        }

        .print-controls a {
            padding: 10px 20px;
            background-color: #4b5563;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 6px;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
            font-size: 13px;
        }

        .invoice-container {
            max-width: 190mm;
            margin: 0 auto;
            padding: 5px;
            background-color: white;
            position: relative;
        }

        /* Letterhead Space - Reserved for official header */
        .letterhead-space {
            height: 25mm;
            margin-bottom: 8px;
            border-bottom: 1px dashed #000;
            position: relative;
            padding-top: 3mm;
        }

        .letterhead-placeholder {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #000;
            font-size: 14px;
            text-align: center;
            font-style: italic;
        }

        /* PAID Stamp Styles */
        .paid-stamp {
            position: relative;
            width: 100px;
            height: 100px;
            border: 3px solid #000;
            border-radius: 50%;
            background-color: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            pointer-events: none;
        }

        .paid-stamp-inner {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .paid-text {
            font-size: 18px;
            font-weight: bold;
            color: #dc2626;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .curved-text-svg {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .curved-text-svg text {
            font-family: 'Arial', sans-serif;
            text-anchor: middle;
        }

        /* Invoice Header */
        .invoice-header {
            margin-bottom: 8px;
        }

        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 2px;
        }

        .invoice-number {
            font-size: 12px;
            color: #6b7280;
        }

        /* Copy Labels */
        .copy-label {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Invoice Details */
        .invoice-details {
            margin-bottom: 8px;
        }

        /* Services Table */
        .services-section {
            margin-bottom: 8px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 6px;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }

        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .services-table th,
        .services-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            font-size: 10px;
        }

        .services-table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }

        .service-name {
            font-weight: bold;
            color: #000000;
        }

        .service-details {
            font-size: 9px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* Totals Section */
        .totals-section {
            margin-top: 10px;
            border-top: 2px solid #000;
            padding-top: 8px;
            display: flex;
            justify-content: space-between;
        }

        .totals-wrapper {
            display: flex;
            width: 100%;
        }

        .totals-left-space {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100px;
        }

        .totals-table {
            flex: 1;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 3px 0;
            font-size: 12px;
        }

        .total-label {
            text-align: left;
            font-weight: bold;
        }

        .total-value {
            text-align: right;
            font-weight: bold;
        }

        .grand-total-row {
            border-top: 1px solid #000;
            font-size: 14px;
            font-weight: bold;
        }

        .paid-amount {
            color: #059669;
        }

        .remaining-amount {
            color: #dc2626;
        }

        /* Page Footer */
        .page-footer {
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            margin-top: 8px;
            padding-top: 4px;
            border-top: 1px solid #e5e7eb;
        }

        /* Continuation Header */
        .continuation-header {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #000;
        }

        /* Page Breaks */
        .page-break {
            page-break-before: always;
        }

        /* Responsive adjustments */
        @media print {
            .print-controls {
                display: none;
            }

            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }

        /* Utility Classes */
        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }
    </style>
</head>

<body>
    <div class="print-controls">
        <button onclick="window.print()">Print Invoice</button>
    </div>

    @php
    $itemsPerPage = 25;
    $itemChunks = $items->chunk($itemsPerPage);
    $totalPages = $itemChunks->count();
    @endphp

    @if($doubleCopyPrint ?? false)
    {{-- Generate two copies when double copy print is enabled --}}
    @php
    $copies = [
    ['type' => 'patient', 'label' => 'PATIENT COPY', 'bg_color' => 'rgba(59, 130, 246, 0.1)', 'border_color' => '#3b82f6'],
    ['type' => 'business', 'label' => 'BUSINESS COPY', 'bg_color' => 'rgba(16, 185, 129, 0.1)', 'border_color' => '#10b981']
    ];
    @endphp

    @foreach($copies as $copyIndex => $copy)
    @foreach($itemChunks as $pageIndex => $pageItems)
    <div class="invoice-page {{ $pageIndex > 0 || $copyIndex > 0 ? 'page-break' : '' }}">
        <div class="invoice-container">

            @if($pageIndex == 0)
            <!-- Letterhead Space for Official Header (First Page Only) -->
            <div class="letterhead-space">
                @if(isset($letterhead) && $letterhead)
                <!-- Professional Letterhead -->
                @php
                $logoPath = public_path('images/logo.png');
                $hasLogo = file_exists($logoPath);
                @endphp

                @if($hasLogo)
                <!-- Layout with Logo -->
                <div style="display: flex; align-items: center; justify-content: space-between; height: 100%; padding: 0 10px;">
                    <!-- Logo on Left -->
                    <div style="flex: 0 0 80px; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="max-width: 100%; max-height: 80px; object-fit: contain;">
                    </div>

                    <!-- Business Info on Right -->
                    <div style="flex: 1; text-align: center; padding-left: 15px;">
                        <!-- Business Names (Top) -->
                        @if($letterhead->business_name_bangla)
                        <div style="font-size: 16px; font-weight: bold; color: #000; margin-bottom: 2px; font-family: 'Arial', sans-serif;">
                            {{ $letterhead->business_name_bangla }}
                        </div>
                        @endif
                        @if($letterhead->business_name_english)
                        <div style="font-size: 14px; font-weight: bold; color: #000; margin-bottom: 4px;">
                            {{ $letterhead->business_name_english }}
                        </div>
                        @endif

                        <!-- Location -->
                        @if($letterhead->location)
                        <div style="font-size: 10px; color: #333; margin-bottom: 4px;">
                            📍 {{ $letterhead->location }}
                        </div>
                        @endif

                        <!-- Phone Numbers (One Line) -->
                        @if($letterhead->contacts && count($letterhead->contacts) > 0)
                        <div style="font-size: 10px; color: #333; margin-bottom: 2px;">
                            📞 {{ implode(' | ', array_filter($letterhead->contacts)) }}
                        </div>
                        @endif

                        <!-- Email -->
                        @if($letterhead->emails && count($letterhead->emails) > 0)
                        <div style="font-size: 10px; color: #333;">
                            ✉️ {{ implode(' | ', array_filter($letterhead->emails)) }}
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <!-- Full Width Layout without Logo -->
                <div style="display: flex; flex-direction: column; justify-content: center; height: 100%; padding: 0 10px; text-align: center; width: 100%;">
                    <!-- Business Names (Top) -->
                    @if($letterhead->business_name_bangla)
                    <div style="font-size: 16px; font-weight: bold; color: #000; margin-bottom: 2px; font-family: 'Arial', sans-serif;">
                        {{ $letterhead->business_name_bangla }}
                    </div>
                    @endif
                    @if($letterhead->business_name_english)
                    <div style="font-size: 14px; font-weight: bold; color: #000; margin-bottom: 4px;">
                        {{ $letterhead->business_name_english }}
                    </div>
                    @endif

                    <!-- Location -->
                    @if($letterhead->location)
                    <div style="font-size: 10px; color: #333; margin-bottom: 4px;">
                        📍 {{ $letterhead->location }}
                    </div>
                    @endif

                    <!-- Phone Numbers (One Line) -->
                    @if($letterhead->contacts && count($letterhead->contacts) > 0)
                    <div style="font-size: 10px; color: #333; margin-bottom: 2px;">
                        📞 {{ implode(' | ', array_filter($letterhead->contacts)) }}
                    </div>
                    @endif

                    <!-- Email -->
                    @if($letterhead->emails && count($letterhead->emails) > 0)
                    <div style="font-size: 10px; color: #333;">
                        ✉️ {{ implode(' | ', array_filter($letterhead->emails)) }}
                    </div>
                    @endif
                </div>
                @endif
                @else
                <div class="letterhead-placeholder">
                    [Space reserved for official letterhead]
                </div>
                @endif
            </div>

            <!-- Invoice Header (First Page Only) -->
            <div class="invoice-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; padding-bottom: 4px; border-bottom: 1px solid #000;">
                <div style="flex: 1;">
                    <div class="invoice-title">MEDICAL INVOICE</div>
                    <div class="invoice-number">Invoice #: {{ $invoice->invoice_number }}</div>
                </div>
            </div>

            <!-- Invoice Details (First Page Only) -->
            <div class="invoice-details">
                <!-- Patient Information -->
                <div class="mb-2">
                    <div class="text-sm font-bold mb-1" style="color: #000000;">Patient Information:</div>
                    <div class="compact-info">
                        <span class="label">Name:</span>
                        <span class="value">{{ $invoice->patient ? $invoice->patient->full_name : 'N/A' }}</span>
                    </div>
                    <div class="compact-info">
                        <span class="label">ID:</span>
                        <span class="value">{{ $invoice->patient ? $invoice->patient->patient_id : 'N/A' }}</span>
                    </div>
                    <div class="compact-info">
                        <span class="label">Phone:</span>
                        <span class="value">{{ $invoice->patient ? $invoice->patient->phone : 'N/A' }}</span>
                    </div>
                    <div class="compact-info">
                        <span class="label">Age:</span>
                        <span class="value">{{ $invoice->patient ? $invoice->patient->age : 'N/A' }} years</span>
                    </div>
                    <div class="compact-info">
                        <span class="label">Gender:</span>
                        <span class="value">{{ $invoice->patient ? ucfirst($invoice->patient->gender ?? 'N/A') : 'N/A' }}</span>
                    </div>
                </div>
                <div class="divider"></div>

                <!-- Doctor Information -->
                @if(isset($doctor) && $doctor)
                <div class="mb-2">
                    <div class="text-sm font-bold mb-1" style="color: #000000;">Doctor Information:</div>
                    <div class="compact-info">
                        <span class="label">Name:</span>
                        <span class="value">{{ $doctor->name ?? 'N/A' }}</span>
                    </div>
                    @if($doctor->specialization)
                    <div class="compact-info">
                        <span class="label">Specialization:</span>
                        <span class="value">{{ $doctor->specialization }}</span>
                    </div>
                    @endif
                    @if($doctor->license_number)
                    <div class="compact-info">
                        <span class="label">License:</span>
                        <span class="value">{{ $doctor->license_number }}</span>
                    </div>
                    @endif
                </div>
                <div class="divider"></div>
                @endif

                <!-- Invoice Details -->
                <div class="mb-2">
                    <div class="text-sm font-bold mb-1" style="color: #000000;">Invoice Details:</div>
                    <div class="compact-info">
                        <span class="label">Date:</span>
                        <span class="value">{{ $invoice->invoice_date->format('d/m/Y') }}</span>
                    </div>
                    <div class="compact-info">
                        <span class="label">Time:</span>
                        <span class="value">{{ $invoice->invoice_date->format('h:i A') }}</span>
                    </div>
                </div>
                <div class="divider"></div>
                @else
                <!-- Continuation Header for subsequent pages -->
                <div class="continuation-header">
                    MEDICAL INVOICE (Continued) - Invoice #: {{ $invoice->invoice_number }}
                </div>
                @endif

                <!-- Services/Tests Section -->
                <div class="services-section">
                    @if($pageIndex == 0)
                    <div class="section-title">Services & Tests</div>
                    @endif

                    <table class="services-table">
                        <thead>
                            <tr>
                                <th style="width: 40%;">Service/Test</th>
                                <th style="width: 15%;" class="text-center">Qty</th>
                                <th style="width: 20%;" class="text-right">Unit Price</th>
                                <th style="width: 15%;" class="text-right">Discount</th>
                                <th style="width: 20%;" class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pageItems as $item)
                            <tr>
                                <td>
                                    <div class="service-name">{{ $item->service_name }}</div>
                                    @if($item->service_type)
                                    <div class="service-details">Type: {{ ucfirst(str_replace('_', ' ', $item->service_type)) }}</div>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->quantity ?? 1 }}</td>
                                <td class="text-right">৳{{ number_format($item->unit_price ?? 0, 2) }}</td>
                                <td class="text-right">৳{{ number_format($item->line_discount ?? 0, 2) }}</td>
                                <td class="text-right">৳{{ number_format($item->total_price ?? 0, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center" style="padding: 20px; color: #6b7280;">
                                    No services or tests found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pageIndex == $totalPages - 1)
                <!-- Totals Section (Last Page Only) -->
                <div class="totals-section">
                    <div class="totals-wrapper">
                        <!-- Left space for PAID stamp -->
                        <div class="totals-left-space">
                            @if($invoice->status === 'paid' || ($paidAmount > 0 && $remainingAmount <= 0))
                                <div class="paid-stamp">
                                <div class="paid-stamp-inner">
                                    <div class="paid-text">PAID</div>
                                    <svg class="curved-text-svg" viewBox="0 0 150 150">
                                        <defs>
                                            <path id="circle-path" d="M 75, 75 m -60, 0 a 60,60 0 1,1 120,0 a 60,60 0 1,1 -120,0" />
                                        </defs>
                                        <text font-size="12" font-weight="bold" fill="#000">
                                            <textPath href="#circle-path" startOffset="0%">
                                                {{ $hospital->name ?? 'MEDICAL CENTER' }}
                                            </textPath>
                                        </text>
                                        <text font-size="10" font-weight="bold" fill="#000">
                                            <textPath href="#circle-path" startOffset="50%">
                                                INV-{{ $invoice->invoice_number }}
                                            </textPath>
                                        </text>
                                    </svg>
                                </div>
                        </div>
                        @endif
                    </div>

                    <!-- Totals table -->
                    <table class="totals-table">
                        <tr>
                            <td class="total-label">Subtotal:</td>
                            <td class="total-value">৳{{ number_format($subtotal, 2) }}</td>
                        </tr>
                        @if($discountAmount > 0)
                        <tr>
                            <td class="total-label">
                                Discount
                                @if($invoice->discount_percentage > 0)
                                ({{ $invoice->discount_percentage }}%)
                                @endif
                                :
                            </td>
                            <td class="total-value">-৳{{ number_format($discountAmount, 2) }}</td>
                        </tr>
                        @endif
                        @if($taxAmount > 0)
                        <tr>
                            <td class="total-label">
                                Tax
                                @if($invoice->tax_percentage > 0)
                                ({{ $invoice->tax_percentage }}%)
                                @endif
                                :
                            </td>
                            <td class="total-value">৳{{ number_format($taxAmount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="grand-total-row">
                            <td>Grand Total:</td>
                            <td>৳{{ number_format($grandTotal, 2) }}</td>
                        </tr>
                        @if($paidAmount > 0)
                        <tr>
                            <td class="total-label">Paid Amount:</td>
                            <td class="total-value paid-amount">৳{{ number_format($paidAmount, 2) }}</td>
                        </tr>
                        @endif
                        @if($remainingAmount > 0)
                        <tr>
                            <td class="total-label">Remaining Amount:</td>
                            <td class="total-value remaining-amount">৳{{ number_format($remainingAmount, 2) }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                @endif

                <!-- Page Footer -->
                @if($totalPages > 1)
                <div class="page-footer">
                    Page {{ $pageIndex + 1 }} of {{ $totalPages }}
                </div>
                @endif
            </div>
        </div>

        <!-- Invoice Footer -->
        <div class="print-info" style="text-align: center; margin: 10px 0; font-size: 0.9em;">
            <div>Printed on: {{ now()->format('d/m/Y h:i A') }}</div>
            <div>This is a computer-generated invoice and does not require a signature.</div>
        </div>

        <div class="powered-by" style="text-align: center; margin: 10px 0; font-size: 0.8em;">
            Powered by <span class="brand">ePATNER</span>
        </div>
        @endforeach
        @endforeach
        @else
        {{-- Single copy when double copy print is disabled --}}
        @foreach($itemChunks as $pageIndex => $pageItems)
        <div class="invoice-page {{ $pageIndex > 0 ? 'page-break' : '' }}">
            <div class="invoice-container">

                @if($pageIndex == 0)
                <!-- Letterhead Space for Official Header (First Page Only) -->
                <div class="letterhead-space">
                    @if(isset($letterhead) && $letterhead)
                    <!-- Professional Letterhead -->
                    @php
                    $logoPath = public_path('images/logo.png');
                    $hasLogo = file_exists($logoPath);
                    @endphp

                    @if($hasLogo)
                    <!-- Layout with Logo -->
                    <div style="display: flex; align-items: center; justify-content: space-between; height: 100%; padding: 0 10px;">
                        <!-- Logo on Left -->
                        <div style="flex: 0 0 80px; display: flex; align-items: center; justify-content: center;">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="max-width: 100%; max-height: 80px; object-fit: contain;">
                        </div>

                        <!-- Business Info on Right -->
                        <div style="flex: 1; text-align: center; padding-left: 15px;">
                            <!-- Business Names (Top) -->
                            @if($letterhead->business_name_bangla)
                            <div style="font-size: 16px; font-weight: bold; color: #000; margin-bottom: 2px; font-family: 'Arial', sans-serif;">
                                {{ $letterhead->business_name_bangla }}
                            </div>
                            @endif
                            @if($letterhead->business_name_english)
                            <div style="font-size: 14px; font-weight: bold; color: #000; margin-bottom: 4px;">
                                {{ $letterhead->business_name_english }}
                            </div>
                            @endif

                            <!-- Location -->
                            @if($letterhead->location)
                            <div style="font-size: 10px; color: #333; margin-bottom: 4px;">
                                📍 {{ $letterhead->location }}
                            </div>
                            @endif

                            <!-- Phone Numbers (One Line) -->
                            @if($letterhead->contacts && count($letterhead->contacts) > 0)
                            <div style="font-size: 10px; color: #333; margin-bottom: 2px;">
                                📞 {{ implode(' | ', array_filter($letterhead->contacts)) }}
                            </div>
                            @endif

                            <!-- Email -->
                            @if($letterhead->emails && count($letterhead->emails) > 0)
                            <div style="font-size: 10px; color: #333;">
                                ✉️ {{ implode(' | ', array_filter($letterhead->emails)) }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @else
                    <!-- Full Width Layout without Logo -->
                    <div style="display: flex; flex-direction: column; justify-content: center; height: 100%; padding: 0 10px; text-align: center; width: 100%;">
                        <!-- Business Names (Top) -->
                        @if($letterhead->business_name_bangla)
                        <div style="font-size: 16px; font-weight: bold; color: #000; margin-bottom: 2px; font-family: 'Arial', sans-serif;">
                            {{ $letterhead->business_name_bangla }}
                        </div>
                        @endif
                        @if($letterhead->business_name_english)
                        <div style="font-size: 14px; font-weight: bold; color: #000; margin-bottom: 4px;">
                            {{ $letterhead->business_name_english }}
                        </div>
                        @endif

                        <!-- Location -->
                        @if($letterhead->location)
                        <div style="font-size: 10px; color: #333; margin-bottom: 4px;">
                            📍 {{ $letterhead->location }}
                        </div>
                        @endif

                        <!-- Phone Numbers (One Line) -->
                        @if($letterhead->contacts && count($letterhead->contacts) > 0)
                        <div style="font-size: 10px; color: #333; margin-bottom: 2px;">
                            📞 {{ implode(' | ', array_filter($letterhead->contacts)) }}
                        </div>
                        @endif

                        <!-- Email -->
                        @if($letterhead->emails && count($letterhead->emails) > 0)
                        <div style="font-size: 10px; color: #333;">
                            ✉️ {{ implode(' | ', array_filter($letterhead->emails)) }}
                        </div>
                        @endif
                    </div>
                    @endif
                    @else
                    <div class="letterhead-placeholder">
                        [Space reserved for official letterhead]
                    </div>
                    @endif
                </div>

                <!-- Invoice Header (First Page Only) -->
                <div class="invoice-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; padding-bottom: 4px; border-bottom: 1px solid #000;">
                    <div style="flex: 1;">
                        <div class="invoice-title">MEDICAL INVOICE</div>
                        <div class="invoice-number">Invoice #: {{ $invoice->invoice_number }}</div>
                    </div>
                </div>

                <!-- Invoice Details (First Page Only) -->
                <div class="invoice-details">
                    <!-- Patient Information -->
                    <div class="mb-2">
                        <div class="text-sm font-bold mb-1" style="color: #000000;">Patient Information:</div>
                        <div class="compact-info">
                            <span class="label">Name:</span>
                            <span class="value">{{ $invoice->patient ? $invoice->patient->full_name : 'N/A' }}</span>
                        </div>
                        <div class="compact-info">
                            <span class="label">ID:</span>
                            <span class="value">{{ $invoice->patient ? $invoice->patient->patient_id : 'N/A' }}</span>
                        </div>
                        <div class="compact-info">
                            <span class="label">Phone:</span>
                            <span class="value">{{ $invoice->patient ? $invoice->patient->phone : 'N/A' }}</span>
                        </div>
                        <div class="compact-info">
                            <span class="label">Age:</span>
                            <span class="value">{{ $invoice->patient ? $invoice->patient->age : 'N/A' }} years</span>
                        </div>
                        <div class="compact-info">
                            <span class="label">Gender:</span>
                            <span class="value">{{ $invoice->patient ? ucfirst($invoice->patient->gender ?? 'N/A') : 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="divider"></div>

                    <!-- Doctor Information -->
                    @if(isset($doctor) && $doctor)
                    <div class="mb-2">
                        <div class="text-sm font-bold mb-1" style="color: #000000;">Doctor Information:</div>
                        <div class="compact-info">
                            <span class="label">Name:</span>
                            <span class="value">{{ $doctor->name ?? 'N/A' }}</span>
                        </div>
                        @if($doctor->specialization)
                        <div class="compact-info">
                            <span class="label">Specialization:</span>
                            <span class="value">{{ $doctor->specialization }}</span>
                        </div>
                        @endif
                        @if($doctor->license_number)
                        <div class="compact-info">
                            <span class="label">License:</span>
                            <span class="value">{{ $doctor->license_number }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="divider"></div>
                    @endif

                    <!-- Invoice Details -->
                    <div class="mb-2">
                        <div class="text-sm font-bold mb-1" style="color: #000000;">Invoice Details:</div>
                        <div class="compact-info">
                            <span class="label">Date:</span>
                            <span class="value">{{ $invoice->invoice_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="compact-info">
                            <span class="label">Time:</span>
                            <span class="value">{{ $invoice->invoice_date->format('h:i A') }}</span>
                        </div>
                    </div>
                    <div class="divider"></div>
                    @else
                    <!-- Continuation Header for subsequent pages -->
                    <div class="continuation-header">
                        MEDICAL INVOICE (Continued) - Invoice #: {{ $invoice->invoice_number }}
                    </div>
                    @endif

                    <!-- Services/Tests Section -->
                    <div class="services-section">
                        @if($pageIndex == 0)
                        <div class="section-title">Services & Tests</div>
                        @endif

                        <table class="services-table">
                            <thead>
                                <tr>
                                    <th style="width: 40%;">Service/Test</th>
                                    <th style="width: 15%;" class="text-center">Qty</th>
                                    <th style="width: 20%;" class="text-right">Unit Price</th>
                                    <th style="width: 15%;" class="text-right">Discount</th>
                                    <th style="width: 20%;" class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pageItems as $item)
                                <tr>
                                    <td>
                                        <div class="service-name">{{ $item->service_name }}</div>
                                        @if($item->service_type)
                                        <div class="service-details">Type: {{ ucfirst(str_replace('_', ' ', $item->service_type)) }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->quantity ?? 1 }}</td>
                                    <td class="text-right">৳{{ number_format($item->unit_price ?? 0, 2) }}</td>
                                    <td class="text-right">৳{{ number_format($item->line_discount ?? 0, 2) }}</td>
                                    <td class="text-right">৳{{ number_format($item->total_price ?? 0, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center" style="padding: 20px; color: #6b7280;">
                                        No services or tests found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($pageIndex == $totalPages - 1)
                    <!-- Totals Section (Last Page Only) -->
                    <div class="totals-section">
                        <div class="totals-wrapper">
                            <!-- Left space for PAID stamp -->
                            <div class="totals-left-space">
                                @if($invoice->status === 'paid' || ($paidAmount > 0 && $remainingAmount <= 0))
                                    <div class="paid-stamp">
                                    <div class="paid-stamp-inner">
                                        <div class="paid-text">PAID</div>
                                        <svg class="curved-text-svg" viewBox="0 0 150 150">
                                            <defs>
                                                <path id="circle-path" d="M 75, 75 m -60, 0 a 60,60 0 1,1 120,0 a 60,60 0 1,1 -120,0" />
                                            </defs>
                                            <text font-size="12" font-weight="bold" fill="#000">
                                                <textPath href="#circle-path" startOffset="0%">
                                                    {{ $hospital->name ?? 'MEDICAL CENTER' }}
                                                </textPath>
                                            </text>
                                            <text font-size="10" font-weight="bold" fill="#000">
                                                <textPath href="#circle-path" startOffset="50%">
                                                    INV-{{ $invoice->invoice_number }}
                                                </textPath>
                                            </text>
                                        </svg>
                                    </div>
                            </div>
                            @endif
                        </div>

                        <!-- Totals table -->
                        <table class="totals-table">
                            <tr>
                                <td class="total-label">Subtotal:</td>
                                <td class="total-value">৳{{ number_format($subtotal, 2) }}</td>
                            </tr>
                            @if($discountAmount > 0)
                            <tr>
                                <td class="total-label">
                                    Discount
                                    @if($invoice->discount_percentage > 0)
                                    ({{ $invoice->discount_percentage }}%)
                                    @endif
                                    :
                                </td>
                                <td class="total-value">-৳{{ number_format($discountAmount, 2) }}</td>
                            </tr>
                            @endif
                            @if($taxAmount > 0)
                            <tr>
                                <td class="total-label">
                                    Tax
                                    @if($invoice->tax_percentage > 0)
                                    ({{ $invoice->tax_percentage }}%)
                                    @endif
                                    :
                                </td>
                                <td class="total-value">৳{{ number_format($taxAmount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="grand-total-row">
                                <td>Grand Total:</td>
                                <td>৳{{ number_format($grandTotal, 2) }}</td>
                            </tr>
                            @if($paidAmount > 0)
                            <tr>
                                <td class="total-label">Paid Amount:</td>
                                <td class="total-value paid-amount">৳{{ number_format($paidAmount, 2) }}</td>
                            </tr>
                            @endif
                            @if($remainingAmount > 0)
                            <tr>
                                <td class="total-label">Remaining Amount:</td>
                                <td class="total-value remaining-amount">৳{{ number_format($remainingAmount, 2) }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    @endif

                    <!-- Page Footer -->
                    @if($totalPages > 1)
                    <div class="page-footer">
                        Page {{ $pageIndex + 1 }} of {{ $totalPages }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Invoice Footer -->
            <div class="print-info" style="text-align: center; margin: 10px 0; font-size: 0.9em;">
                <div>Printed on: {{ now()->format('d/m/Y h:i A') }}</div>
                <div>This is a computer-generated invoice and does not require a signature.</div>
            </div>

            <div class="powered-by" style="text-align: center; margin: 10px 0; font-size: 0.8em;">
                Powered by <span class="brand">ePATNER</span>
            </div>
            @endforeach
            @endif
</body>

</html>