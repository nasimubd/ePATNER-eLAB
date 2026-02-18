@extends('super-admin.layouts.app')

@section('page-title', 'Subscription Settings')
@section('page-description', 'Manage monthly subscription fees and payment settings')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Subscription Settings</h1>
                <p class="text-gray-600 mt-1">Configure monthly fees and payment options</p>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="bg-white shadow-sm rounded-lg">
        <div class="p-6">
            <form action="{{ route('super-admin.subscriptions.update-settings') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Monthly Fee -->
                <div>
                    <label for="monthly_fee" class="block text-sm font-medium text-gray-700 mb-2">
                        Monthly Subscription Fee (৳)
                    </label>
                    <input type="number" id="monthly_fee" name="monthly_fee"
                        value="{{ old('monthly_fee', $monthlyFee) }}"
                        step="0.01" min="0" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <p class="mt-1 text-sm text-gray-600">This fee will be charged monthly for each business subscription</p>
                    @error('monthly_fee')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment QR Path -->
                <div>
                    <label for="payment_qr_path" class="block text-sm font-medium text-gray-700 mb-2">
                        Payment QR Code Path
                    </label>
                    <input type="text" id="payment_qr_path" name="payment_qr_path"
                        value="{{ old('payment_qr_path', $paymentQrPath) }}"
                        placeholder="images/Payment-QR.png"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <p class="mt-1 text-sm text-gray-600">Path to the QR code image file in the public directory</p>
                    @error('payment_qr_path')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- QR Preview -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">QR Code Preview</h3>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <img src="{{ asset($paymentQrPath) }}"
                                alt="Payment QR Code"
                                class="w-24 h-24 object-contain border border-gray-200 rounded"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <div class="hidden w-24 h-24 border border-gray-200 rounded bg-gray-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 21h.01M12 3h.01M21 12h.01M3 12h.01M21 21h.01M3 3h.01"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-600">
                                This QR code will be shown to businesses when their subscription expires.
                                Make sure the image file exists at <code class="bg-gray-200 px-1 py-0.5 rounded text-xs">{{ $paymentQrPath }}</code>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-4 border-t border-gray-200">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update Settings
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Active Businesses -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Businesses</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Business::where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Inactive Businesses -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Inactive Businesses</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Business::where('is_active', false)->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending Payments</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Payment::pending()->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection