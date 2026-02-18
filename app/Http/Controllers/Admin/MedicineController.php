<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Medicine::with('business')
            ->forBusiness(Auth::user()->business_id);

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by medicine type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'expired':
                    $query->expired();
                    break;
                case 'expiring_soon':
                    $query->expiringSoon();
                    break;
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $medicines = $query->paginate(15)->withQueryString();

        // Get filter options
        $medicineTypes = Medicine::getMedicineTypes();

        // Get statistics
        $stats = [
            'total' => Medicine::forBusiness(Auth::user()->business_id)->count(),
            'active' => Medicine::forBusiness(Auth::user()->business_id)->active()->count(),
            'low_stock' => Medicine::forBusiness(Auth::user()->business_id)->lowStock()->count(),
            'expired' => Medicine::forBusiness(Auth::user()->business_id)->expired()->count(),
            'expiring_soon' => Medicine::forBusiness(Auth::user()->business_id)->expiringSoon()->count(),
        ];

        return view('admin.medicines.index', compact('medicines', 'medicineTypes', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $medicineTypes = Medicine::getMedicineTypes();
        return view('admin.medicines.create', compact('medicineTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:255',
            'medicine_type' => 'required|string|in:' . implode(',', array_keys(Medicine::getMedicineTypes())),
            'strength' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'minimum_stock_level' => 'required|integer|min:0',
            'manufacturing_date' => 'nullable|date|before_or_equal:today',
            'expiry_date' => 'nullable|date|after:manufacturing_date',
            'storage_conditions' => 'nullable|string|max:255',
            'side_effects' => 'nullable|string',
            'dosage_instructions' => 'nullable|string',
            'prescription_required' => 'boolean',
            'barcode' => 'nullable|string|unique:medicines,barcode',
            'medicine_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean'
        ]);

        $medicine = new Medicine();
        $medicine->business_id = Auth::user()->business_id;
        $medicine->fill($request->except(['medicine_image']));
        $medicine->prescription_required = $request->has('prescription_required');
        $medicine->is_active = $request->has('is_active');

        // Handle image upload
        if ($request->hasFile('medicine_image')) {
            $medicine->medicine_image = $this->handleImageUpload($request->file('medicine_image'));
        }

        $medicine->save();

        return redirect()->route('admin.medicines.index')
            ->with('success', 'Medicine created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Medicine $medicine)
    {
        // Check if medicine belongs to current user's business
        if ($medicine->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this medicine.');
        }

        return view('admin.medicines.show', compact('medicine'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Medicine $medicine)
    {
        // Check if medicine belongs to current user's business
        if ($medicine->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this medicine.');
        }

        $medicineTypes = Medicine::getMedicineTypes();
        return view('admin.medicines.edit', compact('medicine', 'medicineTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Medicine $medicine)
    {
        // Check if medicine belongs to current user's business
        if ($medicine->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this medicine.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:255',
            'medicine_type' => 'required|string|in:' . implode(',', array_keys(Medicine::getMedicineTypes())),
            'strength' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'minimum_stock_level' => 'required|integer|min:0',
            'manufacturing_date' => 'nullable|date|before_or_equal:today',
            'expiry_date' => 'nullable|date|after:manufacturing_date',
            'storage_conditions' => 'nullable|string|max:255',
            'side_effects' => 'nullable|string',
            'dosage_instructions' => 'nullable|string',
            'prescription_required' => 'boolean',
            'barcode' => 'nullable|string|unique:medicines,barcode,' . $medicine->id,
            'medicine_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean'
        ]);

        $medicine->fill($request->except(['medicine_image']));
        $medicine->prescription_required = $request->has('prescription_required');
        $medicine->is_active = $request->has('is_active');

        // Handle image upload
        if ($request->hasFile('medicine_image')) {
            // Delete old image
            if ($medicine->medicine_image && Storage::exists($medicine->medicine_image)) {
                Storage::delete($medicine->medicine_image);
            }
            $medicine->medicine_image = $this->handleImageUpload($request->file('medicine_image'));
        }

        $medicine->save();

        return redirect()->route('admin.medicines.index')
            ->with('success', 'Medicine updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medicine $medicine)
    {
        // Check if medicine belongs to current user's business
        if ($medicine->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this medicine.');
        }

        // Delete image
        if ($medicine->medicine_image && Storage::exists($medicine->medicine_image)) {
            Storage::delete($medicine->medicine_image);
        }

        $medicine->delete();

        return redirect()->route('admin.medicines.index')
            ->with('success', 'Medicine deleted successfully.');
    }

    /**
     * Toggle medicine status
     */
    public function toggleStatus(Medicine $medicine)
    {
        // Check if medicine belongs to current user's business
        if ($medicine->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this medicine.');
        }

        $medicine->update(['is_active' => !$medicine->is_active]);

        $status = $medicine->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Medicine {$status} successfully.");
    }

    /**
     * Update stock quantity
     */
    public function updateStock(Request $request, Medicine $medicine)
    {
        // Check if medicine belongs to current user's business
        if ($medicine->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this medicine.');
        }

        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'action' => 'required|in:add,subtract,set'
        ]);

        $currentStock = $medicine->stock_quantity;
        $newQuantity = $request->stock_quantity;

        switch ($request->action) {
            case 'add':
                $medicine->stock_quantity = $currentStock + $newQuantity;
                break;
            case 'subtract':
                $medicine->stock_quantity = max(0, $currentStock - $newQuantity);
                break;
            case 'set':
                $medicine->stock_quantity = $newQuantity;
                break;
        }

        $medicine->save();

        return redirect()->back()->with('success', 'Stock updated successfully.');
    }

    /**
     * Generate barcode for medicine
     */
    public function generateBarcode(Medicine $medicine)
    {
        // Check if medicine belongs to current user's business
        if ($medicine->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this medicine.');
        }

        if (!$medicine->barcode) {
            $medicine->barcode = 'MED' . str_pad($medicine->id, 8, '0', STR_PAD_LEFT);
            $medicine->save();
        }

        return redirect()->back()->with('success', 'Barcode generated successfully.');
    }

    /**
     * Export medicines to CSV
     */
    public function export(Request $request)
    {
        $query = Medicine::forBusiness(Auth::user()->business_id);

        // Apply same filters as index
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('type')) {
            $query->byType($request->type);
        }
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'expired':
                    $query->expired();
                    break;
                case 'expiring_soon':
                    $query->expiringSoon();
                    break;
            }
        }

        $medicines = $query->get();

        $filename = 'medicines_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($medicines) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Name',
                'Generic Name',
                'Brand Name',
                'Manufacturer',
                'Batch Number',
                'Type',
                'Strength',
                'Unit Price',
                'Selling Price',
                'Stock Quantity',
                'Minimum Stock',
                'Manufacturing Date',
                'Expiry Date',
                'Prescription Required',
                'Status',
                'Barcode'
            ]);

            // Add data rows
            foreach ($medicines as $medicine) {
                fputcsv($file, [
                    $medicine->id,
                    $medicine->name,
                    $medicine->generic_name,
                    $medicine->brand_name,
                    $medicine->manufacturer,
                    $medicine->batch_number,
                    $medicine->medicine_type,
                    $medicine->strength,
                    $medicine->unit_price,
                    $medicine->selling_price,
                    $medicine->stock_quantity,
                    $medicine->minimum_stock_level,
                    $medicine->manufacturing_date?->format('Y-m-d'),
                    $medicine->expiry_date?->format('Y-m-d'),
                    $medicine->prescription_required ? 'Yes' : 'No',
                    $medicine->is_active ? 'Active' : 'Inactive',
                    $medicine->barcode
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get medicine details for AJAX
     */
    public function getMedicine(Medicine $medicine)
    {
        // Check if medicine belongs to current user's business
        if ($medicine->business_id !== Auth::user()->business_id) {
            abort(403, 'Unauthorized access to this medicine.');
        }

        return response()->json([
            'id' => $medicine->id,
            'name' => $medicine->name,
            'generic_name' => $medicine->generic_name,
            'brand_name' => $medicine->brand_name,
            'strength' => $medicine->strength,
            'selling_price' => $medicine->selling_price,
            'stock_quantity' => $medicine->stock_quantity,
            'medicine_type' => $medicine->medicine_type,
            'prescription_required' => $medicine->prescription_required,
            'is_active' => $medicine->is_active,
            'is_expired' => $medicine->is_expired,
            'is_low_stock' => $medicine->is_low_stock
        ]);
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload($file)
    {
        if ($file && $file->isValid()) {
            // Create image instance and resize
            $image = Image::make($file)
                ->resize(400, 400, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('jpg', 80);

            // Generate unique filename
            $filename = 'medicine-images/' . uniqid() . '.jpg';

            // Store the image
            Storage::put($filename, $image->getEncoded());

            return $filename;
        }

        return null;
    }
}
