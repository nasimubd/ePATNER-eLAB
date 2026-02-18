<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Report - {{ $labReport->report_number }}</title>
    <style>
        /* A4 Print Styles */
        @page {
            size: A4;
            margin: 10mm;
            /* 5px for top/bottom, 12mm for left/right */

            @top-left {
                content: "";
            }

            @top-center {
                content: "";
            }

            @top-right {
                content: "";
            }

            @bottom-left {
                content: "";
            }

            @bottom-center {
                content: "";
            }

            @bottom-right {
                content: "";
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier Prime', monospace;
            font-size: 10px;
            line-height: 1.2;
            color: #000;
            background: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .print-container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Letterhead Space - Reserved for official header (Similar to Invoice) */
        .letterhead-space {
            height: 35mm;
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

        .report-title {
            text-align: center;
            background: white;
            padding: 8px;
            border: 1px solid #000;
            margin: 8px 0;
        }

        .report-title h2 {
            font-size: 20px;
            font-weight: bold;
            color: #000;
            margin-bottom: 3px;
        }

        .report-number {
            font-size: 14px;
            font-weight: bold;
            color: #000;
        }

        /* Report Header with Barcodes */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #000;
        }

        .barcode-section {
            flex: 0 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .barcode-section svg {
            width: 120px !important;
            height: 50px !important;
        }

        .barcode-label {
            font-size: 12px;
            font-weight: bold;
            color: #000;
            margin-bottom: 2px;
        }

        .report-title-center {
            flex: 1;
            text-align: center;
        }

        .report-main-title {
            font-size: 25px;
            font-weight: bold;
            color: #000;
            margin: 2px 0;
        }

        .report-sub-number {
            font-size: 10px;
            color: #000;
            font-weight: 600;
        }

        /* Content Area */
        .content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding-bottom: 120px;
            /* Space for fixed signature */
        }

        /* Patient Info - More Compact */
        .patient-info-compact {
            background: white;
            padding: 10px;
            border: 1px solid #000;
            margin-bottom: 12px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
        }

        .patient-left {
            flex: 1;
            padding-right: 15px;
        }

        .patient-right {
            flex: 1;
            text-align: right;
        }

        .patient-info-compact p {
            margin-bottom: 4px;
            line-height: 1.3;
        }

        .patient-info-compact strong {
            color: #000;
        }

        /* Test Results Section - Compact */
        .results-section {
            margin: 10px 0;
        }

        .section-header {
            display: none;
            background: #000;
            color: white;
            padding: 8px;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 0;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 12px;
            background: white;
        }

        .results-table th {
            background: white;
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            color: #000;
            font-size: 15px;
        }

        .results-table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
            line-height: 1.3;
            font-size: 14px;
        }

        .results-table tr:nth-child(even) {
            background: white;
        }

        .abnormal-value {
            color: #000;
            font-weight: bold;
        }

        .normal-value {
            color: #000;
        }

        /* Page Break */
        .page-break {
            page-break-before: always;
        }

        .page-break-avoid {
            page-break-inside: avoid;
        }

        /* Lab Signature Section - Fixed at Bottom */
        .lab-signature {
            position: fixed;
            bottom: 80px;
            left: 0;
            right: 0;
            background: white;
            padding: 15px 20px;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 11px;
            line-height: 1.2;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin: 25px 0 4px 0;
            padding-top: 4px;
        }

        .signature-left {
            text-align: left;
            flex: 1;
        }

        .signature-center {
            text-align: center;
            flex: 1;
            margin: 0 20px;
        }

        .signature-right {
            text-align: right;
            flex: 1;
        }

        .signature-name {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 2px;
        }

        .signature-title {
            font-size: 10px;
            color: #000;
            margin-bottom: 1px;
        }

        /* Footer - Always at bottom */
        .report-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #000;
            padding: 10px;
            text-align: center;
            font-size: 10px;
            color: #000;
            background: white;
            z-index: 99;
        }

        /* Print Specific */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background: white !important;
                margin: 0;
                padding: 0;
            }

            .print-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
                background: white !important;
            }

            .no-print {
                display: none !important;
            }

            .letterhead-space {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: white !important;
            }

            .page-break {
                page-break-before: always;
            }

            .page-break-avoid {
                page-break-inside: avoid;
            }

            /* Hide browser headers/footers */
            @page {
                margin: 5px 12mm;
                /* 5px for top/bottom, 12mm for left/right */
            }

            /* Ensure signature and footer stay fixed on print */
            .lab-signature {
                position: fixed;
                bottom: 80px;
                left: 0;
                right: 0;
                background: white;
                z-index: 100;
            }

            .report-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: white;
                border-top: 1px solid #000;
                padding: 10px;
                text-align: center;
                font-size: 10px;
                color: #000;
                z-index: 99;
            }

            /* Adjust content to account for fixed elements */
            .content-area {
                padding-bottom: 120px;
            }

            /* Ensure header barcodes are visible on print */
            .barcode-section svg {
                display: block !important;
                visibility: visible !important;
            }
        }

        /* Print Button */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #000;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .print-button:hover {
            background: #333;
        }
    </style>
</head>

<body>
    <!-- Print Button (hidden when printing) -->
    <button onclick="window.print()" class="print-button no-print">
        🖨️ Print Report
    </button>

    <div class="print-container">
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
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo" style="max-width: 130%; max-height: 100px; object-fit: contain;">
                </div>
                @endif

                <!-- Business Info -->
                <div style="flex: 1 1 100%; @if(!file_exists(public_path('images/logo.jpg'))) width: 100%; padding: 0 5px; @else padding: 0 20px; @endif">
                    <!-- Business Names (Top) -->
                    @if($letterhead->business_name_bangla)
                    <div style="font-size: 24px; font-weight: bold; color: #000; margin-bottom: 1px; font-family: 'Arial', sans-serif; text-align: justify;">
                        {{ $letterhead->business_name_bangla }}
                    </div>
                    @endif
                    @if($letterhead->business_name_english)
                    <div style="font-size: 20px; font-weight: bold; color: #000; margin-bottom: 3px; text-align: justify;">
                        {{ $letterhead->business_name_english }}
                    </div>
                    @endif

                    <!-- Location -->
                    @if($letterhead->location)
                    <div style="font-size: 15px; color: #333; font-weight: bold; margin-bottom: 3px; text-align: justify;">
                        {{ $letterhead->location }}
                    </div>
                    @endif

                    <!-- Phone Numbers (One Line) -->
                    @if($letterhead->contacts && count($letterhead->contacts) > 0)
                    <div style="font-size: 15px; color: #333; font-weight: bold; margin-bottom: 1px; text-align: justify;">
                        {{ implode(' | ', array_filter($letterhead->contacts)) }}
                    </div>
                    @endif

                    <!-- Email -->
                    @if($letterhead->emails && count($letterhead->emails) > 0)
                    <div style="font-size: 15px; color: #333; font-weight: bold; text-align: justify;">
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
                [LETTERHEAD SPACE - 15MM]
            </div>
            @endif
        </div>

        <!-- Report Header with Barcodes (First Page Only) -->
        <div class="report-header">
            <!-- Lab ID Barcode (Left) -->
            <div class="barcode-section">
                <div class="barcode-label">LAB ID: {{ $labReport->id ?? 'N/A' }}</div>
                @if(isset($labIdBarcode) && $labIdBarcode)
                {!! $labIdBarcode !!}
                @endif
            </div>

            <!-- Report Title (Center) -->
            <div class="report-title-center">
                <div class="report-main-title">PATHOLOGY REPORT</div>
                <div class="report-sub-number">Report No: {{ $labReport->report_number }}</div>
            </div>

            <!-- Invoice ID Barcode (Right) -->
            <div class="barcode-section">
                <div class="barcode-label">INVOICE ID: {{ $labReport->lab_id ?? 'N/A' }}</div>
                @if(isset($invoiceIdBarcode) && $invoiceIdBarcode)
                {!! $invoiceIdBarcode !!}
                @endif
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Patient Information with Left/Right Layout -->
            <div class="patient-info-compact">
                <div class="patient-left">
                    <p><strong>Patient ID:</strong> {{ $labReport->patient->patient_id }}</p>
                    <p><strong>Name:</strong> {{ $labReport->patient->first_name }} {{ $labReport->patient->last_name }}</p>
                    <p><strong>Ref. By:</strong> {{ $labReport->advised_by ?? 'SELF' }}</p>
                </div>
                <div class="patient-right">
                    <p><strong>Age:</strong> {{ $labReport->patient->age ?? 'N/A' }} YRS</p>
                    <p><strong>Sex:</strong> {{ ucfirst($labReport->patient->gender ?? 'N/A') }}</p>
                    <p><strong>Date:</strong> {{ $labReport->report_date->format('d M Y') }}</p>
                </div>
            </div>

            <!-- Test Results Sections with Pagination -->
            @if($labReport->sections && $labReport->sections->count() > 0)
            @foreach($labReport->sections as $sectionIndex => $section)
            @if($section->fields && $section->fields->count() > 0)
            @php
            $fields = $section->fields;
            $totalFields = $fields->count();
            $fieldsPerPage = 18; // Adjusted for letterhead space
            $totalPages = ceil($totalFields / $fieldsPerPage);
            @endphp

            @for($page = 0; $page < $totalPages; $page++)
                @php
                $startIndex=$page * $fieldsPerPage;
                $endIndex=min($startIndex + $fieldsPerPage, $totalFields);
                $pageFields=$fields->slice($startIndex, $fieldsPerPage);
                @endphp

                @if($page > 0 || $sectionIndex > 0)
                <div class="page-break"></div>

                <!-- Letterhead on New Page -->
                <div class="letterhead-space">
                    @if(isset($letterhead) && $letterhead)
                    @php
                    $logoPath = public_path('images/logo.jpg');
                    $hasLogo = file_exists($logoPath);
                    @endphp

                    @if($hasLogo)
                    <div style="display: flex; align-items: center; justify-content: space-between; height: 100%; padding: 0 8px; text-align: justify;">
                        @if(file_exists(public_path('images/logo.jpg')))
                        <div style="flex: 0 0 60px; display: flex; align-items: center; justify-content: center;">
                            <img src="{{ asset('images/logo.jpg') }}" alt="Logo" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                        </div>
                        @endif

                        <div style="flex: 1 1 100%; @if(!file_exists(public_path('images/logo.jpg'))) width: 100%; padding: 0 5px; @else padding: 0 10px; @endif">
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

                            @if($letterhead->location)
                            <div style="font-size: 10px; color: #333; font-weight: bold; margin-bottom: 3px; text-align: justify;">
                                {{ $letterhead->location }}
                            </div>
                            @endif

                            @if($letterhead->contacts && count($letterhead->contacts) > 0)
                            <div style="font-size: 10px; color: #333; font-weight: bold; margin-bottom: 1px; text-align: justify;">
                                {{ implode(' | ', array_filter($letterhead->contacts)) }}
                            </div>
                            @endif

                            @if($letterhead->emails && count($letterhead->emails) > 0)
                            <div style="font-size: 10px; color: #333; font-weight: bold; text-align: justify;">
                                {{ implode(' | ', array_filter($letterhead->emails)) }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @else
                    <div style="display: flex; flex-direction: column; justify-content: center; height: 100%; padding: 0 8px; text-align: justify; width: 100%;">
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

                        @if($letterhead->location)
                        <div style="font-size: 12px; color: #333; font-weight: bold; margin-bottom: 3px; text-align: justify;">
                            {{ $letterhead->location }}
                        </div>
                        @endif

                        @if($letterhead->contacts && count($letterhead->contacts) > 0)
                        <div style="font-size: 13px; color: #333; font-weight: bold; margin-bottom: 1px; text-align: justify;">
                            {{ implode(' | ', array_filter($letterhead->contacts)) }}
                        </div>
                        @endif

                        @if($letterhead->emails && count($letterhead->emails) > 0)
                        <div style="font-size: 13px; color: #333; font-weight: bold; text-align: justify;">
                            {{ implode(' | ', array_filter($letterhead->emails)) }}
                        </div>
                        @endif
                    </div>
                    @endif
                    @else
                    <div class="letterhead-placeholder">
                        [LETTERHEAD SPACE - 15MM]
                    </div>
                    @endif
                </div>

                <!-- Report Header with Barcodes -->
                <div class="report-header">
                    <div class="barcode-section">
                        <div class="barcode-label">LAB ID: {{ $labReport->id ?? 'N/A' }}</div>
                        @if(isset($labIdBarcode) && $labIdBarcode)
                        {!! $labIdBarcode !!}
                        @endif
                    </div>

                    <div class="report-title-center">
                        <div class="report-main-title">PATHOLOGY REPORT</div>
                        <div class="report-sub-number">Report No: {{ $labReport->report_number }}</div>
                    </div>

                    <div class="barcode-section">
                        <div class="barcode-label">INVOICE ID: {{ $labReport->lab_id ?? 'N/A' }}</div>
                        @if(isset($invoiceIdBarcode) && $invoiceIdBarcode)
                        {!! $invoiceIdBarcode !!}
                        @endif
                    </div>
                </div>

                <!-- Repeat Patient Info on New Page -->
                <div class="patient-info-compact">
                    <div class="patient-left">
                        <p><strong>Patient ID:</strong> {{ $labReport->patient->patient_id }}</p>
                        <p><strong>Name:</strong> {{ $labReport->patient->first_name }} {{ $labReport->patient->last_name }}</p>
                        <p><strong>Ref. By:</strong> {{ $labReport->advised_by ?? 'SELF' }}</p>
                    </div>
                    <div class="patient-right">
                        <p><strong>Age:</strong> {{ $labReport->patient->age ?? 'N/A' }} YRS</p>
                        <p><strong>Sex:</strong> {{ ucfirst($labReport->patient->gender ?? 'N/A') }}</p>
                        <p><strong>Date:</strong> {{ $labReport->report_date->format('d M Y') }}</p>
                    </div>
                </div>
                @endif

                <div class="results-section page-break-avoid">
                    <div class="section-header">{{ strtoupper($section->name) }}</div>
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th style="width: 40%;">Test Parameter</th>
                                <th style="width: 20%;">Result</th>
                                <th style="width: 15%;">Unit</th>
                                <th style="width: 25%;">Normal Range</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pageFields as $field)
                            <tr>
                                <td><strong>{{ $field->field_label ?? $field->field_name ?? '-' }}</strong></td>
                                <td class="{{ $field->is_abnormal ? 'abnormal-value' : 'normal-value' }}">
                                    {{ $field->field_value ?? '-' }}
                                </td>
                                <td>{{ $field->unit ?? '-' }}</td>
                                <td>{{ $field->normal_range ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endfor
                @endif
                @endforeach
                @else
                <div class="results-section">
                    <div class="section-header">TEST RESULTS</div>
                    <div style="padding: 15px; text-align: center; color: #000; font-size: 12px;">
                        No test results available for this report.
                    </div>
                </div>
                @endif
        </div>

        <!-- Lab Signature Section - Fixed at Bottom -->
        <div class="lab-signature">
            <div class="signature-left">
                <div class="signature-title" style="text-decoration: underline; margin-bottom: 5px;">Lab Incharge</div>
                <div class="signature-name">LOTA KHATUN</div>
                <div class="signature-title">Dip .In Lab.Medicine (IMTM)</div>
                <div class="signature-title">F.T.250 General Hospital, Meherpur</div>
                <div class="signature-title">Medical Health Technologist</div>
            </div>
            <div class="signature-center">
                <div class="signature-line">
                    <div class="signature-name">Checked By</div>
                </div>
            </div>
            <div class="signature-right">
                <div>
                    <div class="signature-name">Dr. Md. Al Mamun</div>
                    <div class="signature-title">MBBS, CCD, PGT</div>
                    <div class="signature-title">F.T.250 General Hospital, Meherpur</div>
                </div>
            </div>
        </div>

        <!-- Footer - Always at bottom -->
        <div style="margin-top: 4px; page-break-inside: avoid;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #000; padding: 3px 0;">
                <!-- Left Side: Text Information -->
                <div style="flex: 1; text-align: left;">
                    <div style="font-size: 10px; margin-bottom: 1px; color: #000; font-weight: 700;">Printed: {{ now()->format('d/m/Y h:i A') }}</div>
                    <div style="font-size: 10px; color: #000; line-height: 2; font-weight: 700;">This is a computer-generated report.</div>
                    <div style="font-size: 7px; font-weight: 900; margin-top: 2px; letter-spacing: 0.5px; color: #000;">
                        POWERED BY <span style="font-size: 8px;">ePATNER | eLAB (SCAN THE QR CODE TO GET MORE INFO....)</span>
                    </div>
                </div>

                <!-- Right Side: QR Code -->
                <div style="flex: 0 0 auto; text-align: center;">
                    <img src="{{ asset('images/ePATNER_QR.png') }}" alt="ePATNER QR" style="width: 50px; height: 50px; object-fit: contain; display: block;">
                </div>
            </div>
        </div>
    </div><!-- End print-container -->

    <script>
        // Print function
        function printReport() {
            window.print();
        }

        // Keyboard shortcut for printing (Ctrl+P)
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });

        // Hide print button during printing
        window.addEventListener('beforeprint', function() {
            document.querySelector('.print-button').style.display = 'none';
        });

        window.addEventListener('afterprint', function() {
            document.querySelector('.print-button').style.display = 'block';
        });
    </script>
</body>

</html>