<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $businesses = Business::latest()->paginate(10);

        return view('super-admin.businesses.index', compact('businesses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('super-admin.businesses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'hospital_name' => 'required|string|max:255',
            'address' => 'required|string',
            'contact_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);

        $business = new Business();
        $business->hospital_name = $request->hospital_name;
        $business->address = $request->address;
        $business->contact_number = $request->contact_number;
        $business->email = $request->email;
        $business->is_active = $request->has('is_active') ? true : false;

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $this->handleLogoUpload($business, $request->file('logo'));
        }

        $business->save();

        return redirect()->route('super-admin.businesses.index')
            ->with('success', 'Hospital created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Business $business)
    {
        return view('super-admin.businesses.show', compact('business'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Business $business)
    {
        return view('super-admin.businesses.edit', compact('business'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Business $business)
    {
        $request->validate([
            'hospital_name' => 'required|string|max:255',
            'address' => 'required|string',
            'contact_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);

        $business->hospital_name = $request->hospital_name;
        $business->address = $request->address;
        $business->contact_number = $request->contact_number;
        $business->email = $request->email;
        $business->is_active = $request->has('is_active') ? true : false;

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $this->handleLogoUpload($business, $request->file('logo'));
        }

        $business->save();

        return redirect()->route('super-admin.businesses.index')
            ->with('success', 'Hospital updated successfully.');
    }

    private function handleLogoUpload(Business $business, $file)
    {
        if ($file && $file->isValid()) {
            // Create image instance and resize/compress
            $image = Image::make($file)
                ->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('jpg', 80); // Compress to 80% quality

            $business->logo = $image->getEncoded();
            $business->logo_mime_type = 'image/jpeg';
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Business $business)
    {
        $hospitalName = $business->hospital_name;
        $business->delete();

        return redirect()
            ->route('super-admin.businesses.index')
            ->with('success', "Hospital '{$hospitalName}' deleted successfully.");
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleStatus(Business $business)
    {
        $business->is_active = !$business->is_active;
        $business->save();

        $status = $business->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->back()
            ->with('success', "Hospital '{$business->hospital_name}' has been {$status}.");
    }
}
