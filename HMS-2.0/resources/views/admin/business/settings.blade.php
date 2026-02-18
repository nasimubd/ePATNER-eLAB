@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-2 sm:p-4">
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl overflow-hidden border border-white/20 mb-4">
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="text-center sm:text-left">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">
                            <svg class="inline-block w-8 h-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Business Settings
                        </h1>
                        <p class="text-blue-100 text-sm">Manage your business information and printing preferences</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                {{ session('error') }}
            </div>
        </div>
        @endif

        {{-- Settings Form --}}
        <div class="bg-white/90 backdrop-blur-sm shadow-xl rounded-2xl overflow-hidden border border-white/20">
            <form method="POST" action="{{ route('admin.business.settings.update') }}" class="p-6">
                @csrf
                @method('PUT')

                {{-- Business Information Section --}}
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Business Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Hospital Name --}}
                        <div>
                            <label for="hospital_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Hospital/Clinic Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="hospital_name" name="hospital_name"
                                value="{{ old('hospital_name', $business->hospital_name) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('hospital_name') border-red-500 @enderror"
                                required>
                            @error('hospital_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Contact Number --}}
                        <div>
                            <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="contact_number" name="contact_number"
                                value="{{ old('contact_number', $business->contact_number) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('contact_number') border-red-500 @enderror"
                                required>
                            @error('contact_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address
                            </label>
                            <input type="email" id="email" name="email"
                                value="{{ old('email', $business->email) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                            @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="mt-6">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <textarea id="address" name="address" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror"
                            required>{{ old('address', $business->address) }}</textarea>
                        @error('address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Printing Preferences Section --}}
                <div class="mb-8 border-t border-gray-200 pt-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Printing Preferences
                    </h2>

                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="flex items-start space-x-3">
                            <div class="flex items-center h-5">
                                <input type="checkbox" id="enable_a5_printing" name="enable_a5_printing" value="1"
                                    {{ old('enable_a5_printing', $business->enable_a5_printing) ? 'checked' : '' }}
                                    class="w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 focus:ring-2">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="enable_a5_printing" class="font-medium text-gray-700">
                                    Enable A5 Paper Printing
                                </label>
                                <p class="text-gray-500 mt-1">
                                    When enabled, invoices will be printed in A5 format suitable for official letterheads.
                                    When disabled, invoices will use POS receipt format for thermal printers.
                                </p>
                                <div class="mt-3 text-xs text-gray-600 bg-white p-3 rounded border-l-4 border-purple-400">
                                    <strong>Note:</strong>
                                    <ul class="list-disc list-inside mt-1 space-y-1">
                                        <li><strong>A5 Format:</strong> Professional layout for printing on official letterheads (148 × 210 mm)</li>
                                        <li><strong>POS Format:</strong> Compact receipt layout for thermal printers (80mm width)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.medical.invoices.index') }}"
                        class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Invoices
                    </a>

                    <button type="submit"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection