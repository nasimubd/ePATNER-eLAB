<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ strtoupper($transaction->transaction_type) }} Voucher #{{ $transaction->id }} - Print</title>
    <style>
        /* 80mm Paper Optimization */
        @page {
            size: 80mm auto;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            font-weight: bold;
            line-height: 1.4;
            width: 80mm;
            margin: 0 auto;
            padding: 8px;
            background: white;
            color: #000;
        }

        .receipt-container {
            width: 100%;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }

        .business-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .business-address {
            font-size: 10px;
            margin-bottom: 2px;
            font-weight: normal;
        }

        .business-contact {
            font-size: 9px;
            margin-bottom: 5px;
            font-weight: normal;
        }

        .document-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            background: #000;
            color: white;
            padding: 5px;
        }

        .print-time {
            font-size: 10px;
            margin-top: 3px;
        }

        /* Transaction Info */
        .transaction-info {
            margin-bottom: 15px;
            border: 1px solid #000;
            padding: 8px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 11px;
            font-weight: bold;
        }

        .info-label {
            width: 40%;
        }

        .info-value {
            width: 60%;
            text-align: right;
        }

        /* Transaction Lines */
        .transaction-lines {
            margin-bottom: 15px;
        }

        .lines-header {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 8px;
            padding: 5px;
            border: 2px solid #000;
            background: #000;
            color: white;
        }

        .line-item {
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #000;
        }

        .ledger-name {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .ledger-type {
            font-size: 10px;
            margin-bottom: 5px;
            color: #333;
        }

        .line-amounts {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }

        .amount-dr,
        .amount-cr {
            font-size: 11px;
            font-weight: bold;
            padding: 3px;
        }

        .amount-dr {
            border: 1px solid #000;
        }

        .amount-cr {
            border: 1px solid #000;
        }

        /* Totals */
        .totals-section {
            margin-bottom: 15px;
            padding: 8px;
            border: 2px solid #000;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 12px;
        }

        .grand-total {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            padding: 8px;
            margin: 10px 0;
            border: 2px solid #000;
            background: #000;
            color: white;
        }

        /* Balance Check */
        .balance-check {
            text-align: center;
            margin: 10px 0;
            font-size: 12px;
            font-weight: bold;
            padding: 8px;
            border: 2px solid #000;
        }

        .balanced {
            color: #000;
        }

        /* Amount in Words */
        .amount-words {
            margin: 15px 0;
            padding: 8px;
            border: 2px solid #000;
            text-align: center;
        }

        .amount-words-label {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .amount-words-text {
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        /* Signature Section */
        .signature-section {
            margin: 20px 0;
            display: flex;
            justify-content: space-between;
        }

        .signature-line {
            width: 45%;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
        }

        .signature-space {
            height: 30px;
            border-bottom: 2px solid #000;
            margin: 8px 0;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 2px solid #000;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
        }

        /* Print Button */
        .print-controls {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
            background: white;
            padding: 10px;
            border: 2px solid #000;
            border-radius: 5px;
        }

        .print-btn,
        .close-btn {
            background: #000;
            color: white;
            border: none;
            padding: 10px 15px;
            margin: 0 5px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
        }

        .print-btn:hover,
        .close-btn:hover {
            background: #333;
        }

        /* Print Specific */
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                font-size: 11px;
                padding: 5px;
            }
        }
    </style>
</head>

<body>
    <!-- Print Controls -->
    <div class="print-controls no-print">
        <button onclick="window.print()" class="print-btn">🖨️ Print</button>
        <button onclick="window.close()" class="close-btn">✕ Close</button>
    </div>

    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            @if(isset($hospital) && $hospital)
            <div class="business-name">{{ $hospital->name }}</div>
            @if($hospital->address)
            <div class="business-address">{{ $hospital->address }}</div>
            @endif
            @if($hospital->phone || $hospital->email)
            <div class="business-contact">
                @if($hospital->phone)Phone: {{ $hospital->phone }}@endif
                @if($hospital->phone && $hospital->email) | @endif
                @if($hospital->email)Email: {{ $hospital->email }}@endif
            </div>
            @endif
            @else
            <div class="business-name">{{ config('app.name', 'Your Business') }}</div>
            @endif

            <div class="document-title">{{ strtoupper($transaction->transaction_type) }} VOUCHER</div>
            <div class="print-time">Printed: {{ now()->format('d/m/Y H:i:s') }}</div>
        </div>

        <!-- Transaction Info -->
        <div class="transaction-info">
            <div class="info-row">
                <span class="info-label">Voucher No:</span>
                <span class="info-value">#{{ $transaction->id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Type:</span>
                <span class="info-value">{{ strtoupper($transaction->transaction_type) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Amount:</span>
                <span class="info-value">৳ {{ number_format($transaction->amount, 2) }}</span>
            </div>
            @if($transaction->narration)
            <div class="info-row">
                <span class="info-label">Description:</span>
                <span class="info-value">{{ $transaction->narration }}</span>
            </div>
            @endif
        </div>

        <!-- Transaction Lines -->
        <div class="transaction-lines">
            <div class="lines-header">ACCOUNT DETAILS</div>

            @php
            $totalDebit = 0;
            $totalCredit = 0;
            @endphp

            @foreach($transaction->transactionLines as $line)
            @php
            $totalDebit += $line->debit_amount;
            $totalCredit += $line->credit_amount;
            @endphp
            <div class="line-item">
                <div class="ledger-name">{{ $line->ledger->name }}</div>
                <div class="line-amounts">
                    <div class="amount-dr">
                        DEBIT: ৳ {{ number_format($line->debit_amount, 2) }}
                    </div>
                    <div class="amount-cr">
                        CREDIT: ৳ {{ number_format($line->credit_amount, 2) }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Totals -->
        <div class="totals-section">
            <div class="total-row">
                <span>TOTAL DEBIT:</span>
                <span>৳ {{ number_format($totalDebit, 2) }}</span>
            </div>
            <div class="total-row">
                <span>TOTAL CREDIT:</span>
                <span>৳ {{ number_format($totalCredit, 2) }}</span>
            </div>
        </div>

        <div class="grand-total">
            VOUCHER AMOUNT: ৳ {{ number_format($transaction->amount, 2) }}
        </div>

        <!-- Balance Check - Only show if balanced -->
        @if(abs($totalDebit - $totalCredit) <= 0.01)
            <div class="balance-check balanced">
            ✓ VOUCHER BALANCED
    </div>
    @endif

    <!-- Amount in Words -->
    <div class="amount-words">
        <div class="amount-words-label">AMOUNT IN WORDS:</div>
        <div class="amount-words-text">
            @php
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

                $integerPart=intval($transaction->amount);
                $decimalPart = intval(($transaction->amount - $integerPart) * 100);

                $amountInWords = numberToWords($integerPart) . ' Taka';
                if ($decimalPart > 0) {
                $amountInWords .= ' and ' . numberToWords($decimalPart) . ' Paisa';
                }
                $amountInWords .= ' Only';
                @endphp
                {{ $amountInWords }}
        </div>
    </div>
    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-line">
            <div>PREPARED BY:</div>
            <div class="signature-space">ePATNER</div>
        </div>
        <div class="signature-line">
            <div>APPROVED BY:</div>
            <div class="signature-space"></div>
            <div>Signature & Date</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>Powered By ePATNER</div>
        <div>Generated on: {{ now()->format('d/m/Y') }}</div>
        <div>Voucher ID: {{ $transaction->id }} | Type: {{ $transaction->transaction_type }}</div>
    </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }

        // Auto close after printing
        window.addEventListener('afterprint', function() {
            setTimeout(function() {
                window.close();
            }, 1000); // Wait 1 second after printing completes
        });

        // Handle print dialog cancellation
        window.addEventListener('beforeprint', function() {
            // Set a flag when print dialog opens
            window.printDialogOpened = true;
        });

        // Check if print was cancelled and close anyway
        setTimeout(function() {
            if (window.printDialogOpened) {
                // If print dialog was opened, close after reasonable time
                setTimeout(function() {
                    window.close();
                }, 10000); // Close after 10 seconds regardless
            }
        }, 2000);

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
            }

            if (e.key === 'Escape') {
                window.close();
            }
        });

        // Alternative method for browsers that don't support afterprint
        if (window.matchMedia) {
            var mediaQueryList = window.matchMedia('print');
            mediaQueryList.addListener(function(mql) {
                if (!mql.matches) {
                    // Print mode ended
                    setTimeout(function() {
                        window.close();
                    }, 1000);
                }
            });
        }
    </script>
</body>

</html>