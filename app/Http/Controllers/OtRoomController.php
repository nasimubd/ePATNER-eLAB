<?php

namespace App\Http\Controllers;

use App\Models\OtRoom;
use App\Models\OtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtRoomController extends Controller
{
    public function index(Request $request)
    {
        $businessId = Auth::user()->business_id;

        $otRooms = OtRoom::forBusiness($businessId)
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('room_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('room_number')
            ->paginate(10);

        return view('admin.ot-rooms.index', compact('otRooms'));
    }

    public function create()
    {
        return view('admin.ot-rooms.create');
    }

    public function store(Request $request)
    {
        $businessId = Auth::user()->business_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'room_number' => [
                'required',
                'string',
                'max:50',
                "unique:ot_rooms,room_number,NULL,id,business_id,{$businessId}"
            ],
            'description' => 'nullable|string',
            'equipment_available' => 'nullable|array',
            'equipment_available.*' => 'string',
            'status' => 'required|in:active,inactive,maintenance',
            'capacity' => 'required|integer|min:1',
        ]);

        $validated['business_id'] = $businessId;

        OtRoom::create($validated);

        return redirect()->route('admin.ot-rooms.index')
            ->with('success', 'OT room created successfully.');
    }

    public function show(OtRoom $otRoom)
    {
        // Ensure user can only view rooms from their business
        if ($otRoom->business_id !== Auth::user()->business_id) {
            abort(404);
        }

        $otRoom->load(['bookings' => function ($query) {
            $query->with(['patient', 'bookable'])
                ->where('booking_date', '>=', now()->subDays(30))
                ->orderBy('booking_date', 'desc')
                ->orderBy('booking_time', 'desc');
        }]);

        // Get today's bookings
        $todayBookings = $otRoom->getTodayBookings();

        // Get upcoming bookings
        $upcomingBookings = $otRoom->getUpcomingBookings();

        return view('admin.ot-rooms.show', compact('otRoom', 'todayBookings', 'upcomingBookings'));
    }

    public function edit(OtRoom $otRoom)
    {
        // Ensure user can only edit rooms from their business
        if ($otRoom->business_id !== Auth::user()->business_id) {
            abort(404);
        }

        return view('admin.ot-rooms.edit', compact('otRoom'));
    }

    public function update(Request $request, OtRoom $otRoom)
    {
        // Ensure user can only update rooms from their business
        if ($otRoom->business_id !== Auth::user()->business_id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'room_number' => [
                'required',
                'string',
                'max:50',
                "unique:ot_rooms,room_number,{$otRoom->id},id,business_id,{$otRoom->business_id}"
            ],
            'description' => 'nullable|string',
            'equipment_available' => 'nullable|array',
            'equipment_available.*' => 'string',
            'status' => 'required|in:active,inactive,maintenance',
            'capacity' => 'required|integer|min:1',
        ]);

        $otRoom->update($validated);

        return redirect()->route('admin.ot-rooms.index')
            ->with('success', 'OT room updated successfully.');
    }

    public function destroy(OtRoom $otRoom)
    {
        // Ensure user can only delete rooms from their business
        if ($otRoom->business_id !== Auth::user()->business_id) {
            abort(404);
        }

        // Check if there are any active bookings
        $activeBookings = $otRoom->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        if ($activeBookings > 0) {
            return redirect()->route('admin.ot-rooms.index')
                ->with('error', 'Cannot delete OT room with active bookings.');
        }

        $otRoom->delete();

        return redirect()->route('admin.ot-rooms.index')
            ->with('success', 'OT room deleted successfully.');
    }

    // API endpoint for checking availability
    public function checkAvailability(Request $request, OtRoom $otRoom)
    {
        // Ensure user can only check rooms from their business
        if ($otRoom->business_id !== Auth::user()->business_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'duration_minutes' => 'required|integer|min:1',
            'exclude_booking_id' => 'nullable|integer',
        ]);

        $date = $request->date;
        $time = $request->time;
        $durationMinutes = $request->duration_minutes;
        $excludeBookingId = $request->exclude_booking_id;

        // Calculate end time
        $endTime = date('H:i', strtotime($time) + ($durationMinutes * 60));

        $isAvailable = $otRoom->isAvailableAt($date, $time, $excludeBookingId);

        return response()->json([
            'available' => $isAvailable,
            'room_name' => $otRoom->name,
            'room_number' => $otRoom->room_number,
            'requested_time' => $time,
            'end_time' => $endTime,
            'date' => $date,
        ]);
    }

    // API endpoint for getting room schedule
    public function getSchedule(Request $request, OtRoom $otRoom)
    {
        // Ensure user can only view rooms from their business
        if ($otRoom->business_id !== Auth::user()->business_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $date = $request->get('date', now()->toDateString());

        $bookings = $otRoom->getBookingsForDate($date);

        return response()->json([
            'room' => [
                'id' => $otRoom->id,
                'name' => $otRoom->name,
                'room_number' => $otRoom->room_number,
            ],
            'date' => $date,
            'bookings' => $bookings->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'patient_name' => $booking->patient->full_name,
                    'service_name' => $booking->bookable->name,
                    'booking_time' => $booking->booking_time,
                    'end_time' => $booking->end_time,
                    'status' => $booking->status,
                    'complexity_level' => $booking->complexity_level,
                ];
            }),
        ]);
    }

    public function apiIndex(Request $request)
    {
        $businessId = Auth::user()->business_id;

        $rooms = OtRoom::forBusiness($businessId)
            ->active()
            ->select('id', 'name', 'description')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }
}
