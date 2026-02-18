<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use App\Models\WaitingList;
use App\Models\DoctorScheduleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor'])
            ->forBusiness(Auth::user()->business_id);

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('appointment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('appointment_date', '<=', $request->date_to);
        }

        // Filter by doctor
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Quick filters
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'today':
                    $query->today();
                    break;
                case 'upcoming':
                    $query->upcoming();
                    break;
                case 'active':
                    $query->whereIn('status', ['scheduled', 'confirmed', 'in_progress']);
                    break;
                case 'completed':
                    $query->where('status', 'completed');
                    break;
            }
        }

        $appointments = $query->latest('appointment_date')
            ->latest('appointment_time')
            ->paginate(15)
            ->withQueryString();

        // Get filter options
        $doctors = Doctor::forBusiness(Auth::user()->business_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Get statistics
        $stats = $this->getAppointmentStats();

        return view('admin.appointments.index', compact('appointments', 'doctors', 'stats'));
    }

    public function create(Request $request)
    {
        $doctors = Doctor::forBusiness(Auth::user()->business_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Change from users to patients table
        $patients = \App\Models\Patient::where('business_id', Auth::user()->business_id)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        // Pre-select values if provided
        $selectedDoctor = null;
        $selectedPatient = null;
        $selectedDate = $request->get('date', today()->format('Y-m-d'));
        $selectedTime = $request->get('time');

        if ($request->filled('doctor_id')) {
            $selectedDoctor = $doctors->find($request->doctor_id);
        }

        if ($request->filled('patient_id')) {
            $selectedPatient = $patients->where('patient_id', $request->patient_id)->first();
        }

        return view('admin.appointments.create', compact(
            'doctors',
            'patients',
            'selectedDoctor',
            'selectedPatient',
            'selectedDate',
            'selectedTime'
        ));
    }


    public function store(Request $request)
    {
        try {
            // Add detailed logging
            Log::info('Appointment creation started', [
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
                'business_id' => Auth::user()->business_id
            ]);

            $request->validate([
                'patient_id' => 'required|string', // Changed to string since it's TH-001 format
                'doctor_id' => 'required|exists:doctors,id',
                'appointment_date' => 'required|date|after_or_equal:today',
                'appointment_time' => 'required|date_format:H:i',
                'duration' => 'required|integer|min:15|max:240',
                'appointment_type' => 'required|in:consultation,follow_up,emergency,checkup',
                'chief_complaint' => 'nullable|string|max:500',
                'notes' => 'nullable|string|max:1000',
                'priority' => 'required|in:low,medium,high,urgent',
                'consultation_fee' => 'nullable|numeric|min:0'
            ]);

            Log::info('Validation passed');

            // Verify doctor belongs to current business
            $doctor = Doctor::forBusiness(Auth::user()->business_id)
                ->findOrFail($request->doctor_id);

            Log::info('Doctor found', ['doctor_id' => $doctor->id, 'doctor_name' => $doctor->name]);

            // Find patient by patient_id (TH-001) instead of id
            $patient = \App\Models\Patient::where('business_id', Auth::user()->business_id)
                ->where('patient_id', $request->patient_id)
                ->where('is_active', true)
                ->first();

            if (!$patient) {
                Log::error('Patient not found', [
                    'patient_id' => $request->patient_id,
                    'business_id' => Auth::user()->business_id
                ]);

                // Check if it's an AJAX request
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Patient not found.',
                        'errors' => ['patient_id' => ['Selected patient not found.']]
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->withErrors(['patient_id' => 'Selected patient not found.']);
            }

            Log::info('Patient found', [
                'patient_database_id' => $patient->id,
                'patient_id' => $patient->patient_id,
                'patient_name' => $patient->full_name
            ]);

            // Check for conflicts
            $conflictingAppointment = Appointment::forBusiness(Auth::user()->business_id)
                ->where('doctor_id', $request->doctor_id)
                ->where('appointment_date', $request->appointment_date)
                ->where('appointment_time', $request->appointment_time)
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->exists();

            if ($conflictingAppointment) {
                Log::info('Conflicting appointment found');

                // Check if it's an AJAX request
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This time slot is already booked for the selected doctor.',
                        'errors' => [
                            'appointment_time' => ['This time slot is already booked.']
                        ]
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->withErrors(['appointment_time' => 'This time slot is already booked for the selected doctor.']);
            }

            // Convert duration to integer to avoid Carbon error
            $duration = (int) $request->duration;

            // Calculate end time
            $startTime = Carbon::parse($request->appointment_date . ' ' . $request->appointment_time);
            $endTime = $startTime->copy()->addMinutes($duration);

            Log::info('About to create appointment', [
                'patient_database_id' => $patient->id,
                'doctor_id' => $request->doctor_id,
                'duration' => $duration,
                'start_time' => $startTime->format('Y-m-d H:i:s'),
                'end_time' => $endTime->format('H:i:s')
            ]);

            $appointmentData = [
                'business_id' => Auth::user()->business_id,
                'patient_id' => $patient->id, // Use the actual database id for foreign key
                'doctor_id' => $request->doctor_id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'end_time' => $endTime->format('H:i:s'),
                'duration' => $duration,
                'appointment_type' => $request->appointment_type,
                'chief_complaint' => $request->chief_complaint,
                'notes' => $request->notes,
                'priority' => $request->priority,
                'consultation_fee' => $request->consultation_fee ? (float) $request->consultation_fee : $doctor->consultation_fee,
                'payment_status' => 'pending',
                'status' => 'scheduled',
                'created_by' => Auth::id()
            ];

            Log::info('Appointment data prepared', $appointmentData);

            $appointment = Appointment::create($appointmentData);

            Log::info('Appointment created successfully', ['appointment_id' => $appointment->id]);

            // Check if this appointment fulfills any waiting list entries
            $this->processWaitingListForSlot(
                $request->doctor_id,
                $request->appointment_date,
                $request->appointment_time
            );

            // Check if it's an AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Appointment created successfully.',
                    'appointment' => [
                        'id' => $appointment->id,
                        'patient_name' => $patient->full_name,
                        'patient_id' => $patient->patient_id,
                        'doctor_name' => $doctor->name,
                        'appointment_date' => $appointment->appointment_date->format('Y-m-d'),
                        'appointment_time' => $appointment->appointment_time->format('H:i'),
                        'end_time' => $appointment->end_time ? $appointment->end_time->format('H:i') : null,
                        'duration' => $appointment->duration,
                        'status' => $appointment->status,
                        'appointment_type' => $appointment->appointment_type,
                        'priority' => $appointment->priority,
                        'consultation_fee' => $appointment->consultation_fee
                    ]
                ]);
            }

            // For regular form submissions, redirect with success message
            return redirect()->route('admin.appointments.index')
                ->with('success', 'Appointment created successfully! Patient: ' . $patient->full_name . ' with Dr. ' . $doctor->name . ' on ' . $appointment->appointment_date->format('M d, Y') . ' at ' . $appointment->appointment_time->format('h:i A'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please check the form for errors.',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Model not found', ['exception' => $e->getMessage()]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected patient or doctor not found.',
                    'errors' => ['general' => ['Selected patient or doctor not found.']]
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Selected patient or doctor not found.']);
        } catch (\Exception $e) {
            Log::error('Appointment creation failed: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
                'business_id' => Auth::user()->business_id,
                'exception_message' => $e->getMessage(),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create appointment: ' . $e->getMessage(),
                    'errors' => ['general' => ['An unexpected error occurred: ' . $e->getMessage()]]
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create appointment. Please try again.');
        }
    }



    /**
     * Get appointment details for AJAX requests
     */
    public function getAppointmentDetails(Appointment $appointment)
    {
        try {
            // Check if appointment belongs to current user's business
            if ($appointment->business_id !== Auth::user()->business_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this appointment.'
                ], 403);
            }

            // Load relationships
            $appointment->load(['patient', 'doctor', 'createdBy']);

            return response()->json([
                'success' => true,
                'appointment' => [
                    'id' => $appointment->id,
                    'patient' => [
                        'id' => $appointment->patient->id,
                        'patient_id' => $appointment->patient->patient_id,
                        'name' => $appointment->patient->full_name,
                        'email' => $appointment->patient->email,
                        'phone' => $appointment->patient->phone,
                        'age' => $appointment->patient->age,
                        'gender' => $appointment->patient->gender,
                        'blood_group' => $appointment->patient->blood_group,
                        'avatar_url' => $appointment->patient->profile_image_url,
                    ],
                    'doctor' => [
                        'id' => $appointment->doctor->id,
                        'name' => $appointment->doctor->name,
                        'specialization' => $appointment->doctor->specialization,
                        'profile_image_url' => $appointment->doctor->profile_image_url ?? asset('images/avatars/default-doctor.png'),
                    ],
                    'appointment_date' => $appointment->appointment_date->format('Y-m-d'),
                    'appointment_time' => $appointment->appointment_time->format('H:i'),
                    'end_time' => $appointment->end_time ? $appointment->end_time->format('H:i') : null,
                    'duration' => $appointment->duration,
                    'appointment_type' => $appointment->appointment_type,
                    'status' => $appointment->status,
                    'priority' => $appointment->priority,
                    'chief_complaint' => $appointment->chief_complaint,
                    'notes' => $appointment->notes,
                    'consultation_fee' => $appointment->consultation_fee,
                    'payment_status' => $appointment->payment_status,
                    'created_at' => $appointment->created_at->format('M d, Y h:i A'),
                    'confirmed_at' => $appointment->confirmed_at ? $appointment->confirmed_at->format('M d, Y h:i A') : null,
                    'completed_at' => $appointment->completed_at ? $appointment->completed_at->format('M d, Y h:i A') : null,
                    'cancelled_at' => $appointment->cancelled_at ? $appointment->cancelled_at->format('M d, Y h:i A') : null,
                    'cancellation_reason' => $appointment->cancellation_reason,
                    'created_by' => $appointment->createdBy ? $appointment->createdBy->name : null,
                    'can_be_edited' => $appointment->canBeEdited(),
                    'can_be_cancelled' => $appointment->canBeCancelled(),
                    'can_be_completed' => $appointment->canBeCompleted(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching appointment details: ' . $e->getMessage(), [
                'appointment_id' => $appointment->id ?? null,
                'user_id' => Auth::id(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading appointment details.'
            ], 500);
        }
    }

    /**
     * Display the specified appointment
     */
    public function show(Appointment $appointment)
    {
        // Check if appointment belongs to current user's business
        if ($appointment->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this appointment.');
        }

        $appointment->load(['patient', 'doctor', 'createdBy']);

        return view('admin.appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified appointment
     */
    public function edit(Appointment $appointment)
    {
        // Check if appointment belongs to current user's business
        if ($appointment->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this appointment.');
        }

        if (!$appointment->canBeEdited()) {
            return redirect()->route('admin.appointments.show', $appointment)
                ->with('error', 'This appointment cannot be edited.');
        }

        $doctors = Doctor::forBusiness(Auth::user()->business_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $patients = User::where('business_id', Auth::user()->business_id)
            ->role('patient')
            ->orderBy('name')
            ->get();

        return view('admin.appointments.edit', compact('appointment', 'doctors', 'patients'));
    }

    /**
     * Update the specified appointment
     */
    public function update(Request $request, Appointment $appointment)
    {
        // Check if appointment belongs to current user's business
        if ($appointment->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this appointment.');
        }

        if (!$appointment->canBeEdited()) {
            return redirect()->route('admin.appointments.show', $appointment)
                ->with('error', 'This appointment cannot be edited.');
        }

        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'duration' => 'required|integer|min:15|max:240',
            'appointment_type' => 'required|in:consultation,follow_up,emergency,checkup',
            'chief_complaint' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'priority' => 'required|in:low,medium,high,urgent',
            'consultation_fee' => 'nullable|numeric|min:0'
        ]);

        // Check for conflicts (excluding current appointment)
        $conflictingAppointment = Appointment::forBusiness(Auth::user()->business_id)
            ->where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('id', '!=', $appointment->id)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->exists();

        if ($conflictingAppointment) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This time slot is already booked for the selected doctor.');
        }

        try {
            $doctor = Doctor::forBusiness(Auth::user()->business_id)
                ->findOrFail($request->doctor_id);

            $appointment->update([
                'patient_id' => $request->patient_id,
                'doctor_id' => $request->doctor_id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'duration' => $request->duration,
                'appointment_type' => $request->appointment_type,
                'chief_complaint' => $request->chief_complaint,
                'notes' => $request->notes,
                'priority' => $request->priority,
                'consultation_fee' => $request->consultation_fee ?: $doctor->consultation_fee
            ]);

            return redirect()->route('admin.appointments.show', $appointment)
                ->with('success', 'Appointment updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update appointment. Please try again.');
        }
    }

    /**
     * Remove the specified appointment
     */
    public function destroy(Appointment $appointment)
    {
        // Check if appointment belongs to current user's business
        if ($appointment->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this appointment.');
        }

        if (!$appointment->canBeDeleted()) {
            return redirect()->back()
                ->with('error', 'This appointment cannot be deleted.');
        }

        $appointment->delete();

        return redirect()->route('admin.appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }

    /**
     * Confirm appointment
     */
    public function confirm(Appointment $appointment)
    {
        if ($appointment->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this appointment.');
        }

        if ($appointment->status !== 'scheduled') {
            return redirect()->back()
                ->with('error', 'Only scheduled appointments can be confirmed.');
        }

        $appointment->confirm();

        return redirect()->back()
            ->with('success', 'Appointment confirmed successfully.');
    }

    /**
     * Complete appointment
     */
    public function complete(Appointment $appointment)
    {
        if ($appointment->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this appointment.');
        }

        if (!$appointment->canBeCompleted()) {
            return redirect()->back()
                ->with('error', 'This appointment cannot be marked as completed.');
        }

        $appointment->markAsCompleted();

        return redirect()->back()
            ->with('success', 'Appointment marked as completed.');
    }

    /**
     * Cancel appointment
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        if ($appointment->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this appointment.');
        }

        if (!$appointment->canBeCancelled()) {
            return redirect()->back()
                ->with('error', 'This appointment cannot be cancelled.');
        }

        $appointment->cancel(
            $request->get('cancellation_reason', 'Cancelled by staff'),
            Auth::id()
        );

        return redirect()->back()
            ->with('success', 'Appointment cancelled successfully.');
    }

    /**
     * Mark appointment as no show
     */
    public function noShow(Appointment $appointment)
    {
        if ($appointment->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this appointment.');
        }

        if (!in_array($appointment->status, ['scheduled', 'confirmed'])) {
            return redirect()->back()
                ->with('error', 'Only scheduled or confirmed appointments can be marked as no show.');
        }

        $appointment->markAsNoShow();

        return redirect()->back()
            ->with('success', 'Appointment marked as no show.');
    }

    /**
     * Export appointments to CSV
     */
    public function export(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor'])
            ->forBusiness(Auth::user()->business_id);

        // Apply same filters as index
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('date_from')) {
            $query->where('appointment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('appointment_date', '<=', $request->date_to);
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->get();

        $filename = 'appointments_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($appointments) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Date',
                'Time',
                'Patient Name',
                'Patient Email',
                'Doctor Name',
                'Type',
                'Status',
                'Priority',
                'Chief Complaint',
                'Duration (mins)',
                'Consultation Fee',
                'Payment Status',
                'Notes',
                'Created At'
            ]);

            // CSV data
            foreach ($appointments as $appointment) {
                fputcsv($file, [
                    $appointment->appointment_date->format('Y-m-d'),
                    $appointment->appointment_time->format('H:i'),
                    $appointment->patient->name,
                    $appointment->patient->email,
                    $appointment->doctor->name,
                    ucfirst(str_replace('_', ' ', $appointment->appointment_type)),
                    ucfirst($appointment->status),
                    ucfirst($appointment->priority),
                    $appointment->chief_complaint,
                    $appointment->duration,
                    $appointment->consultation_fee,
                    ucfirst(str_replace('_', ' ', $appointment->payment_status)),
                    $appointment->notes,
                    $appointment->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get appointment statistics
     */
    private function getAppointmentStats()
    {
        $businessId = Auth::user()->business_id;

        return [
            'today_total' => Appointment::forBusiness($businessId)->today()->count(),
            'today_completed' => Appointment::forBusiness($businessId)->today()->where('status', 'completed')->count(),
            'upcoming' => Appointment::forBusiness($businessId)->upcoming()->count(),
            'this_month' => Appointment::forBusiness($businessId)
                ->whereMonth('appointment_date', now()->month)
                ->whereYear('appointment_date', now()->year)
                ->count(),
            'active_count' => Appointment::forBusiness($businessId)
                ->whereIn('status', ['scheduled', 'confirmed', 'in_progress'])
                ->count(),
            'cancelled_today' => Appointment::forBusiness($businessId)->today()->where('status', 'cancelled')->count(),
            'no_show_today' => Appointment::forBusiness($businessId)->today()->where('status', 'no_show')->count(),
        ];
    }

    /**
     * Process waiting list for available slot
     */
    private function processWaitingListForSlot($doctorId, $date, $time)
    {
        try {
            // Find matching waiting list entries
            $matchingEntries = WaitingList::where('status', 'waiting')
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->where('doctor_id', $doctorId)
                ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
                ->get()
                ->filter(function ($entry) use ($date, $time) {
                    return $entry->matchesSlot($date, $time);
                });

            // Notify up to 3 patients about the available slot
            $notifiedCount = 0;
            foreach ($matchingEntries->take(3) as $entry) {
                try {
                    $entry->notify();
                    $notifiedCount++;
                } catch (\Exception $e) {
                    // Log error but continue
                    Log::error("Failed to notify waiting list entry {$entry->id}: " . $e->getMessage());
                }
            }

            if ($notifiedCount > 0) {
                Log::info("Notified {$notifiedCount} patients about available slot for doctor {$doctorId} on {$date} at {$time}");
            }
        } catch (\Exception $e) {
            Log::error("Error processing waiting list for slot: " . $e->getMessage());
        }
    }

    /**
     * Get available time slots for a doctor on a specific date
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today'
        ]);

        // Verify doctor belongs to current business
        $doctor = Doctor::forBusiness(Auth::user()->business_id)
            ->findOrFail($request->doctor_id);

        $date = Carbon::parse($request->date);
        $dayOfWeek = strtolower($date->format('l'));

        // Check if doctor works on this day
        if (!$doctor->available_days || !in_array($dayOfWeek, $doctor->available_days)) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor is not available on ' . $date->format('l'),
                'slots' => []
            ]);
        }

        // Get doctor's working hours
        $startTime = $doctor->start_time ? Carbon::parse($doctor->start_time) : Carbon::parse('09:00');
        $endTime = $doctor->end_time ? Carbon::parse($doctor->end_time) : Carbon::parse('17:00');

        // Get existing appointments for this doctor on this date
        $existingAppointments = Appointment::where('doctor_id', $doctor->id)
            ->where('appointment_date', $request->date)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->get()
            ->keyBy(function ($appointment) {
                return $appointment->appointment_time->format('H:i');
            });

        // Get schedule exceptions for this date
        $exceptions = DoctorScheduleException::forDoctor($doctor->id)
            ->forDate($request->date)
            ->get();

        // Generate time slots (30-minute intervals)
        $slots = [];
        $currentTime = $startTime->copy();
        $slotDuration = 30; // minutes

        while ($currentTime->lt($endTime)) {
            $timeSlot = $currentTime->format('H:i');
            $endSlotTime = $currentTime->copy()->addMinutes($slotDuration);

            $isAvailable = true;
            $reason = null;

            // Check if slot conflicts with existing appointment
            if ($existingAppointments->has($timeSlot)) {
                $appointment = $existingAppointments->get($timeSlot);
                $isAvailable = false;
                $reason = 'Booked - ' . $appointment->patient->name;
            }

            // Check for overlapping appointments
            if ($isAvailable) {
                foreach ($existingAppointments as $appointment) {
                    $appointmentStart = Carbon::parse($appointment->appointment_time);
                    $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->duration);

                    // Check if current slot overlaps with existing appointment
                    if (($currentTime->gte($appointmentStart) && $currentTime->lt($appointmentEnd)) ||
                        ($endSlotTime->gt($appointmentStart) && $endSlotTime->lte($appointmentEnd)) ||
                        ($currentTime->lte($appointmentStart) && $endSlotTime->gte($appointmentEnd))
                    ) {
                        $isAvailable = false;
                        $reason = 'Booked - ' . $appointment->patient->name;
                        break;
                    }
                }
            }

            // Check schedule exceptions
            if ($isAvailable) {
                foreach ($exceptions as $exception) {
                    if ($exception->affectsTime($timeSlot)) {
                        $isAvailable = $exception->is_available;
                        if (!$isAvailable) {
                            $reason = $exception->reason;
                        }
                        break;
                    }
                }
            }

            // Don't show past slots for today
            if ($date->isToday() && $currentTime->lt(now())) {
                $isAvailable = false;
                $reason = 'Past time';
            }

            $slots[] = [
                'time' => $timeSlot,
                'formatted_time' => $currentTime->format('h:i A'),
                'end_time' => $endSlotTime->format('H:i'),
                'formatted_end_time' => $endSlotTime->format('h:i A'),
                'available' => $isAvailable,
                'reason' => $reason,
                'duration' => $slotDuration
            ];

            $currentTime->addMinutes($slotDuration);
        }

        return response()->json([
            'success' => true,
            'date' => $request->date,
            'formatted_date' => $date->format('l, M d, Y'),
            'doctor' => [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'specialization' => $doctor->specialization,
                'consultation_fee' => $doctor->consultation_fee
            ],
            'slots' => $slots,
            'working_hours' => [
                'start' => $startTime->format('h:i A'),
                'end' => $endTime->format('h:i A')
            ]
        ]);
    }

    /**
     * Get available slots for multiple dates (for calendar view)
     */
    public function getAvailableSlotsRange(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'duration' => 'nullable|integer|min:15|max:240'
        ]);

        $doctor = Doctor::forBusiness(Auth::user()->business_id)
            ->findOrFail($request->doctor_id);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $duration = $request->get('duration', 30);

        $availableSlots = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dayOfWeek = strtolower($currentDate->format('l'));

            // Check if doctor works on this day
            if ($doctor->available_days && in_array($dayOfWeek, $doctor->available_days)) {
                $slotsResponse = $this->getAvailableSlots(new Request([
                    'doctor_id' => $request->doctor_id,
                    'date' => $currentDate->format('Y-m-d')
                ]));

                $slotsData = $slotsResponse->getData(true);

                if ($slotsData['success']) {
                    $availableSlots[$currentDate->format('Y-m-d')] = array_filter(
                        $slotsData['slots'],
                        function ($slot) {
                            return $slot['available'];
                        }
                    );
                }
            }

            $currentDate->addDay();
        }

        return response()->json([
            'success' => true,
            'doctor' => [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'specialization' => $doctor->specialization
            ],
            'date_range' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'available_slots' => $availableSlots,
            'total_days' => count($availableSlots),
            'total_slots' => array_sum(array_map('count', $availableSlots))
        ]);
    }

    /**
     * Check if a specific time slot is available
     */
    public function checkSlotAvailability(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'duration' => 'nullable|integer|min:15|max:240',
            'exclude_appointment_id' => 'nullable|exists:appointments,id'
        ]);

        $doctor = Doctor::forBusiness(Auth::user()->business_id)
            ->findOrFail($request->doctor_id);

        $date = Carbon::parse($request->date);
        $time = Carbon::parse($request->time);
        $duration = $request->get('duration', 30);
        $endTime = $time->copy()->addMinutes($duration);

        // Check if doctor works on this day
        $dayOfWeek = strtolower($date->format('l'));
        if (!$doctor->available_days || !in_array($dayOfWeek, $doctor->available_days)) {
            return response()->json([
                'available' => false,
                'reason' => 'Doctor is not available on ' . $date->format('l')
            ]);
        }

        // Check working hours
        $startWorkTime = $doctor->start_time ? Carbon::parse($doctor->start_time) : Carbon::parse('09:00');
        $endWorkTime = $doctor->end_time ? Carbon::parse($doctor->end_time) : Carbon::parse('17:00');

        if ($time->lt($startWorkTime) || $endTime->gt($endWorkTime)) {
            return response()->json([
                'available' => false,
                'reason' => 'Outside working hours (' . $startWorkTime->format('h:i A') . ' - ' . $endWorkTime->format('h:i A') . ')'
            ]);
        }

        // Check for conflicting appointments
        $query = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->date)
            ->whereNotIn('status', ['cancelled', 'no_show']);

        if ($request->filled('exclude_appointment_id')) {
            $query->where('id', '!=', $request->exclude_appointment_id);
        }

        $conflictingAppointments = $query->get();

        foreach ($conflictingAppointments as $appointment) {
            $appointmentStart = Carbon::parse($appointment->appointment_time);
            $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->duration);

            // Check for overlap
            if (($time->gte($appointmentStart) && $time->lt($appointmentEnd)) ||
                ($endTime->gt($appointmentStart) && $endTime->lte($appointmentEnd)) ||
                ($time->lte($appointmentStart) && $endTime->gte($appointmentEnd))
            ) {
                return response()->json([
                    'available' => false,
                    'reason' => 'Conflicts with existing appointment (' . $appointmentStart->format('h:i A') . ' - ' . $appointmentEnd->format('h:i A') . ')'
                ]);
            }
        }

        // Check schedule exceptions
        $exceptions = DoctorScheduleException::forDoctor($request->doctor_id)
            ->forDate($request->date)
            ->get();

        foreach ($exceptions as $exception) {
            if ($exception->affectsTime($request->time)) {
                if (!$exception->is_available) {
                    return response()->json([
                        'available' => false,
                        'reason' => $exception->reason
                    ]);
                }
            }
        }

        // Check if it's in the past
        if ($date->isToday() && $time->lt(now())) {
            return response()->json([
                'available' => false,
                'reason' => 'Past time'
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => 'Time slot is available'
        ]);
    }

    /**
     * Bulk actions for appointments
     */
    public function bulkActions(Request $request)
    {
        $request->validate([
            'action' => 'required|in:confirm,cancel,complete,delete',
            'appointment_ids' => 'required|array',
            'appointment_ids.*' => 'exists:appointments,id',
            'cancellation_reason' => 'required_if:action,cancel|string|max:500'
        ]);

        $appointments = Appointment::whereIn('id', $request->appointment_ids)
            ->forBusiness(Auth::user()->business_id)
            ->get();

        $count = 0;
        DB::beginTransaction();

        try {
            foreach ($appointments as $appointment) {
                switch ($request->action) {
                    case 'confirm':
                        if ($appointment->status === 'scheduled') {
                            $appointment->confirm();
                            $count++;
                        }
                        break;
                    case 'cancel':
                        if ($appointment->canBeCancelled()) {
                            $appointment->cancel($request->cancellation_reason, Auth::id());
                            $count++;
                        }
                        break;
                    case 'complete':
                        if ($appointment->canBeCompleted()) {
                            $appointment->markAsCompleted();
                            $count++;
                        }
                        break;
                    case 'delete':
                        if ($appointment->canBeDeleted()) {
                            $appointment->delete();
                            $count++;
                        }
                        break;
                }
            }

            DB::commit();

            $actionText = [
                'confirm' => 'confirmed',
                'cancel' => 'cancelled',
                'complete' => 'completed',
                'delete' => 'deleted'
            ];

            return redirect()->back()->with('success', "{$count} appointments {$actionText[$request->action]} successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to perform bulk action. Please try again.');
        }
    }

    /**
     * Display calendar view with appointments
     */
    public function calendar(Request $request)
    {
        $doctors = Doctor::forBusiness(Auth::user()->business_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'specialization', 'consultation_fee', 'calendar_color']);

        $patients = User::where('business_id', Auth::user()->business_id)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'patient');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);

        return view('admin.calendar.index', compact('doctors', 'patients'));
    }

    /**
     * Search doctors for dropdown
     */
    public function searchDoctors(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        $search = $request->get('search', '');
        $limit = $request->get('limit', 20);

        $query = Doctor::forBusiness(Auth::user()->business_id)
            ->where('is_active', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('specialization', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $doctors = $query->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name', 'specialization', 'consultation_fee', 'email', 'phone']);

        return response()->json([
            'success' => true,
            'doctors' => $doctors->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'specialization' => $doctor->specialization,
                    'consultation_fee' => $doctor->consultation_fee,
                    'email' => $doctor->email,
                    'phone' => $doctor->phone,
                    'display_name' => "Dr. {$doctor->name} - {$doctor->specialization}",
                    'subtitle' => "Fee: $" . number_format($doctor->consultation_fee, 2)
                ];
            })
        ]);
    }

    /**
     * Search patients for dropdown
     */
    public function searchPatients(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        $search = $request->get('search', '');
        $limit = $request->get('limit', 20);

        // Query the patients table
        $query = \App\Models\Patient::where('patients.business_id', Auth::user()->business_id) // Specify table name
            ->where('is_active', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('patient_id', 'LIKE', "%{$search}%");
            });
        }

        // Get recent patients first (those with recent appointments) - FIX THE AMBIGUOUS COLUMN
        $recentPatientIds = Appointment::select('patients.patient_id')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->where('appointments.business_id', Auth::user()->business_id) // Specify table name
            ->where('appointment_date', '>=', now()->subDays(30))
            ->distinct()
            ->pluck('patients.patient_id')
            ->toArray();

        $patients = $query->orderByRaw($recentPatientIds ? "FIELD(patients.patient_id, '" . implode("','", $recentPatientIds) . "') DESC" : "1")
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->limit($limit)
            ->get(['id', 'patient_id', 'first_name', 'last_name', 'full_name', 'email', 'phone', 'date_of_birth']);

        return response()->json([
            'success' => true,
            'patients' => $patients->map(function ($patient) use ($recentPatientIds) {
                return [
                    'id' => $patient->patient_id, // Use patient_id (TH-001) as the value
                    'database_id' => $patient->id, // Keep database id for reference if needed
                    'patient_id' => $patient->patient_id,
                    'name' => $patient->full_name,
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'email' => $patient->email,
                    'phone' => $patient->phone,
                    'date_of_birth' => $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : null,
                    'display_name' => $patient->full_name . " ({$patient->patient_id})",
                    'subtitle' => ($patient->email ?: 'No email') . ($patient->phone ? " • {$patient->phone}" : ""),
                    'is_recent' => in_array($patient->patient_id, $recentPatientIds)
                ];
            })
        ]);
    }



    /**
     * Quick create appointment (AJAX endpoint)
     */
    public function quickStore(Request $request)
    {
        try {
            $request->validate([
                'patient_id' => 'required|exists:users,id',
                'doctor_id' => 'required|exists:doctors,id',
                'appointment_date' => 'required|date|after_or_equal:today',
                'appointment_time' => 'required|date_format:H:i',
                'appointment_type' => 'required|in:consultation,follow_up,emergency,checkup',
                'duration' => 'nullable|integer|min:15|max:240',
                'priority' => 'nullable|in:low,medium,high,urgent',
                'notes' => 'nullable|string|max:1000'
            ]);

            // Verify doctor belongs to current business
            $doctor = Doctor::forBusiness(Auth::user()->business_id)
                ->findOrFail($request->doctor_id);

            // Verify patient belongs to current business
            $patient = User::where('business_id', Auth::user()->business_id)
                ->findOrFail($request->patient_id);

            // Set defaults based on appointment type
            $duration = $request->get('duration') ?: $this->getDefaultDuration($request->appointment_type);
            $priority = $request->get('priority') ?: 'medium';

            // Check for conflicts
            $conflictingAppointment = Appointment::forBusiness(Auth::user()->business_id)
                ->where('doctor_id', $request->doctor_id)
                ->where('appointment_date', $request->appointment_date)
                ->where('appointment_time', $request->appointment_time)
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->exists();

            if ($conflictingAppointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'This time slot is already booked for the selected doctor.',
                    'errors' => [
                        'appointment_time' => ['This time slot is already booked.']
                    ]
                ], 422);
            }

            $appointment = Appointment::create([
                'business_id' => Auth::user()->business_id,
                'patient_id' => $request->patient_id,
                'doctor_id' => $request->doctor_id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'duration' => $duration,
                'appointment_type' => $request->appointment_type,
                'notes' => $request->notes,
                'priority' => $priority,
                'consultation_fee' => $doctor->consultation_fee,
                'payment_status' => 'pending',
                'status' => 'scheduled',
                'created_by' => Auth::id()
            ]);

            // Process waiting list
            $this->processWaitingListForSlot(
                $request->doctor_id,
                $request->appointment_date,
                $request->appointment_time
            );

            return response()->json([
                'success' => true,
                'message' => 'Appointment created successfully!',
                'appointment' => [
                    'id' => $appointment->id,
                    'patient_name' => $patient->name,
                    'doctor_name' => $doctor->name,
                    'appointment_date' => $appointment->appointment_date->format('Y-m-d'),
                    'appointment_time' => $appointment->appointment_time->format('H:i'),
                    'status' => $appointment->status,
                    'appointment_type' => $appointment->appointment_type
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please check the form for errors.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Quick appointment creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create appointment. Please try again.'
            ], 500);
        }
    }

    /**
     * Get default duration based on appointment type
     */
    private function getDefaultDuration($type)
    {
        return match ($type) {
            'consultation' => 30,
            'follow_up' => 20,
            'emergency' => 45,
            'checkup' => 25,
            default => 30
        };
    }


    /**
     * Get status color for calendar events
     */
    private function getStatusColor($status)
    {
        return match ($status) {
            'scheduled' => '#007bff',
            'confirmed' => '#28a745',
            'in_progress' => '#ffc107',
            'completed' => '#6c757d',
            'cancelled' => '#dc3545',
            'no_show' => '#fd7e14',
            default => '#6c757d'
        };
    }
}
