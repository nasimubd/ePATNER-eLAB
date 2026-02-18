<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorScheduleException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Display the calendar view
     */
    public function index(Request $request)
    {
        $doctors = Doctor::forBusiness(Auth::user()->business_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedDoctor = null;
        if ($request->filled('doctor_id')) {
            $selectedDoctor = $doctors->find($request->doctor_id);
        }

        // Get today's appointments
        $todayAppointments = Appointment::with(['patient', 'doctor'])
            ->forBusiness(Auth::user()->business_id)
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->get();

        // Get patients for the quick create form
        $patients = User::where('business_id', Auth::user()->business_id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'patient');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.calendar.index', compact(
            'doctors',
            'selectedDoctor',
            'todayAppointments',
            'patients'
        ));
    }

    /**
     * Get calendar events (appointments) as JSON
     */
    public function getEvents(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
            'doctor_id' => 'nullable|exists:doctors,id'
        ]);

        $query = Appointment::with(['patient', 'doctor'])
            ->forBusiness(Auth::user()->business_id)
            ->whereBetween('appointment_date', [$request->start, $request->end]);

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        $appointments = $query->get();

        $events = $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'title' => $appointment->patient->name . ' - ' . $appointment->doctor->name,
                'start' => $appointment->appointment_date->format('Y-m-d') . 'T' . $appointment->appointment_time->format('H:i:s'),
                'end' => $appointment->appointment_date->format('Y-m-d') . 'T' . $appointment->end_time->format('H:i:s'),
                'backgroundColor' => $this->getStatusColor($appointment->status),
                'borderColor' => $this->getStatusColor($appointment->status),
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'patient_name' => $appointment->patient->name,
                    'doctor_name' => $appointment->doctor->name,
                    'status' => $appointment->status,
                    'type' => $appointment->appointment_type,
                    'complaint' => $appointment->chief_complaint,
                    'phone' => $appointment->patient->phone ?? '',
                    'fee' => $appointment->consultation_fee
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * Get calendar data for appointments (used by JavaScript)
     */
    public function getCalendarData(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ]);

        $appointments = Appointment::with(['patient', 'doctor'])
            ->forBusiness(Auth::user()->business_id)
            ->whereBetween('appointment_date', [$request->start_date, $request->end_date])
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'patient_name' => $appointment->patient->name,
                    'doctor_name' => $appointment->doctor->name,
                    'doctor_id' => $appointment->doctor_id,
                    'appointment_date' => $appointment->appointment_date->format('Y-m-d'),
                    'appointment_time' => $appointment->appointment_time->format('H:i'),
                    'duration' => $appointment->duration,
                    'appointment_type' => $appointment->appointment_type,
                    'status' => $appointment->status,
                    'consultation_fee' => $appointment->consultation_fee,
                    'chief_complaint' => $appointment->chief_complaint
                ];
            });

        $todayAppointments = Appointment::with(['patient', 'doctor'])
            ->forBusiness(Auth::user()->business_id)
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'patient_name' => $appointment->patient->name,
                    'doctor_name' => $appointment->doctor->name,
                    'appointment_time' => $appointment->appointment_time->format('H:i'),
                    'status' => $appointment->status
                ];
            });

        return response()->json([
            'appointments' => $appointments,
            'today_appointments' => $todayAppointments
        ]);
    }

    public function getAppointments(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $query = Appointment::with(['patient', 'doctor'])
            ->whereBetween('appointment_date', [$start, $end]);

        if ($request->has('doctor_id') && $request->doctor_id !== 'all') {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $appointments = $query->get()->map(function ($appointment) {
            // Handle patient name safely
            $patientName = null;
            if ($appointment->patient) {
                // If patient relationship exists, use the name
                $patientName = $appointment->patient->name ?? $appointment->patient->full_name ?? null;
            }

            // Handle doctor name safely
            $doctorName = null;
            if ($appointment->doctor) {
                $doctorName = $appointment->doctor->name;
            }

            return [
                'id' => $appointment->id,
                'patient_name' => $patientName,
                'doctor_name' => $doctorName,
                'doctor_id' => $appointment->doctor_id,
                'appointment_date' => $appointment->appointment_date->format('Y-m-d'),
                'appointment_time' => $appointment->appointment_time->format('H:i'),
                'appointment_type' => $appointment->appointment_type,
                'status' => $appointment->status,
                'duration' => $appointment->duration ?? 30,
            ];
        });

        return response()->json(['appointments' => $appointments]);
    }


    /**
     * Get doctor schedule exceptions as events
     */
    public function getScheduleExceptions(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
            'doctor_id' => 'nullable|exists:doctors,id'
        ]);

        $query = DoctorScheduleException::with('doctor')
            ->whereHas('doctor', function ($q) {
                $q->forBusiness(Auth::user()->business_id);
            })
            ->whereBetween('date', [$request->start, $request->end]);

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        $exceptions = $query->get();

        $events = $exceptions->map(function ($exception) {
            $start = $exception->date->format('Y-m-d');
            $end = $exception->date->format('Y-m-d');

            if ($exception->start_time && $exception->end_time) {
                $start .= 'T' . $exception->start_time->format('H:i:s');
                $end .= 'T' . $exception->end_time->format('H:i:s');
            } else {
                // All day event
                $end = $exception->date->addDay()->format('Y-m-d');
            }

            return [
                'id' => 'exception_' . $exception->id,
                'title' => ($exception->is_available ? 'Extra: ' : 'Unavailable: ') . $exception->reason,
                'start' => $start,
                'end' => $end,
                'backgroundColor' => $exception->is_available ? '#28a745' : '#dc3545',
                'borderColor' => $exception->is_available ? '#28a745' : '#dc3545',
                'textColor' => '#ffffff',
                'display' => $exception->start_time && $exception->end_time ? 'block' : 'background',
                'extendedProps' => [
                    'type' => 'schedule_exception',
                    'doctor_name' => $exception->doctor->name,
                    'is_available' => $exception->is_available,
                    'reason' => $exception->reason
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * Create appointment from calendar
     */
    public function createAppointment(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'duration' => 'required|integer|min:15|max:240',
            'appointment_type' => 'required|in:consultation,follow_up,emergency,checkup',
            'chief_complaint' => 'nullable|string|max:500'
        ]);

        // Verify doctor belongs to current business
        $doctor = Doctor::forBusiness(Auth::user()->business_id)
            ->findOrFail($request->doctor_id);

        // Check if slot is available
        $existingAppointment = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->exists();

        if ($existingAppointment) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot is already booked.'
            ], 400);
        }

        try {
            $appointment = Appointment::create([
                'business_id' => Auth::user()->business_id,
                'patient_id' => $request->patient_id,
                'doctor_id' => $request->doctor_id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'duration' => $request->duration,
                'appointment_type' => $request->appointment_type,
                'chief_complaint' => $request->chief_complaint,
                'consultation_fee' => $doctor->consultation_fee,
                'status' => 'scheduled',
                'priority' => 'medium',
                'payment_status' => 'pending',
                'created_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment created successfully.',
                'appointment' => $appointment->load(['patient', 'doctor'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update appointment from calendar (drag & drop)
     */
    public function updateAppointment(Request $request, Appointment $appointment)
    {
        // Check if appointment belongs to current user's business
        if ($appointment->business_id !== Auth::user()->business_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i'
        ]);

        if (!$appointment->canBeRescheduled()) {
            return response()->json([
                'success' => false,
                'message' => 'This appointment cannot be rescheduled.'
            ], 400);
        }

        // Check if new slot is available
        $existingAppointment = Appointment::where('doctor_id', $appointment->doctor_id)
            ->where('appointment_date', $request->date)
            ->where('appointment_time', $request->time)
            ->where('id', '!=', $appointment->id)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->exists();

        if ($existingAppointment) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot is already booked.'
            ], 400);
        }

        try {
            $appointment->update([
                'appointment_date' => $request->date,
                'appointment_time' => $request->time
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment rescheduled successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reschedule appointment.'
            ], 500);
        }
    }

    /**
     * Export calendar to various formats
     */
    public function exportCalendar(Request $request)
    {
        $request->validate([
            'format' => 'required|in:pdf,csv',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'doctors' => 'nullable|string',
            'filter' => 'nullable|string'
        ]);

        $query = Appointment::with(['patient', 'doctor'])
            ->forBusiness(Auth::user()->business_id)
            ->whereBetween('appointment_date', [$request->start_date, $request->end_date]);

        // Apply doctor filter
        if ($request->doctors && $request->doctors !== 'all') {
            $doctorIds = explode(',', $request->doctors);
            $query->whereIn('doctor_id', $doctorIds);
        }

        // Apply status filter
        if ($request->filter && $request->filter !== 'all') {
            switch ($request->filter) {
                case 'confirmed':
                    $query->where('status', 'confirmed');
                    break;
                case 'scheduled':
                    $query->where('status', 'scheduled');
                    break;
                case 'today':
                    $query->whereDate('appointment_date', today());
                    break;
                case 'this_week':
                    $query->whereBetween('appointment_date', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]);
                    break;
            }
        }

        $appointments = $query->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        if ($request->format === 'csv') {
            return $this->exportToCsv($appointments);
        }

        // For PDF export, you would implement PDF generation here
        return response()->json(['message' => 'PDF export not implemented yet']);
    }

    /**
     * Export appointments to CSV
     */
    private function exportToCsv($appointments)
    {
        $filename = 'calendar_export_' . now()->format('Y_m_d_H_i_s') . '.csv';

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
                'Doctor Specialization',
                'Appointment Type',
                'Status',
                'Duration (mins)',
                'Consultation Fee',
                'Chief Complaint'
            ]);

            // CSV data
            foreach ($appointments as $appointment) {
                fputcsv($file, [
                    $appointment->appointment_date->format('Y-m-d'),
                    $appointment->appointment_time->format('H:i'),
                    $appointment->patient->name,
                    $appointment->patient->email,
                    $appointment->doctor->name,
                    $appointment->doctor->specialization,
                    ucfirst(str_replace('_', ' ', $appointment->appointment_type)),
                    ucfirst($appointment->status),
                    $appointment->duration,
                    number_format($appointment->consultation_fee, 2),
                    $appointment->chief_complaint ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get available time slots for a specific doctor and date
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today'
        ]);

        $doctor = Doctor::forBusiness(Auth::user()->business_id)
            ->findOrFail($request->doctor_id);

        $slots = $this->generateAvailableSlots($doctor, $request->date);

        return response()->json([
            'success' => true,
            'slots' => $slots,
            'doctor' => [
                'name' => $doctor->name,
                'specialization' => $doctor->specialization
            ]
        ]);
    }

    /**
     * Generate available time slots for a doctor on a specific date
     */
    private function generateAvailableSlots($doctor, $date)
    {
        $slots = [];
        $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));

        // Check if doctor works on this day
        if (!$doctor->available_days || !in_array($dayOfWeek, $doctor->available_days)) {
            return $slots;
        }

        // Get doctor's working hours
        $startTime = $doctor->start_time ? Carbon::parse($doctor->start_time) : Carbon::parse('09:00');
        $endTime = $doctor->end_time ? Carbon::parse($doctor->end_time) : Carbon::parse('17:00');

        // Get existing appointments
        $existingAppointments = Appointment::where('doctor_id', $doctor->id)
            ->where('appointment_date', $date)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->pluck('appointment_time')
            ->map(function ($time) {
                return Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        // Get schedule exceptions for this date
        $exceptions = DoctorScheduleException::forDoctor($doctor->id)
            ->forDate($date)
            ->get();

        // Generate 30-minute slots
        $currentTime = $startTime->copy();

        while ($currentTime->lt($endTime)) {
            $timeSlot = $currentTime->format('H:i');
            $isAvailable = true;

            // Check if slot is already booked
            if (in_array($timeSlot, $existingAppointments)) {
                $isAvailable = false;
            }

            // Check schedule exceptions
            foreach ($exceptions as $exception) {
                if ($exception->affectsTime($timeSlot)) {
                    $isAvailable = $exception->is_available;
                    break;
                }
            }

            $slots[] = [
                'time' => $timeSlot,
                'formatted_time' => $currentTime->format('h:i A'),
                'available' => $isAvailable,
                'reason' => !$isAvailable ? $this->getUnavailabilityReason($timeSlot, $existingAppointments, $exceptions) : null
            ];

            // Move to next 30-minute slot
            $currentTime->addMinutes(30);
        }

        return $slots;
    }

    /**
     * Get reason for unavailability
     */
    private function getUnavailabilityReason($timeSlot, $existingAppointments, $exceptions)
    {
        if (in_array($timeSlot, $existingAppointments)) {
            return 'Appointment booked';
        }

        foreach ($exceptions as $exception) {
            if ($exception->affectsTime($timeSlot) && !$exception->is_available) {
                return $exception->reason;
            }
        }

        return 'Unavailable';
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

    /**
     * Get calendar statistics
     */
    public function getCalendarStats(Request $request)
    {
        $businessId = Auth::user()->business_id;
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $stats = [
            'total_appointments' => Appointment::forBusiness($businessId)
                ->whereBetween('appointment_date', [$startDate, $endDate])
                ->count(),
            'confirmed_appointments' => Appointment::forBusiness($businessId)
                ->whereBetween('appointment_date', [$startDate, $endDate])
                ->where('status', 'confirmed')
                ->count(),
            'completed_appointments' => Appointment::forBusiness($businessId)
                ->whereBetween('appointment_date', [$startDate, $endDate])
                ->where('status', 'completed')
                ->count(),
            'cancelled_appointments' => Appointment::forBusiness($businessId)
                ->whereBetween('appointment_date', [$startDate, $endDate])
                ->where('status', 'cancelled')
                ->count(),
            'today_appointments' => Appointment::forBusiness($businessId)
                ->whereDate('appointment_date', today())
                ->count(),
            'upcoming_appointments' => Appointment::forBusiness($businessId)
                ->where('appointment_date', '>', today())
                ->whereNotIn('status', ['cancelled', 'completed'])
                ->count()
        ];

        return response()->json($stats);
    }

    /**
     * Search appointments for calendar
     */
    public function searchAppointments(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2'
        ]);

        $appointments = Appointment::with(['patient', 'doctor'])
            ->forBusiness(Auth::user()->business_id)
            ->where(function ($q) use ($request) {
                $q->whereHas('patient', function ($pq) use ($request) {
                    $pq->where('name', 'LIKE', "%{$request->query}%")
                        ->orWhere('email', 'LIKE', "%{$request->query}%");
                })
                    ->orWhereHas('doctor', function ($dq) use ($request) {
                        $dq->where('name', 'LIKE', "%{$request->query}%");
                    })
                    ->orWhere('chief_complaint', 'LIKE', "%{$request->query}%");
            })
            ->orderBy('appointment_date', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'patient_name' => $appointment->patient->name,
                    'doctor_name' => $appointment->doctor->name,
                    'appointment_date' => $appointment->appointment_date->format('Y-m-d'),
                    'appointment_time' => $appointment->appointment_time->format('H:i'),
                    'status' => $appointment->status,
                    'type' => $appointment->appointment_type
                ];
            });

        return response()->json($appointments);
    }

    /**
     * Get doctor's schedule for a specific date range
     */
    public function getDoctorSchedule(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);

        $doctor = Doctor::forBusiness(Auth::user()->business_id)
            ->findOrFail($request->doctor_id);

        $appointments = Appointment::with('patient')
            ->where('doctor_id', $request->doctor_id)
            ->whereBetween('appointment_date', [$request->start_date, $request->end_date])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'patient_name' => $appointment->patient->name,
                    'date' => $appointment->appointment_date->format('Y-m-d'),
                    'time' => $appointment->appointment_time->format('H:i'),
                    'duration' => $appointment->duration,
                    'status' => $appointment->status,
                    'type' => $appointment->appointment_type
                ];
            });

        $exceptions = DoctorScheduleException::forDoctor($request->doctor_id)
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->get()
            ->map(function ($exception) {
                return [
                    'id' => $exception->id,
                    'date' => $exception->date->format('Y-m-d'),
                    'start_time' => $exception->start_time ? $exception->start_time->format('H:i') : null,
                    'end_time' => $exception->end_time ? $exception->end_time->format('H:i') : null,
                    'is_available' => $exception->is_available,
                    'reason' => $exception->reason
                ];
            });

        return response()->json([
            'doctor' => [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'specialization' => $doctor->specialization,
                'available_days' => $doctor->available_days,
                'start_time' => $doctor->start_time,
                'end_time' => $doctor->end_time
            ],
            'appointments' => $appointments,
            'exceptions' => $exceptions
        ]);
    }
}
