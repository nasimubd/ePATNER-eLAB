<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.3in 0.4in 0.3in 0.4in;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 13px;
            line-height: 1.3;
            color: #000;
        }

        .page {
            page-break-after: always;
            padding: 0;
            margin: 0;
            min-height: auto;
        }

        .page:last-child {
            page-break-after: avoid;
            min-height: auto;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
        }

        .hospital-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .hospital-details {
            font-size: 10px;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .date-range {
            font-size: 12px;
            font-weight: bold;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: left;
            font-size: 12px;
        }

        .report-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 11px;
            text-transform: uppercase;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-row {
            background-color: #e0e0e0;
            font-weight: bold;
        }

        .totals-row td {
            border-top: 2px solid #000;
            font-size: 12px;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            border-top: 1px solid #000;
            padding-top: 6px;
            margin-top: 15px;
        }

        /* Single date detailed view row styling */
        .invoice-row {
            background-color: #f9f9f9;
        }

        .commission-row {
            background-color: #fff3cd;
        }

        .expense-row {
            background-color: #f8d7da;
        }

        .text-left {
            text-align: left;
        }



        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .page {
                page-break-after: always;
                height: auto !important;
                min-height: auto !important;
            }

            .page:last-child {
                page-break-after: avoid !important;
                height: auto !important;
                min-height: auto !important;
            }

            .footer {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    @php
    $breakdown = $reportData['breakdown'];
    $totals = $reportData['totals'];
    $rowsPerPage = 40;
    $totalPages = ceil(count($breakdown) / $rowsPerPage);
    $chunks = array_chunk($breakdown, $rowsPerPage);
    @endphp

    @foreach($chunks as $pageIndex => $pageData)
    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="hospital-name">{{ $business->hospital_name ?? 'TEST HOSPITAL' }}</div>
            @if($business)
            <div class="hospital-details">
                {{ $business->address ?? '' }}
                @if($business->contact_number) | Phone: {{ $business->contact_number }} @endif
                @if($business->email) | Email: {{ $business->email }} @endif
            </div>
            @endif
            <div class="report-title">TOP SHEET REPORT</div>
            <div class="date-range">Period: {{ $reportData['date_range']['start'] }} to {{ $reportData['date_range']['end'] }}</div>
        </div>

        <!-- Report Table -->
        <table class="report-table">
            <thead>
                <tr>
                    @if(isset($reportData['is_single_date']) && $reportData['is_single_date'])
                    <th style="width: 25%;">Patient/Care Name</th>
                    <th style="width: 15%;">Sales</th>
                    <th style="width: 15%;">Collected</th>
                    <th style="width: 15%;">Expenses</th>
                    <th style="width: 15%;">Commission</th>
                    <th style="width: 15%;">Discount</th>
                    @else
                    <th style="width: 18%;">Date</th>
                    <th style="width: 22%;">Sales</th>
                    <th style="width: 22%;">Collected</th>
                    <th style="width: 20%;">Expenses</th>
                    <th style="width: 18%;">Commission</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($pageData as $row)
                <tr class="{{ isset($row['type']) ? $row['type'] . '-row' : '' }}">
                    @if(isset($reportData['is_single_date']) && $reportData['is_single_date'])
                    <td class="text-left">{{ $row['name'] ?? 'N/A' }}</td>
                    <td class="text-right">{{ $row['sales'] > 0 ? '৳' . number_format($row['sales'], 2) : '' }}</td>
                    <td class="text-right">{{ $row['collected_sales'] > 0 ? '৳' . number_format($row['collected_sales'], 2) : '' }}</td>
                    <td class="text-right">{{ $row['expenses'] > 0 ? '৳' . number_format($row['expenses'], 2) : '' }}</td>
                    <td class="text-right">{{ $row['commission'] > 0 ? '৳' . number_format($row['commission'], 2) : '' }}</td>
                    <td class="text-right">{{ $row['discount'] > 0 ? '৳' . number_format($row['discount'], 2) : '' }}</td>
                    @else
                    <td class="text-center">{{ $row['date_formatted'] }}</td>
                    <td class="text-right">৳{{ number_format($row['sales'], 2) }}</td>
                    <td class="text-right">৳{{ number_format($row['collected_sales'], 2) }}</td>
                    <td class="text-right">৳{{ number_format($row['expenses'], 2) }}</td>
                    <td class="text-right">৳{{ number_format($row['commission'], 2) }}</td>
                    @endif
                </tr>
                @endforeach

                @if($pageIndex === count($chunks) - 1)
                <!-- Show totals only on the last page -->
                <tr class="totals-row">
                    @if(isset($reportData['is_single_date']) && $reportData['is_single_date'])
                    <td class="text-center"><strong>TOTAL</strong></td>
                    <td class="text-right"><strong>৳{{ number_format($totals['sales'], 2) }}</strong></td>
                    <td class="text-right"><strong>৳{{ number_format($totals['collected_sales'], 2) }}</strong></td>
                    <td class="text-right"><strong>৳{{ number_format($totals['expenses'], 2) }}</strong></td>
                    <td class="text-right"><strong>৳{{ number_format($totals['commission'], 2) }}</strong></td>
                    <td class="text-right"><strong>৳{{ number_format($totals['discount'], 2) }}</strong></td>
                    @else
                    <td class="text-center"><strong>TOTAL</strong></td>
                    <td class="text-right"><strong>৳{{ number_format($totals['sales'], 2) }}</strong></td>
                    <td class="text-right"><strong>৳{{ number_format($totals['collected_sales'], 2) }}</strong></td>
                    <td class="text-right"><strong>৳{{ number_format($totals['expenses'], 2) }}</strong></td>
                    <td class="text-right"><strong>৳{{ number_format($totals['commission'], 2) }}</strong></td>
                    @endif
                </tr>
                @endif
            </tbody>
        </table>



        <!-- Footer -->
        <div class="footer">
            {{ $business->hospital_name ?? 'TEST HOSPITAL' }} - Top Sheet Report |
            Generated on {{ now()->format('M d, Y h:i A') }} |
            Page {{ $pageIndex + 1 }} of {{ $totalPages }}
        </div>
    </div>
    @endforeach

    <script>
        window.onload = function() {
            document.title = '';
            setTimeout(function() {
                window.print();
            }, 100);
        };
    </script>
</body>

</html>