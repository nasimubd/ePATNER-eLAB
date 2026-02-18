<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Invoice - A5 Print</title>
    <style>
        @page {
            margin: 5mm 8mm;
            size: A5;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Courier Prime', 'Courier New', monospace;
            background-color: white;
            color: #000000;
            line-height: 1.0;
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
            max-width: 148mm;
            margin: 0 auto;
            padding: 2px;
            background-color: white;
            position: relative;
            border-bottom: 1px dashed #000;
            padding-top: 2px;
            padding-bottom: 2px;
        }

        @media print {
            .invoice-container {
                padding-left: 5mm;
                padding-right: 5mm;
            }
        }

        /* Letterhead Space - Reserved for official header */
        .letterhead-space {
            height: 15mm;
            margin-bottom: 8px;
            border-bottom: 1px dashed #000;
            position: relative;
            padding-top: 3mm;
            padding-bottom: 3mm;
        }

        .letterhead-placeholder {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #000;
            font-size: 10px;
            text-align: center;
            font-style: italic;
        }

        /* Payment Status Stamps - Circular Design */
        .payment-stamp {
            width: 80px;
            height: 80px;
            border: 4px solid #000;
            border-radius: 50%;
            background-color: #f8f8f8;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 0 0 2px #000 inset;
        }

        .payment-stamp-text {
            font-size: 16px;
            font-weight: 900;
            color: #000;
            text-align: center;
            letter-spacing: 1px;
            text-transform: uppercase;
            line-height: 1;
        }

        /* PAID Stamp Specific */
        .paid-stamp {
            background-color: #f0f0f0;
            border: 4px solid #000;
        }

        /* UNPAID Stamp Specific */
        .unpaid-stamp {
            background-color: #f0f0f0;
            border: 4px solid #000;
        }

        /* Legacy styles for compatibility */
        .paid-stamp-inner {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .paid-text {
            font-size: 25px;
            font-weight: 900;
            color: #000;
            text-align: center;
            letter-spacing: 1px;
        }

        .curved-text-svg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .curved-text-svg text {
            font-size: 8px;
            font-weight: bold;
            fill: #000;
            font-family: 'Courier Prime', 'Courier New', monospace;
        }

        /* Invoice Header */
        .invoice-header {
            text-align: center;
            margin-bottom: 4px;
            padding-bottom: 2px;
            border-bottom: 1px solid #000;
        }

        .invoice-title {
            font-size: 25px;
            font-weight: bold;
            color: #000;
            margin: 2px 0 2px 0;
        }

        .invoice-number {
            font-size: 10px;
            color: #000;
            font-weight: 600;
        }

        /* Two Column Layout */
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            gap: 8px;
        }

        .patient-info,
        .invoice-info {
            flex: 1;
            background-color: transparent;
            padding: 4px;
            border: 1px solid #000;
            border-radius: 0;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #000;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1px;
            padding: 0;
        }

        .info-label {
            font-weight: bold;
            color: #000;
            min-width: 40px;
            font-size: 10px;
        }

        .info-value {
            color: #000;
            font-weight: bold;
            font-size: 10px;
            text-align: right;
            flex: 1;
        }

        /* Services Table */
        .services-section {
            margin: 3px 0;
        }

        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
            border: 1px solid #000;
        }

        .services-table th {
            background-color: transparent;
            color: #000;
            padding: 2px 1px;
            text-align: left;
            font-weight: 900;
            font-size: 14px;
            border: 1px solid #000;
        }

        .services-table td {
            padding: 1px 1px;
            border: 1px solid #000;
            vertical-align: top;
            font-size: 12px;
            font-weight: 900;
            line-height: 1.0;
        }

        .services-table tr:nth-child(even) {
            background-color: transparent;
        }

        .service-name {
            font-weight: bold;
            color: #000;
            margin-bottom: 0px;
            font-size: 7px;
        }

        .service-details {
            font-size: 6px;
            color: #000;
            line-height: 1.0;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Totals Section */
        .totals-section {
            margin-top: 2px;
            margin-bottom: 2px;
            position: relative;
        }

        .totals-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            justify-content: space-between;
        }

        .totals-left-space {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 0;
        }

        .totals-table {
            width: 150px;
            border-collapse: collapse;
            border: 1px solid #000;
        }

        .totals-table td {
            padding: 2px 4px;
            border: 1px solid #000;
            font-size: 7px;
        }

        .totals-table .total-label {
            font-weight: 600;
            color: #000;
        }

        .totals-table .total-value {
            text-align: right;
            font-weight: 600;
            color: #000;
        }

        .grand-total-row {
            background-color: transparent;
            color: #000;
            font-weight: bold;
            font-size: 15px;
        }

        .grand-total-row td {
            border: 1px solid #000;
            padding: 3px 4px;
        }

        .paid-amount {
            color: #000 !important;
            font-weight: bold;
        }

        .remaining-amount {
            color: #000 !important;
            font-weight: bold;
        }

        /* Footer */
        .invoice-footer {
            margin-top: 4px;
            padding-top: 3px;
            border-top: 1px solid #000;
            text-align: center;
            clear: both;
            width: 100%;
        }

        .footer-message {
            font-size: 8px;
            color: #000;
            margin-bottom: 1px;
        }

        .payment-status {
            font-size: 7px;
            font-weight: bold;
            margin: 2px 0;
        }

        .payment-completed {
            color: #000;
        }

        .payment-pending {
            color: #000;
        }

        /* UNPAID stamp highlighting - Black only */
        .unpaid-highlight {
            background-color: #f0f0f0 !important;
            border: 4px solid #000 !important;
            color: #000 !important;
            font-weight: 900 !important;
            box-shadow: 0 0 0 2px #000 inset !important;
        }

        .print-info {
            margin-top: 3px;
            padding: 3px 0;
            border-top: 1px dashed #000;
            font-size: 8px;
            color: #000;
            page-break-inside: avoid;
            clear: both;
            width: 100%;
        }

        .powered-by {
            margin-top: 0px;
            font-size: 7px;
            font-weight: 900;
            color: #000;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            page-break-inside: avoid;
        }

        .powered-by .brand {
            color: #000;
            font-size: 8px;
        }

        .footer-qr {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        /* Page Break Styles */
        .page-break {
            page-break-before: always;
            break-before: page;
        }

        .page-break-avoid {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .invoice-page {
            min-height: auto;
            height: auto;
            position: relative;
            page-break-after: auto;
        }

        .continuation-header {
            display: none;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #000;
        }

        .page-footer {
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 8px;
            color: #666;
        }

        @media print {
            .print-controls {
                display: none;
            }

            body {
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .invoice-container {
                padding: 0;
            }

            .letterhead-placeholder {
                display: none;
            }

            .payment-stamp {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .paid-stamp {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .unpaid-stamp {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .services-table th {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .grand-total-row {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .continuation-header {
                display: block;
            }

            .page-footer {
                display: block;
            }

            .invoice-page {
                min-height: auto;
                height: auto;
                page-break-after: auto;
            }
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .invoice-details {
                flex-direction: column;
                gap: 15px;
            }

            .totals-wrapper {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }

            .totals-left-space {
                padding-top: 0;
            }

            .totals-table {
                width: 100%;
                max-width: 250px;
            }

            .payment-stamp {
                width: 60px;
                height: 60px;
            }

            .payment-stamp-text {
                font-size: 25px;
            }
        }
    </style>
</head>

<body>
    <div class="print-controls">
        <button onclick="window.print()" class="print-btn">
            <svg style="width: 16px; height: 16px; display: inline-block; margin-right: 5px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print A5
        </button>
        <a href="{{ route('admin.medical.invoices.show', $invoice) }}" class="back-btn">
            <svg style="width: 16px; height: 16px; display: inline-block; margin-right: 5px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Invoice
        </a>
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
    <div class="invoice-page {{ $copyIndex > 0 ? 'page-break' : '' }}" style="{{ $copyIndex > 0 ? 'page-break-before: always;' : '' }}">
        <div class="invoice-container" style="page-break-inside: avoid;">

            @if($pageIndex == 0)
            <!-- Letterhead Space (First Page Only) -->
            <div class="letterhead-space">
                @if(isset($letterhead) && $letterhead)
                <!-- Professional Letterhead -->
                @php
                $logoPath = public_path('images/logo.jpg');
                $hasLogo = file_exists($logoPath);
                @endphp

                @if($hasLogo)
                <!-- Layout with Logo -->
                <div style="display: flex; align-items: center; justify-content: space-between; height: 100%; padding: 0 8px; text-align: justify;">
                    <!-- Logo on Left -->
                    @if(file_exists(public_path('images/logo.jpg')))
                    <div style="flex: 0 0 60px; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Logo" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                    </div>
                    @endif

                    <!-- Business Info -->
                    <div style="flex: 1 1 100%; @if(!file_exists(public_path('images/logo.jpg'))) width: 100%; padding: 0 5px; @else padding: 0 10px; @endif">
                        <!-- Business Names (Top) -->
                        @if($letterhead->business_name_bangla)
                        <div style="font-size: 16px; font-weight: bold; color: #000; margin-bottom: 1px; font-family: 'Arial', sans-serif; text-align: justify;">
                            {{ $letterhead->business_name_bangla }}
                        </div>
                        @endif
                        @if($letterhead->business_name_english)
                        <div style="font-size: 14px; font-weight: bold; color: #000; margin-bottom: 3px; text-align: justify;">
                            {{ $letterhead->business_name_english }}
                        </div>
                        @endif

                        <!-- Location -->
                        @if($letterhead->location)
                        <div style="font-size: 10px; color: #333; font-weight: bold; margin-bottom: 3px; text-align: justify;">
                            {{ $letterhead->location }}
                        </div>
                        @endif

                        <!-- Phone Numbers (One Line) -->
                        @if($letterhead->contacts && count($letterhead->contacts) > 0)
                        <div style="font-size: 10px; color: #333; font-weight: bold; margin-bottom: 1px; text-align: justify;">
                            {{ implode(' | ', array_filter($letterhead->contacts)) }}
                        </div>
                        @endif

                        <!-- Email -->
                        @if($letterhead->emails && count($letterhead->emails) > 0)
                        <div style="font-size: 10px; color: #333; font-weight: bold; text-align: justify;">
                            {{ implode(' | ', array_filter($letterhead->emails)) }}
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <!-- Full Width Layout without Logo -->
                <div style="display: flex; flex-direction: column; justify-content: center; height: 100%; padding: 0 8px; text-align: justify; width: 100%;">
                    <!-- Business Names (Top) -->
                    @if($letterhead->business_name_bangla)
                    <div style="font-size: 18px; font-weight: bold; color: #000; margin-bottom: 1px; font-family: 'Arial', sans-serif; text-align: justify;">
                        {{ $letterhead->business_name_bangla }}
                    </div>
                    @endif
                    @if($letterhead->business_name_english)
                    <div style="font-size: 15px; font-weight: bold; color: #000; margin-bottom: 3px; text-align: justify;">
                        {{ $letterhead->business_name_english }}
                    </div>
                    @endif

                    <!-- Location -->
                    @if($letterhead->location)
                    <div style="font-size: 12px; color: #333; font-weight: bold; margin-bottom: 3px; text-align: justify;">
                        {{ $letterhead->location }}
                    </div>
                    @endif

                    <!-- Phone Numbers (One Line) -->
                    @if($letterhead->contacts && count($letterhead->contacts) > 0)
                    <div style="font-size: 13px; color: #333; font-weight: bold; margin-bottom: 1px; text-align: justify;">
                        {{ implode(' | ', array_filter($letterhead->contacts)) }}
                    </div>
                    @endif

                    <!-- Email -->
                    @if($letterhead->emails && count($letterhead->emails) > 0)
                    <div style="font-size: 13px; color: #333; font-weight: bold; text-align: justify;">
                        {{ implode(' | ', array_filter($letterhead->emails)) }}
                    </div>
                    @endif
                </div>
                @endif
                @else
                <div class="letterhead-placeholder">
                    [LETTERHEAD SPACE - 20MM]
                </div>
                @endif
            </div>



            <!-- Invoice Header with Barcodes (First Page Only) -->
            <div class="invoice-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; padding-bottom: 4px; border-bottom: 1px solid #000;">
                <!-- Lab ID Barcode (Left) -->
                <div style="flex: 0 0 auto; display: flex; flex-direction: column; align-items: center;">
                    <div style="font-size: 12px; font-weight: bold; color: #000; margin-bottom: 2px;">LAB ID: {{ $invoice->lab_id ?? 'N/A' }}</div>
                    @if(isset($labIdBarcode) && $labIdBarcode)
                    {!! $labIdBarcode !!}
                    @endif
                </div>

                <!-- Invoice Title (Center) -->
                <div style="flex: 1; text-align: center;">
                    <div class="invoice-title">MEDICAL INVOICE</div>
                    <div class="invoice-number">PATIENT ID: {{ $invoice->patient ? $invoice->patient->id : 'N/A' }}</div>
                </div>

                <!-- Invoice Number Barcode (Right) -->
                <div style="flex: 0 0 auto; display: flex; flex-direction: column; align-items: center;">
                    <div style="font-size: 12px; font-weight: bold; color: #000; margin-bottom: 2px;">INVOICE ID: {{ $invoice->invoice_number }}</div>
                    @if(isset($invoiceNumberBarcode) && $invoiceNumberBarcode)
                    {!! $invoiceNumberBarcode !!}
                    @endif
                </div>
            </div>

            <!-- Invoice Details (First Page Only) -->
            <div class="invoice-details">
                <!-- Patient Information -->
                <div class="patient-info">
                    <div class="section-title">Patient Information</div>
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span class="info-value">{{ $invoice->patient ? $invoice->patient->full_name : 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Age:</span>
                        <span class="info-value">{{ $invoice->patient ? $invoice->patient->age : 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Gender:</span>
                        <span class="info-value">{{ $invoice->patient ? ucfirst($invoice->patient->gender) : 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone:</span>
                        <span class="info-value">{{ $invoice->patient ? $invoice->patient->phone : 'N/A' }}</span>
                    </div>
                </div>

                <!-- Invoice Information -->
                <div class="invoice-info">
                    <div class="section-title">Invoice Information</div>
                    <div class="info-row">
                        <span class="info-label">Date:</span>
                        <span class="info-value">{{ $invoice->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Time:</span>
                        <span class="info-value">{{ $invoice->created_at->format('h:i A') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value">{{ ucfirst($invoice->status) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Doctor:</span>
                        <span class="info-value" style="font-weight: bold; font-size: 12px;">{{ $doctor ? $doctor->name : 'N/A' }}</span>
                    </div>
                </div>
            </div>
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
                            <th style="width: 15%; font-size: 12px; font-weight: 1000; text-align: center; text-transform: uppercase;">SL. NO.</th>
                            <th style="width: 50%; font-size: 12px; font-weight: 1000; text-align: center; text-transform: uppercase;">DESCRIPTION</th>
                            <th style="width: 20%; font-size: 12px; font-weight: 1000; text-align: center; text-transform: uppercase;">UNIT PRICE</th>
                            <th style="width: 15%; font-size: 12px; font-weight: 1000; text-align: center; text-transform: uppercase;">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pageItems as $index => $item)
                        <tr>
                            <td class="text-center" style="font-size: 11px; font-weight: bold;">{{ $loop->iteration }}</td>
                            <td class="text-center" style="font-size: 11px; font-weight: bold;">
                                {{ $item->service_name }}
                            </td>
                            <td class="text-center" style="font-size: 11px; font-weight: bold;">{{ number_format($item->unit_price ?? 0, 2) }}</td>
                            <td class="text-center" style="font-size: 11px; font-weight: bold;">{{ number_format($item->total_price ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center" style="padding: 20px; color: #6b7280;">
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
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 15px; margin-top: 2px; margin-bottom: 2px; position: relative;">
                    <!-- Left Column - Payment Summary -->
                    <div style="flex: 1; position: relative; display: flex; align-items: center; gap: 10px;">
                        <!-- Payment Status Stamp -->
                        <div style="flex-shrink: 0;">
                            @if($invoice->status === 'paid' || ($paidAmount > 0 && $remainingAmount <= 0))
                                <div class="payment-stamp paid-stamp">
                                <div class="payment-stamp-text">PAID</div>
                        </div>
                        @else
                        <div class="payment-stamp unpaid-stamp unpaid-highlight">
                            <div class="payment-stamp-text">UNPAID</div>
                        </div>
                        @endif
                    </div>

                    <!-- Payment Summary Table -->
                    <div style="flex: 1;">
                        <table style="width: 100%; border-collapse: collapse; border: 2px solid #000;">
                            <tr>
                                <td style="padding: 6px 8px; border: 1px solid #000; font-size: 10px; font-weight: bold; color: #000;">Amount</td>
                                <td style="padding: 6px 8px; border: 1px solid #000; font-size: 10px; font-weight: bold; color: #000; text-align: right;">{{ number_format($grandTotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 6px 8px; border: 1px solid #000; font-size: 10px; font-weight: bold; color: #000;">Collection</td>
                                <td style="padding: 6px 8px; border: 1px solid #000; font-size: 10px; font-weight: bold; color: #000; text-align: right;">{{ number_format($paidAmount, 2) }}</td>
                            </tr>
                            <tr style="background-color: #ffe6e6;">
                                <td style="padding: 6px 8px; border: 1px solid #000; font-size: 16px; font-weight: 1000; color: #cc0000;">DUE</td>
                                <td style="padding: 6px 8px; border: 1px solid #000; font-size: 16px; font-weight: 1000; color: #cc0000; text-align: right;">{{ number_format($remainingAmount, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Right Column - Invoice Summary -->
                <div style="flex: 1;">
                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
                        <tr>
                            <td style="padding: 6px 8px; border: 1px solid #000; font-size: 11px; font-weight: 600; color: #000;">Total Amount</td>
                            <td style="padding: 6px 8px; border: 1px solid #000; font-size: 11px; font-weight: 600; color: #000; text-align: right;">{{ number_format($subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 8px; border: 1px solid #000; font-size: 11px; font-weight: 600; color: #000;">Invoice Discount</td>
                            <td style="padding: 6px 8px; border: 1px solid #000; font-size: 11px; font-weight: 600; color: #000; text-align: right;">{{ number_format($discountAmount, 2) }}</td>
                        </tr>
                        <tr style="background-color: transparent; color: #000;">
                            <td style="padding: 8px 10px; border: 1px solid #000; font-size: 12px; font-weight: 1000;">GRAND TOTAL</td>
                            <td style="padding: 8px 10px; border: 1px solid #000; font-size: 16px; font-weight: 1000; text-align: right;">{{ number_format($grandTotal, 2) }}</td>
                        </tr>
                    </table>
                </div>


            </div>
            @endif

            <!-- Invoice Footer - Below Payment Tables -->
            @if($pageIndex == $totalPages - 1)
            <div style="margin-top: 4px; page-break-inside: avoid;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #000; padding: 3px 0;">
                    <!-- Left Side: Text Information -->
                    <div style="flex: 1; text-align: left;">
                        <div style="font-size: 10px; margin-bottom: 1px; color: #000; font-weight: 700;">Printed: {{ now()->format('d/m/Y h:i A') }}</div>
                        <div style="font-size: 10px; color: #000; line-height: 1; font-weight: 700;">This is a computer-generated invoice and does not require a signature.</div>
                        <div style="font-size: 7px; font-weight: 900; margin-top: 2px; letter-spacing: 0.5px; color: #000;">
                            POWERED BY <span style="font-size: 8px;">ePATNER | eLAB (SCAN THE QR CODE TO GET MORE INFO....)</span>
                        </div>
                    </div>

                    <!-- Right Side: QR Code -->
                    <div style="flex: 0 0 auto; text-align: center;">
                        <img src="{{ asset('images/ePATNER_QR.png') }}" alt="ePATNER QR" style="width: 35px; height: 31px; object-fit: contain; display: block;">
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Page Footer -->
    @if($totalPages > 1)
    <div class="page-footer">
        Page {{ $pageIndex + 1 }} of {{ $totalPages }}
    </div>
    @endif
    @endforeach
    @endforeach
    @else
    {{-- Single copy when double copy print is disabled --}}
    @foreach($itemChunks as $pageIndex => $pageItems)
    <div class="invoice-page {{ $pageIndex > 0 ? 'page-break' : '' }}">
        <div class="invoice-container">

            @if($pageIndex == 0)
            <!-- Letterhead Space (First Page Only) -->
            <div class="letterhead-space">
                @if(isset($letterhead) && $letterhead)
                <!-- Professional Letterhead -->
                @php
                $logoPath = public_path('images/logo.png');
                $hasLogo = file_exists($logoPath);
                @endphp

                @if($hasLogo)
                <!-- Layout with Logo -->
                <div style="display: flex; align-items: center; justify-content: space-between; height: 100%; padding: 0 8px;">
                    <!-- Logo on Left -->
                    <div style="flex: 0 0 60px; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Logo" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                    </div>

                    <!-- Business Info on Right -->
                    <div style="flex: 1; text-align: center; padding-left: 10px;">
                        <!-- Business Names (Top) -->
                        @if($letterhead->business_name_bangla)
                        <div style="font-size: 18px; font-weight: bold; color: #000; margin-bottom: 1px; font-family: 'Arial', sans-serif;">
                            {{ $letterhead->business_name_bangla }}
                        </div>
                        @endif
                        @if($letterhead->business_name_english)
                        <div style="font-size: 15px; font-weight: bold; color: #000; margin-bottom: 3px;">
                            {{ $letterhead->business_name_english }}
                        </div>
                        @endif

                        <!-- Location -->
                        @if($letterhead->location)
                        <div style="font-size: 12px; color: #333; font-weight: bold; margin-bottom: 3px;">
                            {{ $letterhead->location }}
                        </div>
                        @endif

                        <!-- Phone Numbers (One Line) -->
                        @if($letterhead->contacts && count($letterhead->contacts) > 0)
                        <div style="font-size: 13px; color: #333; font-weight: bold; margin-bottom: 1px;">
                            {{ implode(' | ', array_filter($letterhead->contacts)) }}
                        </div>
                        @endif

                        <!-- Email -->
                        @if($letterhead->emails && count($letterhead->emails) > 0)
                        <div style="font-size: 13px; color: #333; font-weight: bold;">
                            {{ implode(' | ', array_filter($letterhead->emails)) }}
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <!-- Full Width Layout without Logo -->
                <div style="display: flex; flex-direction: column; justify-content: center; height: 100%; padding: 0 8px; text-align: center; width: 100%;">
                    <!-- Business Names (Top) -->
                    @if($letterhead->business_name_bangla)
                    <div style="font-size: 18px; font-weight: bold; color: #000; margin-bottom: 1px; font-family: 'Arial', sans-serif;">
                        {{ $letterhead->business_name_bangla }}
                    </div>
                    @endif
                    @if($letterhead->business_name_english)
                    <div style="font-size: 15px; font-weight: bold; color: #000; margin-bottom: 3px;">
                        {{ $letterhead->business_name_english }}
                    </div>
                    @endif

                    <!-- Location -->
                    @if($letterhead->location)
                    <div style="font-size: 12px; color: #333; font-weight: bold; margin-bottom: 3px;">
                        {{ $letterhead->location }}
                    </div>
                    @endif

                    <!-- Phone Numbers (One Line) -->
                    @if($letterhead->contacts && count($letterhead->contacts) > 0)
                    <div style="font-size: 13px; color: #333; font-weight: bold; margin-bottom: 1px;">
                        {{ implode(' | ', array_filter($letterhead->contacts)) }}
                    </div>
                    @endif

                    <!-- Email -->
                    @if($letterhead->emails && count($letterhead->emails) > 0)
                    <div style="font-size: 13px; color: #333; font-weight: bold;">
                        {{ implode(' | ', array_filter($letterhead->emails)) }}
                    </div>
                    @endif
                </div>
                @endif
                @else
                <div class="letterhead-placeholder">
                    [LETTERHEAD SPACE - 20MM]
                </div>
                @endif
            </div>



            <!-- Invoice Header with Barcodes (First Page Only) -->
            <div class="invoice-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; padding-bottom: 4px; border-bottom: 1px solid #000;">
                <!-- Lab ID Barcode (Left) -->
                <div style="flex: 0 0 auto; display: flex; flex-direction: column; align-items: center;">
                    <div style="font-size: 12px; font-weight: bold; color: #000; margin-bottom: 2px;">LAB ID: {{ $invoice->lab_id ?? 'N/A' }}</div>
                    @if(isset($labIdBarcode) && $labIdBarcode)
                    {!! $labIdBarcode !!}
                    @endif
                </div>

                <!-- Invoice Title (Center) -->
                <div style="flex: 1; text-align: center;">
                    <div class="invoice-title">MEDICAL INVOICE</div>
                    <div class="invoice-number">Patient ID: {{ $invoice->patient ? $invoice->patient->id : 'N/A' }}</div>
                </div>

                <!-- Invoice Number Barcode (Right) -->
                <div style="flex: 0 0 auto; display: flex; flex-direction: column; align-items: center;">
                    <div style="font-size: 12px; font-weight: bold; color: #000; margin-bottom: 2px;">INVOICE ID: {{ $invoice->invoice_number }}</div>
                    @if(isset($invoiceNumberBarcode) && $invoiceNumberBarcode)
                    {!! $invoiceNumberBarcode !!}
                    @endif
                </div>
            </div>

            <!-- Invoice Details (First Page Only) -->
            <div class="invoice-details">
                <!-- Patient Information -->
                <div class="patient-info">
                    <div class="section-title">Patient Information</div>
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span class="info-value">{{ $invoice->patient ? $invoice->patient->full_name : 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Age:</span>
                        <span class="info-value">{{ $invoice->patient ? $invoice->patient->age : 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Gender:</span>
                        <span class="info-value">{{ $invoice->patient ? ucfirst($invoice->patient->gender) : 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone:</span>
                        <span class="info-value">{{ $invoice->patient ? $invoice->patient->phone : 'N/A' }}</span>
                    </div>
                </div>

                <!-- Invoice Information -->
                <div class="invoice-info">
                    <div class="section-title">Invoice Information</div>
                    <div class="info-row">
                        <span class="info-label">Date:</span>
                        <span class="info-value">{{ $invoice->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Time:</span>
                        <span class="info-value">{{ $invoice->created_at->format('h:i A') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value">{{ ucfirst($invoice->status) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Doctor:</span>
                        <span class="info-value" style="font-weight: bold; font-size: 12px;">{{ $doctor ? $doctor->name : 'N/A' }}</span>
                    </div>
                </div>
            </div>
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
                            <th style="width: 15%; font-size: 12px; font-weight: 1000; text-align: center; text-transform: uppercase;">SL. NO.</th>
                            <th style="width: 50%; font-size: 12px; font-weight: 1000; text-align: center; text-transform: uppercase;">DESCRIPTION</th>
                            <th style="width: 20%; font-size: 12px; font-weight: 1000; text-align: center; text-transform: uppercase;">UNIT PRICE</th>
                            <th style="width: 15%; font-size: 12px; font-weight: 1000; text-align: center; text-transform: uppercase;">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pageItems as $index => $item)
                        <tr>
                            <td class="text-center" style="font-size: 11px; font-weight: bold;">{{ $loop->iteration }}</td>
                            <td class="text-center" style="font-size: 11px; font-weight: bold;">
                                {{ $item->service_name }}
                            </td>
                            <td class="text-center" style="font-size: 11px; font-weight: bold;">{{ number_format($item->unit_price ?? 0, 2) }}</td>
                            <td class="text-center" style="font-size: 11px; font-weight: bold;">{{ number_format($item->total_price ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center" style="padding: 20px; color: #6b7280;">
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
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 15px; margin-top: 2px; margin-bottom: 2px; position: relative;">
                    <!-- Left Column - Payment Summary -->
                    <div style="flex: 1; position: relative; display: flex; align-items: center; gap: 10px;">
                        <!-- Payment Status Stamp -->
                        <div style="flex-shrink: 0;">
                            @if($invoice->status === 'paid' || ($paidAmount > 0 && $remainingAmount <= 0))
                                <div class="payment-stamp paid-stamp">
                                <div class="payment-stamp-text">PAID</div>
                        </div>
                        @else
                        <div class="payment-stamp unpaid-stamp unpaid-highlight">
                            <div class="payment-stamp-text">UNPAID</div>
                        </div>
                        @endif
                    </div>

                    <!-- Payment Summary Table -->
                    <div style="flex: 1;">
                        <table style="width: 100%; border-collapse: collapse; border: 2px solid #000;">
                            <tr>
                                <td style="padding: 6px 8px; border: 1px solid #000; font-size: 10px; font-weight: bold; color: #000;">Amount</td>
                                <td style="padding: 6px 8px; border: 1px solid #000; font-size: 10px; font-weight: bold; color: #000; text-align: right;">{{ number_format($grandTotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 6px 8px; border: 1px solid #000; font-size: 10px; font-weight: bold; color: #000;">Collection</td>
                                <td style="padding: 6px 8px; border: 1px solid #000; font-size: 10px; font-weight: bold; color: #000; text-align: right;">{{ number_format($paidAmount, 2) }}</td>
                            </tr>
                            <tr style="background-color: #ffe6e6;">
                                <td style="padding: 6px 8px; border: 1px solid #000; font-size: 16px; font-weight: 1000; color: #cc0000;">DUE</td>
                                <td style="padding: 6px 8px; border: 1px solid #000; font-size: 16px; font-weight: 1000; color: #cc0000; text-align: right;">{{ number_format($remainingAmount, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Right Column - Invoice Summary -->
                <div style="flex: 1;">
                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
                        <tr>
                            <td style="padding: 6px 8px; border: 1px solid #000; font-size: 11px; font-weight: 600; color: #000;">Total Amount</td>
                            <td style="padding: 6px 8px; border: 1px solid #000; font-size: 11px; font-weight: 600; color: #000; text-align: right;">{{ number_format($subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 8px; border: 1px solid #000; font-size: 11px; font-weight: 600; color: #000;">Invoice Discount</td>
                            <td style="padding: 6px 8px; border: 1px solid #000; font-size: 11px; font-weight: 600; color: #000; text-align: right;">{{ number_format($discountAmount, 2) }}</td>
                        </tr>
                        <tr style="background-color: transparent; color: #000;">
                            <td style="padding: 8px 10px; border: 1px solid #000; font-size: 16px; font-weight: 1000;">Grand Total</td>
                            <td style="padding: 8px 10px; border: 1px solid #000; font-size: 16px; font-weight: 1000; text-align: right;">{{ number_format($grandTotal, 2) }}</td>
                        </tr>
                    </table>
                </div>


            </div>
            @endif

            <!-- Invoice Footer - Below Payment Tables -->
            @if($pageIndex == $totalPages - 1)
            <div style="margin-top: 4px; page-break-inside: avoid;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #000; padding: 3px 0;">
                    <!-- Left Side: Text Information -->
                    <div style="flex: 1; text-align: left;">
                        <div style="font-size: 8px; margin-bottom: 1px; color: #000;">Printed: {{ now()->format('d/m/Y h:i A') }}</div>
                        <div style="font-size: 7px; color: #000; line-height: 1;">This is a computer-generated invoice and does not require a signature.</div>
                        <div style="font-size: 7px; font-weight: 900; margin-top: 2px; letter-spacing: 0.5px; color: #000;">
                            POWERED BY <span style="font-size: 8px;">ePATNER | eLAB (SCAN THE QR CODE TO GET MORE INFO....)</span>
                        </div>
                    </div>

                    <!-- Right Side: QR Code -->
                    <div style="flex: 0 0 auto; text-align: center;">
                        <img src="{{ asset('images/ePATNER_QR.png') }}" alt="ePATNER QR" style="width: 35px; height: 31px; object-fit: contain; display: block;">
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Page Footer -->
    @if($totalPages > 1)
    <div class="page-footer">
        Page {{ $pageIndex + 1 }} of {{ $totalPages }}
    </div>
    @endif
    @endforeach
    @endif
</body>

</html>