@extends('admin.layouts.app')

@section('content')
@php
$user = Auth::user();
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-2 sm:p-4">
    <div class="max-w-4xl mx-auto">
        {{-- Header Section --}}
        <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl overflow-hidden border border-white/20 mb-6">
            <div class="bg-gradient-to-r from-blue-600 via-indigo-700 to-purple-700 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-center sm:text-left">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">
                            <i class="fas fa-calendar-check mr-2"></i>Booking Details
                        </h1>
                        <p class="text-blue-100 text-sm">Booking ID: #{{ $booking->id }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        {{-- Print Button --}}
                        <div class="mb-4">
                            <a href="{{ route('bookings.print', $booking) }}"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200">
                                <i class="fas fa-print mr-2"></i>
                                Print Receipt
                            </a>
                        </div>

                        {{-- Edit Button --}}
                        @if($booking->status == 'pending')
                        <a href="{{ route('bookings.edit', $booking) }}"
                            class="group relative overflow-hidden bg-gradient-to-r from-yellow-500 to-orange-600 hover:from-yellow-600 hover:to-orange-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <div class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                            <span class="relative flex items-center justify-center">
                                <i class="fas fa-edit mr-2"></i>
                                Edit
                            </span>
                        </a>
                        @endif

                        {{-- Back Button --}}
                        <a href="{{ route('bookings.index') }}"
                            class="group relative overflow-hidden bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <div class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                            <span class="relative flex items-center justify-center">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Main Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column - Main Details --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Patient Information --}}
                <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-white/20 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-4">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-user mr-2"></i>Patient Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user text-white text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900">{{ $booking->patient->full_name }}</h4>
                                <p class="text-gray-600">Patient ID: {{ $booking->patient->patient_id }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone</label>
                                <p class="text-gray-900">{{ $booking->patient->phone ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <p class="text-gray-900">{{ $booking->patient->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Service Information --}}
                <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-white/20 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-4">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-{{ $booking->booking_type == 'ward' ? 'bed' : 'procedures' }} mr-2"></i>Service Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Service Name</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $booking->bookable->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Service Type</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ 
                                    $booking->booking_type == 'ward' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' 
                                }}">
                                    <i class="fas fa-{{ $booking->booking_type == 'ward' ? 'bed' : 'procedures' }} mr-1"></i>
                                    {{ ucfirst($booking->booking_type) }}
                                </span>
                            </div>
                            @if($booking->bookable->description)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <p class="text-gray-900">{{ $booking->bookable->description }}</p>
                            </div>
                            @endif
                            @if($booking->booking_type == 'ot' && $booking->otRoom)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">OT Room</label>
                                <p class="text-gray-900">{{ $booking->otRoom->name }} ({{ $booking->otRoom->room_number }})</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Booking Schedule --}}
                <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-white/20 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-500 to-red-600 p-4">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-calendar-alt mr-2"></i>Schedule Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date</label>
                                <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</p>
                                <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($booking->booking_date)->format('l') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Time</label>
                                <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->booking_time)->format('h:i A') }}</p>
                            </div>
                            @if($booking->end_time)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Time</label>
                                <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</p>
                            </div>
                            @endif
                        </div>
                        @if($booking->preparation_time_minutes || $booking->cleanup_time_minutes)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($booking->preparation_time_minutes)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Preparation Time</label>
                                    <p class="text-gray-900">{{ $booking->preparation_time_minutes }} minutes</p>
                                </div>
                                @endif
                                @if($booking->cleanup_time_minutes)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Cleanup Time</label>
                                    <p class="text-gray-900">{{ $booking->cleanup_time_minutes }} minutes</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Notes --}}
                @if($booking->notes)
                <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-white/20 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-4">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-sticky-note mr-2"></i>Notes
                        </h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-900">{{ $booking->notes }}</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Right Column - Status & Actions --}}
            <div class="space-y-6">
                {{-- Status Card --}}
                <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-white/20 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-4">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>Status
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center mb-4">
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-medium {{ 
                                $booking->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                ($booking->status == 'confirmed' ? 'bg-blue-100 text-blue-800' : 
                                ($booking->status == 'completed' ? 'bg-green-100 text-green-800' : 
                                ($booking->status == 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) 
                            }}">
                                <i class="fas fa-{{ 
                                    $booking->status == 'pending' ? 'clock' : 
                                    ($booking->status == 'confirmed' ? 'check-circle' : 
                                    ($booking->status == 'completed' ? 'check-double' : 
                                    ($booking->status == 'cancelled' ? 'times-circle' : 'question-circle'))) 
                                }} mr-2"></i>
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>

                        {{-- Status Actions --}}
                        <div class="space-y-2">
                            @if($booking->status == 'pending')
                            <form action="{{ route('bookings.confirm', $booking) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300">
                                    <i class="fas fa-check mr-2"></i>Confirm Booking
                                </button>
                            </form>
                            <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" onclick="return confirm('Are you sure you want to cancel this booking?')"
                                    class="w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300">
                                    <i class="fas fa-times mr-2"></i>Cancel Booking
                                </button>
                            </form>
                            @elseif($booking->status == 'confirmed')
                            <form action="{{ route('bookings.complete', $booking) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300">
                                    <i class="fas fa-check-double mr-2"></i>Mark Complete
                                </button>
                            </form>

                            @endif
                        </div>
                    </div>
                </div>

                {{-- Fee Information --}}
                <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-white/20 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-4">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-money-bill-wave mr-2"></i>Fee Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Service Fee:</span>
                                <span class="font-semibold text-gray-900">৳{{ number_format($booking->service_fee, 2) }}</span>
                            </div>
                            @if($booking->booking_type == 'ot')
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Room Fee:</span>
                                <span class="font-semibold text-gray-900">৳{{ number_format($booking->bookable->room_fee ?? 0, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Equipment Fee:</span>
                                <span class="font-semibold text-gray-900">৳{{ number_format($booking->bookable->equipment_fee ?? 0, 2) }}</span>
                            </div>
                            @endif
                            <hr class="border-gray-300">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-900">Total Fee:</span>
                                <span class="text-2xl font-bold text-green-600">৳{{ number_format($booking->service_fee, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Booking Details --}}
                <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-white/20 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-500 to-gray-600 p-4">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-info mr-2"></i>Booking Details
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-gray-600">Created:</span>
                                <p class="font-medium">{{ $booking->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">Created By:</span>
                                <p class="font-medium">{{ $booking->createdBy->name ?? 'N/A' }}</p>
                            </div>
                            @if($booking->updated_at != $booking->created_at)
                            <div>
                                <span class="text-gray-600">Last Updated:</span>
                                <p class="font-medium">{{ $booking->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                            @endif
                            @if($booking->updatedBy)
                            <div>
                                <span class="text-gray-600">Updated By:</span>
                                <p class="font-medium">{{ $booking->updatedBy->name }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Print Template (Hidden) --}}
<div id="printTemplate" class="hidden">
    <div class="print-container">
        {{-- Header --}}
        <div class="print-header">
            <div class="clinic-logo">
                <div class="logo-circle">
                    <i class="fas fa-hospital-alt"></i>
                </div>
                <div class="clinic-info">
                    <h1>{{ config('app.name', 'Healthcare Clinic') }}</h1>
                    <p>Professional Healthcare Services</p>
                </div>
            </div>
            <div class="booking-id">
                <h2>BOOKING RECEIPT</h2>
                <p>ID: #{{ $booking->id }}</p>
            </div>
        </div>

        {{-- Patient Info --}}
        <div class="print-section patient-section">
            <div class="section-header">
                <i class="fas fa-user"></i>
                <h3>Patient Information</h3>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Name:</label>
                    <span>{{ $booking->patient->full_name }}</span>
                </div>
                <div class="info-item">
                    <label>Patient ID:</label>
                    <span>{{ $booking->patient->patient_id }}</span>
                </div>
                <div class="info-item">
                    <label>Phone:</label>
                    <span>{{ $booking->patient->phone ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <label>Email:</label>
                    <span>{{ $booking->patient->email ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        {{-- Service Info --}}
        <div class="print-section service-section">
            <div class="section-header">
                <i class="fas fa-{{ $booking->booking_type == 'ward' ? 'bed' : 'procedures' }}"></i>
                <h3>Service Details</h3>
            </div>
            <div class="info-grid">
                <div class="info-item full-width">
                    <label>Service:</label>
                    <span>{{ $booking->bookable->name }}</span>
                </div>
                <div class="info-item">
                    <label>Type:</label>
                    <span class="service-type {{ $booking->booking_type }}">{{ ucfirst($booking->booking_type) }}</span>
                </div>
                @if($booking->booking_type == 'ot' && $booking->otRoom)
                <div class="info-item">
                    <label>OT Room:</label>
                    <span>{{ $booking->otRoom->name }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Schedule Info --}}
        <div class="print-section schedule-section">
            <div class="section-header">
                <i class="fas fa-calendar-alt"></i>
                <h3>Schedule</h3>
            </div>
            <div class="schedule-details">
                <div class="date-time">
                    <div class="date">
                        <i class="fas fa-calendar"></i>
                        <span>{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</span>
                        <small>{{ \Carbon\Carbon::parse($booking->booking_date)->format('l') }}</small>
                    </div>
                    <div class="time">
                        <i class="fas fa-clock"></i>
                        <span>{{ \Carbon\Carbon::parse($booking->booking_time)->format('h:i A') }}</span>
                        @if($booking->end_time)
                        <small>to {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Fee Info --}}
        <div class="print-section fee-section">
            <div class="section-header">
                <i class="fas fa-money-bill-wave"></i>
                <h3>Fee Details</h3>
            </div>
            <div class="fee-breakdown">
                <div class="fee-item">
                    <span>Service Fee:</span>
                    <span>৳{{ number_format($booking->service_fee, 2) }}</span>
                </div>
                @if($booking->booking_type == 'ot')
                <div class="fee-item">
                    <span>Room Fee:</span>
                    <span>৳{{ number_format($booking->bookable->room_fee ?? 0, 2) }}</span>
                </div>
                <div class="fee-item">
                    <span>Equipment Fee:</span>
                    <span>৳{{ number_format($booking->bookable->equipment_fee ?? 0, 2) }}</span>
                </div>
                @endif
                <div class="fee-total">
                    <span>Total Amount:</span>
                    <span>৳{{ number_format($booking->service_fee, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="print-section status-section">
            <div class="status-badge {{ $booking->status }}">
                <i class="fas fa-{{ 
                    $booking->status == 'pending' ? 'clock' : 
                    ($booking->status == 'confirmed' ? 'check-circle' : 
                    ($booking->status == 'completed' ? 'check-double' : 
                    ($booking->status == 'cancelled' ? 'times-circle' : 'question-circle'))) 
                }}"></i>
                <span>{{ ucfirst($booking->status) }}</span>
            </div>
        </div>

        {{-- Notes --}}
        @if($booking->notes)
        <div class="print-section notes-section">
            <div class="section-header">
                <i class="fas fa-sticky-note"></i>
                <h3>Notes</h3>
            </div>
            <p>{{ $booking->notes }}</p>
        </div>
        @endif

        {{-- Footer --}}
        <div class="print-footer">
            <div class="footer-info">
                <p><strong>Important:</strong> Please arrive 15 minutes before your scheduled time.</p>
                <p>For any changes or cancellations, please contact us at least 24 hours in advance.</p>
            </div>
            <div class="footer-meta">
                <p>Printed on: {{ now()->format('M d, Y h:i A') }}</p>
                <p>Thank you for choosing our services!</p>
            </div>
        </div>
    </div>
</div>

@endsection

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

    /* Print Styles */
    .print-container {
        width: 13cm;
        height: 18cm;
        margin: 0;
        padding: 0.5cm;
        font-family: 'Arial', sans-serif;
        font-size: 10px;
        line-height: 1.3;
        color: #333;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }

    .print-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.15) 0%, transparent 50%),
            radial-gradient(circle at 40% 40%, rgba(120, 119, 198, 0.15) 0%, transparent 50%);
        pointer-events: none;
    }

    .print-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.8cm;
        padding: 0.3cm;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 0.3cm;
        box-shadow: 0 0.1cm 0.3cm rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 1;
    }

    .clinic-logo {
        display: flex;
        align-items: center;
        gap: 0.3cm;
    }

    .logo-circle {
        width: 1.2cm;
        height: 1.2cm;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
    }

    .clinic-info h1 {
        font-size: 14px;
        font-weight: bold;
        color: #333;
        margin: 0;
        line-height: 1.2;
    }

    .clinic-info p {
        font-size: 8px;
        color: #666;
        margin: 0;
    }

    .booking-id {
        text-align: right;
    }

    .booking-id h2 {
        font-size: 12px;
        font-weight: bold;
        color: #667eea;
        margin: 0;
        line-height: 1.2;
    }

    .booking-id p {
        font-size: 10px;
        color: #666;
        margin: 0;
    }

    .print-section {
        margin-bottom: 0.5cm;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 0.2cm;
        padding: 0.3cm;
        box-shadow: 0 0.05cm 0.2cm rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 1;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 0.2cm;
        margin-bottom: 0.3cm;
        padding-bottom: 0.1cm;
        border-bottom: 1px solid #e0e0e0;
    }

    .section-header i {
        color: #667eea;
        font-size: 12px;
    }

    .section-header h3 {
        font-size: 11px;
        font-weight: bold;
        color: #333;
        margin: 0;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.2cm;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.1cm;
    }

    .info-item.full-width {
        grid-column: 1 / -1;
    }

    .info-item label {
        font-size: 8px;
        color: #666;
        font-weight: bold;
        text-transform: uppercase;
    }

    .info-item span {
        font-size: 10px;
        color: #333;
        font-weight: 500;
    }

    .service-type {
        padding: 0.1cm 0.2cm;
        border-radius: 0.1cm;
        font-size: 8px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .service-type.ward {
        background: #e3f2fd;
        color: #1976d2;
    }

    .service-type.ot {
        background: #f3e5f5;
        color: #7b1fa2;
    }

    .schedule-details {
        display: flex;
        justify-content: center;
    }

    .date-time {
        display: flex;
        gap: 0.5cm;
        align-items: center;
    }

    .date,
    .time {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.1cm;
        padding: 0.2cm;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 0.2cm;
        min-width: 2cm;
    }

    .date i,
    .time i {
        font-size: 12px;
    }

    .date span,
    .time span {
        font-size: 10px;
        font-weight: bold;
    }

    .date small,
    .time small {
        font-size: 7px;
        opacity: 0.8;
    }

    .fee-breakdown {
        display: flex;
        flex-direction: column;
        gap: 0.2cm;
    }

    .fee-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.1cm 0;
        border-bottom: 1px dotted #ddd;
    }

    .fee-item span:first-child {
        font-size: 9px;
        color: #666;
    }

    .fee-item span:last-child {
        font-size: 10px;
        font-weight: bold;
        color: #333;
    }

    .fee-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.2cm;
        background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
        color: white;
        border-radius: 0.2cm;
        margin-top: 0.2cm;
    }

    .fee-total span:first-child {
        font-size: 11px;
        font-weight: bold;
    }

    .fee-total span:last-child {
        font-size: 14px;
        font-weight: bold;
    }

    .status-section {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0.2cm;
    }

    .status-badge {
        display: flex;
        align-items: center;
        gap: 0.2cm;
        padding: 0.2cm 0.4cm;
        border-radius: 0.2cm;
        font-weight: bold;
        font-size: 10px;
        text-transform: uppercase;
    }

    .status-badge.pending {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .status-badge.confirmed {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .status-badge.completed {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-badge.cancelled {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .notes-section p {
        font-size: 9px;
        color: #555;
        line-height: 1.4;
        margin: 0;
        padding: 0.2cm;
        background: #f8f9fa;
        border-radius: 0.1cm;
        border-left: 0.1cm solid #667eea;
    }

    .print-footer {
        position: absolute;
        bottom: 0.3cm;
        left: 0.5cm;
        right: 0.5cm;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 0.2cm;
        padding: 0.3cm;
        box-shadow: 0 -0.05cm 0.2cm rgba(0, 0, 0, 0.1);
    }

    .footer-info {
        margin-bottom: 0.2cm;
        padding-bottom: 0.2cm;
        border-bottom: 1px solid #e0e0e0;
    }

    .footer-info p {
        font-size: 8px;
        color: #666;
        margin: 0.1cm 0;
        line-height: 1.3;
    }

    .footer-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .footer-meta p {
        font-size: 7px;
        color: #999;
        margin: 0;
    }

    /* Print Media Query */
    @media print {
        body * {
            visibility: hidden;
        }

        #printTemplate,
        #printTemplate * {
            visibility: visible;
        }

        #printTemplate {
            position: absolute;
            left: 0;
            top: 0;
            width: 13cm;
            height: 18cm;
        }

        .print-container {
            width: 13cm;
            height: 18cm;
            margin: 0;
            padding: 0;
            box-shadow: none;
        }

        @page {
            size: 13cm 18cm;
            margin: 0;
        }
    }

    /* Screen styles for better visibility */
    @media screen {
        #printTemplate {
            position: fixed;
            top: -9999px;
            left: -9999px;
        }
    }

    /* Animation for status changes */
    .status-badge {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    /* Hover effects for buttons */
    button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .grid.grid-cols-1.lg\\:grid-cols-3 {
            grid-template-columns: 1fr;
        }

        .grid.grid-cols-1.md\\:grid-cols-2 {
            grid-template-columns: 1fr;
        }

        .grid.grid-cols-1.md\\:grid-cols-3 {
            grid-template-columns: 1fr;
        }

        .flex.flex-col.sm\\:flex-row {
            flex-direction: column;
        }

        .text-2xl.sm\\:text-3xl {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function printBooking() {
        // Show loading state
        const printBtn = document.querySelector('button[onclick="printBooking()"]');
        const originalContent = printBtn.innerHTML;
        printBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Preparing...';
        printBtn.disabled = true;

        // Small delay to show loading state
        setTimeout(() => {
            // Create a new window for printing
            const printWindow = window.open('', '_blank', 'width=500,height=700');

            // Get the print template content
            const printContent = document.getElementById('printTemplate').innerHTML;

            // Create the print document
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Booking Receipt - #{{ $booking->id }}</title>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                    <style>
                        ${document.querySelector('style').innerHTML}
                        
                        body {
                            margin: 0;
                            padding: 0;
                            font-family: Arial, sans-serif;
                            background: #f0f0f0;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            min-height: 100vh;
                        }
                        
                        .print-container {
                            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                            transform: scale(1);
                        }
                        
                        @media print {
                            body {
                                background: white;
                            }
                            
                            .print-container {
                                box-shadow: none;
                                transform: none;
                            }
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                </body>
                </html>
            `);

            printWindow.document.close();

            // Wait for content to load then print
            printWindow.onload = function() {
                setTimeout(() => {
                    printWindow.print();

                    // Close the print window after printing
                    setTimeout(() => {
                        printWindow.close();
                    }, 1000);
                }, 500);
            };

            // Restore button state
            setTimeout(() => {
                printBtn.innerHTML = originalContent;
                printBtn.disabled = false;
            }, 2000);

        }, 500);
    }

    // Auto-hide success/error messages
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            }, 5000);
        });

        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';

        // Add loading states to form buttons
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    const originalContent = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                    submitBtn.disabled = true;

                    // Re-enable after 5 seconds as fallback
                    setTimeout(() => {
                        submitBtn.innerHTML = originalContent;
                        submitBtn.disabled = false;
                    }, 5000);
                }
            });
        });

        // Add tooltips for better UX
        const tooltipElements = document.querySelectorAll('[title]');
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', function() {
                const tooltip = document.createElement('div');
                tooltip.className = 'tooltip';
                tooltip.textContent = this.getAttribute('title');
                tooltip.style.cssText = `
                    position: absolute;
                    background: rgba(0, 0, 0, 0.8);
                    color: white;
                    padding: 0.5rem;
                    border-radius: 0.25rem;
                    font-size: 0.75rem;
                    z-index: 1000;
                    pointer-events: none;
                    transform: translateY(-100%);
                    margin-top: -0.5rem;
                `;

                this.style.position = 'relative';
                this.appendChild(tooltip);
                this.removeAttribute('title');
                this.setAttribute('data-original-title', tooltip.textContent);
            });

            element.addEventListener('mouseleave', function() {
                const tooltip = this.querySelector('.tooltip');
                if (tooltip) {
                    this.setAttribute('title', this.getAttribute('data-original-title'));
                    this.removeAttribute('data-original-title');
                    tooltip.remove();
                }
            });
        });

        // Add confirmation dialogs with better styling
        const confirmButtons = document.querySelectorAll('button[onclick*="confirm"]');
        confirmButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const action = this.textContent.trim();
                const message = `Are you sure you want to ${action.toLowerCase()}?`;

                // Create custom confirmation dialog
                const confirmDialog = document.createElement('div');
                confirmDialog.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                confirmDialog.innerHTML = `
                    <div class="bg-white rounded-lg p-6 max-w-sm mx-4 transform transition-all duration-300 scale-95">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Confirm Action</h3>
                        </div>
                        <p class="text-gray-600 mb-6">${message}</p>
                        <div class="flex justify-end space-x-3">
                            <button class="cancel-btn px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                                Cancel
                            </button>
                            <button class="confirm-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                Confirm
                            </button>
                        </div>
                    </div>
                `;

                document.body.appendChild(confirmDialog);

                // Animate in
                setTimeout(() => {
                    confirmDialog.querySelector('div').classList.remove('scale-95');
                    confirmDialog.querySelector('div').classList.add('scale-100');
                }, 10);

                // Handle cancel
                confirmDialog.querySelector('.cancel-btn').addEventListener('click', () => {
                    confirmDialog.remove();
                });

                // Handle confirm
                confirmDialog.querySelector('.confirm-btn').addEventListener('click', () => {
                    confirmDialog.remove();
                    this.closest('form').submit();
                });

                // Handle backdrop click
                confirmDialog.addEventListener('click', (e) => {
                    if (e.target === confirmDialog) {
                        confirmDialog.remove();
                    }
                });
            });
        });

        // Add status change animations
        const statusBadge = document.querySelector('.status-badge');
        if (statusBadge) {
            statusBadge.addEventListener('animationend', function() {
                this.style.animation = 'none';
                setTimeout(() => {
                    this.style.animation = 'pulse 2s infinite';
                }, 100);
            });
        }

        // Add print preview functionality
        window.showPrintPreview = function() {
            const previewWindow = window.open('', '_blank', 'width=600,height=800');
            const printContent = document.getElementById('printTemplate').innerHTML;

            previewWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Print Preview - Booking #{{ $booking->id }}</title>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                    <style>
                        ${document.querySelector('style').innerHTML}
                        
                        body {
                            margin: 0;
                            padding: 20px;
                            font-family: Arial, sans-serif;
                            background: #f0f0f0;
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            min-height: 100vh;
                        }
                        
                        .preview-header {
                            background: white;
                            padding: 1rem;
                            border-radius: 0.5rem;
                            margin-bottom: 1rem;
                            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                            display: flex;
                            gap: 1rem;
                            align-items: center;
                        }
                        
                        .preview-header button {
                            padding: 0.5rem 1rem;
                            border: none;
                            border-radius: 0.25rem;
                            cursor: pointer;
                            font-weight: bold;
                            transition: all 0.2s;
                        }
                        
                        .print-btn {
                            background: #4CAF50;
                            color: white;
                        }
                        
                        .print-btn:hover {
                            background: #45a049;
                        }
                        
                        .close-btn {
                            background: #f44336;
                            color: white;
                        }
                        
                        .close-btn:hover {
                            background: #da190b;
                        }
                        
                        .print-container {
                            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                            transform: scale(1.2);
                            margin: 2rem 0;
                        }
                    </style>
                </head>
                <body>
                    <div class="preview-header">
                        <h2>Print Preview</h2>
                        <button class="print-btn" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <button class="close-btn" onclick="window.close()">
                            <i class="fas fa-times"></i> Close
                        </button>
                    </div>
                    ${printContent}
                </body>
                </html>
            `);

            previewWindow.document.close();
        };

        // Add smooth transitions for all interactive elements
        const interactiveElements = document.querySelectorAll('button, a, input, select, textarea');
        interactiveElements.forEach(element => {
            element.style.transition = 'all 0.2s ease-in-out';
        });

        // Add loading overlay for form submissions
        window.showLoadingOverlay = function(message = 'Processing...') {
            const overlay = document.createElement('div');
            overlay.id = 'loadingOverlay';
            overlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            overlay.innerHTML = `
                <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="text-gray-700 font-medium">${message}</span>
                </div>
            `;
            document.body.appendChild(overlay);
        };

        window.hideLoadingOverlay = function() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.remove();
            }
        };

        // Add error handling for print function
        window.onerror = function(msg, url, lineNo, columnNo, error) {
            console.error('Error: ', msg, url, lineNo, columnNo, error);
            hideLoadingOverlay();

            // Show user-friendly error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white p-4 rounded-lg shadow-lg z-50';
            errorDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>An error occurred. Please try again.</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(errorDiv);

            setTimeout(() => {
                if (errorDiv.parentNode) {
                    errorDiv.remove();
                }
            }, 5000);

            return true;
        };
    });

    // Add page visibility API to handle print completion
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            // Page became visible again (user might have finished printing)
            const printBtn = document.querySelector('button[onclick="printBooking()"]');
            if (printBtn && printBtn.disabled) {
                setTimeout(() => {
                    printBtn.innerHTML = '<i class="fas fa-print mr-2"></i>Print';
                    printBtn.disabled = false;
                }, 1000);
            }
        }
    });

    // Add print success callback
    window.addEventListener('afterprint', function() {
        // Show success message after printing
        const successDiv = document.createElement('div');
        successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
        successDiv.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>Print job sent successfully!</span>
            </div>
        `;
        document.body.appendChild(successDiv);

        // Animate in
        setTimeout(() => {
            successDiv.classList.remove('translate-x-full');
        }, 100);

        // Remove after 3 seconds
        setTimeout(() => {
            successDiv.classList.add('translate-x-full');
            setTimeout(() => {
                if (successDiv.parentNode) {
                    successDiv.remove();
                }
            }, 300);
        }, 3000);
    });
</script>
@endpush