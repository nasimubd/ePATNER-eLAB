@extends('admin.layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Lab Test</h1>
            <p class="text-gray-600 mt-1">Update laboratory test information</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.lab-tests.show', $labTest) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200">
                <i class="fas fa-eye mr-2"></i>
                View Details
            </a>
            <a href="{{ route('admin.lab-tests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Tests
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form action="{{ route('admin.lab-tests.update', $labTest) }}" method="POST" id="labTestForm" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Basic Information Section -->
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-info-circle text-blue-600"></i>
                    </div>
                    Basic Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Test Name -->
                    <div class="lg:col-span-2">
                        <label for="test_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Test Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="test_name" id="test_name" value="{{ old('test_name', $labTest->test_name) }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('test_name') border-red-300 @enderror">
                        @error('test_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Test Code -->
                    <div>
                        <label for="test_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Test Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="test_code" id="test_code" value="{{ old('test_code', $labTest->test_code) }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('test_code') border-red-300 @enderror">
                        @error('test_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select name="department" id="department"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('department') border-red-300 @enderror">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                            <option value="{{ $department }}" {{ old('department', $labTest->department) === $department ? 'selected' : '' }}>{{ $department }}</option>
                            @endforeach
                        </select>
                        @error('department')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sample Type -->
                    <div>
                        <label for="sample_type" class="block text-sm font-medium text-gray-700 mb-2">Sample Type</label>
                        <select name="sample_type" id="sample_type"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('sample_type') border-red-300 @enderror">
                            <option value="">Select Sample Type</option>
                            @foreach($sampleTypes as $sampleType)
                            <option value="{{ $sampleType }}" {{ old('sample_type', $labTest->sample_type) === $sampleType ? 'selected' : '' }}>{{ $sampleType }}</option>
                            @endforeach
                        </select>
                        @error('sample_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Price <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-lg">৳</span>
                            </div>
                            <input type="number" name="price" id="price" value="{{ old('price', $labTest->price) }}" step="0.01" min="0" required
                                class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('price') border-red-300 @enderror">
                        </div>
                        @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Duration -->
                    <div>
                        <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $labTest->duration_minutes) }}" min="1"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('duration_minutes') border-red-300 @enderror">
                        @error('duration_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('description') border-red-300 @enderror">{{ old('description', $labTest->description) }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Instructions Section -->
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-clipboard-list text-green-600"></i>
                    </div>
                    Instructions
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Test Instructions -->
                    <div>
                        <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">Test Instructions</label>
                        <textarea name="instructions" id="instructions" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('instructions') border-red-300 @enderror">{{ old('instructions', $labTest->instructions) }}</textarea>
                        @error('instructions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Preparation Instructions -->
                    <div>
                        <label for="preparation_instructions" class="block text-sm font-medium text-gray-700 mb-2">Preparation Instructions</label>
                        <textarea name="preparation_instructions" id="preparation_instructions" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('preparation_instructions') border-red-300 @enderror">{{ old('preparation_instructions', $labTest->preparation_instructions) }}</textarea>
                        @error('preparation_instructions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Status Section -->
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-toggle-on text-yellow-600"></i>
                    </div>
                    Status Settings
                </h3>

                <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $labTest->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                        <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Active Status</label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="requires_fasting" id="requires_fasting" value="1" {{ old('requires_fasting', $labTest->requires_fasting) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                        <label for="requires_fasting" class="ml-2 text-sm font-medium text-gray-700">Requires Fasting</label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="requires_appointment" id="requires_appointment" value="1" {{ old('requires_appointment', $labTest->requires_appointment) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                        <label for="requires_appointment" class="ml-2 text-sm font-medium text-gray-700">Requires Appointment</label>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Fields marked with <span class="text-red-500">*</span> are required
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.lab-tests.show', $labTest) }}"
                            class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <button type="submit" id="submitBtn"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Update Lab Test
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeForm();
    });

    function initializeForm() {
        setupFormValidation();
        setupPriceFormatting();
    }

    function setupFormValidation() {
        var form = document.getElementById('labTestForm');
        var submitBtn = document.getElementById('submitBtn');

        if (!form || !submitBtn) return;

        form.addEventListener('submit', function(e) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
            submitBtn.disabled = true;
        });
    }

    function setupPriceFormatting() {
        var priceInput = document.getElementById('price');

        if (!priceInput) return;

        priceInput.addEventListener('input', function() {
            var value = parseFloat(this.value);
            if (!isNaN(value) && value >= 0) {
                this.value = value.toFixed(2);
            }
        });
    }
</script>
@endpush