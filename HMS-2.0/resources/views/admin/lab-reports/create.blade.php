@extends('admin.layouts.app')

@section('title', 'Create Lab Report')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Create Lab Report</h2>
                        <p class="text-gray-600 mt-1">Generate a new laboratory test report</p>
                    </div>
                    <a href="{{ route('admin.lab-reports.index') }}"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Reports
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

                <form action="{{ route('admin.lab-reports.store') }}" method="POST" id="reportForm" class="space-y-6">
                    @csrf

                    <!-- Lab ID Input -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-flask text-blue-600 mr-2"></i>
                            <h3 class="text-lg font-medium text-gray-900">Lab Information</h3>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Lab ID Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lab ID <span class="text-red-500">*</span></label>
                                <input type="text" name="lab_id" id="lab_id" required
                                    placeholder="Enter Lab ID (e.g., LAB-001)"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('lab_id') border-red-500 @enderror">
                                @error('lab_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Enter the Lab ID to fetch associated tests and patient information.</p>
                            </div>

                            <!-- Fetch Data Button -->
                            <div class="flex items-end">
                                <button type="button" id="fetchLabDataBtn"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition duration-200">
                                    <i class="fas fa-search mr-2"></i>Fetch Lab Data
                                </button>
                            </div>
                        </div>

                        <!-- Loading Indicator -->
                        <div id="labDataLoading" class="mt-4 hidden">
                            <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-blue-500 bg-blue-100">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Fetching lab data...
                            </div>
                        </div>

                        <!-- Lab Data Results -->
                        <div id="labDataResults" class="mt-4 hidden">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                    <span class="text-green-800 font-medium">Lab data loaded successfully</span>
                                </div>
                                <div id="labDataSummary" class="text-sm text-green-700"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Basic Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-4">
                                <!-- Patient Selection -->
                                <div id="patient_selection_container">
                                    <label class="block text-sm font-medium text-gray-700">Patient <span class="text-red-500">*</span></label>
                                    <select name="patient_id" id="patient_id" required
                                        class="select2-patient mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('patient_id') border-red-500 @enderror">
                                        <option value="">Search and select a patient...</option>
                                    </select>
                                    @error('patient_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror

                                    <!-- Selected Patient Info -->
                                    <div id="patient_info" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-md hidden">
                                        <h6 class="font-medium text-blue-900">Selected Patient:</h6>
                                        <div id="patient_details" class="text-sm text-blue-800 mt-1"></div>
                                    </div>
                                </div>

                                <!-- Auto-filled Patient Info -->
                                <div id="auto_patient_info" class="hidden">
                                    <label class="block text-sm font-medium text-gray-700">Patient (Auto-filled from Lab ID)</label>
                                    <div class="mt-1 p-3 bg-green-50 border border-green-200 rounded-md">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h6 class="font-medium text-green-900" id="auto_patient_name"></h6>
                                                <div id="auto_patient_details" class="text-sm text-green-800 mt-1"></div>
                                            </div>
                                            <button type="button" id="change_patient_btn" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                <i class="fas fa-edit mr-1"></i>Change
                                            </button>
                                        </div>
                                        <input type="hidden" name="patient_id" id="auto_patient_id" value="">
                                    </div>
                                </div>

                                <!-- Advised By (Auto-filled from Lab ID) -->
                                <div id="auto_doctor_info" class="hidden">
                                    <label class="block text-sm font-medium text-gray-700">Advised By (Auto-filled from Lab ID)</label>
                                    <div class="mt-1 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h6 class="font-medium text-blue-900" id="auto_doctor_name"></h6>
                                                <div id="auto_doctor_details" class="text-sm text-blue-800 mt-1"></div>
                                            </div>
                                            <button type="button" id="change_doctor_btn" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                <i class="fas fa-edit mr-1"></i>Change
                                            </button>
                                        </div>
                                        <input type="hidden" name="advised_by_auto" id="auto_doctor_value">
                                    </div>
                                </div>

                                <!-- Lab Test Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Lab Test <span class="text-red-500">*</span></label>
                                    <select name="lab_test_id" id="lab_test_id" required
                                        class="select2-lab-test mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('lab_test_id') border-red-500 @enderror">
                                        <option value="">Search and select a lab test...</option>
                                        @foreach($labTests as $test)
                                        <option value="{{ $test->id }}"
                                            data-category="{{ $test->category }}"
                                            {{ old('lab_test_id') == $test->id ? 'selected' : '' }}>
                                            {{ $test->test_name }}
                                            @if($test->category)
                                            ({{ $test->category }})
                                            @endif
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('lab_test_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Report Template Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Report Template <span class="text-red-500">*</span></label>
                                    <select name="template_id" id="template_id" required disabled
                                        class="select2-template mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('template_id') border-red-500 @enderror">
                                        <option value="">Select lab test first...</option>
                                    </select>
                                    @error('template_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>

                            <!-- Right Column -->
                            <div class="space-y-4">
                                <!-- Report Date -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Report Date <span class="text-red-500">*</span></label>
                                    <input type="date" name="report_date" id="report_date" required
                                        value="{{ old('report_date', date('Y-m-d')) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('report_date') border-red-500 @enderror">
                                    @error('report_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Advised By -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Advised By</label>
                                    <input type="text" name="advised_by" id="advised_by"
                                        value="{{ old('advised_by') }}"
                                        placeholder="Doctor name or SELF"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('advised_by') border-red-500 @enderror">
                                    @error('advised_by')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Care Of Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Care Of</label>
                                    <select name="care_of_id" id="care_of_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('care_of_id') border-red-500 @enderror">
                                        <option value="">Select Care Of (Optional)</option>
                                        @foreach($careOfs as $careOf)
                                        <option value="{{ $careOf->id }}" {{ old('care_of_id') == $careOf->id ? 'selected' : '' }}>
                                            {{ $careOf->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('care_of_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                                    <select name="status" id="status" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                                        <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="verified" {{ old('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                                    </select>
                                    @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Investigation Details -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Investigation Details</label>
                            <textarea name="investigation_details" id="investigation_details" rows="3"
                                placeholder="Additional investigation details..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('investigation_details') border-red-500 @enderror">{{ old('investigation_details') }}</textarea>
                            @error('investigation_details')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Report Sections -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                            <div class="flex items-center mb-2 sm:mb-0">
                                <i class="fas fa-list-alt text-blue-600 mr-2"></i>
                                <h3 class="text-lg font-medium text-gray-900">Report Sections</h3>
                            </div>
                            <button type="button" id="addSectionBtn"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                                <i class="fas fa-plus mr-2"></i>Add Section
                            </button>
                        </div>

                        <div id="sections_container">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                <span class="text-blue-800">Select a template to load predefined sections, or add sections manually.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-sticky-note text-blue-600 mr-2"></i>
                            <h3 class="text-lg font-medium text-gray-900">Additional Notes</h3>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Technical Notes -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Technical Notes</label>
                                <textarea name="technical_notes" id="technical_notes" rows="4"
                                    placeholder="Technical observations, methodology notes..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('technical_notes') border-red-500 @enderror">{{ old('technical_notes') }}</textarea>
                                @error('technical_notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Doctor Comments -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Doctor Comments</label>
                                <textarea name="doctor_comments" id="doctor_comments" rows="4"
                                    placeholder="Clinical interpretation, recommendations..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('doctor_comments') border-red-500 @enderror">{{ old('doctor_comments') }}</textarea>
                                @error('doctor_comments')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row justify-between space-y-3 sm:space-y-0 sm:space-x-3 pt-6 border-t">
                        <a href="{{ route('admin.lab-reports.index') }}"
                            class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-6 rounded-lg transition duration-200 text-center">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                            <button type="submit" name="action" value="save_draft" id="saveDraftBtn"
                                class="w-full sm:w-auto bg-gray-600 hover:bg-gray-700 text-white font-medium py-2.5 px-6 rounded-lg transition duration-200">
                                <i class="fas fa-save mr-2"></i><span id="draftText">Save as Draft</span>
                            </button>
                            <button type="submit" name="action" value="save_complete" id="saveCompleteBtn"
                                class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition duration-200">
                                <i class="fas fa-check mr-2"></i><span id="completeText">Save & Complete</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Section Template (Hidden) -->
<div id="section_template" class="hidden">
    <div class="section-item bg-white border border-gray-200 rounded-lg p-4 mb-4 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
            <h6 class="text-lg font-medium text-gray-900 mb-2 sm:mb-0">
                Section <span class="section-number bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm"></span>
            </h6>
            <button type="button" class="remove-section-btn bg-red-100 hover:bg-red-200 text-red-700 font-medium py-1.5 px-3 rounded-lg transition duration-200 text-sm">
                <i class="fas fa-trash mr-1"></i>Remove
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Section Name <span class="text-red-500">*</span></label>
                <input type="text" name="sections[INDEX][section_name]"
                    class="section-name mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Section Description</label>
                <input type="text" name="sections[INDEX][section_description]"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="fields-container">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-2 sm:mb-0">Fields</label>
                <button type="button" class="add-field-btn bg-green-100 hover:bg-green-200 text-green-700 font-medium py-1.5 px-3 rounded-lg transition duration-200 text-sm">
                    <i class="fas fa-plus mr-1"></i>Add Field
                </button>
            </div>
            <div class="fields-list space-y-3"></div>
        </div>
    </div>
</div>

<!-- Field Template (Hidden) -->
<div id="field_template" class="hidden">
    <div class="field-item bg-gray-50 border-l-4 border-blue-500 p-3 rounded-r-lg">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 mb-3">
            <div class="lg:col-span-1">
                <input type="text" name="sections[SECTION_INDEX][fields][FIELD_INDEX][field_name]"
                    class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Field Name" required>
            </div>
            <div class="lg:col-span-1">
                <input type="text" name="sections[SECTION_INDEX][fields][FIELD_INDEX][field_label]"
                    class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Display Label" required>
            </div>
            <div class="lg:col-span-1">
                <input type="text" name="sections[SECTION_INDEX][fields][FIELD_INDEX][field_value]"
                    class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="result">
            </div>
            <div class="lg:col-span-1">
                <input type="text" name="sections[SECTION_INDEX][fields][FIELD_INDEX][unit]"
                    class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Unit">
            </div>
            <!-- <div class="lg:col-span-1 flex items-center">
                <div class="flex items-center">
                    <input type="checkbox" name="sections[SECTION_INDEX][fields][FIELD_INDEX][is_abnormal]"
                        class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50" value="1">
                    <label class="ml-2 text-xs text-gray-700">Abnormal</label>
                </div>
            </div> -->
            <div class="lg:col-span-1 flex justify-end">
                <button type="button" class="remove-field-btn bg-red-100 hover:bg-red-200 text-red-700 font-medium py-1 px-2 rounded transition duration-200 text-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <input type="text" name="sections[SECTION_INDEX][fields][FIELD_INDEX][normal_range]"
                    class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Normal Range">
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
        let sectionIndex = 0;
        let labDataFetched = false;

        // Initialize Select2 for patient search with AJAX
        function initializePatientSelect2() {
            $('#patient_id').select2({
                theme: 'default',
                placeholder: 'Search by name, phone, or patient ID...',
                allowClear: true,
                width: '100%',
                minimumInputLength: 2,
                ajax: {
                    url: '{{ route("admin.lab-reports.api.search-patients") }}',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            search: params.term,
                            page: params.page || 1,
                            limit: 20
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        if (data.success && data.patients) {
                            return {
                                results: data.patients.map(function(patient) {
                                    return {
                                        id: patient.id,
                                        text: patient.display_name,
                                        subtitle: patient.subtitle,
                                        patient_data: patient
                                    };
                                }),
                                pagination: {
                                    more: data.patients.length >= 20
                                }
                            };
                        }
                        return {
                            results: []
                        };
                    },
                    cache: true
                },
                templateResult: function(patient) {
                    if (patient.loading) {
                        return patient.text;
                    }

                    if (!patient.patient_data) {
                        return patient.text;
                    }

                    const data = patient.patient_data;
                    const $result = $(
                        '<div class="select2-result-patient">' +
                        '<div class="select2-result-patient__name">' + data.display_name + '</div>' +
                        '<div class="select2-result-patient__details">' + data.subtitle + '</div>' +
                        (data.is_recent ? '<span class="select2-result-patient__badge">Recent</span>' : '') +
                        '</div>'
                    );

                    return $result;
                },
                templateSelection: function(patient) {
                    if (patient.patient_data) {
                        // Show patient info when selected
                        showPatientInfo(patient.patient_data);
                        return patient.patient_data.display_name;
                    }
                    return patient.text;
                },
                escapeMarkup: function(markup) {
                    return markup;
                }
            });
        }

        initializePatientSelect2();

        // Function to fetch lab data
        function fetchLabData() {
            const labId = $('#lab_id').val().trim();

            if (!labId) {
                alert('Please enter a Lab ID first.');
                $('#lab_id').focus();
                return;
            }

            // Show loading
            $('#labDataLoading').removeClass('hidden');
            $('#labDataResults').addClass('hidden');
            $('#fetchLabDataBtn').prop('disabled', true);

            // Fetch lab data
            fetch(`/admin/lab-reports/api/lab-id-data?lab_id=${encodeURIComponent(labId)}`)
                .then(response => response.json())
                .then(data => {
                    $('#labDataLoading').addClass('hidden');
                    $('#fetchLabDataBtn').prop('disabled', false);

                    if (data.success) {
                        labDataFetched = true;
                        handleLabDataResponse(data);
                        $('#labDataResults').removeClass('hidden');
                    } else {
                        alert(data.message || 'Failed to fetch lab data.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching lab data:', error);
                    $('#labDataLoading').addClass('hidden');
                    $('#fetchLabDataBtn').prop('disabled', false);
                    alert('An error occurred while fetching lab data.');
                });
        }

        // Fetch Lab Data Button Handler
        $('#fetchLabDataBtn').on('click', function() {
            fetchLabData();
        });

        // Trigger fetch on Enter key in Lab ID field
        $('#lab_id').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                fetchLabData();
            }
        });

        // Handle Lab Data Response
        function handleLabDataResponse(data) {
            const summary = [];

            // Handle Patient
            if (data.patient) {
                showAutoPatientInfo(data.patient);
                summary.push(`Patient: ${data.patient.name}`);
            } else {
                showManualPatientSelection();
                summary.push('Patient: Manual selection required');
            }

            // Handle Doctor
            if (data.doctor) {
                showAutoDoctorInfo(data.doctor);
                // Auto-set status to completed when doctor is found
                $('#status').val('completed');
                summary.push(`Doctor: ${data.doctor.name}`);
            } else {
                hideAutoDoctorInfo();
                summary.push('Doctor: Not specified');
            }

            // Handle Tests
            if (data.tests && data.tests.length > 0) {
                populateLabTests(data.tests);
                summary.push(`Tests: ${data.tests.length} test(s) found`);
            } else {
                $('#lab_test_id').empty().append('<option value="">No tests found for this Lab ID</option>');
                summary.push('Tests: No tests found');
            }

            // Update summary
            $('#labDataSummary').html(summary.join('<br>'));
        }

        // Show Auto Patient Info
        function showAutoPatientInfo(patient) {
            $('#auto_patient_name').text(`${patient.name} (${patient.patient_id})`);
            $('#auto_patient_details').html(`
                Phone: ${patient.phone || 'N/A'}<br>
                Email: ${patient.email || 'N/A'}<br>
                Age: ${patient.age || 'N/A'} years
            `);
            $('#auto_patient_id').val(patient.id); // Use patient_id (string) for form submission
            $('#auto_patient_info').removeClass('hidden');
            $('#patient_selection_container').addClass('hidden');
            // Remove required attribute when auto-filled
            $('#patient_id').prop('required', false);
        }

        // Show Auto Doctor Info
        function showAutoDoctorInfo(doctor) {
            $('#auto_doctor_name').text(doctor.display_name);
            $('#auto_doctor_details').html(`
                Specialization: ${doctor.specialization || 'N/A'}<br>
                License: ${doctor.license_number || 'N/A'}
            `);
            $('#auto_doctor_value').val(doctor.name);
            $('#advised_by').val(doctor.name); // Set the advised_by field
            $('#auto_doctor_info').removeClass('hidden');
        }

        // Hide Auto Doctor Info
        function hideAutoDoctorInfo() {
            $('#auto_doctor_info').addClass('hidden');
            $('#auto_doctor_value').val('');
            // Don't clear advised_by field, let user manually enter
        }

        // Show Manual Patient Selection
        function showManualPatientSelection() {
            $('#auto_patient_info').addClass('hidden');
            $('#patient_selection_container').removeClass('hidden');
            $('#patient_id').val('').trigger('change');
            $('#patient_info').addClass('hidden');
            // Add required attribute back when manual selection is shown
            $('#patient_id').prop('required', true);
        }

        // Change Patient Button
        $('#change_patient_btn').on('click', function() {
            showManualPatientSelection();
        });

        // Change Doctor Button
        $('#change_doctor_btn').on('click', function() {
            hideAutoDoctorInfo();
        });

        // Populate Lab Tests
        function populateLabTests(tests) {
            const testSelect = $('#lab_test_id');
            testSelect.empty().append('<option value="">Select a test...</option>');

            tests.forEach(test => {
                testSelect.append(`<option value="${test.id}" data-category="${test.category || ''}">${test.display_name}</option>`);
            });

            // Re-initialize Select2 if needed
            if (testSelect.hasClass('select2-hidden-accessible')) {
                testSelect.select2('destroy');
            }
            testSelect.select2({
                theme: 'default',
                placeholder: 'Select a test...',
                allowClear: true,
                width: '100%'
            });
        }

        // Function to show patient info
        function showPatientInfo(patient) {
            const patientInfo = $('#patient_info');
            const patientDetails = $('#patient_details');

            patientDetails.html(`
                <strong>${patient.name}</strong> (${patient.patient_id})<br>
                Phone: ${patient.phone || 'N/A'}<br>
                Email: ${patient.email || 'N/A'}<br>
                Age: ${patient.age || 'N/A'} years
            `);
            patientInfo.removeClass('hidden');
        }

        // Handle patient selection change
        $('#patient_id').on('change', function() {
            if (!$(this).val()) {
                $('#patient_info').addClass('hidden');
            }
        });

        // Initialize Select2 for lab test search
        $('#lab_test_id').select2({
            theme: 'default',
            placeholder: 'Search and select a lab test...',
            allowClear: true,
            width: '100%'
        });

        // Initialize Select2 for template selection
        $('#template_id').select2({
            theme: 'default',
            placeholder: 'Select lab test first...',
            allowClear: true,
            width: '100%'
        });


        // Lab test and template functionality
        const labTestSelect = $('#lab_test_id');
        const templateSelect = $('#template_id');

        labTestSelect.on('change', function() {
            const testId = this.value;
            const templateSelect = $('#template_id');
            templateSelect.empty().append('<option value="">Loading templates...</option>');
            templateSelect.prop('disabled', true);
            templateSelect.select2('destroy').select2({
                theme: 'default',
                placeholder: 'Loading templates...',
                allowClear: true,
                width: '100%'
            });

            if (testId) {
                fetch(`/admin/lab-reports/templates/api/test/${testId}/templates`)
                    .then(response => response.json())
                    .then(templates => {
                        templateSelect.empty().append('<option value="">Select Template</option>');
                        let defaultTemplateId = null;
                        templates.forEach(template => {
                            templateSelect.append(`<option value="${template.id}">${template.template_name}</option>`);
                            if (template.is_default) {
                                defaultTemplateId = template.id;
                            }
                        });
                        templateSelect.prop('disabled', false);
                        templateSelect.select2('destroy').select2({
                            theme: 'default',
                            placeholder: 'Search and select a template...',
                            allowClear: true,
                            width: '100%'
                        });

                        // Auto-select and load template
                        let templateToLoad = defaultTemplateId;
                        if (!templateToLoad && templates.length > 0) {
                            // If no default template, use the first available template
                            templateToLoad = templates[0].id;
                        }

                        if (templateToLoad) {
                            templateSelect.val(templateToLoad).trigger('change');
                            // Load the template structure directly
                            loadTemplateStructure(templateToLoad);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading templates:', error);
                        templateSelect.empty().append('<option value="">Error loading templates</option>');
                        templateSelect.select2('destroy').select2({
                            theme: 'default',
                            placeholder: 'Error loading templates',
                            allowClear: true,
                            width: '100%'
                        });
                    });
            } else {
                templateSelect.empty().append('<option value="">Select lab test first</option>');
                templateSelect.prop('disabled', true);
                templateSelect.select2('destroy').select2({
                    theme: 'default',
                    placeholder: 'Select lab test first...',
                    allowClear: true,
                    width: '100%'
                });
                // Clear sections when no test is selected
                const sectionsContainer = $('#sections_container');
                sectionsContainer.html(`
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        <span class="text-blue-800">Select a lab test to load the template automatically.</span>
                    </div>
                `);
            }
        });


        function loadTemplateStructure(templateId) {
            // Show loading indicator
            const sectionsContainer = $('#sections_container');
            sectionsContainer.html(`
                <div class="text-center py-8">
                    <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-blue-500 bg-blue-100">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading template structure...
                    </div>
                </div>
            `);

            fetch(`/admin/lab-reports/templates/api/${templateId}/structure`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(template => {
                    sectionsContainer.empty();
                    sectionIndex = 0;

                    if (template.sections && template.sections.length > 0) {
                        template.sections.forEach(section => {
                            addSection(section);
                        });
                    } else {
                        sectionsContainer.html(`
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                                <span class="text-yellow-800">This template has no predefined sections. You can add sections manually.</span>
                            </div>
                        `);
                    }
                })
                .catch(error => {
                    console.error('Error loading template structure:', error);
                    sectionsContainer.html(`
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                            <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
                            <span class="text-red-800">Failed to load template structure. Please try again or add sections manually.</span>
                        </div>
                    `);
                });
        }

        // Section management
        const addSectionBtn = $('#addSectionBtn');
        const sectionsContainer = $('#sections_container');

        addSectionBtn.on('click', function() {
            addSection();
        });

        function addSection(sectionData = null) {
            const template = $('#section_template');
            const sectionElement = template.clone();
            sectionElement.attr('id', '');
            sectionElement.removeClass('hidden');

            // Update section number
            sectionElement.find('.section-number').text(sectionIndex + 1);

            // Update field names with current index
            updateSectionIndexes(sectionElement, sectionIndex);

            // Populate with template data if provided
            if (sectionData) {
                sectionElement.find('.section-name').val(sectionData.section_name);
                sectionElement.find('input[name*="section_description"]').val(sectionData.section_description || '');

                // Add fields
                const fieldsContainer = sectionElement.find('.fields-list');
                sectionData.fields.forEach(fieldData => {
                    addField(fieldsContainer, sectionIndex, fieldData);
                });
            }

            sectionsContainer.append(sectionElement);

            // Add event listeners
            sectionElement.find('.remove-section-btn').on('click', function() {
                sectionElement.remove();
                updateSectionNumbers();
            });

            sectionElement.find('.add-field-btn').on('click', function() {
                const fieldsContainer = sectionElement.find('.fields-list');
                addField(fieldsContainer, sectionIndex);
            });

            sectionIndex++;
        }

        function addField(fieldsContainer, sectionIdx, fieldData = null) {
            const template = $('#field_template');
            const fieldElement = template.clone();
            fieldElement.attr('id', '');
            fieldElement.removeClass('hidden');

            const fieldIndex = fieldsContainer.children().length;

            // Update field names with indexes
            updateFieldIndexes(fieldElement, sectionIdx, fieldIndex);

            // Populate with data if provided
            if (fieldData) {
                fieldElement.find('input[name*="field_name"]').val(fieldData.field_name);
                fieldElement.find('input[name*="field_label"]').val(fieldData.field_label);
                fieldElement.find('input[name*="field_value"]').val(fieldData.field_value || '');
                fieldElement.find('input[name*="unit"]').val(fieldData.unit || '');
                fieldElement.find('input[name*="normal_range"]').val(fieldData.normal_range || '');
                fieldElement.find('input[name*="is_abnormal"]').prop('checked', fieldData.is_abnormal || false);
            }

            fieldsContainer.append(fieldElement);

            // Add remove functionality
            fieldElement.find('.remove-field-btn').on('click', function() {
                fieldElement.remove();
            });
        }

        function updateSectionIndexes(sectionElement, index) {
            const inputs = sectionElement.find('input, textarea, select');
            inputs.each(function() {
                if ($(this).attr('name')) {
                    $(this).attr('name', $(this).attr('name').replace(/\[INDEX\]/g, `[${index}]`));
                }
            });
        }

        function updateFieldIndexes(fieldElement, sectionIndex, fieldIndex) {
            const inputs = fieldElement.find('input, textarea, select');
            inputs.each(function() {
                if ($(this).attr('name')) {
                    $(this).attr('name', $(this).attr('name')
                        .replace(/\[SECTION_INDEX\]/g, `[${sectionIndex}]`)
                        .replace(/\[FIELD_INDEX\]/g, `[${fieldIndex}]`));
                }
            });
        }

        function updateSectionNumbers() {
            const sections = sectionsContainer.find('.section-item');
            sections.each(function(index) {
                $(this).find('.section-number').text(index + 1);
                updateSectionIndexes($(this), index);

                // Update field indexes within this section
                const fields = $(this).find('.field-item');
                fields.each(function(fieldIndex) {
                    updateFieldIndexes($(this), index, fieldIndex);
                });
            });
            sectionIndex = sections.length;
        }

        // Form submission handling
        const reportForm = $('#reportForm');
        const saveDraftBtn = $('#saveDraftBtn');
        const saveCompleteBtn = $('#saveCompleteBtn');
        const draftText = $('#draftText');
        const completeText = $('#completeText');

        reportForm.on('submit', function(e) {
            const submitter = $(e.originalEvent.submitter);
            const action = submitter.val();

            // Validate that lab data has been fetched
            if (!labDataFetched) {
                e.preventDefault();
                alert('Please fetch lab data first by entering a Lab ID and clicking "Fetch Lab Data".');
                $('#lab_id').focus();
                return;
            }

            // Show loading state
            if (action === 'save_complete') {
                $('#status').val('completed');
                saveCompleteBtn.prop('disabled', true);
                completeText.text('Saving...');
                saveCompleteBtn.addClass('opacity-75');
            } else if (action === 'save_draft') {
                $('#status').val('draft');
                saveDraftBtn.prop('disabled', true);
                draftText.text('Saving...');
                saveDraftBtn.addClass('opacity-75');
            }

            // Validate that at least one section exists
            const sections = sectionsContainer.find('.section-item');
            if (sections.length === 0) {
                e.preventDefault();
                alert('Please add at least one section to the report.');

                // Reset button states
                saveDraftBtn.prop('disabled', false);
                saveCompleteBtn.prop('disabled', false);
                draftText.text('Save as Draft');
                completeText.text('Save & Complete');
                saveDraftBtn.removeClass('opacity-75');
                saveCompleteBtn.removeClass('opacity-75');
                return;
            }

            // Validate that each section has at least one field
            let hasEmptySection = false;
            sections.each(function() {
                const fields = $(this).find('.field-item');
                if (fields.length === 0) {
                    hasEmptySection = true;
                }
            });

            if (hasEmptySection) {
                e.preventDefault();
                alert('Each section must have at least one field.');

                // Reset button states
                saveDraftBtn.prop('disabled', false);
                saveCompleteBtn.prop('disabled', false);
                draftText.text('Save as Draft');
                completeText.text('Save & Complete');
                saveDraftBtn.removeClass('opacity-75');
                saveCompleteBtn.removeClass('opacity-75');
                return;
            }
        });

        // Real-time validation
        const inputs = reportForm.find('input[required], select[required], textarea[required]');
        inputs.on('blur', function() {
            if ($(this).val().trim()) {
                $(this).removeClass('border-red-500').addClass('border-green-500');
            } else {
                $(this).removeClass('border-green-500').addClass('border-red-500');
            }
        });

        // Select2 validation
        $('#patient_id, #lab_test_id').on('change', function() {
            if ($(this).val()) {
                $(this).next('.select2-container').find('.select2-selection').removeClass('border-red-500').addClass('border-green-500');
            } else {
                $(this).next('.select2-container').find('.select2-selection').removeClass('border-green-500').addClass('border-red-500');
            }
        });

        // Auto-hide success/error messages
        const alerts = $('.bg-green-100, .bg-red-100');
        alerts.each(function() {
            const alert = $(this);
            setTimeout(function() {
                alert.fadeOut(300, function() {
                    alert.remove();
                });
            }, 5000);
        });

        // Mobile responsive improvements
        function handleMobileLayout() {
            const isMobile = window.innerWidth < 768;

            if (isMobile) {
                // Adjust Select2 dropdown positioning for mobile
                $('.select2-container').css('z-index', '9999');

                // Make dropdowns full width on mobile
                $('.select2-dropdown').css({
                    'width': '100%',
                    'max-width': '100%'
                });
            }
        }

        // Call on load and resize
        handleMobileLayout();
        $(window).on('resize', handleMobileLayout);

        // Prevent form submission on Enter key in input fields (except textareas)
        reportForm.find('input').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                return false;
            }
        });

        // Add smooth scrolling to newly added sections
        $(document).on('click', '#addSectionBtn', function() {
            setTimeout(function() {
                const lastSection = sectionsContainer.find('.section-item').last();
                if (lastSection.length) {
                    $('html, body').animate({
                        scrollTop: lastSection.offset().top - 100
                    }, 500);
                }
            }, 100);
        });

        // Initialize template loading if pre-selected
        if ($('#lab_test_id').val()) {
            $('#lab_test_id').trigger('change');
        }
    });
</script>

<style>
    /* Custom Select2 styling to match Tailwind */
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
        padding: 0.5rem 0.75rem !important;
        background-color: #ffffff !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5rem !important;
        padding-left: 0 !important;
        color: #374151 !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        right: 8px !important;
    }

    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }

    .select2-dropdown {
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        z-index: 9999 !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6 !important;
        color: white !important;
    }

    /* Custom patient result styling */
    .select2-result-patient {
        padding: 0.5rem 0;
    }

    .select2-result-patient__name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .select2-result-patient__details {
        font-size: 0.875rem;
        color: #6b7280;
        line-height: 1.25rem;
    }

    .select2-result-patient__badge {
        display: inline-block;
        background-color: #dbeafe;
        color: #1e40af;
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.125rem 0.5rem;
        border-radius: 9999px;
        margin-top: 0.25rem;
    }

    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
        .select2-container {
            width: 100% !important;
        }

        .select2-dropdown {
            width: 100% !important;
            max-width: 100% !important;
            left: 0 !important;
            right: 0 !important;
        }

        .select2-search--dropdown .select2-search__field {
            width: 100% !important;
        }

        /* Adjust field layout on mobile */
        .field-item .grid {
            grid-template-columns: 1fr !important;
        }

        .field-item .lg\:col-span-1 {
            grid-column: span 1 !important;
        }

        /* Stack form elements vertically on mobile */
        .section-item .grid {
            grid-template-columns: 1fr !important;
        }
    }

    /* Loading animation */
    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* Improved focus states */
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }

    /* Better disabled state */
    .select2-container--default .select2-selection--single.select2-selection--disabled {
        background-color: #f9fafb !important;
        color: #9ca3af !important;
        cursor: not-allowed !important;
    }

    /* Scrollbar styling for dropdown */
    .select2-results__options {
        scrollbar-width: thin;
        scrollbar-color: #d1d5db #f9fafb;
    }

    .select2-results__options::-webkit-scrollbar {
        width: 6px;
    }

    .select2-results__options::-webkit-scrollbar-track {
        background: #f9fafb;
        border-radius: 3px;
    }

    .select2-results__options::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }

    .select2-results__options::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    /* Loading state for Select2 */
    .select2-container--default .select2-results__message {
        color: #6b7280;
        font-style: italic;
    }

    /* Error state styling */
    .border-red-500+.select2-container .select2-selection {
        border-color: #ef4444 !important;
    }

    .border-green-500+.select2-container .select2-selection {
        border-color: #10b981 !important;
    }

    /* Ensure proper z-index layering */
    .select2-container {
        z-index: 1000;
    }

    .select2-dropdown {
        z-index: 9999 !important;
    }

    /* Better spacing for mobile */
    @media (max-width: 640px) {
        .space-y-4>*+* {
            margin-top: 1rem;
        }

        .space-y-6>*+* {
            margin-top: 1.5rem;
        }

        .gap-6 {
            gap: 1rem;
        }

        .p-6 {
            padding: 1rem;
        }

        .px-4 {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
    }
</style>
@endpush