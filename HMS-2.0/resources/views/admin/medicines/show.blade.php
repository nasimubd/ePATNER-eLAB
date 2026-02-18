@extends('admin.layouts.app')

@section('page-title', 'Medicine Details')
@section('page-description', 'View detailed information about ' . $medicine->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @if($medicine->medicine_image)
                    <img src="{{ $medicine->image_url }}" alt="{{ $medicine->name }}" class="h-16 w-16 object-cover rounded-lg border border-gray-200">
                    @else
                    <div class="h-16 w-16 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $medicine->name }}</h1>
                        <div class="flex items-center space-x-4 mt-1">
                            @if($medicine->generic_name)
                            <span class="text-sm text-gray-600">{{ $medicine->generic_name }}</span>
                            @endif
                            @if($medicine->brand_name)
                            <span class="text-sm text-blue-600">({{ $medicine->brand_name }})</span>
                            @endif
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ ucfirst($medicine->medicine_type) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Quick Actions -->
                    <button onclick="openStockModal({{ $medicine->id }}, '{{ $medicine->name }}', {{ $medicine->stock_quantity }})" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 011 1v1a1 1 0 01-1 1v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7a1 1 0 01-1-1V5a1 1 0 011-1h4zM9 3v1h6V3H9zm2 8a1 1 0 102 0v2a1 1 0 11-2 0v-2z"></path>
                        </svg>
                        Update Stock
                    </button>
                    <a href="{{ route('admin.medicines.edit', $medicine) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Medicine
                    </a>
                    <a href="{{ route('admin.medicines.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Status Indicators -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center space-x-6">
                <!-- Active Status -->
                <div class="flex items-center">
                    @if($medicine->is_active)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Active
                    </span>
                    @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        Inactive
                    </span>
                    @endif
                </div>

                <!-- Stock Status -->
                <div class="flex items-center">
                    @if($medicine->stock_quantity <= 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Out of Stock
                        </span>
                        @elseif($medicine->is_low_stock)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Low Stock
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            In Stock
                        </span>
                        @endif
                </div>

                <!-- Prescription Required -->
                @if($medicine->prescription_required)
                <div class="flex items-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Prescription Required
                    </span>
                </div>
                @endif

                <!-- Expiry Warning -->
                @if($medicine->expiry_date && $medicine->expiry_date->isPast())
                <div class="flex items-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Expired
                    </span>
                </div>
                @elseif($medicine->expiry_date && $medicine->expiry_date->diffInDays(now()) <= 30)
                    <div class="flex items-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Expiring Soon
                    </span>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column - Main Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
            </div>
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Medicine Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $medicine->name }}</dd>
                    </div>
                    @if($medicine->generic_name)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Generic Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $medicine->generic_name }}</dd>
                    </div>
                    @endif
                    @if($medicine->brand_name)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Brand Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $medicine->brand_name }}</dd>
                    </div>
                    @endif
                    @if($medicine->manufacturer)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Manufacturer</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $medicine->manufacturer }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Medicine Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($medicine->medicine_type) }}</dd>
                    </div>
                    @if($medicine->strength)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Strength</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $medicine->strength }}</dd>
                    </div>
                    @endif
                    @if($medicine->barcode)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Barcode</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $medicine->barcode }}</dd>
                    </div>
                    @endif
                    @if($medicine->batch_number)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Batch Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $medicine->batch_number }}</dd>
                    </div>
                    @endif
                </dl>
                @if($medicine->description)
                <div class="mt-6">
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $medicine->description }}</dd>
                </div>
                @endif
            </div>
        </div>

        <!-- Pricing Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Pricing Information</h3>
            </div>
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Unit Price (Cost)</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">${{ number_format($medicine->unit_price, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Selling Price</dt>
                        <dd class="mt-1 text-lg font-semibold text-green-600">${{ number_format($medicine->selling_price, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Profit Margin</dt>
                        <dd class="mt-1 text-lg font-semibold text-blue-600">{{ $medicine->profit_margin }}%</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Medical Information -->
        @if($medicine->dosage_instructions || $medicine->side_effects || $medicine->storage_conditions)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Medical Information</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                @if($medicine->dosage_instructions)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Dosage Instructions</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $medicine->dosage_instructions }}</dd>
                </div>
                @endif
                @if($medicine->side_effects)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Side Effects</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $medicine->side_effects }}</dd>
                </div>
                @endif
                @if($medicine->storage_conditions)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Storage Conditions</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $medicine->storage_conditions }}</dd>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column - Stock & Dates -->
    <div class="space-y-6">
        <!-- Stock Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Stock Information</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="text-center">
                    <div class="text-3xl font-bold {{ $medicine->stock_quantity <= 0 ? 'text-red-600' : ($medicine->is_low_stock ? 'text-yellow-600' : 'text-green-600') }}">
                        {{ $medicine->stock_quantity }}
                    </div>
                    <div class="text-sm text-gray-500">Current Stock</div>
                </div>
                <div class="border-t border-gray-200 pt-4">
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Minimum Level</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $medicine->minimum_stock_level }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Stock Value</dt>
                            <dd class="text-sm font-medium text-gray-900">${{ number_format($medicine->stock_value, 2) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Date Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Date Information</h3>
            </div>
            <div class="px-6 py-4">
                <dl class="space-y-3">
                    @if($medicine->manufacturing_date)
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Manufacturing Date</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $medicine->manufacturing_date->format('M d, Y') }}</dd>
                    </div>
                    @endif
                    @if($medicine->expiry_date)
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Expiry Date</dt>
                        <dd class="text-sm font-medium {{ $medicine->expiry_date->isPast() ? 'text-red-600' : ($medicine->expiry_date->diffInDays(now()) <= 30 ? 'text-yellow-600' : 'text-gray-900') }}">
                            {{ $medicine->expiry_date->format('M d, Y') }}
                        </dd>
                    </div>
                    @if($medicine->expiry_date->isFuture())
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Days to Expiry</dt>
                        <dd class="text-sm font-medium {{ $medicine->expiry_date->diffInDays(now()) <= 30 ? 'text-yellow-600' : 'text-gray-900' }}">
                            {{ $medicine->expiry_date->diffInDays(now()) }} days
                        </dd>
                    </div>
                    @endif
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Added On</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $medicine->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Last Updated</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $medicine->updated_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
            </div>
            <div class="px-6 py-4 space-y-3">
                <button onclick="openStockModal({{ $medicine->id }}, '{{ $medicine->name }}', {{ $medicine->stock_quantity }})" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 011 1v1a1 1 0 01-1 1v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7a1 1 0 01-1-1V5a1 1 0 011-1h4zM9 3v1h6V3H9zm2 8a1 1 0 102 0v2a1 1 0 11-2 0v-2z"></path>
                    </svg>
                    Update Stock
                </button>

                <a href="{{ route('admin.medicines.edit', $medicine) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Medicine
                </a>

                @if($medicine->barcode)
                <button onclick="printBarcode('{{ $medicine->barcode }}', '{{ $medicine->name }}')" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Barcode
                </button>
                @endif

                <button onclick="toggleStatus({{ $medicine->id }}, {{ $medicine->is_active ? 'false' : 'true' }})" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium {{ $medicine->is_active ? 'text-red-700 hover:bg-red-50' : 'text-green-700 hover:bg-green-50' }} bg-white">
                    @if($medicine->is_active)
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                    </svg>
                    Deactivate
                    @else
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Activate
                    @endif
                </button>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Stock Update Modal -->
<div id="stockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="stockModalTitle">Update Stock</h3>
                <button onclick="closeStockModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="stockUpdateForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label for="current_stock" class="block text-sm font-medium text-gray-700 mb-2">Current Stock</label>
                    <input type="number" id="current_stock" readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50">
                </div>
                <div class="mb-4">
                    <label for="stock_action" class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                    <select id="stock_action" name="action" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Select Action</option>
                        <option value="add">Add Stock</option>
                        <option value="remove">Remove Stock</option>
                        <option value="set">Set Stock</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                    <input type="number" id="quantity" name="quantity" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                    <textarea id="reason" name="reason" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Reason for stock update..."></textarea>
                </div>
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" onclick="closeStockModal()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        Update Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Stock Modal Functions
    function openStockModal(medicineId, medicineName, currentStock) {
        document.getElementById('stockModalTitle').textContent = `Update Stock - ${medicineName}`;
        document.getElementById('current_stock').value = currentStock;
        document.getElementById('stockUpdateForm').action = `/admin/medicines/${medicineId}/stock`;
        document.getElementById('stockModal').classList.remove('hidden');
    }

    function closeStockModal() {
        document.getElementById('stockModal').classList.add('hidden');
        document.getElementById('stockUpdateForm').reset();
    }

    // Toggle Status Function
    function toggleStatus(medicineId, newStatus) {
        if (confirm(`Are you sure you want to ${newStatus ? 'activate' : 'deactivate'} this medicine?`)) {
            fetch(`/admin/medicines/${medicineId}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        is_active: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error updating medicine status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating medicine status');
                });
        }
    }

    // Print Barcode Function
    function printBarcode(barcode, medicineName) {
        // Open a new window for printing barcode
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
        <html>
            <head>
                <title>Barcode - ${medicineName}</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
                    .barcode { font-family: 'Courier New', monospace; font-size: 24px; letter-spacing: 2px; margin: 20px 0; }
                    .medicine-name { font-size: 14px; margin-bottom: 10px; }
                    @media print { body { margin: 0; padding: 10px; } }
                </style>
            </head>
            <body>
                <div class="medicine-name">${medicineName}</div>
                <div class="barcode">${barcode}</div>
                <script>
                    window.onload = function() {
                        window.print();
                        window.onafterprint = function() {
                            window.close();
                        }
                    }
</script>
</body>

</html>
`);
printWindow.document.close();
}

// Close modal when clicking outside
document.getElementById('stockModal').addEventListener('click', function(e) {
if (e.target === this) {
closeStockModal();
}
});

// Handle stock update form submission
document.getElementById('stockUpdateForm').addEventListener('submit', function(e) {
e.preventDefault();

const formData = new FormData(this);
const actionUrl = this.action;

fetch(actionUrl, {
method: 'POST',
headers: {
'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
},
body: formData
})
.then(response => response.json())
.then(data => {
if (data.success) {
closeStockModal();
location.reload();
} else {
alert(data.message || 'Error updating stock');
}
})
.catch(error => {
console.error('Error:', error);
alert('Error updating stock');
});
});
</script>
@endsection