<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LabTest;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LabTestController extends Controller
{
    /**
     * Get available departments
     */
    private function getDepartments()
    {
        return [
            'Pathology',
            'Radiology',
            'Cardiology',
            'Microbiology',
            'Biochemistry',
            'Haematology',
            'Ultrasonography',
            'ECG & ECHO',
            'Urine & Stool',
            'Renal Function',
            'Cholesterol',
            'Serology',
            'Blood Transfusion',
            'Blood Sugar',
            'Digital X-Ray',
            'Liver Function',
            'Hormone'
        ];
    }

    /**
     * Get available sample types
     */
    private function getSampleTypes()
    {
        return ['Blood', 'Urine', 'Stool', 'Saliva', 'Tissue', 'Swab', 'Other'];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LabTest::with(['medicines']);

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->byDepartment($request->department);
        }

        // Filter by sample type
        if ($request->filled('sample_type')) {
            $query->bySampleType($request->sample_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $labTests = $query->latest()->paginate(15)->withQueryString();

        // Get filter options
        $departments = LabTest::forBusiness(Auth::user()->business_id)
            ->distinct()
            ->pluck('department')
            ->filter()
            ->sort();

        $sampleTypes = LabTest::forBusiness(Auth::user()->business_id)
            ->distinct()
            ->pluck('sample_type')
            ->filter()
            ->sort();

        // Statistics
        $stats = [
            'total' => LabTest::forBusiness(Auth::user()->business_id)->count(),
            'active' => LabTest::forBusiness(Auth::user()->business_id)->active()->count(),
            'inactive' => LabTest::forBusiness(Auth::user()->business_id)->where('is_active', false)->count(),
        ];

        return view('admin.lab-tests.index', compact('labTests', 'departments', 'sampleTypes', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = $this->getDepartments();
        $sampleTypes = $this->getSampleTypes();

        return view('admin.lab-tests.create', compact('departments', 'sampleTypes'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'test_name' => 'required|string|max:255',
            'test_code' => 'required|string|max:50|unique:lab_tests,test_code',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:1',
            'instructions' => 'nullable|string',
            'preparation_instructions' => 'nullable|string',
            'sample_type' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ]);

        LabTest::create([
            'business_id' => Auth::user()->business_id,
            'test_name' => $request->test_name,
            'test_code' => $request->test_code,
            'description' => $request->description,
            'price' => $request->price,
            'duration_minutes' => $request->duration_minutes,
            'instructions' => $request->instructions,
            'preparation_instructions' => $request->preparation_instructions,
            'sample_type' => $request->sample_type,
            'department' => $request->department,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.lab-tests.index')
            ->with('success', 'Lab test created successfully.');
    }

    public function edit(LabTest $labTest)
    {
        $departments = $this->getDepartments();
        $sampleTypes = $this->getSampleTypes();

        return view('admin.lab-tests.edit', compact('labTest', 'departments', 'sampleTypes'));
    }


    /**
     * Display the specified resource.
     */
    public function show(LabTest $labTest)
    {
        $labTest->load(['medicines', 'business']);

        return view('admin.lab-tests.show', compact('labTest'));
    }



    public function update(Request $request, LabTest $labTest)
    {
        $request->validate([
            'test_name' => 'required|string|max:255',
            'test_code' => 'required|string|max:50|unique:lab_tests,test_code,' . $labTest->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:1',
            'instructions' => 'nullable|string',
            'preparation_instructions' => 'nullable|string',
            'sample_type' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ]);

        $labTest->update([
            'test_name' => $request->test_name,
            'test_code' => $request->test_code,
            'description' => $request->description,
            'price' => $request->price,
            'duration_minutes' => $request->duration_minutes,
            'instructions' => $request->instructions,
            'preparation_instructions' => $request->preparation_instructions,
            'sample_type' => $request->sample_type,
            'department' => $request->department,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.lab-tests.index')
            ->with('success', 'Lab test updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LabTest $labTest)
    {
        $labTest->delete();

        return redirect()->route('admin.lab-tests.index')
            ->with('success', 'Lab test deleted successfully.');
    }

    /**
     * Toggle lab test status
     */
    public function toggleStatus(LabTest $labTest)
    {
        $labTest->update(['is_active' => !$labTest->is_active]);

        $status = $labTest->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Lab test {$status} successfully.");
    }

    /**
     * Get medicines for AJAX requests
     */
    public function getMedicines(Request $request)
    {
        $medicines = Medicine::forBusiness(Auth::user()->business_id)
            ->active()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('generic_name', 'LIKE', "%{$search}%");
            })
            ->select('id', 'name', 'generic_name', 'stock_quantity', 'unit_price')
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json($medicines);
    }

    /**
     * Check stock availability for test
     */
    public function checkStock(LabTest $labTest)
    {
        $insufficientStock = $labTest->getInsufficientStockMedicines();
        $hasSufficientStock = $labTest->hasSufficientStock();

        return response()->json([
            'has_sufficient_stock' => $hasSufficientStock,
            'insufficient_medicines' => $insufficientStock->map(function ($medicine) {
                return [
                    'name' => $medicine->name,
                    'required' => $medicine->pivot->quantity_required,
                    'available' => $medicine->stock_quantity,
                    'shortage' => $medicine->pivot->quantity_required - $medicine->stock_quantity
                ];
            })
        ]);
    }

    /**
     * Export lab tests
     */
    public function export(Request $request)
    {
        $query = LabTest::with(['medicines'])
            ->forBusiness(Auth::user()->business_id);

        // Apply same filters as index
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('department')) {
            $query->byDepartment($request->department);
        }

        if ($request->filled('sample_type')) {
            $query->bySampleType($request->sample_type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $labTests = $query->latest()->get();

        $filename = 'lab_tests_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($labTests) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Test Code',
                'Test Name',
                'Department',
                'Sample Type',
                'Price',
                'Duration',
                'Status',
                'Medicines Required',
                'Total Medicine Cost',
                'Created Date'
            ]);

            foreach ($labTests as $test) {
                $medicines = $test->medicines->map(function ($medicine) {
                    return $medicine->name . ' (' . $medicine->pivot->quantity_required . ')';
                })->implode(', ');

                fputcsv($file, [
                    $test->test_code,
                    $test->test_name,
                    $test->department,
                    $test->sample_type,
                    '$' . number_format($test->price, 2),
                    $test->formatted_duration,
                    $test->is_active ? 'Active' : 'Inactive',
                    $medicines,
                    '$' . number_format($test->total_medicine_cost, 2),
                    $test->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
