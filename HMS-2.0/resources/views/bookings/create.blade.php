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
            <div class="bg-gradient-to-r from-blue-600 via-indigo-700 to-purple-700 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-center sm:text-left">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">
                            <i class="fas fa-plus-circle mr-2"></i>Create New Booking
                        </h1>
                        <p class="text-blue-100 text-sm">Schedule a new ward or OT booking</p>
                    </div>

                    <div class="w-full sm:w-auto">
                        <a href="{{ route('bookings.index') }}"
                            class="group relative overflow-hidden bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto flex items-center justify-center">
                            <div class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                            <span class="relative flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to List
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Error Messages --}}
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were some errors with your submission</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Success Message --}}
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Form Section --}}
        <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-white/20 overflow-hidden">
            <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm" class="space-y-6">
                @csrf

                <div class="p-6">
                    {{-- Patient Selection Section --}}
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">Patient Information</h2>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            {{-- Patient Selection --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Patient <span class="text-red-500">*</span></label>

                                <select name="patient_id" id="patient_id" required
                                    class="select2-patient mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('patient_id') border-red-500 @enderror">
                                    <option value="">Search and select a patient...</option>
                                    @if(isset($patients) && $patients->count() > 0)
                                    @foreach($patients as $patient)
                                    <option value="{{ $patient->patient_id }}"
                                        data-name="{{ $patient->full_name ?? ($patient->first_name . ' ' . $patient->last_name) }}"
                                        data-phone="{{ $patient->phone }}"
                                        data-email="{{ $patient->email }}"
                                        data-patient-id="{{ $patient->patient_id }}"
                                        {{ old('patient_id') == $patient->patient_id ? 'selected' : '' }}>
                                        {{ $patient->full_name ?? ($patient->first_name . ' ' . $patient->last_name) }} ({{ $patient->patient_id }}) - {{ $patient->phone }}
                                    </option>
                                    @endforeach
                                    @else
                                    <option value="" disabled>No patients found</option>
                                    @endif
                                </select>
                                @error('patient_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Service Selection Section --}}
                        <div class="mb-8">
                            <!-- <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-bed text-white text-sm"></i>
                                </div>
                                <h2 class="text-xl font-semibold text-gray-900">Service Selection</h2>
                            </div> -->

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Booking Type --}}
                                <div class="space-y-2">
                                    <label for="booking_type" class="block text-sm font-medium text-gray-700">
                                        <i class="fas fa-list mr-1 text-green-500"></i>Booking Type *
                                    </label>
                                    <select id="booking_type" name="booking_type" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('booking_type') border-red-500 @enderror">
                                        <option value="">Select booking type...</option>
                                        <option value="ward" {{ old('booking_type') == 'ward' ? 'selected' : '' }}>Ward Service</option>
                                        <option value="ot" {{ old('booking_type') == 'ot' ? 'selected' : '' }}>OT Service</option>
                                    </select>
                                    @error('booking_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Service Selection --}}
                                <div class="space-y-2">
                                    <label for="bookable_id" class="block text-sm font-medium text-gray-700">
                                        <i class="fas fa-procedures mr-1 text-purple-500"></i>Select Service *
                                    </label>
                                    <select id="bookable_id" name="bookable_id" required disabled
                                        class="select2-service mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('bookable_id') border-red-500 @enderror">
                                        <option value="">First select booking type...</option>
                                    </select>
                                    @error('bookable_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- OT Room Selection (Only for OT bookings) --}}
                                <div id="otRoomSection" class="space-y-2 hidden md:col-span-2">
                                    <label for="ot_room_id" class="block text-sm font-medium text-gray-700">
                                        <i class="fas fa-door-open mr-1 text-orange-500"></i>Select OT Room *
                                    </label>
                                    <select id="ot_room_id" name="ot_room_id"
                                        class="select2-room mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('ot_room_id') border-red-500 @enderror">
                                        <option value="">Select OT room...</option>
                                    </select>
                                    @error('ot_room_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Service Information Display --}}
                            <div id="serviceInfo" class="hidden mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-2">Service Details</h4>
                                        <div id="serviceDetails" class="text-sm text-gray-600"></div>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-2">Fee Structure</h4>
                                        <div id="feeDetails" class="text-sm text-gray-600"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Date & Time Selection Section --}}
                        <div class="mb-8">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-600 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-calendar-alt text-white text-sm"></i>
                                </div>
                                <h2 class="text-xl font-semibold text-gray-900">Date & Time</h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Booking Date --}}
                                <div class="space-y-2">
                                    <label for="booking_date" class="block text-sm font-medium text-gray-700">
                                        <i class="fas fa-calendar mr-1 text-orange-500"></i>Booking Date *
                                    </label>
                                    <input type="date" id="booking_date" name="booking_date" value="{{ old('booking_date') }}" required
                                        min="{{ date('Y-m-d') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('booking_date') border-red-500 @enderror">
                                    @error('booking_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Booking Time --}}
                                <div class="space-y-2">
                                    <label for="booking_time" class="block text-sm font-medium text-gray-700">
                                        <i class="fas fa-clock mr-1 text-red-500"></i>Booking Time *
                                    </label>
                                    <select id="booking_time" name="booking_time" required disabled
                                        class="select2-time mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('booking_time') border-red-500 @enderror">
                                        <option value="">Select date and service first...</option>
                                    </select>
                                    @error('booking_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Available Slots Display --}}
                            <div id="availableSlots" class="hidden mt-4 p-4 bg-orange-50 rounded-lg border border-orange-200">
                                <h4 class="font-semibold text-gray-900 mb-2">
                                    <i class="fas fa-clock mr-2 text-orange-600"></i>Available Time Slots
                                </h4>
                                <div id="slotsContainer" class="grid grid-cols-2 md:grid-cols-4 gap-2"></div>
                            </div>
                        </div>

                        {{-- Additional Information Section --}}
                        <div class="mb-8">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-info-circle text-white text-sm"></i>
                                </div>
                                <h2 class="text-xl font-semibold text-gray-900">Additional Information</h2>
                            </div>

                            <div class="grid grid-cols-1 gap-6">
                                {{-- Notes --}}
                                <div class="space-y-2">
                                    <label for="notes" class="block text-sm font-medium text-gray-700">
                                        <i class="fas fa-sticky-note mr-1 text-purple-500"></i>Notes
                                    </label>
                                    <textarea id="notes" name="notes" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror"
                                        placeholder="Any special instructions or notes...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Special Requirements (For OT bookings) --}}
                                <div id="specialRequirementsSection" class="space-y-2 hidden">
                                    <label for="special_requirements" class="block text-sm font-medium text-gray-700">
                                        <i class="fas fa-tools mr-1 text-pink-500"></i>Special Requirements
                                    </label>
                                    <div id="requirementsContainer" class="space-y-2">
                                        <div class="flex items-center space-x-2 requirement-item">
                                            <input type="text" name="special_requirements[]"
                                                class="flex-1 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                placeholder="e.g., Extra equipment, Special staff">
                                            <button type="button" onclick="removeRequirementItem(this)"
                                                class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" onclick="addRequirementItem()"
                                        class="mt-2 px-4 py-2 bg-purple-500 text-white rounded-md hover:bg-purple-600 transition-colors">
                                        <i class="fas fa-plus mr-1"></i>Add Requirement
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Fee Summary Section --}}
                        <div class="mb-8">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-money-bill-wave text-white text-sm"></i>
                                </div>
                                <h2 class="text-xl font-semibold text-gray-900">Fee Summary</h2>
                            </div>

                            <div id="feeSummary" class="p-4 bg-green-50 rounded-lg border border-green-200">
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Service Fee:</span>
                                        <span id="serviceFee" class="font-medium text-gray-900">৳0.00</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Room Fee:</span>
                                        <span id="roomFee" class="font-medium text-gray-900">৳0.00</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Equipment Fee:</span>
                                        <span id="equipmentFee" class="font-medium text-gray-900">৳0.00</span>
                                    </div>
                                    <hr class="border-green-300">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-semibold text-gray-900">Total Fee:</span>
                                        <span id="totalFee" class="text-2xl font-bold text-green-600">৳0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-6 border-t">
                        <a href="{{ route('bookings.index') }}"
                            class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-6 rounded-lg transition duration-200 text-center">
                            Cancel
                        </a>
                        <button type="submit" id="submitBtn"
                            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition duration-200 disabled:opacity-50">
                            <span id="submitText">Create Booking</span>
                        </button>
                    </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2 dropdowns
        initializeSelect2();

        // Setup form interactions
        setupFormInteractions();

        // Setup form validation
        setupFormValidation();

        // Load old values if validation fails
        loadOldValues();
    });

    function initializeSelect2() {
        // Patient selection with search (exactly like appointments)
        // Patient selection with search (exactly like appointments)
        $('#patient_id').select2({
            theme: 'default',
            placeholder: 'Search by name, phone, or patient ID...',
            allowClear: true,
            width: '100%',
            matcher: function(params, data) {
                if ($.trim(params.term) === '') {
                    return data;
                }

                const term = params.term.toLowerCase();
                const text = data.text.toLowerCase();
                const name = $(data.element).data('name') ? $(data.element).data('name').toLowerCase() : '';
                const phone = $(data.element).data('phone') ? $(data.element).data('phone').toLowerCase() : '';
                const patientId = $(data.element).data('patient-id') ? $(data.element).data('patient-id').toLowerCase() : '';

                if (text.indexOf(term) > -1 ||
                    name.indexOf(term) > -1 ||
                    phone.indexOf(term) > -1 ||
                    patientId.indexOf(term) > -1) {
                    return data;
                }

                return null;
            }
        });

        // Service selection
        $('#bookable_id').select2({
            theme: 'default',
            placeholder: 'Search and select a service...',
            allowClear: true,
            width: '100%'
        });

        // OT Room selection
        $('#ot_room_id').select2({
            theme: 'default',
            placeholder: 'Select OT room...',
            allowClear: true,
            width: '100%'
        });

        // Booking time selection
        $('#booking_time').select2({
            theme: 'default',
            placeholder: 'Select time slot...',
            allowClear: true,
            width: '100%'
        });
    }

    function setupFormInteractions() {
        // Booking type change
        $('#booking_type').on('change', function() {
            const bookingType = $(this).val();
            loadServices(bookingType);

            if (bookingType === 'ot') {
                $('#otRoomSection').removeClass('hidden');
                $('#specialRequirementsSection').removeClass('hidden');
                loadOtRooms();
            } else {
                $('#otRoomSection').addClass('hidden');
                $('#specialRequirementsSection').addClass('hidden');
                $('#ot_room_id').val(null).trigger('change');
            }

            // Reset dependent fields
            $('#bookable_id').val(null).trigger('change');
            resetTimeSlots();
            resetFeeCalculation();
        });

        // Service selection change
        $('#bookable_id').on('change', function() {
            const serviceId = $(this).val();
            if (serviceId) {
                loadServiceDetails(serviceId);
                loadAvailableSlots();
            } else {
                hideServiceInfo();
                resetTimeSlots();
                resetFeeCalculation();
            }
        });

        // Date change
        $('#booking_date').on('change', function() {
            loadAvailableSlots();
        });

        // OT Room change
        $('#ot_room_id').on('change', function() {
            loadAvailableSlots();
            calculateFees();
        });

        // Time selection change
        $('#booking_time').on('change', function() {
            const selectedTime = $(this).val();
            if (selectedTime) {
                $('.time-slot').removeClass('selected');
                $('.time-slot[data-time="' + selectedTime + '"]').addClass('selected');
            }
        });
    }

    function loadServices(bookingType) {
        if (!bookingType) {
            $('#bookable_id').prop('disabled', true).html('<option value="">First select booking type...</option>');
            return;
        }

        $('#bookable_id').prop('disabled', true).html('<option value="">Loading services...</option>');

        const url = bookingType === 'ward' ? "{{ route('api.ward-services') }}" : "{{ route('api.ot-services') }}";

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                let options = '<option value="">Select a service...</option>';
                response.data.forEach(function(service) {
                    options += '<option value="' + service.id + '" data-fee="' + service.fee + '" data-equipment-fee="' + (service.equipment_fee || 0) + '">' + service.name + ' - ৳' + service.fee + '</option>';
                });
                $('#bookable_id').html(options).prop('disabled', false);
            },
            error: function() {
                $('#bookable_id').html('<option value="">Error loading services</option>');
                alert('Error loading services. Please try again.');
            }
        });
    }

    function loadOtRooms() {
        $('#ot_room_id').html('<option value="">Loading OT rooms...</option>');

        $.ajax({
            url: "{{ route('api.ot-rooms') }}",
            method: 'GET',
            success: function(response) {
                let options = '<option value="">Select OT room...</option>';
                response.data.forEach(function(room) {
                    options += '<option value="' + room.id + '" data-hourly-rate="' + room.hourly_rate + '">' + room.name + ' - ৳' + room.hourly_rate + '/hour</option>';
                });
                $('#ot_room_id').html(options);
            },
            error: function() {
                $('#ot_room_id').html('<option value="">Error loading rooms</option>');
                alert('Error loading OT rooms. Please try again.');
            }
        });
    }

    function loadServiceDetails(serviceId) {
        const bookingType = $('#booking_type').val();
        const url = bookingType === 'ward' ?
            "{{ route('api.ward-services.details', ':id') }}".replace(':id', serviceId) :
            "{{ route('api.ot-services.details', ':id') }}".replace(':id', serviceId);

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                showServiceInfo(response.data);
                calculateFees(response.data);
            },
            error: function() {
                alert('Error loading service details. Please try again.');
            }
        });
    }

    function showServiceInfo(service) {
        let serviceDetails = '<p><strong>Name:</strong> ' + service.name + '</p>';
        serviceDetails += '<p><strong>Description:</strong> ' + (service.description || 'N/A') + '</p>';
        serviceDetails += '<p><strong>Duration:</strong> ' + (service.duration || 'N/A') + ' minutes</p>';

        let feeDetails = '<p><strong>Service Fee:</strong> ৳' + service.fee + '</p>';
        if (service.equipment_fee) {
            feeDetails += '<p><strong>Equipment Fee:</strong> ৳' + service.equipment_fee + '</p>';
        }

        $('#serviceDetails').html(serviceDetails);
        $('#feeDetails').html(feeDetails);
        $('#serviceInfo').removeClass('hidden');
    }

    function hideServiceInfo() {
        $('#serviceInfo').addClass('hidden');
    }

    function loadAvailableSlots() {
        const serviceId = $('#bookable_id').val();
        const bookingDate = $('#booking_date').val();
        const bookingType = $('#booking_type').val();
        const otRoomId = $('#ot_room_id').val();

        if (!serviceId || !bookingDate || !bookingType) {
            resetTimeSlots();
            return;
        }

        if (bookingType === 'ot' && !otRoomId) {
            resetTimeSlots();
            return;
        }

        $('#booking_time').prop('disabled', true).html('<option value="">Loading available slots...</option>');

        const url = bookingType === 'ward' ?
            "{{ route('api.ward-services.slots', ':id') }}".replace(':id', serviceId) :
            "{{ route('api.ot-services.slots', ':id') }}".replace(':id', serviceId);

        const requestData = {
            date: bookingDate
        };

        if (bookingType === 'ot') {
            requestData.ot_room_id = otRoomId;
        }

        $.ajax({
            url: url,
            method: 'GET',
            data: requestData,
            success: function(response) {
                displayAvailableSlots(response.data);
            },
            error: function() {
                resetTimeSlots();
                alert('Error loading available slots. Please try again.');
            }
        });
    }

    function displayAvailableSlots(slots) {
        let options = '<option value="">Select time slot...</option>';
        let slotsHtml = '';

        slots.forEach(function(slot) {
            const isAvailable = slot.available;
            const slotClass = isAvailable ? 'time-slot' : 'time-slot unavailable';

            if (isAvailable) {
                options += '<option value="' + slot.time + '">' + slot.time + '</option>';
            }

            slotsHtml += '<div class="' + slotClass + '" data-time="' + slot.time + '">' + slot.time + '</div>';
        });

        $('#booking_time').html(options).prop('disabled', false);
        $('#slotsContainer').html(slotsHtml);
        $('#availableSlots').removeClass('hidden');

        // Add click handlers for time slots
        $('.time-slot:not(.unavailable)').on('click', function() {
            const time = $(this).data('time');
            $('#booking_time').val(time).trigger('change');
            $('.time-slot').removeClass('selected');
            $(this).addClass('selected');
        });
    }

    function resetTimeSlots() {
        $('#booking_time').prop('disabled', true).html('<option value="">Select date and service first...</option>');
        $('#availableSlots').addClass('hidden');
    }

    function calculateFees(serviceData = null) {
        let serviceFee = 0;
        let equipmentFee = 0;
        let roomFee = 0;

        // Get service fees
        if (serviceData) {
            serviceFee = parseFloat(serviceData.fee || 0);
            equipmentFee = parseFloat(serviceData.equipment_fee || 0);
        } else {
            const selectedService = $('#bookable_id option:selected');
            if (selectedService.val()) {
                serviceFee = parseFloat(selectedService.data('fee') || 0);
                equipmentFee = parseFloat(selectedService.data('equipment-fee') || 0);
            }
        }

        // Get room fee for OT bookings
        if ($('#booking_type').val() === 'ot') {
            const selectedRoom = $('#ot_room_id option:selected');
            if (selectedRoom.val()) {
                roomFee = parseFloat(selectedRoom.data('hourly-rate') || 0);
            }
        }

        const totalFee = serviceFee + equipmentFee + roomFee;

        $('#serviceFee').text('৳' + serviceFee.toFixed(2));
        $('#roomFee').text('৳' + roomFee.toFixed(2));
        $('#equipmentFee').text('৳' + equipmentFee.toFixed(2));
        $('#totalFee').text('৳' + totalFee.toFixed(2));
    }

    function resetFeeCalculation() {
        $('#serviceFee').text('৳0.00');
        $('#roomFee').text('৳0.00');
        $('#equipmentFee').text('৳0.00');
        $('#totalFee').text('৳0.00');
    }

    // Focus first input
    $(document).ready(function() {
        const bookingForm = document.getElementById('bookingForm');
        const firstInput = bookingForm.querySelector('input, select');
        if (firstInput && firstInput.id !== 'patient_id') {
            firstInput.focus();
        }
    });


    function setupFormValidation() {
        const form = document.getElementById('bookingForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');

        // Form submission handler
        form.addEventListener('submit', function(e) {
            // Basic validation
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(function(field) {
                if (field.id === 'patient_id') {
                    // For Select2 field
                    if (!$('#patient_id').val()) {
                        isValid = false;
                        $('#patient_id').next('.select2-container').find('.select2-selection').addClass('border-red-500');
                    } else {
                        $('#patient_id').next('.select2-container').find('.select2-selection').removeClass('border-red-500');
                    }
                } else if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                } else {
                    field.classList.remove('border-red-500');
                }
            });

            // Check OT room if OT booking
            if ($('#booking_type').val() === 'ot' && !$('#ot_room_id').val()) {
                isValid = false;
                $('#ot_room_id').next('.select2-container').find('.select2-selection').addClass('border-red-500');
            }

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }

            // Show loading state
            submitBtn.disabled = true;
            submitText.textContent = 'Creating...';
            submitBtn.classList.add('opacity-75');
        });

        // Real-time validation
        const inputs = form.querySelectorAll('input, select:not(#patient_id), textarea');
        inputs.forEach(function(input) {
            input.addEventListener('blur', function() {
                if (this.hasAttribute('required')) {
                    if (this.value.trim()) {
                        this.classList.remove('border-red-500');
                        this.classList.add('border-green-500');
                    } else {
                        this.classList.remove('border-green-500');
                        this.classList.add('border-red-500');
                    }
                }
            });
        });

        // Select2 validation
        $('#patient_id').on('change', function() {
            if ($(this).val()) {
                $(this).next('.select2-container').find('.select2-selection').removeClass('border-red-500');
                $(this).next('.select2-container').find('.select2-selection').addClass('border-green-500');
            } else {
                $(this).next('.select2-container').find('.select2-selection').removeClass('border-green-500');
                $(this).next('.select2-container').find('.select2-selection').addClass('border-red-500');
            }
        });
    }

    function loadOldValues() {
        // Load old values if validation fails
        const oldBookingType = "{{ old('booking_type') }}";
        const oldBookableId = "{{ old('bookable_id') }}";
        const oldOtRoomId = "{{ old('ot_room_id') }}";
        const oldSpecialRequirements = @json(old('special_requirements', []));

        if (oldBookingType) {
            $('#booking_type').val(oldBookingType).trigger('change');

            setTimeout(function() {
                if (oldBookableId) {
                    $('#bookable_id').val(oldBookableId).trigger('change');
                }

                if (oldOtRoomId) {
                    $('#ot_room_id').val(oldOtRoomId).trigger('change');
                }
            }, 1000);
        }

        // Load special requirements
        if (oldSpecialRequirements.length > 0) {
            $('#requirementsContainer').empty();
            oldSpecialRequirements.forEach(function(requirement, index) {
                if (index === 0) {
                    $('#requirementsContainer').html(
                        '<div class="flex items-center space-x-2 requirement-item">' +
                        '<input type="text" name="special_requirements[]" value="' + requirement + '" ' +
                        'class="flex-1 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" ' +
                        'placeholder="e.g., Extra equipment, Special staff">' +
                        '<button type="button" onclick="removeRequirementItem(this)" ' +
                        'class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">' +
                        '<i class="fas fa-trash"></i>' +
                        '</button>' +
                        '</div>'
                    );
                } else {
                    addRequirementItem(requirement);
                }
            });
        }
    }

    function addRequirementItem(value = '') {
        const newItem = $('<div class="flex items-center space-x-2 requirement-item">' +
            '<input type="text" name="special_requirements[]" value="' + value + '" ' +
            'class="flex-1 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" ' +
            'placeholder="e.g., Extra equipment, Special staff">' +
            '<button type="button" onclick="removeRequirementItem(this)" ' +
            'class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">' +
            '<i class="fas fa-trash"></i>' +
            '</button>' +
            '</div>');

        $('#requirementsContainer').append(newItem);
    }

    function removeRequirementItem(button) {
        $(button).closest('.requirement-item').remove();
    }

    // Auto-hide success/error messages
    const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Prevent past dates
    const dateInput = document.querySelector('input[name="booking_date"]');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);
    }

    // Character counters for textareas
    const textareas = document.querySelectorAll('textarea[maxlength]');
    textareas.forEach(function(textarea) {
        const maxLength = textarea.getAttribute('maxlength');
        if (maxLength) {
            const counter = document.createElement('div');
            counter.className = 'text-xs text-gray-500 text-right mt-1';
            counter.textContent = `0/${maxLength}`;
            textarea.parentNode.appendChild(counter);

            textarea.addEventListener('input', function() {
                const length = this.value.length;
                counter.textContent = `${length}/${maxLength}`;

                if (length > maxLength * 0.9) {
                    counter.className = 'text-xs text-yellow-600 text-right mt-1';
                } else if (length >= maxLength) {
                    counter.className = 'text-xs text-red-600 text-right mt-1';
                } else {
                    counter.className = 'text-xs text-gray-500 text-right mt-1';
                }
            });
        }
    });

    // Focus first input
    const firstInput = form.querySelector('input, select');
    if (firstInput && firstInput.id !== 'patient_id') {
        firstInput.focus();
    }
</script>

<style>
    /* Time slot buttons */
    .time-slot {
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
        cursor: pointer;
        background-color: white;
        text-align: center;
    }

    .time-slot:hover {
        background-color: #eff6ff;
        border-color: #93c5fd;
    }

    .time-slot.selected {
        background-color: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }

    .time-slot.unavailable {
        background-color: #f3f4f6;
        color: #9ca3af;
        border-color: #e5e7eb;
        cursor: not-allowed;
    }

    /* Form animations */
    .requirement-item {
        animation: slideInUp 0.3s ease-out;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Input validation styles */
    .border-red-500 {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 1px #ef4444 !important;
    }

    .border-green-500 {
        border-color: #22c55e !important;
        box-shadow: 0 0 0 1px #22c55e !important;
    }

    /* Mobile responsive improvements */
    @media (max-width: 640px) {
        .requirement-item {
            flex-direction: column;
            gap: 0.5rem;
        }

        .requirement-item button {
            width: 100%;
        }
    }
</style>
@endpush