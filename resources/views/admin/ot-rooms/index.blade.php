@extends('admin.layouts.app')

@section('content')
@php
$user = Auth::user();
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-orange-50 p-2 sm:p-4">
    <div class="max-w-7xl mx-auto">
        {{-- Enhanced Header --}}
        <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl overflow-hidden border border-white/20 mb-4">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-700 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-center sm:text-left">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white mb-1">
                            <i class="fas fa-hospital mr-2"></i>OT Rooms Management
                        </h1>
                        <p class="text-blue-100 text-xs sm:text-sm">Manage operating theater rooms and facilities</p>
                    </div>

                    <div class="w-full sm:w-auto">
                        <a href="{{ route('admin.ot-rooms.create') }}" id="createOtRoomBtn"
                            class="group relative overflow-hidden bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold py-2 sm:py-3 px-4 sm:px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto flex items-center justify-center text-sm sm:text-base">
                            <div class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                            <span class="relative flex items-center justify-center">
                                <svg id="defaultPlusIcon" class="w-4 h-4 sm:w-5 sm:h-5 mr-2 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <i id="spinnerIcon" class="hidden fas fa-spinner fa-spin mr-2"></i>
                                <span id="buttonText" class="hidden sm:inline">Create OT Room</span>
                                <span class="sm:hidden">Create</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Enhanced Filter Section -->
            <div class="p-3 sm:p-4 bg-gradient-to-r from-gray-50 to-blue-50 border-t border-gray-200">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3">
                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" id="search" name="search" value="{{ request('search') }}"
                            placeholder="Search rooms..."
                            class="w-full pl-8 sm:pl-10 pr-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-xs sm:text-sm">
                        <i class="fas fa-search absolute left-2 sm:left-3 top-2 sm:top-3 text-gray-400 text-xs sm:text-sm"></i>
                    </div>

                    <!-- Status Filter -->
                    <div class="relative">
                        <select id="status" name="status" class="w-full pl-8 sm:pl-10 pr-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-xs sm:text-sm">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                        <i class="fas fa-toggle-on absolute left-2 sm:left-3 top-2 sm:top-3 text-gray-400 text-xs sm:text-sm"></i>
                    </div>

                    <!-- Capacity Filter -->
                    <div class="relative">
                        <input type="number" name="capacity" value="{{ request('capacity') }}" placeholder="Min Capacity"
                            class="w-full pl-8 sm:pl-10 pr-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-xs sm:text-sm">
                        <i class="fas fa-users absolute left-2 sm:left-3 top-2 sm:top-3 text-gray-400 text-xs sm:text-sm"></i>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" id="filterBtn"
                            class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium py-2 sm:py-2.5 px-3 sm:px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md text-xs sm:text-sm">
                            <i class="fas fa-filter mr-1"></i>
                            <span class="hidden sm:inline">Filter</span>
                        </button>

                        <a href="{{ route('admin.ot-rooms.index') }}"
                            class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-medium py-2 sm:py-2.5 px-3 sm:px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md text-xs sm:text-sm text-center flex items-center justify-center">
                            <i class="fas fa-undo mr-1"></i>
                            <span class="hidden sm:inline">Reset</span>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Quick Stats Section -->
            <div class="p-3 sm:p-4 bg-gradient-to-r from-blue-50 to-purple-50 border-t border-gray-200">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-4">
                    <div class="stats-card bg-white/70 backdrop-blur-sm rounded-lg p-2 sm:p-3 text-center border border-white/30">
                        <div class="text-lg sm:text-2xl font-bold text-gray-900">{{ $otRooms->total() }}</div>
                        <div class="text-xs text-gray-600">Total Rooms</div>
                    </div>
                    <div class="stats-card bg-white/70 backdrop-blur-sm rounded-lg p-2 sm:p-3 text-center border border-white/30">
                        <div class="text-lg sm:text-2xl font-bold text-green-600">{{ $otRooms->where('status', 'active')->count() }}</div>
                        <div class="text-xs text-gray-600">Active</div>
                    </div>
                    <div class="stats-card bg-white/70 backdrop-blur-sm rounded-lg p-2 sm:p-3 text-center border border-white/30">
                        <div class="text-lg sm:text-2xl font-bold text-yellow-600">{{ $otRooms->where('status', 'maintenance')->count() }}</div>
                        <div class="text-xs text-gray-600">Maintenance</div>
                    </div>
                    <div class="stats-card bg-white/70 backdrop-blur-sm rounded-lg p-2 sm:p-3 text-center border border-white/30">
                        <div class="text-lg sm:text-2xl font-bold text-blue-600">{{ $otRooms->sum('capacity') }}</div>
                        <div class="text-xs text-gray-600">Total Capacity</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile Cards - Only show on small screens --}}
        <div class="lg:hidden space-y-3">
            @forelse($otRooms as $room)
            <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-md border border-white/20 overflow-hidden hover:shadow-lg transition-all duration-300">
                <!-- Mobile Card Header -->
                <div class="bg-gradient-to-r from-slate-50 to-blue-50 px-3 sm:px-4 py-3 border-b border-gray-100">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center space-x-2 sm:space-x-3 flex-1 min-w-0">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-hospital text-white text-xs sm:text-sm"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-gray-900 text-sm truncate">{{ $room->name }}</h3>
                                <p class="text-xs text-gray-500 truncate">Room: {{ $room->room_number }}</p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 ml-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ 
                                $room->status == 'active' ? 'bg-green-100 text-green-800' : 
                                ($room->status == 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') 
                            }}">
                                {{ ucfirst($room->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Mobile Card Body -->
                <div class="p-3 sm:p-4">
                    <div class="grid grid-cols-2 gap-2 sm:gap-3 mb-3 text-xs sm:text-sm">
                        <div>
                            <span class="text-gray-500">Capacity:</span>
                            <span class="font-medium text-gray-900 block">{{ $room->capacity }} patient(s)</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Equipment:</span>
                            <span class="font-medium text-gray-900 block">{{ is_array($room->equipment_available) ? count($room->equipment_available) : 0 }} items</span>
                        </div>
                    </div>

                    @if($room->description)
                    <div class="mb-3">
                        <p class="text-xs sm:text-sm text-gray-600 line-clamp-2">{{ Str::limit($room->description, 60) }}</p>
                    </div>
                    @endif

                    <!-- Mobile Action Buttons -->
                    <div class="grid grid-cols-2 gap-2">
                        <!-- View Button -->
                        <a href="{{ route('admin.ot-rooms.show', $room) }}"
                            class="flex flex-col items-center justify-center p-2 sm:p-3 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-all duration-300">
                            <i class="fas fa-eye text-sm mb-1"></i>
                            <span class="text-xs font-medium">View</span>
                        </a>

                        <!-- Delete Button -->
                        <form action="{{ route('admin.ot-rooms.destroy', $room) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                onclick="return confirm('Are you sure you want to delete this OT room?')"
                                class="w-full flex flex-col items-center justify-center p-2 sm:p-3 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-all duration-300">
                                <i class="fas fa-trash-alt text-sm mb-1"></i>
                                <span class="text-xs font-medium">Delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-lg p-6 sm:p-8 text-center">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-hospital text-gray-400 text-xl sm:text-2xl"></i>
                </div>
                <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">No OT rooms found</h3>
                <p class="text-gray-500 text-sm">Try adjusting your search criteria or create a new OT room.</p>
            </div>
            @endforelse
        </div>

        {{-- Desktop Table - Only show on large screens --}}
        <div class="hidden lg:block bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-white/20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-blue-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-hospital mr-2"></i>Room Details
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-users mr-2"></i>Capacity
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-tools mr-2"></i>Equipment
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-toggle-on mr-2"></i>Status
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-file-alt mr-2"></i>Description
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-cogs mr-2"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($otRooms as $room)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 transition-all duration-300">
                            <!-- Room Details Column -->
                            <td class="px-4 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fas fa-hospital text-white text-sm"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $room->name }}</div>
                                        <div class="text-xs text-gray-500 truncate">Room: {{ $room->room_number }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Capacity Column -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <i class="fas fa-users text-blue-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-900">{{ $room->capacity }}</span>
                                    <span class="text-xs text-gray-500 ml-1">patient(s)</span>
                                </div>
                            </td>

                            <!-- Equipment Column -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($room->equipment_available && is_array($room->equipment_available))
                                    <div class="flex items-center">
                                        <i class="fas fa-tools text-green-500 mr-2"></i>
                                        <span class="font-medium">{{ count($room->equipment_available) }} items</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ Str::limit(implode(', ', array_slice($room->equipment_available, 0, 3)), 40) }}
                                        @if(count($room->equipment_available) > 3)
                                        <span class="text-blue-600">+{{ count($room->equipment_available) - 3 }} more</span>
                                        @endif
                                    </div>
                                    @else
                                    <div class="flex items-center text-gray-400">
                                        <i class="fas fa-tools mr-2"></i>
                                        <span class="text-sm">No equipment listed</span>
                                    </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Status Column -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                    $room->status == 'active' ? 'bg-green-100 text-green-800' : 
                                    ($room->status == 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') 
                                }}">
                                    <i class="fas {{ 
                                        $room->status == 'active' ? 'fa-check-circle' : 
                                        ($room->status == 'maintenance' ? 'fa-wrench' : 'fa-times-circle') 
                                    }} mr-1"></i>
                                    {{ ucfirst($room->status) }}
                                </span>
                            </td>

                            <!-- Description Column -->
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900 max-w-xs">
                                    @if($room->description)
                                    <p class="truncate" title="{{ $room->description }}">{{ Str::limit($room->description, 50) }}</p>
                                    @else
                                    <span class="text-gray-400 italic">No description</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Actions Column -->
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center space-x-2">
                                    <!-- View Button -->
                                    <a href="{{ route('admin.ot-rooms.show', $room) }}"
                                        class="p-2 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-all duration-300"
                                        title="View OT Room">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                    </a>

                                    <!-- Delete Button -->
                                    <form action="{{ route('admin.ot-rooms.destroy', $room) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this OT room?')"
                                            class="p-2 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-all duration-300"
                                            title="Delete OT Room">
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
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-hospital text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No OT rooms found</h3>
                                <p class="text-gray-500">Try adjusting your search criteria or create a new OT room.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($otRooms->hasPages())
        <div class="mt-4 sm:mt-6 flex justify-center">
            <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-2">
                {{ $otRooms->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* Prevent horizontal scroll */
    body {
        overflow-x: hidden;
    }

    .container,
    .max-w-7xl {
        max-width: 100%;
        overflow-x: hidden;
    }

    /* Custom scrollbar for table */
    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
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

    .bg-white\/70 {
        background: rgba(255, 255, 255, 0.7);
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

    /* Stats card hover effects */
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Mobile responsive table */
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

    /* Responsive text truncation */
    .truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
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

    /* Ensure no horizontal overflow */
    .min-w-0 {
        min-width: 0;
    }

    /* Grid responsive improvements */
    @media (max-width: 640px) {
        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .sm\:grid-cols-4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    /* Table responsive improvements */
    table {
        table-layout: fixed;
        width: 100%;
    }

    td,
    th {
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    /* Status badge improvements */
    .inline-flex {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Status colors */
    .bg-green-100 {
        background-color: #dcfce7;
    }

    .text-green-800 {
        color: #166534;
    }

    .bg-yellow-100 {
        background-color: #fef3c7;
    }

    .text-yellow-800 {
        color: #92400e;
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

    /* Card animations */
    .stats-card {
        transition: all 0.3s ease;
    }

    /* Mobile card improvements */
    @media (max-width: 640px) {
        .space-y-3>*+* {
            margin-top: 0.75rem;
        }

        .p-2 {
            padding: 0.5rem;
        }

        .text-xs {
            font-size: 0.75rem;
            line-height: 1rem;
        }
    }

    /* Prevent text selection on buttons */
    button,
    .btn {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    /* Enhanced hover states for mobile */
    @media (hover: hover) {
        .hover\:bg-blue-100:hover {
            background-color: #dbeafe;
        }

        .hover\:bg-red-100:hover {
            background-color: #fee2e2;
        }

        .hover\:shadow-lg:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    }

    /* Loading spinner */
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Responsive padding adjustments */
    @media (max-width: 640px) {
        .p-4 {
            padding: 0.75rem;
        }

        .px-4 {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .py-4 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }
    }

    /* Ensure proper spacing on very small screens */
    @media (max-width: 375px) {
        .p-2 {
            padding: 0.375rem;
        }

        .gap-2 {
            gap: 0.375rem;
        }

        .text-xs {
            font-size: 0.7rem;
        }
    }

    /* Fix for select dropdown icons */
    select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }

    /* Accessibility improvements */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }

    /* High contrast mode support */
    @media (prefers-contrast: high) {
        .border-gray-300 {
            border-color: #000;
        }

        .text-gray-500 {
            color: #000;
        }

        .bg-gray-50 {
            background-color: #fff;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Enhanced button loading states
        $('#createOtRoomBtn').on('click', function() {
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
        $('#status, input[name="capacity"]').on('change', function() {
            $(this).closest('form').submit();
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

        // Stats card animations
        $('.stats-card').hover(
            function() {
                $(this).addClass('transform scale-105');
            },
            function() {
                $(this).removeClass('transform scale-105');
            }
        );

        // Mobile touch feedback
        if ('ontouchstart' in window) {
            $('.hover\\:scale-105').on('touchstart', function() {
                $(this).addClass('scale-95');
            }).on('touchend', function() {
                $(this).removeClass('scale-95');
            });
        }

        // Responsive table handling
        function handleResponsiveTable() {
            const table = $('.overflow-x-auto table');
            if (table.length) {
                const tableWidth = table[0].scrollWidth;
                const containerWidth = table.parent().width();

                if (tableWidth > containerWidth) {
                    table.parent().addClass('shadow-inner');
                } else {
                    table.parent().removeClass('shadow-inner');
                }
            }
        }

        // Call on load and resize
        handleResponsiveTable();
        $(window).resize(handleResponsiveTable);

        // Lazy loading for images if any
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
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

    // Performance optimization
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Optimized scroll handler
    const optimizedScrollHandler = debounce(function() {
        // Handle scroll events here
    }, 100);

    window.addEventListener('scroll', optimizedScrollHandler);
</script>
@endpush
@endsection