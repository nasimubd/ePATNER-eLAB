<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Medical Invoice</title>
    <style>
        @page {
            margin: 0;
            size: 80mm auto;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', 'Helvetica', sans-serif;
            background-color: white;
            color: #000000;
            line-height: 1.3;
        }

        .print-controls {
            text-align: center;
            margin: 12px 0;
        }

        .print-controls button {
            padding: 8px 16px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 4px;
            font-weight: bold;
        }

        .print-controls a {
            padding: 8px 16px;
            background-color: #4b5563;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 4px;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
        }

        .invoice {
            width: 78mm;
            margin: 0 auto;
            padding: 4px;
            background-color: white;
            color: #000000;
            position: relative;
        }

        /* PAID Stamp Styles */
        .paid-stamp {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 120px;
            height: 120px;
            border: 4px solid #059669;
            border-radius: 50%;
            background-color: rgba(5, 150, 105, 0.1);
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
            font-size: 24px;
            font-weight: 900;
            color: #059669;
            text-align: center;
            letter-spacing: 2px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .hospital-name-curve {
            position: absolute;
            top: 15px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 8px;
            font-weight: bold;
            color: #059669;
            text-align: center;
            width: 90px;
            line-height: 1;
        }

        .invoice-number-curve {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 8px;
            font-weight: bold;
            color: #059669;
            text-align: center;
            width: 90px;
            line-height: 1;
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
            font-size: 10px;
            font-weight: bold;
            fill: #059669;
            font-family: 'Arial', 'Helvetica', sans-serif;
        }

        /* Divider styles */
        .divider {
            border-bottom: 2px solid #000000;
            margin: 0.4rem 0;
        }

        .divider-top {
            border-top: 2px solid #000000;
            margin-top: 0.4rem;
        }

        .light-divider {
            border-bottom: 1px solid #333333;
            margin: 0.2rem 0;
        }

        /* Spacing utilities */
        .mb-2 {
            margin-bottom: 0.4rem;
        }

        .mb-0\.5 {
            margin-bottom: 0.1rem;
        }

        .mb-0\.25 {
            margin-bottom: 0.08rem;
        }

        .mb-1 {
            margin-bottom: 0.2rem;
        }

        .mt-4 {
            margin-top: 0.8rem;
        }

        .mt-2 {
            margin-top: 0.4rem;
        }

        .mt-1 {
            margin-top: 0.2rem;
        }

        .py-1 {
            padding-top: 0.2rem;
            padding-bottom: 0.2rem;
        }

        .pb-2 {
            padding-bottom: 0.4rem;
        }

        .pb-1 {
            padding-bottom: 0.2rem;
        }

        .pb-0\.5 {
            padding-bottom: 0.1rem;
        }

        .pt-1 {
            padding-top: 0.2rem;
        }

        .pt-0\.5 {
            padding-top: 0.1rem;
        }

        .pt-2 {
            padding-top: 0.4rem;
        }

        .px-3 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        /* Text alignment */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        /* Text sizes */
        .text-sm {
            font-size: 0.85rem;
            font-weight: 500;
            color: #000000;
            line-height: 1.1;
            /* Reduced line height */
            margin-bottom: 0.05rem;
            /* Minimal bottom margin */
        }

        .text-xs {
            font-size: 0.75rem;
            font-weight: 500;
            color: #000000;
            line-height: 1.1;
            /* Reduced line height */
            margin-bottom: 0.05rem;
            /* Minimal bottom margin */
        }

        .text-xl {
            font-size: 1.1rem;
            font-weight: bold;
            color: #000000;
            line-height: 1.1;
            /* Reduced line height */
            margin-bottom: 0.05rem;
            /* Minimal bottom margin */
        }

        /* Compact header specific styles */
        .text-center h2,
        .text-center h3,
        .text-center p {
            margin-top: 0;
            margin-bottom: 0.05rem;
        }

        .text-center h3.mt-1 {
            margin-top: 0.15rem;
        }

        /* Font weights */
        .font-bold {
            font-weight: bold;
            color: #000000;
        }

        .font-semibold {
            font-weight: 600;
            color: #000000;
        }

        /* Text colors */
        .text-green-600 {
            color: #059669;
            font-weight: bold;
        }

        .text-blue-600 {
            color: #1d4ed8;
            font-weight: bold;
        }

        .text-red-600 {
            color: #dc2626;
            font-weight: bold;
        }

        .text-gray-dark {
            color: #374151;
            font-weight: 500;
        }

        /* Layout utilities */
        .w-full {
            width: 100%;
        }

        .flex {
            display: flex;
        }

        .justify-between {
            justify-content: space-between;
        }

        /* Compact info layout */
        .compact-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0.15rem 0;
            padding: 0.1rem 0;
        }

        .compact-info .label {
            font-weight: bold;
            font-size: 0.75rem;
            color: #000000;
            min-width: 35%;
        }

        .compact-info .value {
            font-size: 0.75rem;
            color: #000000;
            font-weight: 500;
            text-align: right;
            flex: 1;
        }

        /* Section headers */
        .section-header {
            padding: 0.3rem 0;
            margin-bottom: 0.3rem;
        }

        /* Test/Service items */
        .test-item {
            padding: 0.2rem 0;
            margin: 0.1rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .test-item:last-child {
            border-bottom: none;
        }

        .test-name {
            font-weight: bold;
            font-size: 0.8rem;
            color: #000000;
            margin-bottom: 0.1rem;
        }

        .test-details {
            font-size: 0.7rem;
            color: #374151;
            font-weight: 500;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.75rem;
            font-weight: 500;
            color: #000000;
            margin-top: 0.1rem;
        }

        /* Total section */
        .total-section {
            padding: 0.3rem 0;
            margin-top: 0.3rem;
            border-top: 2px solid #000000;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0.1rem 0;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .grand-total {
            font-weight: bold;
            font-size: 0.9rem;
            color: #000000;
            border-top: 1px solid #000000;
            padding-top: 0.2rem;
            margin-top: 0.2rem;
        }

        @media print {
            .print-controls {
                display: none;
            }

            .divider {
                border-bottom: 1.5px solid #000000;
                margin: 0.3rem 0;
            }

            .divider-top {
                border-top: 1.5px solid #000000;
                margin-top: 0.3rem;
            }

            body {
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .invoice {
                width: 100%;
                padding: 0;
            }

            .test-item {
                border-bottom: 1px solid #cccccc !important;
            }

            /* Ensure paid stamp prints correctly */
            .paid-stamp {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: rgba(5, 150, 105, 0.1) !important;
                border-color: #059669 !important;
            }

            .paid-text,
            .hospital-name-curve,
            .invoice-number-curve,
            .curved-text-svg text {
                color: #059669 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* Mobile-specific styles */
        @media screen and (max-width: 768px) {
            .print-controls {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: white;
                box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
                padding: 10px;
                z-index: 100;
            }

            .print-controls button,
            .print-controls a {
                padding: 12px 20px;
                font-size: 16px;
            }

            body {
                padding-bottom: 60px;
            }

            .invoice {
                width: 100%;
                max-width: 80mm;
            }

            /* Adjust paid stamp for mobile */
            .paid-stamp {
                width: 100px;
                height: 100px;
            }

            .paid-text {
                font-size: 20px;
            }

            .hospital-name-curve,
            .invoice-number-curve {
                font-size: 7px;
                width: 80px;
            }
        }
    </style>
</head>

<body>
    <div class="print-controls">
        <a href="{{ route('admin.medical.invoices.index') }}">Back to List</a>
        <button onclick="window.print()">Print Invoice</button>
    </div>

    @if($doubleCopyPrint ?? false)
    {{-- Generate two copies when double copy print is enabled --}}
    @php
    $copies = [
    ['type' => 'patient', 'label' => 'PATIENT COPY', 'bg_color' => 'rgba(59, 130, 246, 0.1)', 'border_color' => '#3b82f6'],
    ['type' => 'business', 'label' => 'BUSINESS COPY', 'bg_color' => 'rgba(16, 185, 129, 0.1)', 'border_color' => '#10b981']
    ];
    @endphp

    @foreach($copies as $copy)
    <div class="invoice {{ $copy !== reset($copies) ? 'page-break' : '' }}" style="margin-bottom: 20px;">

        {{-- PAID Stamp Overlay - Only show when invoice is fully paid --}}
        @php
        $isFullyPaid = $invoice->status === 'paid' || ($paidAmount ?? 0) >= ($grandTotal ?? 0);
        $hospitalName = isset($hospital) && $hospital ? $hospital->name : 'Medical Center';
        @endphp


        @if($isFullyPaid)
        <div class="paid-stamp">
            {{-- SVG for better curved text --}}
            <svg class="curved-text-svg" viewBox="0 0 120 120">
                {{-- Hospital name on top curve --}}
                <path id="top-curve" d="M 20,60 A 40,40 0 0,1 100,60" fill="none" stroke="none" />
                <text>
                    <textPath href="#top-curve" startOffset="50%" text-anchor="middle">
                        {{ Str::upper(Str::limit($hospitalName, 20)) }}
                    </textPath>
                </text>

                {{-- Invoice number on bottom curve --}}
                <path id="bottom-curve" d="M 100,60 A 40,40 0 0,1 20,60" fill="none" stroke="none" />
                <text>
                    <textPath href="#bottom-curve" startOffset="50%" text-anchor="middle">
                        {{ $invoice->invoice_number }}
                    </textPath>
                </text>
            </svg>

            {{-- PAID text in center --}}
            <div class="paid-stamp-inner">
                <div class="paid-text">PAID</div>
            </div>
        </div>
        @endif

        <div class="px-3">
            <!-- Letterhead Section (when enabled) -->
            @if(isset($letterhead) && $letterhead)
            <div style="margin-bottom: 0.3rem; padding-bottom: 0.2rem; border-bottom: 1px solid #000; padding-top: 0.3rem;">
                @php
                $logoPath = public_path('images/logo.png');
                $hasLogo = file_exists($logoPath);
                @endphp

                @if($hasLogo)
                <!-- Layout with Logo -->
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.2rem;">
                    <!-- Logo on Left -->
                    <div style="flex: 0 0 25px; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="max-width: 100%; max-height: 25px; object-fit: contain;">
                    </div>

                    <!-- Business Info on Right -->
                    <div style="flex: 1; text-align: center; padding-left: 8px;">
                        <!-- Business Names (Top) -->
                        @if($letterhead->business_name_bangla)
                        <div style="font-size: 10px; font-weight: bold; color: #000; margin-bottom: 1px;">
                            {{ $letterhead->business_name_bangla }}
                        </div>
                        @endif
                        @if($letterhead->business_name_english)
                        <div style="font-size: 9px; font-weight: bold; color: #000; margin-bottom: 2px;">
                            {{ $letterhead->business_name_english }}
                        </div>
                        @endif

                        <!-- Location -->
                        @if($letterhead->location)
                        <div style="font-size: 7px; color: #333; margin-bottom: 2px;">
                            📍 {{ $letterhead->location }}
                        </div>
                        @endif

                        <!-- Phone Numbers (One Line) -->
                        @if($letterhead->contacts && count($letterhead->contacts) > 0)
                        <div style="font-size: 7px; color: #333; margin-bottom: 1px;">
                            📞 {{ implode(' | ', array_filter($letterhead->contacts)) }}
                        </div>
                        @endif

                        <!-- Email -->
                        @if($letterhead->emails && count($letterhead->emails) > 0)
                        <div style="font-size: 7px; color: #333;">
                            ✉️ {{ implode(' | ', array_filter($letterhead->emails)) }}
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <!-- Full Width Layout without Logo -->
                <div style="display: flex; flex-direction: column; text-align: center; margin-bottom: 0.2rem; width: 100%;">
                    <!-- Business Names (Top) -->
                    @if($letterhead->business_name_bangla)
                    <div style="font-size: 10px; font-weight: bold; color: #000; margin-bottom: 1px;">
                        {{ $letterhead->business_name_bangla }}
                    </div>
                    @endif
                    @if($letterhead->business_name_english)
                    <div style="font-size: 9px; font-weight: bold; color: #000; margin-bottom: 2px;">
                        {{ $letterhead->business_name_english }}
                    </div>
                    @endif

                    <!-- Location -->
                    @if($letterhead->location)
                    <div style="font-size: 7px; color: #333; margin-bottom: 2px;">
                        📍 {{ $letterhead->location }}
                    </div>
                    @endif

                    <!-- Phone Numbers (One Line) -->
                    @if($letterhead->contacts && count($letterhead->contacts) > 0)
                    <div style="font-size: 7px; color: #333; margin-bottom: 1px;">
                        📞 {{ implode(' | ', array_filter($letterhead->contacts)) }}
                    </div>
                    @endif

                    <!-- Email -->
                    @if($letterhead->emails && count($letterhead->emails) > 0)
                    <div style="font-size: 7px; color: #333;">
                        ✉️ {{ implode(' | ', array_filter($letterhead->emails)) }}
                    </div>
                    @endif
                </div>
                @endif
            </div>
            <div class="divider"></div>
            @endif

            <!-- Hospital/Clinic Header -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="flex: 1; text-align: center;">
                    @if(!isset($letterhead) || !$letterhead)
                    @if(isset($hospital) && $hospital)
                    <h2 class="font-bold text-xl">{{ $hospital->name }}</h2>
                    <p class="text-sm">{{ $hospital->address ?? 'Hospital Address' }}</p>
                    <p class="text-xs">Phone: {{ $hospital->phone ?? 'N/A' }}</p>
                    @else
                    <h2 class="font-bold text-xl">Medical Center</h2>
                    <p class="text-sm">Hospital Address</p>
                    <p class="text-xs">Phone: N/A</p>
                    @endif
                    @endif

                    <!-- Invoice Header -->
                    <h3 class="font-bold text-lg mt-1">MEDICAL INVOICE</h3>
                    <p class="text-sm">Invoice #: <span class="font-bold">{{ $invoice->invoice_number }}</span></p>
                </div>
            </div>


            <div class="divider"></div>

            <!-- Patient Information -->
            <div class="section-header">
                <h4 class="font-bold text-sm mb-1">PATIENT INFORMATION</h4>
                <div class="light-divider"></div>
                <div class="compact-info">
                    <span class="label">Name:</span>
                    <span class="value">{{ $invoice->patient->full_name }}</span>
                </div>
                <div class="compact-info">
                    <span class="label">ID:</span>
                    <span class="value">{{ $invoice->patient->patient_id }}</span>
                </div>
                <div class="compact-info">
                    <span class="label">Phone:</span>
                    <span class="value">{{ $invoice->patient->phone ?? 'N/A' }}</span>
                </div>
                <div class="compact-info">
                    <span class="label">Age:</span>
                    <span class="value">{{ $invoice->patient->age ?? 'N/A' }} years</span>
                </div>
                <div class="compact-info">
                    <span class="label">Gender:</span>
                    <span class="value">{{ ucfirst($invoice->patient->gender ?? 'N/A') }}</span>
                </div>
            </div>

            <!-- Invoice Details -->
            <div class="mt-2">
                <div class="light-divider"></div>
                <div class="compact-info">
                    <span class="label">Invoice Date:</span>
                    <span class="value">{{ $invoice->invoice_date->format('d/m/Y') }}</span>
                </div>
                <div class="compact-info">
                    <span class="label">Time:</span>
                    <span class="value">{{ $invoice->invoice_date->format('h:i A') }}</span>
                </div>
            </div>

            <div class="divider mt-2"></div>

            <!-- Tests/Services -->
            <div class="mt-2">
                <h4 class="font-bold text-sm mb-1">TESTS/SERVICES</h4>
                <div class="light-divider"></div>
                @if($items && $items->count() > 0)
                @foreach($items as $item)
                <div class="test-item">
                    <div class="test-name">{{ $item->test->name ?? 'Medical Service' }}</div>
                    @if(isset($item->test->description) && $item->test->description)
                    <div class="test-details">{{ Str::limit($item->test->description, 50) }}</div>
                    @endif
                    @if(isset($item->test->test_code) && $item->test->test_code)
                    <div class="test-details">Code: {{ $item->test->test_code }}</div>
                    @endif
                    @if(isset($item->test->department) && $item->test->department)
                    <div class="test-details">Dept: {{ $item->test->department }}</div>
                    @endif
                    <div class="price-row">
                        <span>Qty: {{ $item->quantity ?? 1 }}</span>
                        <span>Rate: ৳{{ number_format($item->unit_price ?? 0, 2) }}</span>
                        <span class="font-bold">৳{{ number_format($item->total_price ?? 0, 2) }}</span>
                    </div>
                    @if(isset($item->line_discount) && $item->line_discount > 0)
                    <div class="price-row">
                        <span colspan="2">Line Discount:</span>
                        <span class="text-red-600">-৳{{ number_format($item->line_discount, 2) }}</span>
                    </div>
                    @endif
                </div>
                @endforeach
                @else
                <div class="test-item">
                    <div class="test-name">No services found</div>
                    <div class="price-row">
                        <span>Qty: 0</span>
                        <span>Rate: ৳0.00</span>
                        <span class="font-bold">৳0.00</span>
                    </div>
                </div>
                @endif
            </div>

            <div class="divider mt-2"></div>

            <!-- Totals -->
            <div class="total-section">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span class="font-semibold">৳{{ number_format($subtotal ?? $invoice->subtotal ?? 0, 2) }}</span>
                </div>

                @if(($discountAmount ?? $invoice->discount_amount ?? 0) > 0)
                <div class="total-row">
                    <span>Discount:</span>
                    <span class="text-red-600">-৳{{ number_format($discountAmount ?? $invoice->discount_amount ?? 0, 2) }}</span>
                </div>
                @endif

                @if(($taxAmount ?? $invoice->tax_amount ?? 0) > 0)
                <div class="total-row">
                    <span>Tax ({{ $invoice->tax_percentage ?? 0 }}%):</span>
                    <span>৳{{ number_format($taxAmount ?? $invoice->tax_amount ?? 0, 2) }}</span>
                </div>
                @endif

                <div class="total-row grand-total">
                    <span>Grand Total:</span>
                    <span>৳{{ number_format($grandTotal ?? $invoice->grand_total ?? 0, 2) }}</span>
                </div>

                @if(($paidAmount ?? $invoice->paid_amount ?? 0) > 0)
                <div class="total-row">
                    <span>Paid Amount:</span>
                    <span class="text-green-600">৳{{ number_format($paidAmount ?? $invoice->paid_amount ?? 0, 2) }}</span>
                </div>
                @endif

                @php
                $remainingAmount = ($grandTotal ?? $invoice->grand_total ?? 0) - ($paidAmount ?? $invoice->paid_amount ?? 0);
                @endphp

                @if($remainingAmount > 0)
                <div class="total-row">
                    <span>Remaining:</span>
                    <span class="text-red-600">৳{{ number_format($remainingAmount, 2) }}</span>
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="divider-top mt-4"></div>
            <div class="text-center mt-2">
                <p class="text-xs mb-0.5">Thank you for choosing our services!</p>
                <p class="text-xs mb-0.5">For any queries, please contact us.</p>
                @if($isFullyPaid)
                <p class="text-xs text-green-600 font-bold">✓ Payment Completed</p>
                @else
                <p class="text-xs text-red-600 font-bold">⚠ Payment Pending</p>
                @endif
            </div>

            <!-- Print Information -->
            <div class="text-center mt-2 pt-1" style="border-top: 1px dashed #666; padding-top: 0.3rem;">
                <p class="text-xs" style="margin-bottom: 0.2rem; line-height: 1.2;">Printed on: {{ now()->setTimezone(config('app.timezone', 'Asia/Dhaka'))->format('d/m/Y h:i A') }}</p>
                <p style="margin: 0.4rem 0 0 0; font-size: 0.8rem; font-weight: 900; color: #1d4ed8; letter-spacing: 1px; text-transform: uppercase; text-shadow: 1px 1px 2px rgba(0,0,0,0.1);">
                    POWERED BY <span style="color: #dc2626; font-weight: 900; font-size: 0.85rem; text-transform: none;">ePATNER</span>
                </p>
            </div>
        </div>
    </div>
    @endforeach
    @else
    <div class="invoice">
        {{-- PAID Stamp Overlay - Only show when invoice is fully paid --}}
        @php
        $isFullyPaid = $invoice->status === 'paid' || ($paidAmount ?? 0) >= ($grandTotal ?? 0);
        $hospitalName = isset($hospital) && $hospital ? $hospital->name : 'Medical Center';
        @endphp
        @if($isFullyPaid)
        <div class="paid-stamp">
            {{-- SVG for better curved text --}}
            <svg class="curved-text-svg" viewBox="0 0 120 120">
                {{-- Hospital name on top curve --}}
                <path id="top-curve" d="M 20,60 A 40,40 0 0,1 100,60" fill="none" stroke="none" />
                <text>
                    <textPath href="#top-curve" startOffset="50%" text-anchor="middle">
                        {{ Str::upper(Str::limit($hospitalName, 20)) }}
                    </textPath>
                </text>
                {{-- Invoice number on bottom curve --}}
                <path id="bottom-curve" d="M 100,60 A 40,40 0 0,1 20,60" fill="none" stroke="none" />
                <text>
                    <textPath href="#bottom-curve" startOffset="50%" text-anchor="middle">
                        {{ $invoice->invoice_number }}
                    </textPath>
                </text>
            </svg>
            {{-- PAID text in center --}}
            <div class="paid-stamp-inner">
                <div class="paid-text">PAID</div>
            </div>
        </div>
        @endif
        <div class="px-3">
            <!-- Hospital/Clinic Header -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="flex: 1; text-align: center;">
                    @if(isset($hospital) && $hospital)
                    <div class="text-xl font-bold mb-2" style="color: #000000;">{{ $hospital->name }}</div>
                    @if($hospital->address)
                    <div class="text-xs mb-0.5" style="color: #000000;">{{ $hospital->address }}</div>
                    @endif
                    @if($hospital->phone)
                    <div class="text-xs mb-0.5" style="color: #000000;">Phone: {{ $hospital->phone }}</div>
                    @endif
                    @if($hospital->email)
                    <div class="text-xs mb-0.5" style="color: #000000;">Email: {{ $hospital->email }}</div>
                    @endif
                    @endif
                </div>
                <div style="text-align: right; font-size: 0.8rem;">
                    <div class="text-xs mb-0.5"><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</div>
                    <div class="text-xs mb-0.5"><strong>Date:</strong> {{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') : 'N/A' }}</div>
                    <div class="text-xs mb-0.5"><strong>Time:</strong> {{ $invoice->created_at ? $invoice->created_at->format('h:i A') : 'N/A' }}</div>
                </div>
            </div>
            <div class="divider"></div>
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
                    <span class="label">Age:</span>
                    <span class="value">{{ $invoice->patient ? ($invoice->patient->date_of_birth ? \Carbon\Carbon::parse($invoice->patient->date_of_birth)->age : 'N/A') : 'N/A' }}
                </div>
                <div class="compact-info">
                    <span class="label">Gender:</span>
                    <span class="value">{{ $invoice->patient ? ucfirst($invoice->patient->gender ?? 'N/A') : 'N/A' }}</span>
                </div>
                <div class="compact-info">
                    <span class="label">Phone:</span>
                    <span class="value">{{ $invoice->patient ? $invoice->patient->phone : 'N/A' }}</span>
                </div>
                @if($invoice->patient && $invoice->patient->address)
                <div class="compact-info">
                    <span class="label">Address:</span>
                    <span class="value">{{ $invoice->patient->address }}</span>
                </div>
                @endif
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
            <!-- Services/Tests -->
            <div class="mb-2">
                <div class="text-sm font-bold mb-1" style="color: #000000;">Services & Tests:</div>
                @forelse($items ?? [] as $item)
                <div class="test-item">
                    <div class="test-name">{{ $item->service_name ?? $item->test->name ?? 'N/A' }}</div>
                    @if(isset($item->test) && $item->test)
                    <div class="test-details">
                        @if($item->test->description)
                        {{ $item->test->description }}
                        @endif
                        @if($item->test->department)
                        | Department: {{ $item->test->department }}
                        @endif
                        @if($item->test->sample_type)
                        | Sample: {{ $item->test->sample_type }}
                        @endif
                    </div>
                    @endif
                    <div class="price-row">
                        <span>Qty: {{ $item->quantity ?? 1 }} × ৳{{ number_format($item->unit_price ?? 0, 2) }}</span>
                        <span>Total: ৳{{ number_format($item->total_price ?? 0, 2) }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-2 text-xs" style="color: #6b7280;">No services or tests found</div>
                @endforelse
            </div>
            <div class="divider"></div>
            <!-- Totals Section -->
            <div class="total-section">
                <div class="total-row">
                    <span class="font-bold">Subtotal:</span>
                    <span class="font-bold">৳{{ number_format($subtotal ?? 0, 2) }}</span>
                </div>
                @if($discountAmount ?? 0 > 0)
                <div class="total-row">
                    <span class="font-bold">Discount:</span>
                    <span class="font-bold">৳{{ number_format($discountAmount, 2) }}</span>
                </div>
                @endif
                @if($taxAmount ?? 0 > 0)
                <div class="total-row">
                    <span class="font-bold">Tax:</span>
                    <span class="font-bold">৳{{ number_format($taxAmount, 2) }}</span>
                </div>
                @endif
                <div class="total-row grand-total">
                    <span class="font-bold">Grand Total:</span>
                    <span class="font-bold">৳{{ number_format($grandTotal ?? 0, 2) }}</span>
                </div>
                @if($paidAmount ?? 0 > 0)
                <div class="total-row">
                    <span class="font-bold text-green-600">Paid Amount:</span>
                    <span class="font-bold text-green-600">৳{{ number_format($paidAmount, 2) }}</span>
                </div>
                @endif
                @if($remainingAmount ?? 0 > 0)
                <div class="total-row">
                    <span class="font-bold text-red-600">Remaining Amount:</span>
                    <span class="font-bold text-red-600">৳{{ number_format($remainingAmount, 2) }}</span>
                </div>
                @endif
            </div>
            <!-- Footer Message -->
            <div class="text-center mt-2">
                <p class="text-xs mb-0.5">Thank you for choosing our services!</p>
                <p class="text-xs mb-0.5">For any queries, please contact us.</p>
                @if($isFullyPaid)
                <p class="text-xs text-green-600 font-bold">✓ Payment Completed</p>
                @else
                <p class="text-xs text-red-600 font-bold">⚠ Payment Pending</p>
                @endif
            </div>
            <!-- Print Information -->
            <div class="text-center mt-2 pt-1" style="border-top: 1px dashed #666; padding-top: 0.3rem;">
                <p class="text-xs" style="margin-bottom: 0.2rem; line-height: 1.2;">Printed on: {{ now()->setTimezone(config('app.timezone', 'Asia/Dhaka'))->format('d/m/Y h:i A') }}</p>
                <p style="margin: 0.4rem 0 0 0; font-size: 0.8rem; font-weight: 900; color: #1d4ed8; letter-spacing: 1px; text-transform: uppercase; text-shadow: 1px 1px 2px rgba(0,0,0,0.1);">
                    POWERED BY <span style="color: #dc2626; font-weight: 900; font-size: 0.85rem; text-transform: none;">ePATNER</span>
                </p>
            </div>
        </div>
    </div>
    @endif

    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }

        // Auto-close after printing with multiple fallback methods
        let printCompleted = false;
        let printDialogOpened = false;

        // Method 1: afterprint event (works in most modern browsers)
        window.addEventListener('afterprint', function() {
            console.log('Print completed - closing window');
            printCompleted = true;
            setTimeout(function() {
                window.close();
            }, 1000);
        });

        // Method 2: beforeprint event to track dialog opening
        window.addEventListener('beforeprint', function() {
            printDialogOpened = true;
            document.body.style.overflow = 'hidden';
            console.log('Print dialog opened');
        });

        // Method 3: Media query listener for print state changes
        if (window.matchMedia) {
            const mediaQueryList = window.matchMedia('print');
            mediaQueryList.addListener(function(mql) {
                if (!mql.matches && printDialogOpened) {
                    // Print mode ended
                    console.log('Print mode ended - closing window');
                    setTimeout(function() {
                        if (!printCompleted) {
                            window.close();
                        }
                    }, 1500);
                }
            });
        }

        // Method 4: Focus event fallback (when user returns to window after print)
        window.addEventListener('focus', function() {
            if (printDialogOpened && !printCompleted) {
                console.log('Window focused after print - closing window');
                setTimeout(function() {
                    window.close();
                }, 2000);
            }
        });

        // Method 5: Visibility change fallback
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && printDialogOpened && !printCompleted) {
                console.log('Page became visible after print - closing window');
                setTimeout(function() {
                    window.close();
                }, 1500);
            }
        });

        // Method 6: Auto-close after reasonable time if print dialog was opened
        setTimeout(function() {
            if (printDialogOpened && !printCompleted) {
                console.log('Auto-closing after timeout');
                window.close();
            }
        }, 15000); // 15 seconds timeout

        // Method 7: Absolute fallback - close after 30 seconds regardless
        setTimeout(function() {
            console.log('Absolute timeout - closing window');
            window.close();
        }, 30000);

        // Auto-print functionality with URL parameter
        function autoPrint() {
            if (window.location.search.includes('auto_print=1')) {
                setTimeout(() => {
                    window.print();
                }, 1000);
            }
        }

        // Print button functionality
        function printInvoice() {
            window.print();
        }

        // Mobile-friendly print
        if (window.innerWidth <= 768) {
            document.addEventListener('DOMContentLoaded', function() {
                const printButton = document.querySelector('button[onclick="window.print()"]');
                if (printButton) {
                    printButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        setTimeout(() => {
                            window.print();
                        }, 100);
                    });
                }
            });
        }

        // Initialize auto-print
        document.addEventListener('DOMContentLoaded', autoPrint);

        // Restore body overflow after print
        window.addEventListener('afterprint', function() {
            document.body.style.overflow = 'auto';
        });

        // Print quality optimization
        window.addEventListener('beforeprint', function() {
            const images = document.querySelectorAll('img');
            let loadedImages = 0;

            if (images.length === 0) return;

            images.forEach(img => {
                if (img.complete) {
                    loadedImages++;
                } else {
                    img.addEventListener('load', function() {
                        loadedImages++;
                    });
                }
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+P for print
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }

            // Escape to close window immediately
            if (e.key === 'Escape') {
                window.close();
            }
        });

        // Touch-friendly interactions for mobile
        if ('ontouchstart' in window) {
            document.addEventListener('touchstart', function() {
                document.body.classList.add('touch-device');
            });
        }

        // Enhanced mobile print support
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(function(error) {
                console.log('Service Worker registration failed:', error);
            });
        }

        // Handle browser back button
        window.addEventListener('popstate', function() {
            window.close();
        });

        // Handle page unload
        window.addEventListener('beforeunload', function() {
            if (printDialogOpened) {
                // Allow natural closing
                return;
            }
        });
    </script>

</body>

</html>