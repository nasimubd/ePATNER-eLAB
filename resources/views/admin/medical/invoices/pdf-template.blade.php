<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .hospital-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .hospital-info {
            font-size: 11px;
            color: #666;
        }

        .invoice-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .invoice-left,
        .invoice-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .invoice-right {
            text-align: right;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            color: #333;
        }

        .patient-info,
        .invoice-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .items-table .text-right {
            text-align: right;
        }

        .totals {
            float: right;
            width: 300px;
            margin-top: 20px;
        }

        .totals table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals td {
            padding: 5px 10px;
            border-bottom: 1px solid #eee;
        }

        .totals .total-row {
            font-weight: bold;
            font-size: 14px;
            background-color: #f8f9fa;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="hospital-name">{{ $hospital->name }}</div>
        <div class="hospital-info">
            {{ $hospital->address }}<br>
            Phone: {{ $hospital->phone }}
            @if($hospital->email)
            | Email: {{ $hospital->email }}
            @endif
        </div>
    </div>

    <!-- Invoice Information -->
    <div class="invoice-info">
        <div class="invoice-left">
            <div class="patient-info">
                <div class="section-title">Patient Information</div>
                <strong>{{ $invoice->patient->full_name }}</strong><br>
                Patient ID: {{ $invoice->patient->patient_id }}<br>
                @if($invoice->patient->phone)
                Phone: {{ $invoice->patient->phone }}<br>
                @endif
                @if($invoice->patient->email)
                Email: {{ $invoice->patient->email }}<br>
                @endif
                @if($invoice->patient->address)
                Address: {{ $invoice->patient->address }}
                @endif
            </div>
        </div>
        <div class="invoice-right">
            <div class="invoice-details">
                <div class="section-title">Invoice Details</div>
                <strong>Invoice #: {{ $invoice->invoice_number }}</strong><br>
                Date: {{ $invoice->invoice_date->format('M d, Y') }}<br>
                Time: {{ $invoice->created_at->format('h:i A') }}<br>
                Status: <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span><br>
                Payment Method: {{ ucfirst(str_replace('_', ' ', $invoice->payment_method)) }}
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 40%">Service/Test</th>
                <th style="width: 15%">Department</th>
                <th style="width: 10%" class="text-right">Qty</th>
                <th style="width: 15%" class="text-right">Unit Price</th>
                <th style="width: 15%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item->test->name }}</strong>
                    @if($item->test->test_code)
                    <br><small>Code: {{ $item->test->test_code }}</small>
                    @endif
                    @if($item->test->description && $item->test->description !== $item->test->name)
                    <br><small>{{ $item->test->description }}</small>
                    @endif
                </td>
                <td>{{ $item->test->department ?? 'General' }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">৳{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">৳{{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="clearfix">
        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">৳{{ number_format($subtotal, 2) }}</td>
                </tr>
                @if($discountAmount > 0)
                <tr>
                    <td>Discount:</td>
                    <td class="text-right">-৳{{ number_format($discountAmount, 2) }}</td>
                </tr>
                @endif
                @if($taxAmount > 0)
                <tr>
                    <td>Tax:</td>
                    <td class="text-right">৳{{ number_format($taxAmount, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>Grand Total:</td>
                    <td class="text-right">৳{{ number_format($grandTotal, 2) }}</td>
                </tr>
                @if($paidAmount > 0)
                <tr>
                    <td>Paid Amount:</td>
                    <td class="text-right">৳{{ number_format($paidAmount, 2) }}</td>
                </tr>
                @endif
                @if($remainingAmount > 0)
                <tr style="color: #dc2626;">
                    <td>Remaining:</td>
                    <td class="text-right">৳{{ number_format($remainingAmount, 2) }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- Doctor Information -->
    @if($doctor)
    <div style="margin-top: 80px;">
        <div class="section-title">Attending Doctor</div>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <strong>{{ $doctor->name }}</strong><br>
            @if(isset($doctor->specialization) && $doctor->specialization)
            Specialization: {{ $doctor->specialization }}<br>
            @endif
            @if(isset($doctor->license_number) && $doctor->license_number)
            License: {{ $doctor->license_number }}
            @endif
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for choosing {{ $hospital->name }}</p>
        <p>This is a computer-generated invoice. Generated on {{ now()->format('M d, Y h:i A') }}</p>
        @if($invoice->notes)
        <p><strong>Notes:</strong> {{ $invoice->notes }}</p>
        @endif
    </div>
</body>

</html>