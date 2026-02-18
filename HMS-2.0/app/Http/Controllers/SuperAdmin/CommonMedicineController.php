<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CommonMedicine;
use App\Services\CommonMedicineImportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CommonMedicineController extends Controller
{
    public function __construct(
        private CommonMedicineImportService $importService
    ) {}

    public function index(): View
    {
        return view('super-admin.common-medicines.index');
    }

    public function search(Request $request): JsonResponse
    {
        $term = $request->get('search', '');
        $perPage = $request->get('per_page', 20);

        if (empty($term)) {
            $medicines = CommonMedicine::active()
                ->select(['id', 'medicine_id', 'brand_name', 'generic_name', 'company_name', 'dosage_form', 'dosage_strength', 'pack_info'])
                ->orderBy('brand_name')
                ->paginate($perPage);
        } else {
            $medicines = CommonMedicine::fastSearch($term, $perPage);
        }

        return response()->json([
            'success' => true,
            'data' => $medicines->items(),
            'pagination' => [
                'current_page' => $medicines->currentPage(),
                'last_page' => $medicines->lastPage(),
                'per_page' => $medicines->perPage(),
                'total' => $medicines->total(),
            ]
        ]);
    }

    public function show($medicine): JsonResponse
    {
        // Find by ID or medicine_id
        $commonMedicine = CommonMedicine::where('id', $medicine)
            ->orWhere('medicine_id', $medicine)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $commonMedicine
        ]);
    }

    public function showImport()
    {
        return view('super-admin.common-medicines.import');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:100',
            'dosage_form' => 'required|string|max:50',
            'brand_name' => 'required|string|max:150',
            'generic_name' => 'required|string|max:200',
            'dosage_strength' => 'required|string|max:100',
            'pack_info' => 'required|string|max:100',
        ]);

        $medicine = CommonMedicine::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Medicine created successfully',
            'data' => $medicine
        ]);
    }

    public function update(Request $request, CommonMedicine $commonMedicine): JsonResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:100',
            'dosage_form' => 'required|string|max:50',
            'brand_name' => 'required|string|max:150',
            'generic_name' => 'required|string|max:200',
            'dosage_strength' => 'required|string|max:100',
            'pack_info' => 'required|string|max:100',
        ]);

        $commonMedicine->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Medicine updated successfully',
            'data' => $commonMedicine
        ]);
    }

    public function destroy(CommonMedicine $commonMedicine): JsonResponse
    {
        $commonMedicine->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Medicine deactivated successfully'
        ]);
    }

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
            'has_medicine_id' => 'boolean'
        ]);

        try {
            $file = $request->file('file');
            $hasMedicineId = $request->boolean('has_medicine_id', false);

            $result = $this->importService->importFromCsv($file->getPathname(), $hasMedicineId);

            return response()->json([
                'success' => true,
                'message' => 'Import completed successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request): JsonResponse
    {
        try {
            $medicines = CommonMedicine::active()
                ->select(['medicine_id', 'company_name', 'dosage_form', 'brand_name', 'generic_name', 'dosage_strength', 'pack_info'])
                ->orderBy('brand_name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $medicines,
                'total' => $medicines->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $request->validate([
            'medicine_ids' => 'required|array',
            'medicine_ids.*' => 'required|string|exists:common_medicines,medicine_id'
        ]);

        try {
            CommonMedicine::whereIn('medicine_id', $request->medicine_ids)
                ->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Medicines deactivated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkActivate(Request $request): JsonResponse
    {
        $request->validate([
            'medicine_ids' => 'required|array',
            'medicine_ids.*' => 'required|string|exists:common_medicines,medicine_id'
        ]);

        try {
            CommonMedicine::whereIn('medicine_id', $request->medicine_ids)
                ->update(['is_active' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Medicines activated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk activate failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStats(): JsonResponse
    {
        try {
            $stats = [
                'total_medicines' => CommonMedicine::count(),
                'active_medicines' => CommonMedicine::active()->count(),
                'inactive_medicines' => CommonMedicine::where('is_active', false)->count(),
                'total_companies' => CommonMedicine::active()->distinct('company_name')->count(),
                'total_dosage_forms' => CommonMedicine::active()->distinct('dosage_form')->count(),
                'recent_additions' => CommonMedicine::active()
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get stats: ' . $e->getMessage()
            ], 500);
        }
    }
}
