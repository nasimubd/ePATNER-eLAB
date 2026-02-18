@extends('super-admin.layouts.app')

@section('page-title', 'Super Admin Dashboard')
@section('page-description', 'System overview and management')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-lg shadow-sm">
        <div class="px-6 py-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Welcome back, {{ auth()->user()->name }}!</h1>
                    <p class="mt-2 text-red-100">Here's what's happening in your system today.</p>
                </div>
                <div class="hidden md:block">
                    <svg class="w-16 h-16 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Hospitals -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Hospitals</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ \App\Models\Business::count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Hospitals -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Hospitals</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ \App\Models\Business::where('is_active', true)->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inactive Hospitals -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Inactive Hospitals</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ \App\Models\Business::where('is_active', false)->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Users -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">System Users</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ \App\Models\User::count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity and Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Hospitals -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Hospitals</h3>
            </div>
            <div class="p-6">
                @php
                $recentBusinesses = \App\Models\Business::latest()->take(5)->get();
                @endphp

                @if($recentBusinesses->count() > 0)
                <div class="space-y-4">
                    @foreach($recentBusinesses as $business)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            @if($business->hasLogo())
                            <img src="{{ $business->logo_base64 }}"
                                alt="{{ $business->hospital_name }}"
                                class="h-8 w-8 rounded-lg object-cover">
                            @else
                            <div class="h-8 w-8 bg-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $business->hospital_name }}</p>
                                <p class="text-xs text-gray-500">{{ $business->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($business->is_active)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Inactive
                            </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-6">
                    <a href="{{ route('super-admin.businesses.index') }}"
                        class="text-sm text-red-600 hover:text-red-500 font-medium">
                        View all hospitals →
                    </a>
                </div>
                @else
                <div class="text-center py-6">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hospitals</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating your first hospital.</p>
                    <div class="mt-6">
                        <a href="{{ route('super-admin.businesses.create') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                            Add Hospital
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-4">
                    <a href="{{ route('super-admin.businesses.create') }}"
                        class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Add New Hospital</p>
                            <p class="text-sm text-gray-500">Create a new hospital in the system</p>
                        </div>
                    </a>

                    <a href="{{ route('super-admin.businesses.index') }}"
                        class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Manage Hospitals</p>
                            <p class="text-sm text-gray-500">View and manage all hospitals</p>
                        </div>
                    </a>

                    <div class="flex items-center p-4 border border-gray-200 rounded-lg bg-gray-50 opacity-75">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Manage Users</p>
                            <p class="text-sm text-gray-400">Coming soon</p>
                        </div>
                    </div>

                    <div class="flex items-center p-4 border border-gray-200 rounded-lg bg-gray-50 opacity-75">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">System Reports</p>
                            <p class="text-sm text-gray-400">Coming soon</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">System Status</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Database</p>
                        <p class="text-sm text-gray-500">Connected</p>
                    </div>
                </div>

                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Application</p>
                        <p class="text-sm text-gray-500">Running</p>
                    </div>
                </div>

                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Storage</p>
                        <p class="text-sm text-gray-500">Available</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection