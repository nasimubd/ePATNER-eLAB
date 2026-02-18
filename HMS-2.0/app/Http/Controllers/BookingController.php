<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Patient;
use App\Models\WardService;
use App\Models\OtService;
use App\Models\OtRoom;
use App\Models\BookingStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $businessId = Auth::user()->business_id;

        $bookings = Booking::forBusiness($businessId)
            ->with(['patient', 'bookable', 'otRoom', 'createdBy'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('patient_id', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->booking_type, function ($query, $type) {
                $query->where('booking_type', $type);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->where('booking_date', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->where('booking_date', '<=', $date);
            })
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->paginate(15);

        return view('bookings.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        $businessId = Auth::user()->business_id;

        // Load patients with all necessary fields - same as appointments
        $patients = Patient::forBusiness($businessId)
            ->active()
            ->select('id', 'patient_id', 'first_name', 'last_name', 'phone', 'email')
            ->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")
            ->orderBy('first_name')
            ->get();

        $selectedPatient = null;
        if ($request->patient_id) {
            // Find by patient_id field, not database id
            $selectedPatient = Patient::forBusiness($businessId)
                ->where('patient_id', $request->patient_id)
                ->first();
        }

        return view('bookings.create', compact('patients', 'selectedPatient'));
    }




    public function store(Request $request)
    {
        $businessId = Auth::user()->business_id;

        $validated = $request->validate([
            'patient_id' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($businessId) {
                    $patient = Patient::forBusiness($businessId)->where('patient_id', $value)->first();
                    if (!$patient) {
                        $fail('The selected patient is invalid.');
                    }
                }
            ],
            'booking_type' => 'required|in:ward,ot',
            'bookable_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($request, $businessId) {
                    $bookingType = $request->input('booking_type');
                    if ($bookingType === 'ward') {
                        $service = WardService::forBusiness($businessId)->find($value);
                        if (!$service) {
                            $fail('The selected ward service is invalid.');
                        }
                    } elseif ($bookingType === 'ot') {
                        $service = OtService::forBusiness($businessId)->find($value);
                        if (!$service) {
                            $fail('The selected OT service is invalid.');
                        }
                    }
                }
            ],
            'ot_room_id' => 'required_if:booking_type,ot|nullable|exists:ot_rooms,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string',
            'special_requirements' => 'nullable|array',
        ]);

        // Get patient by patient_id field (not database id)
        $patient = Patient::forBusiness($businessId)->where('patient_id', $validated['patient_id'])->firstOrFail();

        DB::beginTransaction();
        try {
            // Get service and calculate fees
            if ($validated['booking_type'] === 'ward') {
                $service = WardService::forBusiness($businessId)->findOrFail($validated['bookable_id']);

                // Check availability for ward service
                $currentBookings = $service->bookings()
                    ->where('booking_date', $validated['booking_date'])
                    ->where('booking_time', $validated['booking_time'])
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->count();

                if ($currentBookings >= $service->max_patients_per_slot) {
                    return back()->withErrors(['booking_time' => 'This time slot is fully booked.']);
                }

                $bookingData = [
                    'bookable_type' => WardService::class,
                    'bookable_id' => $service->id,
                    'service_fee' => $service->daily_fee,
                    'end_time' => Carbon::createFromFormat('H:i', $validated['booking_time'])
                        ->addMinutes($service->duration_minutes)
                        ->format('H:i:s'),
                ];
            } else {
                $service = OtService::forBusiness($businessId)->findOrFail($validated['bookable_id']);
                $otRoom = OtRoom::forBusiness($businessId)->findOrFail($validated['ot_room_id']);

                // Check OT room availability
                $conflictingBookings = Booking::where('ot_room_id', $otRoom->id)
                    ->where('booking_date', $validated['booking_date'])
                    ->where('booking_time', $validated['booking_time'])
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->count();

                if ($conflictingBookings > 0) {
                    return back()->withErrors(['booking_time' => 'This OT room is not available at the selected time.']);
                }

                // Calculate total fee since it doesn't exist as a column
                $totalFee = $service->base_fee + $service->room_fee + $service->equipment_fee;

                $bookingData = [
                    'bookable_type' => OtService::class,
                    'bookable_id' => $service->id,
                    'ot_room_id' => $otRoom->id,
                    'service_fee' => $totalFee,
                    'preparation_time_minutes' => $service->preparation_time_minutes,
                    'cleanup_time_minutes' => $service->cleanup_time_minutes,
                    'end_time' => Carbon::createFromFormat('H:i', $validated['booking_time'])
                        ->addMinutes($service->estimated_duration_minutes + $service->preparation_time_minutes + $service->cleanup_time_minutes)
                        ->format('H:i:s'),
                ];
            }

            // Create booking with patient database id
            $booking = Booking::create(array_merge([
                'patient_id' => $patient->id, // Use database id for foreign key
                'booking_type' => $validated['booking_type'],
                'booking_date' => $validated['booking_date'],
                'booking_time' => $validated['booking_time'],
                'notes' => $validated['notes'],
            ], $bookingData, [
                'business_id' => $businessId,
                'user_id' => Auth::id(),
                'created_by' => Auth::id(),
                'status' => 'pending',
            ]));

            DB::commit();

            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Booking created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create booking. Please try again.' . $e->getMessage()]);
        }
    }



    /**
     * Display the specified booking
     */
    public function show(Booking $booking)
    {
        // Ensure user can only view bookings from their business
        if ($booking->business_id !== Auth::user()->business_id) {
            abort(404);
        }

        // Load necessary relationships
        $booking->load([
            'patient',
            'bookable',
            'otRoom',
            'createdBy',
            'updatedBy'
        ]);

        return view('bookings.show', compact('booking'));
    }

    /**
     * Print booking receipt
     */
    public function print(Booking $booking)
    {
        try {
            // Ensure user can only print bookings from their business
            if ($booking->business_id !== Auth::user()->business_id) {
                abort(404);
            }

            // Load necessary relationships
            $booking->load([
                'patient',
                'bookable',
                'otRoom',
                'createdBy',
                'business'
            ]);

            // Get hospital/business information
            $hospital = null;
            if ($booking->business) {
                $hospital = (object) [
                    'name' => $booking->business->hospital_name ?? $booking->business->name ?? config('app.name', 'Healthcare Clinic'),
                    'address' => $booking->business->address ?? 'Healthcare Address',
                    'phone' => $booking->business->contact_number ?? $booking->business->phone ?? '+880-XXXXXXXXX',
                    'emergency_contact' => $booking->business->emergency_contact ?? null,
                    'email' => $booking->business->email ?? null,
                    'website' => $booking->business->website ?? null,
                ];
            } else {
                // Fallback to default hospital/clinic info
                $hospital = (object) [
                    'name' => config('app.name', 'Healthcare Clinic'),
                    'address' => config('app.hospital_address', 'Healthcare Address'),
                    'phone' => config('app.hospital_phone', '+880-XXXXXXXXX'),
                    'emergency_contact' => config('app.hospital_emergency', null),
                    'email' => config('app.hospital_email', null),
                    'website' => config('app.hospital_website', null),
                ];
            }

            // Calculate fee breakdown
            $feeBreakdown = [
                'service_fee' => $booking->service_fee,
                'room_fee' => 0,
                'equipment_fee' => 0,
                'total' => $booking->service_fee,
            ];

            if ($booking->booking_type === 'ot' && $booking->bookable) {
                $feeBreakdown['room_fee'] = $booking->bookable->room_fee ?? 0;
                $feeBreakdown['equipment_fee'] = $booking->bookable->equipment_fee ?? 0;
                $feeBreakdown['total'] = $feeBreakdown['service_fee'] + $feeBreakdown['room_fee'] + $feeBreakdown['equipment_fee'];
            }

            return view('bookings.print', compact(
                'booking',
                'hospital',
                'feeBreakdown'
            ));
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error printing booking: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'error' => $e->getTraceAsString()
            ]);

            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Unable to print booking. Please try again.');
        }
    }


    public function edit(Booking $booking)
    {
        // Ensure user can only edit bookings from their business
        if ($booking->business_id !== Auth::user()->business_id) {
            abort(404);
        }

        // Only allow editing of pending bookings
        if ($booking->status !== 'pending') {
            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Only pending bookings can be edited.');
        }

        $businessId = Auth::user()->business_id;

        $patients = Patient::forBusiness($businessId)->active()->get();
        $wardServices = WardService::forBusiness($businessId)->active()->get();
        $otServices = OtService::forBusiness($businessId)->active()->get();
        $otRooms = OtRoom::forBusiness($businessId)->active()->get();

        return view('bookings.edit', compact('booking', 'patients', 'wardServices', 'otServices', 'otRooms'));
    }

    public function update(Request $request, Booking $booking)
    {
        // Ensure user can only update bookings from their business
        if ($booking->business_id !== Auth::user()->business_id) {
            abort(404);
        }

        // Only allow updating of pending bookings
        if ($booking->status !== 'pending') {
            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Only pending bookings can be updated.');
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'booking_type' => 'required|in:ward,ot',
            'service_id' => 'required|integer',
            'ot_room_id' => 'required_if:booking_type,ot|nullable|exists:ot_rooms,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string',
            'special_requirements' => 'nullable|array',
        ]);

        $businessId = Auth::user()->business_id;

        // Verify patient belongs to business
        $patient = Patient::forBusiness($businessId)->findOrFail($validated['patient_id']);

        DB::beginTransaction();
        try {
            // Store old values for history
            $oldValues = $booking->only(['booking_date', 'booking_time', 'status']);

            // Get service and recalculate fees
            if ($validated['booking_type'] === 'ward') {
                $service = WardService::forBusiness($businessId)->findOrFail($validated['service_id']);

                // Check availability (exclude current booking)
                $currentBookings = $service->bookings()
                    ->where('booking_date', $validated['booking_date'])
                    ->where('booking_time', $validated['booking_time'])
                    ->where('id', '!=', $booking->id)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->count();

                if ($currentBookings >= $service->max_patients_per_slot) {
                    return back()->withErrors(['booking_time' => 'This time slot is fully booked.']);
                }

                $updateData = [
                    'bookable_type' => WardService::class,
                    'bookable_id' => $service->id,
                    'ot_room_id' => null,
                    'service_fee' => $service->daily_fee,
                    'room_fee' => 0,
                    'equipment_fee' => 0,
                    'total_fee' => $service->daily_fee,
                    'preparation_time_minutes' => null,
                    'cleanup_time_minutes' => null,
                    'complexity_level' => null,
                    'end_time' => Carbon::createFromFormat('H:i', $validated['booking_time'])
                        ->addMinutes($service->duration_minutes)
                        ->format('H:i:s'),
                ];
            } else {
                $service = OtService::forBusiness($businessId)->findOrFail($validated['service_id']);
                $otRoom = OtRoom::forBusiness($businessId)->findOrFail($validated['ot_room_id']);

                // Check OT room availability (exclude current booking)
                if (!$otRoom->isAvailableAt($validated['booking_date'], $validated['booking_time'], $booking->id)) {
                    return back()->withErrors(['booking_time' => 'This OT room is not available at the selected time.']);
                }

                $updateData = [
                    'bookable_type' => OtService::class,
                    'bookable_id' => $service->id,
                    'ot_room_id' => $otRoom->id,
                    'service_fee' => $service->base_fee,
                    'room_fee' => $service->room_fee,
                    'equipment_fee' => $service->equipment_fee,
                    'total_fee' => $service->total_fee,
                    'preparation_time_minutes' => $service->preparation_time_minutes,
                    'cleanup_time_minutes' => $service->cleanup_time_minutes,
                    'complexity_level' => $service->complexity_level,
                    'end_time' => Carbon::createFromFormat('H:i', $validated['booking_time'])
                        ->addMinutes($service->total_duration_minutes)
                        ->format('H:i:s'),
                ];
            }

            // Update booking
            $booking->update(array_merge($validated, $updateData, [
                'updated_by' => Auth::id(),
            ]));

            // Create status history if there are changes
            $newValues = $booking->fresh()->only(['booking_date', 'booking_time', 'status']);
            $changedFields = array_diff_assoc($newValues, $oldValues);

            if (!empty($changedFields)) {
                BookingStatusHistory::create([
                    'booking_id' => $booking->id,
                    'old_status' => $oldValues['status'],
                    'new_status' => $booking->status,
                    'reason' => 'Booking updated',
                    'changed_fields' => $changedFields,
                    'changed_by' => Auth::id(),
                    'changed_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Booking updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update booking. Please try again.']);
        }
    }

    public function destroy(Booking $booking)
    {
        // Ensure user can only delete bookings from their business
        if ($booking->business_id !== Auth::user()->business_id) {
            abort(404);
        }

        // Only allow deletion of pending bookings
        if (!in_array($booking->status, ['pending', 'cancelled'])) {
            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Only pending or cancelled bookings can be deleted.');
        }

        DB::beginTransaction();
        try {
            // Create final status history
            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'old_status' => $booking->status,
                'new_status' => 'deleted',
                'reason' => 'Booking deleted',
                'changed_by' => Auth::id(),
                'changed_at' => now(),
            ]);

            $booking->delete();

            DB::commit();

            return redirect()->route('bookings.index')
                ->with('success', 'Booking deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to delete booking. Please try again.']);
        }
    }

    // Status management methods
    public function confirm(Booking $booking)
    {
        return $this->updateStatus($booking, 'confirmed', 'Booking confirmed');
    }

    public function cancel(Request $request, Booking $booking)
    {
        $reason = $request->input('reason', 'Booking cancelled');
        return $this->updateStatus($booking, 'cancelled', $reason);
    }

    public function complete(Booking $booking)
    {
        return $this->updateStatus($booking, 'completed', 'Booking completed');
    }

    public function markNoShow(Booking $booking)
    {
        return $this->updateStatus($booking, 'no_show', 'Patient did not show up');
    }

    private function updateStatus(Booking $booking, string $newStatus, string $reason)
    {
        // Ensure user can only update bookings from their business
        if ($booking->business_id !== Auth::user()->business_id) {
            abort(404);
        }

        $oldStatus = $booking->status;

        // Validate status transition
        $validTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['completed', 'cancelled', 'no_show'],
            'cancelled' => [], // Cannot change from cancelled
            'completed' => [], // Cannot change from completed
            'no_show' => [], // Cannot change from no_show
        ];

        if (!in_array($newStatus, $validTransitions[$oldStatus] ?? [])) {
            return back()->with('error', "Cannot change status from {$oldStatus} to {$newStatus}.");
        }

        DB::beginTransaction();
        try {
            // Update booking status
            $booking->update([
                'status' => $newStatus,
                'updated_by' => Auth::id(),
                $newStatus . '_at' => now(), // confirmed_at, cancelled_at, completed_at
            ]);

            // Create status history
            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'reason' => $reason,
                'changed_by' => Auth::id(),
                'changed_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', "Booking status updated to {$newStatus}.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to update booking status.');
        }
    }

    // API endpoints
    public function getServiceDetails(Request $request)
    {
        $businessId = Auth::user()->business_id;
        $type = $request->type;
        $serviceId = $request->service_id;

        if ($type === 'ward') {
            $service = WardService::forBusiness($businessId)->find($serviceId);
            if (!$service) {
                return response()->json(['error' => 'Service not found'], 404);
            }

            return response()->json([
                'name' => $service->name,
                'description' => $service->description,
                'fee' => $service->daily_fee,
                'formatted_fee' => $service->formatted_fee,
                'duration_minutes' => $service->duration_minutes,
                'max_patients_per_slot' => $service->max_patients_per_slot,
                'available_days' => $service->available_days,
                'start_time' => $service->start_time,
                'end_time' => $service->end_time,
            ]);
        } else {
            $service = OtService::forBusiness($businessId)->find($serviceId);
            if (!$service) {
                return response()->json(['error' => 'Service not found'], 404);
            }

            return response()->json([
                'name' => $service->name,
                'description' => $service->description,
                'base_fee' => $service->base_fee,
                'room_fee' => $service->room_fee,
                'equipment_fee' => $service->equipment_fee,
                'total_fee' => $service->total_fee,
                'formatted_total_fee' => $service->formatted_total_fee,
                'estimated_duration_minutes' => $service->estimated_duration_minutes,
                'total_duration_minutes' => $service->total_duration_minutes,
                'complexity_level' => $service->complexity_level,
                'required_equipment' => $service->required_equipment,
                'required_staff' => $service->required_staff,
            ]);
        }
    }
}
