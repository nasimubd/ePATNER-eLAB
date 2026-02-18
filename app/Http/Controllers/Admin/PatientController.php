<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class PatientController extends Controller
{
    /**
     * Display a listing of patients
     */
    public function index(Request $request)
    {
        $query = Patient::with('business')
            ->forBusiness(Auth::user()->business_id);

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->byGender($request->gender);
        }

        // Filter by blood group
        if ($request->filled('blood_group')) {
            $query->byBloodGroup($request->blood_group);
        }

        // Filter by age range
        if ($request->filled('min_age') && $request->filled('max_age')) {
            $query->byAgeRange($request->min_age, $request->max_age);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $patients = $query->latest()->paginate(15)->withQueryString();

        // Get filter options
        $genders = ['male', 'female', 'other'];
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

        return view('admin.patients.index', compact('patients', 'genders', 'bloodGroups'));
    }

    public function apiDetails(Patient $patient)
    {
        // Ensure user can only access patients from their business
        if ($patient->business_id !== Auth::user()->business_id) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $patient->id,
                'full_name' => $patient->full_name,
                'patient_id' => $patient->patient_id,
                'phone' => $patient->phone,
                'email' => $patient->email,
                'age' => $patient->age,
                'gender' => $patient->gender
            ]
        ]);
    }


    /**
     * Show the form for creating a new patient
     */
    public function create()
    {
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $maritalStatuses = ['single', 'married', 'divorced', 'widowed'];
        $relationships = ['spouse', 'parent', 'child', 'sibling', 'friend', 'other'];

        return view('admin.patients.create', compact('bloodGroups', 'maritalStatuses', 'relationships'));
    }

    /**
     * Store a newly created patient
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'phone' => 'required|string|max:20|unique:patients,phone,NULL,id,business_id,' . Auth::user()->business_id,
            'email' => 'nullable|email|unique:patients,email,NULL,id,business_id,' . Auth::user()->business_id,
            'address' => 'required|string',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'national_id' => 'nullable|string|unique:patients,national_id,NULL,id,business_id,' . Auth::user()->business_id,
        ]);

        $patient = new Patient();
        $patient->business_id = Auth::user()->business_id;
        $patient->fill($request->except(['profile_image']));
        $patient->is_active = $request->has('is_active');

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $patient->profile_image = $this->handleImageUpload($request->file('profile_image'));
        }

        $patient->save();

        return redirect()->route('admin.patients.index')
            ->with('success', "Patient created successfully with ID: {$patient->patient_id}");
    }

    /**
     * Display the specified patient
     */
    public function show(Patient $patient)
    {
        // Check if patient belongs to current user's business
        if ($patient->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this patient.');
        }

        return view('admin.patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified patient
     */
    public function edit(Patient $patient)
    {
        // Check if patient belongs to current user's business
        if ($patient->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this patient.');
        }

        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $maritalStatuses = ['single', 'married', 'divorced', 'widowed'];
        $relationships = ['spouse', 'parent', 'child', 'sibling', 'friend', 'other'];

        return view('admin.patients.edit', compact('patient', 'bloodGroups', 'maritalStatuses', 'relationships'));
    }

    /**
     * Update the specified patient
     */
    public function update(Request $request, Patient $patient)
    {
        // Check if patient belongs to current user's business
        if ($patient->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this patient.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'phone' => 'required|string|max:20|unique:patients,phone,' . $patient->id . ',id,business_id,' . Auth::user()->business_id,
            'email' => 'nullable|email|unique:patients,email,' . $patient->id . ',id,business_id,' . Auth::user()->business_id,
            'address' => 'required|string',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'national_id' => 'nullable|string|unique:patients,national_id,' . $patient->id . ',id,business_id,' . Auth::user()->business_id,
        ]);

        $patient->fill($request->except(['profile_image']));
        $patient->is_active = $request->has('is_active');

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image
            if ($patient->profile_image && Storage::exists($patient->profile_image)) {
                Storage::delete($patient->profile_image);
            }
            $patient->profile_image = $this->handleImageUpload($request->file('profile_image'));
        }

        $patient->save();

        return redirect()->route('admin.patients.index')
            ->with('success', 'Patient updated successfully.');
    }

    /**
     * Remove the specified patient from storage
     */
    public function destroy(Patient $patient)
    {
        // Check if patient belongs to current user's business
        if ($patient->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this patient.');
        }

        // Delete profile image
        if ($patient->profile_image && Storage::exists($patient->profile_image)) {
            Storage::delete($patient->profile_image);
        }

        $patientId = $patient->patient_id;
        $patient->delete();

        return redirect()->route('admin.patients.index')
            ->with('success', "Patient {$patientId} deleted successfully.");
    }

    /**
     * Toggle patient status
     */
    public function toggleStatus(Patient $patient)
    {
        // Check if patient belongs to current user's business
        if ($patient->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this patient.');
        }

        $patient->update(['is_active' => !$patient->is_active]);

        $status = $patient->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Patient {$status} successfully.");
    }

    /**
     * Search patients (AJAX)
     */
    public function search(Request $request)
    {
        $query = Patient::forBusiness(Auth::user()->business_id)
            ->active();

        if ($request->filled('q')) {
            $query->search($request->q);
        }

        $patients = $query->limit(10)->get(['id', 'patient_id', 'first_name', 'last_name', 'phone', 'profile_image']);

        return response()->json($patients->map(function ($patient) {
            return [
                'id' => $patient->id,
                'patient_id' => $patient->patient_id,
                'name' => $patient->first_name . ' ' . $patient->last_name,
                'phone' => $patient->phone,
                'avatar' => $patient->profile_image_url
            ];
        }));
    }

    /**
     * Get patient details (AJAX)
     */
    public function getPatient(Patient $patient)
    {
        // Check if patient belongs to current user's business
        if ($patient->business_id !== Auth::user()->business_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id' => $patient->id,
            'patient_id' => $patient->patient_id,
            'name' => $patient->first_name . ' ' . $patient->last_name,
            'age' => $patient->age,
            'gender' => $patient->gender,
            'phone' => $patient->phone,
            'email' => $patient->email,
            'blood_group' => $patient->blood_group,
            'address' => $patient->formatted_address,
            'allergies' => $patient->allergies,
            'medical_history' => $patient->medical_history,
            'current_medications' => $patient->current_medications,
            'avatar' => $patient->profile_image_url
        ]);
    }

    /**
     * Export patients to CSV
     */
    public function export(Request $request)
    {
        $query = Patient::forBusiness(Auth::user()->business_id);

        // Apply same filters as index
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('gender')) {
            $query->byGender($request->gender);
        }
        if ($request->filled('blood_group')) {
            $query->byBloodGroup($request->blood_group);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $patients = $query->get();

        $filename = 'patients_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($patients) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Patient ID',
                'First Name',
                'Last Name',
                'Date of Birth',
                'Age',
                'Gender',
                'Blood Group',
                'Phone',
                'Email',
                'Address',
                'City',
                'Emergency Contact',
                'Emergency Phone',
                'Status',
                'Created At'
            ]);

            // CSV data
            foreach ($patients as $patient) {
                fputcsv($file, [
                    $patient->patient_id,
                    $patient->first_name,
                    $patient->last_name,
                    $patient->date_of_birth->format('Y-m-d'),
                    $patient->age,
                    ucfirst($patient->gender),
                    $patient->blood_group,
                    $patient->phone,
                    $patient->email,
                    $patient->address,
                    $patient->city,
                    $patient->emergency_contact_name,
                    $patient->emergency_contact_phone,
                    $patient->is_active ? 'Active' : 'Inactive',
                    $patient->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk actions for patients
     */
    public function bulkActions(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'patient_ids' => 'required|array',
            'patient_ids.*' => 'exists:patients,id'
        ]);

        $patients = Patient::whereIn('id', $request->patient_ids)
            ->forBusiness(Auth::user()->business_id)
            ->get();

        $count = 0;
        foreach ($patients as $patient) {
            switch ($request->action) {
                case 'activate':
                    $patient->update(['is_active' => true]);
                    $count++;
                    break;
                case 'deactivate':
                    $patient->update(['is_active' => false]);
                    $count++;
                    break;
                case 'delete':
                    if ($patient->profile_image && Storage::exists($patient->profile_image)) {
                        Storage::delete($patient->profile_image);
                    }
                    $patient->delete();
                    $count++;
                    break;
            }
        }

        $actionText = [
            'activate' => 'activated',
            'deactivate' => 'deactivated',
            'delete' => 'deleted'
        ];

        return redirect()->back()->with('success', "{$count} patients {$actionText[$request->action]} successfully.");
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
            $filename = 'patient-profiles/' . uniqid() . '.jpg';

            // Store the image
            Storage::put($filename, $image->getEncoded());

            return $filename;
        }

        return null;
    }

    /**
     * Show patient profile image
     */
    public function showImage(Patient $patient)
    {
        // Check if patient belongs to current user's business
        if ($patient->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this patient.');
        }

        if ($patient->profile_image && Storage::exists($patient->profile_image)) {
            return response()->file(Storage::path($patient->profile_image));
        }

        // Return default avatar
        $gender = $patient->gender === 'female' ? 'female' : 'male';
        return response()->file(public_path("images/avatars/default-{$gender}.png"));
    }

    /**
     * Generate patient report
     */
    public function generateReport(Patient $patient)
    {
        // Check if patient belongs to current user's business
        if ($patient->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this patient.');
        }

        // This would generate a comprehensive patient report
        // Including medical history, visits, lab results, etc.

        return view('admin.patients.report', compact('patient'));
    }
}
