@extends('admin.layouts.app')

@section('page-title', 'Edit Report Template')
@section('page-description', 'Edit lab report template')

@push('styles')
<style>
    .section-item {
        transition: all 0.3s ease;
    }

    .section-item:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .field-item {
        transition: all 0.3s ease;
    }

    .field-item:hover {
        border-color: #6366f1;
    }

    .remove-section-btn:hover,
    .remove-field-btn:hover {
        transform: scale(1.1);
    }

    .btn-hover:hover {
        transform: translateY(-1px);
    }

    @media (max-width: 640px) {
        .mobile-stack>* {
            width: 100% !important;
            margin-bottom: 0.5rem;
        }

        .mobile-full {
            width: 100% !important;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-4 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Edit Report Template</h1>
                    <p class="mt-1 text-sm sm:text-base text-gray-600">Modify the lab report template</p>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('admin.lab-reports.templates.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 btn-hover w-full sm:w-auto justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Templates
                    </a>
                </div>
            </div>
        </div>

        <form id="templateForm" method="POST" action="{{ route('admin.lab-reports.templates.update', $template->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 sm:px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Basic Information</h2>
                </div>
                <div class="p-4 sm:p-6 space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="template_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Template Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('template_name') border-red-500 ring-2 ring-red-200 @enderror"
                                id="template_name"
                                name="template_name"
                                value="{{ old('template_name', $template->template_name) }}"
                                placeholder="Enter template name"
                                required>
                            @error('template_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="lab_test_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                Associated Lab Test
                            </label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('lab_test_id') border-red-500 ring-2 ring-red-200 @enderror"
                                id="lab_test_id"
                                name="lab_test_id">
                                <option value="">Select Lab Test (Optional)</option>
                                @foreach($labTests as $test)
                                <option value="{{ $test->id }}" {{ old('lab_test_id', $template->lab_test_id) == $test->id ? 'selected' : '' }}>
                                    {{ $test->test_name }}
                                </option>
                                @endforeach
                            </select>
                            @error('lab_test_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('description') border-red-500 ring-2 ring-red-200 @enderror"
                            id="description"
                            name="description"
                            rows="3"
                            placeholder="Enter template description">{{ old('description', $template->description) }}</textarea>
                        @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-center">
                            <input class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                type="checkbox"
                                id="is_active"
                                name="is_active"
                                value="1"
                                {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                            <label class="ml-2 text-sm font-medium text-gray-700" for="is_active">
                                Active Template
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                type="checkbox"
                                id="is_default"
                                name="is_default"
                                value="1"
                                {{ old('is_default', $template->is_default) ? 'checked' : '' }}>
                            <label class="ml-2 text-sm font-medium text-gray-700" for="is_default">
                                Set as Default Template
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Template Sections -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-4 sm:px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Template Sections</h2>
                        <button type="button"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 btn-hover w-full sm:w-auto justify-center"
                            id="addSectionBtn">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Section
                        </button>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <div id="sections_container" class="space-y-4">
                        <!-- Existing sections will be loaded here -->
                        @foreach($template->sections as $sectionIndex => $section)
                        <div class="section-item bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-4 sm:p-6 space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Section <span class="section-number">{{ $sectionIndex + 1 }}</span>
                                </h3>
                                <button type="button"
                                    class="inline-flex items-center justify-center w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-full transition-colors duration-200 remove-section-btn flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Section Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 section-name"
                                        name="sections[{{ $sectionIndex }}][section_name]"
                                        value="{{ old('sections.'.$sectionIndex.'.section_name', $section->section_name) }}"
                                        placeholder="Enter section name"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Section Description
                                    </label>
                                    <input type="text"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                        name="sections[{{ $sectionIndex }}][section_description]"
                                        value="{{ old('sections.'.$sectionIndex.'.section_description', $section->section_description) }}"
                                        placeholder="Enter section description">
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center">
                                    <input class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                        type="checkbox"
                                        name="sections[{{ $sectionIndex }}][is_required]"
                                        value="1"
                                        {{ old('sections.'.$sectionIndex.'.is_required', $section->is_required) ? 'checked' : '' }}>
                                    <label class="ml-2 text-sm font-medium text-gray-700">
                                        Required Section
                                    </label>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg border border-gray-200 p-4">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                                    <label class="text-sm font-semibold text-gray-700">Fields</label>
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-colors duration-200 add-field-btn btn-hover w-full sm:w-auto justify-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Field
                                    </button>
                                </div>
                                <div class="fields-list space-y-3">
                                    <!-- Existing fields -->
                                    @foreach($section->fields as $fieldIndex => $field)
                                    <div class="field-item bg-white border-2 border-gray-200 rounded-lg p-4 space-y-4">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                            <span class="text-sm font-semibold text-gray-600 bg-gray-100 px-2 py-1 rounded">Field Configuration</span>
                                            <button type="button"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-full transition-colors duration-200 remove-field-btn flex-shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                    Field Name <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                                    name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][field_name]"
                                                    value="{{ old('sections.'.$sectionIndex.'.fields.'.$fieldIndex.'.field_name', $field->field_name) }}"
                                                    placeholder="e.g., hemoglobin_level"
                                                    required>
                                                <p class="mt-1 text-xs text-gray-500">Used for database storage (no spaces)</p>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                    Field Label <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                                    name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][field_label]"
                                                    value="{{ old('sections.'.$sectionIndex.'.fields.'.$fieldIndex.'.field_label', $field->field_label) }}"
                                                    placeholder="e.g., Hemoglobin Level"
                                                    required>
                                                <p class="mt-1 text-xs text-gray-500">Display name for users</p>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                    Field Type <span class="text-red-500">*</span>
                                                </label>
                                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 field-type-select"
                                                    name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][field_type]"
                                                    required>
                                                    <option value="">Select Type</option>
                                                    <option value="text" {{ old('sections.'.$sectionIndex.'.fields.'.$fieldIndex.'.field_type', $field->field_type) == 'text' ? 'selected' : '' }}>Text</option>
                                                    <option value="number" {{ old('sections.'.$sectionIndex.'.fields.'.$fieldIndex.'.field_type', $field->field_type) == 'number' ? 'selected' : '' }}>Number</option>
                                                    <option value="select" {{ old('sections.'.$sectionIndex.'.fields.'.$fieldIndex.'.field_type', $field->field_type) == 'select' ? 'selected' : '' }}>Select Dropdown</option>
                                                    <option value="textarea" {{ old('sections.'.$sectionIndex.'.fields.'.$fieldIndex.'.field_type', $field->field_type) == 'textarea' ? 'selected' : '' }}>Textarea</option>
                                                    <option value="date" {{ old('sections.'.$sectionIndex.'.fields.'.$fieldIndex.'.field_type', $field->field_type) == 'date' ? 'selected' : '' }}>Date</option>
                                                    <option value="time" {{ old('sections.'.$sectionIndex.'.fields.'.$fieldIndex.'.field_type', $field->field_type) == 'time' ? 'selected' : '' }}>Time</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Unit</label>
                                                <input type="text"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                                    name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][unit]"
                                                    value="{{ old('sections.'.$sectionIndex.'.fields.'.$fieldIndex.'.unit', $field->unit) }}"
                                                    placeholder="e.g., mg/dL, %">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Normal Range</label>
                                                <input type="text"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                                    name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][normal_range]"
                                                    value="{{ old('sections.'.$sectionIndex.'.fields.'.$fieldIndex.'.normal_range', $field->normal_range) }}"
                                                    placeholder="e.g., 12-16 g/dL">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Default Value</label>
                                                <input type="text"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                                    name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][default_value]"
                                                    value="{{ old('sections.'.$sectionIndex.'.fields.'.$fieldIndex.'.default_value', $field->default_value) }}"
                                                    placeholder="Default value">
                                            </div>
                                            <div class="flex items-center justify-center">
                                                <div class="flex items-center mt-6">
                                                    <input class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                                        type="checkbox"
                                                        name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][is_required]"
                                                        value="1"
                                                        {{ old('sections.'.$sectionIndex.'.fields.'.$fieldIndex.'.is_required', $field->is_required) ? 'checked' : '' }}>
                                                    <label class="ml-2 text-sm font-medium text-gray-700">
                                                        Required Field
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="field-options-container {{ $field->field_type == 'select' ? '' : 'hidden' }}">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                    Options <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 field-options-input"
                                                    name="sections[{{ $sectionIndex }}][fields][{{ $fieldIndex }}][field_options]"
                                                    value="{{ old('sections.'.$sectionIndex.'.fields.'.$fieldIndex.'.field_options', $field->field_options) }}"
                                                    placeholder="Enter options separated by commas (e.g., Normal,Abnormal,Critical)"
                                                    {{ $field->field_type == 'select' ? 'required' : '' }}>
                                                <p class="mt-1 text-xs text-gray-500">Separate multiple options with commas</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @if($section->fields->count() == 0)
                                <div class="no-fields-message text-center py-6 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
                                    <div class="mx-auto w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 text-sm">Click "Add Field" to add fields to this section</p>
                                </div>
                                @else
                                <div class="no-fields-message text-center py-6 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50" style="display: none;">
                                    <div class="mx-auto w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 text-sm">Click "Add Field" to add fields to this section</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($template->sections->count() == 0)
                    <div id="no-sections-message" class="text-center py-8 sm:py-12">
                        <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-gray-500 text-sm sm:text-base">Click "Add Section" to start building your template</p>
                    </div>
                    @else
                    <div id="no-sections-message" class="text-center py-8 sm:py-12 hidden">
                        <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-gray-500 text-sm sm:text-base">Click "Add Section" to start building your template</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row gap-3 sm:justify-end mobile-stack">
                    <a href="{{ route('admin.lab-reports.templates.index') }}"
                        class="inline-flex items-center justify-center px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 btn-hover">
                        Cancel
                    </a>
                    <button type="submit"
                        name="action"
                        value="save_draft"
                        class="inline-flex items-center justify-center px-6 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 btn-hover">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Update as Draft
                    </button>
                    <button type="submit"
                        name="action"
                        value="save_active"
                        class="inline-flex items-center justify-center px-6 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 btn-hover">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update & Activate
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Section Template -->
<div id="section_template" class="hidden">
    <div class="section-item bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-4 sm:p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-lg font-semibold text-gray-900">
                Section <span class="section-number">1</span>
            </h3>
            <button type="button"
                class="inline-flex items-center justify-center w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-full transition-colors duration-200 remove-section-btn flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Section Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 section-name"
                    name="sections[INDEX][section_name]"
                    placeholder="Enter section name"
                    required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Section Description
                </label>
                <input type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                    name="sections[INDEX][section_description]"
                    placeholder="Enter section description">
            </div>
        </div>

        <div>
            <div class="flex items-center">
                <input class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                    type="checkbox"
                    name="sections[INDEX][is_required]"
                    value="1"
                    checked>
                <label class="ml-2 text-sm font-medium text-gray-700">
                    Required Section
                </label>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <label class="text-sm font-semibold text-gray-700">Fields</label>
                <button type="button"
                    class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-colors duration-200 add-field-btn btn-hover w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Field
                </button>
            </div>
            <div class="fields-list space-y-3">
                <!-- Fields will be added here -->
            </div>
            <div class="no-fields-message text-center py-6 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
                <div class="mx-auto w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-gray-500 text-sm">Click "Add Field" to add fields to this section</p>
            </div>
        </div>
    </div>
</div>

<!-- Field Template -->
<div id="field_template" class="hidden">
    <div class="field-item bg-white border-2 border-gray-200 rounded-lg p-4 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <span class="text-sm font-semibold text-gray-600 bg-gray-100 px-2 py-1 rounded">Field Configuration</span>
            <button type="button"
                class="inline-flex items-center justify-center w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-full transition-colors duration-200 remove-field-btn flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Field Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                    name="sections[SECTION_INDEX][fields][FIELD_INDEX][field_name]"
                    placeholder="e.g., hemoglobin_level"
                    required>
                <p class="mt-1 text-xs text-gray-500">Used for database storage (no spaces)</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Field Label <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                    name="sections[SECTION_INDEX][fields][FIELD_INDEX][field_label]"
                    placeholder="e.g., Hemoglobin Level"
                    required>
                <p class="mt-1 text-xs text-gray-500">Display name for users</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Field Type <span class="text-red-500">*</span>
                </label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 field-type-select"
                    name="sections[SECTION_INDEX][fields][FIELD_INDEX][field_type]"
                    required>
                    <option value="">Select Type</option>
                    <option value="text">Text</option>
                    <option value="number">Number</option>
                    <option value="select">Select Dropdown</option>
                    <option value="textarea">Textarea</option>
                    <option value="date">Date</option>
                    <option value="time">Time</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Unit</label>
                <input type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                    name="sections[SECTION_INDEX][fields][FIELD_INDEX][unit]"
                    placeholder="e.g., mg/dL, %">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Normal Range</label>
                <input type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                    name="sections[SECTION_INDEX][fields][FIELD_INDEX][normal_range]"
                    placeholder="e.g., 12-16 g/dL">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Default Value</label>
                <input type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                    name="sections[SECTION_INDEX][fields][FIELD_INDEX][default_value]"
                    placeholder="Default value">
            </div>
            <div class="flex items-center justify-center">
                <div class="flex items-center mt-6">
                    <input class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                        type="checkbox"
                        name="sections[SECTION_INDEX][fields][FIELD_INDEX][is_required]"
                        value="1">
                    <label class="ml-2 text-sm font-medium text-gray-700">
                        Required Field
                    </label>
                </div>
            </div>
        </div>

        <div class="field-options-container hidden">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Options <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 field-options-input"
                    name="sections[SECTION_INDEX][fields][FIELD_INDEX][field_options]"
                    placeholder="Enter options separated by commas (e.g., Normal,Abnormal,Critical)">
                <p class="mt-1 text-xs text-gray-500">Separate multiple options with commas</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    var sectionIndex = {
        {
            $template - > sections - > count()
        }
    };

    $(document).ready(function() {
        // Add section functionality
        $('#addSectionBtn').click(function() {
            addSection();
        });

        // Remove section (delegated)
        $(document).on('click', '.remove-section-btn', function() {
            if (confirm('Are you sure you want to remove this section? All fields in this section will also be removed.')) {
                $(this).closest('.section-item').remove();
                updateSectionNumbers();
                toggleNoSectionsMessage();
            }
        });

        // Add field (delegated)
        $(document).on('click', '.add-field-btn', function() {
            var section = $(this).closest('.section-item');
            var fieldsContainer = section.find('.fields-list');
            var sectionIdx = $('#sections_container .section-item').index(section);
            addField(fieldsContainer, sectionIdx);
            section.find('.no-fields-message').hide();
        });

        // Remove field (delegated)
        $(document).on('click', '.remove-field-btn', function() {
            if (confirm('Are you sure you want to remove this field?')) {
                var section = $(this).closest('.section-item');
                $(this).closest('.field-item').remove();

                // Show no fields message if no fields left
                if (section.find('.field-item').length === 0) {
                    section.find('.no-fields-message').show();
                }
            }
        });

        // Handle field type changes (delegated)
        $(document).on('change', '.field-type-select', function() {
            var fieldType = $(this).val();
            var fieldItem = $(this).closest('.field-item');
            var optionsContainer = fieldItem.find('.field-options-container');
            var optionsInput = fieldItem.find('.field-options-input');

            if (fieldType === 'select') {
                optionsContainer.removeClass('hidden');
                optionsInput.attr('required', true);
            } else {
                optionsContainer.addClass('hidden');
                optionsInput.attr('required', false);
            }
        });

        // Form validation
        $('#templateForm').submit(function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }

            var action = $(document.activeElement).val();
            if (action === 'save_active') {
                $('#is_active').prop('checked', true);
            }
        });

        // Initialize page
        toggleNoSectionsMessage();
    });

    function addSection() {
        var template = $('#section_template').html();
        var newSection = $(template);

        // Update section number
        newSection.find('.section-number').text(sectionIndex + 1);

        // Update input names
        newSection.find('input, textarea, select').each(function() {
            var name = $(this).attr('name');
            if (name) {
                $(this).attr('name', name.replace('INDEX', sectionIndex));
            }
        });

        $('#sections_container').append(newSection);
        sectionIndex++;
        toggleNoSectionsMessage();
    }

    function addField(fieldsContainer, sectionIdx) {
        var template = $('#field_template').html();
        var newField = $(template);
        var fieldIdx = fieldsContainer.children('.field-item').length;

        // Update input names
        newField.find('input, textarea, select').each(function() {
            var name = $(this).attr('name');
            if (name) {
                name = name.replace('SECTION_INDEX', sectionIdx);
                name = name.replace('FIELD_INDEX', fieldIdx);
                $(this).attr('name', name);
            }
        });

        fieldsContainer.append(newField);
    }

    function updateSectionNumbers() {
        $('#sections_container .section-item').each(function(index) {
            $(this).find('.section-number').text(index + 1);

            // Update section indexes in input names
            $(this).find('input, textarea, select').each(function() {
                var name = $(this).attr('name');
                if (name && name.includes('sections[')) {
                    var newName = name.replace(/sections\[\d+\]/, 'sections[' + index + ']');
                    $(this).attr('name', newName);
                }
            });

            // Update field indexes
            $(this).find('.field-item').each(function(fieldIndex) {
                $(this).find('input, textarea, select').each(function() {
                    var name = $(this).attr('name');
                    if (name && name.includes('fields[')) {
                        var newName = name.replace(/fields\[\d+\]/, 'fields[' + fieldIndex + ']');
                        $(this).attr('name', newName);
                    }
                });
            });
        });

        sectionIndex = $('#sections_container .section-item').length;
    }

    function toggleNoSectionsMessage() {
        var sectionsCount = $('#sections_container .section-item').length;
        if (sectionsCount === 0) {
            $('#no-sections-message').removeClass('hidden');
        } else {
            $('#no-sections-message').addClass('hidden');
        }
    }

    function validateForm() {
        var templateName = $('#template_name').val().trim();
        if (!templateName) {
            showAlert('Please enter a template name.', 'error');
            $('#template_name').focus();
            return false;
        }

        var sections = $('#sections_container .section-item');

        if (sections.length === 0) {
            showAlert('Please add at least one section to the template.', 'error');
            $('#addSectionBtn').focus();
            return false;
        }

        var hasEmptySection = false;
        var hasEmptyField = false;

        sections.each(function(sectionIndex) {
            var sectionName = $(this).find('.section-name').val().trim();
            if (!sectionName) {
                showAlert('Please provide a name for section ' + (sectionIndex + 1) + '.', 'error');
                $(this).find('.section-name').focus();
                hasEmptySection = true;
                return false;
            }

            var fields = $(this).find('.field-item');
            if (fields.length === 0) {
                showAlert('Section "' + sectionName + '" must have at least one field.', 'error');
                $(this).find('.add-field-btn').focus();
                hasEmptySection = true;
                return false;
            }

            // Validate each field
            fields.each(function(fieldIndex) {
                var fieldName = $(this).find('input[name*="[field_name]"]').val().trim();
                var fieldLabel = $(this).find('input[name*="[field_label]"]').val().trim();
                var fieldType = $(this).find('select[name*="[field_type]"]').val();

                if (!fieldName) {
                    showAlert('Please provide a field name for field ' + (fieldIndex + 1) + ' in section "' + sectionName + '".', 'error');
                    $(this).find('input[name*="[field_name]"]').focus();
                    hasEmptyField = true;
                    return false;
                }

                if (!fieldLabel) {
                    showAlert('Please provide a field label for field ' + (fieldIndex + 1) + ' in section "' + sectionName + '".', 'error');
                    $(this).find('input[name*="[field_label]"]').focus();
                    hasEmptyField = true;
                    return false;
                }

                if (!fieldType) {
                    showAlert('Please select a field type for field ' + (fieldIndex + 1) + ' in section "' + sectionName + '".', 'error');
                    $(this).find('select[name*="[field_type]"]').focus();
                    hasEmptyField = true;
                    return false;
                }

                // Validate field name format (no spaces, special characters)
                if (!/^[a-zA-Z_][a-zA-Z0-9_]*$/.test(fieldName)) {
                    showAlert('Field name "' + fieldName + '" is invalid. Use only letters, numbers, and underscores. Must start with a letter or underscore.', 'error');
                    $(this).find('input[name*="[field_name]"]').focus();
                    hasEmptyField = true;
                    return false;
                }

                // Check if select type has options
                if (fieldType === 'select') {
                    var options = $(this).find('input[name*="[field_options]"]').val().trim();
                    if (!options) {
                        showAlert('Please provide options for the select field "' + fieldLabel + '" in section "' + sectionName + '".', 'error');
                        $(this).find('input[name*="[field_options]"]').focus();
                        hasEmptyField = true;
                        return false;
                    }
                }
            });

            if (hasEmptyField) return false;
        });

        return !hasEmptySection && !hasEmptyField;
    }

    // Enhanced alert function for better mobile UX
    function showAlert(message, type = 'info') {
        // Remove existing alerts
        $('.custom-alert').remove();

        var alertClass = type === 'error' ? 'bg-red-100 border-red-500 text-red-700' : 'bg-blue-100 border-blue-500 text-blue-700';
        var iconSvg = type === 'error' ?
            '<svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>' :
            '<svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>';

        var alertHtml = `
           <div class="custom-alert fixed top-4 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 sm:w-96 z-50 border-l-4 p-4 rounded-lg shadow-lg ${alertClass}" style="animation: slideDown 0.3s ease-out;">
               <div class="flex items-center">
                   ${iconSvg}
                   <p class="text-sm font-medium">${message}</p>
                   <button class="ml-auto pl-3" onclick="$(this).closest('.custom-alert').remove()">
                       <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                           <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                       </svg>
                   </button>
               </div>
           </div>
       `;

        $('body').append(alertHtml);

        // Auto remove after 5 seconds
        setTimeout(function() {
            $('.custom-alert').fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }

    // Auto-generate field name from field label
    $(document).on('input', 'input[name*="[field_label]"]', function() {
        var fieldLabel = $(this).val();
        var fieldNameInput = $(this).closest('.field-item').find('input[name*="[field_name]"]');

        // Only auto-generate if field name is empty
        if (fieldNameInput.val().trim() === '') {
            var fieldName = fieldLabel.toLowerCase()
                .replace(/[^a-zA-Z0-9\s]/g, '') // Remove special characters
                .replace(/\s+/g, '_') // Replace spaces with underscores
                .replace(/^_+|_+$/g, ''); // Remove leading/trailing underscores

            fieldNameInput.val(fieldName);
        }
    });

    // Prevent form submission on Enter key in input fields
    $(document).on('keypress', 'input', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            return false;
        }
    });

    // Mobile-friendly scroll to element
    function scrollToElement(element) {
        if ($(window).width() < 768) {
            $('html, body').animate({
                scrollTop: $(element).offset().top - 100
            }, 500);
        }
    }

    // Add loading states for better UX
    $('#templateForm').on('submit', function() {
        var submitBtn = $(document.activeElement);
        var originalText = submitBtn.html();

        submitBtn.prop('disabled', true).html(`
           <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
               <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
               <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
           </svg>
           Processing...
       `);

        // Re-enable button after 10 seconds as fallback
        setTimeout(function() {
            submitBtn.prop('disabled', false).html(originalText);
        }, 10000);
    });

    // Add touch-friendly interactions for mobile
    if ('ontouchstart' in window) {
        $(document).on('touchstart', '.btn-hover', function() {
            $(this).addClass('scale-95');
        }).on('touchend', '.btn-hover', function() {
            $(this).removeClass('scale-95');
        });
    }
</script>

<style>
    @keyframes slideDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .scale-95 {
        transform: scale(0.95);
    }

    /* Custom scrollbar for webkit browsers */
    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Focus styles for better accessibility */
    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Smooth transitions for all interactive elements */
    button,
    input,
    select,
    textarea {
        transition: all 0.2s ease-in-out;
    }
</style>
@endpush

@endsection