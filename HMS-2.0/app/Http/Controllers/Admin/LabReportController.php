<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LabReport;
use App\Models\LabReportSection;
use App\Models\LabReportField;
use App\Models\ReportTemplate;
use App\Models\LabTest;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Picqer\Barcode\BarcodeGeneratorSVG;

class LabReportController extends Controller
{
    public function index(Request $request)
    {
        $query = LabReport::with(['patient', 'labTest', 'creator']) // Changed from customer
            ->forBusiness(Auth::user()->business_id);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('report_number', 'like', '%' . $request->search . '%')
                    ->orWhereHas('patient', function ($patientQuery) use ($request) { // Changed from customer
                        $patientQuery->where('first_name', 'like', '%' . $request->search . '%')
                            ->orWhere('last_name', 'like', '%' . $request->search . '%')
                            ->orWhere('phone', 'like', '%' . $request->search . '%')
                            ->orWhere('patient_id', 'like', '%' . $request->search . '%')
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$request->search}%"]);
                    });
            });
        }

        if ($request->filled('test_id')) {
            $query->where('lab_test_id', $request->test_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('report_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('report_date', '<=', $request->date_to);
        }

        $reports = $query->latest()->paginate(15)->withQueryString();

        $labTests = LabTest::forBusiness(Auth::user()->business_id)
            ->active()
            ->orderBy('test_name')
            ->get();

        $stats = [
            'total' => LabReport::forBusiness(Auth::user()->business_id)->count(),
            'draft' => LabReport::forBusiness(Auth::user()->business_id)->byStatus('draft')->count(),
            'completed' => LabReport::forBusiness(Auth::user()->business_id)->byStatus('completed')->count(),
            'verified' => LabReport::forBusiness(Auth::user()->business_id)->byStatus('verified')->count(),
            'delivered' => LabReport::forBusiness(Auth::user()->business_id)->byStatus('delivered')->count(),
        ];

        return view('admin.lab-reports.index', compact('reports', 'labTests', 'stats'));
    }

    public function create()
    {
        $labTests = LabTest::forBusiness(Auth::user()->business_id)
            ->active()
            ->orderBy('test_name')
            ->get();

        // Add care-ofs for the dropdown
        $careOfs = \App\Models\CareOf::where('business_id', Auth::user()->business_id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Don't load patients initially - they'll be loaded via AJAX for better performance
        return view('admin.lab-reports.create', compact('labTests', 'careOfs'));
    }

    public function searchPatients(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        $search = $request->get('search', '');
        $limit = $request->get('limit', 20);

        // Query the patients table (matching appointment controller structure)
        $query = \App\Models\Patient::where('patients.business_id', Auth::user()->business_id)
            ->where('is_active', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('patient_id', 'LIKE', "%{$search}%");
            });
        }

        // Get recent patients first (those with recent lab reports)
        $recentPatientIds = LabReport::select('patients.patient_id')
            ->join('patients', 'lab_reports.patient_id', '=', 'patients.id')
            ->where('lab_reports.business_id', Auth::user()->business_id)
            ->where('report_date', '>=', now()->subDays(30))
            ->distinct()
            ->pluck('patients.patient_id')
            ->toArray();

        $patients = $query->orderByRaw($recentPatientIds ? "FIELD(patients.patient_id, '" . implode("','", $recentPatientIds) . "') DESC" : "1")
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->limit($limit)
            ->get(['id', 'patient_id', 'first_name', 'last_name', 'full_name', 'email', 'phone', 'date_of_birth']);

        return response()->json([
            'success' => true,
            'patients' => $patients->map(function ($patient) use ($recentPatientIds) {
                return [
                    'id' => $patient->patient_id, // Use patient_id (TH-001) as the value
                    'database_id' => $patient->id, // Keep database id for reference
                    'patient_id' => $patient->patient_id,
                    'name' => $patient->full_name,
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'email' => $patient->email,
                    'phone' => $patient->phone,
                    'date_of_birth' => $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : null,
                    'age' => $patient->date_of_birth ? $patient->date_of_birth->age : null,
                    'display_name' => $patient->full_name . " ({$patient->patient_id})",
                    'subtitle' => ($patient->email ?: 'No email') . ($patient->phone ? " • {$patient->phone}" : ""),
                    'is_recent' => in_array($patient->patient_id, $recentPatientIds)
                ];
            })
        ]);
    }



    public function store(Request $request)
    {
        $request->validate([
            'lab_id' => 'required|string|max:255',
            'patient_id' => 'required|string', // Changed from exists:patients,id to string
            'lab_test_id' => 'required|exists:lab_tests,id',
            'template_id' => 'required|exists:report_templates,id',
            'report_date' => 'required|date',
            'advised_by' => 'nullable|string|max:255',
            'care_of_id' => 'nullable|exists:care_ofs,id', // ADD THIS LINE
            'investigation_details' => 'nullable|string',
            'sections' => 'required|array',
            'sections.*.section_name' => 'required|string',
            'sections.*.fields' => 'required|array',
            'sections.*.fields.*.field_name' => 'required|string',
            'sections.*.fields.*.field_value' => 'nullable|string',
            'technical_notes' => 'nullable|string',
            'doctor_comments' => 'nullable|string',
            'status' => 'required|in:draft,completed,verified'
        ]);

        // Find patient by patient_id (TH-001) instead of database id - SAME AS APPOINTMENT
        $patient = \App\Models\Patient::where('business_id', Auth::user()->business_id)
            ->where('patient_id', $request->patient_id) // Use patient_id field
            ->where('is_active', true)
            ->first();

        if (!$patient) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['patient_id' => 'Selected patient not found.']);
        }

        DB::transaction(function () use ($request, $patient) {
            $report = LabReport::create([
                'business_id' => Auth::user()->business_id,
                'lab_id' => $request->lab_id,
                'patient_id' => $patient->id, // Use the actual database id for foreign key
                'lab_test_id' => $request->lab_test_id,
                'template_id' => $request->template_id,
                'report_date' => $request->report_date,
                'advised_by' => $request->advised_by ?? 'SELF',
                'care_of_id' => $request->care_of_id, // ADD THIS LINE
                'investigation_details' => $request->investigation_details,
                'technical_notes' => $request->technical_notes,
                'doctor_comments' => $request->doctor_comments,
                'status' => $request->status,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->sections as $sectionIndex => $sectionData) {
                $reportSection = LabReportSection::create([
                    'lab_report_id' => $report->id,
                    'template_section_id' => $sectionData['template_section_id'] ?? null,
                    'section_name' => $sectionData['section_name'],
                    'section_description' => $sectionData['section_description'] ?? null,
                    'section_order' => $sectionIndex + 1,
                ]);

                foreach ($sectionData['fields'] as $fieldIndex => $fieldData) {
                    LabReportField::create([
                        'report_section_id' => $reportSection->id,
                        'template_field_id' => $fieldData['template_field_id'] ?? null,
                        'field_name' => $fieldData['field_name'],
                        'field_label' => $fieldData['field_label'],
                        'field_value' => $fieldData['field_value'],
                        'unit' => $fieldData['unit'] ?? null,
                        'normal_range' => $fieldData['normal_range'] ?? null,
                        'is_abnormal' => $fieldData['is_abnormal'] ?? false,
                        'field_order' => $fieldIndex + 1,
                    ]);
                }
            }
        });

        return redirect()->route('admin.lab-reports.index')
            ->with('success', 'Lab report created successfully.');
    }




    public function show(LabReport $labReport)
    {
        // Ensure the lab report belongs to the current business
        if ($labReport->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this lab report.');
        }

        // Load all necessary relationships
        $labReport->load([
            'patient',
            'labTest',
            'template',
            'sections' => function ($query) {
                $query->orderBy('section_order');
            },
            'sections.fields' => function ($query) {
                $query->orderBy('field_order');
            },
            'creator',
            'verifier'
        ]);

        // Get patient's lab report history (excluding current report)
        $patientHistory = LabReport::where('patient_id', $labReport->patient_id)
            ->where('business_id', Auth::user()->business_id)
            ->where('id', '!=', $labReport->id)
            ->with(['labTest:id,test_name'])
            ->latest('report_date')
            ->limit(10)
            ->get(['id', 'report_number', 'lab_test_id', 'report_date', 'status', 'created_at']);

        return view('admin.lab-reports.show', compact('labReport', 'patientHistory'));
    }


    public function edit(LabReport $labReport)
    {
        $labReport->load([
            'patient', // Changed from customer
            'labTest',
            'template',
            'sections.fields'
        ]);

        $labTests = LabTest::forBusiness(Auth::user()->business_id)
            ->active()
            ->orderBy('test_name')
            ->get();

        // Add care-ofs for the dropdown
        $careOfs = \App\Models\CareOf::where('business_id', Auth::user()->business_id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.lab-reports.edit', compact('labReport', 'labTests', 'careOfs'));
    }

    public function update(Request $request, LabReport $labReport)
    {
        $request->validate([
            'lab_id' => 'required|string|max:255',
            'patient_id' => 'required|string', // Changed from exists:patients,id to string
            'lab_test_id' => 'required|exists:lab_tests,id',
            'template_id' => 'required|exists:report_templates,id',
            'report_date' => 'required|date',
            'advised_by' => 'nullable|string|max:255',
            'care_of_id' => 'nullable|exists:care_ofs,id', // Changed from required to nullable
            'investigation_details' => 'nullable|string',
            'sections' => 'required|array',
            'sections.*.section_name' => 'required|string',
            'sections.*.fields' => 'required|array',
            'sections.*.fields.*.field_name' => 'required|string',
            'sections.*.fields.*.field_value' => 'nullable|string',
            'technical_notes' => 'nullable|string',
            'doctor_comments' => 'nullable|string',
            'status' => 'required|in:draft,completed,verified'
        ]);

        // Find patient by patient_id (TH-001) instead of database id - SAME AS APPOINTMENT
        $patient = \App\Models\Patient::where('business_id', Auth::user()->business_id)
            ->where('patient_id', $request->patient_id) // Use patient_id field
            ->where('is_active', true)
            ->first();

        if (!$patient) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['patient_id' => 'Selected patient not found.']);
        }

        DB::transaction(function () use ($request, $labReport, $patient) {
            $labReport->update([
                'lab_id' => $request->lab_id,
                'patient_id' => $patient->id, // Use the actual database id for foreign key
                'lab_test_id' => $request->lab_test_id,
                'template_id' => $request->template_id,
                'report_date' => $request->report_date,
                'advised_by' => $request->advised_by ?? 'SELF',
                'care_of_id' => $request->care_of_id, // Add care_of_id to update
                'investigation_details' => $request->investigation_details,
                'technical_notes' => $request->technical_notes,
                'doctor_comments' => $request->doctor_comments,
                'status' => $request->status,
            ]);

            // Delete existing sections and fields
            $labReport->sections()->delete();

            // Recreate sections and fields
            foreach ($request->sections as $sectionIndex => $sectionData) {
                $reportSection = LabReportSection::create([
                    'lab_report_id' => $labReport->id,
                    'template_section_id' => $sectionData['template_section_id'] ?? null,
                    'section_name' => $sectionData['section_name'],
                    'section_description' => $sectionData['section_description'] ?? null,
                    'section_order' => $sectionIndex + 1,
                ]);

                foreach ($sectionData['fields'] as $fieldIndex => $fieldData) {
                    LabReportField::create([
                        'report_section_id' => $reportSection->id,
                        'template_field_id' => $fieldData['template_field_id'] ?? null,
                        'field_name' => $fieldData['field_name'],
                        'field_label' => $fieldData['field_label'],
                        'field_value' => $fieldData['field_value'],
                        'unit' => $fieldData['unit'] ?? null,
                        'normal_range' => $fieldData['normal_range'] ?? null,
                        'is_abnormal' => $fieldData['is_abnormal'] ?? false,
                        'field_order' => $fieldIndex + 1,
                    ]);
                }
            }
        });

        return redirect()->route('admin.lab-reports.index')
            ->with('success', 'Lab report updated successfully.');
    }



    public function destroy(LabReport $labReport)
    {
        $labReport->delete();

        return redirect()->route('admin.lab-reports.index')
            ->with('success', 'Lab report deleted successfully.');
    }

    public function updateStatus(Request $request, LabReport $labReport)
    {
        $request->validate([
            'status' => 'required|in:draft,completed,verified,delivered'
        ]);

        $updateData = ['status' => $request->status];

        if ($request->status === 'verified' && $labReport->status !== 'verified') {
            $updateData['verified_at'] = now();
            $updateData['verified_by'] = Auth::id();
        }

        $labReport->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Report status updated successfully.',
            'status' => $labReport->status
        ]);
    }

    public function print(LabReport $labReport)
    {
        $labReport->load([
            'patient', // Changed from customer
            'labTest',
            'business',
            'sections.fields',
            'creator',
            'verifier'
        ]);

        $letterhead = null;

        // Generate barcodes
        $labIdBarcode = $this->generateBarcodeSVG($labReport->id); // Left header - Lab ID (database id)
        $invoiceIdBarcode = $this->generateBarcodeSVG($labReport->lab_id); // Right header - Invoice ID (lab_id field)

        return view('admin.lab-reports.print', compact('labReport', 'letterhead', 'labIdBarcode', 'invoiceIdBarcode'));
    }

    public function printWithLetterhead(LabReport $labReport)
    {
        $labReport->load([
            'patient',
            'labTest',
            'business',
            'sections.fields',
            'creator',
            'verifier'
        ]);

        // Check if letterhead is enabled and get active letterhead for lab reports
        $letterheadEnabled = filter_var(\App\Models\Setting::get('letterhead_enabled', false), FILTER_VALIDATE_BOOLEAN);
        $letterhead = null;
        if ($letterheadEnabled && $labReport->business_id) {
            $letterhead = \App\Models\Letterhead::getActiveForBusiness($labReport->business_id, 'Lab Report');
        }

        $business = $labReport->business;

        // Generate barcodes
        $labIdBarcode = $this->generateBarcodeSVG($labReport->id); // Left header - Lab ID (database id)
        $invoiceIdBarcode = $this->generateBarcodeSVG($labReport->lab_id); // Right header - Invoice ID (lab_id field)

        return view('admin.lab-reports.print-with-letterhead', compact('labReport', 'letterhead', 'business', 'labIdBarcode', 'invoiceIdBarcode'));
    }




    public function getPatientHistory(Patient $patient) // Changed from getCustomerHistory
    {
        // Check if patient belongs to current business
        if ($patient->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this patient.');
        }

        $reports = LabReport::forPatient($patient->id) // Changed from forCustomer
            ->with(['labTest'])
            ->latest()
            ->limit(10)
            ->get(['id', 'report_number', 'lab_test_id', 'report_date', 'status']);

        return response()->json($reports);
    }

    public function duplicate(LabReport $labReport)
    {
        $labReport->load([
            'patient', // Changed from customer
            'labTest',
            'template',
            'sections.fields'
        ]);

        $labTests = LabTest::forBusiness(Auth::user()->business_id)
            ->active()
            ->orderBy('test_name')
            ->get();

        return view('admin.lab-reports.duplicate', compact('labReport', 'labTests'));
    }

    public function export(Request $request)
    {
        $query = LabReport::with(['patient', 'labTest'])
            ->forBusiness(Auth::user()->business_id);

        if ($request->filled('date_from')) {
            $query->whereDate('report_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('report_date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->latest()->get();

        $filename = 'lab_reports_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($reports) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Report Number',
                'Patient Name',
                'Patient ID',
                'Phone',
                'Age',
                'Test Name',
                'Report Date',
                'Status',
                'Created Date'
            ]);

            foreach ($reports as $report) {
                fputcsv($file, [
                    $report->report_number,
                    $report->patient->first_name . ' ' . $report->patient->last_name,
                    $report->patient->patient_id,
                    $report->patient->phone,
                    $report->patient->age,
                    $report->labTest->test_name,
                    $report->report_date->format('Y-m-d'),
                    ucfirst($report->status),
                    $report->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get templates by lab test ID - NEW METHOD
     */
    public function getTemplatesByTest($testId)
    {
        // Verify the test belongs to current business
        $labTest = LabTest::forBusiness(Auth::user()->business_id)->findOrFail($testId);

        $templates = ReportTemplate::forBusiness(Auth::user()->business_id)
            ->forTest($testId)
            ->active()
            ->orderBy('template_name')
            ->get(['id', 'template_name', 'description', 'is_default']);

        return response()->json($templates);
    }

    /**
     * Get template structure with sections and fields - NEW METHOD
     */
    public function getTemplateStructure($templateId)
    {
        // Verify the template belongs to current business
        $template = ReportTemplate::forBusiness(Auth::user()->business_id)
            ->with(['sections.fields' => function ($query) {
                $query->orderBy('field_order');
            }])
            ->findOrFail($templateId);

        // Format the response for the frontend
        $templateData = [
            'id' => $template->id,
            'template_name' => $template->template_name,
            'description' => $template->description,
            'sections' => $template->sections->map(function ($section) {
                return [
                    'id' => $section->id,
                    'section_name' => $section->section_name,
                    'section_description' => $section->section_description,
                    'section_order' => $section->section_order,
                    'is_required' => $section->is_required,
                    'fields' => $section->fields->map(function ($field) {
                        return [
                            'id' => $field->id,
                            'field_name' => $field->field_name,
                            'field_label' => $field->field_label,
                            'field_type' => $field->field_type,
                            'field_options' => $field->field_options,
                            'default_value' => $field->default_value,
                            'unit' => $field->unit,
                            'normal_range' => $field->normal_range,
                            'is_required' => $field->is_required,
                            'field_order' => $field->field_order,
                            'field_value' => $field->default_value // Use default value as initial field value
                        ];
                    })
                ];
            })
        ];

        return response()->json($templateData);
    }

    /**
     * Get lab tests and patient data by lab_id - NEW METHOD
     */
    public function getLabIdData(Request $request)
    {
        $request->validate([
            'lab_id' => 'required|string|max:255'
        ]);

        $labId = $request->lab_id;
        $businessId = Auth::user()->business_id;

        // Find the medical invoice with this lab_id
        $invoice = \App\Models\MedicalInvoice::where('business_id', $businessId)
            ->where('lab_id', $labId)
            ->with(['patient', 'doctor', 'lines.labTest'])
            ->first();

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'No invoice found with this Lab ID.'
            ], 404);
        }

        // Get patient data if exists
        $patient = null;
        if ($invoice->patient_id) {
            $patient = $invoice->patient;
            $patientData = [
                'id' => $patient->patient_id, // Use patient_id as value
                'database_id' => $patient->id,
                'patient_id' => $patient->patient_id,
                'name' => $patient->full_name,
                'first_name' => $patient->first_name,
                'last_name' => $patient->last_name,
                'email' => $patient->email,
                'phone' => $patient->phone,
                'date_of_birth' => $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : null,
                'age' => $patient->date_of_birth ? $patient->date_of_birth->age : null,
                'display_name' => $patient->full_name . " ({$patient->patient_id})",
                'subtitle' => ($patient->email ?: 'No email') . ($patient->phone ? " • {$patient->phone}" : ""),
                'is_recent' => false // Not needed here
            ];
        }

        // Get doctor data if exists
        $doctor = null;
        if ($invoice->doctor_id) {
            $doctor = $invoice->doctor;
            $doctorData = [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'specialization' => $doctor->specialization,
                'license_number' => $doctor->license_number,
                'display_name' => $doctor->name . ($doctor->specialization ? " ({$doctor->specialization})" : "")
            ];
        }

        // Get lab tests from invoice lines
        $tests = $invoice->lines->map(function ($line) {
            $test = $line->labTest;
            return [
                'id' => $test->id,
                'test_name' => $test->test_name,
                'category' => $test->category,
                'description' => $test->description,
                'price' => $test->price,
                'display_name' => $test->test_name . ($test->category ? " ({$test->category})" : '')
            ];
        })->unique('id')->values();

        return response()->json([
            'success' => true,
            'patient' => $patientData ?? null,
            'doctor' => $doctorData ?? null,
            'tests' => $tests,
            'invoice' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'total_amount' => $invoice->total_amount
            ]
        ]);
    }

    /**
     * Generate SVG barcode for given text
     */
    private function generateBarcodeSVG($text, $type = 'TYPE_CODE_128')
    {
        try {
            $generator = new BarcodeGeneratorSVG();
            return $generator->getBarcode($text, $generator::TYPE_CODE_128);
        } catch (\Exception $e) {
            Log::error('Error generating barcode', [
                'text' => $text,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
