<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingStatusController extends Controller
{
    public function history(Booking $booking)
    {
        // Ensure user can only view bookings from their business
        if ($booking->business_id !== Auth::user()->business_id) {
            abort(404);
        }

        $histories = $booking->statusHistories()
            ->with('changedBy')
            ->orderBy('changed_at', 'desc')
            ->get();

        return view('bookings.status-history', compact('booking', 'histories'));
    }

    public function updateWithReason(Request $request, Booking $booking)
    {
        // Ensure user can only update bookings from their business
        if ($booking->business_id !== Auth::user()->business_id) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => 'required|in:confirmed,cancelled,completed,no_show',
            'reason' => 'required|string|max:500',
        ]);

        $oldStatus = $booking->status;
        $newStatus = $validated['status'];

        // Validate status transition
        $validTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['completed', 'cancelled', 'no_show'],
            'cancelled' => [],
            'completed' => [],
            'no_show' => [],
        ];

        if (!in_array($newStatus, $validTransitions[$oldStatus] ?? [])) {
            return back()->withErrors(['status' => "Cannot change status from {$oldStatus} to {$newStatus}."]);
        }

        try {
            // Update booking status
            $booking->update([
                'status' => $newStatus,
                'updated_by' => Auth::id(),
                $newStatus . '_at' => now(),
            ]);

            // Create status history
            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'reason' => $validated['reason'],
                'changed_by' => Auth::id(),
                'changed_at' => now(),
            ]);

            return back()->with('success', "Booking status updated to {$newStatus}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update booking status. Please try again.');
        }
    }

    public function bulkUpdate(Request $request)
    {
        $businessId = Auth::user()->business_id;

        $validated = $request->validate([
            'booking_ids' => 'required|array|min:1',
            'booking_ids.*' => 'integer|exists:bookings,id',
            'status' => 'required|in:confirmed,cancelled,completed,no_show',
            'reason' => 'required|string|max:500',
        ]);

        $bookingIds = $validated['booking_ids'];
        $newStatus = $validated['status'];
        $reason = $validated['reason'];

        // Get bookings that belong to the user's business
        $bookings = Booking::forBusiness($businessId)
            ->whereIn('id', $bookingIds)
            ->get();

        if ($bookings->count() !== count($bookingIds)) {
            return back()->with('error', 'Some bookings were not found or do not belong to your business.');
        }

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($bookings as $booking) {
            $oldStatus = $booking->status;

            // Validate status transition for each booking
            $validTransitions = [
                'pending' => ['confirmed', 'cancelled'],
                'confirmed' => ['completed', 'cancelled', 'no_show'],
                'cancelled' => [],
                'completed' => [],
                'no_show' => [],
            ];

            if (!in_array($newStatus, $validTransitions[$oldStatus] ?? [])) {
                $errors[] = "Booking #{$booking->id}: Cannot change from {$oldStatus} to {$newStatus}";
                $errorCount++;
                continue;
            }

            try {
                // Update booking status
                $booking->update([
                    'status' => $newStatus,
                    'updated_by' => Auth::id(),
                    $newStatus . '_at' => now(),
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

                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Booking #{$booking->id}: Failed to update status";
                $errorCount++;
            }
        }

        $message = "{$successCount} bookings updated successfully.";
        if ($errorCount > 0) {
            $message .= " {$errorCount} bookings failed to update.";
        }

        $alertType = $errorCount > 0 ? 'warning' : 'success';

        return back()->with($alertType, $message);
    }

    public function getStatusOptions(Booking $booking)
    {
        // Ensure user can only view bookings from their business
        if ($booking->business_id !== Auth::user()->business_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $currentStatus = $booking->status;

        $validTransitions = [
            'pending' => [
                ['value' => 'confirmed', 'label' => 'Confirm Booking', 'class' => 'btn-success'],
                ['value' => 'cancelled', 'label' => 'Cancel Booking', 'class' => 'btn-danger'],
            ],
            'confirmed' => [
                ['value' => 'completed', 'label' => 'Mark as Completed', 'class' => 'btn-success'],
                ['value' => 'cancelled', 'label' => 'Cancel Booking', 'class' => 'btn-danger'],
                ['value' => 'no_show', 'label' => 'Mark as No Show', 'class' => 'btn-warning'],
            ],
            'cancelled' => [],
            'completed' => [],
            'no_show' => [],
        ];

        return response()->json([
            'current_status' => $currentStatus,
            'available_transitions' => $validTransitions[$currentStatus] ?? [],
        ]);
    }

    public function getRecentActivity(Request $request)
    {
        $businessId = Auth::user()->business_id;
        $limit = $request->get('limit', 10);

        $recentActivities = BookingStatusHistory::whereHas('booking', function ($query) use ($businessId) {
            $query->where('business_id', $businessId);
        })
            ->with(['booking.patient', 'changedBy'])
            ->orderBy('changed_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'activities' => $recentActivities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'booking_id' => $activity->booking_id,
                    'patient_name' => $activity->booking->patient->full_name,
                    'old_status' => $activity->old_status,
                    'new_status' => $activity->new_status,
                    'reason' => $activity->reason,
                    'changed_by' => $activity->changedBy->name,
                    'changed_at' => $activity->formatted_changed_at,
                    'status_change_description' => $activity->status_change_description,
                ];
            }),
        ]);
    }

    public function exportHistory(Booking $booking)
    {
        // Ensure user can only export bookings from their business
        if ($booking->business_id !== Auth::user()->business_id) {
            abort(404);
        }

        $histories = $booking->statusHistories()
            ->with('changedBy')
            ->orderBy('changed_at', 'asc')
            ->get();

        $csvData = [];
        $csvData[] = ['Date/Time', 'Old Status', 'New Status', 'Reason', 'Changed By'];

        foreach ($histories as $history) {
            $csvData[] = [
                $history->changed_at->format('Y-m-d H:i:s'),
                $history->old_status ?? 'N/A',
                $history->new_status,
                $history->reason,
                $history->changedBy->name,
            ];
        }

        $filename = "booking_{$booking->id}_status_history_" . now()->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
