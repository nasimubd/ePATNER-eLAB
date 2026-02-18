@extends('super-admin.layouts.app')

@section('page-title', 'Edit Admin')
@section('page-description', 'Update administrator information')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Edit Admin</h1>
                    <p class="mt-1 text-sm text-gray-500">Update {{ $admin->name }}'s information</p>
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
        <form action="{{ route('super-admin.admins.update', $admin) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $admin->name) }}"
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
                    value="{{ old('email', $admin->email) }}"
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
                    <option value="{{ $business->id }}" {{ old('business_id', $admin->business_id) == $business->id ? 'selected' : '' }}>
                        {{ $business->hospital_name }}
                    </option>
                    @endforeach
                </select>
                @error('business_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Select which hospital this admin will manage</p>
            </div>

            <!-- Password Section -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password</h3>
                <p class="text-sm text-gray-500 mb-4">Leave password fields empty to keep current password</p>

                <!-- New Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        New Password
                    </label>
                    <input type="password"
                        name="password"
                        id="password"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 @error('password') border-red-300 @enderror"
                        placeholder="Enter new password (optional)">
                    @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm New Password
                    </label>
                    <input type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500"
                        placeholder="Confirm new password">
                </div>
            </div>

            <!-- Account Status -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Account Status</h3>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Email Verification</p>
                            <p class="text-sm text-gray-500">
                                @if($admin->email_verified_at)
                                Verified on {{ $admin->email_verified_at->format('M d, Y \a\t g:i A') }}
                                @else
                                Email not verified
                                @endif
                            </p>
                        </div>
                        <div>
                            @if($admin->email_verified_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Verified
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Account Created</p>
                            <p class="text-sm text-gray-500">{{ $admin->created_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>

                    @if($admin->updated_at != $admin->created_at)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Last Updated</p>
                            <p class="text-sm text-gray-500">{{ $admin->updated_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('super-admin.admins.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Cancel
                </a>
                <button type="submit"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Update Admin
                </button>
            </div>
        </form>
    </div>
</div>
@endsection