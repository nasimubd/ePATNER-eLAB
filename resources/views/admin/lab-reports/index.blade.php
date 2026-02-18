@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Lab Reports Management</h1>
                <p class="mt-1 text-sm sm:text-base text-gray-600">Manage laboratory test reports</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('admin.lab-reports.export', request()->query()) }}"
                    class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export
                </a>
                <a href="{{ route('admin.lab-reports.templates.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Templates
                </a>
                <a href="{{ route('admin.lab-reports.create') }}"
                    class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Report
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-4 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-4 flex-1 min-w-0">
                            <div class="text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Total Reports</div>
                            <div class="text-lg sm:text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-4 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-4 flex-1 min-w-0">
                            <div class="text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Draft Reports</div>
                            <div class="text-lg sm:text-2xl font-bold text-gray-900">{{ $stats['draft'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-4 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-4 flex-1 min-w-0">
                            <div class="text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Completed</div>
                            <div class="text-lg sm:text-2xl font-bold text-gray-900">{{ $stats['completed'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-4 sm:p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.586-2A2 2 0 0019 4H5a2 2 0 00-2 2v6a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2h-1.586z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-4 flex-1 min-w-0">
                            <div class="text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Verified</div>
                            <div class="text-lg sm:text-2xl font-bold text-gray-900">{{ $stats['verified'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Filter Reports</h2>
                <button id="toggleFilters" class="sm:hidden text-gray-500 hover:text-gray-700 p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                    </svg>
                </button>
            </div>
            <form method="GET" action="{{ route('admin.lab-reports.index') }}" id="filtersForm" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4" id="filterGrid">
                    <div class="sm:col-span-2 lg:col-span-1 xl:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text"
                            name="search"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                            placeholder="Report #, Patient Name, Phone..."
                            value="{{ request('search') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Test</label>
                        <select name="test_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="">All Tests</option>
                            @foreach($labTests as $test)
                            <option value="{{ $test->id }}" {{ request('test_id') == $test->id ? 'selected' : '' }}>
                                {{ $test->test_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="">All Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                        <input type="date"
                            name="date_from"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                            value="{{ request('date_from') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                        <input type="date"
                            name="date_to"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                            value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 pt-2">
                    <button type="submit"
                        class="flex-1 sm:flex-none px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('admin.lab-reports.index') }}"
                        class="flex-1 sm:flex-none px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200 text-center">
                        <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Reports List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if($reports->count() > 0)
            <!-- Header -->
            <div class="bg-gray-50 px-4 sm:px-6 py-3 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Lab Reports ({{ $reports->total() }})</h2>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reports as $report)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('admin.lab-reports.show', $report) }}" class="hover:text-blue-600">
                                                Report #{{ $report->report_number }}
                                            </a>
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $report->created_at->format('M d, Y h:i A') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $report->patient_name }}</div>
                                <div class="text-sm text-gray-500">{{ $report->patient_phone }}</div>
                                @if($report->patient_email)
                                <div class="text-xs text-gray-400">{{ $report->patient_email }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $report->labTest->test_name ?? 'N/A' }}</div>
                                @if($report->labTest)
                                <div class="text-xs text-gray-500">{{ $report->labTest->test_code }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                $statusColors = [
                                'draft' => 'bg-yellow-100 text-yellow-800',
                                'completed' => 'bg-blue-100 text-blue-800',
                                'verified' => 'bg-green-100 text-green-800',
                                'delivered' => 'bg-purple-100 text-purple-800'
                                ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$report->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $report->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $report->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.lab-reports.show', $report) }}"
                                        class="text-blue-600 hover:text-blue-900" title="View Report">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.lab-reports.edit', $report) }}"
                                        class="text-indigo-600 hover:text-indigo-900" title="Edit Report">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    @if($report->status === 'completed' || $report->status === 'verified')
                                    <a href="{{ route('admin.lab-reports.download', $report) }}"
                                        class="text-green-600 hover:text-green-900" title="Download PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </a>
                                    @endif
                                    <form action="{{ route('admin.lab-reports.destroy', $report) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Are you sure you want to delete this report?')"
                                            title="Delete Report">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="lg:hidden divide-y divide-gray-200">
                @foreach($reports as $report)
                <div class="p-4 sm:p-6 hover:bg-gray-50 transition-colors duration-200">
                    <!-- Report Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center flex-1 min-w-0">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 truncate">
                                    <a href="{{ route('admin.lab-reports.show', $report) }}" class="hover:text-blue-600">
                                        Report #{{ $report->report_number }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-500">{{ $report->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        <div class="ml-3 flex-shrink-0">
                            @php
                            $statusColors = [
                            'draft' => 'bg-yellow-100 text-yellow-800',
                            'completed' => 'bg-blue-100 text-blue-800',
                            'verified' => 'bg-green-100 text-green-800',
                            'delivered' => 'bg-purple-100 text-purple-800'
                            ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$report->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Report Details -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</div>
                            <div class="mt-1 text-sm font-medium text-gray-900">{{ $report->patient_name }}</div>
                            <div class="text-sm text-gray-500">{{ $report->patient_phone }}</div>
                            @if($report->patient_email)
                            <div class="text-xs text-gray-400">{{ $report->patient_email }}</div>
                            @endif
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Test</div>
                            <div class="mt-1 text-sm text-gray-900">{{ $report->labTest->test_name ?? 'N/A' }}</div>
                            @if($report->labTest)
                            <div class="text-xs text-gray-500">{{ $report->labTest->test_code }}</div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap gap-2 pt-2">
                        <a href="{{ route('admin.lab-reports.show', $report) }}"
                            class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View
                        </a>
                        <a href="{{ route('admin.lab-reports.edit', $report) }}"
                            class="inline-flex items-center px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-xs font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </a>
                        @if($report->status === 'completed' || $report->status === 'verified')
                        <a href="{{ route('admin.lab-reports.download', $report) }}"
                            class="inline-flex items-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download
                        </a>
                        @endif
                        <form action="{{ route('admin.lab-reports.destroy', $report) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded-lg transition-colors duration-200"
                                onclick="return confirm('Are you sure you want to delete this report?')">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($reports->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $reports->withQueryString()->links() }}
            </div>
            @endif
            @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No lab reports found</h3>
                <p class="text-gray-500 mb-6">
                    @if(request()->hasAny(['search', 'test_id', 'status', 'date_from', 'date_to']))
                    No reports match your current filters. Try adjusting your search criteria.
                    @else
                    Get started by creating your first lab report.
                    @endif
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    @if(request()->hasAny(['search', 'test_id', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('admin.lab-reports.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Clear Filters
                    </a>
                    @endif
                    <a href="{{ route('admin.lab-reports.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Report
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div id="success-message" class="fixed top-4 right-4 z-50 max-w-sm w-full bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
        <button onclick="document.getElementById('success-message').remove()" class="ml-auto pl-3">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div id="error-message" class="fixed top-4 right-4 z-50 max-w-sm w-full bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <span class="text-sm font-medium">{{ session('error') }}</span>
        <button onclick="document.getElementById('error-message').remove()" class="ml-auto pl-3">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
</div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile filter toggle
        const toggleFiltersBtn = document.getElementById('toggleFilters');
        const filterGrid = document.getElementById('filterGrid');

        if (toggleFiltersBtn && filterGrid) {
            toggleFiltersBtn.addEventListener('click', function() {
                filterGrid.classList.toggle('hidden');

                // Update icon
                const icon = this.querySelector('svg');
                if (filterGrid.classList.contains('hidden')) {
                    icon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                    `;
                } else {
                    icon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    `;
                }
            });
        }

        // Auto-hide success/error messages after 5 seconds
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');

        if (successMessage) {
            setTimeout(() => {
                successMessage.style.opacity = '0';
                successMessage.style.transform = 'translateX(100%)';
                setTimeout(() => successMessage.remove(), 300);
            }, 5000);
        }

        if (errorMessage) {
            setTimeout(() => {
                errorMessage.style.opacity = '0';
                errorMessage.style.transform = 'translateX(100%)';
                setTimeout(() => errorMessage.remove(), 300);
            }, 5000);
        }

        // Smooth transitions for messages
        const messages = document.querySelectorAll('#success-message, #error-message');
        messages.forEach(message => {
            message.style.transition = 'all 0.3s ease-in-out';
        });

        // Form auto-submit on filter change (optional)
        const filterForm = document.getElementById('filtersForm');
        if (filterForm) {
            const filterInputs = filterForm.querySelectorAll('select, input[type="date"]');
            filterInputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Optional: Auto-submit form on filter change
                    // Uncomment the line below if you want auto-submit
                    // filterForm.submit();
                });
            });
        }

        // Enhanced mobile experience
        if (window.innerWidth < 768) {
            // Add touch feedback for mobile buttons
            const buttons = document.querySelectorAll('button, a');
            buttons.forEach(button => {
                button.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.95)';
                }, {
                    passive: true
                });

                button.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                }, {
                    passive: true
                });
            });

            // Improve scroll performance on mobile
            let ticking = false;

            function updateScrollPosition() {
                // Add any scroll-based animations here if needed
                ticking = false;
            }

            window.addEventListener('scroll', function() {
                if (!ticking) {
                    requestAnimationFrame(updateScrollPosition);
                    ticking = true;
                }
            }, {
                passive: true
            });
        }

        // Keyboard navigation support
        document.addEventListener('keydown', function(e) {
            // Close messages with Escape key
            if (e.key === 'Escape') {
                const messages = document.querySelectorAll('#success-message, #error-message');
                messages.forEach(message => message.remove());
            }
        });

        // Enhanced accessibility
        const actionButtons = document.querySelectorAll('[title]');
        actionButtons.forEach(button => {
            button.addEventListener('focus', function() {
                // Add visual focus indicator
                this.style.outline = '2px solid #3B82F6';
                this.style.outlineOffset = '2px';
            });

            button.addEventListener('blur', function() {
                this.style.outline = 'none';
            });
        });

        // Lazy loading for better performance (if needed for large datasets)
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }

        // Search input debouncing for better UX
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const form = this.closest('form');

                // Add loading indicator
                this.style.backgroundImage = 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\'%3E%3Ccircle cx=\'12\' cy=\'12\' r=\'10\' stroke=\'%23d1d5db\' stroke-width=\'4\'/%3E%3Cpath fill=\'%233b82f6\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'/%3E%3C/svg%3E")';
                this.style.backgroundRepeat = 'no-repeat';
                this.style.backgroundPosition = 'right 8px center';
                this.style.backgroundSize = '16px';

                // Optional: Auto-submit after delay (uncomment if desired)
                // searchTimeout = setTimeout(() => {
                //     form.submit();
                // }, 500);

                // Remove loading indicator after delay
                setTimeout(() => {
                    this.style.backgroundImage = 'none';
                }, 1000);
            });
        }

        // Improved table responsiveness
        const tables = document.querySelectorAll('table');
        tables.forEach(table => {
            const wrapper = table.parentElement;
            if (wrapper && wrapper.classList.contains('overflow-x-auto')) {
                let isScrolling = false;

                wrapper.addEventListener('scroll', function() {
                    if (!isScrolling) {
                        this.classList.add('scrolling');
                        isScrolling = true;

                        setTimeout(() => {
                            this.classList.remove('scrolling');
                            isScrolling = false;
                        }, 150);
                    }
                }, {
                    passive: true
                });
            }
        });

        // Status badge animations
        const statusBadges = document.querySelectorAll('[class*="bg-"][class*="text-"]');
        statusBadges.forEach(badge => {
            badge.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.05)';
            });

            badge.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });

        // Performance monitoring (optional)
        if ('performance' in window) {
            window.addEventListener('load', function() {
                setTimeout(() => {
                    const perfData = performance.getEntriesByType('navigation')[0];
                    if (perfData && perfData.loadEventEnd - perfData.loadEventStart > 3000) {
                        console.warn('Page load time is slow. Consider optimizing.');
                    }
                }, 0);
            });
        }
    });

    // Utility function for smooth scrolling
    function smoothScrollTo(element) {
        if (element) {
            element.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }

    // Export function with loading state
    function exportReports() {
        const exportBtn = document.querySelector('a[href*="export"]');
        if (exportBtn) {
            const originalText = exportBtn.innerHTML;
            exportBtn.innerHTML = `
                <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" class="opacity-75"></path>
                </svg>
                Exporting...
            `;

            setTimeout(() => {
                exportBtn.innerHTML = originalText;
            }, 3000);
        }
    }

    // Add click handler for export button
    document.addEventListener('DOMContentLoaded', function() {
        const exportBtn = document.querySelector('a[href*="export"]');
        if (exportBtn) {
            exportBtn.addEventListener('click', exportReports);
        }
    });
</script>

<style>
    /* Additional CSS for enhanced mobile experience */
    @media (max-width: 768px) {

        /* Improve touch targets */
        button,
        a,
        input,
        select {
            min-height: 44px;
        }

        /* Better spacing on mobile */
        .space-y-6>*+* {
            margin-top: 1rem;
        }

        /* Smooth scrolling for tables */
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }

        .overflow-x-auto::-webkit-scrollbar {
            height: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }

        /* Card hover effects on mobile */
        .hover\:bg-gray-50:hover {
            background-color: #f9fafb;
            transition: background-color 0.2s ease;
        }
    }

    /* Loading states */
    .loading {
        opacity: 0.7;
        pointer-events: none;
    }

    /* Smooth transitions */
    * {
        transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
        transition-duration: 200ms;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Focus styles for accessibility */
    button:focus,
    a:focus,
    input:focus,
    select:focus {
        outline: 2px solid #3B82F6;
        outline-offset: 2px;
    }

    /* Status badge transitions */
    .status-badge {
        transition: transform 0.2s ease;
    }

    .status-badge:hover {
        transform: scale(1.05);
    }

    /* Message animations */
    #success-message,
    #error-message {
        animation: slideInRight 0.3s ease-out;
    }

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

    /* Responsive grid improvements */
    @media (min-width: 640px) and (max-width: 1024px) {
        .grid-cols-1.sm\:grid-cols-2.lg\:grid-cols-3.xl\:grid-cols-6 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }
</style>
@endpush

@endsection