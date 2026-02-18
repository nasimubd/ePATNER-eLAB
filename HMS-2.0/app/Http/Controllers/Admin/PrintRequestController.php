<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrintRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrintRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PrintRequest::with(['invoice.patient', 'user', 'approver'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('request_type')) {
            $query->where('request_type', $request->request_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('invoice', function ($invoiceQuery) use ($search) {
                    $invoiceQuery->where('invoice_number', 'like', "%{$search}%");
                })
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $printRequests = $query->paginate(15);

        return view('admin.print-requests.index', compact('printRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:medical_invoices,id',
            'request_type' => 'required|in:pos,a5',
            'reason' => 'required|string|max:500',
        ]);

        // Check if request already exists for this invoice and type
        $existingRequest = PrintRequest::where('invoice_id', $request->invoice_id)
            ->where('request_type', $request->request_type)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'A pending request already exists for this invoice and print type.'
            ]);
        }

        PrintRequest::create([
            'invoice_id' => $request->invoice_id,
            'user_id' => Auth::id(),
            'request_type' => $request->request_type,
            'reason' => $request->reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Print request submitted successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrintRequest $printRequest)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'rejected_reason' => 'required_if:action,reject|string|max:500',
            'allowed_prints' => 'required_if:action,approve|integer|min:1',
        ]);

        if ($request->action === 'approve') {
            $printRequest->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'allowed_prints' => $request->allowed_prints ?? 1,
            ]);
            $message = 'Print request approved successfully.';
        } else {
            $printRequest->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'rejected_reason' => $request->rejected_reason,
                'approved_at' => now(),
            ]);
            $message = 'Print request rejected successfully.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
