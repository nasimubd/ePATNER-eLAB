@extends('admin.layouts.app')

@section('title', 'Create New Appointment')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Create New Appointment</h2>
                    <a href="{{ route('admin.appointments.index') }}"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition duration-200">
                        Back to Appointments
                    </a>
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
                @endif

                @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <strong>Please correct the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('admin.appointments.store') }}" id="appointmentForm" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Patient & Doctor Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Patient & Doctor</h3>

                            <!-- Patient Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Patient <span class="text-red-500">*</span></label>
                                <select name="patient_id" id="patient_id" required
                                    class="select2-patient mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('patient_id') border-red-500 @enderror">
                                    <option value="">Search and select a patient...</option>
                                    @foreach($patients as $patient)
                                    <option value="{{ $patient->patient_id }}"
                                        data-name="{{ $patient->full_name }}"
                                        data-phone="{{ $patient->phone }}"
                                        data-email="{{ $patient->email }}"
                                        {{ old('patient_id') == $patient->patient_id ? 'selected' : '' }}>
                                        {{ $patient->full_name }} ({{ $patient->patient_id }}) - {{ $patient->phone }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('patient_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>


                            <!-- Doctor Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Doctor <span class="text-red-500">*</span></label>
                                <select name="doctor_id" id="doctor_id" required
                                    class="select2-doctor mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('doctor_id') border-red-500 @enderror">
                                    <option value="">Search and select a doctor...</option>
                                    @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}"
                                        data-fee="{{ $doctor->consultation_fee }}"
                                        data-specialization="{{ $doctor->specialization }}"
                                        {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                        Dr. {{ $doctor->name }} - {{ $doctor->specialization }} (Fee: ${{ number_format($doctor->consultation_fee, 2) }})
                                    </option>
                                    @endforeach
                                    <option value="other" data-fee="custom">Other (Custom Fee)</option>
                                </select>
                                @error('doctor_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>


                            <!-- Appointment Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Appointment Type <span class="text-red-500">*</span></label>
                                <select name="appointment_type" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('appointment_type') border-red-500 @enderror">
                                    <option value="">Select type...</option>
                                    <option value="consultation" {{ old('appointment_type') == 'consultation' ? 'selected' : '' }}>Consultation</option>
                                    <option value="follow_up" {{ old('appointment_type') == 'follow_up' ? 'selected' : '' }}>Follow Up</option>
                                    <option value="checkup" {{ old('appointment_type') == 'checkup' ? 'selected' : '' }}>Check Up</option>
                                    <option value="emergency" {{ old('appointment_type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                                </select>
                                @error('appointment_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Priority -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Priority <span class="text-red-500">*</span></label>
                                <select name="priority" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('priority') border-red-500 @enderror">
                                    <option value="">Select priority...</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Appointment Details -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Appointment Details</h3>

                            <!-- Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date <span class="text-red-500">*</span></label>
                                <input type="date" name="appointment_date" required
                                    value="{{ old('appointment_date', $selectedDate ?? '') }}"
                                    min="{{ date('Y-m-d') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('appointment_date') border-red-500 @enderror">
                                @error('appointment_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Time -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Time <span class="text-red-500">*</span></label>
                                <input type="time" name="appointment_time" required
                                    value="{{ old('appointment_time') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('appointment_time') border-red-500 @enderror">
                                @error('appointment_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Duration -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Duration <span class="text-red-500">*</span></label>
                                <select name="duration" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('duration') border-red-500 @enderror">
                                    <option value="15" {{ old('duration') == '15' ? 'selected' : '' }}>15 minutes</option>
                                    <option value="30" {{ old('duration', '30') == '30' ? 'selected' : '' }}>30 minutes</option>
                                    <option value="45" {{ old('duration') == '45' ? 'selected' : '' }}>45 minutes</option>
                                    <option value="60" {{ old('duration') == '60' ? 'selected' : '' }}>1 hour</option>
                                </select>
                                @error('duration')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Consultation Fee -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Consultation Fee ($) <span class="text-red-500" id="fee-required" style="display: none;">*</span></label>
                                <input type="number" name="consultation_fee" id="consultation_fee"
                                    step="0.01" min="0" value="{{ old('consultation_fee') }}"
                                    placeholder="Select doctor first"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('consultation_fee') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500" id="fee-help">Fee will be auto-filled when you select a doctor</p>
                                @error('consultation_fee')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">Additional Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Chief Complaint -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Chief Complaint</label>
                                <textarea name="chief_complaint" rows="3" maxlength="500"
                                    placeholder="Brief description of the patient's main concern..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('chief_complaint') border-red-500 @enderror">{{ old('chief_complaint') }}</textarea>
                                @error('chief_complaint')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Additional Notes</label>
                                <textarea name="notes" rows="3" maxlength="1000"
                                    placeholder="Any additional notes or special instructions..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                                @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-6 border-t">
                        <a href="{{ route('admin.appointments.index') }}"
                            class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-6 rounded-lg transition duration-200 text-center">
                            Cancel
                        </a>
                        <button type="submit" id="submitBtn"
                            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition duration-200 disabled:opacity-50">
                            <span id="submitText">Create Appointment</span>
                        </button>
                    </div>
                </form>
            </div>
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
        // Initialize Select2 for patient search with custom search
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

                if (text.indexOf(term) > -1 || name.indexOf(term) > -1 || phone.indexOf(term) > -1) {
                    return data;
                }

                return null;
            }
        });

        // Initialize Select2 for doctor search
        $('#doctor_id').select2({
            theme: 'default',
            placeholder: 'Search and select a doctor...',
            allowClear: true,
            width: '100%'
        });

        // Handle doctor selection and fee management
        $('#doctor_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const feeInput = $('#consultation_fee');
            const feeRequired = $('#fee-required');
            const feeHelp = $('#fee-help');

            if (selectedOption.val() === 'other') {
                // Custom fee option selected
                feeInput.prop('required', true);
                feeInput.prop('readonly', false);
                feeInput.val('');
                feeInput.attr('placeholder', 'Enter custom consultation fee');
                feeRequired.show();
                feeHelp.text('Please enter the consultation fee for this appointment');
            } else if (selectedOption.val() && selectedOption.data('fee')) {
                // Doctor with fee selected
                const fee = parseFloat(selectedOption.data('fee')).toFixed(2);
                feeInput.prop('required', false);
                feeInput.prop('readonly', true);
                feeInput.val(fee);
                feeInput.attr('placeholder', `Default: $${fee}`);
                feeRequired.hide();
                feeHelp.text(`Default fee for Dr. ${selectedOption.text().split(' - ')[0].replace('Dr. ', '')}: $${fee}`);
            } else {
                // No selection
                feeInput.prop('required', false);
                feeInput.prop('readonly', false);
                feeInput.val('');
                feeInput.attr('placeholder', 'Select doctor first');
                feeRequired.hide();
                feeHelp.text('Fee will be auto-filled when you select a doctor');
            }
        });

        const form = document.getElementById('appointmentForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const doctorSelect = document.getElementById('doctor_id');
        const consultationFeeInput = document.getElementById('consultation_fee');

        // Auto-fill consultation fee when doctor is selected
        doctorSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value && selectedOption.dataset.fee) {
                consultationFeeInput.placeholder = `Default: $${parseFloat(selectedOption.dataset.fee).toFixed(2)}`;
            } else {
                consultationFeeInput.placeholder = 'Auto-filled from doctor';
            }
        });

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

        // Auto-hide success/error messages
        const alerts = document.querySelectorAll('.bg-green-100, .bg-red-100');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            }, 5000);
        });

        // Prevent past dates
        const dateInput = document.querySelector('input[name="appointment_date"]');
        if (dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.setAttribute('min', today);
        }

        // Character counters for textareas
        const textareas = document.querySelectorAll('textarea[maxlength]');
        textareas.forEach(function(textarea) {
            const maxLength = textarea.getAttribute('maxlength');
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
        });

        // Focus first input
        const firstInput = form.querySelector('input, select');
        if (firstInput && firstInput.id !== 'patient_id') {
            firstInput.focus();
        }

        // Initialize doctor fee if pre-selected
        if (doctorSelect.value) {
            const selectedOption = doctorSelect.options[doctorSelect.selectedIndex];
            if (selectedOption.dataset.fee) {
                consultationFeeInput.placeholder = `Default: $${parseFloat(selectedOption.dataset.fee).toFixed(2)}`;
            }
        }
    });
</script>
@endpush