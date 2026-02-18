<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Business;

class BusinessSettingsController extends Controller
{
    /**
     * Display the business settings form.
     */
    public function edit()
    {
        $business = Auth::user()->business;

        if (!$business) {
            return redirect()->back()->with('error', 'No business associated with your account.');
        }

        return view('admin.business.settings', compact('business'));
    }

    /**
     * Update the business settings.
     */
    public function update(Request $request)
    {
        $business = Auth::user()->business;

        if (!$business) {
            return redirect()->back()->with('error', 'No business associated with your account.');
        }

        $request->validate([
            'hospital_name' => 'required|string|max:255',
            'address' => 'required|string',
            'contact_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'enable_a5_printing' => 'boolean',
        ]);

        $business->update([
            'hospital_name' => $request->hospital_name,
            'address' => $request->address,
            'contact_number' => $request->contact_number,
            'email' => $request->email,
            'enable_a5_printing' => $request->has('enable_a5_printing'),
        ]);

        return redirect()->back()->with('success', 'Business settings updated successfully.');
    }
}
