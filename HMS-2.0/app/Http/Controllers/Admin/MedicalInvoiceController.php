<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalInvoice;
use App\Models\Patient;
use App\Models\LabTest;
use App\Models\Appointment;
use App\Models\Booking;
use App\Models\CareOf;
use App\Models\Ledger;
use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Models\Doctor;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Picqer\Barcode\BarcodeGeneratorSVG;

class MedicalInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = MedicalInvoice::with(['patient', 'doctor', 'lines.labTest', 'lines.appointment', 'lines.booking'])
            ->select([
                'id',
                'patient_id',
                'doctor_id',
                'invoice_date',
                'status',
                'payment_method',
                'subtotal',
                'discount',
                'grand_total',
                'paid_amount',
                'created_at'
            ]);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('full_name', 'like', "%{$search}%")
                            ->orWhere('patient_id', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        $invoices = $query->leftJoin('doctors', 'medical_invoices.doctor_id', '=', 'doctors.id')
            ->select('medical_invoices.*')
            ->orderByRaw('doctors.name IS NULL ASC, doctors.name ASC')
            ->orderBy('medical_invoices.doctor_id', 'asc')
            ->orderBy('medical_invoices.created_at', 'desc')
            ->paginate(15);

        // Get current user's business for A5 printing setting
        $currentBusiness = Auth::user()->business;

        return view('admin.medical.invoices.index', compact('invoices', 'currentBusiness'));
    }

    public function create()
    {
        // Get business ID from authenticated user
        $user = Auth::user();
        $businessId = $user->business_id;

        if (!$businessId) {
            return redirect()->back()->with('error', 'Access denied. No business association found.');
        }

        // Get all patients, ordered by name
        $patients = Patient::orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        // Get all active lab tests, ordered by test name
        $labTests = LabTest::where('is_active', true)
            ->orderBy('department')
            ->orderBy('test_name')
            ->get();

        // Get test categories/departments
        $testCategories = LabTest::where('is_active', true)
            ->select('department')
            ->distinct()
            ->whereNotNull('department')
            ->orderBy('department')
            ->pluck('department');

        // Group lab tests by department for easier selection
        $labTestsByDepartment = $labTests->groupBy('department');

        // Get Care Of options for the business
        $careOfs = CareOf::where('business_id', $businessId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Get doctors for the business
        $doctors = Doctor::where('business_id', $businessId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.medical.invoices.create', compact(
            'patients',
            'labTests',
            'testCategories',
            'labTestsByDepartment',
            'careOfs',
            'doctors'
        ));
    }

    /**
     * Check patient appointments and bookings for consultation/service fees
     */
    public function checkPatientAppointments(Request $request)
    {
        try {
            Log::info('checkPatientAppointments called', [
                'patient_id' => $request->patient_id,
                'user_id' => Auth::id()
            ]);

            $request->validate([
                'patient_id' => 'required|exists:patients,id'
            ]);

            $patientId = $request->patient_id;
            $user = Auth::user();
            $businessId = $user->business_id;

            $consultationLines = [];
            $bookingLines = [];

            // Check appointments
            if (class_exists('App\Models\Appointment')) {
                try {
                    if (Schema::hasTable('appointments')) {
                        Log::info('Querying appointments table for patient', ['patient_id' => $patientId]);

                        $pendingAppointments = Appointment::where('patient_id', $patientId)
                            ->where(function ($query) {
                                $query->whereIn('status', ['confirmed', 'scheduled', 'completed'])
                                    ->where('payment_status', 'pending');
                            })
                            ->orWhere(function ($query) use ($patientId) {
                                $query->where('patient_id', $patientId)
                                    ->whereIn('status', ['confirmed', 'scheduled'])
                                    ->whereDate('appointment_date', '>=', now()->toDateString());
                            })
                            ->with(['doctor', 'patient'])
                            ->get();

                        foreach ($pendingAppointments as $appointment) {
                            $consultationFee = $appointment->consultation_fee ??
                                $appointment->fee ??
                                $appointment->amount ??
                                ($appointment->doctor->consultation_fee ?? 500);

                            $consultationLines[] = [
                                'appointment_id' => $appointment->id,
                                'service_name' => 'Consultation - ' . ($appointment->doctor->name ?? 'Doctor'),
                                'appointment_date' => $appointment->appointment_date,
                                'appointment_time' => $appointment->appointment_time ?? 'Not specified',
                                'consultation_fee' => $consultationFee,
                                'doctor_name' => $appointment->doctor->name ?? 'Unknown Doctor',
                                'appointment_status' => $appointment->status,
                                'payment_status' => $appointment->payment_status
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Error querying appointments table', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Check bookings
            if (class_exists('App\Models\Booking')) {
                try {
                    if (Schema::hasTable('bookings')) {
                        Log::info('Querying bookings table for patient', ['patient_id' => $patientId]);

                        $pendingBookings = Booking::where('patient_id', $patientId)
                            ->where('business_id', $businessId)
                            ->whereIn('status', ['pending', 'confirmed'])
                            ->whereDate('booking_date', '>=', now()->toDateString())
                            ->with(['bookable', 'otRoom'])
                            ->get();

                        foreach ($pendingBookings as $booking) {
                            $serviceName = '';
                            $serviceDetails = '';

                            if ($booking->booking_type === 'ward') {
                                $serviceName = 'Ward Booking';
                                if ($booking->bookable) {
                                    $serviceName .= ' - ' . $booking->bookable->name;
                                }
                            } elseif ($booking->booking_type === 'ot') {
                                $serviceName = 'OT Booking';
                                if ($booking->otRoom) {
                                    $serviceName .= ' - ' . $booking->otRoom->name;
                                }
                                if ($booking->bookable) {
                                    $serviceName .= ' (' . $booking->bookable->name . ')';
                                }
                            }

                            $serviceDetails = 'Date: ' . $booking->booking_date . ' | Time: ' . $booking->booking_time;
                            if ($booking->end_time) {
                                $serviceDetails .= ' - ' . $booking->end_time;
                            }

                            $bookingLines[] = [
                                'booking_id' => $booking->id,
                                'service_name' => $serviceName,
                                'service_details' => $serviceDetails,
                                'booking_date' => $booking->booking_date,
                                'booking_time' => $booking->booking_time,
                                'end_time' => $booking->end_time,
                                'service_fee' => $booking->service_fee,
                                'booking_type' => $booking->booking_type,
                                'booking_status' => $booking->status
                            ];
                        }

                        Log::info('Bookings found', [
                            'patient_id' => $patientId,
                            'bookings_count' => $pendingBookings->count(),
                            'booking_lines' => $bookingLines
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error querying bookings table', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            $hasPendingServices = count($consultationLines) > 0 || count($bookingLines) > 0;
            $totalServices = count($consultationLines) + count($bookingLines);

            $message = $hasPendingServices
                ? "Found {$totalServices} pending service(s) for this patient"
                : 'Patient selected successfully';

            Log::info('checkPatientAppointments completed', [
                'patient_id' => $patientId,
                'has_pending_services' => $hasPendingServices,
                'consultation_lines_count' => count($consultationLines),
                'booking_lines_count' => count($bookingLines),
                'total_services' => $totalServices
            ]);

            return response()->json([
                'success' => true,
                'has_pending_appointments' => count($consultationLines) > 0,
                'has_pending_bookings' => count($bookingLines) > 0,
                'has_pending_services' => $hasPendingServices,
                'consultation_lines' => $consultationLines,
                'booking_lines' => $bookingLines,
                'message' => $message
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in checkPatientAppointments', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid patient selected',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in checkPatientAppointments', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check patient services: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Care Of commission details
     */
    public function getCareOfCommission(Request $request)
    {
        try {
            $request->validate([
                'care_of_id' => 'required|exists:care_ofs,id'
            ]);

            $user = Auth::user();
            $businessId = $user->business_id;

            $careOf = CareOf::where('id', $request->care_of_id)
                ->where('business_id', $businessId)
                ->where('status', 'active')
                ->first();

            if (!$careOf) {
                return response()->json([
                    'success' => false,
                    'message' => 'Care Of not found or inactive'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'care_of' => [
                    'id' => $careOf->id,
                    'name' => $careOf->name,
                    'commission_type' => $careOf->commission_type,
                    'commission_rate' => $careOf->commission_rate,
                    'fixed_commission_amount' => $careOf->fixed_commission_amount,
                    'commission_amount' => $careOf->fixed_commission_amount
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting Care Of commission', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get commission details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new customer/patient from invoice section with automatic ledger creation
     */
    public function createCustomer(Request $request)
    {
        try {
            Log::info('Customer creation started from invoice', [
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            // Validate the request
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'phone' => 'required|string|max:20',
                'gender' => 'required|in:male,female,other',
                'age' => 'required|integer|min:1|max:150',
                'date_of_birth' => 'required|date|before:today',
                'address' => 'nullable|string|max:500'
            ]);

            $user = Auth::user();
            $businessId = $user->business_id;

            if (!$businessId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. No business association found.'
                ], 403);
            }

            // Check if phone number already exists for this business
            $existingPatient = Patient::where('phone', $request->phone)
                ->where('business_id', $businessId)
                ->first();

            if ($existingPatient) {
                return response()->json([
                    'success' => false,
                    'message' => 'A patient with this phone number already exists.'
                ], 422);
            }

            DB::beginTransaction();

            // Create the patient
            $patientData = [
                'business_id' => $businessId,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address ?? 'Not provided',
                'city' => null,
                'state' => null,
                'postal_code' => null,
                'country' => 'Bangladesh',
                'is_active' => true
            ];

            $patient = Patient::create($patientData);

            // Create automatic ledger for the customer
            $ledgerData = [
                'name' => $patient->full_name . ' (Patient)',
                'business_id' => $businessId,
                'ledger_type' => 'Sundry Debtors (Customer)',
                'balance_type' => 'Dr', // Debit balance for customers
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'contact' => $patient->phone,
                'location' => $patient->address,
                'status' => 'active'
            ];

            $ledger = Ledger::create($ledgerData);

            DB::commit();

            Log::info('Customer and ledger created successfully', [
                'patient_id' => $patient->id,
                'ledger_id' => $ledger->id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Patient created successfully with automatic ledger.',
                'patient' => [
                    'id' => $patient->id,
                    'patient_id' => $patient->patient_id,
                    'full_name' => $patient->full_name,
                    'phone' => $patient->phone,
                    'gender' => $patient->gender,
                    'age' => $patient->age
                ],
                'ledger' => [
                    'id' => $ledger->id,
                    'name' => $ledger->name,
                    'ledger_type' => $ledger->ledger_type
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error in customer creation', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating customer from invoice', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create patient: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        // Log the incoming request
        Log::info('Medical invoice creation started', [
            'user_id' => Auth::id(),
            'doctor_id_from_request' => $request->doctor_id,
            'request_data' => $request->all(),
            'timestamp' => now()
        ]);

        try {
            // Enhanced validation - removed commission from test_lines validation
            Log::info('Starting validation for medical invoice');
            $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'care_of_id' => 'nullable|exists:care_ofs,id',
                'doctor_id' => 'nullable|exists:doctors,id',
                'invoice_date' => 'required|date',
                'payment_method' => 'required|in:cash,credit',
                'test_lines' => 'required|array|min:1',
                'test_lines.*.service_type' => 'required|in:lab_test,consultation,booking', // Removed commission
                // For lab tests
                'test_lines.*.lab_test_id' => 'required_if:test_lines.*.service_type,lab_test|nullable|exists:lab_tests,id',
                // For consultations
                'test_lines.*.appointment_id' => 'required_if:test_lines.*.service_type,consultation|nullable|exists:appointments,id',
                // For bookings
                'test_lines.*.booking_id' => 'required_if:test_lines.*.service_type,booking|nullable|exists:bookings,id',
                'test_lines.*.quantity' => 'required|numeric|min:1',
                'test_lines.*.unit_price' => 'required|numeric|min:0',
                'test_lines.*.service_name' => 'required|string',
                'business_id' => 'nullable|exists:businesses,id',
                'commission_amount' => 'nullable|numeric|min:0', // Commission sent separately
                'add_commission_to_ledger' => 'nullable|boolean', // Option to add commission to ledger
            ]);
            Log::info('Validation passed for medical invoice');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed for medical invoice', [
                'user_id' => Auth::id(),
                'validation_errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        }

        DB::beginTransaction();

        try {
            // Get or set business_id
            $user = Auth::user();
            $businessId = $request->business_id ?? $user->business_id;
            if (!$businessId) {
                Log::info('No business_id provided, searching for active business');
                $business = \App\Models\Business::where('is_active', true)->first();
                if (!$business) {
                    Log::warning('No active business found, creating default business');
                    $business = \App\Models\Business::create([
                        'hospital_name' => 'Default Medical Center',
                        'address' => 'Healthcare Address',
                        'contact_number' => '01234567890',
                        'email' => 'info@medicalcenter.com',
                        'is_active' => true
                    ]);
                    Log::info('Default business created', ['business_id' => $business->id]);
                } else {
                    Log::info('Found active business', ['business_id' => $business->id]);
                }
                $businessId = $business->id;
            }
            Log::info('Business ID set', ['business_id' => $businessId]);

            // Generate invoice number with race condition protection
            Log::info('Generating invoice number');
            $invoiceNumber = $this->generateUniqueInvoiceNumber();
            Log::info('Invoice number generated', ['invoice_number' => $invoiceNumber]);

            // Generate lab ID with race condition protection
            Log::info('Generating lab ID');
            $labId = $this->generateUniqueLabId();
            Log::info('Lab ID generated', ['lab_id' => $labId]);

            // Calculate totals from SERVICE LINES ONLY (no commission)
            Log::info('Calculating totals', ['lines_count' => count($request->test_lines)]);
            $subtotal = 0;
            $lineCalculations = [];
            $consultationAppointmentIds = [];
            $bookingIds = [];
            $careOfId = $request->care_of_id;

            // Get commission amount from request (calculated on frontend)
            $commissionAmount = $request->commission_amount ?? 0;

            foreach ($request->test_lines as $index => $line) {
                // Only process service lines (lab_test, consultation, booking)
                if (!in_array($line['service_type'], ['lab_test', 'consultation', 'booking'])) {
                    Log::warning('Skipping non-service line', ['service_type' => $line['service_type']]);
                    continue;
                }

                $lineTotal = $line['quantity'] * $line['unit_price'];
                $lineDiscount = $line['line_discount'] ?? 0;
                $lineSubtotal = $lineTotal - $lineDiscount;
                $subtotal += $lineSubtotal; // Only service lines, no commission

                // Track different service types for status updates
                if ($line['service_type'] === 'consultation' && isset($line['appointment_id'])) {
                    $consultationAppointmentIds[] = $line['appointment_id'];
                }

                if ($line['service_type'] === 'booking' && isset($line['booking_id'])) {
                    $bookingIds[] = $line['booking_id'];
                }

                $lineCalculations[] = [
                    'line_index' => $index,
                    'service_type' => $line['service_type'],
                    'service_name' => $line['service_name'],
                    'lab_test_id' => $line['lab_test_id'] ?? null,
                    'appointment_id' => $line['appointment_id'] ?? null,
                    'booking_id' => $line['booking_id'] ?? null,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'line_total' => $lineTotal,
                    'line_discount' => $lineDiscount,
                    'line_subtotal' => $lineSubtotal
                ];
            }

            $discount = $request->discount ?? 0;
            $roundOff = $request->round_off ?? 0;

            // Customer grand total (NO commission)
            $grandTotal = ($subtotal - $discount) + $roundOff;

            Log::info('Totals calculated', [
                'subtotal' => $subtotal,
                'discount' => $discount,
                'round_off' => $roundOff,
                'customer_grand_total' => $grandTotal, // What customer pays
                'commission_amount' => $commissionAmount, // Internal cost
                'consultation_appointments' => $consultationAppointmentIds,
                'booking_ids' => $bookingIds,
                'care_of_id' => $careOfId,
                'line_calculations' => $lineCalculations
            ]);

            // Create invoice with customer total only
            Log::info('Creating medical invoice record');
            $invoiceData = [
                'business_id' => $businessId,
                'invoice_number' => $invoiceNumber,
                'lab_id' => $labId,
                'patient_id' => $request->patient_id,
                'care_of_id' => $careOfId,
                'doctor_id' => $request->doctor_id,
                'invoice_date' => $request->invoice_date,
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'grand_total' => $grandTotal, // Customer amount only
                'paid_amount' => 0,
                'status' => 'pending',
                'notes' => $request->notes ?? null,
                'created_by' => Auth::id(),
            ];

            Log::info('Invoice data prepared', ['invoice_data' => $invoiceData]);

            // Create invoice with retry mechanism for duplicate key errors
            $invoice = $this->createInvoiceWithRetry($invoiceData);

            Log::info('Medical invoice created successfully', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number
            ]);

            // Create invoice lines (only service lines, no commission lines)
            Log::info('Creating invoice lines', ['lines_count' => count($lineCalculations)]);
            foreach ($lineCalculations as $index => $calculation) {
                $lineData = [
                    'service_type' => $calculation['service_type'],
                    'service_name' => $calculation['service_name'],
                    'lab_test_id' => $calculation['service_type'] === 'lab_test' ? $calculation['lab_test_id'] : null,
                    'appointment_id' => $calculation['service_type'] === 'consultation' ? $calculation['appointment_id'] : null,
                    'booking_id' => $calculation['service_type'] === 'booking' ? $calculation['booking_id'] : null,
                    'quantity' => $calculation['quantity'],
                    'unit_price' => $calculation['unit_price'],
                    'line_discount' => $calculation['line_discount'],
                    'line_total' => $calculation['line_subtotal'],
                ];

                // Add service-specific notes
                $notes = [];
                if ($calculation['service_type'] === 'consultation' && isset($calculation['appointment_id'])) {
                    $notes[] = 'Consultation - Appointment ID: ' . $calculation['appointment_id'];
                } elseif ($calculation['service_type'] === 'booking' && isset($calculation['booking_id'])) {
                    $notes[] = 'Booking - Booking ID: ' . $calculation['booking_id'];
                }

                if (!empty($notes)) {
                    $lineData['notes'] = implode(' | ', $notes);
                }

                Log::info("Creating invoice line {$index}", ['line_data' => $lineData]);
                $invoiceLine = $invoice->lines()->create($lineData);
                Log::info("Invoice line {$index} created", ['line_id' => $invoiceLine->id]);
            }

            // Update appointment payment status
            if (!empty($consultationAppointmentIds)) {
                Log::info('Updating appointment payment status', ['appointment_ids' => $consultationAppointmentIds]);
                try {
                    \App\Models\Appointment::whereIn('id', $consultationAppointmentIds)
                        ->update(['payment_status' => 'paid']);
                    Log::info('Appointment payment status updated successfully', [
                        'updated_appointments' => count($consultationAppointmentIds)
                    ]);
                } catch (\Exception $appointmentError) {
                    Log::error('Failed to update appointment payment status', [
                        'error' => $appointmentError->getMessage(),
                        'appointment_ids' => $consultationAppointmentIds
                    ]);
                }
            }

            // Update booking status and confirmed_at timestamp
            if (!empty($bookingIds)) {
                Log::info('Updating booking status', ['booking_ids' => $bookingIds]);
                try {
                    \App\Models\Booking::whereIn('id', $bookingIds)
                        ->update([
                            'status' => 'confirmed',
                            'confirmed_at' => now()
                        ]);
                    Log::info('Booking status updated successfully', [
                        'updated_bookings' => count($bookingIds)
                    ]);
                } catch (\Exception $bookingError) {
                    Log::error('Failed to update booking status', [
                        'error' => $bookingError->getMessage(),
                        'booking_ids' => $bookingIds
                    ]);
                }
            }

            // Create accounting entries (commission handled separately)
            $addCommissionToLedger = $request->add_commission_to_ledger ?? true;
            $this->createAccountingEntries($invoice, $careOfId, $commissionAmount, $grandTotal, $businessId, $addCommissionToLedger);

            Log::info('All invoice lines created successfully');
            DB::commit();

            Log::info('Medical invoice creation completed successfully', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'user_id' => Auth::id(),
                'customer_total' => $grandTotal,
                'commission_amount' => $commissionAmount,
                'consultation_appointments_updated' => count($consultationAppointmentIds),
                'bookings_updated' => count($bookingIds),
                'accounting_entries_created' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Medical invoice created successfully',
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'redirect_url' => route('admin.medical.invoices.show', $invoice->id)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create medical invoice', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString(),
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice: ' . $e->getMessage(),
                'error_details' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }


    /**
     * Get or create a ledger
     */
    private function getOrCreateLedger($name, $ledgerType, $businessId)
    {
        return Ledger::firstOrCreate(
            [
                'name' => $name,
                'business_id' => $businessId,
                'ledger_type' => $ledgerType
            ],
            [
                'current_balance' => 0.00,
                'balance_type' => 'Cr',
                'status' => 'active',
                'opening_balance' => 0.00
            ]
        );
    }

    /**
     * Get or create customer ledger for patient
     */
    private function getOrCreateCustomerLedger($patient, $businessId)
    {
        $ledgerName = "Patient - {$patient->full_name} ({$patient->patient_id})";

        return Ledger::firstOrCreate(
            [
                'name' => $ledgerName,
                'business_id' => $businessId,
                'ledger_type' => 'Sundry Debtors (Customer)'
            ],
            [
                'current_balance' => 0.00,
                'balance_type' => 'Dr',
                'status' => 'active',
                'opening_balance' => 0.00,
                'contact' => $patient->phone ?? null,
                'location' => $patient->address ?? null
            ]
        );
    }

    /**
     * Get or create Cash-in-Hand ledger
     */
    private function getOrCreateCashInHandLedger($businessId)
    {
        // First, try to find an active Cash-in-Hand ledger
        $cashLedger = Ledger::where('business_id', $businessId)
            ->where('ledger_type', 'Cash-in-Hand')
            ->where('status', 'active')
            ->first();

        if ($cashLedger) {
            return $cashLedger;
        }

        // If no active ledger found, look for default status and activate it
        $defaultCashLedger = Ledger::where('business_id', $businessId)
            ->where('ledger_type', 'Cash-in-Hand')
            ->where('status', 'default')
            ->first();

        if ($defaultCashLedger) {
            $defaultCashLedger->update(['status' => 'active']);
            return $defaultCashLedger;
        }

        // If no ledger exists, create a new one with default status first, then activate
        $cashLedger = Ledger::create([
            'name' => 'Cash A/c',
            'business_id' => $businessId,
            'ledger_type' => 'Cash-in-Hand',
            'current_balance' => 0.00,
            'balance_type' => 'Dr',
            'status' => 'default',
            'opening_balance' => 0.00
        ]);

        // Immediately activate it
        $cashLedger->update(['status' => 'active']);

        return $cashLedger;
    }

    /**
     * Get or create commission ledger for care of
     */
    private function getOrCreateCommissionLedger($careOf, $businessId)
    {
        try {
            Log::info('Creating/finding commission ledger', [
                'care_of_id' => $careOf->id,
                'care_of_name' => $careOf->name,
                'business_id' => $businessId,
                'existing_ledger_id' => $careOf->ledger_id
            ]);

            // Check if care of already has a ledger
            if ($careOf->ledger_id) {
                $existingLedger = Ledger::find($careOf->ledger_id);
                if ($existingLedger && $existingLedger->business_id === $businessId) {
                    Log::info('Using existing ledger', [
                        'ledger_id' => $existingLedger->id,
                        'ledger_name' => $existingLedger->name
                    ]);
                    return $existingLedger;
                }
            }

            $ledgerName = "Commission - {$careOf->name}";

            $ledger = Ledger::firstOrCreate(
                [
                    'name' => $ledgerName,
                    'business_id' => $businessId,
                    'ledger_type' => 'commission agent'
                ],
                [
                    'current_balance' => 0.00,
                    'balance_type' => 'Dr',
                    'status' => 'active',
                    'opening_balance' => 0.00,
                    'contact' => $careOf->phone_number ?? null,
                    'location' => $careOf->address ?? null
                ]
            );

            Log::info('Commission ledger created/found', [
                'ledger_id' => $ledger->id,
                'ledger_name' => $ledger->name,
                'was_created' => $ledger->wasRecentlyCreated
            ]);

            // Update care of with ledger reference
            if (!$careOf->ledger_id) {
                $careOf->update(['ledger_id' => $ledger->id]);
                Log::info('Updated care of with ledger reference', [
                    'care_of_id' => $careOf->id,
                    'ledger_id' => $ledger->id
                ]);
            }

            return $ledger;
        } catch (\Exception $e) {
            Log::error('Failed to create commission ledger', [
                'care_of_id' => $careOf->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }


    /**
     * Recalculate ledger balance (updated to use Dr/Cr)
     */
    private function recalcLedgerBalance(Ledger $ledger): void
    {
        $drLedgers = [
            'Bank Accounts',
            'Cash-in-Hand',
            'Expenses',
            'Fixed Assets',
            'Investments',
            'Loans & Advances (Asset)',
            'Purchase Accounts',
            'Sundry Debtors (Customer)',
            'commission agent'
        ];

        // Start with opening balance
        $currentBalance = $ledger->opening_balance ?? 0;

        // Get all transaction lines for this ledger
        $transactionLines = TransactionLine::where('ledger_id', $ledger->id)->get();

        // Calculate running balance based on transaction lines
        foreach ($transactionLines as $line) {
            if (in_array($ledger->ledger_type, $drLedgers)) {
                $currentBalance += $line->debit_amount;
                $currentBalance -= $line->credit_amount;
            } else {
                $currentBalance -= $line->debit_amount;
                $currentBalance += $line->credit_amount;
            }
        }

        $ledger->current_balance = $currentBalance;
        $ledger->save();

        Log::debug('Ledger balance recalculated', [
            'ledger_id' => $ledger->id,
            'ledger_name' => $ledger->name,
            'ledger_type' => $ledger->ledger_type,
            'opening_balance' => $ledger->opening_balance,
            'current_balance' => $currentBalance
        ]);
    }
    /**
     * Create accounting entries for the medical invoice (updated for single commission entry)
     */
    private function createAccountingEntries($invoice, $careOfId, $commissionAmount, $grandTotal, $businessId, $addCommissionToLedger = true)
    {
        try {
            Log::info('Creating accounting entries', [
                'invoice_id' => $invoice->id,
                'care_of_id' => $careOfId,
                'commission_amount' => $commissionAmount,
                'grand_total' => $grandTotal,
                'business_id' => $businessId
            ]);

            // Get or create required ledgers
            $salesLedger = $this->getOrCreateLedger('Sales Account', 'Sales Accounts', $businessId);
            $customerLedger = $this->getOrCreateCustomerLedger($invoice->patient, $businessId);

            Log::info('Ledgers created/found', [
                'sales_ledger_id' => $salesLedger->id,
                'customer_ledger_id' => $customerLedger->id
            ]);

            // Create transaction for customer billing (WITHOUT commission)
            $customerTransaction = Transaction::create([
                'business_id' => $businessId,
                'transaction_type' => 'Journal',
                'transaction_date' => $invoice->invoice_date,
                'amount' => $grandTotal, // Customer amount only (no commission)
                'narration' => "Medical Invoice #{$invoice->invoice_number} - Patient: {$invoice->patient->full_name}"
            ]);

            Log::info('Customer transaction created', ['transaction_id' => $customerTransaction->id]);

            // DEBIT: Customer/Patient ledger with invoice amount (no commission)
            TransactionLine::create([
                'transaction_id' => $customerTransaction->id,
                'ledger_id' => $customerLedger->id,
                'debit_amount' => $grandTotal,
                'credit_amount' => 0,
                'narration' => "Medical services - Invoice #{$invoice->invoice_number}"
            ]);

            // CREDIT: Sales ledger with invoice amount (no commission)
            TransactionLine::create([
                'transaction_id' => $customerTransaction->id,
                'ledger_id' => $salesLedger->id,
                'debit_amount' => 0,
                'credit_amount' => $grandTotal,
                'narration' => "Medical services revenue - Invoice #{$invoice->invoice_number}"
            ]);

            Log::info('Customer transaction lines created successfully');

            // Create SINGLE commission entry if applicable (DEBIT ONLY)
            Log::info('Checking commission conditions', [
                'care_of_id' => $careOfId,
                'commission_amount' => $commissionAmount,
                'commission_amount_greater_than_zero' => $commissionAmount > 0,
                'add_commission_to_ledger' => $addCommissionToLedger
            ]);

            if ($careOfId && $commissionAmount > 0 && $addCommissionToLedger) {
                Log::info('Commission conditions met, creating commission entry');

                $careOf = CareOf::find($careOfId);

                if (!$careOf) {
                    Log::error('Care Of not found', ['care_of_id' => $careOfId]);
                    throw new \Exception("Care Of with ID {$careOfId} not found");
                }

                Log::info('Care Of found', [
                    'care_of_id' => $careOf->id,
                    'care_of_name' => $careOf->name
                ]);

                $commissionLedger = $this->getOrCreateCommissionLedger($careOf, $businessId);

                Log::info('Commission ledger created/found', [
                    'commission_ledger_id' => $commissionLedger->id,
                    'commission_ledger_name' => $commissionLedger->name
                ]);

                // Create commission transaction (single entry)
                $commissionTransaction = Transaction::create([
                    'business_id' => $businessId,
                    'transaction_type' => 'Journal',
                    'transaction_date' => $invoice->invoice_date,
                    'amount' => $commissionAmount,
                    'narration' => "Commission payment - Invoice #{$invoice->invoice_number} - Care Of: {$careOf->name}"
                ]);

                Log::info('Commission transaction created', [
                    'commission_transaction_id' => $commissionTransaction->id,
                    'commission_amount' => $commissionAmount
                ]);

                // SINGLE ENTRY: DEBIT commission agent ledger only
                $commissionTransactionLine = TransactionLine::create([
                    'transaction_id' => $commissionTransaction->id,
                    'ledger_id' => $commissionLedger->id,
                    'debit_amount' => $commissionAmount, // Amount owed to commission agent
                    'credit_amount' => 0,
                    'narration' => "Commission earned - Invoice #{$invoice->invoice_number}"
                ]);

                Log::info('Commission transaction line created (DEBIT ONLY)', [
                    'transaction_line_id' => $commissionTransactionLine->id,
                    'ledger_id' => $commissionLedger->id,
                    'debit_amount' => $commissionAmount
                ]);

                // Recalculate commission agent ledger balance
                $this->recalcLedgerBalance($commissionLedger);

                Log::info('Commission entry completed successfully', [
                    'commission_transaction_id' => $commissionTransaction->id,
                    'commission_agent' => $careOf->name,
                    'commission_amount' => $commissionAmount,
                    'entry_type' => 'SINGLE_DEBIT',
                    'ledger_balance_after' => $commissionLedger->current_balance
                ]);
            } else {
                $reason = !$careOfId ? 'No care_of_id provided' : ($commissionAmount <= 0 ? 'Commission amount is zero or negative' :
                    'Commission ledger entry disabled by user');

                Log::info('Commission entry skipped', [
                    'reason' => $reason,
                    'care_of_id' => $careOfId,
                    'commission_amount' => $commissionAmount,
                    'add_commission_to_ledger' => $addCommissionToLedger
                ]);
            }

            // Recalculate customer and sales ledger balances
            $this->recalcLedgerBalance($customerLedger);
            $this->recalcLedgerBalance($salesLedger);

            Log::info('All accounting entries created successfully', [
                'customer_transaction_id' => $customerTransaction->id,
                'commission_transaction_id' => isset($commissionTransaction) ? $commissionTransaction->id : null,
                'customer_amount' => $grandTotal,
                'commission_amount' => $commissionAmount,
                'commission_created' => isset($commissionTransaction)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create accounting entries', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'care_of_id' => $careOfId,
                'commission_amount' => $commissionAmount
            ]);
            throw $e;
        }
    }



    public function show(MedicalInvoice $invoice)
    {
        try {
            // Load relationships safely - use visibleLines to exclude commission
            $invoice->load([
                'patient',
                'business',
                'careOf',
                'doctor',
                'visibleLines.labTest', // Use visibleLines instead of lines
                'visibleLines.appointment',
                'visibleLines.booking.bookable',
                'visibleLines.booking.otRoom',
                'createdBy',
            ]);

            // Fallback: load patient even if not active
            if (!$invoice->patient && $invoice->patient_id) {
                $invoice->patient = \App\Models\Patient::find($invoice->patient_id);
            }

            // Fallback: load doctor even if not active (with soft deletes)
            if (!$invoice->doctor && $invoice->doctor_id) {
                $invoice->doctor = \App\Models\Doctor::find($invoice->doctor_id);
            }

            // Fallback: load patient even if not active
            if (!$invoice->patient && $invoice->patient_id) {
                $invoice->patient = \App\Models\Patient::find($invoice->patient_id);
            }

            // Fallback: load doctor even if not active (with soft deletes)
            if (!$invoice->doctor && $invoice->doctor_id) {
                $invoice->doctor = \App\Models\Doctor::find($invoice->doctor_id);
            }

            // Fallback: load doctor even if not active (with soft deletes)
            if (!$invoice->doctor && $invoice->doctor_id) {
                $invoice->doctor = \App\Models\Doctor::find($invoice->doctor_id);
            }

            // Get business/hospital information using the model method
            $hospital = $invoice->getHospitalInfo();

            // Check if letterhead is enabled and get active letterhead for invoices
            $letterheadEnabled = filter_var(Setting::get('letterhead_enabled', false), FILTER_VALIDATE_BOOLEAN);
            $letterhead = null;
            if ($letterheadEnabled && $invoice->business_id) {
                $letterhead = \App\Models\Letterhead::getActiveForBusiness($invoice->business_id, 'Invoice');
            }

            // Check if letterhead is enabled and get active letterhead for invoices
            $letterheadEnabled = filter_var(Setting::get('letterhead_enabled', false), FILTER_VALIDATE_BOOLEAN);
            $letterhead = null;
            if ($letterheadEnabled && $invoice->business_id) {
                $letterhead = \App\Models\Letterhead::getActiveForBusiness($invoice->business_id, 'Invoice');
            }

            // Check if letterhead is enabled and get active letterhead for invoices
            $letterheadEnabled = filter_var(Setting::get('letterhead_enabled', false), FILTER_VALIDATE_BOOLEAN);
            $letterhead = null;
            if ($letterheadEnabled && $invoice->business_id) {
                $letterhead = \App\Models\Letterhead::getActiveForBusiness($invoice->business_id, 'Invoice');
            }

            // Get doctor information safely
            $doctor = null;

            // Always try to load doctor if doctor_id exists
            if ($invoice->doctor_id) {
                if (!$invoice->doctor) {
                    $invoice->doctor = \App\Models\Doctor::withTrashed()->find($invoice->doctor_id);
                }
                // If doctor is loaded, use it
                if ($invoice->doctor) {
                    $doctor = $invoice->doctor;
                } else {
                    // Doctor not found, fall back to appointment doctor or createdBy
                    $doctorFromAppointment = $invoice->getDoctorFromAppointments();
                    if ($doctorFromAppointment) {
                        $doctor = $doctorFromAppointment;
                    } elseif ($invoice->createdBy) {
                        $doctor = (object) [
                            'name' => $invoice->createdBy->name,
                            'specialization' => null,
                            'license_number' => null,
                        ];
                    }
                }
            } else {
                // No doctor_id, fall back to appointment doctor or createdBy
                $doctorFromAppointment = $invoice->getDoctorFromAppointments();
                if ($doctorFromAppointment) {
                    $doctor = $doctorFromAppointment;
                } elseif ($invoice->createdBy) {
                    $doctor = (object) [
                        'name' => $invoice->createdBy->name,
                        'specialization' => null,
                        'license_number' => null,
                    ];
                }
            }

            // Transform visible lines to items format for the template (excluding commission)
            $items = collect();
            if ($invoice->visibleLines && $invoice->visibleLines->count() > 0) {
                $items = $invoice->visibleLines->map(function ($line) {
                    return (object) [
                        'test' => (object) [
                            'name' => $line->service_display_name,
                            'test_code' => $line->labTest->test_code ?? 'SVC',
                            'description' => $line->labTest->description ?? $line->service_name ?? 'Medical Service',
                            'department' => $line->labTest->department ?? 'General',
                            'sample_type' => $line->labTest->sample_type ?? 'N/A',
                        ],
                        'quantity' => $line->quantity ?? 1,
                        'unit_price' => $line->unit_price ?? 0,
                        'line_discount' => $line->line_discount ?? 0,
                        'total_price' => $line->line_total ?? 0,
                        'notes' => $line->notes ?? null,
                    ];
                });
            }

            // Calculate financial totals using visible lines only
            $subtotal = $invoice->display_subtotal; // Use display_subtotal which excludes commission
            $discountAmount = $invoice->discount ?? 0;
            $taxAmount = $invoice->tax_amount ?? 0;
            $grandTotal = $invoice->grand_total ?? 0;
            $paidAmount = $invoice->paid_amount ?? 0;
            $remainingAmount = $grandTotal - $paidAmount;

            return view('admin.medical.invoices.show', compact(
                'invoice',
                'hospital',
                'doctor',
                'items',
                'subtotal',
                'discountAmount',
                'taxAmount',
                'grandTotal',
                'paidAmount',
                'remainingAmount'
            ));
        } catch (\Exception $e) {
            // Log the error and return with minimal data
            Log::error('Error showing medical invoice: ' . $e->getMessage(), [
                'invoice_id' => $invoice->id,
                'error' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.medical.invoices.index')
                ->with('error', 'Unable to load invoice details.');
        }
    }


    public function update(Request $request, MedicalInvoice $invoice)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'care_of_id' => 'nullable|exists:care_ofs,id',
            'invoice_date' => 'required|date',
            'payment_method' => 'required|in:cash,credit',
            'lines' => 'required|array|min:1',
            'lines.*.service_type' => 'required|in:lab_test,consultation,booking,commission',
            'lines.*.lab_test_id' => 'required_if:lines.*.service_type,lab_test|nullable|exists:lab_tests,id',
            'lines.*.appointment_id' => 'required_if:lines.*.service_type,consultation|nullable|exists:appointments,id',
            'lines.*.booking_id' => 'required_if:lines.*.service_type,booking|nullable|exists:bookings,id',
            'lines.*.quantity' => 'required|numeric|min:1',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.service_name' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // Calculate totals
            $subtotal = 0;
            foreach ($request->lines as $line) {
                $subtotal += $line['quantity'] * $line['unit_price'];
            }

            $discount = $request->discount ?? 0;
            $grandTotal = $subtotal - $discount;

            // Update invoice
            $invoice->update([
                'patient_id' => $request->patient_id,
                'care_of_id' => $request->care_of_id,
                'invoice_date' => $request->invoice_date,
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'grand_total' => $grandTotal,
            ]);

            // Delete existing lines and create new ones
            $invoice->lines()->delete();
            foreach ($request->lines as $line) {
                $invoice->lines()->create([
                    'service_type' => $line['service_type'],
                    'service_name' => $line['service_name'],
                    'lab_test_id' => $line['service_type'] === 'lab_test' ? $line['lab_test_id'] : null,
                    'appointment_id' => $line['service_type'] === 'consultation' ? $line['appointment_id'] : null,
                    'booking_id' => $line['service_type'] === 'booking' ? $line['booking_id'] : null,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'line_discount' => $line['line_discount'] ?? 0,
                    'line_total' => ($line['quantity'] * $line['unit_price']) - ($line['line_discount'] ?? 0),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Medical invoice updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(MedicalInvoice $invoice)
    {
        try {
            DB::beginTransaction();

            // Delete invoice lines first
            $invoice->lines()->delete();

            // Delete the invoice
            $invoice->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Medical invoice deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    public function collectPayment(Request $request, MedicalInvoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|max:50',
            'payment_notes' => 'nullable|string|max:500'
        ]);

        $amount = $request->amount;
        $remainingAmount = $invoice->grand_total - $invoice->paid_amount;

        // Validate amount
        if ($amount > $remainingAmount) {
            return response()->json([
                'success' => false,
                'message' => "Amount cannot exceed remaining balance of ৳" . number_format($remainingAmount, 2)
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $businessId = $user->business_id;

            // Get or create Cash-in-Hand ledger
            $cashLedger = $this->getOrCreateCashInHandLedger($businessId);

            // Get patient's customer ledger
            $customerLedger = $this->getOrCreateCustomerLedger($invoice->patient, $businessId);

            // Create accounting transaction for payment collection
            $transaction = Transaction::create([
                'business_id' => $businessId,
                'transaction_type' => 'Receipt',
                'transaction_date' => now()->toDateString(),
                'amount' => $amount,
                'narration' => "Payment collection - Invoice #{$invoice->invoice_number}"
            ]);

            // Create transaction lines
            // Debit Cash-in-Hand (money received)
            TransactionLine::create([
                'transaction_id' => $transaction->id,
                'ledger_id' => $cashLedger->id,
                'debit_amount' => $amount,
                'credit_amount' => 0,
                'narration' => "Payment received - Invoice #{$invoice->invoice_number}"
            ]);

            // Credit Customer ledger (payment received from customer)
            TransactionLine::create([
                'transaction_id' => $transaction->id,
                'ledger_id' => $customerLedger->id,
                'debit_amount' => 0,
                'credit_amount' => $amount,
                'narration' => "Payment received - Invoice #{$invoice->invoice_number}"
            ]);

            // Recalculate ledger balances
            $this->recalcLedgerBalance($cashLedger);
            $this->recalcLedgerBalance($customerLedger);

            // Update paid amount
            $newPaidAmount = $invoice->paid_amount + $amount;

            $updateData = [
                'paid_amount' => $newPaidAmount,
                'status' => $newPaidAmount >= $invoice->grand_total ? 'paid' : 'pending',
                'payment_notes' => $request->payment_notes
            ];

            // Add payment method if provided
            if ($request->payment_method) {
                $updateData['payment_method'] = $request->payment_method;
            }

            $invoice->update($updateData);

            DB::commit();

            $newRemainingAmount = $invoice->grand_total - $newPaidAmount;
            $isFullyPaid = $newRemainingAmount <= 0;

            Log::info('Payment collected successfully', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'amount_collected' => $amount,
                'transaction_id' => $transaction->id,
                'cash_ledger_id' => $cashLedger->id,
                'customer_ledger_id' => $customerLedger->id,
                'new_paid_amount' => $newPaidAmount,
                'grand_total' => $invoice->grand_total,
                'is_fully_paid' => $isFullyPaid
            ]);

            $message = $isFullyPaid ?
                'Payment collected successfully. Invoice is now fully paid!' :
                'Payment collected successfully. Remaining: ৳' . number_format($newRemainingAmount, 2);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'paid_amount' => $newPaidAmount,
                    'grand_total' => $invoice->grand_total,
                    'remaining_amount' => $newRemainingAmount,
                    'status' => $invoice->status,
                    'is_fully_paid' => $isFullyPaid
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to collect payment', [
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to collect payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Apply discount to an invoice
     */
    public function applyDiscount(Request $request, MedicalInvoice $invoice)
    {
        try {
            Log::info('Apply discount request received', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'request_data' => $request->all()
            ]);

            // Validate request
            $request->validate([
                'discount_type' => 'required|in:percentage,fixed',
                'discount_value' => 'required|numeric|min:0',
                'discount_reason' => 'nullable|string|max:255'
            ]);

            $discountType = $request->discount_type;
            $discountValue = $request->discount_value;
            $discountReason = $request->discount_reason;

            // Calculate remaining amount (grand_total - paid_amount)
            $remainingAmount = $invoice->grand_total - $invoice->paid_amount;

            if ($remainingAmount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot apply discount to a fully paid invoice'
                ], 400);
            }

            // Calculate discount amount
            if ($discountType === 'percentage') {
                if ($discountValue > 100) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Percentage discount cannot exceed 100%'
                    ], 400);
                }
                $discountAmount = ($remainingAmount * $discountValue) / 100;
            } else {
                if ($discountValue > $remainingAmount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Fixed discount cannot exceed remaining amount'
                    ], 400);
                }
                $discountAmount = $discountValue;
            }

            DB::beginTransaction();

            // Update invoice with discount
            $newDiscountAmount = $invoice->discount + $discountAmount;
            $newGrandTotal = $invoice->grand_total - $discountAmount;
            $newRemainingAmount = $newGrandTotal - $invoice->paid_amount;

            $invoice->update([
                'discount' => $newDiscountAmount,
                'grand_total' => $newGrandTotal,
                'status' => $newRemainingAmount <= 0 ? 'paid' : 'pending'
            ]);

            // Log the discount application
            Log::info('Discount applied successfully', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_amount' => $discountAmount,
                'discount_reason' => $discountReason,
                'old_discount' => $invoice->discount - $discountAmount,
                'new_discount' => $newDiscountAmount,
                'old_grand_total' => $invoice->grand_total + $discountAmount,
                'new_grand_total' => $newGrandTotal,
                'new_remaining_amount' => $newRemainingAmount
            ]);

            DB::commit();

            $message = 'Discount applied successfully!';
            if ($newRemainingAmount <= 0) {
                $message .= ' Invoice is now fully paid.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'discount_applied' => $discountAmount,
                    'total_discount' => $newDiscountAmount,
                    'grand_total' => $newGrandTotal,
                    'remaining_amount' => $newRemainingAmount,
                    'status' => $invoice->status,
                    'is_fully_paid' => $newRemainingAmount <= 0
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in applyDiscount', [
                'invoice_id' => $invoice->id,
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid discount data',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to apply discount', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to apply discount: ' . $e->getMessage()
            ], 500);
        }
    }

    // /**
    //  * Apply discount to invoice without collecting payment
    //  */
    // public function applyDiscount(Request $request, MedicalInvoice $invoice)
    // {
    //     $request->validate([
    //         'discount_type' => 'required|in:percentage,fixed',
    //         'discount_value' => 'required|numeric|min:0.01',
    //         'discount_reason' => 'nullable|string|max:255'
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         $discountValue = $request->discount_value;
    //         $discountType = $request->discount_type;
    //         $remainingAmount = $invoice->grand_total - $invoice->paid_amount;

    //         // Calculate discount amount
    //         if ($discountType === 'percentage') {
    //             if ($discountValue > 100) {
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'Percentage discount cannot exceed 100%'
    //                 ], 422);
    //             }
    //             $discountAmount = ($remainingAmount * $discountValue) / 100;
    //         } else { // fixed amount
    //             if ($discountValue > $remainingAmount) {
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'Fixed discount cannot exceed remaining amount of ৳' . number_format($remainingAmount, 2)
    //                 ], 422);
    //             }
    //             $discountAmount = $discountValue;
    //         }

    //         // Round discount to 2 decimal places
    //         $discountAmount = round($discountAmount, 2);

    //         // Update invoice with discount
    //         $newDiscountAmount = $invoice->discount + $discountAmount;
    //         $newGrandTotal = $invoice->grand_total - $discountAmount;
    //         $newRemainingAmount = $newGrandTotal - $invoice->paid_amount;

    //         $invoice->update([
    //             'discount' => $newDiscountAmount,
    //             'grand_total' => $newGrandTotal,
    //             'status' => $invoice->paid_amount >= $newGrandTotal ? 'paid' : 'pending'
    //         ]);

    //         DB::commit();

    //         Log::info('Discount applied successfully', [
    //             'invoice_id' => $invoice->id,
    //             'invoice_number' => $invoice->invoice_number,
    //             'discount_type' => $discountType,
    //             'discount_value' => $discountValue,
    //             'discount_amount' => $discountAmount,
    //             'discount_reason' => $request->discount_reason,
    //             'new_discount_total' => $newDiscountAmount,
    //             'new_grand_total' => $newGrandTotal,
    //             'new_remaining_amount' => $newRemainingAmount
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Discount of ৳' . number_format($discountAmount, 2) . ' applied successfully. New remaining amount: ৳' . number_format($newRemainingAmount, 2),
    //             'data' => [
    //                 'discount_amount' => $newDiscountAmount,
    //                 'grand_total' => $newGrandTotal,
    //                 'remaining_amount' => $newRemainingAmount,
    //                 'status' => $invoice->status,
    //                 'discount_applied' => $discountAmount
    //             ]
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         Log::error('Failed to apply discount', [
    //             'invoice_id' => $invoice->id,
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to apply discount: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function updateStatus(Request $request, MedicalInvoice $invoice)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,cancelled'
        ]);

        try {
            $invoice->update([
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function print(MedicalInvoice $invoice)
    {
        try {
            // Check user permissions and invoice status
            $user = Auth::user();
            $isAdmin = ($user->role === 'admin'); // Replace with your actual role field or relationship
            $isFullyPaid = $invoice->status === 'paid' || ($invoice->paid_amount >= $invoice->grand_total);
            $printingUnpaidEnabled = filter_var(Setting::get('printing_invoice_unpaid', false), FILTER_VALIDATE_BOOLEAN);

            // Admin users can print any invoice without restrictions
            if ($isAdmin) {
                // Allow unlimited prints for admin users
            }
            // If printing unpaid setting is disabled, everyone can print all invoices
            elseif (!$printingUnpaidEnabled) {
                // Allow printing all invoices
            }
            // Non-admin users can print paid invoices without restrictions
            elseif ($isFullyPaid) {
                // Allow unlimited prints for paid invoices
            }
            // Non-admin users need to request access for unpaid invoices when setting is enabled
            else {
                $approvedRequest = $invoice->printRequests()->where('request_type', 'pos')->where('status', 'approved')->first();
                if (!$approvedRequest) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Print access required. Please request permission from an administrator.'
                    ], 403);
                }
                if ($approvedRequest->prints_used >= $approvedRequest->allowed_prints) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Print limit exceeded. You have used all ' . $approvedRequest->allowed_prints . ' allowed prints for this invoice. Please request additional access.'
                    ], 403);
                }
            }

            // Check if double copy print is enabled
            $doubleCopyPrint = Setting::get('double_copy_print', false);

            // Load relationships safely - use visibleLines to exclude commission
            $invoice->load([
                'patient',
                'business',
                'careOf',
                'doctor',
                'visibleLines.labTest', // Use visibleLines instead of lines
                'visibleLines.appointment',
                'visibleLines.booking.bookable',
                'visibleLines.booking.otRoom',
                'createdBy',
            ]);

            // Fallback: load patient even if not active
            if (!$invoice->patient && $invoice->patient_id) {
                $invoice->patient = \App\Models\Patient::find($invoice->patient_id);
            }

            // Fallback: load doctor even if not active (with soft deletes)
            if (!$invoice->doctor && $invoice->doctor_id) {
                $invoice->doctor = \App\Models\Doctor::withTrashed()->find($invoice->doctor_id);
            }

            // Get business/hospital information using the model method
            $hospital = $invoice->getHospitalInfo();

            // Check if letterhead is enabled and get active letterhead for invoices
            $letterheadEnabled = filter_var(Setting::get('letterhead_enabled', false), FILTER_VALIDATE_BOOLEAN);
            $letterhead = null;
            if ($letterheadEnabled && $invoice->business_id) {
                $letterhead = \App\Models\Letterhead::getActiveForBusiness($invoice->business_id, 'Invoice');
            }

            // Get doctor information safely
            $doctor = null;

            // First, check if doctor_id exists for this invoice and load that specific doctor
            if ($invoice->doctor_id) {
                $doctor = Doctor::find($invoice->doctor_id);
            }

            // Fallback: get doctor from appointments if no direct doctor_id
            if (!$doctor && ($doctorFromAppointment = $invoice->getDoctorFromAppointments())) {
                $doctor = $doctorFromAppointment;
            }

            // Final fallback: use created by user
            if (!$doctor && $invoice->createdBy) {
                $doctor = (object) [
                    'name' => $invoice->createdBy->name,
                    'specialization' => null,
                    'license_number' => null,
                ];
            }

            // Transform visible lines to items format for the print template (excluding commission)
            $items = collect();
            if ($invoice->visibleLines && $invoice->visibleLines->count() > 0) {
                $items = $invoice->visibleLines->map(function ($line) {
                    $item = (object) [
                        'service_type' => $line->service_type ?? 'lab_test',
                        'service_name' => $line->service_display_name,
                        'quantity' => $line->quantity ?? 1,
                        'unit_price' => $line->unit_price ?? 0,
                        'line_discount' => $line->line_discount ?? 0,
                        'total_price' => $line->line_total ?? 0,
                    ];

                    // Add service-specific details for display
                    if (($line->service_type === 'lab_test' || !$line->service_type) && $line->labTest) {
                        $item->test = (object) [
                            'name' => $line->labTest->test_name,
                            'test_code' => $line->labTest->test_code ?? null,
                            'description' => $line->labTest->description ?? 'Lab Test',
                            'department' => $line->labTest->department ?? null,
                            'sample_type' => $line->labTest->sample_type ?? null,
                        ];
                    } elseif ($line->service_type === 'consultation') {
                        $item->test = (object) [
                            'name' => $line->service_name ?? 'Consultation Service',
                            'test_code' => 'CONSULT',
                            'description' => 'Doctor Consultation Service',
                            'department' => 'Consultation',
                            'sample_type' => 'N/A',
                        ];

                        // Add consultation details
                        $serviceDetails = $line->service_details;
                        if ($serviceDetails) {
                            $item->appointment_date = $serviceDetails['appointment_date'] ?? null;
                            $item->appointment_time = $serviceDetails['appointment_time'] ?? null;
                            $item->doctor_name = $serviceDetails['doctor_name'] ?? 'Doctor';
                        }
                    } elseif ($line->service_type === 'booking') {
                        $bookingType = 'Service';
                        $testCode = 'BOOK';
                        $department = 'Booking Services';

                        if ($line->booking) {
                            $bookingType = ucfirst($line->booking->booking_type);
                            $testCode = $line->booking->booking_type === 'ot' ? 'OT-BOOK' : 'WARD-BOOK';
                            $department = $bookingType . ' Services';
                        }

                        $item->test = (object) [
                            'name' => $line->service_name ?? ($bookingType . ' Booking'),
                            'test_code' => $testCode,
                            'description' => $line->booking && $line->booking->booking_type === 'ot' ? 'Operation Theater Booking' : 'Ward Booking Service',
                            'department' => $department,
                            'sample_type' => 'N/A',
                        ];

                        // Add booking details
                        $serviceDetails = $line->service_details;
                        if ($serviceDetails) {
                            $item->booking_date = $serviceDetails['booking_date'] ?? null;
                            $item->booking_time = $serviceDetails['booking_time'] ?? null;
                            $item->booking_type = $serviceDetails['booking_type'] ?? null;
                        }

                        if ($line->booking) {
                            if ($line->booking->bookable) {
                                $item->bookable_name = $line->booking->bookable->name ?? 'Service';
                            }
                            if ($line->booking->otRoom) {
                                $item->ot_room_name = $line->booking->otRoom->name ?? 'OT Room';
                            }
                        }
                    } else {
                        // Fallback for unknown service types or when service_type is null
                        $item->test = (object) [
                            'name' => $line->service_name ?? ($line->labTest ? $line->labTest->test_name : 'Medical Service'),
                            'test_code' => $line->labTest ? $line->labTest->test_code : 'SVC',
                            'description' => $line->labTest ? $line->labTest->description : 'Medical Service',
                            'department' => $line->labTest ? $line->labTest->department : 'General',
                            'sample_type' => $line->labTest ? $line->labTest->sample_type : 'N/A',
                        ];
                    }

                    return $item;
                });
            }

            // Calculate financial totals using visible lines only
            $subtotal = $invoice->display_subtotal; // Use display_subtotal which excludes commission
            $discountAmount = $invoice->discount ?? 0;
            $taxAmount = $invoice->tax_amount ?? 0;
            $grandTotal = $invoice->grand_total ?? 0;
            $paidAmount = $invoice->paid_amount ?? 0;
            $remainingAmount = $grandTotal - $paidAmount;

            // Set variables that the print template expects
            $invoice->items = $items; // Add items to invoice object for template compatibility
            $invoice->discount_amount = $discountAmount; // Map discount to discount_amount
            $invoice->discount_percentage = $invoice->discount_percentage ?? 0;
            $invoice->tax_percentage = $invoice->tax_percentage ?? 0;

            // Increment print count for approved request (only for non-admin users with unpaid invoices)
            if ($approvedRequest && !$isAdmin && !$isFullyPaid) {
                $approvedRequest->increment('prints_used');
            }

            return view('admin.medical.invoices.print', compact(
                'invoice',
                'hospital',
                'doctor',
                'items',
                'subtotal',
                'discountAmount',
                'taxAmount',
                'grandTotal',
                'paidAmount',
                'remainingAmount',
                'doubleCopyPrint',
                'letterhead'
            ));
        } catch (\Exception $e) {
            // Log the error and return with minimal data
            Log::error('Error printing medical invoice: ' . $e->getMessage(), [
                'invoice_id' => $invoice->id,
                'error' => $e->getTraceAsString()
            ]);

            // Create safe fallback data
            $invoice->items = collect(); // Ensure items is always a collection
            $invoice->discount_amount = $invoice->discount ?? 0;
            $invoice->discount_percentage = 0;
            $invoice->tax_percentage = 0;

            // Get hospital info using fallback method
            $hospital = $this->getDefaultHospitalInfo();

            return view('admin.medical.invoices.print', [
                'invoice' => $invoice,
                'hospital' => $hospital,
                'doctor' => null,
                'items' => collect(),
                'subtotal' => $invoice->display_subtotal ?? 0,
                'discountAmount' => $invoice->discount ?? 0,
                'taxAmount' => $invoice->tax_amount ?? 0,
                'grandTotal' => $invoice->grand_total ?? 0,
                'paidAmount' => $invoice->paid_amount ?? 0,
                'remainingAmount' => ($invoice->grand_total ?? 0) - ($invoice->paid_amount ?? 0),
            ]);
        }
    }

    private function getDefaultHospitalInfo()
    {
        // Try to get any active business first
        $activeBusiness = \App\Models\Business::where('is_active', true)->first();
        if ($activeBusiness) {
            return (object) [
                'name' => $activeBusiness->hospital_name,
                'address' => $activeBusiness->address,
                'phone' => $activeBusiness->contact_number,
                'emergency_contact' => $activeBusiness->emergency_contact ?? null,
                'email' => $activeBusiness->email,
            ];
        }

        // Absolute fallback to config values
        return (object) [
            'name' => config('app.hospital_name', 'Medical Center'),
            'address' => config('app.hospital_address', 'Healthcare Address'),
            'phone' => config('app.hospital_phone', '+880-XXXXXXXXX'),
            'emergency_contact' => config('app.hospital_emergency', null),
            'email' => config('app.hospital_email', 'info@medicalcenter.com'),
        ];
    }
    public function printA4(MedicalInvoice $invoice)
    {
        try {
            // Check user permissions and invoice status
            $user = Auth::user();
            $isAdmin = ($user->role === 'admin');
            $isFullyPaid = $invoice->status === 'paid' || ($invoice->paid_amount >= $invoice->grand_total);
            $printingUnpaidEnabled = filter_var(Setting::get('printing_invoice_unpaid', false), FILTER_VALIDATE_BOOLEAN);

            // Admin users can print any invoice without restrictions
            if ($isAdmin) {
                // Allow unlimited prints for admin users
            }
            // If printing unpaid setting is disabled, everyone can print all invoices
            elseif (!$printingUnpaidEnabled) {
                // Allow printing all invoices
            }
            // Non-admin users can print paid invoices without restrictions
            elseif ($isFullyPaid) {
                // Allow unlimited prints for paid invoices
            }
            // Non-admin users need to request access for unpaid invoices when setting is enabled
            else {
                $approvedRequest = $invoice->printRequests()->where('request_type', 'a4')->where('status', 'approved')->first();
                if (!$approvedRequest) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Print access required. Please request permission from an administrator.'
                    ], 403);
                }
                if ($approvedRequest->prints_used >= $approvedRequest->allowed_prints) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Print limit exceeded. You have used all ' . $approvedRequest->allowed_prints . ' allowed prints for this invoice. Please request additional access.'
                    ], 403);
                }
            }

            // Check if double copy print is enabled
            $doubleCopyPrint = Setting::get('double_copy_print', false);

            // Load relationships safely - use visibleLines to exclude commission
            $invoice->load([
                'patient',
                'business',
                'careOf',
                'doctor',
                'visibleLines.labTest', // Use visibleLines instead of lines
                'visibleLines.appointment',
                'visibleLines.booking.bookable',
                'visibleLines.booking.otRoom',
                'createdBy',
            ]);

            // Fallback: load patient even if not active
            if (!$invoice->patient && $invoice->patient_id) {
                $invoice->patient = \App\Models\Patient::find($invoice->patient_id);
            }

            // Fallback: load doctor even if not active (with soft deletes)
            if (!$invoice->doctor && $invoice->doctor_id) {
                $invoice->doctor = \App\Models\Doctor::withTrashed()->find($invoice->doctor_id);
            }

            // Get business/hospital information using the model method
            $hospital = $invoice->getHospitalInfo();

            // Check if letterhead is enabled and get active letterhead for invoices
            $letterheadEnabled = filter_var(Setting::get('letterhead_enabled', false), FILTER_VALIDATE_BOOLEAN);
            $letterhead = null;
            if ($letterheadEnabled && $invoice->business_id) {
                $letterhead = \App\Models\Letterhead::getActiveForBusiness($invoice->business_id, 'Invoice');
            }

            // Get doctor information safely
            $doctor = null;

            // First, check if doctor_id exists for this invoice and load that specific doctor
            if ($invoice->doctor_id) {
                $doctor = Doctor::find($invoice->doctor_id);
            }

            // Fallback: get doctor from appointments if no direct doctor_id
            if (!$doctor && ($doctorFromAppointment = $invoice->getDoctorFromAppointments())) {
                $doctor = $doctorFromAppointment;
            }

            // Final fallback: use created by user
            if (!$doctor && $invoice->createdBy) {
                $doctor = (object) [
                    'name' => $invoice->createdBy->name,
                    'specialization' => null,
                    'license_number' => null,
                ];
            }

            // Transform visible lines to items format for the print template (excluding commission)
            $items = collect();
            if ($invoice->visibleLines && $invoice->visibleLines->count() > 0) {
                $items = $invoice->visibleLines->map(function ($line) {
                    $item = (object) [
                        'service_type' => $line->service_type ?? 'lab_test',
                        'service_name' => $line->service_display_name,
                        'quantity' => $line->quantity ?? 1,
                        'unit_price' => $line->unit_price ?? 0,
                        'line_discount' => $line->line_discount ?? 0,
                        'total_price' => $line->line_total ?? 0,
                    ];

                    // Add service-specific details for display
                    if (($line->service_type === 'lab_test' || !$line->service_type) && $line->labTest) {
                        $item->test = (object) [
                            'name' => $line->labTest->test_name,
                            'test_code' => $line->labTest->test_code ?? null,
                            'description' => $line->labTest->description ?? 'Lab Test',
                            'department' => $line->labTest->department ?? null,
                            'sample_type' => $line->labTest->sample_type ?? null,
                        ];
                    } elseif ($line->service_type === 'consultation') {
                        $item->test = (object) [
                            'name' => $line->service_name ?? 'Consultation Service',
                            'test_code' => 'CONSULT',
                            'description' => 'Doctor Consultation Service',
                            'department' => 'Consultation',
                            'sample_type' => 'N/A',
                        ];

                        // Add consultation details
                        $serviceDetails = $line->service_details;
                        if ($serviceDetails) {
                            $item->appointment_date = $serviceDetails['appointment_date'] ?? null;
                            $item->appointment_time = $serviceDetails['appointment_time'] ?? null;
                            $item->doctor_name = $serviceDetails['doctor_name'] ?? 'Doctor';
                        }
                    } elseif ($line->service_type === 'booking') {
                        $bookingType = 'Service';
                        $testCode = 'BOOK';
                        $department = 'Booking Services';

                        if ($line->booking) {
                            $bookingType = ucfirst($line->booking->booking_type);
                            $testCode = $line->booking->booking_type === 'ot' ? 'OT-BOOK' : 'WARD-BOOK';
                            $department = $bookingType . ' Services';
                        }

                        $item->test = (object) [
                            'name' => $line->service_name ?? ($bookingType . ' Booking'),
                            'test_code' => $testCode,
                            'description' => $line->booking && $line->booking->booking_type === 'ot' ? 'Operation Theater Booking' : 'Ward Booking Service',
                            'department' => $department,
                            'sample_type' => 'N/A',
                        ];

                        // Add booking details
                        $serviceDetails = $line->service_details;
                        if ($serviceDetails) {
                            $item->booking_date = $serviceDetails['booking_date'] ?? null;
                            $item->booking_time = $serviceDetails['booking_time'] ?? null;
                            $item->booking_type = $serviceDetails['booking_type'] ?? null;
                        }

                        if ($line->booking) {
                            if ($line->booking->bookable) {
                                $item->bookable_name = $line->booking->bookable->name ?? 'Service';
                            }
                            if ($line->booking->otRoom) {
                                $item->ot_room_name = $line->booking->otRoom->name ?? 'OT Room';
                            }
                        }
                    } else {
                        // Fallback for unknown service types or when service_type is null
                        $item->test = (object) [
                            'name' => $line->service_name ?? ($line->labTest ? $line->labTest->test_name : 'Medical Service'),
                            'test_code' => $line->labTest ? $line->labTest->test_code : 'SVC',
                            'description' => $line->labTest ? $line->labTest->description : 'Medical Service',
                            'department' => $line->labTest ? $line->labTest->department : 'General',
                            'sample_type' => $line->labTest ? $line->labTest->sample_type : 'N/A',
                        ];
                    }

                    return $item;
                });
            }

            // Calculate financial totals using visible lines only
            $subtotal = $invoice->display_subtotal; // Use display_subtotal which excludes commission
            $discountAmount = $invoice->discount ?? 0;
            $taxAmount = $invoice->tax_amount ?? 0;
            $grandTotal = $invoice->grand_total ?? 0;
            $paidAmount = $invoice->paid_amount ?? 0;
            $remainingAmount = $grandTotal - $paidAmount;

            // Set variables that the print template expects
            $invoice->items = $items; // Add items to invoice object for template compatibility
            $invoice->discount_amount = $discountAmount; // Map discount to discount_amount
            $invoice->discount_percentage = $invoice->discount_percentage ?? 0;
            $invoice->tax_percentage = $invoice->tax_percentage ?? 0;

            // Increment print count for approved request (only for non-admin users with unpaid invoices)
            if (isset($approvedRequest) && $approvedRequest && !$isAdmin && !$isFullyPaid) {
                $approvedRequest->increment('prints_used');
            }

            return view('admin.medical.invoices.print-a4', compact(
                'invoice',
                'hospital',
                'doctor',
                'items',
                'subtotal',
                'discountAmount',
                'taxAmount',
                'grandTotal',
                'paidAmount',
                'remainingAmount',
                'doubleCopyPrint',
                'letterhead'
            ));
        } catch (\Exception $e) {
            // Log the error and return with minimal data
            Log::error('Error printing A4 medical invoice: ' . $e->getMessage(), [
                'invoice_id' => $invoice->id,
                'error' => $e->getTraceAsString()
            ]);

            // Create safe fallback data
            $invoice->items = collect(); // Ensure items is always a collection
            $invoice->discount_amount = $invoice->discount ?? 0;
            $invoice->discount_percentage = 0;
            $invoice->tax_percentage = 0;

            // Get hospital info using fallback method
            $hospital = $this->getDefaultHospitalInfo();

            return view('admin.medical.invoices.print-a4', [
                'invoice' => $invoice,
                'hospital' => $hospital,
                'doctor' => null,
                'items' => collect(),
                'subtotal' => $invoice->display_subtotal ?? 0,
                'discountAmount' => $invoice->discount ?? 0,
                'taxAmount' => $invoice->tax_amount ?? 0,
                'grandTotal' => $invoice->grand_total ?? 0,
                'paidAmount' => $invoice->paid_amount ?? 0,
                'remainingAmount' => ($invoice->grand_total ?? 0) - ($invoice->paid_amount ?? 0),
            ]);
        }
    }

    public function printA5(MedicalInvoice $invoice)
    {
        try {
            // Initialize variables
            $letterhead = null;

            // Check user permissions and invoice status
            $user = Auth::user();
            $isAdmin = ($user->role === 'admin');
            $isFullyPaid = $invoice->status === 'paid' || ($invoice->paid_amount >= $invoice->grand_total);
            $printingUnpaidEnabled = filter_var(Setting::get('printing_invoice_unpaid', false), FILTER_VALIDATE_BOOLEAN);

            // Admin users can print any invoice without restrictions
            if ($isAdmin) {
                // Allow unlimited prints for admin users
            }
            // If printing unpaid setting is disabled, everyone can print all invoices
            elseif (!$printingUnpaidEnabled) {
                // Allow printing all invoices
            }
            // Non-admin users can print paid invoices without restrictions
            elseif ($isFullyPaid) {
                // Allow unlimited prints for paid invoices
            }
            // Non-admin users need to request access for unpaid invoices when setting is enabled
            else {
                $approvedRequest = $invoice->printRequests()->where('request_type', 'a5')->where('status', 'approved')->first();
                if (!$approvedRequest) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Print access required. Please request permission from an administrator.'
                    ], 403);
                }
                if ($approvedRequest->prints_used >= $approvedRequest->allowed_prints) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Print limit exceeded. You have used all ' . $approvedRequest->allowed_prints . ' allowed prints for this invoice. Please request additional access.'
                    ], 403);
                }
            }

            // Check if double copy print is enabled
            $doubleCopyPrint = Setting::get('double_copy_print', false);

            // Load relationships safely - use visibleLines to exclude commission
            $invoice->load([
                'patient',
                'business',
                'careOf',
                'doctor',
                'visibleLines.labTest', // Use visibleLines instead of lines
                'visibleLines.appointment',
                'visibleLines.booking.bookable',
                'visibleLines.booking.otRoom',
                'createdBy',
            ]);

            // Fallback: load patient even if not active
            if (!$invoice->patient && $invoice->patient_id) {
                $invoice->patient = \App\Models\Patient::find($invoice->patient_id);
            }

            // Fallback: load doctor even if not active (with soft deletes)
            if (!$invoice->doctor && $invoice->doctor_id) {
                $invoice->doctor = \App\Models\Doctor::withTrashed()->find($invoice->doctor_id);
            }

            // Get business/hospital information using the model method
            $hospital = $invoice->getHospitalInfo();

            // Check if letterhead is enabled and get active letterhead for invoices
            $letterheadEnabled = filter_var(Setting::get('letterhead_enabled', false), FILTER_VALIDATE_BOOLEAN);
            $letterhead = null;
            if ($letterheadEnabled && $invoice->business_id) {
                $letterhead = \App\Models\Letterhead::getActiveForBusiness($invoice->business_id, 'Invoice');
            }

            // Get doctor information safely
            $doctor = null;

            // First, check if doctor_id exists for this invoice and load that specific doctor
            if ($invoice->doctor_id) {
                $doctor = Doctor::find($invoice->doctor_id);
            }

            // Fallback: get doctor from appointments if no direct doctor_id
            if (!$doctor && ($doctorFromAppointment = $invoice->getDoctorFromAppointments())) {
                $doctor = $doctorFromAppointment;
            }

            // Final fallback: use created by user
            if (!$doctor && $invoice->createdBy) {
                $doctor = (object) [
                    'name' => $invoice->createdBy->name,
                    'specialization' => null,
                    'license_number' => null,
                ];
            }

            // Transform visible lines to items format for the print template (excluding commission)
            $items = collect();
            if ($invoice->visibleLines && $invoice->visibleLines->count() > 0) {
                $items = $invoice->visibleLines->map(function ($line) {
                    $item = (object) [
                        'service_type' => $line->service_type ?? 'lab_test',
                        'service_name' => $line->service_display_name,
                        'quantity' => $line->quantity ?? 1,
                        'unit_price' => $line->unit_price ?? 0,
                        'line_discount' => $line->line_discount ?? 0,
                        'total_price' => $line->line_total ?? 0,
                    ];

                    // Add additional details based on service type
                    if ($line->labTest) {
                        $item->service_details = "Lab Test: " . $line->labTest->name;
                    } elseif ($line->appointment) {
                        $item->service_details = "Consultation";
                    } elseif ($line->booking && $line->booking->bookable) {
                        $item->service_details = "Booking: " . $line->booking->bookable->name;
                    }

                    return $item;
                });
            }

            // Calculate totals using visible lines only
            $subtotal = $invoice->visibleLines->sum('line_total') ?? 0;
            $discountAmount = $invoice->discount ?? 0;
            $taxAmount = $invoice->tax_amount ?? 0;
            $grandTotal = $invoice->grand_total ?? 0;
            $paidAmount = $invoice->paid_amount ?? 0;
            $remainingAmount = $grandTotal - $paidAmount;

            // Set variables that the print template expects
            $invoice->items = $items; // Add items to invoice object for template compatibility
            $invoice->discount_amount = $discountAmount; // Map discount to discount_amount
            $invoice->discount_percentage = $invoice->discount_percentage ?? 0;
            $invoice->tax_percentage = $invoice->tax_percentage ?? 0;

            // Generate barcodes for lab_id and invoice_number
            $labIdBarcode = $this->generateBarcodeSVG($invoice->lab_id);
            $invoiceNumberBarcode = $this->generateBarcodeSVG($invoice->invoice_number);

            // Increment print count for approved request (only for non-admin users with unpaid invoices)
            if (isset($approvedRequest) && $approvedRequest && !$isAdmin && !$isFullyPaid) {
                $approvedRequest->increment('prints_used');
            }

            return view('admin.medical.invoices.print-a5', compact(
                'invoice',
                'hospital',
                'doctor',
                'items',
                'subtotal',
                'discountAmount',
                'taxAmount',
                'grandTotal',
                'paidAmount',
                'remainingAmount',
                'doubleCopyPrint',
                'letterhead',
                'labIdBarcode',
                'invoiceNumberBarcode'
            ));
        } catch (\Exception $e) {
            // Log the error and return with minimal data
            Log::error('Error printing A5 medical invoice: ' . $e->getMessage(), [
                'invoice_id' => $invoice->id,
                'error' => $e->getTraceAsString()
            ]);

            // Create safe fallback data
            $invoice->items = collect(); // Ensure items is always a collection
            $invoice->discount_amount = $invoice->discount ?? 0;
            $invoice->discount_percentage = 0;
            $invoice->tax_percentage = 0;

            // Get hospital info using fallback method
            $hospital = $this->getDefaultHospitalInfo();

            return view('admin.medical.invoices.print-a5', [
                'invoice' => $invoice,
                'hospital' => $hospital,
                'doctor' => null,
                'items' => collect(),
                'subtotal' => $invoice->display_subtotal ?? 0,
                'discountAmount' => $invoice->discount ?? 0,
                'taxAmount' => $invoice->tax_amount ?? 0,
                'grandTotal' => $invoice->grand_total ?? 0,
                'paidAmount' => $invoice->paid_amount ?? 0,
                'remainingAmount' => ($invoice->grand_total ?? 0) - ($invoice->paid_amount ?? 0),
            ]);
        }
    }

    // Add this method to generate PDF
    public function generatePDF(MedicalInvoice $invoice)
    {
        try {
            // Load relationships
            $invoice->load([
                'patient',
                'business',
                'careOf',
                'lines.labTest',
                'lines.appointment.doctor',
                'lines.booking.bookable',
                'lines.booking.otRoom',
                'createdBy',
                'doctor',
            ]);

            // Get business/hospital information
            $hospital = null;
            if ($invoice->business) {
                $hospital = (object) [
                    'name' => $invoice->business->hospital_name,
                    'address' => $invoice->business->address ?? 'Healthcare Address',
                    'phone' => $invoice->business->contact_number ?? '+880-XXXXXXXXX',
                    'emergency_contact' => $invoice->business->emergency_contact ?? null,
                    'email' => $invoice->business->email ?? null,
                ];
            } else {
                $hospital = $this->getDefaultHospitalInfo();
            }

            // Get doctor information
            $doctor = null;
            if ($invoice->doctor) {
                $doctor = $invoice->doctor;
            } elseif ($invoice->createdBy) {
                $doctor = (object) [
                    'name' => $invoice->createdBy->name,
                    'specialization' => $invoice->createdBy->specialization ?? null,
                    'license_number' => $invoice->createdBy->license_number ?? null,
                ];
            }

            // Transform lines to items format
            $items = collect();
            if ($invoice->lines && $invoice->lines->count() > 0) {
                $items = $invoice->lines->map(function ($line) {
                    $item = (object) [
                        'service_type' => $line->service_type,
                        'service_name' => $line->service_name,
                        'quantity' => $line->quantity ?? 1,
                        'unit_price' => $line->unit_price ?? 0,
                        'line_discount' => $line->line_discount ?? 0,
                        'total_price' => $line->line_total ?? 0,
                        'notes' => $line->notes ?? null,
                    ];

                    // Add service-specific details
                    if ($line->service_type === 'lab_test' && $line->labTest) {
                        $item->test = (object) [
                            'name' => $line->labTest->test_name,
                            'test_code' => $line->labTest->test_code ?? null,
                            'description' => $line->labTest->description ?? 'Lab Test',
                            'department' => $line->labTest->department ?? null,
                            'sample_type' => $line->labTest->sample_type ?? null,
                        ];
                    } elseif ($line->service_type === 'consultation') {
                        $item->test = (object) [
                            'name' => $line->service_name,
                            'test_code' => 'CONSULT',
                            'description' => 'Consultation Service',
                            'department' => 'Consultation',
                            'sample_type' => 'N/A',
                        ];
                    } elseif ($line->service_type === 'booking') {
                        $item->test = (object) [
                            'name' => $line->service_name,
                            'test_code' => strtoupper($line->booking->booking_type ?? 'BOOKING'),
                            'description' => 'Booking Service',
                            'department' => ucfirst($line->booking->booking_type ?? 'Booking') . ' Services',
                            'sample_type' => 'N/A',
                        ];
                    } else {
                        $item->test = (object) [
                            'name' => $line->service_name ?? 'Medical Service',
                            'test_code' => null,
                            'description' => 'Medical Service',
                            'department' => null,
                            'sample_type' => null,
                        ];
                    }

                    return $item;
                });
            }

            // Calculate totals
            $subtotal = $invoice->subtotal ?? 0;
            $discountAmount = $invoice->discount ?? 0;
            $taxAmount = $invoice->tax_amount ?? 0;
            $grandTotal = $invoice->grand_total ?? 0;
            $paidAmount = $invoice->paid_amount ?? 0;
            $remainingAmount = $grandTotal - $paidAmount;

            // Generate PDF
            $pdf = Pdf::loadView('admin.medical.invoices.pdf-template', compact(
                'invoice',
                'hospital',
                'doctor',
                'items',
                'subtotal',
                'discountAmount',
                'taxAmount',
                'grandTotal',
                'paidAmount',
                'remainingAmount'
            ));

            $pdf->setPaper('A4', 'portrait');

            // Generate filename
            $filename = 'invoice_' . $invoice->invoice_number . '_' . now()->format('Y-m-d') . '.pdf';

            // Store PDF temporarily
            $pdfContent = $pdf->output();
            Storage::disk('public')->put('temp/invoices/' . $filename, $pdfContent);

            // Return PDF for download or sharing
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            Log::error('Error generating PDF: ' . $e->getMessage(), [
                'invoice_id' => $invoice->id,
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add this method to get PDF URL for WhatsApp sharing
    public function getShareableLink(MedicalInvoice $invoice)
    {
        try {
            // Generate a temporary shareable link
            $token = Str::random(32);

            // Store the token with invoice ID in cache for 24 hours
            Cache::put('invoice_share_' . $token, $invoice->id, now()->addHours(24));

            // Return the shareable URL
            $shareUrl = route('admin.medical.invoices.shared', ['token' => $token]);

            return response()->json([
                'success' => true,
                'share_url' => $shareUrl,
                'pdf_url' => route('admin.medical.invoices.pdf', $invoice->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate shareable link: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add this method to handle shared invoice access
    public function sharedInvoice($token)
    {
        try {
            $invoiceId = Cache::get('invoice_share_' . $token);

            if (!$invoiceId) {
                abort(404, 'Shared link has expired or is invalid');
            }

            $invoice = MedicalInvoice::findOrFail($invoiceId);

            // Return the same view as show method but without edit capabilities
            return $this->show($invoice);
        } catch (\Exception $e) {
            abort(404, 'Invoice not found');
        }
    }

    /**
     * Generate a unique invoice number with race condition protection
     */
    private function generateUniqueInvoiceNumber()
    {
        $maxAttempts = 10;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                // Get the highest invoice number using raw SQL for better performance
                // Use FOR UPDATE to lock the rows being read (works within existing transaction)
                $result = DB::select("
                    SELECT invoice_number
                    FROM medical_invoices
                    WHERE invoice_number IS NOT NULL
                    ORDER BY CAST(invoice_number AS UNSIGNED) DESC
                    LIMIT 1
                    FOR UPDATE
                ");

                $nextNumber = 1;
                if (!empty($result)) {
                    // Extract the number part from the last invoice number
                    $lastNumber = (int) $result[0]->invoice_number;
                    $nextNumber = $lastNumber + 1;
                }

                $invoiceNumber = (string) $nextNumber;

                // Double-check if this number already exists (extra safety)
                $exists = MedicalInvoice::where('invoice_number', $invoiceNumber)->exists();

                if (!$exists) {
                    Log::info('Unique invoice number generated', [
                        'invoice_number' => $invoiceNumber,
                        'attempt' => $attempt + 1,
                        'next_number' => $nextNumber
                    ]);
                    return $invoiceNumber;
                }

                Log::warning('Generated invoice number already exists, retrying', [
                    'invoice_number' => $invoiceNumber,
                    'attempt' => $attempt + 1
                ]);
            } catch (\Exception $e) {
                Log::error('Error generating invoice number', [
                    'attempt' => $attempt + 1,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            $attempt++;

            // Small delay before retry to reduce contention
            if ($attempt < $maxAttempts) {
                usleep(rand(50000, 150000)); // Random delay between 50-150ms
            }
        }

        // Fallback: use timestamp-based number if all attempts fail
        $timestamp = time();
        $fallbackNumber = (string) $timestamp;

        // Ensure fallback number is also unique
        $counter = 0;
        while (MedicalInvoice::where('invoice_number', $fallbackNumber)->exists() && $counter < 100) {
            $counter++;
            $fallbackNumber = (string) ($timestamp + $counter);
        }

        Log::error('Failed to generate unique invoice number after max attempts, using fallback', [
            'fallback_number' => $fallbackNumber,
            'max_attempts' => $maxAttempts,
            'fallback_counter' => $counter
        ]);

        return $fallbackNumber;
    }

    /**
     * Generate a unique lab ID with race condition protection
     */
    private function generateUniqueLabId()
    {
        $maxAttempts = 10;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                // Get the highest lab_id using raw SQL for better performance
                // Use FOR UPDATE to lock the rows being read (works within existing transaction)
                $result = DB::select("
                    SELECT lab_id
                    FROM medical_invoices
                    WHERE lab_id IS NOT NULL
                    ORDER BY CAST(lab_id AS UNSIGNED) DESC
                    LIMIT 1
                    FOR UPDATE
                ");

                $nextNumber = 1;
                if (!empty($result)) {
                    // Extract the number part from the last lab_id
                    $lastNumber = (int) $result[0]->lab_id;
                    $nextNumber = $lastNumber + 1;
                }

                $labId = (string) $nextNumber;

                // Double-check if this lab_id already exists (extra safety)
                $exists = MedicalInvoice::where('lab_id', $labId)->exists();

                if (!$exists) {
                    Log::info('Unique lab ID generated', [
                        'lab_id' => $labId,
                        'attempt' => $attempt + 1,
                        'next_number' => $nextNumber
                    ]);
                    return $labId;
                }

                Log::warning('Generated lab ID already exists, retrying', [
                    'lab_id' => $labId,
                    'attempt' => $attempt + 1
                ]);
            } catch (\Exception $e) {
                Log::error('Error generating lab ID', [
                    'attempt' => $attempt + 1,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            $attempt++;

            // Small delay before retry to reduce contention
            if ($attempt < $maxAttempts) {
                usleep(rand(50000, 150000)); // Random delay between 50-150ms
            }
        }

        // Fallback: use timestamp-based number if all attempts fail
        $timestamp = time();
        $fallbackLabId = (string) $timestamp;

        // Ensure fallback lab_id is also unique
        $counter = 0;
        while (MedicalInvoice::where('lab_id', $fallbackLabId)->exists() && $counter < 100) {
            $counter++;
            $fallbackLabId = (string) ($timestamp + $counter);
        }

        Log::error('Failed to generate unique lab ID after max attempts, using fallback', [
            'fallback_lab_id' => $fallbackLabId,
            'max_attempts' => $maxAttempts,
            'fallback_counter' => $counter
        ]);

        return $fallbackLabId;
    }

    /**
     * Create invoice with retry mechanism for duplicate key errors
     */
    private function createInvoiceWithRetry($invoiceData, $maxRetries = 3)
    {
        $attempt = 0;

        while ($attempt < $maxRetries) {
            try {
                return MedicalInvoice::create($invoiceData);
            } catch (\Illuminate\Database\QueryException $e) {
                // Check if it's a duplicate key error (MySQL error code 1062)
                if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    Log::warning('Duplicate invoice number detected, generating new number', [
                        'attempt' => $attempt + 1,
                        'original_invoice_number' => $invoiceData['invoice_number'],
                        'error' => $e->getMessage()
                    ]);

                    // Generate a new invoice number
                    $invoiceData['invoice_number'] = $this->generateUniqueInvoiceNumber();

                    Log::info('New invoice number generated for retry', [
                        'new_invoice_number' => $invoiceData['invoice_number'],
                        'attempt' => $attempt + 1
                    ]);

                    $attempt++;

                    // Small delay before retry
                    if ($attempt < $maxRetries) {
                        usleep(rand(100000, 200000)); // Random delay between 100-200ms
                    }

                    continue;
                }

                // If it's not a duplicate key error, re-throw the exception
                throw $e;
            }
        }

        // If all retries failed, throw the last exception
        throw new \Exception("Failed to create invoice after {$maxRetries} attempts due to duplicate key errors");
    }

    /**
     * Generate SVG barcode for given text
     */
    private function generateBarcodeSVG($text, $type = 'TYPE_CODE_128')
    {
        try {
            $generator = new BarcodeGeneratorSVG();
            return $generator->getBarcode($text, $generator::TYPE_CODE_128);
        } catch (\Exception $e) {
            Log::error('Error generating barcode', [
                'text' => $text,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
