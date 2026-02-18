@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Add New Patient</h1>
                    <p class="text-gray-600 mt-1">Create a new patient record</p>
                </div>
                <a href="{{ route('admin.patients.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back
                </a>
            </div>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <svg class="w-5 h-5 text-red-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Success Message -->
        @if (session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <!-- Main Form -->
        <form action="{{ route('admin.patients.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Personal Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- First Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('first_name') border-red-500 @enderror"
                            value="{{ old('first_name') }}"
                            placeholder="Enter first name" required>
                        @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="last_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('last_name') border-red-500 @enderror"
                            value="{{ old('last_name') }}"
                            placeholder="Enter last name" required>
                        @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Age Input -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Age <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="age" id="ageInput"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('date_of_birth') border-red-500 @enderror"
                            value="{{ old('age', old('date_of_birth') ? \Carbon\Carbon::parse(old('date_of_birth'))->age : '') }}"
                            min="0" max="150" placeholder="Enter age in years" required>
                        <!-- Hidden field for actual date_of_birth submission -->
                        <input type="hidden" name="date_of_birth" id="dateOfBirthHidden" value="{{ old('date_of_birth') }}">
                        <div id="dobDisplay" class="mt-1 text-xs text-gray-500"></div>
                        @error('date_of_birth')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>



                    <!-- Gender -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select name="gender"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('gender') border-red-500 @enderror" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="phone"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                            value="{{ old('phone') }}"
                            placeholder="Enter phone number" required>
                        @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                            value="{{ old('email') }}"
                            placeholder="Enter email address">
                        @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Blood Group -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Blood Group</label>
                        <select name="blood_group"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('blood_group') border-red-500 @enderror">
                            <option value="">Select Blood Group</option>
                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bloodGroup)
                            <option value="{{ $bloodGroup }}" {{ old('blood_group') == $bloodGroup ? 'selected' : '' }}>
                                {{ $bloodGroup }}
                            </option>
                            @endforeach
                        </select>
                        @error('blood_group')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- National ID -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">National ID</label>
                        <input type="text" name="national_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('national_id') border-red-500 @enderror"
                            value="{{ old('national_id') }}"
                            placeholder="Enter national ID">
                        @error('national_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Address <span class="text-red-500">*</span>
                    </label>
                    <textarea name="address" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror"
                        placeholder="Enter full address" required>{{ old('address') }}</textarea>
                    @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    Emergency Contact
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Emergency Contact Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Name</label>
                        <input type="text" name="emergency_contact_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('emergency_contact_name') border-red-500 @enderror"
                            value="{{ old('emergency_contact_name') }}"
                            placeholder="Enter contact name">
                        @error('emergency_contact_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Emergency Contact Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                        <input type="tel" name="emergency_contact_phone"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('emergency_contact_phone') border-red-500 @enderror"
                            value="{{ old('emergency_contact_phone') }}"
                            placeholder="Enter contact phone">
                        @error('emergency_contact_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Emergency Contact Relationship -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Relationship</label>
                        <select name="emergency_contact_relationship"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('emergency_contact_relationship') border-red-500 @enderror">
                            <option value="">Select Relationship</option>
                            @foreach(['parent', 'spouse', 'sibling', 'child', 'friend', 'other'] as $relationship)
                            <option value="{{ $relationship }}" {{ old('emergency_contact_relationship') == $relationship ? 'selected' : '' }}>
                                {{ ucfirst($relationship) }}
                            </option>
                            @endforeach
                        </select>
                        @error('emergency_contact_relationship')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Medical Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Medical Information
                </h2>

                <div class="space-y-4">
                    <!-- Medical History -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Medical History</label>
                        <textarea name="medical_history" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('medical_history') border-red-500 @enderror"
                            placeholder="Previous surgeries, chronic conditions, etc.">{{ old('medical_history') }}</textarea>
                        @error('medical_history')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Allergies -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Allergies</label>
                        <textarea name="allergies" rows="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('allergies') border-red-500 @enderror"
                            placeholder="Drug allergies, food allergies, etc.">{{ old('allergies') }}</textarea>
                        @error('allergies')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Medications -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Medications</label>
                        <textarea name="current_medications" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('current_medications') border-red-500 @enderror"
                            placeholder="List current medications and dosages">{{ old('current_medications') }}</textarea>
                        @error('current_medications')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Insurance Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Insurance Provider</label>
                            <input type="text" name="insurance_provider"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('insurance_provider') border-red-500 @enderror"
                                value="{{ old('insurance_provider') }}"
                                placeholder="Enter insurance provider">
                            @error('insurance_provider')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Insurance Number</label>
                            <input type="text" name="insurance_number"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('insurance_number') border-red-500 @enderror"
                                value="{{ old('insurance_number') }}"
                                placeholder="Enter insurance number">
                            @error('insurance_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                        <textarea name="notes" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                            placeholder="Any additional information about the patient">{{ old('notes') }}</textarea>
                        @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Profile Image -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Profile Image
                </h2>

                <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6">
                    <!-- Image Preview -->
                    <div class="flex-shrink-0">
                        <div id="imagePreview" class="hidden">
                            <img id="previewImg" src="" alt="Preview" class="w-24 h-24 rounded-full object-cover border-4 border-gray-200">
                        </div>
                        <div id="defaultAvatar" class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- File Input -->
                    <div class="flex-1">
                        <input type="file" name="profile_image" id="profileImage"
                            class="hidden @error('profile_image') border-red-500 @enderror"
                            accept="image/jpeg,image/png,image/jpg">
                        <label for="profileImage"
                            class="cursor-pointer inline-flex items-center px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 hover:border-gray-400 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Choose Image
                        </label>
                        <p class="mt-2 text-xs text-gray-500">Max size: 2MB. Formats: JPG, PNG</p>
                        @error('profile_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="isActive"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="isActive" class="ml-2 block text-sm text-gray-700">
                            Active Patient
                        </label>
                    </div>

                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 w-full sm:w-auto">
                        <a href="{{ route('admin.patients.index') }}"
                            class="inline-flex items-center justify-center px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" id="submitBtn"
                            class="inline-flex items-center justify-center px-6 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                            Create Patient
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
        <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-sm font-medium text-gray-900">Creating patient...</span>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image preview functionality
        const profileImageInput = document.getElementById('profileImage');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        const defaultAvatar = document.getElementById('defaultAvatar');

        profileImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    this.value = '';
                    return;
                }

                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Only JPG, JPEG and PNG files are allowed');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                    defaultAvatar.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.classList.add('hidden');
                defaultAvatar.classList.remove('hidden');
            }
        });

        // Auto-calculate date of birth when age changes
        const ageInput = document.getElementById('ageInput');
        const dobHiddenInput = document.getElementById('dateOfBirthHidden');
        const dobDisplay = document.getElementById('dobDisplay');

        if (ageInput && dobHiddenInput && dobDisplay) {
            // Function to calculate date of birth from age
            function calculateDateOfBirth(age) {
                const today = new Date();
                const birthYear = today.getFullYear() - parseInt(age);
                // Use January 1st as default birth date for the calculated year
                const dob = new Date(birthYear, 0, 1);
                return dob.toISOString().split('T')[0]; // Format as YYYY-MM-DD
            }

            // Function to update hidden field and display
            function updateDateOfBirth() {
                const age = parseInt(ageInput.value);

                if (age >= 0 && age <= 150 && !isNaN(age)) {
                    const calculatedDob = calculateDateOfBirth(age);
                    dobHiddenInput.value = calculatedDob;

                    // Display the calculated birth year
                    const birthYear = new Date().getFullYear() - age;
                    dobDisplay.textContent = `Approximate birth year: ${birthYear}`;
                    dobDisplay.classList.remove('text-red-500');
                    dobDisplay.classList.add('text-green-600');

                    // Remove error styling
                    ageInput.classList.remove('border-red-500');
                } else if (ageInput.value === '') {
                    // Clear when empty
                    dobHiddenInput.value = '';
                    dobDisplay.textContent = '';
                    dobDisplay.classList.remove('text-red-500', 'text-green-600');
                } else {
                    // Invalid age
                    dobHiddenInput.value = '';
                    dobDisplay.textContent = 'Please enter a valid age (0-150)';
                    dobDisplay.classList.remove('text-green-600');
                    dobDisplay.classList.add('text-red-500');
                    ageInput.classList.add('border-red-500');
                }
            }

            // Update on input change
            ageInput.addEventListener('input', updateDateOfBirth);
            ageInput.addEventListener('blur', updateDateOfBirth);

            // Initialize if there's already a value
            if (ageInput.value) {
                updateDateOfBirth();
            }
        }

        // Form submission handling
        const form = document.querySelector('form');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const submitBtn = document.getElementById('submitBtn');
        const originalSubmitText = submitBtn.innerHTML;

        form.addEventListener('submit', function(e) {
            // Basic validation
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            let firstInvalidField = null;

            requiredFields.forEach(field => {
                // Special validation for age input
                if (field.id === 'ageInput') {
                    const age = parseInt(field.value);
                    if (!field.value.trim() || isNaN(age) || age < 0 || age > 150) {
                        isValid = false;
                        field.classList.add('border-red-500');
                        if (!firstInvalidField) {
                            firstInvalidField = field;
                        }
                    } else {
                        field.classList.remove('border-red-500');
                        // Ensure date_of_birth is calculated and set
                        if (dobHiddenInput && !dobHiddenInput.value) {
                            const calculatedDob = calculateDateOfBirth(age);
                            dobHiddenInput.value = calculatedDob;
                        }
                    }
                } else if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                } else {
                    field.classList.remove('border-red-500');
                }
            });

            // Additional check to ensure date_of_birth is set
            if (isValid && dobHiddenInput && !dobHiddenInput.value && ageInput && ageInput.value) {
                const age = parseInt(ageInput.value);
                if (!isNaN(age) && age >= 0 && age <= 150) {
                    const today = new Date();
                    const birthYear = today.getFullYear() - age;
                    const dob = new Date(birthYear, 0, 1);
                    dobHiddenInput.value = dob.toISOString().split('T')[0];
                }
            }

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields with valid information');
                if (firstInvalidField) {
                    firstInvalidField.focus();
                    firstInvalidField.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
                return;
            }

            // Show loading state
            loadingOverlay.classList.remove('hidden');
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Creating...
        `;
        });

        // Real-time validation feedback
        const inputs = document.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                // Special handling for age input
                if (this.id === 'ageInput') {
                    const age = parseInt(this.value);
                    if (this.hasAttribute('required') && (!this.value.trim() || isNaN(age) || age < 0 || age > 150)) {
                        this.classList.add('border-red-500');
                    } else {
                        this.classList.remove('border-red-500');
                    }
                } else if (this.hasAttribute('required') && !this.value.trim()) {
                    this.classList.add('border-red-500');
                } else {
                    this.classList.remove('border-red-500');
                }
            });

            input.addEventListener('input', function() {
                if (this.classList.contains('border-red-500') && this.value.trim()) {
                    // Special validation for age input
                    if (this.id === 'ageInput') {
                        const age = parseInt(this.value);
                        if (!isNaN(age) && age >= 0 && age <= 150) {
                            this.classList.remove('border-red-500');
                        }
                    } else {
                        this.classList.remove('border-red-500');
                    }
                }
            });
        });

        // Phone number formatting (simple)
        const phoneInputs = document.querySelectorAll('input[type="tel"]');
        phoneInputs.forEach(input => {
            input.addEventListener('input', function() {
                // Remove non-numeric characters except + and -
                this.value = this.value.replace(/[^\d+\-\s()]/g, '');
            });
        });

        // Email validation
        const emailInput = document.querySelector('input[type="email"]');
        if (emailInput) {
            emailInput.addEventListener('blur', function() {
                if (this.value && !this.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                    this.classList.add('border-red-500');
                    this.setCustomValidity('Please enter a valid email address');
                } else {
                    this.classList.remove('border-red-500');
                    this.setCustomValidity('');
                }
            });
        }

        // Reset loading state on page show (for back button)
        window.addEventListener('pageshow', function() {
            loadingOverlay.classList.add('hidden');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalSubmitText;
        });

        // Mobile-specific enhancements
        if (window.innerWidth <= 768) {
            // Add input modes for better mobile experience
            phoneInputs.forEach(input => {
                input.setAttribute('inputmode', 'tel');
            });

            if (emailInput) {
                emailInput.setAttribute('inputmode', 'email');
            }

            const dateInput = document.querySelector('input[type="date"]');
            if (dateInput) {
                dateInput.setAttribute('inputmode', 'numeric');
            }
        }

        // Smooth scroll to first error
        function scrollToFirstError() {
            const firstError = document.querySelector('.border-red-500');
            if (firstError) {
                firstError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                firstError.focus();
            }
        }

        // Focus first input on load
        const firstInput = document.querySelector('input[name="first_name"]');
        if (firstInput) {
            firstInput.focus();
        }

        // Prevent form resubmission
        let isSubmitting = false;
        form.addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            isSubmitting = true;
        });

        console.log('Patient creation form initialized');
    });
</script>
@endpush