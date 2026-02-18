<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Sheet Report - {{ $business->hospital_name ?? 'Medical Center' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.5in;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .hospital-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .hospital-details {
            font-size: 11px;
            color: #666;
            margin-bottom: 10px;
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #34495e;
            margin-bottom: 5px;
        }

        .date-range {
            font-size: 14px;
            color: #666;
            font-weight: bold;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .report-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }

        .report-table .text-end {
            text-align: right;
        }

        .report-table .text-center {
            text-align: center;
        }

        .totals-row {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .totals-row td {
            border-top: 2px solid #333;
        }

        .positive {
            color: #28a745;
        }

        .negative {
            color: #dc3545;
        }

        .summary-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .summary-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .summary-card {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            background-color: #f8f9fa;
        }

        .summary-card .label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-card .value {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
        }

        .footer {
            position: fixed;
            bottom: 0.5in;
            left: 0.5in;
            right: 0.5in;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .page-break {
            page-break-before: always;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="hospital-name">{{ $business->hospital_name ?? 'Medical Center' }}</div>
        @if($business)
        <div class="hospital-details">
            {{ $business->address ?? '' }}
            @if($business->contact_number)
            | Phone: {{ $business->contact_number }}
            @endif
            @if($business->email)
            | Email: {{ $business->email }}
            @endif
        </div>
        @endif
        <div class="report-title">TOP SHEET REPORT</div>
        <div class="date-range">
            Period: {{ $reportData['date_range']['start'] }} to {{ $reportData['date_range']['end'] }}
        </div>
    </div>

    <!-- Report Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 15%;">Date</th>
                <th style="width: 17%;">Sales</th>
                <th style="width: 17%;">Collected</th>
                <th style="width: 17%;">Expenses</th>
                <th style="width: 17%;">Commission</th>
                <th style="width: 17%;">Net Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['breakdown'] as $row)
            <tr>
                <td class="text-center">{{ $row['date_formatted'] }}</td>
                <td class="text-end">₹{{ number_format($row['sales'], 2) }}</td>
                <td class="text-end">₹{{ number_format($row['collected_sales'], 2) }}</td>
                <td class="text-end">₹{{ number_format($row['expenses'], 2) }}</td>
                <td class="text-end">₹{{ number_format($row['commission'], 2) }}</td>
                <td class="text-end {{ $row['net_profit'] >= 0 ? 'positive' : 'negative' }}">
                    ₹{{ number_format($row['net_profit'], 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals-row">
                <td class="text-center"><strong>TOTAL</strong></td>
                <td class="text-end"><strong>₹{{ number_format($reportData['totals']['sales'], 2) }}</strong></td>
                <td class="text-end"><strong>₹{{ number_format($reportData['totals']['collected_sales'], 2) }}</strong></td>
                <td class="text-end"><strong>₹{{ number_format($reportData['totals']['expenses'], 2) }}</strong></td>
                <td class="text-end"><strong>₹{{ number_format($reportData['totals']['commission'], 2) }}</strong></td>
                <td class="text-end {{ $reportData['totals']['net_profit'] >= 0 ? 'positive' : 'negative' }}">
                    <strong>₹{{ number_format($reportData['totals']['net_profit'], 2) }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-title">Financial Summary</div>
        <div class="summary-grid">
            <div class="summary-card">
                <div class="label">Total Sales</div>
                <div class="value">₹{{ number_format($reportData['totals']['sales'], 0) }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Total Collected</div>
                <div class="value positive">₹{{ number_format($reportData['totals']['collected_sales'], 0) }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Collection Rate</div>
                <div class="value">
                    {{ $reportData['totals']['sales'] > 0 ? number_format(($reportData['totals']['collected_sales'] / $reportData['totals']['sales']) * 100, 1) : 0 }}%
                </div>
            </div>
            <div class="summary-card">
                <div class="label">Total Expenses</div>
                <div class="value negative">₹{{ number_format($reportData['totals']['expenses'], 0) }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Total Commission</div>
                <div class="value">₹{{ number_format($reportData['totals']['commission'], 0) }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Net Profit</div>
                <div class="value {{ $reportData['totals']['net_profit'] >= 0 ? 'positive' : 'negative' }}">
                    ₹{{ number_format($reportData['totals']['net_profit'], 0) }}
                </div>
            </div>
        </div>

        <!-- Additional Metrics -->
        <div style="margin-top: 20px; font-size: 11px;">
            <strong>Report Generated:</strong> {{ now()->format('M d, Y h:i A') }} |
            <strong>Total Days:</strong> {{ count($reportData['breakdown']) }} |
            <strong>Average Daily Sales:</strong> ₹{{ count($reportData['breakdown']) > 0 ? number_format($reportData['totals']['sales'] / count($reportData['breakdown']), 0) : 0 }}
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>
            {{ $business->hospital_name ?? 'Medical Center' }} - Top Sheet Report |
            Generated on {{ now()->format('M d, Y h:i A') }} |
            Page 1 of 1
        </div>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>