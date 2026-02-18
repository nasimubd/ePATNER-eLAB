<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template Preview - {{ $template->template_name }}</title>
    <style>
        /* A4 Print Styles */
        @page {
            size: A4;
            margin: 12mm;

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
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.2;
            color: #333;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 20px 0;
        }

        .print-container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Header Section - Compact */
        .report-header {
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .hospital-logo {
            width: 50px;
            height: 50px;
            margin-left: 20px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .hospital-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .hospital-info {
            flex: 1;
            text-align: right;
        }

        .hospital-name {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 2px;
        }

        .hospital-details {
            font-size: 9px;
            color: #666;
            line-height: 1.1;
        }

        .report-title {
            text-align: center;
            background: #f8f9fa;
            padding: 6px;
            border: 1px solid #dee2e6;
            margin: 8px 0;
        }

        .report-title h2 {
            font-size: 14px;
            color: #2c3e50;
            margin-bottom: 2px;
        }

        .report-number {
            font-size: 11px;
            font-weight: bold;
            color: #e74c3c;
        }

        /* Content Area */
        .content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding-bottom: 80px;
            /* Space for fixed signature */
        }

        /* Patient Info - More Compact */
        .patient-info-compact {
            background: #f8f9fa;
            padding: 8px;
            border: 1px solid #dee2e6;
            margin-bottom: 10px;
            font-size: 10px;
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
            margin-bottom: 3px;
            line-height: 1.3;
        }

        .patient-info-compact strong {
            color: #2c3e50;
        }

        /* Test Results Section - Compact */
        .results-section {
            margin: 8px 0;
        }

        .section-header {
            background: #34495e;
            color: white;
            padding: 6px;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 0;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 8px;
            /* Reduced from 10px to 8px */
            background: white;
        }

        .results-table th {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 4px;
            /* Reduced from 6px to 4px */
            text-align: left;
            font-weight: bold;
            color: #2c3e50;
            font-size: 9px;
        }

        .results-table td {
            border: 1px solid #dee2e6;
            padding: 3px;
            /* Reduced from 6px to 3px */
            vertical-align: top;
            line-height: 1.2;
        }

        .results-table tr:nth-child(even) {
            background: #f9f9f9;
        }

        .abnormal-value {
            color: #e74c3c;
            font-weight: bold;
        }

        .normal-value {
            color: #27ae60;
        }

        /* Page Break */
        .page-break {
            page-break-before: always;
        }

        .page-break-avoid {
            page-break-inside: avoid;
        }

        /* Lab Technical Signature - Fixed at Bottom */
        .lab-signature {
            position: fixed;
            bottom: 50px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            background: white;
            padding: 8px 15px;
            z-index: 100;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin: 30px auto 4px auto;
            width: 180px;
            padding-top: 4px;
            font-size: 10px;
            color: #666;
        }

        /* Footer - Always at bottom */
        .report-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #2c3e50;
            padding: 8px;
            text-align: center;
            font-size: 8px;
            color: #666;
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
                border-radius: 0;
            }

            .no-print {
                display: none !important;
            }

            .page-break {
                page-break-before: always;
            }

            .page-break-avoid {
                page-break-inside: avoid;
            }

            /* Hide browser headers/footers */
            @page {
                margin: 12mm;
            }

            /* Ensure signature and footer stay fixed on print */
            .lab-signature {
                position: fixed;
                bottom: 50px;
                left: 50%;
                transform: translateX(-50%);
                background: white;
                z-index: 100;
            }

            .report-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: white;
                border-top: 1px solid #2c3e50;
                padding: 8px;
                text-align: center;
                font-size: 8px;
                color: #666;
                z-index: 99;
            }

            /* Adjust content to account for fixed elements */
            .content-area {
                padding-bottom: 100px;
            }
        }

        /* Action Buttons */
        .action-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }

        .action-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .action-button:hover {
            background: #0056b3;
            color: white;
            text-decoration: none;
        }

        .action-button.print-btn {
            background: #28a745;
        }

        .action-button.print-btn:hover {
            background: #1e7e34;
        }

        .action-button.back-btn {
            background: #6c757d;
        }

        .action-button.back-btn:hover {
            background: #545b62;
        }

        /* Preview Badge */
        .preview-badge {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #17a2b8;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <!-- Preview Badge -->
    <div class="preview-badge no-print">
        📋 Template Preview
    </div>

    <!-- Action Buttons (hidden when printing) -->
    <div class="action-buttons no-print">
        <a href="{{ url()->previous() }}" class="action-button back-btn">
            ← Back
        </a>
        <button onclick="window.print()" class="action-button print-btn">
            🖨️ Print Template
        </button>
    </div>

    <div class="print-container">
        <!-- Header Section -->
        <div class="report-header">
            <div class="hospital-logo">
                <img src="{{ asset('images/logo.jpg') }}" alt="Hospital Logo">
            </div>
            <div class="hospital-info">
                <div class="hospital-name">{{ Auth::user()->business->hospital_name ?? 'Medical Laboratory' }}</div>
                <div class="hospital-details">
                    @if(Auth::user()->business)
                    {{ Auth::user()->business->address ?? '' }}<br>
                    Phone: {{ Auth::user()->business->phone ?? 'N/A' }} |
                    Email: {{ Auth::user()->business->email ?? 'N/A' }}<br>
                    @if(Auth::user()->business->license_number)
                    License No: {{ Auth::user()->business->license_number }}
                    @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="report-title">
            <h2>{{ strtoupper($template->template_name) }}</h2>
            <div class="report-number">Template for: {{ $template->labTest->test_name ?? 'Lab Test' }}</div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Sample Patient Information for Template Preview -->
            <div class="patient-info-compact">
                <div class="patient-left">
                    <p><strong>Patient ID:</strong> SAMPLE001</p>
                    <p><strong>Name:</strong> John Doe</p>
                    <p><strong>Ref. By:</strong> Dr. Smith</p>
                </div>
                <div class="patient-right">
                    <p><strong>Age:</strong> 35 YRS</p>
                    <p><strong>Sex:</strong> Male</p>
                    <p><strong>Date:</strong> {{ now()->format('d M Y') }}</p>
                </div>
            </div>

            <!-- Template Sections Preview -->
            @if($template->sections && $template->sections->count() > 0)
            @foreach($template->sections as $sectionIndex => $section)
            @if($section->fields && $section->fields->count() > 0)
            @php
            $fields = $section->fields;
            $totalFields = $fields->count();
            $fieldsPerPage = 20; // Increased from 12 to 20 rows
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

                <!-- Repeat Header on New Page -->
                <div class="report-header">
                    <div class="hospital-logo">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Hospital Logo">
                    </div>
                    <div class="hospital-info">
                        <div class="hospital-name">{{ Auth::user()->business->hospital_name ?? 'Medical Laboratory' }}</div>
                        <div class="hospital-details">
                            @if(Auth::user()->business)
                            {{ Auth::user()->business->address ?? '' }}<br>
                            Phone: {{ Auth::user()->business->phone ?? 'N/A' }} |
                            Email: {{ Auth::user()->business->email ?? 'N/A' }}<br>
                            @if(Auth::user()->business->license_number)
                            License No: {{ Auth::user()->business->license_number }}
                            @endif
                            @endif
                        </div>
                    </div>
                </div>

                <div class="report-title">
                    <h2>{{ strtoupper($template->template_name) }}</h2>
                    <div class="report-number">Template for: {{ $template->labTest->test_name ?? 'Lab Test' }}</div>
                </div>

                <!-- Repeat Patient Info on New Page -->
                <div class="patient-info-compact">
                    <div class="patient-left">
                        <p><strong>Patient ID:</strong> SAMPLE001</p>
                        <p><strong>Name:</strong> John Doe</p>
                        <p><strong>Ref. By:</strong> Dr. Smith</p>
                    </div>
                    <div class="patient-right">
                        <p><strong>Age:</strong> 35 YRS</p>
                        <p><strong>Sex:</strong> Male</p>
                        <p><strong>Date:</strong> {{ now()->format('d M Y') }}</p>
                    </div>
                </div>
                @endif

                <div class="results-section">
                    <div class="section-header">
                        {{ strtoupper($section->section_name) }}
                        @if($section->section_description)
                        - {{ $section->section_description }}
                        @endif
                        @if($totalPages > 1)
                        (Page {{ $page + 1 }} of {{ $totalPages }})
                        @endif
                    </div>

                    <table class="results-table">
                        <thead>
                            <tr>
                                <th style="width: 30%;">Test Parameter</th>
                                <th style="width: 20%;">Result</th>
                                <th style="width: 15%;">Unit</th>
                                <th style="width: 25%;">Normal Range</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pageFields as $field)
                            <tr>
                                <td>
                                    <strong>{{ $field->field_label }}</strong>
                                    @if($field->field_name !== $field->field_label)
                                    <br><small style="color: #666; font-size: 7px;">({{ $field->field_name }})</small>
                                    @endif
                                </td>
                                <td class="normal-value">
                                    <strong>
                                        @if($field->default_value)
                                        {{ $field->default_value }}
                                        @else
                                        @switch($field->field_type)
                                        @case('number')
                                        {{ rand(10, 100) }}
                                        @break
                                        @case('select')
                                        @if($field->field_options)
                                        @php
                                        $options = is_array($field->field_options) ? $field->field_options : json_decode($field->field_options, true);
                                        if (!$options) $options = explode(',', $field->field_options);
                                        @endphp
                                        {{ $options[0] ?? 'Option 1' }}
                                        @else
                                        Sample Value
                                        @endif
                                        @break
                                        @case('date')
                                        {{ now()->format('d/m/Y') }}
                                        @break
                                        @case('time')
                                        {{ now()->format('H:i') }}
                                        @break
                                        @default
                                        Sample Value
                                        @endswitch
                                        @endif
                                    </strong>
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
                    <div style="padding: 15px; text-align: center; color: #666; font-size: 10px;">
                        No test results available for this report.
                    </div>
                </div>
                @endif
        </div>

        <!-- Lab Technical Signature - Fixed at Bottom -->
        <div class="lab-signature">
            <div class="signature-line">
                Lab Technical<br>
                {{ $template->creator->name ?? Auth::user()->name ?? 'Lab Technician' }}
            </div>
        </div>

        <!-- Footer - Always at bottom -->
        <div class="report-footer">
            <strong>{{ Auth::user()->business->hospital_name ?? 'Medical Laboratory' }}</strong><br>
            Template: {{ $template->template_name }} | Preview Generated on: {{ now()->format('d-m-Y H:i:s') }}
        </div>
    </div>

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

        // Hide action buttons during printing
        window.addEventListener('beforeprint', function() {
            const actionButtons = document.querySelector('.action-buttons');
            const previewBadge = document.querySelector('.preview-badge');
            if (actionButtons) actionButtons.style.display = 'none';
            if (previewBadge) previewBadge.style.display = 'none';
        });

        window.addEventListener('afterprint', function() {
            const actionButtons = document.querySelector('.action-buttons');
            const previewBadge = document.querySelector('.preview-badge');
            if (actionButtons) actionButtons.style.display = 'flex';
            if (previewBadge) previewBadge.style.display = 'block';
        });
    </script>
</body>

</html>