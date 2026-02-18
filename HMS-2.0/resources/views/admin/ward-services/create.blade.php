@extends('admin.layouts.app')

@section('content')
@php
$user = Auth::user();
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-2 sm:p-4">
    <div class="max-w-4xl mx-auto">
        {{-- Enhanced Header --}}
        <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl overflow-hidden border border-white/20 mb-6">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-center sm:text-left">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">
                            <i class="fas fa-plus-circle mr-2"></i>Create Ward Service
                        </h1>
                        <p class="text-blue-100 text-sm">Add a new ward service to your hospital</p>
                    </div>

                    <div class="w-full sm:w-auto">
                        <a href="{{ route('admin.ward-services.index') }}"
                            class="group relative overflow-hidden bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto flex items-center justify-center">
                            <div class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                            <span class="relative flex items-center justify-center">
                                <i class="fas fa-arrow-left mr-2"></i>
                                <span>Back to List</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Form --}}
        <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-white/20 overflow-hidden">
            <form action="{{ route('admin.ward-services.store') }}" method="POST" id="wardServiceForm" class="space-y-6">
                @csrf

                <div class="p-6 sm:p-8">
                    {{-- Basic Information Section --}}
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-info-circle text-white text-sm"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">Basic Information</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Service Name --}}
                            <div class="space-y-2">
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-bed mr-1 text-blue-500"></i>Service Name *
                                </label>
                                <input type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('name') border-red-500 @enderror"
                                    placeholder="e.g., General Ward, ICU, Private Room"
                                    required>
                                @error('name')
                                <p class="text-red-500 text-xs mt-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Daily Fee --}}
                            <div class="space-y-2">
                                <label for="daily_fee" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-money-bill-wave mr-1 text-green-500"></i>Daily Fee (৳) *
                                </label>
                                <input type="number"
                                    id="daily_fee"
                                    name="daily_fee"
                                    value="{{ old('daily_fee') }}"
                                    step="0.01"
                                    min="0"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('daily_fee') border-red-500 @enderror"
                                    placeholder="0.00"
                                    required>
                                @error('daily_fee')
                                <p class="text-red-500 text-xs mt-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Duration --}}
                            <div class="space-y-2">
                                <label for="duration_minutes" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-clock mr-1 text-orange-500"></i>Duration (Minutes) *
                                </label>
                                <input type="number"
                                    id="duration_minutes"
                                    name="duration_minutes"
                                    value="{{ old('duration_minutes', 60) }}"
                                    min="1"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('duration_minutes') border-red-500 @enderror"
                                    placeholder="60"
                                    required>
                                @error('duration_minutes')
                                <p class="text-red-500 text-xs mt-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Max Patients --}}
                            <div class="space-y-2">
                                <label for="max_patients_per_slot" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-users mr-1 text-purple-500"></i>Max Patients per Slot *
                                </label>
                                <input type="number"
                                    id="max_patients_per_slot"
                                    name="max_patients_per_slot"
                                    value="{{ old('max_patients_per_slot', 1) }}"
                                    min="1"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('max_patients_per_slot') border-red-500 @enderror"
                                    placeholder="1"
                                    required>
                                @error('max_patients_per_slot')
                                <p class="text-red-500 text-xs mt-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="mt-6 space-y-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-align-left mr-1 text-gray-500"></i>Description
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('description') border-red-500 @enderror"
                                placeholder="Describe the ward service, facilities, and any special features...">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="text-red-500 text-xs mt-1">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Schedule Configuration Section --}}
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-calendar-alt text-white text-sm"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">Schedule Configuration</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Start Time --}}
                            <div class="space-y-2">
                                <label for="start_time" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-play mr-1 text-green-500"></i>Start Time *
                                </label>
                                <input type="time"
                                    id="start_time"
                                    name="start_time"
                                    value="{{ old('start_time', '08:00') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('start_time') border-red-500 @enderror"
                                    required>
                                @error('start_time')
                                <p class="text-red-500 text-xs mt-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- End Time --}}
                            <div class="space-y-2">
                                <label for="end_time" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-stop mr-1 text-red-500"></i>End Time *
                                </label>
                                <input type="time"
                                    id="end_time"
                                    name="end_time"
                                    value="{{ old('end_time', '18:00') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('end_time') border-red-500 @enderror"
                                    required>
                                @error('end_time')
                                <p class="text-red-500 text-xs mt-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>

                        {{-- Available Days --}}
                        <div class="mt-6 space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-calendar-week mr-1 text-blue-500"></i>Available Days *
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
                                @php
                                $days = [
                                'monday' => 'Monday',
                                'tuesday' => 'Tuesday',
                                'wednesday' => 'Wednesday',
                                'thursday' => 'Thursday',
                                'friday' => 'Friday',
                                'saturday' => 'Saturday',
                                'sunday' => 'Sunday'
                                ];
                                $oldDays = old('available_days', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
                                @endphp

                                @foreach($days as $value => $label)
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 transition-all duration-200 {{ in_array($value, $oldDays) ? 'bg-blue-50 border-blue-300' : '' }}">
                                    <input type="checkbox"
                                        name="available_days[]"
                                        value="{{ $value }}"
                                        class="sr-only"
                                        {{ in_array($value, $oldDays) ? 'checked' : '' }}>
                                    <div class="checkbox-custom w-5 h-5 border-2 border-gray-300 rounded mr-2 flex items-center justify-center {{ in_array($value, $oldDays) ? 'bg-blue-500 border-blue-500' : '' }}">
                                        <i class="fas fa-check text-white text-xs {{ in_array($value, $oldDays) ? '' : 'hidden' }}"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>
                            @error('available_days')
                            <p class="text-red-500 text-xs mt-1">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Status Section --}}
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-toggle-on text-white text-sm"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">Status Configuration</h2>
                        </div>

                        <div class="space-y-2">
                            <label for="status" class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-power-off mr-1 text-green-500"></i>Service Status *
                            </label>
                            <select id="status"
                                name="status"
                                class="status-select w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('status') border-red-500 @enderror"
                                required>
                                <option value="">Select Status</option>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                    Active - Service is available for booking
                                </option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                    Inactive - Service is temporarily unavailable
                                </option>
                            </select>
                            @error('status')
                            <p class="text-red-500 text-xs mt-1">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 sm:px-8 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-end gap-3">
                        <a href="{{ route('admin.ward-services.index') }}"
                            class="w-full sm:w-auto px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all duration-200 text-center font-medium">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>

                        <button type="submit"
                            id="submitBtn"
                            class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:scale-105">
                            <i id="submitIcon" class="fas fa-save mr-2"></i>
                            <i id="loadingIcon" class="hidden fas fa-spinner fa-spin mr-2"></i>
                            <span id="submitText">Create Ward Service</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Help Section --}}
        <div class="mt-6 bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-white/20 overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 p-4">
                <h3 class="text-lg font-semibold text-white">
                    <i class="fas fa-lightbulb mr-2"></i>Quick Tips
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                        <div>
                            <strong>Service Name:</strong> Use clear, descriptive names like "General Ward", "ICU", "Private Room"
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                        <div>
                            <strong>Daily Fee:</strong> Set competitive rates based on facilities and services provided
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                        <div>
                            <strong>Duration:</strong> Typical slot duration is 60 minutes, adjust based on service type
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                        <div>
                            <strong>Capacity:</strong> Set realistic patient limits per time slot for quality care
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<style>
    /* Custom checkbox styling */
    .checkbox-custom {
        transition: all 0.2s ease;
    }

    input[type="checkbox"]:checked+.checkbox-custom {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }

    input[type="checkbox"]:checked+.checkbox-custom .fa-check {
        display: inline-block !important;
    }

    /* Enhanced form styling */
    .form-section {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* Select2 custom styling */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 48px;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        line-height: 2.25rem;
        padding-left: 0;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
        height: 46px;
        right: 12px;
    }

    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }

    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .select2-results__option {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
    }

    .select2-results__option--highlighted {
        background-color: #3b82f6;
        color: white;
    }

    .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    /* Backdrop blur fallback */
    .backdrop-blur-sm {
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }

    /* Glassmorphism effect */
    .bg-white\/90 {
        background: rgba(255, 255, 255, 0.9);
    }

    .bg-white\/80 {
        background: rgba(255, 255, 255, 0.8);
    }

    /* Enhanced focus states */
    .focus\:ring-2:focus {
        outline: 2px solid transparent;
        outline-offset: 2px;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
    }

    /* Smooth transitions */
    * {
        transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    /* Button hover effects */
    .group:hover .group-hover\:translate-x-full {
        transform: translateX(100%);
    }

    /* Loading states */
    .fa-spin {
        animation: fa-spin 2s infinite linear;
    }

    @keyframes fa-spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Form validation styling */
    .border-red-500 {
        border-color: #ef4444;
    }

    .text-red-500 {
        color: #ef4444;
    }

    /* Day selection hover effects */
    label:hover .checkbox-custom {
        border-color: #3b82f6;
        background-color: rgba(59, 130, 246, 0.1);
    }

    /* Responsive improvements */
    @media (max-width: 640px) {
        .grid-cols-2 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
    }

    /* Enhanced input styling */
    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="time"]:focus,
    textarea:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        transform: translateY(-1px);
    }

    /* Custom gradient backgrounds */
    .bg-gradient-to-br {
        background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
    }

    /* Section dividers */
    .section-divider {
        background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
        height: 1px;
        margin: 2rem 0;
    }

    /* Help section styling */
    .help-tip {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border-left: 4px solid #f59e0b;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }

    /* Animation for form sections */
    .form-section {
        animation: slideInUp 0.6s ease-out;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2 for all select elements
        $('.status-select').select2({
            theme: 'bootstrap-5',
            placeholder: 'Select Status',
            allowClear: false,
            width: '100%',
            dropdownParent: $('body')
        });

        // Custom checkbox handling
        $('input[type="checkbox"]').on('change', function() {
            const checkbox = $(this);
            const customCheckbox = checkbox.next('.checkbox-custom');
            const checkIcon = customCheckbox.find('.fa-check');
            const label = checkbox.closest('label');

            if (checkbox.is(':checked')) {
                customCheckbox.addClass('bg-blue-500 border-blue-500');
                checkIcon.removeClass('hidden');
                label.addClass('bg-blue-50 border-blue-300');
            } else {
                customCheckbox.removeClass('bg-blue-500 border-blue-500');
                checkIcon.addClass('hidden');
                label.removeClass('bg-blue-50 border-blue-300');
            }
        });

        // Form validation
        $('#wardServiceForm').on('submit', function(e) {
            e.preventDefault();

            // Show loading state
            const submitBtn = $('#submitBtn');
            const submitIcon = $('#submitIcon');
            const loadingIcon = $('#loadingIcon');
            const submitText = $('#submitText');

            submitIcon.addClass('hidden');
            loadingIcon.removeClass('hidden');
            submitText.text('Creating...');
            submitBtn.prop('disabled', true);

            // Validate required fields
            let isValid = true;
            const requiredFields = ['name', 'daily_fee', 'duration_minutes', 'max_patients_per_slot', 'start_time', 'end_time', 'status'];

            requiredFields.forEach(field => {
                const input = $(`[name="${field}"]`);
                if (!input.val()) {
                    input.addClass('border-red-500');
                    isValid = false;
                } else {
                    input.removeClass('border-red-500');
                }
            });

            // Validate available days
            const checkedDays = $('input[name="available_days[]"]:checked').length;
            if (checkedDays === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please select at least one available day.',
                    confirmButtonColor: '#3b82f6'
                });
                isValid = false;
            }

            // Validate time range
            const startTime = $('#start_time').val();
            const endTime = $('#end_time').val();
            if (startTime && endTime && startTime >= endTime) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Time Range',
                    text: 'End time must be after start time.',
                    confirmButtonColor: '#3b82f6'
                });
                $('#start_time, #end_time').addClass('border-red-500');
                isValid = false;
            } else {
                $('#start_time, #end_time').removeClass('border-red-500');
            }

            // Validate daily fee
            const dailyFee = parseFloat($('#daily_fee').val());
            if (dailyFee < 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Fee',
                    text: 'Daily fee cannot be negative.',
                    confirmButtonColor: '#3b82f6'
                });
                $('#daily_fee').addClass('border-red-500');
                isValid = false;
            }

            if (!isValid) {
                // Reset button state
                submitIcon.removeClass('hidden');
                loadingIcon.addClass('hidden');
                submitText.text('Create Ward Service');
                submitBtn.prop('disabled', false);
                return;
            }

            // If validation passes, submit the form
            setTimeout(() => {
                this.submit();
            }, 1000);
        });

        // Real-time validation
        $('input[required], select[required]').on('blur', function() {
            const input = $(this);
            if (!input.val()) {
                input.addClass('border-red-500');
            } else {
                input.removeClass('border-red-500');
            }
        });

        // Time validation
        $('#start_time, #end_time').on('change', function() {
            const startTime = $('#start_time').val();
            const endTime = $('#end_time').val();

            if (startTime && endTime) {
                if (startTime >= endTime) {
                    $('#start_time, #end_time').addClass('border-red-500');
                    showToast('End time must be after start time', 'error');
                } else {
                    $('#start_time, #end_time').removeClass('border-red-500');
                }
            }
        });

        // Fee validation
        $('#daily_fee').on('input', function() {
            const fee = parseFloat($(this).val());
            if (fee < 0) {
                $(this).addClass('border-red-500');
                showToast('Fee cannot be negative', 'error');
            } else {
                $(this).removeClass('border-red-500');
            }
        });

        // Auto-format fee input
        $('#daily_fee').on('blur', function() {
            const value = parseFloat($(this).val());
            if (!isNaN(value)) {
                $(this).val(value.toFixed(2));
            }
        });

        // Character counter for description
        $('#description').on('input', function() {
            const maxLength = 500;
            const currentLength = $(this).val().length;
            const remaining = maxLength - currentLength;

            if (!$('#char-counter').length) {
                $(this).after(`<div id="char-counter" class="text-xs text-gray-500 mt-1"></div>`);
            }

            $('#char-counter').text(`${currentLength}/${maxLength} characters`);

            if (remaining < 50) {
                $('#char-counter').removeClass('text-gray-500').addClass('text-orange-500');
            } else {
                $('#char-counter').removeClass('text-orange-500').addClass('text-gray-500');
            }
        });

        // Keyboard shortcuts
        $(document).keydown(function(e) {
            // Ctrl/Cmd + S to save
            if ((e.ctrlKey || e.metaKey) && e.keyCode === 83) {
                e.preventDefault();
                $('#wardServiceForm').submit();
            }

            // Escape to cancel
            if (e.keyCode === 27) {
                window.location.href = "{{ route('admin.ward-services.index') }}";
            }
        });

        // Auto-save draft functionality (optional)
        let autoSaveTimer;
        $('input, textarea, select').on('input change', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(saveDraft, 30000); // Save after 30 seconds of inactivity
        });

        function saveDraft() {
            const formData = {
                name: $('#name').val(),
                description: $('#description').val(),
                daily_fee: $('#daily_fee').val(),
                duration_minutes: $('#duration_minutes').val(),
                max_patients_per_slot: $('#max_patients_per_slot').val(),
                start_time: $('#start_time').val(),
                end_time: $('#end_time').val(),
                status: $('#status').val(),
                available_days: $('input[name="available_days[]"]:checked').map(function() {
                    return $(this).val();
                }).get()
            };

            localStorage.setItem('ward_service_draft', JSON.stringify(formData));
            showToast('Draft saved automatically', 'info');
        }

        // Load draft on page load
        function loadDraft() {
            const draft = localStorage.getItem('ward_service_draft');
            if (draft) {
                const data = JSON.parse(draft);

                Swal.fire({
                    title: 'Draft Found',
                    text: 'Would you like to restore your previous draft?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, restore it',
                    cancelButtonText: 'No, start fresh'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Restore form data
                        $('#name').val(data.name);
                        $('#description').val(data.description);
                        $('#daily_fee').val(data.daily_fee);
                        $('#duration_minutes').val(data.duration_minutes);
                        $('#max_patients_per_slot').val(data.max_patients_per_slot);
                        $('#start_time').val(data.start_time);
                        $('#end_time').val(data.end_time);
                        $('#status').val(data.status).trigger('change');

                        // Restore checkboxes
                        $('input[name="available_days[]"]').prop('checked', false);
                        if (data.available_days) {
                            data.available_days.forEach(day => {
                                $(`input[name="available_days[]"][value="${day}"]`).prop('checked', true).trigger('change');
                            });
                        }

                        showToast('Draft restored successfully', 'success');
                    } else {
                        localStorage.removeItem('ward_service_draft');
                    }
                });
            }
        }

        // Load draft if exists
        loadDraft();

        // Clear draft on successful submission
        $('#wardServiceForm').on('submit', function() {
            localStorage.removeItem('ward_service_draft');
        });

        // Focus first input
        $('#name').focus();

        // Smooth scroll to error fields
        function scrollToError() {
            const firstError = $('.border-red-500').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
                firstError.focus();
            }
        }

        // Enhanced form animations
        $('.form-section').each(function(index) {
            $(this).css('animation-delay', (index * 0.1) + 's');
        });

        // Tooltip initialization
        $('[data-tooltip]').hover(
            function() {
                const tooltip = $(this).attr('data-tooltip');
                $(this).append(`<div class="tooltip">${tooltip}</div>`);
            },
            function() {
                $('.tooltip').remove();
            }
        );

        // Form progress indicator (optional)
        function updateProgress() {
            const totalFields = $('input[required], select[required]').length + 1; // +1 for checkboxes
            let filledFields = 0;

            $('input[required], select[required]').each(function() {
                if ($(this).val()) filledFields++;
            });

            if ($('input[name="available_days[]"]:checked').length > 0) filledFields++;

            const progress = (filledFields / totalFields) * 100;

            if (!$('#progress-bar').length) {
                $('.bg-gradient-to-r.from-gray-50').prepend(`
                <div class="mb-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Form Progress</span>
                        <span id="progress-text">${Math.round(progress)}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: ${progress}%"></div>
                    </div>
                </div>
            `);
            } else {
                $('#progress-bar').css('width', progress + '%');
                $('#progress-text').text(Math.round(progress) + '%');
            }
        }

        // Update progress on input
        $('input, select').on('input change', updateProgress);
        $('input[type="checkbox"]').on('change', updateProgress);

        // Initial progress update
        updateProgress();
    });

    // Toast notification function
    function showToast(message, type = 'info') {
        const toastColors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };

        const toastIcons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };

        const toast = $(`
        <div class="fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white ${toastColors[type]} transition-all duration-300 transform translate-x-full">
            <div class="flex items-center">
                <i class="fas ${toastIcons[type]} mr-2"></i>
                <span>${message}</span>
            </div>
        </div>
    `);

        $('body').append(toast);

        // Animate in
        setTimeout(() => {
            toast.removeClass('translate-x-full');
        }, 100);

        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.addClass('translate-x-full');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }

    // Form validation helper
    function validateForm() {
        let isValid = true;
        const errors = [];

        // Required field validation
        $('input[required], select[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('border-red-500');
                errors.push(`${$(this).prev('label').text().replace('*', '').trim()} is required`);
                isValid = false;
            } else {
                $(this).removeClass('border-red-500');
            }
        });

        // Available days validation
        if ($('input[name="available_days[]"]:checked').length === 0) {
            errors.push('At least one available day must be selected');
            isValid = false;
        }

        // Time validation
        const startTime = $('#start_time').val();
        const endTime = $('#end_time').val();
        if (startTime && endTime && startTime >= endTime) {
            errors.push('End time must be after start time');
            $('#start_time, #end_time').addClass('border-red-500');
            isValid = false;
        }

        // Fee validation
        const fee = parseFloat($('#daily_fee').val());
        if (fee < 0) {
            errors.push('Daily fee cannot be negative');
            $('#daily_fee').addClass('border-red-500');
            isValid = false;
        }

        if (!isValid && errors.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Errors',
                html: errors.map(error => `• ${error}`).join('<br>'),
                confirmButtonColor: '#3b82f6'
            });
        }

        return isValid;
    }
</script>
@endpush
@endsection