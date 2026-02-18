@extends('admin.layouts.app')

@section('page-title', 'Edit Medicine')
@section('page-description', 'Update medicine information')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Edit Medicine</h2>
                    <p class="text-sm text-gray-600 mt-1">Update the details for {{ $medicine->name }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.medicines.show', $medicine) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View Details
                    </a>
                    <a href="{{ route('admin.medicines.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.medicines.update', $medicine) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                </div>

                <!-- Medicine Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Medicine Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $medicine->name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror" required>
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Generic Name -->
                <div>
                    <label for="generic_name" class="block text-sm font-medium text-gray-700 mb-2">Generic Name</label>
                    <input type="text" name="generic_name" id="generic_name" value="{{ old('generic_name', $medicine->generic_name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('generic_name') border-red-300 @enderror">
                    @error('generic_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Brand Name -->
                <div>
                    <label for="brand_name" class="block text-sm font-medium text-gray-700 mb-2">Brand Name</label>
                    <input type="text" name="brand_name" id="brand_name" value="{{ old('brand_name', $medicine->brand_name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('brand_name') border-red-300 @enderror">
                    @error('brand_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Manufacturer -->
                <div>
                    <label for="manufacturer" class="block text-sm font-medium text-gray-700 mb-2">Manufacturer</label>
                    <input type="text" name="manufacturer" id="manufacturer" value="{{ old('manufacturer', $medicine->manufacturer) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('manufacturer') border-red-300 @enderror">
                    @error('manufacturer')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Medicine Type -->
                <div>
                    <label for="medicine_type" class="block text-sm font-medium text-gray-700 mb-2">Medicine Type <span class="text-red-500">*</span></label>
                    <select name="medicine_type" id="medicine_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('medicine_type') border-red-300 @enderror" required>
                        <option value="">Select Type</option>
                        @foreach($medicineTypes as $key => $type)
                        <option value="{{ $key }}" {{ old('medicine_type', $medicine->medicine_type) == $key ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('medicine_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Strength -->
                <div>
                    <label for="strength" class="block text-sm font-medium text-gray-700 mb-2">Strength</label>
                    <input type="text" name="strength" id="strength" value="{{ old('strength', $medicine->strength) }}" placeholder="e.g., 500mg, 10ml" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('strength') border-red-300 @enderror">
                    @error('strength')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-300 @enderror">{{ old('description', $medicine->description) }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Pricing & Stock -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing & Stock Information</h3>
                </div>

                <!-- Unit Price -->
                <div>
                    <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-2">Unit Price (Cost) <span class="text-red-500">*</span></label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input type="number" name="unit_price" id="unit_price" step="0.01" min="0" value="{{ old('unit_price', $medicine->unit_price) }}" class="pl-7 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('unit_price') border-red-300 @enderror" required>
                    </div>
                    @error('unit_price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Selling Price -->
                <div>
                    <label for="selling_price" class="block text-sm font-medium text-gray-700 mb-2">Selling Price <span class="text-red-500">*</span></label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input type="number" name="selling_price" id="selling_price" step="0.01" min="0" value="{{ old('selling_price', $medicine->selling_price) }}" class="pl-7 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('selling_price') border-red-300 @enderror" required>
                    </div>
                    @error('selling_price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock Quantity -->
                <div>
                    <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity <span class="text-red-500">*</span></label>
                    <input type="number" name="stock_quantity" id="stock_quantity" min="0" value="{{ old('stock_quantity', $medicine->stock_quantity) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('stock_quantity') border-red-300 @enderror" required>
                    @error('stock_quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Minimum Stock Level -->
                <div>
                    <label for="minimum_stock_level" class="block text-sm font-medium text-gray-700 mb-2">Minimum Stock Level <span class="text-red-500">*</span></label>
                    <input type="number" name="minimum_stock_level" id="minimum_stock_level" min="0" value="{{ old('minimum_stock_level', $medicine->minimum_stock_level) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('minimum_stock_level') border-red-300 @enderror" required>
                    @error('minimum_stock_level')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Batch & Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Batch & Date Information</h3>
                </div>

                <!-- Batch Number -->
                <div>
                    <label for="batch_number" class="block text-sm font-medium text-gray-700 mb-2">Batch Number</label>
                    <input type="text" name="batch_number" id="batch_number" value="{{ old('batch_number', $medicine->batch_number) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('batch_number') border-red-300 @enderror">
                    @error('batch_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Barcode -->
                <div>
                    <label for="barcode" class="block text-sm font-medium text-gray-700 mb-2">Barcode</label>
                    <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $medicine->barcode) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('barcode') border-red-300 @enderror">
                    @error('barcode')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Manufacturing Date -->
                <div>
                    <label for="manufacturing_date" class="block text-sm font-medium text-gray-700 mb-2">Manufacturing Date</label>
                    <input type="date" name="manufacturing_date" id="manufacturing_date" value="{{ old('manufacturing_date', $medicine->manufacturing_date?->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('manufacturing_date') border-red-300 @enderror">
                    @error('manufacturing_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expiry Date -->
                <div>
                    <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                    <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', $medicine->expiry_date?->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('expiry_date') border-red-300 @enderror">
                    @error('expiry_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Additional Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                </div>

                <!-- Storage Conditions -->
                <div>
                    <label for="storage_conditions" class="block text-sm font-medium text-gray-700 mb-2">Storage Conditions</label>
                    <input type="text" name="storage_conditions" id="storage_conditions" value="{{ old('storage_conditions', $medicine->storage_conditions) }}" placeholder="e.g., Store in cool, dry place" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('storage_conditions') border-red-300 @enderror">
                    @error('storage_conditions')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Medicine Image -->
                <div>
                    <label for="medicine_image" class="block text-sm font-medium text-gray-700 mb-2">Medicine Image</label>
                    @if($medicine->medicine_image)
                    <div class="mb-2">
                        <img src="{{ $medicine->image_url }}" alt="{{ $medicine->name }}" class="h-20 w-20 object-cover rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-500 mt-1">Current image</p>
                    </div>
                    @endif
                    <input type="file" name="medicine_image" id="medicine_image" accept="image/*" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('medicine_image') border-red-300 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Upload a new image to replace the current one (JPEG, PNG, JPG - Max: 2MB)</p>
                    @error('medicine_image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Side Effects -->
                <div class="md:col-span-2">
                    <label for="side_effects" class="block text-sm font-medium text-gray-700 mb-2">Side Effects</label>
                    <textarea name="side_effects" id="side_effects" rows="3" placeholder="List common side effects..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('side_effects') border-red-300 @enderror">{{ old('side_effects', $medicine->side_effects) }}</textarea>
                    @error('side_effects')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dosage Instructions -->
                <div class="md:col-span-2">
                    <label for="dosage_instructions" class="block text-sm font-medium text-gray-700 mb-2">Dosage Instructions</label>
                    <textarea name="dosage_instructions" id="dosage_instructions" rows="3" placeholder="Provide dosage instructions..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('dosage_instructions') border-red-300 @enderror">{{ old('dosage_instructions', $medicine->dosage_instructions) }}</textarea>
                    @error('dosage_instructions')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Settings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Settings</h3>
                </div>

                <!-- Prescription Required -->
                <div class="flex items-center">
                    <input type="checkbox" name="prescription_required" id="prescription_required" value="1" {{ old('prescription_required', $medicine->prescription_required) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="prescription_required" class="ml-2 block text-sm text-gray-900">
                        Prescription Required
                    </label>
                </div>

                <!-- Is Active -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $medicine->is_active) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Active
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.medicines.show', $medicine) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Update Medicine
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Auto-calculate profit margin
    document.addEventListener('DOMContentLoaded', function() {
        const unitPriceInput = document.getElementById('unit_price');
        const sellingPriceInput = document.getElementById('selling_price');

        function calculateMargin() {
            const unitPrice = parseFloat(unitPriceInput.value) || 0;
            const sellingPrice = parseFloat(sellingPriceInput.value) || 0;

            if (unitPrice > 0 && sellingPrice > 0) {
                const margin = ((sellingPrice - unitPrice) / unitPrice * 100).toFixed(1);
                // You can display this margin somewhere if needed
            }
        }

        unitPriceInput.addEventListener('input', calculateMargin);
        sellingPriceInput.addEventListener('input', calculateMargin);

        // Validate expiry date is after manufacturing date
        const mfgDateInput = document.getElementById('manufacturing_date');
        const expiryDateInput = document.getElementById('expiry_date');

        function validateDates() {
            const mfgDate = new Date(mfgDateInput.value);
            const expiryDate = new Date(expiryDateInput.value);

            if (mfgDate && expiryDate && expiryDate <= mfgDate) {
                expiryDateInput.setCustomValidity('Expiry date must be after manufacturing date');
            } else {
                expiryDateInput.setCustomValidity('');
            }
        }

        mfgDateInput.addEventListener('change', validateDates);
        expiryDateInput.addEventListener('change', validateDates);
    });
</script>
@endsection