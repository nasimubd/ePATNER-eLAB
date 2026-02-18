@extends('admin.layouts.app')

@section('page-title', 'Edit Doctor')
@section('page-description', 'Update doctor information')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Edit Doctor Information</h2>
                    <p class="text-sm text-gray-600 mt-1">Update details for Dr. {{ $doctor->name }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.doctors.show', $doctor) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View Profile
                    </a>
                    <a href="{{ route('admin.doctors.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Doctors
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <!-- Profile Image Upload -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Profile Image</label>
                <div class="flex items-center space-x-6">
                    <div class="shrink-0">
                        @if($doctor->profile_image)
                        <img id="preview" class="h-16 w-16 object-cover rounded-full border-2 border-gray-300" src="{{ Storage::url($doctor->profile_image) }}" alt="{{ $doctor->name }}">
                        @else
                        <img id="preview" class="h-16 w-16 object-cover rounded-full border-2 border-gray-300" src="https://via.placeholder.com/64x64/e5e7eb/6b7280?text={{ substr($doctor->name, 0, 2) }}" alt="Preview">
                        @endif
                    </div>
                    <label class="block">
                        <span class="sr-only">Choose profile photo</span>
                        <input type="file" name="profile_image" id="profile_image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </label>
                    @if($doctor->profile_image)
                    <p class="text-xs text-gray-500">Leave empty to keep current image</p>
                    @endif
                </div>
                @error('profile_image')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">Basic Information</h3>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $doctor->name) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $doctor->email) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number *</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $doctor->phone) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $doctor->date_of_birth?->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('date_of_birth')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                        <select name="gender" id="gender" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $doctor->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $doctor->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $doctor->gender) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea name="address" id="address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('address', $doctor->address) }}</textarea>
                        @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Professional Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">Professional Information</h3>

                    <!-- Specialization -->
                    <div>
                        <label for="specialization" class="block text-sm font-medium text-gray-700">Specialization *</label>
                        <input type="text" name="specialization" id="specialization" value="{{ old('specialization', $doctor->specialization) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Cardiology, Neurology">
                        @error('specialization')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- License Number -->
                    <div>
                        <label for="license_number" class="block text-sm font-medium text-gray-700">License Number *</label>
                        <input type="text" name="license_number" id="license_number" value="{{ old('license_number', $doctor->license_number) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('license_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Experience Years -->
                    <div>
                        <label for="experience_years" class="block text-sm font-medium text-gray-700">Years of Experience *</label>
                        <input type="number" name="experience_years" id="experience_years" value="{{ old('experience_years', $doctor->experience_years) }}" required min="0" max="50" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('experience_years')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Consultation Fee -->
                    <div>
                        <label for="consultation_fee" class="block text-sm font-medium text-gray-700">Consultation Fee *</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm"></span>
                            </div>
                            <input type="number" name="consultation_fee" id="consultation_fee" value="{{ old('consultation_fee', $doctor->consultation_fee) }}" required min="0" step="0.01" class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    @error('consultation_fee')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Qualifications -->
                <div>
                    <label for="qualifications" class="block text-sm font-medium text-gray-700">Qualifications</label>
                    <textarea name="qualifications" id="qualifications" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., MBBS, MD, Fellowship">{{ old('qualifications', $doctor->qualifications) }}</textarea>
                    @error('qualifications')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bio -->
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                    <textarea name="bio" id="bio" rows="3" maxlength="1000" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Brief description about the doctor">{{ old('bio', $doctor->bio) }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Maximum 1000 characters</p>
                    @error('bio')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
    </div>

    <!-- Schedule Information -->
    <div class="mt-8">
        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2 mb-4">Schedule Information</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Available Days -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-3">Available Days</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($availableDays as $key => $day)
                    <label class="flex items-center">
                        <input type="checkbox" name="available_days[]" value="{{ $key }}"
                            {{ in_array($key, old('available_days', $doctor->available_days ?? [])) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">{{ $day }}</span>
                    </label>
                    @endforeach
                </div>
                @error('available_days')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Time Schedule -->
            <div class="space-y-4">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $doctor->start_time) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('start_time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $doctor->end_time) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('end_time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Status -->
    <div class="mt-6">
        <div class="flex items-center">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $doctor->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            <label for="is_active" class="ml-2 block text-sm text-gray-700">
                Active (Doctor can receive appointments)
            </label>
        </div>
        @error('is_active')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Form Actions -->
    <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-200">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.doctors.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </a>
            <a href="{{ route('admin.doctors.show', $doctor) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                View Profile
            </a>
        </div>

        <div class="flex items-center space-x-4">
            <!-- Status Toggle Button -->
            <form action="{{ route('admin.doctors.toggle-status', $doctor) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium {{ $doctor->is_active ? 'text-red-700 bg-red-100 hover:bg-red-200' : 'text-green-700 bg-green-100 hover:bg-green-200' }} focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $doctor->is_active ? 'focus:ring-red-500' : 'focus:ring-green-500' }}">
                    @if($doctor->is_active)
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                    </svg>
                    Deactivate
                    @else
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Activate
                    @endif
                </button>
            </form>

            <!-- Update Button -->
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Update Doctor
            </button>
        </div>
    </div>
    </form>

    <!-- Danger Zone -->
    <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-red-900">Danger Zone</h3>
                <p class="text-sm text-red-600">Permanently delete this doctor and all associated data.</p>
            </div>
            <form action="{{ route('admin.doctors.destroy', $doctor) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete Dr. {{ $doctor->name }}? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete Doctor
                </button>
            </form>
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
    // Image preview functionality
    document.getElementById('profile_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Character counter for bio
    const bioTextarea = document.getElementById('bio');
    const maxLength = 1000;

    if (bioTextarea) {
        // Initial count
        updateCharacterCount();

        bioTextarea.addEventListener('input', updateCharacterCount);

        function updateCharacterCount() {
            const remaining = maxLength - bioTextarea.value.length;
            const counter = bioTextarea.parentNode.querySelector('.text-gray-500');
            if (counter) {
                counter.textContent = `${remaining} characters remaining`;
                counter.classList.remove('text-gray-500', 'text-yellow-600', 'text-red-600');

                if (remaining < 0) {
                    counter.classList.add('text-red-600');
                } else if (remaining < 100) {
                    counter.classList.add('text-yellow-600');
                } else {
                    counter.classList.add('text-gray-500');
                }
            }
        }
    }

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;

        if (startTime && endTime && startTime >= endTime) {
            e.preventDefault();
            alert('End time must be after start time.');
            return false;
        }
    });
</script>
@endpush
@endsection