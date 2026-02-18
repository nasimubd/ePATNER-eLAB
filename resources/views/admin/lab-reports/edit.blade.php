@extends('admin.layouts.app')

@section('title', 'Edit Lab Report')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Edit Lab Report</h2>
                    <p class="text-gray-600 mt-1">Report #{{ $labReport->report_number }}</p>
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 mt-4 sm:mt-0">
                    <a href="{{ route('admin.lab-reports.show', $labReport) }}"
                        class="inline-flex items-center justify-center px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium rounded-lg transition duration-200">
                        <i class="fas fa-eye mr-2"></i>View Report
                    </a>
                    <a href="{{ route('admin.lab-reports.index') }}"
                        class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Reports
                    </a>
                </div>
            </div>
        </div>

        <!-- Warning for verified reports -->
        @if($labReport->status === 'verified')
        <div class="mx-6 mt-6 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <div>
                    <strong>Warning:</strong> This report has been verified. Changes should be made carefully and may require re-verification.
                </div>
            </div>
        </div>
        @endif

        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="mx-6 mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="mx-6 mt-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            <strong>Please correct the following errors:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.lab-reports.update', $labReport) }}" method="POST" id="reportForm" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Hidden fields for validation -->
            <input type="hidden" name="patient_id" value="{{ $labReport->patient->patient_id }}">
            <input type="hidden" name="lab_test_id" value="{{ $labReport->lab_test_id }}">
            <input type="hidden" name="template_id" value="{{ $labReport->template_id }}">

            <!-- Basic Information -->
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-info-circle text-blue-600"></i>
                    </div>
                    Basic Information
                </h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Patient Info (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Patient</label>
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-3">
                                        @if(method_exists($labReport->patient, 'hasProfileImage') && $labReport->patient->hasProfileImage())
                                        <img src="{{ route('admin.patients.image', $labReport->patient) }}" alt="{{ $labReport->patient->first_name }}" class="w-12 h-12 rounded-full object-cover">
                                        @else
                                        <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                            {{ strtoupper(substr($labReport->patient->first_name, 0, 1) . substr($labReport->patient->last_name ?? '', 0, 1)) }}
                                        </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $labReport->patient->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $labReport->patient->patient_id }} | {{ $labReport->patient->phone }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lab Test (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lab Test</label>
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                <div class="text-sm font-medium text-gray-900">{{ $labReport->labTest->test_name }}</div>
                                @if($labReport->labTest->category)
                                <div class="text-sm text-gray-500">Category: {{ $labReport->labTest->category }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Report Date -->
                        <div>
                            <label for="report_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Report Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="report_date" id="report_date" required
                                value="{{ $labReport->report_date->format('Y-m-d') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('report_date') border-red-300 @enderror">
                            @error('report_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Advised By -->
                        <div>
                            <label for="advised_by" class="block text-sm font-medium text-gray-700 mb-2">Advised By</label>
                            <input type="text" name="advised_by" id="advised_by"
                                value="{{ old('advised_by', $labReport->advised_by) }}"
                                placeholder="Doctor name or SELF"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('advised_by') border-red-300 @enderror">
                            @error('advised_by')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Care Of Selection -->
                        <div>
                            <label for="care_of_id" class="block text-sm font-medium text-gray-700 mb-2">Care Of</label>
                            <select name="care_of_id" id="care_of_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('care_of_id') border-red-300 @enderror">
                                <option value="">Select Care Of (Optional)</option>
                                @foreach(\App\Models\CareOf::where('business_id', Auth::user()->business_id)->where('status', 'active')->orderBy('name')->get() as $careOf)
                                <option value="{{ $careOf->id }}" {{ old('care_of_id', $labReport->care_of_id) == $careOf->id ? 'selected' : '' }}>
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
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('status') border-red-300 @enderror">
                                <option value="draft" {{ $labReport->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="completed" {{ $labReport->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="verified" {{ $labReport->status === 'verified' ? 'selected' : '' }}>Verified</option>
                            </select>
                            @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Investigation Details -->
                <div class="mt-6">
                    <label for="investigation_details" class="block text-sm font-medium text-gray-700 mb-2">Investigation Details</label>
                    <textarea name="investigation_details" id="investigation_details" rows="3"
                        placeholder="Additional investigation details..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('investigation_details') border-red-300 @enderror">{{ old('investigation_details', $labReport->investigation_details) }}</textarea>
                    @error('investigation_details')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Report Sections -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 sm:mb-0 flex items-center">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-list-alt text-green-600"></i>
                        </div>
                        Report Sections
                    </h3>
                    <button type="button" id="addSectionBtn"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200">
                        <i class="fas fa-plus mr-2"></i>Add Section
                    </button>
                </div>

                <div id="sections_container">
                    @foreach($labReport->sections as $sectionIndex => $section)
                    <div class="section-item bg-white border border-gray-200 rounded-lg p-6 mb-6 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                            <h6 class="text-lg font-medium text-gray-900 mb-2 sm:mb-0">
                                Section <span class="section-number bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">{{ $sectionIndex + 1 }}</span>
                            </h6>
                            <button type="button" class="remove-section-btn bg-red-100 hover:bg-red-200 text-red-700 font-medium py-2 px-4 rounded-lg transition duration-200 text-sm">
                                <i class="fas fa-trash mr-1"></i>Remove
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Section Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="sections[{{ $sectionIndex }}][section_name]"
                                    class="section-name w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    value="{{ old('sections.'.$sectionIndex.'.section_name', $section->section_name) }}" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Section Description</label>
                                <input type="text" name="sections[{{ $sectionIndex }}][section_description]"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    value="{{ old('sections.'.$sectionIndex.'.section_description', $section->section_description) }}">
                            </div>
                        </div>

                        <div class="fields-container">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2 sm:mb-0">Fields</label>
                                <button type="button" class="add-field-btn bg-green-100 hover:bg-green-200 text-green-700 font-medium py-2 px-4 rounded-lg transition duration-200 text-sm">
                                    <i class="fas fa-plus mr-1"></i>Add Field
                                </button>
                            </div>
                            <div class="fields-list space-y-4">
                                @foreach($section->fields as $fieldIndex => $field)
                                <div class="field-item bg-gray-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Field Name</label>
                                            <input type="text" name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][field_name]"
                                                class="block w-full text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="Field Name"
                                                value="{{ old('sections.'.$sectionIndex.'.fields.'.$fieldIndex.'.field_name', $field->field_name) }}" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Display Label</label>
                                            <input type="text" name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][field_label]"
                                                class="block w-full text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="Display Label"
                                                value="{{ old('sections.'.$sectionIndex.'.fields.'.$fieldIndex.'.field_label', $field->field_label) }}" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Value</label>
                                            <input type="text" name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][field_value]"
                                                class="block w-full text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="Value"
                                                value="{{ old('sections.'.$sectionIndex.'.fields.'.$fieldIndex.'.field_value', $field->field_value) }}">
                                        </div>
                                        <div class="flex justify-end">
                                            <button type="button" class="remove-field-btn bg-red-100 hover:bg-red-200 text-red-700 font-medium py-2 px-3 rounded-lg transition duration-200 text-sm">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Unit</label>
                                            <input type="text" name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][unit]"
                                                class="block w-full text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="Unit (e.g., mg/dL, %)"
                                                value="{{ old('sections.'.$sectionIndex.'.fields.'.$fieldIndex.'.unit', $field->unit) }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Normal Range</label>
                                            <input type="text" name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][normal_range]"
                                                class="block w-full text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="Normal Range"
                                                value="{{ old('sections.'.$sectionIndex.'.fields.'.$fieldIndex.'.normal_range', $field->normal_range) }}">
                                        </div>
                                        <div class="flex items-center">
                                            <div class="flex items-center h-full">
                                                <input type="checkbox" name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][is_abnormal]"
                                                    class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500 focus:ring-2"
                                                    value="1" {{ old('sections.'.$sectionIndex.'.fields.'.$fieldIndex.'.is_abnormal', $field->is_abnormal) ? 'checked' : '' }}>
                                                <label class="ml-2 text-xs font-medium text-gray-700">Abnormal</label>
                                            </div>
                                        </div>
                                        <div>
                                            <!-- Empty column for spacing -->
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Additional Notes -->
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-sticky-note text-purple-600"></i>
                    </div>
                    Additional Notes
                </h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Technical Notes -->
                    <div>
                        <label for="technical_notes" class="block text-sm font-medium text-gray-700 mb-2">Technical Notes</label>
                        <textarea name="technical_notes" id="technical_notes" rows="5"
                            placeholder="Technical observations, methodology notes..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('technical_notes') border-red-300 @enderror">{{ old('technical_notes', $labReport->technical_notes) }}</textarea>
                        @error('technical_notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Doctor Comments -->
                    <div>
                        <label for="doctor_comments" class="block text-sm font-medium text-gray-700 mb-2">Doctor Comments</label>
                        <textarea name="doctor_comments" id="doctor_comments" rows="5"
                            placeholder="Clinical interpretation, recommendations..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('doctor_comments') border-red-300 @enderror">{{ old('doctor_comments', $labReport->doctor_comments) }}</textarea>
                        @error('doctor_comments')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                <div class="flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Fields marked with <span class="text-red-500">*</span> are required
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 w-full sm:w-auto">
                        <a href="{{ route('admin.lab-reports.show', $labReport) }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition duration-200">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="submit" name="action" value="save_draft" id="saveDraftBtn"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition duration-200">
                            <i class="fas fa-save mr-2"></i><span id="draftText">Save as Draft</span>
                        </button>
                        <button type="submit" name="action" value="save_complete" id="saveCompleteBtn"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200">
                            <i class="fas fa-check mr-2"></i><span id="completeText">Save & Complete</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Section Template (Hidden) -->
<div id="section_template" class="hidden">
    <div class="section-item bg-white border border-gray-200 rounded-lg p-6 mb-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h6 class="text-lg font-medium text-gray-900 mb-2 sm:mb-0">
                Section <span class="section-number bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium"></span>
            </h6>
            <button type="button" class="remove-section-btn bg-red-100 hover:bg-red-200 text-red-700 font-medium py-2 px-4 rounded-lg transition duration-200 text-sm">
                <i class="fas fa-trash mr-1"></i>Remove
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Section Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="sections[INDEX][section_name]"
                    class="section-name w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Section Description</label>
                <input type="text" name="sections[INDEX][section_description]"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
            </div>
        </div>

        <div class="fields-container">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2 sm:mb-0">Fields</label>
                <button type="button" class="add-field-btn bg-green-100 hover:bg-green-200 text-green-700 font-medium py-2 px-4 rounded-lg transition duration-200 text-sm">
                    <i class="fas fa-plus mr-1"></i>Add Field
                </button>
            </div>
            <div class="fields-list space-y-4"></div>
        </div>
    </div>
</div>

<!-- Field Template (Hidden) -->
<div id="field_template" class="hidden">
    <div class="field-item bg-gray-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Field Name</label>
                <input type="text" name="sections[SECTION_INDEX][fields][FIELD_INDEX][field_name]"
                    class="block w-full text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="Field Name" required>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Display Label</label>
                <input type="text" name="sections[SECTION_INDEX][fields][FIELD_INDEX][field_label]"
                    class="block w-full text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="Display Label" required>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Value</label>
                <input type="text" name="sections[SECTION_INDEX][fields][FIELD_INDEX][field_value]"
                    class="block w-full text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="Value">
            </div>
            <div class="flex justify-end">
                <button type="button" class="remove-field-btn bg-red-100 hover:bg-red-200 text-red-700 font-medium py-2 px-3 rounded-lg transition duration-200 text-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Unit</label>
                <input type="text" name="sections[SECTION_INDEX][fields][FIELD_INDEX][unit]"
                    class="block w-full text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="Unit (e.g., mg/dL, %)">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Normal Range</label>
                <input type="text" name="sections[SECTION_INDEX][fields][FIELD_INDEX][normal_range]"
                    class="block w-full text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="Normal Range">
            </div>
            <div class="flex items-center">
                <div class="flex items-center h-full">
                    <input type="checkbox" name="sections[SECTION_INDEX][fields][FIELD_INDEX][is_abnormal]"
                        class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500 focus:ring-2" value="1">
                    <label class="ml-2 text-xs font-medium text-gray-700">Abnormal</label>
                </div>
            </div>
            <div>
                <!-- Empty column for spacing -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        let sectionIndex = {
            {
                count($labReport - > sections)
            }
        };

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

        // Add field functionality for existing sections
        $('.add-field-btn').on('click', function() {
            const sectionElement = $(this).closest('.section-item');
            const fieldsContainer = sectionElement.find('.fields-list');
            const sectionIdx = sectionsContainer.find('.section-item').index(sectionElement);
            addField(fieldsContainer, sectionIdx);
        });

        // Remove field functionality for existing fields
        $('.remove-field-btn').on('click', function() {
            $(this).closest('.field-item').remove();
        });

        // Remove section functionality for existing sections
        $('.remove-section-btn').on('click', function() {
            $(this).closest('.section-item').remove();
            updateSectionNumbers();
        });

        // Form submission handling
        const reportForm = $('#reportForm');
        const saveDraftBtn = $('#saveDraftBtn');
        const saveCompleteBtn = $('#saveCompleteBtn');
        const draftText = $('#draftText');
        const completeText = $('#completeText');

        reportForm.on('submit', function(e) {
            const submitter = $(e.originalEvent.submitter);
            const action = submitter.val();

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
                $(this).removeClass('border-red-300').addClass('border-green-300');
            } else {
                $(this).removeClass('border-green-300').addClass('border-red-300');
            }
        });

        // Auto-hide success/error messages
        const alerts = $('.bg-green-100, .bg-red-100, .bg-yellow-50');
        alerts.each(function() {
            const alert = $(this);
            setTimeout(function() {
                alert.fadeOut(300, function() {
                    alert.remove();
                });
            }, 5000);
        });

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
    });
</script>
@endpush

@push('styles')
<style>
    /* Enhanced focus states */
    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Error states */
    .border-red-300:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    /* Success states */
    .border-green-300 {
        border-color: #10b981;
    }

    .border-green-300:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    /* Button hover effects */
    button:hover {
        transform: translateY(-1px);
    }

    button:active {
        transform: translateY(0);
    }

    /* Card hover effects */
    .section-item:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    /* Field item styling */
    .field-item {
        transition: all 0.2s ease;
    }

    .field-item:hover {
        background-color: #f8fafc;
    }

    /* Loading animation */
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .fa-spin {
        animation: spin 1s linear infinite;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .grid {
            grid-template-columns: 1fr;
        }

        .lg\:grid-cols-4 {
            grid-template-columns: 1fr;
        }

        .sm\:grid-cols-2 {
            grid-template-columns: 1fr;
        }

        .field-item .grid {
            gap: 0.75rem;
        }

        .section-item {
            padding: 1rem;
        }
    }

    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }

        .section-item {
            break-inside: avoid;
            page-break-inside: avoid;
        }
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

    /* High contrast mode support */
    @media (prefers-contrast: high) {
        .border-gray-300 {
            border-color: #000;
            border-width: 2px;
        }

        .text-gray-500 {
            color: #000;
        }

        .bg-blue-600 {
            background-color: #000;
        }
    }

    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {

        .section-item,
        button,
        .field-item {
            animation: none;
            transition: none;
        }
    }
</style>
@endpush