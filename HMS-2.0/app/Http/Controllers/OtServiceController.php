<?php

namespace App\Http\Controllers;

use App\Models\OtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\OtRoom;

class OtServiceController extends Controller
{
    public function index(Request $request)
    {
        $businessId = Auth::user()->business_id;

        $otServices = OtService::forBusiness($businessId)
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->complexity, function ($query, $complexity) {
                $query->where('complexity_level', $complexity);
            })
            ->orderBy('name')
            ->paginate(10);

        return view('admin.ot-services.index', compact('otServices'));
    }

    public function create()
    {
        return view('admin.ot-services.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'base_fee' => 'required|numeric|min:0',
                'room_fee' => 'required|numeric|min:0',
                'equipment_fee' => 'required|numeric|min:0',
                'estimated_duration_minutes' => 'required|integer|min:1',
                'preparation_time_minutes' => 'required|integer|min:0',
                'cleanup_time_minutes' => 'required|integer|min:0',
                'required_equipment' => 'nullable|array',
                'required_equipment.*' => 'nullable|string',
                'required_staff' => 'nullable|array',
                'required_staff.*' => 'nullable|string',
                'complexity_level' => 'required|in:minor,major,critical',
                'status' => 'required|in:active,inactive',
            ], [
                // Custom error messages
                'name.required' => 'Service name is required.',
                'name.max' => 'Service name cannot exceed 255 characters.',
                'base_fee.required' => 'Base service fee is required.',
                'base_fee.numeric' => 'Base service fee must be a valid number.',
                'base_fee.min' => 'Base service fee cannot be negative.',
                'room_fee.required' => 'Room fee is required.',
                'room_fee.numeric' => 'Room fee must be a valid number.',
                'room_fee.min' => 'Room fee cannot be negative.',
                'equipment_fee.required' => 'Equipment fee is required.',
                'equipment_fee.numeric' => 'Equipment fee must be a valid number.',
                'equipment_fee.min' => 'Equipment fee cannot be negative.',
                'estimated_duration_minutes.required' => 'Estimated duration is required.',
                'estimated_duration_minutes.integer' => 'Estimated duration must be a valid number.',
                'estimated_duration_minutes.min' => 'Estimated duration must be at least 1 minute.',
                'preparation_time_minutes.required' => 'Preparation time is required.',
                'preparation_time_minutes.integer' => 'Preparation time must be a valid number.',
                'preparation_time_minutes.min' => 'Preparation time cannot be negative.',
                'cleanup_time_minutes.required' => 'Cleanup time is required.',
                'cleanup_time_minutes.integer' => 'Cleanup time must be a valid number.',
                'cleanup_time_minutes.min' => 'Cleanup time cannot be negative.',
                'complexity_level.required' => 'Complexity level is required.',
                'complexity_level.in' => 'Please select a valid complexity level.',
                'status.required' => 'Status is required.',
                'status.in' => 'Please select a valid status.',
            ]);

            $validated['business_id'] = Auth::user()->business_id;

            OtService::create($validated);

            return redirect()->route('admin.ot-services.index')
                ->with('success', 'OT service created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while creating the OT service. Please try again.')
                ->withInput();
        }
    }


    public function show(OtService $otService)
    {

        $otService->load(['bookings' => function ($query) {
            $query->with(['patient', 'otRoom'])->latest('booking_date');
        }]);

        return view('admin.ot-services.show', compact('otService'));
    }

    public function edit(OtService $otService)
    {

        return view('admin.ot-services.edit', compact('otService'));
    }

    public function update(Request $request, OtService $otService)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_fee' => 'required|numeric|min:0',
            'room_fee' => 'required|numeric|min:0',
            'equipment_fee' => 'required|numeric|min:0',
            'estimated_duration_minutes' => 'required|integer|min:1',
            'preparation_time_minutes' => 'required|integer|min:0',
            'cleanup_time_minutes' => 'required|integer|min:0',
            'required_equipment' => 'nullable|array',
            'required_equipment.*' => 'string',
            'required_staff' => 'nullable|array',
            'required_staff.*' => 'string',
            'complexity_level' => 'required|in:minor,major,critical',
            'status' => 'required|in:active,inactive',
        ]);

        $otService->update($validated);

        return redirect()->route('admin.ot-services.index')
            ->with('success', 'OT service updated successfully.');
    }

    public function destroy(OtService $otService)
    {

        // Check if there are any active bookings
        $activeBookings = $otService->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        if ($activeBookings > 0) {
            return redirect()->route('admin.ot-services.index')
                ->with('error', 'Cannot delete OT service with active bookings.');
        }

        $otService->delete();

        return redirect()->route('admin.ot-services.index')
            ->with('success', 'OT service deleted successfully.');
    }

    // API endpoint for fee calculation
    public function calculateFee(OtService $otService)
    {
        return response()->json([
            'base_fee' => $otService->base_fee,
            'room_fee' => $otService->room_fee,
            'equipment_fee' => $otService->equipment_fee,
            'total_fee' => $otService->total_fee,
            'formatted_total_fee' => $otService->formatted_total_fee,
            'duration_minutes' => $otService->total_duration_minutes,
        ]);
    }

    public function apiIndex(Request $request)
    {
        $businessId = Auth::user()->business_id;

        $services = OtService::forBusiness($businessId)
            ->active()
            ->select('id', 'name', 'description', 'base_fee', 'room_fee', 'equipment_fee')
            ->get()
            ->map(function ($service) {
                $service->fee = $service->base_fee + $service->room_fee + $service->equipment_fee;
                return $service;
            });

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }


    public function apiDetails(OtService $otService)
    {
        // Ensure user can only access services from their business
        if ($otService->business_id !== Auth::user()->business_id) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $otService->id,
                'name' => $otService->name,
                'description' => $otService->description,
                'fee' => $otService->total_fee,
                'base_fee' => $otService->base_fee,
                'room_fee' => $otService->room_fee,
                'equipment_fee' => $otService->equipment_fee,
                'duration' => $otService->estimated_duration_minutes,
                'complexity_level' => $otService->complexity_level
            ]
        ]);
    }

    public function apiSlots(Request $request, OtService $otService)
    {
        // Ensure user can only access services from their business
        if ($otService->business_id !== Auth::user()->business_id) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        $date = $request->input('date');
        $otRoomId = $request->input('ot_room_id');

        if (!$otRoomId) {
            return response()->json(['error' => 'OT Room ID is required'], 400);
        }

        $otRoom = OtRoom::forBusiness(Auth::user()->business_id)->find($otRoomId);
        if (!$otRoom) {
            return response()->json(['error' => 'OT Room not found'], 404);
        }

        // Generate time slots (typically OT slots are longer)
        $slots = [];
        $startTime = Carbon::parse('08:00');
        $endTime = Carbon::parse('20:00');
        $duration = $otService->total_duration_minutes ?? 120;

        while ($startTime->lt($endTime)) {
            $timeSlot = $startTime->format('H:i');
            $endSlot = $startTime->copy()->addMinutes($duration);

            // Check if OT room is available
            $isBooked = Booking::where('bookable_type', OtService::class)
                ->where('ot_room_id', $otRoomId)
                ->where('booking_date', $date)
                ->where(function ($query) use ($timeSlot, $endSlot) {
                    $query->whereBetween('booking_time', [$timeSlot, $endSlot->format('H:i')])
                        ->orWhereBetween('end_time', [$timeSlot, $endSlot->format('H:i')]);
                })
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            $slots[] = [
                'time' => $timeSlot,
                'end_time' => $endSlot->format('H:i'),
                'available' => !$isBooked,
                'duration_minutes' => $duration
            ];

            $startTime->addMinutes(60); // 1-hour intervals for OT slots
        }

        return response()->json([
            'success' => true,
            'data' => $slots
        ]);
    }
}
