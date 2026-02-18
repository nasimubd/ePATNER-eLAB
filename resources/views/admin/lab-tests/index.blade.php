@extends('admin.layouts.app')

@section('title', 'Lab Tests Management')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-2 sm:p-4">
    <div class="max-w-7xl mx-auto">
        {{-- Enhanced Header --}}
        <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl overflow-hidden border border-white/20 mb-4">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-blue-700 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-center sm:text-left">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">
                            <i class="fas fa-flask mr-2"></i>Lab Tests Management
                        </h1>
                        <p class="text-blue-100 text-sm">Manage your laboratory tests and procedures</p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                        <a href="{{ route('admin.lab-tests.export', request()->query()) }}"
                            class="group relative overflow-hidden bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-medium py-2.5 px-4 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center">
                            <div class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                            <span class="relative flex items-center justify-center">
                                <i class="fas fa-download mr-2"></i>
                                <span class="hidden sm:inline">Export</span>
                            </span>
                        </a>
                        <a href="{{ route('admin.lab-tests.create') }}" id="createTestBtn"
                            class="group relative overflow-hidden bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center">
                            <div class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                            <span class="relative flex items-center justify-center">
                                <svg id="defaultPlusIcon" class="w-5 h-5 mr-2 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <i id="spinnerIcon" class="hidden fas fa-spinner fa-spin mr-2"></i>
                                <span id="buttonText" class="hidden sm:inline">Add New Test</span>
                                <span class="sm:hidden">Add</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="p-4 bg-gradient-to-r from-gray-50 to-blue-50 border-b border-gray-200">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                    <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-sm border border-white/20 p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-vials text-white text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-600">Total Tests</p>
                                <p class="text-lg font-semibold text-gray-900">{{ number_format($stats['total']) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-sm border border-white/20 p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check-circle text-white text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-600">Active Tests</p>
                                <p class="text-lg font-semibold text-gray-900">{{ number_format($stats['active']) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-sm border border-white/20 p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-pink-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-times-circle text-white text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-600">Inactive Tests</p>
                                <p class="text-lg font-semibold text-gray-900">{{ number_format($stats['inactive']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Filter Section -->
                <form method="GET" action="{{ route('admin.lab-tests.index') }}" class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        <!-- Search Input -->
                        <div class="relative">
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                placeholder="Search tests..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                        </div>

                        <!-- Department Filter -->
                        <div class="relative">
                            <select id="department" name="department" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                <option value="{{ $department }}" {{ request('department') == $department ? 'selected' : '' }}>
                                    {{ $department }}
                                </option>
                                @endforeach
                            </select>
                            <i class="fas fa-building absolute left-3 top-3 text-gray-400 text-sm"></i>
                        </div>

                        <!-- Sample Type Filter -->
                        <div class="relative">
                            <select id="sample_type" name="sample_type" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                <option value="">All Sample Types</option>
                                @foreach($sampleTypes as $sampleType)
                                <option value="{{ $sampleType }}" {{ request('sample_type') == $sampleType ? 'selected' : '' }}>
                                    {{ $sampleType }}
                                </option>
                                @endforeach
                            </select>
                            <i class="fas fa-vial absolute left-3 top-3 text-gray-400 text-sm"></i>
                        </div>

                        <!-- Status Filter -->
                        <div class="relative">
                            <select id="status" name="status" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <i class="fas fa-toggle-on absolute left-3 top-3 text-gray-400 text-sm"></i>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <button type="submit" id="filterBtn"
                                class="flex-1 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md text-sm">
                                <i class="fas fa-search mr-1"></i>
                                <span class="hidden sm:inline">Filter</span>
                            </button>

                            <a href="{{ route('admin.lab-tests.index') }}"
                                class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md text-sm text-center flex items-center justify-center">
                                <i class="fas fa-times mr-1"></i>
                                <span class="hidden sm:inline">Clear</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Mobile Cards - Only show on small screens --}}
        <div class="lg:hidden space-y-3 mt-4">
            @forelse($labTests as $test)
            <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-md border border-white/20 overflow-hidden hover:shadow-lg transition-all duration-300">
                <!-- Mobile Card Header -->
                <div class="bg-gradient-to-r from-slate-50 to-indigo-50 px-4 py-3 border-b border-gray-100">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-flask text-white text-sm"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-sm">{{ $test->test_name }}</h3>
                                <p class="text-xs text-gray-500">{{ $test->test_code }}</p>
                            </div>
                        </div>
                        <form action="{{ route('admin.lab-tests.toggle-status', $test) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $test->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }} transition-colors duration-200">
                                {{ $test->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Mobile Card Body -->
                <div class="p-4 space-y-2 text-sm text-gray-700">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Department</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $test->department ?? 'N/A' }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Sample Type</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $test->sample_type ?? 'N/A' }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Price</p>
                            <p class="text-sm font-medium text-gray-900">{{ number_format($test->price, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Duration</p>
                            <p class="text-sm font-medium text-gray-900">{{ $test->duration_minutes ? $test->duration_minutes . ' min' : 'N/A' }}</p>
                        </div>
                    </div>

                    @if($test->medicines->count() > 0)
                    <div class="mt-3 p-2 bg-blue-50 rounded-lg">
                        <p class="text-xs text-gray-500">Medicines Required</p>
                        <p class="text-sm text-blue-600 font-medium">{{ $test->medicines->count() }} medicine(s)</p>
                    </div>
                    @endif

                    <div class="flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('admin.lab-tests.show', $test) }}"
                            class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-blue-300 shadow-sm text-xs font-medium rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View
                        </a>
                        <a href="{{ route('admin.lab-tests.edit', $test) }}"
                            class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-yellow-300 shadow-sm text-xs font-medium rounded-lg text-yellow-700 bg-yellow-50 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-300">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </a>
                        <button onclick="checkStock({{ $test->id }})"
                            class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-purple-300 shadow-sm text-xs font-medium rounded-lg text-purple-700 bg-purple-50 hover:bg-purple-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-300">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Stock
                        </button>
                        <form action="{{ route('admin.lab-tests.destroy', $test) }}"
                            method="POST"
                            class="flex-1"
                            onsubmit="return confirm('Are you sure you want to delete this lab test?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center px-3 py-2 border border-red-300 shadow-sm text-xs font-medium rounded-lg text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-300">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-lg p-8 text-center mt-6">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-flask text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No lab tests found</h3>
                <p class="text-gray-500 text-sm mb-4">Try adjusting your search criteria or create a new lab test.</p>
                <a href="{{ route('admin.lab-tests.create') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-plus mr-2"></i>
                    Add Lab Test
                </a>
            </div>
            @endforelse
        </div>

        {{-- Desktop Table - Only show on large screens --}}
        <div class="hidden lg:block bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-white/20 overflow-hidden mt-4">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-list mr-2"></i>Lab Tests ({{ $labTests->total() }})
                </h3>
            </div>

            @if($labTests->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-indigo-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-flask mr-2"></i>Test Details
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-building mr-2"></i>Department
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-vial mr-2"></i>Sample Type
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-dollar-sign mr-2"></i>Price
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-clock mr-2"></i>Duration
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-toggle-on mr-2"></i>Status
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-cogs mr-2"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($labTests as $test)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-300">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-flask text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $test->test_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $test->test_code }}</div>
                                        @if($test->medicines->count() > 0)
                                        <div class="text-xs text-blue-600 mt-1">
                                            <i class="fas fa-pills mr-1"></i>{{ $test->medicines->count() }} medicine(s) required
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-building mr-1"></i>{{ $test->department ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-vial mr-1"></i>{{ $test->sample_type ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <i class="fas fa-dollar-sign text-green-600 mr-1"></i>{{ number_format($test->price, 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-clock mr-1"></i>{{ $test->duration_minutes ? $test->duration_minutes . ' min' : 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form action="{{ route('admin.lab-tests.toggle-status', $test) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $test->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }} transition-colors duration-200">
                                        <i class="fas {{ $test->is_active ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                        {{ $test->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('admin.lab-tests.show', $test) }}"
                                        class="p-2 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-all duration-300"
                                        title="View Test Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.lab-tests.edit', $test) }}"
                                        class="p-2 rounded-lg text-yellow-600 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 transition-all duration-300"
                                        title="Edit Test">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button onclick="checkStock({{ $test->id }})"
                                        class="p-2 rounded-lg text-purple-600 bg-purple-50 hover:bg-purple-100 border border-purple-200 transition-all duration-300"
                                        title="Check Stock">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </button>
                                    <form action="{{ route('admin.lab-tests.destroy', $test) }}"
                                        method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this lab test?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-all duration-300"
                                            title="Delete Test">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
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
            @else
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-flask text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No lab tests found</h3>
                <p class="text-gray-500 text-sm mb-4">Try adjusting your search criteria or create a new lab test.</p>
                <a href="{{ route('admin.lab-tests.create') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-plus mr-2"></i>
                    Add Lab Test
                </a>
            </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if($labTests->hasPages())
        <div class="mt-6 flex justify-center">
            <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-2">
                {{ $labTests->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Stock Check Modal -->
<div id="stockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-boxes mr-2"></i>Stock Availability
                </h3>
                <button onclick="closeStockModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="stockContent" class="space-y-4">
                <!-- Stock content will be loaded here -->
            </div>
            <div class="mt-6 flex justify-end">
                <button onclick="closeStockModal()"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* Custom scrollbar */
    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
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

    /* Text truncation with line clamp */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Mobile responsive improvements */
    @media (max-width: 640px) {
        .p-2 {
            padding: 0.25rem;
        }

        .space-y-3>*+* {
            margin-top: 0.75rem;
        }
    }

    /* Ensure proper responsive behavior */
    @media (max-width: 1023px) {
        .lg\:hidden {
            display: block !important;
        }

        .lg\:block {
            display: none !important;
        }
    }

    @media (min-width: 1024px) {
        .lg\:hidden {
            display: none !important;
        }

        .lg\:block {
            display: block !important;
        }
    }

    /* Button hover effects */
    .group:hover .group-hover\:translate-x-full {
        transform: translateX(100%);
    }

    .group:hover .group-hover\:rotate-90 {
        transform: rotate(90deg);
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

    /* Enhanced focus states */
    .focus\:ring-2:focus {
        outline: 2px solid transparent;
        outline-offset: 2px;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.5);
    }

    /* Smooth transitions */
    * {
        transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    /* Enhanced card hover effects */
    .hover\:shadow-lg:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /* Mobile touch improvements */
    @media (max-width: 768px) {
        .hover\:scale-105:hover {
            transform: none;
        }

        .hover\:scale-105:active {
            transform: scale(0.98);
        }
    }

    /* Status color coding */
    .bg-green-100 {
        background-color: #dcfce7;
    }

    .text-green-800 {
        color: #166534;
    }

    .bg-red-100 {
        background-color: #fee2e2;
    }

    .text-red-800 {
        color: #991b1b;
    }

    .bg-blue-100 {
        background-color: #dbeafe;
    }

    .text-blue-800 {
        color: #1e40af;
    }

    .bg-gray-100 {
        background-color: #f3f4f6;
    }

    .text-gray-800 {
        color: #1f2937;
    }

    /* Modal styles */
    .modal-backdrop {
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }

    /* Tooltip styles */
    [title]:hover::after {
        content: attr(title);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 1000;
        pointer-events: none;
    }

    /* Enhanced button styles */
    .btn-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: all 0.3s ease;
    }

    .btn-gradient:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    /* Custom badge styles */
    .badge-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .badge-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }

    .badge-info {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    /* Loading spinner */
    .spinner {
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3498db;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Enhanced button loading states
        $('#createTestBtn').on('click', function() {
            const icon = $('#defaultPlusIcon');
            const spinner = $('#spinnerIcon');
            const text = $('#buttonText');

            icon.addClass('hidden');
            spinner.removeClass('hidden');
            text.text('Loading...');
        });

        $('#filterBtn').on('click', function() {
            const btn = $(this);
            btn.html('<i class="fas fa-spinner fa-spin mr-1"></i><span class="hidden sm:inline">Filtering...</span>');
            btn.prop('disabled', true);
        });

        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // Auto-hide success/error messages
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });

    // Stock check functionality
    function checkStock(testId) {
        $('#stockModal').removeClass('hidden');
        $('#stockContent').html('<div class="flex justify-center"><div class="spinner"></div></div>');

        $.ajax({
            url: `/tests/${testId}/check-stock`,
            method: 'GET',
            success: function(response) {
                let content = '';

                if (response.has_sufficient_stock) {
                    content = `
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                                <div>
                                    <h4 class="text-green-800 font-medium">Stock Available</h4>
                                    <p class="text-green-600 text-sm">All required medicines are in stock.</p>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    content = `
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
                                <div>
                                    <h4 class="text-red-800 font-medium">Insufficient Stock</h4>
                                    <p class="text-red-600 text-sm">Some medicines are out of stock or insufficient.</p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3">
                    `;

                    response.insufficient_medicines.forEach(function(medicine) {
                        content += `
                            <div class="bg-white border border-gray-200 rounded-lg p-3">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h5 class="font-medium text-gray-900">${medicine.name}</h5>
                                        <p class="text-sm text-gray-600">Required: ${medicine.required} | Available: ${medicine.available}</p>
                                    </div>
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        Short: ${medicine.shortage}
                                    </span>
                                </div>
                            </div>
                        `;
                    });

                    content += '</div>';
                }

                $('#stockContent').html(content);
            },
            error: function() {
                $('#stockContent').html(`
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-times-circle text-red-500 text-xl mr-3"></i>
                            <div>
                                <h4 class="text-red-800 font-medium">Error</h4>
                                <p class="text-red-600 text-sm">Failed to check stock availability.</p>
                            </div>
                        </div>
                    </div>
                `);
            }
        });
    }

    function closeStockModal() {
        $('#stockModal').addClass('hidden');
    }

    // Close modal when clicking outside
    $(document).on('click', '#stockModal', function(e) {
        if (e.target === this) {
            closeStockModal();
        }
    });

    // Close modal with Escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            closeStockModal();
        }
    });

    // Enhanced form validation
    $('form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();

        submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');
        submitBtn.prop('disabled', true);

        // Re-enable button after 3 seconds (fallback)
        setTimeout(function() {
            submitBtn.html(originalText);
            submitBtn.prop('disabled', false);
        }, 3000);
    });

    // Search functionality with debounce
    let searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = $(this).val();

        if (searchTerm.length >= 3 || searchTerm.length === 0) {
            searchTimeout = setTimeout(function() {
                // Auto-submit form after 500ms of no typing
                $('#search').closest('form').submit();
            }, 500);
        }
    });

    // Status toggle confirmation
    $('form[action*="toggle-status"]').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const isActive = $(form).find('button').text().trim() === 'Active';
        const action = isActive ? 'deactivate' : 'activate';

        if (confirm(`Are you sure you want to ${action} this lab test?`)) {
            form.submit();
        }
    });

    // Smooth scroll to top
    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    // Show scroll to top button
    $(window).scroll(function() {
        if ($(this).scrollTop() > 300) {
            $('#scrollToTop').fadeIn();
        } else {
            $('#scrollToTop').fadeOut();
        }
    });
</script>
@endpush

<!-- Scroll to Top Button -->
<button id="scrollToTop" onclick="scrollToTop()"
    class="fixed bottom-4 right-4 bg-indigo-600 hover:bg-indigo-700 text-white p-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 hidden z-40">
    <i class="fas fa-arrow-up"></i>
</button>

@endsection