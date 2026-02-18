@extends('admin.layouts.app')

@section('page-title', 'Appointment Calendar')
@section('page-description', 'View and manage appointments in calendar format')

@section('content')
<!-- Add data attributes to pass PHP data to JavaScript -->
<div class="space-y-6"
    data-doctors-search-url="{{ route('admin.search.doctors') }}"
    data-patients-search-url="{{ route('admin.search.patients') }}"
    data-appointments-url="{{ route('admin.calendar.appointments') }}"
    data-csrf-token="{{ csrf_token() }}"
    data-current-user-role="{{ auth()->user()->role }}"
    data-current-doctor-id="{{ auth()->user()->role === 'doctor' ? auth()->user()->doctor_id : '' }}">

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-calendar-alt mr-3 text-blue-600"></i>
                Appointment Calendar
            </h1>
            <p class="text-gray-600 mt-1">View and manage appointments in calendar format</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button type="button"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-200"
                id="todayBtn">
                <i class="fas fa-calendar-day mr-2"></i>
                Today
            </button>
            <a href="{{ route('admin.appointments.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl shadow-lg hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all duration-200">
                <i class="fas fa-plus mr-2"></i>
                New Appointment
            </a>
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:from-purple-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-200">
                    <i class="fas fa-download mr-2"></i>
                    Export
                    <i class="fas fa-chevron-down ml-2"></i>
                </button>
                <div x-show="open" @click.away="open = false" x-transition
                    class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
                    <div class="py-2">
                        <a href="#" onclick="exportCalendar('pdf')"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            <i class="fas fa-file-pdf mr-3 text-red-500"></i>
                            Export to PDF
                        </a>
                        <a href="#" onclick="exportCalendar('csv')"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            <i class="fas fa-file-csv mr-3 text-green-500"></i>
                            Export to CSV
                        </a>
                        <a href="#" onclick="printCalendar()"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            <i class="fas fa-print mr-3 text-blue-500"></i>
                            Print Calendar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Filters Bar -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                <!-- Doctor Filter - Only show for admin/manager roles -->
                @if(auth()->user()->role !== 'doctor')
                <div class="flex-1">
                    <label for="doctorSelect" class="block text-sm font-medium text-gray-700 mb-2">Filter by Doctor</label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                        id="doctorSelect">
                        <option value="all" selected>All Doctors</option>
                        @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" data-color="{{ $doctor->calendar_color ?? '#007bff' }}">
                            Dr. {{ $doctor->name }} - {{ $doctor->specialization }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Quick Status Filters -->
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quick Filters</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button"
                            class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200 filter-btn active"
                            data-filter="all">
                            All
                        </button>
                        <button type="button"
                            class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors duration-200 filter-btn"
                            data-filter="confirmed">
                            Confirmed
                        </button>
                        <button type="button"
                            class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors duration-200 filter-btn"
                            data-filter="scheduled">
                            Scheduled
                        </button>
                        <button type="button"
                            class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors duration-200 filter-btn"
                            data-filter="today">
                            Today
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Calendar -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Calendar Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <button type="button"
                        class="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors duration-200"
                        id="prevBtn">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <h2 class="text-2xl font-bold" id="currentMonth">{{ now()->format('F Y') }}</h2>
                    <button type="button"
                        class="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors duration-200"
                        id="nextBtn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex bg-white/20 rounded-lg p-1">
                        <button type="button"
                            class="px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 view-btn active"
                            data-view="month">
                            Month
                        </button>
                        <button type="button"
                            class="px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 view-btn"
                            data-view="week">
                            Week
                        </button>
                        <button type="button"
                            class="px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 view-btn"
                            data-view="day">
                            Day
                        </button>
                    </div>
                    <button type="button"
                        class="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors duration-200"
                        id="refreshBtn">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Calendar Body -->
        <div class="p-6">
            <div id="calendar-container" class="min-h-[600px]">
                <div class="calendar-grid" id="calendarGrid">
                    <!-- Calendar will be rendered here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-info-circle mr-2 text-blue-600"></i>
            Legend & Tips
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-semibold text-gray-700 mb-3">Status Colors</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 bg-blue-500 rounded"></span>
                        <span class="text-sm text-gray-600">Scheduled</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 bg-green-500 rounded"></span>
                        <span class="text-sm text-gray-600">Confirmed</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 bg-gray-500 rounded"></span>
                        <span class="text-sm text-gray-600">Completed</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 bg-yellow-500 rounded"></span>
                        <span class="text-sm text-gray-600">No Show</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 bg-red-500 rounded"></span>
                        <span class="text-sm text-gray-600">Cancelled</span>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="font-semibold text-gray-700 mb-3">Quick Tips</h4>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <i class="fas fa-mouse-pointer text-blue-500"></i>
                        Single click to select date
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-mouse-pointer text-green-500"></i>
                        Double click to create appointment
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-hand-pointer text-purple-500"></i>
                        Click & drag to select date range
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-eye text-gray-500"></i>
                        Hover to see appointment details
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Appointment Details Modal -->
<div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden" id="appointmentModal">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold flex items-center">
                        <i class="fas fa-calendar-check mr-2"></i>
                        Appointment Details
                    </h3>
                    <button type="button"
                        class="p-2 hover:bg-white/20 rounded-lg transition-colors duration-200"
                        onclick="closeModal('appointmentModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6" id="appointmentModalBody">
                <!-- Appointment details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Quick Create Appointment Modal -->
<div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden" id="quickCreateModal">
    <div class="flex items-center justify-center min-h-screen p-2 sm:p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[95vh] overflow-hidden">
            <form id="quickCreateForm">
                @csrf
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-4 sm:p-6 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg sm:text-xl font-bold flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            <span class="hidden sm:inline">Quick Create Appointment</span>
                            <span class="sm:hidden">New Appointment</span>
                        </h3>
                        <button type="button"
                            class="p-2 hover:bg-white/20 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-white/50"
                            onclick="closeModal('quickCreateModal')"
                            aria-label="Close modal">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Progress Indicator -->
                <div class="bg-gray-50 px-4 sm:px-6 py-3 border-b border-gray-200">
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center step-indicator" data-step="1">
                                <div class="w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center text-xs font-medium">1</div>
                                <span class="ml-2 font-medium text-gray-900">Basic Info</span>
                            </div>
                            <div class="w-8 h-px bg-gray-300"></div>
                            <div class="flex items-center step-indicator" data-step="2">
                                <div class="w-6 h-6 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-xs font-medium">2</div>
                                <span class="ml-2 text-gray-500">Details</span>
                            </div>
                            <div class="w-8 h-px bg-gray-300"></div>
                            <div class="flex items-center step-indicator" data-step="3">
                                <div class="w-6 h-6 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-xs font-medium">3</div>
                                <span class="ml-2 text-gray-500">Review</span>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500">
                            Step <span id="current-step-number">1</span> of 3
                        </div>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-4 sm:p-6 max-h-[60vh] overflow-y-auto">
                    <!-- Step 1: Basic Information -->
                    <div id="step-1" class="step-content">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <!-- Date -->
                            <div>
                                <label for="quickDate" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar mr-1 text-green-500"></i>
                                    Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date"
                                    class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-sm sm:text-base"
                                    id="quickDate"
                                    name="appointment_date"
                                    required>
                            </div>

                            <!-- Time -->
                            <div>
                                <label for="quickTime" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-clock mr-1 text-green-500"></i>
                                    Time <span class="text-red-500">*</span>
                                </label>
                                <input type="time"
                                    class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-sm sm:text-base"
                                    id="quickTime"
                                    name="appointment_time"
                                    required>
                            </div>

                            <!-- Doctor Search - Only show for admin/manager -->
                            @if(auth()->user()->role !== 'doctor')
                            <div>
                                <label for="quickDoctor" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user-md mr-1 text-green-500"></i>
                                    Doctor <span class="text-red-500">*</span>
                                </label>
                                <div class="relative doctor-search-container">
                                    <div class="relative">
                                        <input
                                            type="text"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-sm sm:text-base pr-10 doctor-search-input"
                                            placeholder="Search doctors..."
                                            autocomplete="off">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-search text-gray-400"></i>
                                        </div>
                                    </div>

                                    <!-- Hidden input for form submission -->
                                    <input type="hidden" name="doctor_id" id="quickDoctor" required>

                                    <!-- Dropdown -->
                                    <div class="doctor-dropdown absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-xl shadow-lg max-h-60 overflow-y-auto search-dropdown hidden">
                                        <div class="doctor-results py-1">
                                            <!-- Results will be populated via AJAX -->
                                        </div>
                                    </div>

                                    <!-- Loading indicator -->
                                    <div class="doctor-loading hidden absolute right-10 top-3">
                                        <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                    </div>
                                </div>
                                <div id="doctor-info" class="hidden mt-3"></div>
                            </div>
                            @else
                            <!-- Hidden doctor field for doctors -->
                            <input type="hidden" name="doctor_id" value="{{ auth()->user()->doctor_id }}" id="quickDoctor">
                            @endif

                            <!-- Patient Search -->
                            <div>
                                <label for="quickPatient" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user mr-1 text-green-500"></i>
                                    Patient <span class="text-red-500">*</span>
                                </label>
                                <div class="relative patient-search-container">
                                    <div class="relative">
                                        <input
                                            type="text"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-sm sm:text-base pr-10 patient-search-input"
                                            placeholder="Search patients..."
                                            autocomplete="off">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-search text-gray-400"></i>
                                        </div>
                                    </div>

                                    <!-- Hidden input for form submission -->
                                    <input type="hidden" name="patient_id" id="quickPatient" required>

                                    <!-- Dropdown -->
                                    <div class="patient-dropdown absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-xl shadow-lg max-h-60 overflow-y-auto search-dropdown hidden">
                                        <div class="patient-results py-1">
                                            <!-- Results will be populated via AJAX -->
                                        </div>
                                    </div>

                                    <!-- Loading indicator -->
                                    <div class="patient-loading hidden absolute right-10 top-3">
                                        <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                    </div>
                                </div>
                                <div id="patient-info" class="hidden mt-3"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Additional Details -->
                    <div id="step-2" class="step-content hidden">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <!-- Duration -->
                            <div>
                                <label for="quickDuration" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-hourglass-half mr-1 text-green-500"></i>
                                    Duration (minutes) <span class="text-red-500">*</span>
                                </label>
                                <select class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-sm sm:text-base"
                                    id="quickDuration"
                                    name="duration"
                                    required>
                                    <option value="">Select Duration</option>
                                    <option value="15">15 minutes</option>
                                    <option value="30" selected>30 minutes</option>
                                    <option value="45">45 minutes</option>
                                    <option value="60">1 hour</option>
                                    <option value="90">1.5 hours</option>
                                    <option value="120">2 hours</option>
                                </select>
                            </div>

                            <!-- Appointment Type -->
                            <div>
                                <label for="quickType" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-stethoscope mr-1 text-green-500"></i>
                                    Appointment Type <span class="text-red-500">*</span>
                                </label>
                                <select class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-sm sm:text-base"
                                    id="quickType"
                                    name="appointment_type"
                                    required>
                                    <option value="">Select Type</option>
                                    <option value="consultation">Consultation</option>
                                    <option value="follow_up">Follow Up</option>
                                    <option value="emergency">Emergency</option>
                                    <option value="checkup">Routine Checkup</option>
                                </select>
                            </div>

                            <!-- Priority -->
                            <div>
                                <label for="quickPriority" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-exclamation-triangle mr-1 text-green-500"></i>
                                    Priority <span class="text-red-500">*</span>
                                </label>
                                <select class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-sm sm:text-base"
                                    id="quickPriority"
                                    name="priority"
                                    required>
                                    <option value="">Select Priority</option>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>

                        <!-- Chief Complaint -->
                        <div class="mt-4 sm:mt-6">
                            <label for="quickComplaint" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-clipboard-list mr-1 text-green-500"></i>
                                Chief Complaint
                            </label>
                            <textarea class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-sm sm:text-base resize-none"
                                id="quickComplaint"
                                name="chief_complaint"
                                rows="3"
                                maxlength="500"
                                placeholder="Describe the main reason for this appointment..."></textarea>
                            <div class="flex justify-between items-center mt-1">
                                <div class="text-xs text-gray-500">Brief description of the patient's main concern</div>
                                <div class="text-xs text-gray-400" id="complaint-counter">0/500</div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mt-4 sm:mt-6">
                            <label for="quickNotes" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-sticky-note mr-1 text-green-500"></i>
                                Additional Notes
                            </label>
                            <textarea class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-sm sm:text-base resize-none"
                                id="quickNotes"
                                name="notes"
                                rows="3"
                                maxlength="1000"
                                placeholder="Any additional notes or special instructions..."></textarea>
                            <div class="flex justify-between items-center mt-1">
                                <div class="text-xs text-gray-500">Optional additional information</div>
                                <div class="text-xs text-gray-400" id="notes-counter">0/1000</div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Review & Confirm -->
                    <div id="step-3" class="step-content hidden">
                        <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-xl p-4 sm:p-6 border border-green-200">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-check-circle mr-2 text-green-600"></i>
                                Review Appointment Details
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                                <!-- Basic Information -->
                                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                    <h5 class="font-semibold text-gray-700 mb-3 flex items-center">
                                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                                        Basic Information
                                    </h5>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Date:</span>
                                            <span class="font-medium" id="summary-date">-</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Time:</span>
                                            <span class="font-medium" id="summary-time">-</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Duration:</span>
                                            <span class="font-medium" id="summary-duration">-</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Type:</span>
                                            <span class="font-medium" id="summary-type">-</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Priority:</span>
                                            <span class="font-medium" id="summary-priority">-</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- People Involved -->
                                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                    <h5 class="font-semibold text-gray-700 mb-3 flex items-center">
                                        <i class="fas fa-users mr-2 text-green-500"></i>
                                        People Involved
                                    </h5>
                                    <div class="space-y-2 text-sm">
                                        @if(auth()->user()->role !== 'doctor')
                                        <div>
                                            <span class="text-gray-600">Doctor:</span>
                                            <div class="font-medium mt-1" id="summary-doctor">-</div>
                                        </div>
                                        @endif
                                        <div>
                                            <span class="text-gray-600">Patient:</span>
                                            <div class="font-medium mt-1" id="summary-patient">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Details -->
                            <div class="mt-4 bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                <h5 class="font-semibold text-gray-700 mb-3 flex items-center">
                                    <i class="fas fa-clipboard-list mr-2 text-purple-500"></i>
                                    Additional Details
                                </h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Chief Complaint:</span>
                                        <div class="font-medium mt-1 text-gray-800" id="summary-complaint">-</div>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Notes:</span>
                                        <div class="font-medium mt-1 text-gray-800" id="summary-notes">-</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Confirmation Notice -->
                            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-3"></i>
                                    <div class="text-sm">
                                        <p class="font-medium text-yellow-800">Please review all details carefully</p>
                                        <p class="text-yellow-700 mt-1">Once created, you can still edit the appointment from the appointments management page.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-4 sm:px-6 py-4 rounded-b-2xl flex flex-col sm:flex-row justify-between gap-3">
                    <div class="flex gap-3">
                        <button type="button"
                            class="px-4 sm:px-6 py-2 sm:py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200 text-sm sm:text-base"
                            onclick="closeModal('quickCreateModal')">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </button>
                        <button type="button"
                            class="px-4 sm:px-6 py-2 sm:py-3 bg-blue-100 text-blue-700 font-semibold rounded-xl hover:bg-blue-200 transition-all duration-200 text-sm sm:text-base hidden"
                            id="prevStepBtn"
                            onclick="prevStep()">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Previous
                        </button>
                    </div>
                    <div class="flex gap-3">
                        <button type="button"
                            class="px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-200 text-sm sm:text-base"
                            id="nextStepBtn"
                            onclick="nextStep()">
                            Next
                            <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                        <button type="submit"
                            class="px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl shadow-lg hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all duration-200 text-sm sm:text-base hidden"
                            id="submitBtn">
                            <i class="fas fa-save mr-2"></i>
                            Create Appointment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden items-center justify-center" id="loadingOverlay">
    <div class="bg-white rounded-2xl p-8 shadow-2xl">
        <div class="flex items-center space-x-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="text-lg font-semibold text-gray-700">Loading calendar...</span>
        </div>
    </div>
</div>

<!-- Toast Notification Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

@endsection

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get data from HTML attributes
        const container = document.querySelector('[data-doctors-search-url]');
        const doctorsSearchUrl = container.dataset.doctorsSearchUrl;
        const patientsSearchUrl = container.dataset.patientsSearchUrl;
        const appointmentsUrl = container.dataset.appointmentsUrl;
        const csrfToken = container.dataset.csrfToken;
        const currentUserRole = container.dataset.currentUserRole;
        const currentDoctorId = container.dataset.currentDoctorId;

        // Calendar state
        let currentDate = new Date();
        let currentView = 'month';
        let selectedDoctor = currentUserRole === 'doctor' ? currentDoctorId : 'all';
        let selectedFilter = 'all';
        let appointments = [];
        let currentStep = 1;

        // Search debounce timers
        let doctorSearchTimeout;
        let patientSearchTimeout;

        // DOM elements
        const calendarGrid = document.getElementById('calendarGrid');
        const currentMonthEl = document.getElementById('currentMonth');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const todayBtn = document.getElementById('todayBtn');
        const refreshBtn = document.getElementById('refreshBtn');
        const doctorSelect = document.getElementById('doctorSelect');

        // Search elements
        const doctorSearchInput = document.querySelector('.doctor-search-input');
        const doctorDropdown = document.querySelector('.doctor-dropdown');
        const doctorResults = document.querySelector('.doctor-results');
        const doctorLoading = document.querySelector('.doctor-loading');
        const doctorHiddenInput = document.getElementById('quickDoctor');

        const patientSearchInput = document.querySelector('.patient-search-input');
        const patientDropdown = document.querySelector('.patient-dropdown');
        const patientResults = document.querySelector('.patient-results');
        const patientLoading = document.querySelector('.patient-loading');
        const patientHiddenInput = document.getElementById('quickPatient');

        // Initialize
        init();

        function init() {
            loadAppointments();
            renderCalendar();
            setupEventListeners();
            setupSearchFunctionality();
            setupFormValidation();

            // Set doctor filter for doctors
            if (currentUserRole === 'doctor' && doctorSelect) {
                doctorSelect.value = currentDoctorId;
                doctorSelect.disabled = true;
            }
        }

        function setupSearchFunctionality() {
            // Doctor search - only if not a doctor
            if (doctorSearchInput && currentUserRole !== 'doctor') {
                doctorSearchInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    clearTimeout(doctorSearchTimeout);

                    if (query.length >= 2) {
                        doctorSearchTimeout = setTimeout(() => {
                            searchDoctors(query);
                        }, 300);
                    } else {
                        hideDoctorDropdown();
                    }
                });

                doctorSearchInput.addEventListener('focus', function() {
                    if (this.value.trim().length >= 2) {
                        searchDoctors(this.value.trim());
                    }
                });

                doctorSearchInput.addEventListener('blur', function() {
                    setTimeout(() => {
                        hideDoctorDropdown();
                    }, 200);
                });
            }

            // Patient search
            if (patientSearchInput) {
                patientSearchInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    clearTimeout(patientSearchTimeout);

                    if (query.length >= 2) {
                        patientSearchTimeout = setTimeout(() => {
                            searchPatients(query);
                        }, 300);
                    } else {
                        hidePatientDropdown();
                    }
                });

                patientSearchInput.addEventListener('focus', function() {
                    if (this.value.trim().length >= 2) {
                        searchPatients(this.value.trim());
                    }
                });

                patientSearchInput.addEventListener('blur', function() {
                    setTimeout(() => {
                        hidePatientDropdown();
                    }, 200);
                });
            }
        }

        function searchDoctors(query) {
            showDoctorLoading();

            fetch(`${doctorsSearchUrl}?q=${encodeURIComponent(query)}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideDoctorLoading();
                    displayDoctorResults(data.doctors || []);
                })
                .catch(error => {
                    hideDoctorLoading();
                    console.error('Error searching doctors:', error);
                    displayDoctorResults([]);
                });
        }

        function searchPatients(query) {
            showPatientLoading();

            fetch(`${patientsSearchUrl}?q=${encodeURIComponent(query)}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hidePatientLoading();
                    displayPatientResults(data.patients || []);
                })
                .catch(error => {
                    hidePatientLoading();
                    console.error('Error searching patients:', error);
                    displayPatientResults([]);
                });
        }

        function displayDoctorResults(doctors) {
            if (!doctorResults) return;

            if (doctors.length === 0) {
                doctorResults.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 text-center">No doctors found</div>';
            } else {
                doctorResults.innerHTML = doctors.map(doctor => `
                    <div class="doctor-option px-4 py-2 hover:bg-gray-50 cursor-pointer flex items-center space-x-3 transition-colors duration-200" 
                         data-id="${doctor.id}" 
                         data-name="${doctor.name}" 
                         data-specialization="${doctor.specialization}">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-md text-green-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 text-sm">Dr. ${doctor.name}</div>
                            <div class="text-xs text-gray-500">${doctor.specialization}</div>
                        </div>
                    </div>
                `).join('');

                doctorResults.querySelectorAll('.doctor-option').forEach(option => {
                    option.addEventListener('click', function() {
                        selectDoctor({
                            id: this.dataset.id,
                            name: this.dataset.name,
                            specialization: this.dataset.specialization
                        });
                    });
                });
            }

            showDoctorDropdown();
        }

        function displayPatientResults(patients) {
            if (!patientResults) return;

            if (patients.length === 0) {
                patientResults.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 text-center">No patients found</div>';
            } else {
                patientResults.innerHTML = patients.map(patient => `
                <div class="patient-option px-4 py-2 hover:bg-gray-50 cursor-pointer flex items-center space-x-3 transition-colors duration-200 ${patient.is_recent ? 'bg-blue-50' : ''}" 
                     data-id="${patient.id}" 
                     data-patient-id="${patient.patient_id}"
                     data-name="${patient.name}" 
                     data-email="${patient.email || ''}"
                     data-phone="${patient.phone || ''}">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user text-blue-600 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2">
                            <div class="font-medium text-gray-900 text-sm truncate">${patient.name}</div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                ${patient.patient_id}
                            </span>
                            ${patient.is_recent ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Recent</span>' : ''}
                        </div>
                        <div class="text-xs text-gray-500 truncate">${patient.subtitle}</div>
                        ${patient.date_of_birth ? `<div class="text-xs text-gray-400">DOB: ${new Date(patient.date_of_birth).toLocaleDateString()}</div>` : ''}
                    </div>
                </div>
            `).join('');

                patientResults.querySelectorAll('.patient-option').forEach(option => {
                    option.addEventListener('click', function() {
                        selectPatient({
                            id: this.dataset.id,
                            patient_id: this.dataset.patientId,
                            name: this.dataset.name,
                            email: this.dataset.email,
                            phone: this.dataset.phone
                        });
                    });
                });
            }

            showPatientDropdown();
        }

        function selectDoctor(doctor) {
            doctorSearchInput.value = `Dr. ${doctor.name} - ${doctor.specialization}`;
            doctorHiddenInput.value = doctor.id;
            hideDoctorDropdown();
            updateSummary();

            doctorSearchInput.classList.remove('border-red-500');
        }

        function selectPatient(patient) {
            patientSearchInput.value = `${patient.name} (${patient.patient_id})`;
            patientHiddenInput.value = patient.id;
            hidePatientDropdown();
            updateSummary();

            showPatientInfo(patient);
            patientSearchInput.classList.remove('border-red-500');
        }

        function showPatientInfo(patient) {
            const patientInfo = document.getElementById('patient-info');
            if (patientInfo) {
                patientInfo.innerHTML = `
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="flex items-center space-x-2 mb-2">
                        <i class="fas fa-user text-blue-600"></i>
                        <span class="font-medium text-blue-900">${patient.name}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            ${patient.patient_id}
                        </span>
                    </div>
                    <div class="text-sm text-blue-700 space-y-1">
                        ${patient.email ? `<div><i class="fas fa-envelope w-4"></i> ${patient.email}</div>` : ''}
                        ${patient.phone ? `<div><i class="fas fa-phone w-4"></i> ${patient.phone}</div>` : ''}
                    </div>
                </div>
            `;
                patientInfo.classList.remove('hidden');
            }
        }

        function hidePatientInfo() {
            const patientInfo = document.getElementById('patient-info');
            if (patientInfo) {
                patientInfo.classList.add('hidden');
                patientInfo.innerHTML = '';
            }
        }

        function clearPatientSelection() {
            if (patientSearchInput) patientSearchInput.value = '';
            if (patientHiddenInput) patientHiddenInput.value = '';
            hidePatientInfo();
            hidePatientDropdown();
        }

        // Dropdown visibility functions
        function showDoctorDropdown() {
            if (doctorDropdown) doctorDropdown.classList.remove('hidden');
        }

        function hideDoctorDropdown() {
            if (doctorDropdown) doctorDropdown.classList.add('hidden');
        }

        function showPatientDropdown() {
            if (patientDropdown) patientDropdown.classList.remove('hidden');
        }

        function hidePatientDropdown() {
            if (patientDropdown) patientDropdown.classList.add('hidden');
        }

        // Loading functions
        function showDoctorLoading() {
            if (doctorLoading) doctorLoading.classList.remove('hidden');
        }

        function hideDoctorLoading() {
            if (doctorLoading) doctorLoading.classList.add('hidden');
        }

        function showPatientLoading() {
            if (patientLoading) patientLoading.classList.remove('hidden');
        }

        function hidePatientLoading() {
            if (patientLoading) patientLoading.classList.add('hidden');
        }

        // Step navigation functions
        function nextStep() {
            if (currentStep < 3) {
                if (validateCurrentStep()) {
                    currentStep++;
                    updateStepDisplay();
                    updateSummary();
                }
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                updateStepDisplay();
            }
        }

        function updateStepDisplay() {
            document.querySelectorAll('.step-content').forEach(step => {
                step.classList.add('hidden');
            });

            document.getElementById(`step-${currentStep}`).classList.remove('hidden');

            document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
                const stepNumber = index + 1;
                const circle = indicator.querySelector('div');
                const text = indicator.querySelector('span');

                if (stepNumber <= currentStep) {
                    circle.classList.remove('bg-gray-300', 'text-gray-600');
                    circle.classList.add('bg-green-500', 'text-white');
                    text.classList.remove('text-gray-500');
                    text.classList.add('text-gray-900', 'font-medium');
                } else {
                    circle.classList.remove('bg-green-500', 'text-white');
                    circle.classList.add('bg-gray-300', 'text-gray-600');
                    text.classList.remove('text-gray-900', 'font-medium');
                    text.classList.add('text-gray-500');
                }
            });

            document.getElementById('current-step-number').textContent = currentStep;

            const prevBtn = document.getElementById('prevStepBtn');
            const nextBtn = document.getElementById('nextStepBtn');
            const submitBtn = document.getElementById('submitBtn');

            if (currentStep === 1) {
                prevBtn.classList.add('hidden');
                nextBtn.classList.remove('hidden');
                submitBtn.classList.add('hidden');
            } else if (currentStep === 3) {
                prevBtn.classList.remove('hidden');
                nextBtn.classList.add('hidden');
                submitBtn.classList.remove('hidden');
            } else {
                prevBtn.classList.remove('hidden');
                nextBtn.classList.remove('hidden');
                submitBtn.classList.add('hidden');
            }
        }

        function validateCurrentStep() {
            let isValid = true;
            const errors = [];

            if (currentStep === 1) {
                const appointmentDate = document.getElementById('quickDate')?.value;
                if (!appointmentDate) {
                    errors.push('Appointment date is required');
                    const dateField = document.getElementById('quickDate');
                    if (dateField) dateField.classList.add('border-red-500');
                    isValid = false;
                } else {
                    const selectedDate = new Date(appointmentDate);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    if (selectedDate < today) {
                        errors.push('Appointment date cannot be in the past');
                        const dateField = document.getElementById('quickDate');
                        if (dateField) dateField.classList.add('border-red-500');
                        isValid = false;
                    } else {
                        const dateField = document.getElementById('quickDate');
                        if (dateField) dateField.classList.remove('border-red-500');
                    }
                }

                const appointmentTime = document.getElementById('quickTime')?.value;
                if (!appointmentTime) {
                    errors.push('Appointment time is required');
                    const timeField = document.getElementById('quickTime');
                    if (timeField) timeField.classList.add('border-red-500');
                    isValid = false;
                } else {
                    if (appointmentDate === new Date().toISOString().split('T')[0]) {
                        const selectedTime = new Date(`${appointmentDate}T${appointmentTime}`);
                        const now = new Date();

                        if (selectedTime <= now) {
                            errors.push('Appointment time cannot be in the past');
                            const timeField = document.getElementById('quickTime');
                            if (timeField) timeField.classList.add('border-red-500');
                            isValid = false;
                        } else {
                            const timeField = document.getElementById('quickTime');
                            if (timeField) timeField.classList.remove('border-red-500');
                        }
                    } else {
                        const timeField = document.getElementById('quickTime');
                        if (timeField) timeField.classList.remove('border-red-500');
                    }
                }

                // Doctor validation - only for non-doctors
                if (currentUserRole !== 'doctor') {
                    const doctorId = document.getElementById('quickDoctor')?.value;
                    if (!doctorId) {
                        errors.push('Doctor is required');
                        const doctorInput = document.querySelector('.doctor-search-input');
                        if (doctorInput) doctorInput.classList.add('border-red-500');
                        isValid = false;
                    } else {
                        const doctorInput = document.querySelector('.doctor-search-input');
                        if (doctorInput) doctorInput.classList.remove('border-red-500');
                    }
                }

                const patientId = document.getElementById('quickPatient')?.value;
                if (!patientId) {
                    errors.push('Patient is required');
                    const patientInput = document.querySelector('.patient-search-input');
                    if (patientInput) patientInput.classList.add('border-red-500');
                    isValid = false;
                } else {
                    const patientInput = document.querySelector('.patient-search-input');
                    if (patientInput) patientInput.classList.remove('border-red-500');
                }

            } else if (currentStep === 2) {
                const duration = document.getElementById('quickDuration')?.value;
                if (!duration || duration < 15 || duration > 240) {
                    errors.push('Duration must be between 15 and 240 minutes');
                    const durationField = document.getElementById('quickDuration');
                    if (durationField) durationField.classList.add('border-red-500');
                    isValid = false;
                } else {
                    const durationField = document.getElementById('quickDuration');
                    if (durationField) durationField.classList.remove('border-red-500');
                }

                const appointmentType = document.getElementById('quickType')?.value;
                if (!appointmentType) {
                    errors.push('Appointment type is required');
                    const typeField = document.getElementById('quickType');
                    if (typeField) typeField.classList.add('border-red-500');
                    isValid = false;
                } else {
                    const typeField = document.getElementById('quickType');
                    if (typeField) typeField.classList.remove('border-red-500');
                }

                const priority = document.getElementById('quickPriority')?.value;
                if (!priority) {
                    errors.push('Priority is required');
                    const priorityField = document.getElementById('quickPriority');
                    if (priorityField) priorityField.classList.add('border-red-500');
                    isValid = false;
                } else {
                    const priorityField = document.getElementById('quickPriority');
                    if (priorityField) priorityField.classList.remove('border-red-500');
                }

            } else if (currentStep === 3) {
                const chiefComplaint = document.getElementById('quickComplaint')?.value || '';
                if (chiefComplaint && chiefComplaint.length > 500) {
                    errors.push('Chief complaint cannot exceed 500 characters');
                    const complaintField = document.getElementById('quickComplaint');
                    if (complaintField) complaintField.classList.add('border-red-500');
                    isValid = false;
                } else {
                    const complaintField = document.getElementById('quickComplaint');
                    if (complaintField) complaintField.classList.remove('border-red-500');
                }

                const notes = document.getElementById('quickNotes')?.value || '';
                if (notes && notes.length > 1000) {
                    errors.push('Notes cannot exceed 1000 characters');
                    const notesField = document.getElementById('quickNotes');
                    if (notesField) notesField.classList.add('border-red-500');
                    isValid = false;
                } else {
                    const notesField = document.getElementById('quickNotes');
                    if (notesField) notesField.classList.remove('border-red-500');
                }
            }

            if (!isValid) {
                showToast(errors.join(', '), 'error');

                const firstErrorField = document.querySelector('.border-red-500');
                if (firstErrorField) {
                    firstErrorField.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    firstErrorField.focus();
                }
            }

            return isValid;
        }

        function updateSummary() {
            if (currentStep !== 3) return;

            try {
                // Basic Information Summary
                const appointmentDate = document.getElementById('quickDate')?.value || '';
                const appointmentTime = document.getElementById('quickTime')?.value || '';
                const duration = document.getElementById('quickDuration')?.value || '';

                // Format date for display
                const formattedDate = appointmentDate ?
                    new Date(appointmentDate).toLocaleDateString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }) : '-';

                // Format time for display
                const formattedTime = appointmentTime ?
                    new Date(`2000-01-01T${appointmentTime}`).toLocaleTimeString('en-US', {
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    }) : '-';

                // Update basic info in summary
                const summaryDate = document.getElementById('summary-date');
                const summaryTime = document.getElementById('summary-time');
                const summaryDuration = document.getElementById('summary-duration');

                if (summaryDate) summaryDate.textContent = formattedDate;
                if (summaryTime) summaryTime.textContent = formattedTime;
                if (summaryDuration) summaryDuration.textContent = duration ? `${duration} minutes` : '-';

                // Doctor Information Summary - only for non-doctors
                if (currentUserRole !== 'doctor') {
                    const doctorInput = document.querySelector('.doctor-search-input');
                    const doctorName = doctorInput?.value || '-';
                    const summaryDoctor = document.getElementById('summary-doctor');
                    if (summaryDoctor) summaryDoctor.textContent = doctorName;
                }

                // Patient Information Summary
                const patientInput = document.querySelector('.patient-search-input');
                const patientDisplay = patientInput?.value || '-';
                const summaryPatient = document.getElementById('summary-patient');
                if (summaryPatient) summaryPatient.textContent = patientDisplay;

                // Appointment Details Summary
                const appointmentType = document.getElementById('quickType')?.value || '';
                const priority = document.getElementById('quickPriority')?.value || '';

                const summaryType = document.getElementById('summary-type');
                const summaryPriority = document.getElementById('summary-priority');

                if (summaryType) {
                    summaryType.textContent = appointmentType ?
                        appointmentType.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) : '-';
                }

                if (summaryPriority) {
                    summaryPriority.textContent = priority ?
                        priority.charAt(0).toUpperCase() + priority.slice(1) : '-';

                    // Add priority color coding
                    summaryPriority.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ';
                    switch (priority) {
                        case 'urgent':
                            summaryPriority.className += 'bg-red-100 text-red-800';
                            break;
                        case 'high':
                            summaryPriority.className += 'bg-orange-100 text-orange-800';
                            break;
                        case 'medium':
                            summaryPriority.className += 'bg-yellow-100 text-yellow-800';
                            break;
                        case 'low':
                            summaryPriority.className += 'bg-green-100 text-green-800';
                            break;
                        default:
                            summaryPriority.className += 'bg-gray-100 text-gray-800';
                    }
                }

                // Additional Information Summary
                const chiefComplaint = document.getElementById('quickComplaint')?.value || '';
                const notes = document.getElementById('quickNotes')?.value || '';

                const summaryComplaint = document.getElementById('summary-complaint');
                const summaryNotes = document.getElementById('summary-notes');

                if (summaryComplaint) {
                    summaryComplaint.textContent = chiefComplaint || 'Not specified';
                }

                if (summaryNotes) {
                    summaryNotes.textContent = notes || 'No additional notes';
                }

                // Update character counters
                updateCharacterCounters();

            } catch (error) {
                console.error('Error updating summary:', error);
                showToast('Error updating appointment summary', 'error');
            }
        }

        function updateCharacterCounters() {
            const chiefComplaint = document.getElementById('quickComplaint');
            const notes = document.getElementById('quickNotes');

            if (chiefComplaint) {
                const complaintCounter = document.getElementById('complaint-counter');
                if (complaintCounter) {
                    const length = chiefComplaint.value.length;
                    complaintCounter.textContent = `${length}/500`;
                    complaintCounter.className = length > 450 ? 'text-red-500 text-xs' : 'text-gray-500 text-xs';
                }
            }

            if (notes) {
                const notesCounter = document.getElementById('notes-counter');
                if (notesCounter) {
                    const length = notes.value.length;
                    notesCounter.textContent = `${length}/1000`;
                    notesCounter.className = length > 900 ? 'text-red-500 text-xs' : 'text-gray-500 text-xs';
                }
            }
        }

        function setupFormValidation() {
            const complaintTextarea = document.getElementById('quickComplaint');
            const notesTextarea = document.getElementById('quickNotes');
            const complaintCounter = document.getElementById('complaint-counter');
            const notesCounter = document.getElementById('notes-counter');

            if (complaintTextarea && complaintCounter) {
                complaintTextarea.addEventListener('input', function() {
                    const count = this.value.length;
                    complaintCounter.textContent = `${count}/500`;
                    if (count > 450) {
                        complaintCounter.classList.add('text-red-500');
                    } else {
                        complaintCounter.classList.remove('text-red-500');
                    }
                });
            }

            if (notesTextarea && notesCounter) {
                notesTextarea.addEventListener('input', function() {
                    const count = this.value.length;
                    notesCounter.textContent = `${count}/1000`;
                    if (count > 900) {
                        notesCounter.classList.add('text-red-500');
                    } else {
                        notesCounter.classList.remove('text-red-500');
                    }
                });
            }
        }

        function setupEventListeners() {
            // Navigation buttons
            prevBtn.addEventListener('click', () => {
                if (currentView === 'month') {
                    currentDate.setMonth(currentDate.getMonth() - 1);
                } else if (currentView === 'week') {
                    currentDate.setDate(currentDate.getDate() - 7);
                } else {
                    currentDate.setDate(currentDate.getDate() - 1);
                }
                renderCalendar();
            });

            nextBtn.addEventListener('click', () => {
                if (currentView === 'month') {
                    currentDate.setMonth(currentDate.getMonth() + 1);
                } else if (currentView === 'week') {
                    currentDate.setDate(currentDate.getDate() + 7);
                } else {
                    currentDate.setDate(currentDate.getDate() + 1);
                }
                renderCalendar();
            });

            todayBtn.addEventListener('click', () => {
                currentDate = new Date();
                renderCalendar();
            });

            refreshBtn.addEventListener('click', () => {
                loadAppointments();
            });

            // View switcher
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active', 'bg-white/30'));
                    btn.classList.add('active', 'bg-white/30');
                    currentView = btn.dataset.view;
                    renderCalendar();
                });
            });

            // Filter buttons
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    selectedFilter = btn.dataset.filter;
                    renderCalendar();
                });
            });

            // Doctor filter - only for non-doctors
            if (doctorSelect && currentUserRole !== 'doctor') {
                doctorSelect.addEventListener('change', () => {
                    selectedDoctor = doctorSelect.value;
                    renderCalendar();
                });
            }

            // Quick create form
            document.getElementById('quickCreateForm').addEventListener('submit', handleQuickCreate);
        }

        function loadAppointments() {
            showLoading();

            const startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const endDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

            // Add doctor filter for doctors
            let url = `${appointmentsUrl}?start=${formatDate(startDate)}&end=${formatDate(endDate)}`;
            if (currentUserRole === 'doctor' && currentDoctorId) {
                url += `&doctor_id=${currentDoctorId}`;
            }

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    appointments = data.appointments || [];
                    renderCalendar();
                    hideLoading();
                })
                .catch(error => {
                    console.error('Error loading appointments:', error);
                    hideLoading();
                    showToast('Error loading appointments', 'error');
                });
        }

        function renderCalendar() {
            updateHeader();

            if (currentView === 'month') {
                renderMonthView();
            } else if (currentView === 'week') {
                renderWeekView();
            } else {
                renderDayView();
            }
        }

        function updateHeader() {
            const options = {
                year: 'numeric',
                month: 'long'
            };
            if (currentView === 'day') {
                options.day = 'numeric';
            }
            currentMonthEl.textContent = currentDate.toLocaleDateString('en-US', options);
        }

        function renderMonthView() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const firstDay = new Date(year, month, 1);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());

            let html = '<div class="grid grid-cols-7 gap-1 mb-4">';
            html += '<div class="p-3 text-center font-semibold text-gray-600 bg-gray-50 rounded-lg">Sun</div>';
            html += '<div class="p-3 text-center font-semibold text-gray-600 bg-gray-50 rounded-lg">Mon</div>';
            html += '<div class="p-3 text-center font-semibold text-gray-600 bg-gray-50 rounded-lg">Tue</div>';
            html += '<div class="p-3 text-center font-semibold text-gray-600 bg-gray-50 rounded-lg">Wed</div>';
            html += '<div class="p-3 text-center font-semibold text-gray-600 bg-gray-50 rounded-lg">Thu</div>';
            html += '<div class="p-3 text-center font-semibold text-gray-600 bg-gray-50 rounded-lg">Fri</div>';
            html += '<div class="p-3 text-center font-semibold text-gray-600 bg-gray-50 rounded-lg">Sat</div>';
            html += '</div>';
            html += '<div class="grid grid-cols-7 gap-1">';

            const currentDateObj = new Date(startDate);
            for (let i = 0; i < 42; i++) {
                const dayAppointments = getAppointmentsForDate(currentDateObj);
                const isCurrentMonth = currentDateObj.getMonth() === month;
                const isToday = isDateToday(currentDateObj);
                const dateStr = formatDate(currentDateObj);

                html += '<div class="min-h-[120px] p-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200 cursor-pointer ';
                html += isCurrentMonth ? 'bg-white' : 'bg-gray-50';
                html += isToday ? ' ring-2 ring-blue-500' : '';
                html += '" data-date="' + dateStr + '" onclick="selectDate(\'' + dateStr + '\')" ondblclick="quickCreateAppointment(\'' + dateStr + '\')">';

                html += '<div class="flex items-center justify-between mb-2">';
                html += '<span class="text-sm font-semibold ';
                html += isCurrentMonth ? 'text-gray-900' : 'text-gray-400';
                html += isToday ? ' text-blue-600' : '';
                html += '">' + currentDateObj.getDate() + '</span>';

                if (dayAppointments.length > 0) {
                    html += '<span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full">' + dayAppointments.length + '</span>';
                }
                html += '</div>';

                html += '<div class="space-y-1">';
                for (let j = 0; j < Math.min(dayAppointments.length, 3); j++) {
                    const apt = dayAppointments[j];

                    // Debug: Log the appointment data to console
                    console.log('Appointment data:', apt);

                    let patientName = 'Unknown Patient';

                    if (apt.patient && apt.patient.name) {
                        patientName = apt.patient.name;
                    } else if (apt.patient_name) {
                        patientName = apt.patient_name;
                    } else if (apt.patient && apt.patient.patient_name) {
                        patientName = apt.patient.patient_name;
                    }

                    html += '<div class="text-xs p-1 rounded cursor-pointer hover:opacity-80 transition-opacity duration-200 ' + getAppointmentColor(apt) + '"';
                    html += ' onclick="event.stopPropagation(); showAppointmentDetails(' + apt.id + ')"';
                    html += ' title="' + patientName + ' - ' + apt.appointment_time + '">';
                    html += '<div class="font-medium truncate">' + patientName + '</div>';
                    html += '<div class="truncate">' + apt.appointment_time + '</div>';
                    html += '</div>';
                }

                if (dayAppointments.length > 3) {
                    html += '<div class="text-xs text-gray-500 text-center">+' + (dayAppointments.length - 3) + ' more</div>';
                }
                html += '</div>';
                html += '</div>';

                currentDateObj.setDate(currentDateObj.getDate() + 1);
            }

            html += '</div>';
            calendarGrid.innerHTML = html;
        }

        function renderWeekView() {
            const startOfWeek = new Date(currentDate);
            startOfWeek.setDate(currentDate.getDate() - currentDate.getDay());

            let html = '<div class="grid grid-cols-8 gap-1 mb-4">';
            html += '<div class="p-3"></div>';

            for (let i = 0; i < 7; i++) {
                const day = new Date(startOfWeek);
                day.setDate(startOfWeek.getDate() + i);
                const isToday = isDateToday(day);

                html += '<div class="p-3 text-center font-semibold ';
                html += isToday ? 'text-blue-600 bg-blue-50' : 'text-gray-600 bg-gray-50';
                html += ' rounded-lg">';
                html += '<div class="text-sm">' + day.toLocaleDateString('en-US', {
                    weekday: 'short'
                }) + '</div>';
                html += '<div class="text-lg">' + day.getDate() + '</div>';
                html += '</div>';
            }
            html += '</div>';

            html += '<div class="grid grid-cols-8 gap-1" style="grid-template-rows: repeat(24, minmax(60px, auto));">';

            // Time slots
            for (let hour = 0; hour < 24; hour++) {
                html += '<div class="p-2 text-xs text-gray-500 bg-gray-50 rounded-lg border-r">';
                html += (hour < 10 ? '0' : '') + hour + ':00';
                html += '</div>';

                // Days for this hour
                for (let day = 0; day < 7; day++) {
                    const currentDay = new Date(startOfWeek);
                    currentDay.setDate(startOfWeek.getDate() + day);
                    const hourAppointments = getAppointmentsForDateHour(currentDay, hour);
                    const dateStr = formatDate(currentDay);
                    const timeStr = (hour < 10 ? '0' : '') + hour + ':00';

                    html += '<div class="p-1 border border-gray-200 hover:bg-gray-50 transition-colors duration-200 cursor-pointer"';
                    html += ' data-date="' + dateStr + '" data-hour="' + hour + '"';
                    html += ' onclick="quickCreateAppointment(\'' + dateStr + '\', \'' + timeStr + '\')">';

                    for (let k = 0; k < hourAppointments.length; k++) {
                        const apt = hourAppointments[k];
                        const patientName = apt.patient ? apt.patient.name : (apt.patient_name || 'Unknown Patient');
                        html += '<div class="text-xs p-1 mb-1 rounded cursor-pointer hover:opacity-80 transition-opacity duration-200 ' + getAppointmentColor(apt) + '"';
                        html += ' onclick="event.stopPropagation(); showAppointmentDetails(' + apt.id + ')"';
                        html += ' title="' + patientName + ' - ' + apt.appointment_time + '">';
                        html += '<div class="font-medium truncate">' + patientName + '</div>';
                        html += '<div class="truncate">' + apt.appointment_time + '</div>';
                        html += '</div>';
                    }
                    html += '</div>';
                }
            }

            html += '</div>';
            calendarGrid.innerHTML = html;
        }

        function renderDayView() {
            const dayAppointments = getAppointmentsForDate(currentDate);

            let html = '<div class="space-y-4">';
            html += '<div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">';
            html += '<h3 class="text-xl font-bold text-blue-900 mb-2">';
            html += currentDate.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            html += '</h3>';
            html += '<p class="text-blue-700">';
            html += dayAppointments.length + ' appointment' + (dayAppointments.length !== 1 ? 's' : '') + ' scheduled';
            html += '</p>';
            html += '</div>';

            html += '<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">';

            // Time slots for day view
            for (let hour = 8; hour < 20; hour++) {
                const hourAppointments = getAppointmentsForDateHour(currentDate, hour);
                const timeSlot = (hour < 10 ? '0' : '') + hour + ':00';
                const dateStr = formatDate(currentDate);

                html += '<div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-all duration-200">';
                html += '<div class="flex items-center justify-between mb-3">';
                html += '<h4 class="font-semibold text-gray-800">' + timeSlot + '</h4>';
                html += '<button class="text-blue-600 hover:text-blue-800 transition-colors duration-200"';
                html += ' onclick="quickCreateAppointment(\'' + dateStr + '\', \'' + timeSlot + '\')">';
                html += '<i class="fas fa-plus"></i>';
                html += '</button>';
                html += '</div>';
                html += '<div class="space-y-2">';

                if (hourAppointments.length > 0) {
                    for (let i = 0; i < hourAppointments.length; i++) {
                        const apt = hourAppointments[i];
                        const patientName = apt.patient ? apt.patient.name : (apt.patient_name || 'Unknown Patient');
                        const doctorName = apt.doctor ? apt.doctor.name : (apt.doctor_name || 'Unknown Doctor');
                        html += '<div class="p-3 rounded-lg cursor-pointer hover:opacity-80 transition-opacity duration-200 ' + getAppointmentColor(apt) + '"';
                        html += ' onclick="showAppointmentDetails(' + apt.id + ')">';
                        html += '<div class="flex items-center justify-between">';
                        html += '<div>';
                        html += '<div class="font-medium">' + patientName + '</div>';
                        html += '<div class="text-sm opacity-75">Dr. ' + doctorName + '</div>';
                        html += '<div class="text-xs opacity-60">' + apt.appointment_time + ' - ' + apt.appointment_type.replace('_', ' ') + '</div>';
                        html += '</div>';
                        html += '<div class="text-right">';
                        html += '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ' + getStatusBadgeColor(apt.status) + '">';
                        html += apt.status.charAt(0).toUpperCase() + apt.status.slice(1);
                        html += '</span>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                    }
                } else {
                    html += '<div class="text-center py-8 text-gray-400">';
                    html += '<i class="fas fa-clock text-2xl mb-2"></i>';
                    html += '<p class="text-sm">No appointments</p>';
                    html += '</div>';
                }
                html += '</div>';
                html += '</div>';
            }

            html += '</div></div>';
            calendarGrid.innerHTML = html;
        }

        function getAppointmentsForDate(date) {
            const dateStr = formatDate(date);
            return appointments.filter(apt => {
                const aptDate = apt.appointment_date;
                const matchesDate = aptDate === dateStr;
                const matchesDoctor = selectedDoctor === 'all' || apt.doctor_id == selectedDoctor;
                const matchesFilter = selectedFilter === 'all' ||
                    (selectedFilter === 'today' && isDateToday(date)) ||
                    apt.status === selectedFilter;
                return matchesDate && matchesDoctor && matchesFilter;
            });
        }

        function getAppointmentsForDateHour(date, hour) {
            const dayAppointments = getAppointmentsForDate(date);
            return dayAppointments.filter(apt => {
                const aptTime = apt.appointment_time.split(':');
                const aptHour = parseInt(aptTime[0]);
                return aptHour === hour;
            });
        }

        function getAppointmentColor(appointment) {
            const colors = {
                'scheduled': 'bg-blue-100 text-blue-800 border-blue-200',
                'confirmed': 'bg-green-100 text-green-800 border-green-200',
                'completed': 'bg-gray-100 text-gray-800 border-gray-200',
                'cancelled': 'bg-red-100 text-red-800 border-red-200',
                'no_show': 'bg-yellow-100 text-yellow-800 border-yellow-200'
            };
            return colors[appointment.status] || 'bg-gray-100 text-gray-800 border-gray-200';
        }

        function getStatusBadgeColor(status) {
            const colors = {
                'scheduled': 'bg-blue-100 text-blue-800',
                'confirmed': 'bg-green-100 text-green-800',
                'completed': 'bg-gray-100 text-gray-800',
                'cancelled': 'bg-red-100 text-red-800',
                'no_show': 'bg-yellow-100 text-yellow-800'
            };
            return colors[status] || 'bg-gray-100 text-gray-800';
        }

        function isDateToday(date) {
            const today = new Date();
            return date.toDateString() === today.toDateString();
        }

        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return year + '-' + month + '-' + day;
        }

        function selectDate(dateStr) {
            document.querySelectorAll('[data-date]').forEach(el => {
                el.classList.remove('bg-blue-50', 'ring-2', 'ring-blue-500');
            });

            const selectedEl = document.querySelector('[data-date="' + dateStr + '"]');
            if (selectedEl) {
                selectedEl.classList.add('bg-blue-50', 'ring-2', 'ring-blue-500');
            }
        }

        function quickCreateAppointment(dateStr, time) {
            document.getElementById('quickDate').value = dateStr;
            if (time) {
                document.getElementById('quickTime').value = time;
            }
            currentStep = 1;
            updateStepDisplay();
            showModal('quickCreateModal');
        }

        function showAppointmentDetails(appointmentId) {
            showLoading();

            fetch(`/admin/appointments/${appointmentId}/details`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Response is not JSON');
                    }

                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const appointment = data.appointment;
                        document.getElementById('appointmentModalBody').innerHTML = buildAppointmentDetailsHTML(appointment);
                        hideLoading();
                        showModal('appointmentModal');
                    } else {
                        throw new Error(data.message || 'Failed to load appointment details');
                    }
                })
                .catch(error => {
                    console.error('Error loading appointment details:', error);
                    hideLoading();
                    showToast('Error loading appointment details: ' + error.message, 'error');
                });
        }

        function buildAppointmentDetailsHTML(appointment) {
            let html = '<div class="space-y-6">';

            // Patient and Doctor Info
            html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">';
            html += '<div class="space-y-4">';
            html += '<div>';
            html += '<label class="block text-sm font-medium text-gray-700 mb-1">Patient</label>';
            html += '<div class="flex items-center space-x-3">';
            html += '<div>';
            html += '<div class="font-semibold text-gray-900">' + appointment.patient.name + '</div>';
            html += '<div class="text-sm text-gray-500">' + appointment.patient.patient_id + ' • ' + (appointment.patient.email || 'No email') + '</div>';
            if (appointment.patient.phone) {
                html += '<div class="text-sm text-gray-500">' + appointment.patient.phone + '</div>';
            }
            html += '</div>';
            html += '</div>';
            html += '</div>';

            html += '<div>';
            html += '<label class="block text-sm font-medium text-gray-700 mb-1">Doctor</label>';
            html += '<div class="flex items-center space-x-3">';
            html += '<div>';
            html += '<div class="font-semibold text-gray-900">Dr. ' + appointment.doctor.name + '</div>';
            html += '<div class="text-sm text-gray-500">' + appointment.doctor.specialization + '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';

            // Appointment Details
            html += '<div class="space-y-4">';
            html += '<div>';
            html += '<label class="block text-sm font-medium text-gray-700 mb-1">Date & Time</label>';
            html += '<div class="text-lg font-semibold text-gray-900">';
            html += new Date(appointment.appointment_date).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            html += '</div>';
            html += '<div class="text-gray-600">' + formatTime(appointment.appointment_time);
            if (appointment.end_time) {
                html += ' - ' + formatTime(appointment.end_time);
            }
            html += ' (' + appointment.duration + ' minutes)</div>';
            html += '</div>';

            html += '<div class="grid grid-cols-2 gap-4">';
            html += '<div>';
            html += '<label class="block text-sm font-medium text-gray-700 mb-1">Status</label>';
            html += '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ' + getStatusBadgeColor(appointment.status) + '">';
            html += appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1).replace('_', ' ');
            html += '</span>';
            html += '</div>';

            html += '<div>';
            html += '<label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>';
            html += '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ' + getPriorityBadgeColor(appointment.priority) + '">';
            html += appointment.priority.charAt(0).toUpperCase() + appointment.priority.slice(1);
            html += '</span>';
            html += '</div>';
            html += '</div>';

            html += '<div>';
            html += '<label class="block text-sm font-medium text-gray-700 mb-1">Type</label>';
            html += '<div class="text-gray-900">' + appointment.appointment_type.replace('_', ' ').replace(/\b\w/g, function(l) {
                return l.toUpperCase();
            }) + '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';

            // Chief Complaint and Notes
            if (appointment.chief_complaint) {
                html += '<div>';
                html += '<label class="block text-sm font-medium text-gray-700 mb-1">Chief Complaint</label>';
                html += '<div class="bg-gray-50 rounded-lg p-3 text-gray-900">' + appointment.chief_complaint + '</div>';
                html += '</div>';
            }

            if (appointment.notes) {
                html += '<div>';
                html += '<label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>';
                html += '<div class="bg-gray-50 rounded-lg p-3 text-gray-900">' + appointment.notes + '</div>';
                html += '</div>';
            }

            // Action Buttons
            html += '<div class="flex flex-wrap justify-center gap-3 pt-4 border-t border-gray-200">';
            html += '<a href="/admin/appointments/' + appointment.id + '" class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200">';
            html += '<i class="fas fa-eye mr-2"></i>View Details</a>';


            html += '</div>';
            html += '</div>';

            return html;
        }

        // Helper functions
        function formatTime(timeString) {
            const time = new Date('2000-01-01T' + timeString);
            return time.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        }

        function getPriorityBadgeColor(priority) {
            const colors = {
                'urgent': 'bg-red-100 text-red-800',
                'high': 'bg-orange-100 text-orange-800',
                'medium': 'bg-yellow-100 text-yellow-800',
                'low': 'bg-green-100 text-green-800'
            };
            return colors[priority] || 'bg-gray-100 text-gray-800';
        }

        function handleQuickCreate(e) {
            e.preventDefault();

            if (!validateCurrentStep()) {
                return;
            }

            const formData = new FormData(e.target);
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';

            fetch('/admin/appointments', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Appointment created successfully!', 'success');
                        closeModal('quickCreateModal');
                        e.target.reset();
                        resetForm();
                        loadAppointments();
                    } else {
                        showToast(data.message || 'Error creating appointment', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error creating appointment:', error);
                    showToast('Error creating appointment', 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        }

        function resetForm() {
            currentStep = 1;
            updateStepDisplay();

            clearPatientSelection();
            if (doctorSearchInput) doctorSearchInput.value = '';
            if (doctorHiddenInput) doctorHiddenInput.value = '';

            const complaintCounter = document.getElementById('complaint-counter');
            const notesCounter = document.getElementById('notes-counter');
            if (complaintCounter) complaintCounter.textContent = '0/500';
            if (notesCounter) notesCounter.textContent = '0/1000';

            hidePatientInfo();
            const doctorInfo = document.getElementById('doctor-info');
            if (doctorInfo) {
                doctorInfo.classList.add('hidden');
            }
        }

        function updateAppointmentStatus(appointmentId, status) {
            if (!confirm('Are you sure you want to ' + status + ' this appointment?')) {
                return;
            }

            fetch(`/admin/appointments/${appointmentId}/${status}`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Appointment ' + status + ' successfully!', 'success');
                        closeModal('appointmentModal');
                        loadAppointments();
                    } else {
                        showToast(data.message || 'Error ' + status + 'ing appointment', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error ' + status + 'ing appointment:', error);
                    showToast('Error ' + status + 'ing appointment', 'error');
                });
        }

        // Modal functions
        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                const firstInput = modal.querySelector('input, select, textarea');
                if (firstInput) {
                    setTimeout(() => firstInput.focus(), 100);
                }
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';

                if (modalId === 'quickCreateModal') {
                    resetForm();
                }
            }
        }

        // Loading functions
        function showLoading() {
            const loading = document.getElementById('loadingOverlay');
            if (loading) {
                loading.classList.remove('hidden');
                loading.classList.add('flex');
            }
        }

        function hideLoading() {
            const loading = document.getElementById('loadingOverlay');
            if (loading) {
                loading.classList.add('hidden');
                loading.classList.remove('flex');
            }
        }

        // Toast notification function
        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';

            toast.className = `${bgColor} text-white px-6 py-4 rounded-xl shadow-lg transform translate-x-full transition-transform duration-300 max-w-sm`;
            toast.innerHTML = `
                <div class="flex items-center space-x-3">
                    <i class="fas fa-${icon} flex-shrink-0"></i>
                    <span class="flex-1">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 hover:opacity-75 flex-shrink-0">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);

            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 300);
            }, 5000);
        }

        // Export functions
        window.exportCalendar = function(format) {
            const params = new URLSearchParams({
                format: format,
                month: currentDate.getMonth() + 1,
                year: currentDate.getFullYear(),
                doctor: selectedDoctor,
                filter: selectedFilter
            });

            window.open('/admin/calendar/export?' + params.toString(), '_blank');
            showToast('Exporting calendar to ' + format.toUpperCase() + '...', 'info');
        };

        window.printCalendar = function() {
            window.print();
        };

        // Global functions for modal controls
        window.showModal = showModal;
        window.closeModal = closeModal;
        window.showAppointmentDetails = showAppointmentDetails;
        window.quickCreateAppointment = quickCreateAppointment;
        window.selectDate = selectDate;
        window.updateAppointmentStatus = updateAppointmentStatus;
        window.nextStep = nextStep;
        window.prevStep = prevStep;

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.fixed.inset-0').forEach(function(modal) {
                    if (!modal.classList.contains('hidden')) {
                        modal.classList.add('hidden');
                    }
                });
                document.body.style.overflow = 'auto';
            }

            if (e.ctrlKey || e.metaKey) {
                switch (e.key) {
                    case 'ArrowLeft':
                        e.preventDefault();
                        prevBtn.click();
                        break;
                    case 'ArrowRight':
                        e.preventDefault();
                        nextBtn.click();
                        break;
                    case 't':
                        e.preventDefault();
                        todayBtn.click();
                        break;
                    case 'n':
                        e.preventDefault();
                        const createLink = document.querySelector('a[href*="appointments/create"]');
                        if (createLink) {
                            createLink.click();
                        }
                        break;
                }
            }
        });

        // Touch/swipe support for mobile
        let touchStartX = 0;
        let touchEndX = 0;

        if (calendarGrid) {
            calendarGrid.addEventListener('touchstart', function(e) {
                touchStartX = e.changedTouches[0].screenX;
            });

            calendarGrid.addEventListener('touchend', function(e) {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
            });
        }

        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;

            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    nextBtn.click();
                } else {
                    prevBtn.click();
                }
            }
        }

        // Auto-refresh every 5 minutes
        setInterval(function() {
            loadAppointments();
        }, 300000);

        // Handle window resize
        window.addEventListener('resize', function() {
            clearTimeout(window.resizeTimeout);
            window.resizeTimeout = setTimeout(function() {
                renderCalendar();
            }, 250);
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Calendar specific styles */
    .calendar-grid {
        min-height: 600px;
    }

    /* Step indicator styles */
    .step-indicator {
        display: flex;
        align-items: center;
        flex: 1;
    }

    .step-indicator:not(:last-child)::after {
        content: '';
        flex: 1;
        height: 2px;
        background: #e5e7eb;
        margin-left: 1rem;
    }

    .step-indicator.completed::after {
        background: #10b981;
    }

    /* Search dropdown styles */
    .search-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 50;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        max-height: 200px;
        overflow-y: auto;
    }

    /* Custom scrollbar for dropdowns */
    .search-dropdown::-webkit-scrollbar {
        width: 6px;
    }

    .search-dropdown::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .search-dropdown::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .search-dropdown::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }

        .print-only {
            display: block !important;
        }

        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .calendar-grid {
            break-inside: avoid;
        }

        .bg-gradient-to-r {
            background: #3b82f6 !important;
            color: white !important;
        }

        /* Hide interactive elements when printing */
        button,
        .hover\:bg-gray-50:hover {
            display: none !important;
        }

        /* Ensure calendar is visible */
        .calendar-grid {
            display: block !important;
        }
    }

    /* Custom scrollbar */
    .overflow-y-auto::-webkit-scrollbar {
        width: 8px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Animation classes */
    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }

    .slide-up {
        animation: slideUp 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Hover effects */
    .hover-lift {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    /* Active states */
    .filter-btn.active {
        background-color: rgb(59 130 246) !important;
        color: white !important;
    }

    .view-btn.active {
        background-color: rgba(255, 255, 255, 0.3) !important;
    }

    /* Loading spinner */
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Backdrop blur support */
    .backdrop-blur-sm {
        backdrop-filter: blur(4px);
    }

    /* Focus styles */
    .focus-ring:focus {
        outline: 2px solid transparent;
        outline-offset: 2px;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
    }

    /* Calendar day hover effects */
    [data-date]:hover {
        transform: scale(1.02);
        transition: transform 0.2s ease-in-out;
    }

    /* Appointment item animations */
    .appointment-item {
        transition: all 0.2s ease-in-out;
    }

    .appointment-item:hover {
        transform: translateX(2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Modal animations */
    .modal-enter {
        animation: modalEnter 0.3s ease-out;
    }

    @keyframes modalEnter {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(-20px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    /* Toast notification animations */
    .notification-enter {
        animation: notificationEnter 0.3s ease-out;
    }

    @keyframes notificationEnter {
        from {
            opacity: 0;
            transform: translateX(100%);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Mobile optimizations */
    @media (max-width: 768px) {
        .calendar-grid {
            font-size: 0.875rem;
        }

        .min-h-\[120px\] {
            min-height: 80px;
        }

        .grid-cols-7 {
            gap: 0.25rem;
        }

        .p-6 {
            padding: 1rem;
        }

        .space-y-6>*+* {
            margin-top: 1rem;
        }

        .text-3xl {
            font-size: 1.5rem;
            line-height: 2rem;
        }

        .text-2xl {
            font-size: 1.25rem;
            line-height: 1.75rem;
        }

        .px-6 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .py-4 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        .gap-4 {
            gap: 0.75rem;
        }

        .gap-6 {
            gap: 1rem;
        }

        .rounded-2xl {
            border-radius: 1rem;
        }

        .shadow-lg {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Calendar specific mobile adjustments */
        .calendar-day {
            min-height: 60px;
            padding: 0.5rem;
        }

        .appointment-item {
            font-size: 0.75rem;
            padding: 0.25rem;
            margin-bottom: 0.25rem;
        }

        /* Modal adjustments for mobile */
        .modal-content {
            margin: 1rem;
            max-height: calc(100vh - 2rem);
        }

        /* Button adjustments for mobile */
        .btn-mobile {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
        }

        /* Header adjustments for mobile */
        .calendar-header {
            flex-direction: column;
            gap: 1rem;
            padding: 1rem;
        }

        .calendar-nav {
            justify-content: center;
            gap: 0.5rem;
        }

        /* Filter bar mobile adjustments */
        .filter-bar {
            flex-direction: column;
            gap: 1rem;
        }

        .filter-buttons {
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        /* Week view mobile adjustments */
        .week-view {
            font-size: 0.75rem;
        }

        .week-header {
            padding: 0.5rem 0.25rem;
        }

        .week-time-slot {
            padding: 0.25rem;
            min-height: 40px;
        }

        /* Day view mobile adjustments */
        .day-view-grid {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .day-time-slot {
            padding: 0.75rem;
        }

        /* Step form mobile adjustments */
        .step-form {
            padding: 1rem;
        }

        .step-indicators {
            flex-direction: column;
            gap: 0.5rem;
        }

        .step-indicator::after {
            display: none;
        }
    }

    /* Extra small screens */
    @media (max-width: 480px) {
        .calendar-grid {
            font-size: 0.75rem;
        }

        .min-h-\[120px\] {
            min-height: 60px;
        }

        .grid-cols-7 {
            gap: 0.125rem;
        }

        .p-6 {
            padding: 0.75rem;
        }

        .px-4 {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .py-3 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .text-lg {
            font-size: 1rem;
            line-height: 1.5rem;
        }

        .calendar-day {
            min-height: 50px;
            padding: 0.25rem;
        }

        .appointment-item {
            font-size: 0.625rem;
            padding: 0.125rem;
        }

        .modal-content {
            margin: 0.5rem;
            border-radius: 0.75rem;
        }

        .btn-mobile {
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
        }
    }

    /* Landscape orientation adjustments */
    @media (max-width: 768px) and (orientation: landscape) {
        .calendar-header {
            flex-direction: row;
            padding: 0.75rem 1rem;
        }

        .filter-bar {
            flex-direction: row;
            gap: 0.75rem;
        }

        .modal-content {
            max-height: calc(100vh - 1rem);
            margin: 0.5rem;
        }
    }

    /* High contrast mode support */
    @media (prefers-contrast: high) {
        .border-gray-200 {
            border-color: #000;
        }

        .text-gray-600 {
            color: #000;
        }

        .bg-gray-50 {
            background-color: #f0f0f0;
        }
    }

    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }

    /* Dark mode support (if needed) */
    @media (prefers-color-scheme: dark) {
        .bg-white {
            background-color: #1f2937;
            color: #f9fafb;
        }

        .text-gray-900 {
            color: #f9fafb;
        }

        .text-gray-600 {
            color: #d1d5db;
        }

        .text-gray-500 {
            color: #9ca3af;
        }

        .border-gray-200 {
            border-color: #374151;
        }

        .bg-gray-50 {
            background-color: #111827;
        }

        .bg-gray-100 {
            background-color: #1f2937;
        }
    }

    /* Patient option hover effects */
    .patient-option:hover,
    .doctor-option:hover {
        background-color: #f3f4f6;
        transform: translateX(2px);
    }

    .patient-option.selected,
    .doctor-option.selected {
        background-color: #dbeafe;
        border-left: 3px solid #3b82f6;
    }

    /* Calendar cell improvements */
    .calendar-cell {
        position: relative;
        overflow: hidden;
    }

    .calendar-cell::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent 49%, rgba(59, 130, 246, 0.1) 50%, transparent 51%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .calendar-cell:hover::before {
        opacity: 1;
    }

    /* Appointment status indicators */
    .status-indicator {
        position: absolute;
        top: 2px;
        right: 2px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .status-scheduled {
        background-color: #3b82f6;
    }

    .status-confirmed {
        background-color: #10b981;
    }

    .status-completed {
        background-color: #6b7280;
    }

    .status-cancelled {
        background-color: #ef4444;
    }

    .status-no_show {
        background-color: #f59e0b;
    }

    /* Loading states */
    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    /* Tooltip styles */
    .tooltip {
        position: relative;
    }

    .tooltip::before {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background-color: #1f2937;
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
        z-index: 1000;
    }

    .tooltip::after {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(100%);
        border: 4px solid transparent;
        border-top-color: #1f2937;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .tooltip:hover::before,
    .tooltip:hover::after {
        opacity: 1;
    }

    /* Form validation styles */
    .form-error {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
    }

    .form-success {
        border-color: #10b981 !important;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
    }

    /* Calendar navigation improvements */
    .calendar-nav-btn {
        transition: all 0.2s ease;
    }

    .calendar-nav-btn:hover {
        transform: scale(1.1);
        background-color: rgba(255, 255, 255, 0.2);
    }

    .calendar-nav-btn:active {
        transform: scale(0.95);
    }

    /* Appointment time slots */
    .time-slot {
        position: relative;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .time-slot:hover {
        background-color: #f8fafc;
        border-color: #3b82f6;
    }

    .time-slot.occupied {
        background-color: #fef3c7;
        border-color: #f59e0b;
    }

    .time-slot.available {
        background-color: #ecfdf5;
        border-color: #10b981;
    }

    /* Priority indicators */
    .priority-urgent {
        border-left: 4px solid #ef4444;
    }

    .priority-high {
        border-left: 4px solid #f97316;
    }

    .priority-medium {
        border-left: 4px solid #eab308;
    }

    .priority-low {
        border-left: 4px solid #22c55e;
    }

    /* Search input enhancements */
    .search-input {
        transition: all 0.3s ease;
    }

    .search-input:focus {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Modal backdrop */
    .modal-backdrop {
        backdrop-filter: blur(4px);
        background-color: rgba(0, 0, 0, 0.5);
    }

    /* Step progress bar */
    .step-progress {
        height: 4px;
        background-color: #e5e7eb;
        border-radius: 2px;
        overflow: hidden;
    }

    .step-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #3b82f6, #10b981);
        transition: width 0.3s ease;
    }

    /* Calendar event overflow handling */
    .calendar-events {
        max-height: 80px;
        overflow-y: auto;
    }

    .calendar-events::-webkit-scrollbar {
        width: 3px;
    }

    .calendar-events::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 1.5px;
    }

    /* Responsive text sizing */
    @media (max-width: 640px) {
        .responsive-text-lg {
            font-size: 1rem;
            line-height: 1.5rem;
        }

        .responsive-text-xl {
            font-size: 1.125rem;
            line-height: 1.75rem;
        }

        .responsive-text-2xl {
            font-size: 1.25rem;
            line-height: 1.75rem;
        }

        .responsive-text-3xl {
            font-size: 1.5rem;
            line-height: 2rem;
        }
    }

    /* Calendar grid improvements */
    .calendar-grid-container {
        position: relative;
        overflow: hidden;
        border-radius: 0.75rem;
    }

    .calendar-grid-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%);
        pointer-events: none;
    }

    /* Enhanced button styles */
    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.39);
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px 0 rgba(59, 130, 246, 0.5);
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.39);
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px 0 rgba(16, 185, 129, 0.5);
    }

    /* Calendar legend improvements */
    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem;
        border-radius: 0.375rem;
        transition: background-color 0.2s ease;
    }

    .legend-item:hover {
        background-color: #f8fafc;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    /* Accessibility improvements */
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    /* Focus visible for keyboard navigation */
    .focus-visible:focus {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }

    /* High DPI display optimizations */
    @media (-webkit-min-device-pixel-ratio: 2),
    (min-resolution: 192dpi) {
        .calendar-grid {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    }

    /* Print optimizations */
    @media print {
        .calendar-container {
            break-inside: avoid;
        }

        .appointment-item {
            break-inside: avoid;
        }

        .no-print,
        .modal,
        .toast,
        button:not(.print-btn) {
            display: none !important;
        }

        .calendar-grid {
            box-shadow: none !important;
            border: 1px solid #000 !important;
        }

        .appointment-item {
            border: 1px solid #666 !important;
            background: white !important;
            color: black !important;
        }
    }

    /* Performance optimizations */
    .will-change-transform {
        will-change: transform;
    }

    .will-change-opacity {
        will-change: opacity;
    }

    /* Container queries support (future-proofing) */
    @container (max-width: 768px) {
        .container-responsive {
            padding: 0.5rem;
        }
    }

    /* Custom properties for theming */
    :root {
        --calendar-primary: #3b82f6;
        --calendar-success: #10b981;
        --calendar-warning: #f59e0b;
        --calendar-danger: #ef4444;
        --calendar-gray: #6b7280;
        --calendar-border: #e5e7eb;
        --calendar-bg: #ffffff;
        --calendar-text: #1f2937;
        --calendar-text-muted: #6b7280;
        --calendar-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        --calendar-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --calendar-radius: 0.5rem;
        --calendar-radius-lg: 0.75rem;
    }

    /* Use custom properties */
    .calendar-themed {
        background-color: var(--calendar-bg);
        color: var(--calendar-text);
        border-color: var(--calendar-border);
        border-radius: var(--calendar-radius);
        box-shadow: var(--calendar-shadow);
    }

    /* Animation performance */
    .gpu-accelerated {
        transform: translateZ(0);
        backface-visibility: hidden;
        perspective: 1000px;
    }

    /* Smooth scrolling */
    .smooth-scroll {
        scroll-behavior: smooth;
    }

    /* Calendar cell states */
    .calendar-cell-today {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
        border: 2px solid var(--calendar-primary);
    }

    .calendar-cell-selected {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.2) 0%, rgba(59, 130, 246, 0.1) 100%);
        border: 2px solid var(--calendar-primary);
        transform: scale(1.02);
    }

    .calendar-cell-disabled {
        opacity: 0.5;
        pointer-events: none;
        background-color: #f9fafb;
    }

    /* Enhanced transitions */
    .transition-all-smooth {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .transition-transform-smooth {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Final responsive adjustments */
    @media (max-width: 375px) {
        .ultra-small-text {
            font-size: 0.625rem;
            line-height: 0.875rem;
        }

        .ultra-small-padding {
            padding: 0.25rem;
        }

        .ultra-small-gap {
            gap: 0.25rem;
        }
    }
</style>
@endpush