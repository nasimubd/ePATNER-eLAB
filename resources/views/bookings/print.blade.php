<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Receipt - #{{ $booking->id }}</title>
    <style>
        /* A4 Print Styles with Compact Layout */
        @page {
            size: A4;
            margin: 10mm 8mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #2c3e50;
            background: white;
            padding: 5mm;
        }

        .print-container {
            width: 100%;
            max-width: 190mm;
            margin: 0 auto;
            background: white;
            padding: 10px;
            overflow: hidden;
        }

        /* Vibrant Header Section */
        .receipt-header {
            text-align: center;
            padding: 15px;
            margin-bottom: 20px;
            position: relative;
        }

        .hospital-name {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .hospital-details {
            font-size: 12px;
            color: #764ba2;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .receipt-title {
            color: #ff6b6b;
            padding: 10px 20px;
            margin: 15px auto;
            max-width: 350px;
            background: white;
        }

        .receipt-title h2 {
            font-size: 18px;
            margin-bottom: 6px;
            font-weight: bold;
            color: #e74c3c;
        }

        .booking-number {
            font-size: 14px;
            font-weight: bold;
            color: #ff6b6b;
        }

        /* Compact Info Section with Vibrant Colors */
        .info-section {
            display: flex;
            gap: 15px;
            margin: 20px 0;
        }

        .patient-info,
        .booking-info {
            flex: 1;
            background: white;
            padding: 15px;
        }

        .info-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 12px;
            text-align: center;
            color: #74b9ff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-bottom: 6px;
        }

        .booking-info .info-title {
            color: #00b894;
        }

        .info-row {
            margin-bottom: 8px;
            display: flex;
            align-items: flex-start;
            font-size: 10px;
        }

        .info-label {
            font-weight: bold;
            width: 90px;
            color: #2d3748;
            font-size: 10px;
        }

        .info-value {
            flex: 1;
            font-weight: 600;
            font-size: 10px;
            color: #4a5568;
        }

        /* Compact Schedule Section */
        .schedule-section {
            margin: 20px 0;
        }

        .section-header {
            color: #a29bfe;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: white;
            margin-bottom: 10px;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 10px;
        }

        .schedule-table th {
            background: white;
            padding: 10px 6px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            color: #fd79a8;
        }

        .schedule-table td {
            background: white;
            padding: 12px 6px;
            text-align: center;
            font-weight: 600;
            color: #2d3748;
        }

        .schedule-table tr:nth-child(even) td {
            background: #f8f9ff;
        }

        /* Vibrant Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            background: white;
        }

        .status-pending {
            color: #fdcb6e;
        }

        .status-confirmed {
            color: #74b9ff;
        }

        .status-completed {
            color: #00b894;
        }

        .status-cancelled {
            color: #ff7675;
        }

        /* Compact Notes Section */
        .notes-section {
            margin: 20px 0;
        }

        .notes-box {
            background: white;
            padding: 15px;
            margin-bottom: 12px;
        }

        .notes-title {
            font-weight: bold;
            color: #fdcb6e;
            margin-bottom: 8px;
            font-size: 12px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-bottom: 4px;
        }

        .notes-content {
            color: #2d3748;
            line-height: 1.5;
            font-size: 9px;
            font-weight: 500;
        }

        /* Compact Footer */
        .receipt-footer {
            margin-top: 20px;
            background: white;
            padding: 12px;
        }

        .footer-final {
            text-align: center;
            font-size: 9px;
            line-height: 1.4;
            color: #2d3748;
        }

        .footer-final strong {
            color: #667eea;
        }

        /* Print Button */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            padding: 12px 24px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .print-button:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        /* Print Specific Styles */
        @media print {
            @page {
                margin: 0;
                size: A4;
            }

            body {
                background: white !important;
                padding: 10mm;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .print-container {
                max-width: 100%;
                padding: 8px;
            }

            .no-print {
                display: none !important;
            }

            /* Ensure single page */
            .receipt-header {
                margin-bottom: 15px;
                padding: 10px;
            }

            .info-section {
                margin: 15px 0;
            }

            .schedule-section {
                margin: 15px 0;
            }

            .notes-section {
                margin: 15px 0;
            }

            .receipt-footer {
                margin-top: 15px;
            }

            /* Prevent page breaks */
            .info-section,
            .schedule-section,
            .notes-section {
                page-break-inside: avoid;
            }

            /* Ensure colors print correctly */
            .hospital-name,
            .receipt-title h2,
            .booking-number,
            .info-title,
            .section-header,
            .schedule-table th,
            .status-badge,
            .notes-title,
            .footer-final strong {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .info-section {
                flex-direction: column;
            }

            .patient-info,
            .booking-info {
                margin-bottom: 10px;
            }
        }

        /* Add some animation effects for screen */
        .print-container {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Vibrant text highlights */
        .highlight-blue {
            color: #74b9ff;
            font-weight: bold;
        }

        .highlight-green {
            color: #00b894;
            font-weight: bold;
        }

        .highlight-purple {
            color: #a29bfe;
            font-weight: bold;
        }

        .highlight-pink {
            color: #fd79a8;
            font-weight: bold;
        }

        .highlight-orange {
            color: #fdcb6e;
            font-weight: bold;
        }

        .highlight-red {
            color: #ff7675;
            font-weight: bold;
        }

        /* Section separators */
        .section-separator {
            height: 2px;
            background: linear-gradient(to right, #667eea, #764ba2, #74b9ff, #00b894, #a29bfe, #fd79a8, #fdcb6e, #ff7675);
            margin: 15px 0;
            border-radius: 1px;
        }
    </style>
</head>

<body>
    <!-- Print Button -->
    <button onclick="window.print()" class="print-button no-print">
        🖨️ Print Receipt
    </button>

    <div class="print-container">
        <!-- Vibrant Header Section -->
        <div class="receipt-header">
            <div class="hospital-name">{{ $hospital->name }}</div>
            <div class="hospital-details">
                {{ $hospital->address }}<br>
                <span class="highlight-blue">📞 {{ $hospital->phone }}</span>
                @if($hospital->email)
                | <span class="highlight-green">✉️ {{ $hospital->email }}</span>
                @endif
            </div>

            <div class="receipt-title">
                <h2>🏥 BOOKING RECEIPT</h2>
                <div class="booking-number">Receipt No: #{{ $booking->id }}</div>
            </div>
        </div>

        <div class="section-separator"></div>

        <!-- Compact Patient and Booking Information -->
        <div class="info-section">
            <div class="patient-info">
                <div class="info-title">👤 Patient Information</div>
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-value highlight-blue">{{ $booking->patient->first_name }} {{ $booking->patient->last_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Patient ID:</span>
                    <span class="info-value highlight-purple">{{ $booking->patient->patient_id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Age:</span>
                    <span class="info-value highlight-orange">{{ $booking->patient->age ?? 'N/A' }} years</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Gender:</span>
                    <span class="info-value highlight-pink">{{ ucfirst($booking->patient->gender ?? 'N/A') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value highlight-green">{{ $booking->patient->phone ?? 'N/A' }}</span>
                </div>
                @if($booking->patient->email)
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value highlight-blue">{{ $booking->patient->email }}</span>
                </div>
                @endif
            </div>

            <div class="booking-info">
                <div class="info-title">📅 Booking Information</div>
                <div class="info-row">
                    <span class="info-label">Service:</span>
                    <span class="info-value highlight-green">{{ $booking->bookable->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Type:</span>
                    <span class="info-value highlight-purple">{{ ucfirst($booking->booking_type) }}</span>
                </div>
                @if($booking->booking_type === 'ot' && $booking->otRoom)
                <div class="info-row">
                    <span class="info-label">OT Room:</span>
                    <span class="info-value highlight-orange">{{ $booking->otRoom->name }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Date:</span>
                    <span class="info-value highlight-red">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Time:</span>
                    <span class="info-value highlight-pink">{{ \Carbon\Carbon::parse($booking->booking_time)->format('g:i A') }}</span>
                </div>
                @if($booking->end_time)
                <div class="info-row">
                    <span class="info-label">End Time:</span>
                    <span class="info-value highlight-pink">{{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <span class="status-badge status-{{ $booking->status }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Created:</span>
                    <span class="info-value highlight-blue">{{ $booking->created_at->format('d M Y H:i') }}</span>
                </div>
            </div>
        </div>

        <div class="section-separator"></div>

        <!-- Compact Schedule Section -->
        <div class="schedule-section">
            <div class="section-header">⏰ Appointment Schedule</div>
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>📅 Date</th>
                        <th>🕐 Start Time</th>
                        @if($booking->end_time)
                        <th>🕐 End Time</th>
                        @endif
                        <th>📆 Day</th>
                        <th>⏱️ Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="highlight-red">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M j, Y') }}</span></td>
                        <td><span class="highlight-pink">{{ \Carbon\Carbon::parse($booking->booking_time)->format('g:i A') }}</span></td>
                        @if($booking->end_time)
                        <td><span class="highlight-pink">{{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</span></td>
                        @endif
                        <td><span class="highlight-purple">{{ \Carbon\Carbon::parse($booking->booking_date)->format('l') }}</span></td>
                        <td>
                            @if($booking->end_time)
                            <span class="highlight-orange">{{ \Carbon\Carbon::parse($booking->booking_time)->diffInMinutes(\Carbon\Carbon::parse($booking->end_time)) }} mins</span>
                            @else
                            <span class="highlight-orange">N/A</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section-separator"></div>

        <!-- Compact Notes Sections -->
        <div class="notes-section">
            @if($booking->notes)
            <div class="notes-box">
                <div class="notes-title">📝 Additional Notes</div>
                <div class="notes-content">{{ $booking->notes }}</div>
            </div>
            @endif

            <!-- Important Instructions -->
            <div class="notes-box">
                <div class="notes-title">⚠️ Important Instructions</div>
                <div class="notes-content">
                    @if($booking->booking_type === 'ot')
                    • <span class="highlight-red">Arrive 2 hours before your scheduled procedure time</span><br>
                    • <span class="highlight-blue">Bring all required medical documents and test reports</span><br>
                    • <span class="highlight-green">Follow pre-operative fasting instructions as advised</span><br>
                    • <span class="highlight-purple">Arrange for a responsible adult to accompany you</span><br>
                    • <span class="highlight-orange">Contact the hospital immediately if you experience any emergency symptoms</span>
                    @else
                    • <span class="highlight-red">Please arrive 15 minutes before your scheduled appointment</span><br>
                    • <span class="highlight-blue">Bring your medical records and previous test reports</span><br>
                    • <span class="highlight-green">Carry a valid ID proof for verification</span><br>
                    • <span class="highlight-purple">Contact us immediately if you need to reschedule</span><br>
                    • <span class="highlight-orange">Follow any specific preparation instructions given by your doctor</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="section-separator"></div>

        <!-- Compact Footer Section -->
        <div class="receipt-footer">
            <div class="footer-final">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <div style="font-size: 9px;">
                        <strong>Generated:</strong> <span class="highlight-blue">{{ now()->format('d M Y H:i') }}</span> | <strong>By:</strong> <span class="highlight-green">System</span>
                    </div>
                    <div style="font-size: 9px;">
                        <strong>Booking ID:</strong> <span class="highlight-purple">#{{ $booking->id }}</span> | <strong>Patient ID:</strong> <span class="highlight-orange">{{ $booking->patient->patient_id }}</span>
                    </div>
                </div>
                <div style="text-align: center; font-size: 9px; padding-top: 6px;">
                    <strong class="highlight-red">{{ $hospital->name }}</strong> - <span class="highlight-blue">Computer-generated receipt. For queries, please contact us.</span> 🏥
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };

        // Print function
        function printReceipt() {
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
            const printButton = document.querySelector('.print-button');
            if (printButton) {
                printButton.style.display = 'none';
            }
        });

        window.addEventListener('afterprint', function() {
            const printButton = document.querySelector('.print-button');
            if (printButton) {
                printButton.style.display = 'block';
            }
        });

        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects to info sections
            const infoSections = document.querySelectorAll('.patient-info, .booking-info');
            infoSections.forEach(section => {
                section.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.transition = 'all 0.3s ease';
                    this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
                });
                section.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            });

            // Add pulse effect to status badge
            const statusBadge = document.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.style.animation = 'pulse 2s infinite';
            }

            // Add hover effects to table rows
            const tableRows = document.querySelectorAll('.schedule-table tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f0f8ff';
                    this.style.transition = 'background-color 0.3s ease';
                });
                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });
        });

        // Add pulse animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
        `;
        document.head.appendChild(style);

        // Remove browser print headers/footers
        window.addEventListener('beforeprint', function() {
            // This will help minimize browser headers/footers
            document.title = '';
        });

        window.addEventListener('afterprint', function() {
            document.title = 'Booking Receipt - #{{ $booking->id }}';
        });
    </script>
</body>

</html>