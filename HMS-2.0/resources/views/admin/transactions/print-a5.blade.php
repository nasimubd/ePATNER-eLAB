<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ strtoupper($transaction->transaction_type) }} Voucher #{{ $transaction->id }} - A5 Print</title>
    <style>
        @page {
            margin: 5mm;
            size: A5;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', 'Helvetica', sans-serif;
            background-color: white;
            color: #000000;
            line-height: 1.1;
            font-size: 9px;
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

        .voucher-container {
            max-width: 148mm;
            margin: 0 auto;
            padding: 4px;
            background-color: white;
            position: relative;
        }

        /* Letterhead Space - Reserved for official header */
        .letterhead-space {
            height: 15mm;
            margin-bottom: 6px;
            border-bottom: 1px dashed #000;
            position: relative;
        }

        .letterhead-placeholder {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #000;
            font-size: 12px;
            text-align: center;
            font-style: italic;
        }

        /* Voucher Header */
        .voucher-header {
            text-align: center;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #000;
        }

        .voucher-title {
            font-size: 16px;
            font-weight: bold;
            color: #000;
            margin: 5px 0 5px 0;
        }

        .voucher-number {
            font-size: 12px;
            color: #000;
            font-weight: 600;
        }

        /* Two Column Layout */
        .voucher-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            gap: 15px;
        }

        .transaction-info,
        .voucher-info {
            flex: 1;
            background-color: transparent;
            padding: 8px;
            border: 1px solid #000;
            border-radius: 0;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #000;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            min-width: 60px;
            font-size: 8px;
        }

        .info-value {
            color: #000;
            font-weight: 500;
            font-size: 8px;
            text-align: right;
            flex: 1;
        }

        /* Transaction Lines Table */
        .lines-section {
            margin: 12px 0;
        }

        .lines-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            border: 1px solid #000;
        }

        .lines-table th {
            background-color: #000;
            color: white;
            padding: 3px 2px;
            text-align: left;
            font-weight: bold;
            font-size: 8px;
            border: 1px solid #000;
        }

        .lines-table td {
            padding: 2px 2px;
            border: 1px solid #000;
            vertical-align: top;
            font-size: 8px;
        }

        .lines-table tr:nth-child(even) {
            background-color: transparent;
        }

        .ledger-name {
            font-weight: bold;
            color: #000;
            margin-bottom: 1px;
            font-size: 9px;
        }

        .ledger-type {
            font-size: 7px;
            color: #666;
            line-height: 1.2;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Totals Section */
        .totals-section {
            margin-top: 8px;
            display: flex;
            justify-content: flex-end;
            position: relative;
        }

        .totals-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
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
            width: 200px;
            border-collapse: collapse;
            border: 1px solid #000;
        }

        .totals-table td {
            padding: 4px 8px;
            border: 1px solid #000;
            font-size: 9px;
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
            background-color: #000;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }

        .grand-total-row td {
            border: 1px solid #000;
            padding: 6px 8px;
        }

        /* Amount in Words */
        .amount-words {
            margin: 12px 0;
            padding: 8px;
            border: 1px solid #000;
            text-align: center;
        }

        .amount-words-label {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 4px;
        }

        .amount-words-text {
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }

        /* Balance Status */
        .balance-status {
            margin: 8px 0;
            padding: 6px;
            border: 1px solid #000;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
        }

        .balanced {
            background-color: #d4edda;
            color: #155724;
        }

        .unbalanced {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Signature Section */
        .signature-section {
            margin: 15px 0;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 45%;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
        }

        .signature-line {
            height: 25px;
            border-bottom: 1px solid #000;
            margin: 8px 0 4px 0;
        }

        /* Footer */
        .voucher-footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #000;
            text-align: center;
        }

        .footer-message {
            font-size: 10px;
            color: #000;
            margin-bottom: 5px;
        }

        .print-info {
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px dashed #000;
            font-size: 8px;
            color: #000;
        }

        .powered-by {
            margin-top: 5px;
            font-size: 9px;
            font-weight: 900;
            color: #000;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .powered-by .brand {
            color: #000;
            font-size: 10px;
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

        .voucher-page {
            min-height: 100vh;
            position: relative;
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

            .voucher-container {
                padding: 0;
            }

            .letterhead-placeholder {
                display: none;
            }

            .lines-table th {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .grand-total-row {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .voucher-page {
                min-height: 100vh;
            }
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .voucher-details {
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
        <a href="{{ route('admin.transactions.show', $transaction) }}" class="back-btn">
            <svg style="width: 16px; height: 16px; display: inline-block; margin-right: 5px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Voucher
        </a>
    </div>

    <div class="voucher-page">
        <div class="voucher-container">
            <!-- Letterhead Space -->
            <div class="letterhead-space">
                <div class="letterhead-placeholder">
                    [LETTERHEAD SPACE - Business Header]
                </div>
            </div>

            <!-- Voucher Header -->
            <div class="voucher-header">
                <div class="voucher-title">{{ strtoupper($transaction->transaction_type) }} VOUCHER</div>
                <div class="voucher-number">Voucher #{{ $transaction->id }}</div>
            </div>

            <!-- Voucher Details -->
            <div class="voucher-details">
                <div class="transaction-info">
                    <div class="section-title">Transaction Details</div>
                    <div class="info-row">
                        <span class="info-label">Voucher No:</span>
                        <span class="info-value">#{{ $transaction->id }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Type:</span>
                        <span class="info-value">{{ $transaction->transaction_type }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date:</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Amount:</span>
                        <span class="info-value">৳{{ number_format($transaction->amount, 2) }}</span>
                    </div>
                </div>
                <div class="voucher-info">
                    <div class="section-title">Business Info</div>
                    @if(isset($hospital) && $hospital)
                    <div class="info-row">
                        <span class="info-label">Business:</span>
                        <span class="info-value">{{ $hospital->name }}</span>
                    </div>
                    @if($hospital->address)
                    <div class="info-row">
                        <span class="info-label">Address:</span>
                        <span class="info-value">{{ $hospital->address }}</span>
                    </div>
                    @endif
                    @if($hospital->phone)
                    <div class="info-row">
                        <span class="info-label">Phone:</span>
                        <span class="info-value">{{ $hospital->phone }}</span>
                    </div>
                    @endif
                    @else
                    <div class="info-row">
                        <span class="info-label">Business:</span>
                        <span class="info-value">{{ config('app.name', 'Your Business') }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">Created:</span>
                        <span class="info-value">{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            @if($transaction->narration)
            <!-- Narration -->
            <div class="amount-words">
                <div class="amount-words-label">Description/Narration:</div>
                <div class="amount-words-text">{{ $transaction->narration }}</div>
            </div>
            @endif

            <!-- Transaction Lines -->
            <div class="lines-section">
                <table class="lines-table">
                    <thead>
                        <tr>
                            <th style="width: 50%;">Ledger Account</th>
                            <th style="width: 25%;" class="text-right">Debit (৳)</th>
                            <th style="width: 25%;" class="text-right">Credit (৳)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $totalDebit = 0;
                        $totalCredit = 0;
                        @endphp
                        @foreach($transaction->transactionLines as $line)
                        @php
                        $totalDebit += $line->debit_amount;
                        $totalCredit += $line->credit_amount;
                        @endphp
                        <tr>
                            <td>
                                <div class="ledger-name">{{ $line->ledger->name }}</div>
                                @if($line->ledger->ledger_type)
                                <div class="ledger-type">{{ $line->ledger->ledger_type }}</div>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($line->debit_amount > 0)
                                {{ number_format($line->debit_amount, 2) }}
                                @else
                                -
                                @endif
                            </td>
                            <td class="text-right">
                                @if($line->credit_amount > 0)
                                {{ number_format($line->credit_amount, 2) }}
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="totals-section">
                <div class="totals-wrapper">
                    <div class="totals-left-space">
                        <!-- Balance Status -->
                        @if(abs($totalDebit - $totalCredit) <= 0.01)
                            <div class="balance-status balanced">
                            ✓ VOUCHER BALANCED
                    </div>
                    @else
                    <div class="balance-status unbalanced">
                        ⚠ UNBALANCED - Difference: ৳{{ number_format(abs($totalDebit - $totalCredit), 2) }}
                    </div>
                    @endif
                </div>
                <table class="totals-table">
                    <tr>
                        <td class="total-label">Total Debit:</td>
                        <td class="total-value">৳{{ number_format($totalDebit, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="total-label">Total Credit:</td>
                        <td class="total-value">৳{{ number_format($totalCredit, 2) }}</td>
                    </tr>
                    <tr class="grand-total-row">
                        <td>Voucher Amount:</td>
                        <td>৳{{ number_format($transaction->amount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Amount in Words -->
        <div class="amount-words">
            <div class="amount-words-label">Amount in Words:</div>
            <div class="amount-words-text">
                @php
                // Simple number to words conversion for Bangladeshi Taka
                function numberToWords($number) {
                $ones = array(
                0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
                6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
                11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
                16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen'
                );

                $tens = array(
                2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
                6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
                );

                if ($number < 20) {
                    return $ones[$number];
                    } elseif ($number < 100) {
                    return $tens[intval($number / 10)] . ($number % 10 !=0 ? ' ' . $ones[$number % 10] : '' );
                    } elseif ($number < 1000) {
                    return $ones[intval($number / 100)] . ' Hundred' . ($number % 100 !=0 ? ' ' . numberToWords($number % 100) : '' );
                    } elseif ($number < 100000) {
                    return numberToWords(intval($number / 1000)) . ' Thousand' . ($number % 1000 !=0 ? ' ' . numberToWords($number % 1000) : '' );
                    } elseif ($number < 10000000) {
                    return numberToWords(intval($number / 100000)) . ' Lakh' . ($number % 100000 !=0 ? ' ' . numberToWords($number % 100000) : '' );
                    } else {
                    return numberToWords(intval($number / 10000000)) . ' Crore' . ($number % 10000000 !=0 ? ' ' . numberToWords($number % 10000000) : '' );
                    }
                    }

                    $amount=floor($transaction->amount);
                    $paisa = round(($transaction->amount - $amount) * 100);

                    $words = numberToWords($amount) . ' Taka';
                    if ($paisa > 0) {
                    $words .= ' and ' . numberToWords($paisa) . ' Paisa';
                    }
                    $words .= ' Only';
                    @endphp
                    {{ $words }}
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <!-- <div class="signature-box">
                <div class="signature-line"></div>
                <div>Prepared By</div>
            </div> -->
            <div class="signature-box">
                <div class="signature-line"></div>
                <div>Authorized By</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="voucher-footer">
            <div class="footer-message">
                This is a computer generated voucher and does not require signature.
            </div>
            <div class="print-info">
                <div>Printed on: {{ now()->format('d/m/Y H:i:s') }}</div>
                <div class="powered-by">
                    Powered by <span class="brand">ePATNER</span>
                </div>
            </div>
        </div>
    </div>
    </div>
</body>

</html>