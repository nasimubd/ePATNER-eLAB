<?php

namespace App\Http\Controllers;

use App\Models\WardService;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Booking;

class WardServiceController extends Controller
{
    public function index(Request $request)
    {
        $businessId = Auth::user()->business_id;

        $wardServices = WardService::forBusiness($businessId)
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('name')
            ->paginate(10);

        return view('admin.ward-services.index', compact('wardServices'));
    }

    public function create()
    {
        return view('admin.ward-services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'daily_fee' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'max_patients_per_slot' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
            'available_days' => 'required|array|min:1',
            'available_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $validated['business_id'] = Auth::user()->business_id;

        WardService::create($validated);

        return redirect()->route('admin.ward-services.index')
            ->with('success', 'Ward service created successfully.');
    }

    public function show(WardService $wardService)
    {

        $wardService->load(['bookings' => function ($query) {
            $query->with('patient')->latest('booking_date');
        }]);

        return view('admin.ward-services.show', compact('wardService'));
    }

    public function edit(WardService $wardService)
    {

        return view('admin.ward-services.edit', compact('wardService'));
    }

    public function update(Request $request, WardService $wardService)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'daily_fee' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'max_patients_per_slot' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
            'available_days' => 'required|array|min:1',
            'available_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $wardService->update($validated);

        return redirect()->route('admin.ward-services.index')
            ->with('success', 'Ward service updated successfully.');
    }

    public function destroy(WardService $wardService)
    {

        // Check if there are any active bookings
        $activeBookings = $wardService->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        if ($activeBookings > 0) {
            return redirect()->route('admin.ward-services.index')
                ->with('error', 'Cannot delete ward service with active bookings.');
        }

        $wardService->delete();

        return redirect()->route('admin.ward-services.index')
            ->with('success', 'Ward service deleted successfully.');
    }

    // API endpoint for booking form
    public function getAvailableSlots(Request $request, WardService $wardService)
    {
        $date = $request->date;
        $dayOfWeek = strtolower(date('l', strtotime($date)));

        if (!$wardService->isAvailableOnDay($dayOfWeek)) {
            return response()->json(['slots' => []]);
        }

        // Generate time slots based on duration
        $slots = [];
        $startTime = strtotime($wardService->start_time);
        $endTime = strtotime($wardService->end_time);
        $duration = $wardService->duration_minutes * 60; // Convert to seconds

        while ($startTime < $endTime) {
            $timeSlot = date('H:i', $startTime);

            if ($wardService->canAccommodateMorePatients($date, $timeSlot)) {
                $slots[] = [
                    'time' => $timeSlot,
                    'available_spots' => $wardService->max_patients_per_slot -
                        $wardService->bookings()
                        ->where('booking_date', $date)
                        ->where('booking_time', $timeSlot)
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->count()
                ];
            }

            $startTime += $duration;
        }

        return response()->json(['slots' => $slots]);
    }

    public function apiIndex(Request $request)
    {
        $businessId = Auth::user()->business_id;

        $services = WardService::forBusiness($businessId)
            ->active()
            ->select('id', 'name', 'daily_fee as fee', 'description')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    public function apiDetails(WardService $wardService)
    {
        // Ensure user can only access services from their business
        if ($wardService->business_id !== Auth::user()->business_id) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $wardService->id,
                'name' => $wardService->name,
                'description' => $wardService->description,
                'fee' => $wardService->daily_fee,
                'duration' => $wardService->duration_minutes,
                'max_patients_per_slot' => $wardService->max_patients_per_slot
            ]
        ]);
    }

    public function apiSlots(Request $request, WardService $wardService)
    {
        // Ensure user can only access services from their business
        if ($wardService->business_id !== Auth::user()->business_id) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        $date = $request->input('date');

        // Generate time slots based on service availability
        $slots = [];
        $startTime = Carbon::parse($wardService->start_time ?? '09:00');
        $endTime = Carbon::parse($wardService->end_time ?? '17:00');
        $duration = $wardService->duration_minutes ?? 60;

        while ($startTime->lt($endTime)) {
            $timeSlot = $startTime->format('H:i');

            // Check availability
            $bookedCount = Booking::where('bookable_type', WardService::class)
                ->where('bookable_id', $wardService->id)
                ->where('booking_date', $date)
                ->where('booking_time', $timeSlot)
                ->whereIn('status', ['pending', 'confirmed'])
                ->count();

            $available = $bookedCount < ($wardService->max_patients_per_slot ?? 1);

            $slots[] = [
                'time' => $timeSlot,
                'available' => $available,
                'booked_count' => $bookedCount,
                'max_capacity' => $wardService->max_patients_per_slot ?? 1
            ];

            $startTime->addMinutes($duration);
        }

        return response()->json([
            'success' => true,
            'data' => $slots
        ]);
    }
}
