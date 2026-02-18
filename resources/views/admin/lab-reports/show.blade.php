@extends('admin.layouts.app')

@section('title', 'Lab Report - ' . $labReport->report_number)

@section('content')
<div class="min-h-screen bg-gray-50 py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-4 py-5 sm:px-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="mb-4 sm:mb-0">
                        <h1 class="text-2xl font-bold text-gray-900">Lab Report Details</h1>
                        <div class="mt-1 flex flex-col sm:flex-row sm:items-center sm:space-x-4">
                            <p class="text-sm text-gray-600">Report #{{ $labReport->report_number }}</p>
                            <span class="hidden sm:block text-gray-300">•</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $labReport->status === 'verified' ? 'green' : ($labReport->status === 'completed' ? 'blue' : 'yellow') }}-100 text-{{ $labReport->status === 'verified' ? 'green' : ($labReport->status === 'completed' ? 'blue' : 'yellow') }}-800">
                                {{ ucfirst($labReport->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                        <!-- Print Options Dropdown -->
                        <div class="relative inline-block text-left">
                            <div>
                                <button type="button" class="inline-flex items-center justify-center w-full px-4 py-2 border border-blue-300 rounded-md shadow-sm text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="print-menu-button" aria-expanded="true" aria-haspopup="true" onclick="togglePrintMenu()">
                                    <i class="fas fa-print mr-2"></i>Print Report
                                    <i class="fas fa-chevron-down ml-2 -mr-1 h-4 w-4"></i>
                                </button>
                            </div>

                            <div class="print-dropdown origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 hidden" id="print-menu" role="menu" aria-orientation="vertical" aria-labelledby="print-menu-button" tabindex="-1">
                                <div class="py-1" role="none">
                                    <a href="{{ route('admin.lab-reports.print', $labReport) }}" target="_blank" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1">
                                        <i class="fas fa-file-alt mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500"></i>
                                        Standard Report
                                        <span class="ml-auto text-xs text-gray-500">Simple</span>
                                    </a>
                                    <a href="{{ route('admin.lab-reports.print-with-letterhead', $labReport) }}" target="_blank" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1">
                                        <i class="fas fa-file-medical mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500"></i>
                                        With Letterhead
                                        <span class="ml-auto text-xs text-gray-500">Professional</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @if($labReport->status !== 'verified')
                        <a href="{{ route('admin.lab-reports.edit', $labReport) }}" class="inline-flex items-center justify-center px-4 py-2 border border-amber-300 rounded-md shadow-sm text-sm font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                            <i class="fas fa-edit mr-2"></i>
                            <span class="hidden sm:inline">Edit</span>
                        </a>
                        @endif

                        <button onclick="shareReport()" class="inline-flex items-center justify-center px-4 py-2 border border-green-300 rounded-md shadow-sm text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-share mr-2"></i>
                            <span class="hidden sm:inline">Share</span>
                        </button>

                        <a href="{{ route('admin.lab-reports.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <i class="fas fa-arrow-left mr-2"></i>
                            <span class="hidden sm:inline">Back</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Update Alert -->
        @if($labReport->status !== 'verified')
        <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6 rounded-r-lg">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center mb-3 sm:mb-0">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-amber-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-amber-700">
                            This report is currently <strong>{{ ucfirst($labReport->status) }}</strong>
                        </p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    @if($labReport->status === 'draft')
                    <button type="button" class="status-update-btn inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        data-report-id="{{ $labReport->id }}"
                        data-status="completed">
                        Mark as Completed
                    </button>
                    @elseif($labReport->status === 'completed')
                    <button type="button" class="status-update-btn inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        data-report-id="{{ $labReport->id }}"
                        data-status="verified">
                        Mark as Verified
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Patient Information Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-5 sm:px-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                            <i class="fas fa-user-circle text-blue-600 mr-2"></i>
                            Patient Information
                        </h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:space-x-6">
                            <!-- Patient Avatar -->
                            <div class="flex-shrink-0 mb-4 sm:mb-0">
                                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                                    {{ strtoupper(substr($labReport->patient->first_name ?? $labReport->patient->name ?? 'P', 0, 1) . substr($labReport->patient->last_name ?? '', 0, 1)) }}
                                </div>
                            </div>

                            <!-- Patient Details -->
                            <div class="flex-1 min-w-0">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="text-xl font-semibold text-gray-900 mb-1">
                                            {{ $labReport->patient->first_name ?? $labReport->patient->name ?? 'N/A' }}
                                            {{ $labReport->patient->last_name ?? '' }}
                                        </h4>
                                        <p class="text-sm text-gray-600 mb-3">ID: {{ $labReport->patient->patient_id }}</p>

                                        <div class="space-y-2">
                                            <div class="flex items-center text-sm text-gray-600">
                                                <i class="fas fa-phone w-4 h-4 mr-2 text-gray-400"></i>
                                                {{ $labReport->patient->phone ?? 'N/A' }}
                                            </div>
                                            @if($labReport->patient->email)
                                            <div class="flex items-center text-sm text-gray-600">
                                                <i class="fas fa-envelope w-4 h-4 mr-2 text-gray-400"></i>
                                                {{ $labReport->patient->email }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        @if(isset($labReport->patient->age))
                                        <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                                            <span class="text-sm font-medium text-gray-700">Age</span>
                                            <span class="text-sm text-gray-900">{{ $labReport->patient->age }} years</span>
                                        </div>
                                        @endif

                                        @if(isset($labReport->patient->gender))
                                        <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                                            <span class="text-sm font-medium text-gray-700">Gender</span>
                                            <span class="text-sm text-gray-900">{{ ucfirst($labReport->patient->gender) }}</span>
                                        </div>
                                        @endif

                                        @if(isset($labReport->patient->blood_group))
                                        <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                                            <span class="text-sm font-medium text-gray-700">Blood Group</span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ $labReport->patient->blood_group }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Test Information Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-5 sm:px-6 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                            <i class="fas fa-flask text-green-600 mr-2"></i>
                            Test Information
                        </h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Test Name</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $labReport->labTest->test_name }}</dd>
                                </div>

                                @if($labReport->labTest->category)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Category</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $labReport->labTest->category }}
                                        </span>
                                    </dd>
                                </div>
                                @endif
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Report Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $labReport->report_date->format('M d, Y') }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Advised By</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $labReport->advised_by ?? 'SELF' }}</dd>
                                </div>
                            </div>
                        </div>

                        @if($labReport->investigation_details)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Investigation Details</dt>
                            <dd class="text-sm text-gray-900 bg-gray-50 rounded-lg p-3">{{ $labReport->investigation_details }}</dd>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Report Sections -->
                @foreach($labReport->sections as $section)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-5 sm:px-6 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $section->section_name }}</h3>
                        @if($section->section_description)
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $section->section_description }}
                        </p>
                        @endif
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        @if($section->fields->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test Parameter</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Normal Range</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($section->fields as $field)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $field->field_label }}</div>
                                            @if($field->field_name !== $field->field_label)
                                            <div class="text-xs text-gray-500">{{ $field->field_name }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm {{ $field->is_abnormal ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                                {{ $field->field_value ?: '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $field->unit ?: '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $field->normal_range ?: '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($field->is_abnormal)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Abnormal
                                            </span>
                                            @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Normal
                                            </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-8">
                            <i class="fas fa-flask text-gray-300 text-4xl mb-4"></i>
                            <p class="text-gray-500">No test parameters found in this section.</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach

                <!-- Additional Notes -->
                @if($labReport->technical_notes || $labReport->doctor_comments)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-5 sm:px-6 bg-gradient-to-r from-amber-50 to-orange-50 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                            <i class="fas fa-sticky-note text-amber-600 mr-2"></i>
                            Additional Notes
                        </h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <div class="space-y-6">
                            @if($labReport->technical_notes)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Technical Notes</h4>
                                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                                    <p class="text-sm text-blue-800">{{ $labReport->technical_notes }}</p>
                                </div>
                            </div>
                            @endif

                            @if($labReport->doctor_comments)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Doctor's Comments</h4>
                                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
                                    <p class="text-sm text-green-800">{{ $labReport->doctor_comments }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Report Summary Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-5 sm:px-6 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                            <i class="fas fa-chart-line text-indigo-600 mr-2"></i>
                            Report Summary
                        </h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">Total Sections</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $labReport->sections->count() }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">Total Parameters</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $labReport->sections->sum(function($section) { return $section->fields->count(); }) }}
                                </span>
                            </div>

                            @php
                            $abnormalCount = $labReport->sections->sum(function($section) {
                            return $section->fields->where('is_abnormal', true)->count();
                            });
                            @endphp

                            <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">Abnormal Results</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $abnormalCount > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $abnormalCount }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">Report Status</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $labReport->status === 'verified' ? 'green' : ($labReport->status === 'completed' ? 'blue' : 'yellow') }}-100 text-{{ $labReport->status === 'verified' ? 'green' : ($labReport->status === 'completed' ? 'blue' : 'yellow') }}-800">
                                    {{ ucfirst($labReport->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Timeline -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-5 sm:px-6 bg-gradient-to-r from-gray-50 to-slate-50 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                            <i class="fas fa-clock text-gray-600 mr-2"></i>
                            Timeline
                        </h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li>
                                    <div class="relative pb-8">
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-plus text-white text-xs"></i>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Report created by <span class="font-medium text-gray-900">{{ $labReport->creator->name ?? 'System' }}</span></p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    <time datetime="{{ $labReport->created_at->toISOString() }}">{{ $labReport->created_at->format('M d, Y g:i A') }}</time>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                @if($labReport->status === 'completed' || $labReport->status === 'verified')
                                <li>
                                    <div class="relative pb-8">
                                        @if($labReport->status === 'verified')
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-check text-white text-xs"></i>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Report completed</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    <time datetime="{{ $labReport->updated_at->toISOString() }}">{{ $labReport->updated_at->format('M d, Y g:i A') }}</time>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                @if($labReport->status === 'verified' && $labReport->verified_at)
                                <li>
                                    <div class="relative">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-certificate text-white text-xs"></i>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Report verified by <span class="font-medium text-gray-900">{{ $labReport->verifier->name ?? 'Doctor' }}</span></p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    <time datetime="{{ $labReport->verified_at->toISOString() }}">{{ $labReport->verified_at->format('M d, Y g:i A') }}</time>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Patient History -->
                @if(isset($patientHistory) && $patientHistory->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-5 sm:px-6 bg-gradient-to-r from-teal-50 to-cyan-50 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                            <i class="fas fa-history text-teal-600 mr-2"></i>
                            Patient History
                        </h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <div class="space-y-3">
                            @foreach($patientHistory->take(5) as $history)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-150">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $history->labTest->test_name ?? 'Unknown Test' }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $history->report_date->format('M d, Y') }}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $history->status === 'verified' ? 'green' : ($history->status === 'completed' ? 'blue' : 'yellow') }}-100 text-{{ $history->status === 'verified' ? 'green' : ($history->status === 'completed' ? 'blue' : 'yellow') }}-800">
                                        {{ ucfirst($history->status) }}
                                    </span>
                                    <a href="{{ route('admin.lab-reports.show', $history) }}" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach

                            @if($patientHistory->count() > 5)
                            <div class="text-center pt-2">
                                <button type="button" class="text-sm text-blue-600 hover:text-blue-800 font-medium" onclick="showMoreHistory()">
                                    View {{ $patientHistory->count() - 5 }} more reports
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-5 sm:px-6 bg-gradient-to-r from-rose-50 to-pink-50 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                            <i class="fas fa-bolt text-rose-600 mr-2"></i>
                            Quick Actions
                        </h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <div class="space-y-3">
                            <a href="{{ route('admin.lab-reports.duplicate', $labReport) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-copy mr-2"></i>
                                Duplicate Report
                            </a>

                            <button type="button" onclick="downloadReport()" class="w-full inline-flex items-center justify-center px-4 py-2 border border-blue-300 rounded-md shadow-sm text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-download mr-2"></i>
                                Download PDF
                            </button>

                            <button type="button" onclick="emailReport()" class="w-full inline-flex items-center justify-center px-4 py-2 border border-green-300 rounded-md shadow-sm text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fas fa-envelope mr-2"></i>
                                Email to Patient
                            </button>

                            <button type="button" onclick="createAppointment()" class="w-full inline-flex items-center justify-center px-4 py-2 border border-purple-300 rounded-md shadow-sm text-sm font-medium text-purple-700 bg-purple-50 hover:bg-purple-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <i class="fas fa-calendar-plus mr-2"></i>
                                Schedule Follow-up
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <i class="fas fa-sync-alt text-blue-600"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Update Report Status</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="statusModalText">
                    Are you sure you want to update this report's status?
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="confirmStatusUpdate" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    Confirm
                </button>
                <button id="cancelStatusUpdate" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-24 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 shadow-xl">
        <div class="flex items-center space-x-3">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="text-gray-700 font-medium">Processing...</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Status update functionality
        const statusButtons = document.querySelectorAll('.status-update-btn');
        const statusModal = document.getElementById('statusModal');
        const statusModalText = document.getElementById('statusModalText');
        const confirmButton = document.getElementById('confirmStatusUpdate');
        const cancelButton = document.getElementById('cancelStatusUpdate');
        const loadingOverlay = document.getElementById('loadingOverlay');

        let currentReportId = null;
        let currentStatus = null;

        statusButtons.forEach(button => {
            button.addEventListener('click', function() {
                currentReportId = this.dataset.reportId;
                currentStatus = this.dataset.status;

                const statusText = currentStatus === 'completed' ? 'completed' : 'verified';
                statusModalText.textContent = `Are you sure you want to mark this report as ${statusText}?`;

                statusModal.classList.remove('hidden');
            });
        });

        confirmButton.addEventListener('click', function() {
            if (currentReportId && currentStatus) {
                updateReportStatus(currentReportId, currentStatus);
            }
        });

        cancelButton.addEventListener('click', function() {
            statusModal.classList.add('hidden');
            currentReportId = null;
            currentStatus = null;
        });

        // Close modal when clicking outside
        statusModal.addEventListener('click', function(e) {
            if (e.target === statusModal) {
                statusModal.classList.add('hidden');
                currentReportId = null;
                currentStatus = null;
            }
        });

        function updateReportStatus(reportId, status) {
            statusModal.classList.add('hidden');
            loadingOverlay.classList.remove('hidden');

            fetch(`/admin/lab-reports/${reportId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    loadingOverlay.classList.add('hidden');
                    if (data.success) {
                        showNotification('Report status updated successfully!', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showNotification('Failed to update report status.', 'error');
                    }
                })
                .catch(error => {
                    loadingOverlay.classList.add('hidden');
                    console.error('Error:', error);
                    showNotification('An error occurred while updating the report status.', 'error');
                });
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}`;
            notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    });

    // Print menu toggle function
    function togglePrintMenu() {
        const menu = document.getElementById('print-menu');
        menu.classList.toggle('hidden');
    }

    // Close print menu when clicking outside
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('print-menu');
        const button = document.getElementById('print-menu-button');

        if (!menu.contains(event.target) && !button.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });

    // Quick action functions
    function shareReport() {
        if (navigator.share) {
            navigator.share({
                title: 'Lab Report - {{ $labReport->report_number }}',
                text: 'Lab Report for {{ $labReport->patient->first_name ?? $labReport->patient->name }} {{ $labReport->patient->last_name ?? "" }}',
                url: window.location.href
            });
        } else {
            // Fallback - copy to clipboard
            navigator.clipboard.writeText(window.location.href).then(() => {
                showNotification('Report link copied to clipboard!', 'success');
            });
        }
    }

    function emailReport() {
        const patientEmail = '{{ $labReport->patient->email ?? "" }}';
        if (patientEmail) {
            const subject = encodeURIComponent('Your Lab Report - {{ $labReport->report_number }}');
            const body = encodeURIComponent('Please find your lab report attached. If you have any questions, please contact us.');
            window.location.href = `mailto:${patientEmail}?subject=${subject}&body=${body}`;
        } else {
            showNotification('Patient email not available.', 'error');
        }
    }

    function createAppointment() {
        window.location.href = '{{ route("admin.appointments.create") }}?patient_id={{ $labReport->patient->patient_id }}';
    }

    function showMoreHistory() {
        // This would typically load more history via AJAX
        showNotification('Loading more history...', 'info');
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${type === 'success' ? 'bg-green-500 text-white' : (type === 'error' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white')}`;
        notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : (type === 'error' ? 'exclamation-circle' : 'info-circle')} mr-2"></i>
            <span>${message}</span>
        </div>
    `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    // Print functionality
    window.addEventListener('beforeprint', function() {
        // Hide elements that shouldn't be printed
        const elementsToHide = document.querySelectorAll('.no-print, button, .fixed');
        elementsToHide.forEach(el => {
            el.style.display = 'none';
        });
    });

    window.addEventListener('afterprint', function() {
        // Show elements again after printing
        const elementsToShow = document.querySelectorAll('.no-print, button');
        elementsToShow.forEach(el => {
            el.style.display = '';
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Print dropdown menu styles */
    .print-dropdown {
        transition: all 0.2s ease-in-out;
    }

    .print-dropdown.hidden {
        opacity: 0;
        transform: translateY(-10px);
        pointer-events: none;
    }

    .print-dropdown:not(.hidden) {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    /* Mobile responsive dropdown */
    @media (max-width: 640px) {
        .print-dropdown {
            position: fixed !important;
            top: auto !important;
            bottom: 20px !important;
            left: 20px !important;
            right: 20px !important;
            width: auto !important;
            margin: 0 !important;
        }
    }

    /* Print styles */
    @media print {

        .no-print,
        button,
        .fixed {
            display: none !important;
        }

        .bg-gradient-to-r {
            background: #f8f9fa !important;
            -webkit-print-color-adjust: exact;
        }

        .shadow-sm,
        .shadow-lg {
            box-shadow: none !important;
        }

        .border {
            border: 1px solid #dee2e6 !important;
        }

        body {
            font-size: 12px !important;
        }

        .text-lg {
            font-size: 14px !important;
        }

        .text-2xl {
            font-size: 18px !important;
        }
    }

    /* Mobile responsive table */
    @media (max-width: 768px) {
        .overflow-x-auto table {
            font-size: 0.875rem;
        }

        .overflow-x-auto th,
        .overflow-x-auto td {
            padding: 0.5rem 0.25rem;
            white-space: nowrap;
        }

        .overflow-x-auto th:first-child,
        .overflow-x-auto td:first-child {
            position: sticky;
            left: 0;
            background: white;
            z-index: 10;
            box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.1);
        }

        .overflow-x-auto thead th:first-child {
            background: #f9fafb;
        }

        /* Stack cards vertically on mobile */
        .grid.grid-cols-1.lg\\:grid-cols-3 {
            grid-template-columns: 1fr;
        }

        /* Adjust button layout on mobile */
        .flex.flex-col.sm\\:flex-row {
            flex-direction: column;
            gap: 0.5rem;
        }

        .flex.flex-col.sm\\:flex-row button,
        .flex.flex-col.sm\\:flex-row a {
            width: 100%;
            justify-content: center;
        }

        /* Responsive patient info */
        .flex.flex-col.sm\\:flex-row.sm\\:items-start.sm\\:space-x-6 {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .grid.grid-cols-1.sm\\:grid-cols-2 {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }

    /* Smooth transitions */
    .transition-colors {
        transition-property: color, background-color, border-color;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    /* Custom scrollbar for mobile tables */
    .overflow-x-auto::-webkit-scrollbar {
        height: 4px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 2px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 2px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Enhanced focus states for accessibility */
    button:focus,
    a:focus {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }

    /* Loading animation */
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Gradient backgrounds for better visual hierarchy */
    .bg-gradient-to-r {
        background-image: linear-gradient(to right, var(--tw-gradient-stops));
    }

    /* Status badge animations */
    .inline-flex.items-center.px-2\.5.py-0\.5.rounded-full {
        transition: all 0.2s ease-in-out;
    }

    .inline-flex.items-center.px-2\.5.py-0\.5.rounded-full:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Card hover effects */
    .bg-white.rounded-lg.shadow-sm:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transition: box-shadow 0.3s ease-in-out;
    }

    /* Timeline enhancements */
    .flow-root ul li:last-child .absolute {
        display: none;
    }

    /* Modal backdrop blur effect */
    #statusModal.backdrop-blur {
        backdrop-filter: blur(4px);
    }

    /* Notification animations */
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .notification-enter {
        animation: slideInRight 0.3s ease-out;
    }

    /* Table row hover effect */
    .hover\\:bg-gray-50:hover {
        background-color: #f9fafb;
        transition: background-color 0.15s ease-in-out;
    }

    /* Responsive text sizing */
    @media (max-width: 640px) {
        .text-2xl {
            font-size: 1.5rem;
            line-height: 2rem;
        }

        .text-lg {
            font-size: 1rem;
            line-height: 1.5rem;
        }

        .px-4.py-5.sm\\:px-6 {
            padding: 1rem;
        }

        .px-4.py-5.sm\\:p-6 {
            padding: 1rem;
        }
    }

    /* Enhanced button states */
    button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    button:not(:disabled):hover {
        transform: translateY(-1px);
        transition: transform 0.2s ease-in-out;
    }

    /* Better spacing for mobile */
    @media (max-width: 640px) {
        .space-y-6>*+* {
            margin-top: 1rem;
        }

        .space-y-4>*+* {
            margin-top: 0.75rem;
        }

        .gap-6 {
            gap: 1rem;
        }
    }

    /* Print optimizations */
    @media print {
        * {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        .bg-gradient-to-r {
            background: linear-gradient(to right, #f8f9fa, #e9ecef) !important;
        }

        .text-blue-600,
        .text-green-600,
        .text-purple-600,
        .text-amber-600,
        .text-indigo-600,
        .text-teal-600,
        .text-rose-600 {
            color: #374151 !important;
        }

        .border-l-4 {
            border-left: 4px solid #6b7280 !important;
        }

        .overflow-x-auto {
            overflow: visible !important;
        }

        table {
            page-break-inside: avoid;
        }

        .bg-white {
            background: white !important;
        }

        .shadow-sm,
        .shadow-lg {
            box-shadow: none !important;
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
            background-color: #374151;
        }
    }

    /* Accessibility improvements */
    @media (prefers-reduced-motion: reduce) {

        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }

    /* High contrast mode support */
    @media (prefers-contrast: high) {
        .border {
            border-width: 2px;
        }

        .text-gray-500,
        .text-gray-600 {
            color: #000;
        }

        .bg-gray-50 {
            background-color: #fff;
            border: 1px solid #000;
        }
    }
</style>
@endpush