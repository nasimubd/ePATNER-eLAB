@extends('admin.layouts.app')

@section('title', 'Edit Appointment')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Appointment</h1>
        <div class="btn-group" role="group">
            <a href="{{ route('admin.appointments.show', $appointment) }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye"></i> View Details
            </a>
            <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
    @endif

    <!-- Hidden data for JavaScript -->
    <div id="appointment-data"
        data-appointment-id="{{ $appointment->id }}"
        data-current-time="{{ $appointment->appointment_time }}"
        data-current-time-formatted="{{ date('g:i A', strtotime($appointment->appointment_time)) }}"
        data-available-slots-url="{{ route('admin.appointments.available-slots') }}"
        style="display: none;">
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Edit Appointment Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Appointment Details</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.appointments.update', $appointment) }}" id="appointmentForm">
                        @csrf
                        @method('PUT')

                        <!-- Patient and Doctor Selection -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="patient_id">Patient <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select class="form-control @error('patient_id') is-invalid @enderror"
                                            id="patient_id" name="patient_id" required>
                                            <option value="">Select Patient</option>
                                            @foreach($patients as $patient)
                                            <option value="{{ $patient->id }}"
                                                {{ old('patient_id', $appointment->patient_id) == $patient->id ? 'selected' : '' }}
                                                data-phone="{{ $patient->phone }}"
                                                data-email="{{ $patient->email }}"
                                                data-age="{{ $patient->age }}"
                                                data-gender="{{ $patient->gender }}">
                                                {{ $patient->name }} - {{ $patient->phone }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary" onclick="showPatientModal()">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @error('patient_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="doctor_id">Doctor <span class="text-danger">*</span></label>
                                    <select class="form-control @error('doctor_id') is-invalid @enderror"
                                        id="doctor_id" name="doctor_id" required>
                                        <option value="">Select Doctor</option>
                                        @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}"
                                            {{ old('doctor_id', $appointment->doctor_id) == $doctor->id ? 'selected' : '' }}
                                            data-specialization="{{ $doctor->specialization }}"
                                            data-fee="{{ $doctor->consultation_fee }}"
                                            data-available-days="{{ json_encode($doctor->available_days) }}"
                                            data-start-time="{{ $doctor->start_time }}"
                                            data-end-time="{{ $doctor->end_time }}">
                                            {{ $doctor->name }} - {{ $doctor->specialization }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('doctor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Date and Time -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="appointment_date">Appointment Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('appointment_date') is-invalid @enderror"
                                        id="appointment_date" name="appointment_date"
                                        value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}"
                                        min="{{ date('Y-m-d') }}" required>
                                    @error('appointment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="appointment_time">Appointment Time <span class="text-danger">*</span></label>
                                    <select class="form-control @error('appointment_time') is-invalid @enderror"
                                        id="appointment_time" name="appointment_time" required>
                                        <option value="">Select Time</option>
                                        <option value="{{ $appointment->appointment_time }}" selected>
                                            {{ $appointment->appointment_time ? date('g:i A', strtotime($appointment->appointment_time)) : '' }}
                                        </option>
                                    </select>
                                    @error('appointment_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="duration">Duration (minutes)</label>
                                    <select class="form-control @error('duration') is-invalid @enderror"
                                        id="duration" name="duration">
                                        <option value="15" {{ old('duration', $appointment->duration) == '15' ? 'selected' : '' }}>15 minutes</option>
                                        <option value="30" {{ old('duration', $appointment->duration) == '30' ? 'selected' : '' }}>30 minutes</option>
                                        <option value="45" {{ old('duration', $appointment->duration) == '45' ? 'selected' : '' }}>45 minutes</option>
                                        <option value="60" {{ old('duration', $appointment->duration) == '60' ? 'selected' : '' }}>1 hour</option>
                                        <option value="90" {{ old('duration', $appointment->duration) == '90' ? 'selected' : '' }}>1.5 hours</option>
                                        <option value="120" {{ old('duration', $appointment->duration) == '120' ? 'selected' : '' }}>2 hours</option>
                                    </select>
                                    @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Appointment Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointment_type">Appointment Type</label>
                                    <select class="form-control @error('appointment_type') is-invalid @enderror"
                                        id="appointment_type" name="appointment_type">
                                        <option value="consultation" {{ old('appointment_type', $appointment->appointment_type) == 'consultation' ? 'selected' : '' }}>Consultation</option>
                                        <option value="follow_up" {{ old('appointment_type', $appointment->appointment_type) == 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                                        <option value="check_up" {{ old('appointment_type', $appointment->appointment_type) == 'check_up' ? 'selected' : '' }}>Check-up</option>
                                        <option value="emergency" {{ old('appointment_type', $appointment->appointment_type) == 'emergency' ? 'selected' : '' }}>Emergency</option>
                                        <option value="procedure" {{ old('appointment_type', $appointment->appointment_type) == 'procedure' ? 'selected' : '' }}>Procedure</option>
                                        <option value="other" {{ old('appointment_type', $appointment->appointment_type) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('appointment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priority">Priority</label>
                                    <select class="form-control @error('priority') is-invalid @enderror"
                                        id="priority" name="priority">
                                        <option value="low" {{ old('priority', $appointment->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', $appointment->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority', $appointment->priority) == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ old('priority', $appointment->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Chief Complaint -->
                        <div class="form-group">
                            <label for="chief_complaint">Chief Complaint</label>
                            <textarea class="form-control @error('chief_complaint') is-invalid @enderror"
                                id="chief_complaint" name="chief_complaint" rows="3"
                                placeholder="Describe the patient's main concern or reason for visit...">{{ old('chief_complaint', $appointment->chief_complaint) }}</textarea>
                            @error('chief_complaint')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="form-group">
                            <label for="notes">Additional Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                id="notes" name="notes" rows="3"
                                placeholder="Any additional notes or special instructions...">{{ old('notes', $appointment->notes) }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="consultation_fee">Consultation Fee</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control @error('consultation_fee') is-invalid @enderror"
                                            id="consultation_fee" name="consultation_fee"
                                            value="{{ old('consultation_fee', $appointment->consultation_fee) }}"
                                            step="0.01" min="0">
                                    </div>
                                    @error('consultation_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_status">Payment Status</label>
                                    <select class="form-control @error('payment_status') is-invalid @enderror"
                                        id="payment_status" name="payment_status">
                                        <option value="pending" {{ old('payment_status', $appointment->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ old('payment_status', $appointment->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="partial" {{ old('payment_status', $appointment->payment_status) == 'partial' ? 'selected' : '' }}>Partial</option>
                                    </select>
                                    @error('payment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control @error('status') is-invalid @enderror"
                                        id="status" name="status">
                                        <option value="scheduled" {{ old('status', $appointment->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="confirmed" {{ old('status', $appointment->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="completed" {{ old('status', $appointment->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ old('status', $appointment->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        <option value="no_show" {{ old('status', $appointment->status) == 'no_show' ? 'selected' : '' }}>No Show</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reminder_sent">Reminder Status</label>
                                    <select class="form-control @error('reminder_sent') is-invalid @enderror"
                                        id="reminder_sent" name="reminder_sent">
                                        <option value="0" {{ old('reminder_sent', $appointment->reminder_sent) == '0' ? 'selected' : '' }}>Not Sent</option>
                                        <option value="1" {{ old('reminder_sent', $appointment->reminder_sent) == '1' ? 'selected' : '' }}>Sent</option>
                                    </select>
                                    @error('reminder_sent')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Appointment
                            </button>
                            <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Current Appointment Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Current Appointment</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <p class="mb-1"><strong>ID:</strong> #{{ $appointment->id }}</p>
                            <p class="mb-1"><strong>Patient:</strong> {{ $appointment->patient->name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Doctor:</strong> {{ $appointment->doctor->name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Date:</strong> {{ $appointment->appointment_date->format('M d, Y') }}</p>
                            <p class="mb-1"><strong>Time:</strong> {{ date('g:i A', strtotime($appointment->appointment_time)) }}</p>
                            <p class="mb-1"><strong>Status:</strong>
                                <span class="badge badge-{{ $appointment->status === 'completed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : ($appointment->status === 'confirmed' ? 'info' : 'warning')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                </span>
                            </p>
                            <p class="mb-0"><strong>Fee:</strong> ${{ number_format($appointment->consultation_fee, 2) }}
                                <span class="badge badge-{{ $appointment->payment_status === 'paid' ? 'success' : ($appointment->payment_status === 'partial' ? 'warning' : 'secondary') }} ml-1">
                                    {{ ucfirst($appointment->payment_status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient Info Card -->
            <div class="card shadow mb-4" id="patientInfoCard">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Patient Information</h6>
                </div>
                <div class="card-body" id="patientInfo">
                    <!-- Patient details will be loaded here -->
                </div>
            </div>

            <!-- Doctor Info Card -->
            <div class="card shadow mb-4" id="doctorInfoCard">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Doctor Information</h6>
                </div>
                <div class="card-body" id="doctorInfo">
                    <!-- Doctor details will be loaded here -->
                </div>
            </div>

            <!-- Available Time Slots -->
            <div class="card shadow mb-4" id="timeSlotsCard" style="display: none;">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Available Time Slots</h6>
                </div>
                <div class="card-body" id="timeSlots">
                    <!-- Available slots will be loaded here -->
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($appointment->status === 'scheduled')
                        <form method="POST" action="{{ route('admin.appointments.confirm', $appointment) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-info btn-sm btn-block">
                                <i class="fas fa-check"></i> Confirm Appointment
                            </button>
                        </form>
                        @endif

                        @if(in_array($appointment->status, ['scheduled', 'confirmed']))
                        <form method="POST" action="{{ route('admin.appointments.complete', $appointment) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm btn-block">
                                <i class="fas fa-check-circle"></i> Mark as Completed
                            </button>
                        </form>

                        <button type="button" class="btn btn-warning btn-sm btn-block" onclick="showCancelModal()">
                            <i class="fas fa-times-circle"></i> Cancel Appointment
                        </button>
                        @endif

                        <a href="{{ route('admin.appointments.duplicate', $appointment) }}" class="btn btn-secondary btn-sm btn-block">
                            <i class="fas fa-copy"></i> Duplicate Appointment
                        </a>

                        <button type="button" class="btn btn-primary btn-sm btn-block" onclick="printAppointment()">
                            <i class="fas fa-print"></i> Print Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Patient Search Modal -->
<div class="modal fade" id="patientSearchModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search Patient</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="text" class="form-control" id="patientSearch"
                        placeholder="Search by name, phone, or email...">
                </div>
                <div id="patientSearchResults">
                    <!-- Search results will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Appointment Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Appointment</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.appointments.cancel', $appointment) }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cancellation_reason">Reason for Cancellation</label>
                        <select class="form-control" id="cancellation_reason" name="cancellation_reason" required>
                            <option value="">Select Reason</option>
                            <option value="patient_request">Patient Request</option>
                            <option value="doctor_unavailable">Doctor Unavailable</option>
                            <option value="emergency">Emergency</option>
                            <option value="rescheduled">Rescheduled</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cancellation_notes">Additional Notes</label>
                        <textarea class="form-control" id="cancellation_notes" name="cancellation_notes" rows="3"></textarea>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="notify_patient" name="notify_patient" value="1" checked>
                        <label class="custom-control-label" for="notify_patient">
                            Notify patient about cancellation
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Cancel Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize form
        initializeForm();

        // Patient selection change
        $('#patient_id').change(function() {
            loadPatientInfo();
        });

        // Doctor selection change
        $('#doctor_id').change(function() {
            loadDoctorInfo();
            loadAvailableTimeSlots();
            updateConsultationFee();
        });

        // Date change
        $('#appointment_date').change(function() {
            loadAvailableTimeSlots();
        });
    });

    function initializeForm() {
        // Load initial data
        loadPatientInfo();
        loadDoctorInfo();
    }

    function loadPatientInfo() {
        const patientId = $('#patient_id').val();
        if (!patientId) {
            $('#patientInfoCard').hide();
            return;
        }

        const selectedOption = $('#patient_id option:selected');
        const patientData = {
            name: selectedOption.text().split(' - ')[0],
            phone: selectedOption.data('phone'),
            email: selectedOption.data('email'),
            age: selectedOption.data('age'),
            gender: selectedOption.data('gender')
        };

        let patientHtml = `
        <div class="row">
            <div class="col-12">
                <h6 class="font-weight-bold">${patientData.name}</h6>
                <p class="mb-1"><i class="fas fa-phone text-primary"></i> ${patientData.phone || 'N/A'}</p>
                <p class="mb-1"><i class="fas fa-envelope text-primary"></i> ${patientData.email || 'N/A'}</p>
                <p class="mb-1"><i class="fas fa-birthday-cake text-primary"></i> ${patientData.age ? patientData.age + ' years old' : 'N/A'}</p>
                <p class="mb-0"><i class="fas fa-venus-mars text-primary"></i> ${patientData.gender ? patientData.gender.charAt(0).toUpperCase() + patientData.gender.slice(1) : 'N/A'}</p>
            </div>
        </div>
    `;

        $('#patientInfo').html(patientHtml);
        $('#patientInfoCard').show();
    }

    function loadDoctorInfo() {
        const doctorId = $('#doctor_id').val();
        if (!doctorId) {
            $('#doctorInfoCard').hide();
            return;
        }

        const selectedOption = $('#doctor_id option:selected');
        const doctorData = {
            name: selectedOption.text().split(' - ')[0],
            specialization: selectedOption.data('specialization'),
            fee: selectedOption.data('fee'),
            availableDays: selectedOption.data('available-days'),
            startTime: selectedOption.data('start-time'),
            endTime: selectedOption.data('end-time')
        };

        let doctorHtml = `
        <div class="row">
            <div class="col-12">
                <h6 class="font-weight-bold">${doctorData.name}</h6>
                <p class="mb-1"><i class="fas fa-stethoscope text-primary"></i> ${doctorData.specialization}</p>
                <p class="mb-1"><i class="fas fa-dollar-sign text-primary"></i> $${doctorData.fee} consultation fee</p>
                <p class="mb-1"><i class="fas fa-clock text-primary"></i> ${doctorData.startTime} - ${doctorData.endTime}</p>
                <p class="mb-0"><i class="fas fa-calendar text-primary"></i> Available: ${Array.isArray(doctorData.availableDays) ? doctorData.availableDays.join(', ') : 'Not specified'}</p>
            </div>
        </div>
    `;

        $('#doctorInfo').html(doctorHtml);
        $('#doctorInfoCard').show();
    }

    function updateConsultationFee() {
        const selectedOption = $('#doctor_id option:selected');
        const fee = selectedOption.data('fee');
        $('#consultation_fee').val(fee || 0);
    }

    function loadAvailableTimeSlots() {
        const doctorId = $('#doctor_id').val();
        const appointmentDate = $('#appointment_date').val();

        // Get data from data attributes
        const appointmentData = $('#appointment-data');
        const currentAppointmentId = appointmentData.data('appointment-id');
        const currentTime = appointmentData.data('current-time');
        const currentTimeFormatted = appointmentData.data('current-time-formatted');
        const availableSlotsUrl = appointmentData.data('available-slots-url');

        if (!doctorId || !appointmentDate) {
            $('#timeSlotsCard').hide();
            return;
        }

        // Show loading
        $('#timeSlots').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading available slots...</div>');
        $('#timeSlotsCard').show();

        // Fetch available time slots
        $.ajax({
            url: availableSlotsUrl,
            method: 'GET',
            data: {
                doctor_id: doctorId,
                date: appointmentDate,
                exclude_appointment: currentAppointmentId
            },
            success: function(response) {
                let timeOptions = '<option value="">Select Time</option>';
                let slotsHtml = '';

                // Add current appointment time as an option
                timeOptions += `<option value="${currentTime}" selected>${currentTimeFormatted} (Current)</option>`;

                if (response.slots && response.slots.length > 0) {
                    response.slots.forEach(function(slot) {
                        timeOptions += `<option value="${slot.time}">${slot.formatted_time}</option>`;
                        slotsHtml += `
                        <button type="button" class="btn btn-outline-primary btn-sm m-1 time-slot-btn" 
                                data-time="${slot.time}" onclick="selectTimeSlot('${slot.time}')">
                            ${slot.formatted_time}
                        </button>
                    `;
                    });

                    $('#timeSlots').html(`
                    <p class="mb-2 text-muted">Current time slot or click to select new time:</p>
                    <button type="button" class="btn btn-primary btn-sm m-1" disabled>
                        ${currentTimeFormatted} (Current)
                    </button>
                    <hr>
                    <div class="time-slots-container">${slotsHtml}</div>
                `);
                } else {
                    $('#timeSlots').html(`
                    <p class="mb-2 text-muted">Current time slot:</p>
                    <button type="button" class="btn btn-primary btn-sm m-1" disabled>
                        ${currentTimeFormatted} (Current)
                    </button>
                    <hr>
                    <p class="text-muted mb-0">No other available time slots for this date.</p>
                `);
                }

                $('#appointment_time').html(timeOptions);
            },
            error: function() {
                $('#timeSlots').html('<p class="text-danger mb-0">Error loading available slots. Please try again.</p>');
            }
        });
    }

    function selectTimeSlot(time) {
        $('#appointment_time').val(time);

        // Update visual selection
        $('.time-slot-btn').removeClass('btn-primary').addClass('btn-outline-primary');
        $(`.time-slot-btn[data-time="${time}"]`).removeClass('btn-outline-primary').addClass('btn-primary');

        toastr.success('Time slot selected successfully!');
    }

    function showPatientModal() {
        $('#patientSearchModal').modal('show');
        loadAllPatients();
    }

    function loadAllPatients() {
        $('#patientSearchResults').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading patients...</div>');

        $.ajax({
            url: '{{ route("admin.patients.search") }}',
            method: 'GET',
            success: function(response) {
                let resultsHtml = '';
                if (response.length > 0) {
                    response.forEach(function(patient) {
                        resultsHtml += `
                        <div class="patient-result border rounded p-3 mb-2" style="cursor: pointer;" 
                             onclick="selectPatient(${patient.id}, '${patient.name}', '${patient.phone}', '${patient.email}', ${patient.age}, '${patient.gender}')">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">${patient.name}</h6>
                                    <p class="mb-1 text-muted">${patient.phone}</p>
                                    <small class="text-muted">${patient.email || 'No email'}</small>
                                </div>
                                <div class="text-right">
                                    <small class="text-muted">${patient.age ? patient.age + ' years' : ''}</small><br>
                                    <small class="text-muted">${patient.gender || ''}</small>
                                </div>
                            </div>
                        </div>
                    `;
                    });
                } else {
                    resultsHtml = '<p class="text-muted text-center">No patients found.</p>';
                }
                $('#patientSearchResults').html(resultsHtml);
            },
            error: function() {
                $('#patientSearchResults').html('<p class="text-danger text-center">Error loading patients.</p>');
            }
        });
    }

    function selectPatient(id, name, phone, email, age, gender) {
        // Add option to select if not exists
        if ($(`#patient_id option[value="${id}"]`).length === 0) {
            $('#patient_id').append(`<option value="${id}" data-phone="${phone}" data-email="${email}" data-age="${age}" data-gender="${gender}">${name} - ${phone}</option>`);
        }

        $('#patient_id').val(id);
        $('#patientSearchModal').modal('hide');
        loadPatientInfo();
    }

    // Patient search functionality
    $('#patientSearch').on('input', function() {
        const query = $(this).val();
        if (query.length >= 2) {
            searchPatients(query);
        } else {
            loadAllPatients();
        }
    });

    function searchPatients(query) {
        $('#patientSearchResults').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Searching...</div>');

        $.ajax({
            url: '{{ route("admin.patients.search") }}',
            method: 'GET',
            data: {
                q: query
            },
            success: function(response) {
                let resultsHtml = '';
                if (response.length > 0) {
                    response.forEach(function(patient) {
                        resultsHtml += `
                        <div class="patient-result border rounded p-3 mb-2" style="cursor: pointer;" 
                             onclick="selectPatient(${patient.id}, '${patient.name}', '${patient.phone}', '${patient.email}', ${patient.age}, '${patient.gender}')">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">${patient.name}</h6>
                                    <p class="mb-1 text-muted">${patient.phone}</p>
                                    <small class="text-muted">${patient.email || 'No email'}</small>
                                </div>
                                <div class="text-right">
                                    <small class="text-muted">${patient.age ? patient.age + ' years' : ''}</small><br>
                                    <small class="text-muted">${patient.gender || ''}</small>
                                </div>
                            </div>
                        </div>
                    `;
                    });
                } else {
                    resultsHtml = '<p class="text-muted text-center">No patients found matching your search.</p>';
                }
                $('#patientSearchResults').html(resultsHtml);
            },
            error: function() {
                $('#patientSearchResults').html('<p class="text-danger text-center">Error searching patients.</p>');
            }
        });
    }

    function showCancelModal() {
        $('#cancelModal').modal('show');
    }

    function printAppointment() {
        const printWindow = window.open('', '_blank');
        const appointmentData = {
            patient: $('#patient_id option:selected').text().split(' - ')[0],
            doctor: $('#doctor_id option:selected').text().split(' - ')[0],
            date: $('#appointment_date').val(),
            time: $('#appointment_time option:selected').text(),
            duration: $('#duration').val(),
            type: $('#appointment_type option:selected').text(),
            fee: $('#consultation_fee').val(),
            status: $('#status option:selected').text(),
            complaint: $('#chief_complaint').val(),
            notes: $('#notes').val()
        };

        const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Appointment Details</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .details { margin-bottom: 20px; }
                .row { display: flex; margin-bottom: 10px; }
                .label { font-weight: bold; width: 150px; }
                .value { flex: 1; }
                .section { margin-bottom: 25px; }
                .section-title { font-weight: bold; font-size: 16px; margin-bottom: 10px; border-bottom: 1px solid #ccc; }
                @media print { body { margin: 0; } }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Appointment Details</h2>
                <p>{{ Auth::user()->business->name ?? 'Medical Practice' }}</p>
            </div>
            
            <div class="section">
                <div class="section-title">Patient Information</div>
                <div class="row">
                    <div class="label">Patient:</div>
                    <div class="value">${appointmentData.patient}</div>
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">Doctor Information</div>
                <div class="row">
                    <div class="label">Doctor:</div>
                    <div class="value">${appointmentData.doctor}</div>
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">Appointment Details</div>
                <div class="row">
                    <div class="label">Date:</div>
                    <div class="value">${new Date(appointmentData.date).toLocaleDateString()}</div>
                </div>
                <div class="row">
                    <div class="label">Time:</div>
                    <div class="value">${appointmentData.time}</div>
                </div>
                <div class="row">
                    <div class="label">Duration:</div>
                    <div class="value">${appointmentData.duration} minutes</div>
                </div>
                <div class="row">
                    <div class="label">Type:</div>
                    <div class="value">${appointmentData.type}</div>
                </div>
                <div class="row">
                    <div class="label">Status:</div>
                    <div class="value">${appointmentData.status}</div>
                </div>
                <div class="row">
                    <div class="label">Fee:</div>
                    <div class="value">$${appointmentData.fee}</div>
                </div>
            </div>
            
            ${appointmentData.complaint ? `
            <div class="section">
                <div class="section-title">Chief Complaint</div>
                <div>${appointmentData.complaint}</div>
            </div>
            ` : ''}
            
            ${appointmentData.notes ? `
            <div class="section">
                <div class="section-title">Notes</div>
                <div>${appointmentData.notes}</div>
            </div>
            ` : ''}
            
            <div class="section">
                <div class="section-title">Print Information</div>
                <div class="row">
                    <div class="label">Printed on:</div>
                    <div class="value">${new Date().toLocaleString()}</div>
                </div>
                <div class="row">
                    <div class="label">Printed by:</div>
                    <div class="value">{{ Auth::user()->name }}</div>
                </div>
            </div>
        </body>
        </html>
    `;

        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    }

    // Form validation before submit
    $('#appointmentForm').submit(function(e) {
        const patientId = $('#patient_id').val();
        const doctorId = $('#doctor_id').val();
        const appointmentDate = $('#appointment_date').val();
        const appointmentTime = $('#appointment_time').val();

        if (!patientId || !doctorId || !appointmentDate || !appointmentTime) {
            e.preventDefault();
            toastr.error('Please fill in all required fields.');
            return false;
        }

        // Check if appointment is in the past (only if changing date/time)
        const appointmentDateTime = new Date(`${appointmentDate} ${appointmentTime}`);
        const now = new Date();

        if (appointmentDateTime < now) {
            e.preventDefault();
            toastr.error('Cannot schedule appointment in the past.');
            return false;
        }

        return true;
    });
</script>
@endpush

@push('styles')
<style>
    .patient-result:hover {
        background-color: #f8f9fa;
        border-color: #007bff !important;
    }

    .time-slot-btn {
        min-width: 80px;
    }

    .time-slots-container {
        max-height: 200px;
        overflow-y: auto;
    }

    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-marker {
        position: absolute;
        left: -35px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #dee2e6;
    }

    .timeline-item:before {
        content: '';
        position: absolute;
        left: -30px;
        top: 17px;
        bottom: -20px;
        width: 2px;
        background-color: #dee2e6;
    }

    .timeline-item:last-child:before {
        display: none;
    }

    .timeline-title {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .timeline-text {
        font-size: 13px;
        margin-bottom: 5px;
        color: #6c757d;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .btn:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .d-grid {
        display: grid;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    @media (max-width: 768px) {
        .col-lg-4 {
            margin-top: 20px;
        }

        .btn-group {
            flex-direction: column;
        }

        .btn-group .btn {
            margin-bottom: 5px;
            border-radius: 0.25rem !important;
        }

        .time-slots-container {
            text-align: center;
        }

        .time-slot-btn {
            margin: 2px;
            min-width: 70px;
        }

        .patient-result {
            padding: 15px !important;
        }

        .modal-dialog {
            margin: 10px;
        }
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 10px;
            padding-right: 10px;
        }

        .card {
            margin-bottom: 15px;
        }

        .btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .row {
            margin-left: -5px;
            margin-right: -5px;
        }

        .row>[class*="col-"] {
            padding-left: 5px;
            padding-right: 5px;
        }

        .d-sm-flex {
            flex-direction: column;
            align-items: stretch !important;
        }

        .d-sm-flex .btn-group {
            margin-top: 10px;
            align-self: stretch;
        }

        .timeline {
            padding-left: 20px;
        }

        .timeline-marker {
            left: -25px;
        }

        .timeline-item:before {
            left: -20px;
        }
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loading-spinner {
        color: white;
        font-size: 2rem;
    }

    .badge-status {
        font-size: 0.75em;
        padding: 0.25em 0.5em;
    }

    .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
    }

    .info-item i {
        width: 20px;
        margin-right: 8px;
    }

    .quick-action-btn {
        margin-bottom: 8px;
        width: 100%;
    }

    .appointment-summary {
        background-color: #f8f9fa;
        border-left: 4px solid #007bff;
        padding: 15px;
        margin-bottom: 20px;
    }

    .appointment-summary h6 {
        color: #007bff;
        margin-bottom: 10px;
    }

    .status-badge {
        display: inline-block;
        padding: 0.25em 0.6em;
        font-size: 75%;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
    }

    .status-scheduled {
        color: #856404;
        background-color: #fff3cd;
    }

    .status-confirmed {
        color: #0c5460;
        background-color: #d1ecf1;
    }

    .status-completed {
        color: #155724;
        background-color: #d4edda;
    }

    .status-cancelled {
        color: #721c24;
        background-color: #f8d7da;
    }

    .status-no_show {
        color: #383d41;
        background-color: #e2e3e5;
    }

    .payment-pending {
        color: #856404;
        background-color: #fff3cd;
    }

    .payment-paid {
        color: #155724;
        background-color: #d4edda;
    }

    .payment-partial {
        color: #0c5460;
        background-color: #d1ecf1;
    }

    .form-section {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #dee2e6;
    }

    .form-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .form-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e9ecef;
    }

    .required-field::after {
        content: " *";
        color: #dc3545;
    }

    .field-help {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 4px;
    }

    .time-slot-selected {
        background-color: #007bff !important;
        border-color: #007bff !important;
        color: white !important;
    }

    .patient-search-item {
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }

    .patient-search-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .doctor-info-item {
        display: flex;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f1f3f4;
    }

    .doctor-info-item:last-child {
        border-bottom: none;
    }

    .doctor-info-icon {
        width: 24px;
        text-align: center;
        margin-right: 12px;
        color: #007bff;
    }

    .appointment-actions {
        position: sticky;
        top: 20px;
    }

    .form-floating {
        position: relative;
    }

    .form-floating>.form-control {
        height: calc(3.5rem + 2px);
        padding: 1rem 0.75rem;
    }

    .form-floating>label {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        padding: 1rem 0.75rem;
        pointer-events: none;
        border: 1px solid transparent;
        transform-origin: 0 0;
        transition: opacity 0.1s ease-in-out, transform 0.1s ease-in-out;
    }

    .form-floating>.form-control:focus~label,
    .form-floating>.form-control:not(:placeholder-shown)~label {
        opacity: 0.65;
        transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
    }

    .alert-dismissible .close {
        position: absolute;
        top: 0;
        right: 0;
        padding: 0.75rem 1.25rem;
        color: inherit;
    }

    .fade {
        transition: opacity 0.15s linear;
    }

    .fade:not(.show) {
        opacity: 0;
    }

    .fade.show {
        opacity: 1;
    }

    .custom-control-input:checked~.custom-control-label::before {
        color: #fff;
        border-color: #007bff;
        background-color: #007bff;
    }

    .custom-control-input:focus~.custom-control-label::before {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1040;
        width: 100vw;
        height: 100vh;
        background-color: #000;
    }

    .modal-backdrop.fade {
        opacity: 0;
    }

    .modal-backdrop.show {
        opacity: 0.5;
    }

    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .border-left-primary {
        border-left: 0.25rem solid #007bff !important;
    }

    .border-left-success {
        border-left: 0.25rem solid #28a745 !important;
    }

    .border-left-warning {
        border-left: 0.25rem solid #ffc107 !important;
    }

    .border-left-danger {
        border-left: 0.25rem solid #dc3545 !important;
    }

    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }

    .shadow {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }

    .shadow-lg {
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
    }
</style>
@endpush