@extends('super-admin.layouts.app')

@section('page-title', 'Edit Hospital')
@section('page-description', 'Update hospital information')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Edit Hospital: {{ $business->hospital_name }}</h3>
                <a href="{{ route('super-admin.businesses.index') }}"
                    class="text-gray-600 hover:text-gray-900 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back to Hospitals</span>
                </a>
            </div>
        </div>

        <form action="{{ route('super-admin.businesses.update', $business) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Hospital Name -->
            <div>
                <label for="hospital_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Hospital Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    id="hospital_name"
                    name="hospital_name"
                    value="{{ old('hospital_name', $business->hospital_name) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 @error('hospital_name') border-red-500 @enderror"
                    placeholder="Enter hospital name"
                    required>
                @error('hospital_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                    Address <span class="text-red-500">*</span>
                </label>
                <textarea id="address"
                    name="address"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 @error('address') border-red-500 @enderror"
                    placeholder="Enter hospital address"
                    required>{{ old('address', $business->address) }}</textarea>
                @error('address')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contact Number -->
            <div>
                <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-2">
                    Contact Number <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    id="contact_number"
                    name="contact_number"
                    value="{{ old('contact_number', $business->contact_number) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 @error('contact_number') border-red-500 @enderror"
                    placeholder="Enter contact number"
                    required>
                @error('contact_number')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current Logo -->
            @if($business->hasLogo())
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Logo</label>
                <div class="flex items-center space-x-4">
                    <img src="{{ $business->logo_base64 }}"
                        alt="{{ $business->hospital_name }}"
                        class="h-20 w-20 object-cover rounded-lg border border-gray-300">
                    <div>
                        <p class="text-sm text-gray-600">Current hospital logo</p>
                        <p class="text-xs text-gray-500">Upload a new image to replace this logo</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Logo Upload -->
            <div>
                <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $business->hasLogo() ? 'Update Logo' : 'Hospital Logo' }}
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="logo" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                                <span>Upload a file</span>
                                <input id="logo" name="logo" type="file" class="sr-only" accept="image/*" onchange="previewImage(this)">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                    </div>
                </div>

                <!-- Image Preview -->
                <div id="imagePreview" class="mt-4 hidden">
                    <img id="preview" class="h-32 w-32 object-cover rounded-lg border border-gray-300" alt="Preview">
                </div>

                @error('logo')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <div class="flex items-center">
                    <input id="is_active"
                        name="is_active"
                        type="checkbox"
                        value="1"
                        {{ old('is_active', $business->is_active) ? 'checked' : '' }}
                        class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Active Hospital
                    </label>
                </div>
                <p class="mt-1 text-sm text-gray-500">Uncheck to deactivate this hospital</p>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('super-admin.businesses.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Cancel
                </a>
                <button type="submit"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Update Hospital
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('imagePreview').classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection