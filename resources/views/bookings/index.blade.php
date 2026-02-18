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
            <div class="bg-gradient-to-r from-blue-600 via-indigo-700 to-purple-700 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-center sm:text-left">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">
                            <i class="fas fa-calendar-check mr-2"></i>Bookings Management
                        </h1>
                        <p class="text-blue-100 text-sm">Manage ward and OT bookings</p>
                    </div>

                    <div class="w-full sm:w-auto">
                        <a href="{{ route('bookings.create') }}" id="createBookingBtn"
                            class="group relative overflow-hidden bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto flex items-center justify-center">
                            <div class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                            <span class="relative flex items-center justify-center">
                                <svg id="defaultPlusIcon" class="w-5 h-5 mr-2 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <i id="spinnerIcon" class="hidden fas fa-spinner fa-spin mr-2"></i>
                                <span id="buttonText">Create Booking</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Enhanced Filter Section -->
            <div class="p-4 bg-gradient-to-r from-gray-50 to-blue-50 border-t border-gray-200">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" id="search" name="search" value="{{ request('search') }}"
                            placeholder="Search patient..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                    </div>

                    <!-- Status Filter -->
                    <div class="relative">
                        <select id="status" name="status" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>No Show</option>
                        </select>
                        <i class="fas fa-filter absolute left-3 top-3 text-gray-400 text-sm"></i>
                    </div>

                    <!-- Booking Type Filter -->
                    <div class="relative">
                        <select id="booking_type" name="booking_type" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">All Types</option>
                            <option value="ward" {{ request('booking_type') == 'ward' ? 'selected' : '' }}>Ward</option>
                            <option value="ot" {{ request('booking_type') == 'ot' ? 'selected' : '' }}>OT</option>
                        </select>
                        <i class="fas fa-bed absolute left-3 top-3 text-gray-400 text-sm"></i>
                    </div>

                    <!-- Date From -->
                    <div class="relative">
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <i class="fas fa-calendar-alt absolute left-3 top-3 text-gray-400 text-sm"></i>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" id="filterBtn"
                            class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md text-sm">
                            <i class="fas fa-filter mr-1"></i>
                            <span class="hidden sm:inline">Filter</span>
                        </button>
                        <a href="{{ route('bookings.index') }}"
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
            @forelse($bookings as $booking)
            <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-md border border-white/20 overflow-hidden hover:shadow-lg transition-all duration-300">
                <!-- Mobile Card Header -->
                <div class="bg-gradient-to-r from-slate-50 to-blue-50 px-4 py-3 border-b border-gray-100">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-{{ $booking->booking_type == 'ward' ? 'bed' : 'procedures' }} text-white text-sm"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-sm">{{ $booking->patient->full_name }}</h3>
                                <p class="text-xs text-gray-500">{{ $booking->patient->patient_id }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                $booking->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                ($booking->status == 'confirmed' ? 'bg-blue-100 text-blue-800' : 
                                ($booking->status == 'completed' ? 'bg-green-100 text-green-800' : 
                                ($booking->status == 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) 
                            }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Mobile Card Body -->
                <div class="p-4">
                    <div class="mb-3">
                        <div class="flex items-center text-sm text-gray-600 mb-1">
                            <i class="fas fa-{{ $booking->booking_type == 'ward' ? 'bed' : 'procedures' }} mr-2 text-blue-500"></i>
                            <span class="font-medium">{{ ucfirst($booking->booking_type) }}:</span>
                            <span class="ml-1">{{ $booking->bookable->name }}</span>
                        </div>
                        @if($booking->booking_type == 'ot' && $booking->otRoom)
                        <div class="flex items-center text-sm text-gray-600 mb-1">
                            <i class="fas fa-door-open mr-2 text-green-500"></i>
                            <span>Room: {{ $booking->otRoom->name }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="flex justify-between items-center mb-3">
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            {{ $booking->booking_date }} at {{ $booking->booking_time }}
                        </div>
                        <div class="text-lg font-bold text-green-600">
                            ৳{{ number_format($booking->service_fee, 2) }}
                        </div>
                    </div>

                    <!-- Mobile Action Buttons -->
                    <div class="grid grid-cols-3 gap-2">
                        <!-- View Button -->
                        <a href="{{ route('bookings.show', $booking) }}"
                            class="flex flex-col items-center justify-center p-3 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-all duration-300">
                            <i class="fas fa-eye text-sm mb-1"></i>
                            <span class="text-xs font-medium">View</span>
                        </a>

                        <!-- Print Button -->
                        <a href="{{ route('bookings.print', $booking) }}"
                            target="_blank"
                            class="flex flex-col items-center justify-center p-3 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition-all duration-300">
                            <i class="fas fa-print text-sm mb-1"></i>
                            <span class="text-xs font-medium">Print</span>
                        </a>

                        <!-- Edit Button -->
                        @if($booking->status == 'pending')
                        <a href="{{ route('bookings.edit', $booking) }}"
                            class="flex flex-col items-center justify-center p-3 rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition-all duration-300">
                            <i class="fas fa-edit text-sm mb-1"></i>
                            <span class="text-xs font-medium">Edit</span>
                        </a>
                        @else
                        <div class="flex flex-col items-center justify-center p-3 rounded-lg bg-gray-50 text-gray-400">
                            <i class="fas fa-lock text-sm mb-1"></i>
                            <span class="text-xs font-medium">Locked</span>
                        </div>
                        @endif

                        <!-- Status Actions -->
                        @if($booking->status == 'pending')
                        <form action="{{ route('bookings.confirm', $booking) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="w-full flex flex-col items-center justify-center p-3 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition-all duration-300">
                                <i class="fas fa-check text-sm mb-1"></i>
                                <span class="text-xs font-medium">Confirm</span>
                            </button>
                        </form>
                        @elseif($booking->status == 'confirmed')
                        <form action="{{ route('bookings.complete', $booking) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="w-full flex flex-col items-center justify-center p-3 rounded-lg bg-purple-50 text-purple-600 hover:bg-purple-100 transition-all duration-300">
                                <i class="fas fa-check-double text-sm mb-1"></i>
                                <span class="text-xs font-medium">Complete</span>
                            </button>
                        </form>
                        @else
                        <div class="flex flex-col items-center justify-center p-3 rounded-lg bg-gray-50 text-gray-400">
                            <i class="fas fa-ban text-sm mb-1"></i>
                            <span class="text-xs font-medium">N/A</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-lg p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-check text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No bookings found</h3>
                <p class="text-gray-500 text-sm">Try adjusting your search criteria or create a new booking.</p>
            </div>
            @endforelse
        </div>

        {{-- Desktop Table - Only show on large screens --}}
        <div class="hidden lg:block bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-white/20">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-50 to-blue-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <i class="fas fa-user mr-2"></i>Patient
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <i class="fas fa-bed mr-2"></i>Service
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <i class="fas fa-calendar-alt mr-2"></i>Date & Time
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <i class="fas fa-tag mr-2"></i>Type
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <i class="fas fa-info-circle mr-2"></i>Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <i class="fas fa-money-bill mr-2"></i>Fee
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <i class="fas fa-cogs mr-2"></i>Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 transition-all duration-300">
                        <!-- Patient Column -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->patient->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->patient->patient_id }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Service Column -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-{{ $booking->booking_type == 'ward' ? 'bed' : 'procedures' }} text-white text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->bookable->name }}</div>
                                    @if($booking->booking_type == 'ot' && $booking->otRoom)
                                    <div class="text-sm text-gray-500">Room: {{ $booking->otRoom->name }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Date & Time Column -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $booking->booking_date }}</div>
                            <!-- <div class="text-sm text-gray-500">{{ $booking->booking_time }}
                                @if($booking->end_time)
                                - {{ $booking->end_time }}
                                @endif
                            </div> -->
                        </td>

                        <!-- Type Column -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                $booking->booking_type == 'ward' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' 
                            }}">
                                <i class="fas fa-{{ $booking->booking_type == 'ward' ? 'bed' : 'procedures' }} mr-1"></i>
                                {{ ucfirst($booking->booking_type) }}
                            </span>
                        </td>

                        <!-- Status Column -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                $booking->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                ($booking->status == 'confirmed' ? 'bg-blue-100 text-blue-800' : 
                                ($booking->status == 'completed' ? 'bg-green-100 text-green-800' : 
                                ($booking->status == 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) 
                            }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>

                        <!-- Fee Column -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-lg font-bold text-green-600">
                                ৳{{ number_format($booking->service_fee, 2) }}
                            </div>
                        </td>

                        <!-- Actions Column -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex justify-center space-x-2">
                                <!-- View Button -->
                                <a href="{{ route('bookings.show', $booking) }}"
                                    class="p-2 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-all duration-300"
                                    title="View Booking">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>

                                <!-- Print Button -->
                                <a href="{{ route('bookings.print', $booking) }}"
                                    target="_blank"
                                    class="p-2 rounded-lg text-green-600 bg-green-50 hover:bg-green-100 border border-green-200 transition-all duration-300"
                                    title="Print Receipt">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                                    </svg>
                                </a>

                                <!-- Edit Button -->
                                @if($booking->status == 'pending')
                                <a href="{{ route('bookings.edit', $booking) }}"
                                    class="p-2 rounded-lg text-yellow-600 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 transition-all duration-300"
                                    title="Edit Booking">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </a>
                                @endif

                                <!-- Status Action Buttons -->
                                @if($booking->status == 'pending')
                                <form action="{{ route('bookings.confirm', $booking) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="p-2 rounded-lg text-green-600 bg-green-50 hover:bg-green-100 border border-green-200 transition-all duration-300"
                                        title="Confirm Booking">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                                <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        onclick="return confirm('Are you sure you want to cancel this booking?')"
                                        class="p-2 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-all duration-300"
                                        title="Cancel Booking">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                                @elseif($booking->status == 'confirmed')
                                <form action="{{ route('bookings.complete', $booking) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="p-2 rounded-lg text-purple-600 bg-purple-50 hover:bg-purple-100 border border-purple-200 transition-all duration-300"
                                        title="Complete Booking">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-calendar-check text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No bookings found</h3>
                            <p class="text-gray-500">Try adjusting your search criteria or create a new booking.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($bookings->hasPages())
        <div class="mt-6 flex justify-center">
            <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-2">
                {{ $bookings->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .backdrop-blur-sm {
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }

    .bg-white\/90 {
        background: rgba(255, 255, 255, 0.9);
    }

    .bg-white\/80 {
        background: rgba(255, 255, 255, 0.8);
    }

    .focus\:ring-2:focus {
        outline: 2px solid transparent;
        outline-offset: 2px;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
    }

    /* Prevent horizontal scrolling */
    body {
        overflow-x: hidden;
    }

    .max-w-7xl {
        max-width: 100%;
        padding-left: 1rem;
        padding-right: 1rem;
    }

    /* Responsive table without horizontal scroll */
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

        .max-w-7xl {
            max-width: 80rem;
            padding-left: 2rem;
            padding-right: 2rem;
        }
    }

    /* Ensure table fits within container */
    table {
        table-layout: fixed;
        width: 100%;
    }

    th,
    td {
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    /* Mobile responsive improvements */
    @media (max-width: 640px) {
        .grid-cols-1.sm\:grid-cols-2.lg\:grid-cols-5 {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .p-2.sm\:p-4 {
            padding: 0.5rem;
        }

        .px-4.sm\:px-6 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .text-2xl.sm\:text-3xl {
            font-size: 1.5rem;
        }

        .space-x-3 {
            margin-left: 0;
        }

        .space-x-3>*+* {
            margin-left: 0.75rem;
        }
    }

    /* Ensure no horizontal overflow */
    * {
        box-sizing: border-box;
    }

    .container,
    .max-w-7xl {
        width: 100%;
        margin-left: auto;
        margin-right: auto;
    }

    /* Fix for small screens */
    @media (max-width: 768px) {
        .grid-cols-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .flex.justify-between {
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .text-lg {
            font-size: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Enhanced button loading states
        $('#createBookingBtn').on('click', function() {
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
        $('#status, #booking_type, input[name="date_from"]').on('change', function() {
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

        // Enhanced form validation for status changes
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

        // Confirmation dialogs for status changes
        $('form[action*="confirm"]').on('submit', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to confirm this booking?')) {
                this.submit();
            }
        });

        $('form[action*="complete"]').on('submit', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to mark this booking as completed?')) {
                this.submit();
            }
        });
    });

    // Helper function for alerts
    function showAlert(message, type = "info") {
        const alertColors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };

        const alertIcons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };

        const alertDiv = $(`
            <div class="fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white ${alertColors[type]} transition-all duration-300 transform translate-x-full">
                <div class="flex items-center">
                    <i class="fas ${alertIcons[type]} mr-2"></i>
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
</script>
@endpush

@endsection