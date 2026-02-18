<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Sheet Report - {{ $business->hospital_name ?? 'TEST HOSPITAL' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.4in 0.5in 0.4in 0.5in;
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
            margin: 0;
            padding: 0;
        }

        .page {
            page-break-after: always;
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 15px;
            box-sizing: border-box;
        }

        .page:last-child {
            page-break-after: avoid;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }

        .hospital-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 2px;
        }

        .hospital-details {
            font-size: 9px;
            color: #666;
            margin-bottom: 6px;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #34495e;
            margin-bottom: 2px;
        }

        .date-range {
            font-size: 11px;
            color: #666;
            font-weight: bold;
        }

        .content {
            flex: 1;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #333;
            padding: 8px 6px;
            text-align: left;
            font-size: 11px;
        }

        .report-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
            text-transform: uppercase;
            border: 2px solid #333;
        }

        .report-table .text-right {
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
            border-top: 3px solid #333;
            font-size: 11px;
            font-weight: bold;
        }

        .positive {
            color: #28a745;
        }

        .negative {
            color: #dc3545;
        }

        .footer {
            margin-top: auto;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            position: relative;
        }

        .summary-section {
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .summary-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 2px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-bottom: 10px;
        }

        .summary-card {
            border: 1px solid #333;
            padding: 6px;
            text-align: center;
            background-color: #f8f9fa;
        }

        .summary-card .label {
            font-size: 8px;
            color: #666;
            margin-bottom: 2px;
        }

        .summary-card .value {
            font-size: 10px;
            font-weight: bold;
            color: #2c3e50;
        }

        @media print {
            @page {
                margin: 0.4in 0.5in 0.4in 0.5in !important;
                size: A4 portrait !important;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0 !important;
                padding: 0 !important;
            }

            .page {
                page-break-after: always;
                height: auto !important;
                min-height: 0 !important;
                margin: 0 !important;
                padding: 10px !important;
                display: block !important;
            }

            .page:last-child {
                page-break-after: avoid;
            }

            /* Remove empty spaces */
            .content {
                flex: none !important;
            }

            .footer {
                margin-top: 10px !important;
            }
        }
    </style>
</head>

<body>
    @php
    $breakdown = $reportData['breakdown'];
    $totals = $reportData['totals'];
    $rowsPerPage = 35;
    $totalPages = ceil(count($breakdown) / $rowsPerPage);
    $chunks = array_chunk($breakdown, $rowsPerPage);
    @endphp

    @foreach($chunks as $pageIndex => $pageData)
    <div class="page">
        <!-- Header (repeated on each page) -->
        <div class="header">
            <div class="hospital-name">{{ $business->hospital_name ?? 'TEST HOSPITAL' }}</div>
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

        <div class="content">
            <!-- Report Table -->
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 18%;">Date</th>
                        <th style="width: 22%;">Sales</th>
                        <th style="width: 22%;">Collected</th>
                        <th style="width: 20%;">Expenses</th>
                        <th style="width: 18%;">Commission</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pageData as $row)
                    <tr>
                        <td class="text-center">{{ $row['date_formatted'] }}</td>
                        <td class="text-right">৳{{ number_format($row['sales'], 2) }}</td>
                        <td class="text-right">৳{{ number_format($row['collected_sales'], 2) }}</td>
                        <td class="text-right">৳{{ number_format($row['expenses'], 2) }}</td>
                        <td class="text-right">৳{{ number_format($row['commission'], 2) }}</td>
                    </tr>
                    @endforeach

                    @if($pageIndex === count($chunks) - 1)
                    <!-- Show totals only on the last page -->
                    <tr class="totals-row">
                        <td class="text-center"><strong>TOTAL</strong></td>
                        <td class="text-right"><strong>৳{{ number_format($totals['sales'], 2) }}</strong></td>
                        <td class="text-right"><strong>৳{{ number_format($totals['collected_sales'], 2) }}</strong></td>
                        <td class="text-right"><strong>৳{{ number_format($totals['expenses'], 2) }}</strong></td>
                        <td class="text-right"><strong>৳{{ number_format($totals['commission'], 2) }}</strong></td>
                    </tr>
                    @endif
                </tbody>
            </table>

            @if($pageIndex === count($chunks) - 1)
            <!-- Summary Section (only on last page) -->
            <div class="summary-section">
                <div class="summary-title">Financial Summary</div>
                <div class="summary-grid">
                    <div class="summary-card">
                        <div class="label">Total Sales</div>
                        <div class="value">৳{{ number_format($totals['sales'], 0) }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="label">Total Collected</div>
                        <div class="value positive">৳{{ number_format($totals['collected_sales'], 0) }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="label">Collection Rate</div>
                        <div class="value">
                            {{ $totals['sales'] > 0 ? number_format(($totals['collected_sales'] / $totals['sales']) * 100, 1) : 0 }}%
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="label">Total Expenses</div>
                        <div class="value negative">৳{{ number_format($totals['expenses'], 0) }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="label">Total Commission</div>
                        <div class="value">৳{{ number_format($totals['commission'], 0) }}</div>
                    </div>
                </div>

                <!-- Additional Metrics -->
                <div style="margin-top: 8px; font-size: 9px;">
                    <strong>Report Generated:</strong> {{ now()->format('M d, Y h:i A') }} |
                    <strong>Total Days:</strong> {{ count($breakdown) }} |
                    <strong>Average Daily Sales:</strong> ৳{{ count($breakdown) > 0 ? number_format($totals['sales'] / count($breakdown), 0) : 0 }}
                </div>
            </div>
            @endif
        </div>

        <!-- Footer (repeated on each page) -->
        <div class="footer">
            <div>
                {{ $business->hospital_name ?? 'TEST HOSPITAL' }} - Top Sheet Report |
                Generated on {{ now()->format('M d, Y h:i A') }} |
                Page {{ $pageIndex + 1 }} of {{ $totalPages }}
            </div>
        </div>
    </div>
    @endforeach

    <script>
        // Auto-print when page loads with custom settings
        window.onload = function() {
            // Try to remove browser headers/footers
            if (window.chrome) {
                // For Chrome, try to set print options
                setTimeout(function() {
                    window.print();
                }, 500);
            } else {
                window.print();
            }
        };

        // Additional script to handle print settings
        window.addEventListener('beforeprint', function() {
            document.title = ''; // Remove title from header
        });

        window.addEventListener('afterprint', function() {
            document.title = 'Top Sheet Report - {{ $business->hospital_name ?? "TEST HOSPITAL" }}';
        });
    </script>
</body>

</html>