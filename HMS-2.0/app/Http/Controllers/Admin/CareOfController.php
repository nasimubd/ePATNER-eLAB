<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareOf;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CareOfController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $careOfs = CareOf::with(['business', 'ledger'])
            ->forBusiness(Auth::user()->business_id)
            ->latest()
            ->paginate(10);

        return view('admin.care-ofs.index', compact('careOfs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.care-ofs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'commission_type' => 'required|in:percentage,fixed',
            'commission_rate' => 'required_if:commission_type,percentage|nullable|numeric|min:0|max:100',
            'fixed_commission_amount' => 'required_if:commission_type,fixed|nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $careOf = CareOf::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'commission_type' => $request->commission_type,
            'commission_rate' => $request->commission_type === 'percentage' ? $request->commission_rate : 0,
            'fixed_commission_amount' => $request->commission_type === 'fixed' ? $request->fixed_commission_amount : null,
            'business_id' => Auth::user()->business_id,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.care-ofs.index')
            ->with('success', 'Care Of created successfully with commission settings and ledger has been automatically generated.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CareOf $careOf)
    {

        $careOf->load(['business', 'ledger']);

        return view('admin.care-ofs.show', compact('careOf'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CareOf $careOf)
    {

        return view('admin.care-ofs.edit', compact('careOf'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CareOf $careOf)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'commission_type' => 'required|in:percentage,fixed',
            'commission_rate' => 'required_if:commission_type,percentage|nullable|numeric|min:0|max:100',
            'fixed_commission_amount' => 'required_if:commission_type,fixed|nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $careOf->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'commission_type' => $request->commission_type,
            'commission_rate' => $request->commission_type === 'percentage' ? $request->commission_rate : 0,
            'fixed_commission_amount' => $request->commission_type === 'fixed' ? $request->fixed_commission_amount : null,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.care-ofs.index')
            ->with('success', 'Care Of updated successfully with commission settings and associated ledger has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CareOf $careOf)
    {

        // Delete associated ledger if exists
        if ($careOf->ledger) {
            $careOf->ledger->delete();
        }

        $careOf->delete();

        return redirect()->route('admin.care-ofs.index')
            ->with('success', 'Care Of deleted successfully.');
    }
}
