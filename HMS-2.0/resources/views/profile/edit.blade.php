@extends('admin.layouts.app')

@section('page-title', 'Profile Settings')
@section('page-description', 'Manage your account settings and preferences')

@section('content')
<div class="space-y-6">
    <!-- Profile Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('Profile Settings') }}</h1>
                <p class="text-gray-600">Manage your account settings and preferences</p>
            </div>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="max-w-2xl">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Profile Information</h2>
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <!-- Update Password -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="max-w-2xl">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Update Password</h2>
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <!-- Delete Account -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="max-w-2xl">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Delete Account</h2>
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection