<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Letterhead;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LetterheadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Letterhead::with('business');

        // Filter by business
        if ($request->filled('business_id')) {
            $query->where('business_id', $request->business_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $letterheads = $query->latest()->paginate(15);
        $businesses = Business::orderBy('hospital_name')->get();

        return view('super-admin.letterheads.index', compact('letterheads', 'businesses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $businesses = Business::orderBy('hospital_name')->get();
        return view('super-admin.letterheads.create', compact('businesses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'business_name_bangla' => 'required|string|max:255',
            'business_name_english' => 'required|string|max:255',
            'location' => 'required|string',
            'contacts' => 'nullable|array',
            'contacts.*' => 'nullable|string|max:20',
            'emails' => 'nullable|array',
            'emails.*' => 'nullable|email|max:255',
            'type' => ['required', Rule::in(['Invoice', 'Lab Report'])],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
            'business_id' => 'required|exists:businesses,id',
        ]);

        // Check if there's already an active letterhead for this business and type
        if ($request->status === 'Active') {
            Letterhead::where('business_id', $request->business_id)
                ->where('type', $request->type)
                ->where('status', 'Active')
                ->update(['status' => 'Inactive']);
        }

        Letterhead::create([
            'business_name_bangla' => $request->business_name_bangla,
            'business_name_english' => $request->business_name_english,
            'location' => $request->location,
            'contacts' => array_filter($request->contacts ?? []),
            'emails' => array_filter($request->emails ?? []),
            'type' => $request->type,
            'status' => $request->status,
            'business_id' => $request->business_id,
        ]);

        return redirect()->route('super-admin.letterheads.index')
            ->with('success', 'Letterhead created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Letterhead $letterhead)
    {
        return view('super-admin.letterheads.show', compact('letterhead'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Letterhead $letterhead)
    {
        $businesses = Business::orderBy('hospital_name')->get();
        return view('super-admin.letterheads.edit', compact('letterhead', 'businesses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Letterhead $letterhead)
    {
        $request->validate([
            'business_name_bangla' => 'required|string|max:255',
            'business_name_english' => 'required|string|max:255',
            'location' => 'required|string',
            'contacts' => 'nullable|array',
            'contacts.*' => 'nullable|string|max:20',
            'emails' => 'nullable|array',
            'emails.*' => 'nullable|email|max:255',
            'type' => ['required', Rule::in(['Invoice', 'Lab Report'])],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
            'business_id' => 'required|exists:businesses,id',
        ]);

        // Check if there's already an active letterhead for this business and type (excluding current)
        if ($request->status === 'Active') {
            Letterhead::where('business_id', $request->business_id)
                ->where('type', $request->type)
                ->where('status', 'Active')
                ->where('id', '!=', $letterhead->id)
                ->update(['status' => 'Inactive']);
        }

        $letterhead->update([
            'business_name_bangla' => $request->business_name_bangla,
            'business_name_english' => $request->business_name_english,
            'location' => $request->location,
            'contacts' => array_filter($request->contacts ?? []),
            'emails' => array_filter($request->emails ?? []),
            'type' => $request->type,
            'status' => $request->status,
            'business_id' => $request->business_id,
        ]);

        return redirect()->route('super-admin.letterheads.index')
            ->with('success', 'Letterhead updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Letterhead $letterhead)
    {
        $letterhead->delete();

        return redirect()->route('super-admin.letterheads.index')
            ->with('success', 'Letterhead deleted successfully.');
    }

    /**
     * Toggle the status of the specified resource.
     */
    public function toggleStatus(Letterhead $letterhead)
    {
        if ($letterhead->status === 'Active') {
            $letterhead->status = 'Inactive';
        } else {
            // Deactivate other active letterheads for the same business and type
            Letterhead::where('business_id', $letterhead->business_id)
                ->where('type', $letterhead->type)
                ->where('status', 'Active')
                ->update(['status' => 'Inactive']);

            $letterhead->status = 'Active';
        }

        $letterhead->save();

        $status = $letterhead->status === 'Active' ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Letterhead has been {$status}.");
    }
}
