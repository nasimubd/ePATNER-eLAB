@extends('super-admin.layouts.app')

@section('page-title', 'Add New Admin')
@section('page-description', 'Create a new system administrator')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Add New Admin</h1>
                    <p class="mt-1 text-sm text-gray-500">Create a new administrator account</p>
                </div>
                <a href="{{ route('super-admin.admins.index') }}"
                    class="text-gray-600 hover:text-gray-900 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back to Admins</span>
                </a>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('super-admin.admins.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    name="name"
                    id="name"
                    value="{{ old('name') }}"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 @error('name') border-red-300 @enderror"
                    placeholder="Enter admin's full name"
                    required>
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email"
                    name="email"
                    id="email"
                    value="{{ old('email') }}"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 @error('email') border-red-300 @enderror"
                    placeholder="Enter admin's email address"
                    required>
                @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Hospital Assignment -->
            <div>
                <label for="business_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Assign Hospital <span class="text-red-500">*</span>
                </label>
                <select name="business_id"
                    id="business_id"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 @error('business_id') border-red-300 @enderror"
                    required>
                    <option value="">Select a hospital</option>
                    @foreach($businesses as $business)
                    <option value="{{ $business->id }}" {{ old('business_id') == $business->id ? 'selected' : '' }}>
                        {{ $business->hospital_name }}
                    </option>
                    @endforeach
                </select>
                @error('business_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Select which hospital this admin will manage</p>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password"
                    name="password"
                    id="password"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 @error('password') border-red-300 @enderror"
                    placeholder="Enter a secure password"
                    required>
                @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Password must be at least 8 characters long</p>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <input type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500"
                    placeholder="Confirm the password"
                    required>
            </div>

            <!-- Send Welcome Email -->
            <div class="flex items-center">
                <input type="checkbox"
                    name="send_welcome_email"
                    id="send_welcome_email"
                    value="1"
                    {{ old('send_welcome_email', true) ? 'checked' : '' }}
                    class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <label for="send_welcome_email" class="ml-2 block text-sm text-gray-700">
                    Send welcome email with login credentials
                </label>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('super-admin.admins.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Cancel
                </a>
                <button type="submit"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Create Admin
                </button>
            </div>
        </form>
    </div>
</div>
@endsection