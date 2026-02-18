@extends('admin.layouts.app')

@section('content')
@php
$user = Auth::user();
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-2 sm:p-4">
    <div class="max-w-7xl mx-auto">
        {{-- Enhanced Header --}}
        <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl overflow-hidden border border-white/20 mb-4">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-center sm:text-left">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">
                            <i class="fas fa-bed mr-2"></i>Ward Services Management
                        </h1>
                        <p class="text-blue-100 text-sm">Manage ward services and patient accommodations</p>
                    </div>

                    <div class="w-full sm:w-auto">
                        <a href="{{ route('admin.ward-services.create') }}" id="createWardServiceBtn"
                            class="group relative overflow-hidden bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto flex items-center justify-center">
                            <div class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                            <span class="relative flex items-center justify-center">
                                <svg id="defaultPlusIcon" class="w-5 h-5 mr-2 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <i id="spinnerIcon" class="hidden fas fa-spinner fa-spin mr-2"></i>
                                <span id="buttonText">Create Ward Service</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Enhanced Filter Section -->
            <div class="p-4 bg-gradient-to-r from-gray-50 to-blue-50 border-t border-gray-200">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" id="search" name="search" value="{{ request('search') }}"
                            placeholder="Search ward services..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                    </div>

                    <!-- Status Filter -->
                    <div class="relative">
                        <select id="status" name="status" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <i class="fas fa-filter absolute left-3 top-3 text-gray-400 text-sm"></i>
                    </div>

                    <!-- Fee Range Filter -->
                    <div class="relative">
                        <input type="number" name="min_fee" value="{{ request('min_fee') }}" placeholder="Min Fee"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <i class="fas fa-money-bill-wave absolute left-3 top-3 text-gray-400 text-sm"></i>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" id="filterBtn"
                            class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md text-sm">
                            <i class="fas fa-filter mr-1"></i>
                            <span class="hidden sm:inline">Filter</span>
                        </button>

                        <a href="{{ route('admin.ward-services.index') }}"
                            class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md text-sm text-center flex items-center justify-center">
                            <i class="fas fa-undo mr-1"></i>
                            <span class="hidden sm:inline">Reset</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Mobile Cards - Only show on small screens --}}
        <div class="lg:hidden space-y-3">
            @forelse($wardServices as $service)
            <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-md border border-white/20 overflow-hidden hover:shadow-lg transition-all duration-300">
                <!-- Mobile Card Header -->
                <div class="bg-gradient-to-r from-slate-50 to-blue-50 px-4 py-3 border-b border-gray-100">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-bed text-white text-sm"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-sm">{{ $service->name }}</h3>
                                <p class="text-xs text-gray-500">{{ $service->duration_minutes }} minutes</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                $service->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                            }}">
                                {{ ucfirst($service->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Mobile Card Body -->
                <div class="p-4">
                    <div class="mb-3">
                        <p class="text-sm text-gray-600 line-clamp-2">{{ $service->description ?: 'No description available' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-3 text-sm">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-users mr-2 text-blue-500"></i>
                            <span>{{ $service->max_patients_per_slot }} patients/slot</span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-clock mr-2 text-blue-500"></i>
                            <span>{{ $service->start_time }} - {{ $service->end_time }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mb-3">
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            {{ count($service->available_days) }} days/week
                        </div>
                        <div class="text-lg font-bold text-green-600">
                            ৳{{ number_format($service->daily_fee, 2) }}
                        </div>
                    </div>

                    <!-- Mobile Action Buttons -->
                    <div class="grid grid-cols-3 gap-2">
                        <!-- View Button -->
                        <!-- <a href="{{ route('admin.ward-services.show', $service->id) }}"
                            class="flex flex-col items-center justify-center p-3 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-all duration-300">
                            <i class="fas fa-eye text-sm mb-1"></i>
                            <span class="text-xs font-medium">View</span>
                        </a> -->

                        <!-- Edit Button -->
                        <!-- <a href="{{ route('admin.ward-services.edit', $service->id) }}"
                            class="flex flex-col items-center justify-center p-3 rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition-all duration-300">
                            <i class="fas fa-edit text-sm mb-1"></i>
                            <span class="text-xs font-medium">Edit</span>
                        </a> -->

                        <!-- Delete Button -->
                        <form action="{{ route('admin.ward-services.destroy', $service->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                onclick="return confirm('Are you sure you want to delete this ward service?')"
                                class="w-full flex flex-col items-center justify-center p-3 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-all duration-300">
                                <i class="fas fa-trash-alt text-sm mb-1"></i>
                                <span class="text-xs font-medium">Delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-lg p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bed text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No ward services found</h3>
                <p class="text-gray-500 text-sm">Try adjusting your search criteria or create a new ward service.</p>
            </div>
            @endforelse
        </div>

        {{-- Desktop Table - Only show on large screens --}}
        <div class="hidden lg:block bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-white/20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-blue-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-bed mr-2"></i>Service
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-clock mr-2"></i>Schedule
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-users mr-2"></i>Capacity
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-money-bill mr-2"></i>Fee
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
                        @forelse($wardServices as $service)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-300">
                            <!-- Service Column -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-bed text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $service->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $service->duration_minutes }} minutes</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Schedule Column -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <div class="flex items-center mb-1">
                                        <i class="fas fa-clock text-blue-500 mr-2"></i>
                                        <span>{{ $service->start_time }} - {{ $service->end_time }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-days text-blue-500 mr-2"></i>
                                        <span class="text-xs text-gray-600">{{ count($service->available_days) }} days/week</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Capacity Column -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-users text-white text-xs"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $service->max_patients_per_slot }}</div>
                                        <div class="text-xs text-gray-500">patients/slot</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Fee Column -->
                            <td class="px-6 py-4">
                                <div class="text-lg font-bold text-green-600">
                                    ৳{{ number_format($service->daily_fee, 2) }}
                                </div>
                                <div class="text-xs text-gray-500">per day</div>
                            </td>

                            <!-- Status Column -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                    $service->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                }}">
                                    <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ 
                                        $service->status == 'active' ? 'bg-green-400' : 'bg-red-400'
                                    }}"></span>
                                    {{ ucfirst($service->status) }}
                                </span>
                            </td>

                            <!-- Actions Column -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <!-- View Button -->
                                    <a href="{{ route('admin.ward-services.show', $service->id) }}" id="viewBtn{{ $service->id }}"
                                        class="p-2 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-all duration-300"
                                        title="View Ward Service">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                    </a>

                                    <!-- Edit Button -->
                                    <a href="{{ route('admin.ward-services.edit', $service->id) }}" id="editBtn{{ $service->id }}"
                                        class="p-2 rounded-lg text-yellow-600 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 transition-all duration-300"
                                        title="Edit Ward Service">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </a>

                                    <!-- Delete Button -->
                                    <form action="{{ route('admin.ward-services.destroy', $service->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this ward service? This action cannot be undone.')"
                                            class="p-2 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-all duration-300"
                                            title="Delete Ward Service">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-bed text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No ward services found</h3>
                                <p class="text-gray-500">Try adjusting your search criteria or create a new ward service.</p>
                                <div class="mt-4">
                                    <a href="{{ route('admin.ward-services.create') }}"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        <i class="fas fa-plus mr-2"></i>
                                        Create Ward Service
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($wardServices->hasPages())
        <div class="mt-6 flex justify-center">
            <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-2">
                {{ $wardServices->appends(request()->query())->links() }}
            </div>
        </div>
        @endif

        {{-- Quick Stats Cards --}}
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-md border border-white/20 p-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-bed text-white text-sm"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $wardServices->total() }}</div>
                        <div class="text-xs text-gray-500">Total Services</div>
                    </div>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-md border border-white/20 p-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-check-circle text-white text-sm"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $wardServices->where('status', 'active')->count() }}</div>
                        <div class="text-xs text-gray-500">Active Services</div>
                    </div>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-md border border-white/20 p-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-pink-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-times-circle text-white text-sm"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $wardServices->where('status', 'inactive')->count() }}</div>
                        <div class="text-xs text-gray-500">Inactive Services</div>
                    </div>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-md border border-white/20 p-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-money-bill-wave text-white text-sm"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">৳{{ number_format($wardServices->avg('daily_fee'), 0) }}</div>
                        <div class="text-xs text-gray-500">Average Fee</div>
                    </div>
                </div>
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
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
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

    /* Status indicator animations */
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

    /* Custom gradient backgrounds */
    .bg-gradient-to-br {
        background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
    }

    .from-slate-50 {
        --tw-gradient-from: #f8fafc;
        --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(248, 250, 252, 0));
    }

    .to-blue-50 {
        --tw-gradient-to: #eff6ff;
    }

    /* Animation for status indicators */
    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: .5;
        }
    }

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* Enhanced table row hover */
    tbody tr:hover {
        background: linear-gradient(to right, #eff6ff, #e0f2fe);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    /* Button loading state */
    .btn-loading {
        position: relative;
        pointer-events: none;
    }

    .btn-loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid transparent;
        border-top-color: currentColor;
        border-radius: 50%;
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

    /* Enhanced search input */
    input[type="text"]:focus,
    input[type="number"]:focus,
    select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Stats card animations */
    .stats-card {
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px -8px rgba(0, 0, 0, 0.15);
    }

    /* Responsive grid improvements */
    @media (max-width: 640px) {
        .grid-cols-1 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
    }

    @media (min-width: 640px) and (max-width: 1023px) {
        .sm\:grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1024px) {
        .lg\:grid-cols-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Enhanced button loading states
        $('#createWardServiceBtn').on('click', function() {
            const icon = $('#defaultPlusIcon');
            const spinner = $('#spinnerIcon');
            const text = $('#buttonText');

            icon.addClass('hidden');
            spinner.removeClass('hidden');
            text.text('Loading...');
        });

        $('#filterBtn').on('click', function() {
            const btn = $(this);
            const originalHtml = btn.html();

            btn.html('<i class="fas fa-spinner fa-spin mr-1"></i><span class="hidden sm:inline">Filtering...</span>');
            btn.prop('disabled', true);
        });

        // Enhanced search functionality with debounce
        let searchTimeout;
        $('#search').on('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = $(this).val();

            if (searchTerm.length > 2) {
                searchTimeout = setTimeout(() => {
                    $(this).closest('form').submit();
                }, 1000);
            } else if (searchTerm.length === 0) {
                searchTimeout = setTimeout(() => {
                    $(this).closest('form').submit();
                }, 500);
            }
        });

        // Auto-submit on filter changes
        $('#status, input[name="min_fee"]').on('change', function() {
            $(this).closest('form').submit();
        });

        // Button click handlers for individual ward service actions
        $('[id^="viewBtn"]').on('click', function() {
            const btn = $(this);
            btn.addClass('btn-loading');
        });

        $('[id^="editBtn"]').on('click', function() {
            const btn = $(this);
            btn.addClass('btn-loading');
        });

        // Enhanced delete confirmation
        $('form[method="POST"] button[type="submit"]').on('click', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            const serviceName = $(this).closest('tr').find('td:first .text-sm.font-medium').text();

            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete "${serviceName}". This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // Focus management for better UX
        const searchInput = document.getElementById('search');
        if (searchInput && searchInput.value === '') {
            searchInput.focus();
        }

        // Keyboard shortcuts
        $(document).keydown(function(e) {
            // Ctrl/Cmd + K to focus search
            if ((e.ctrlKey || e.metaKey) && e.keyCode === 75) {
                e.preventDefault();
                $('#search').focus();
            }

            // Escape to clear search
            if (e.keyCode === 27) {
                $('#search').val('').trigger('input');
            }

            // Ctrl/Cmd + N to create new ward service
            if ((e.ctrlKey || e.metaKey) && e.keyCode === 78) {
                e.preventDefault();
                window.location.href = "{{ route('admin.ward-services.create') }}";
            }
        });

        // Auto-hide success messages
        setTimeout(function() {
            $('.alert-success, .bg-green-100').fadeOut('slow');
        }, 5000);

        // Enhanced form validation
        $('form').on('submit', function() {
            const submitBtn = $(this).find('button[type="submit"]');
            if (submitBtn.length) {
                submitBtn.prop('disabled', true);
                setTimeout(() => {
                    submitBtn.prop('disabled', false);
                }, 3000);
            }
        });

        // Smooth scroll to top functionality
        $(window).scroll(function() {
            if ($(this).scrollTop() > 100) {
                if (!$('#backToTop').length) {
                    $('body').append('<button id="backToTop" class="fixed bottom-4 right-4 bg-blue-500 hover:bg-blue-600 text-white p-3 rounded-full shadow-lg transition-all duration-300 z-50"><i class="fas fa-arrow-up"></i></button>');
                }
                $('#backToTop').fadeIn();
            } else {
                $('#backToTop').fadeOut();
            }
        });

        // Back to top click handler
        $(document).on('click', '#backToTop', function() {
            $('html, body').animate({
                scrollTop: 0
            }, 600);
        });

        // Enhanced table row hover effects
        $('tbody tr').hover(
            function() {
                $(this).addClass('shadow-sm');
            },
            function() {
                $(this).removeClass('shadow-sm');
            }
        );

        // Tooltip initialization for truncated text
        $('[title]').hover(
            function() {
                const title = $(this).attr('title');
                if (title && title.length > 50) {
                    $(this).attr('data-original-title', title);
                }
            }
        );

        // Stats cards animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                }
            });
        }, observerOptions);

        // Observe stats cards
        document.querySelectorAll('.stats-card').forEach(card => {
            observer.observe(card);
        });

        // Real-time search suggestions (if needed)
        $('#search').on('input', function() {
            const query = $(this).val();
            if (query.length > 2) {
                // You can implement AJAX search suggestions here
                console.log('Searching for:', query);
            }
        });

        // Enhanced mobile menu toggle (if needed)
        $('.mobile-menu-toggle').on('click', function() {
            $('.mobile-menu').toggleClass('hidden');
        });

        // Print functionality
        window.printWardServices = function() {
            window.print();
        };

        // Export functionality placeholder
        window.exportWardServices = function(format) {
            console.log('Exporting ward services as:', format);
            // Implement export functionality
        };
    });

    // Helper function for alerts
    function showAlert(message, type = "info") {
        const alertDiv = $(`
            <div class="fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            } transition-all duration-300 transform translate-x-full">
                <div class="flex items-center">
                    <i class="fas ${
                        type === 'success' ? 'fa-check-circle' : 
                        type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'
                    } mr-2"></i>
                    <span>${message}</span>
                </div>
            </div>
        `);

        $('body').append(alertDiv);

        // Animate in
        setTimeout(() => {
            alertDiv.removeClass('translate-x-full');
        }, 100);

        // Auto remove after 3 seconds
        setTimeout(() => {
            alertDiv.addClass('translate-x-full');
            setTimeout(() => {
                alertDiv.remove();
            }, 300);
        }, 3000);
    }

    // Add fade-in animation CSS
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }
    `;
    document.head.appendChild(style);
</script>

<!-- SweetAlert2 for better confirmations -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
@endsection