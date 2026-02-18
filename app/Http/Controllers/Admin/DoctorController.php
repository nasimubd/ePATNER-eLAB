<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Doctor::with('business')
            ->forBusiness(Auth::user()->business_id);

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by specialization
        if ($request->filled('specialization')) {
            $query->where('specialization', $request->specialization);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $doctors = $query->latest()->paginate(10)->withQueryString();

        // Get unique specializations for filter
        $specializations = Doctor::forBusiness(Auth::user()->business_id)
            ->distinct()
            ->pluck('specialization')
            ->filter()
            ->sort();

        return view('admin.doctors.index', compact('doctors', 'specializations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $availableDays = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday'
        ];

        return view('admin.doctors.create', compact('availableDays'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors,email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'specialization' => 'required|string|max:255',
            'license_number' => 'required|string|unique:doctors,license_number',
            'qualifications' => 'nullable|string',
            'experience_years' => 'required|integer|min:0|max:50',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'consultation_fee' => 'required|numeric|min:0',
            'available_days' => 'nullable|array',
            'available_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'bio' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean'
        ]);

        DB::beginTransaction();

        try {
            // Create user account first
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'business_id' => Auth::user()->business_id,
                'email_verified_at' => now(), // Auto-verify doctor accounts
            ]);

            // Assign doctor role to the user
            $user->assignRole('doctor');

            // Create doctor profile
            $doctor = new Doctor();
            $doctor->business_id = Auth::user()->business_id;
            $doctor->user_id = $user->id; // Link to user account
            $doctor->fill($request->except(['profile_image', 'password', 'password_confirmation']));
            $doctor->is_active = $request->has('is_active');

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                $doctor->profile_image = $this->handleImageUpload($request->file('profile_image'));
            }

            $doctor->save();

            DB::commit();

            return redirect()->route('admin.doctors.index')
                ->with('success', 'Doctor created successfully with login credentials.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up uploaded image if exists
            if (isset($doctor->profile_image) && Storage::exists($doctor->profile_image)) {
                Storage::delete($doctor->profile_image);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create doctor. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor)
    {
        // Check if doctor belongs to current user's business
        if ($doctor->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this doctor.');
        }

        return view('admin.doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor)
    {
        // Check if doctor belongs to current user's business
        if ($doctor->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this doctor.');
        }

        $availableDays = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday'
        ];

        return view('admin.doctors.edit', compact('doctor', 'availableDays'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Doctor $doctor)
    {
        // Check if doctor belongs to current user's business
        if ($doctor->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this doctor.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors,email,' . $doctor->id . '|unique:users,email,' . ($doctor->user_id ?? 'NULL'),
            'phone' => 'required|string|max:20',
            'specialization' => 'required|string|max:255',
            'license_number' => 'required|string|unique:doctors,license_number,' . $doctor->id,
            'qualifications' => 'nullable|string',
            'experience_years' => 'required|integer|min:0|max:50',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'consultation_fee' => 'required|numeric|min:0',
            'available_days' => 'nullable|array',
            'available_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'bio' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean'
        ]);

        DB::beginTransaction();

        try {
            // Update user account if exists
            if ($doctor->user_id) {
                $user = User::find($doctor->user_id);
                if ($user) {
                    $user->update([
                        'name' => $request->name,
                        'email' => $request->email,
                    ]);
                }
            }

            $doctor->fill($request->except(['profile_image']));
            $doctor->is_active = $request->has('is_active');

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                // Delete old image
                if ($doctor->profile_image && Storage::exists($doctor->profile_image)) {
                    Storage::delete($doctor->profile_image);
                }
                $doctor->profile_image = $this->handleImageUpload($request->file('profile_image'));
            }

            $doctor->save();

            DB::commit();

            return redirect()->route('admin.doctors.index')
                ->with('success', 'Doctor updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update doctor. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
    {
        // Check if doctor belongs to current user's business
        if ($doctor->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this doctor.');
        }

        DB::beginTransaction();

        try {
            // Delete profile image
            if ($doctor->profile_image && Storage::exists($doctor->profile_image)) {
                Storage::delete($doctor->profile_image);
            }

            // Delete associated user account
            if ($doctor->user_id) {
                $user = User::find($doctor->user_id);
                if ($user) {
                    $user->delete();
                }
            }

            $doctor->delete();

            DB::commit();

            return redirect()->route('admin.doctors.index')
                ->with('success', 'Doctor and associated user account deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to delete doctor. Please try again.');
        }
    }

    /**
     * Toggle doctor status
     */
    public function toggleStatus(Doctor $doctor)
    {
        // Check if doctor belongs to current user's business
        if ($doctor->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this doctor.');
        }

        $doctor->update(['is_active' => !$doctor->is_active]);

        $status = $doctor->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Doctor {$status} successfully.");
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload($file)
    {
        if ($file && $file->isValid()) {
            // Create image instance and resize
            $image = Image::make($file)
                ->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('jpg', 80);

            // Generate unique filename
            $filename = 'doctor-profiles/' . uniqid() . '.jpg';

            // Store the image
            Storage::put($filename, $image->getEncoded());

            return $filename;
        }

        return null;
    }

    /**
     * Show doctor profile image
     */
    public function showImage(Doctor $doctor)
    {
        // Check if doctor belongs to current user's business
        if ($doctor->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this doctor.');
        }

        if ($doctor->profile_image && Storage::exists($doctor->profile_image)) {
            return response()->file(Storage::path($doctor->profile_image));
        }

        // Return default avatar
        $gender = $doctor->gender === 'female' ? 'female' : 'male';
        return response()->file(public_path("images/avatars/default-{$gender}.png"));
    }

    /**
     * Reset doctor password
     */
    public function resetPassword(Request $request, Doctor $doctor)
    {
        // Check if doctor belongs to current user's business
        if ($doctor->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this doctor.');
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($doctor->user_id) {
            $user = User::find($doctor->user_id);
            if ($user) {
                $user->update([
                    'password' => Hash::make($request->password)
                ]);

                return redirect()->back()->with('success', 'Doctor password reset successfully.');
            }
        }

        return redirect()->back()->with('error', 'Doctor user account not found.');
    }

    /**
     * Search doctors (AJAX)
     */
    public function search(Request $request)
    {
        $query = Doctor::forBusiness(Auth::user()->business_id)
            ->where('is_active', true);

        if ($request->filled('q')) {
            $query->search($request->q);
        }

        $doctors = $query->limit(10)->get(['id', 'name', 'specialization', 'phone', 'profile_image']);

        return response()->json($doctors->map(function ($doctor) {
            return [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'specialization' => $doctor->specialization,
                'phone' => $doctor->phone,
                'avatar' => $doctor->profile_image_url ?? asset('images/avatars/default-male.png')
            ];
        }));
    }

    /**
     * Get doctor details (AJAX)
     */
    public function getDoctor(Doctor $doctor)
    {
        // Check if doctor belongs to current user's business
        if ($doctor->business_id !== Auth::user()->business_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id' => $doctor->id,
            'name' => $doctor->name,
            'specialization' => $doctor->specialization,
            'phone' => $doctor->phone,
            'email' => $doctor->email,
            'consultation_fee' => $doctor->consultation_fee,
            'experience_years' => $doctor->experience_years,
            'qualifications' => $doctor->qualifications,
            'bio' => $doctor->bio,
            'available_days' => $doctor->available_days,
            'start_time' => $doctor->start_time,
            'end_time' => $doctor->end_time,
            'avatar' => $doctor->profile_image_url ?? asset('images/avatars/default-male.png')
        ]);
    }

    /**
     * Export doctors to CSV
     */
    public function export(Request $request)
    {
        $query = Doctor::forBusiness(Auth::user()->business_id);

        // Apply same filters as index
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('specialization')) {
            $query->where('specialization', $request->specialization);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $doctors = $query->get();

        $filename = 'doctors_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($doctors) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Name',
                'Email',
                'Phone',
                'Specialization',
                'License Number',
                'Experience Years',
                'Consultation Fee',
                'Qualifications',
                'Gender',
                'Date of Birth',
                'Address',
                'Available Days',
                'Start Time',
                'End Time',
                'Status',
                'Created At'
            ]);

            // CSV data
            foreach ($doctors as $doctor) {
                fputcsv($file, [
                    $doctor->name,
                    $doctor->email,
                    $doctor->phone,
                    $doctor->specialization,
                    $doctor->license_number,
                    $doctor->experience_years,
                    $doctor->consultation_fee,
                    $doctor->qualifications,
                    ucfirst($doctor->gender ?? ''),
                    $doctor->date_of_birth ? $doctor->date_of_birth->format('Y-m-d') : '',
                    $doctor->address,
                    is_array($doctor->available_days) ? implode(', ', $doctor->available_days) : '',
                    $doctor->start_time,
                    $doctor->end_time,
                    $doctor->is_active ? 'Active' : 'Inactive',
                    $doctor->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk actions for doctors
     */
    public function bulkActions(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'doctor_ids' => 'required|array',
            'doctor_ids.*' => 'exists:doctors,id'
        ]);

        $doctors = Doctor::whereIn('id', $request->doctor_ids)
            ->forBusiness(Auth::user()->business_id)
            ->get();

        $count = 0;
        DB::beginTransaction();

        try {
            foreach ($doctors as $doctor) {
                switch ($request->action) {
                    case 'activate':
                        $doctor->update(['is_active' => true]);
                        $count++;
                        break;
                    case 'deactivate':
                        $doctor->update(['is_active' => false]);
                        $count++;
                        break;
                    case 'delete':
                        // Delete profile image
                        if ($doctor->profile_image && Storage::exists($doctor->profile_image)) {
                            Storage::delete($doctor->profile_image);
                        }
                        // Delete associated user account
                        if ($doctor->user_id) {
                            $user = User::find($doctor->user_id);
                            if ($user) {
                                $user->delete();
                            }
                        }
                        $doctor->delete();
                        $count++;
                        break;
                }
            }

            DB::commit();

            $actionText = [
                'activate' => 'activated',
                'deactivate' => 'deactivated',
                'delete' => 'deleted'
            ];

            return redirect()->back()->with('success', "{$count} doctors {$actionText[$request->action]} successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to perform bulk action. Please try again.');
        }
    }
}
