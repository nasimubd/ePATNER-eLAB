@extends('admin.layouts.app')

@section('page-title', 'Staff Management')
@section('page-description', 'Manage staff members and their roles')

@section('content')
<!-- Header -->
<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Staff Management</h1>
        <a href="{{ route('staff.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
            <i class="fas fa-plus"></i>
            <span>Add New Staff</span>
        </a>
    </div>

    <!-- Search and Filter Form -->
    <div class="px-6 py-4 bg-gray-50">
        <form method="GET" action="{{ route('staff.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Search Input -->
            <div class="md:col-span-2">
                <input type="text"
                    name="search"
                    placeholder="Search staff..."
                    value="{{ request('search') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Business Filter (Super Admin Only) -->
            @if(Auth::user()->hasRole('super-admin'))
            <div>
                <select name="business_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Businesses</option>
                    @foreach($businesses as $business)
                    <option value="{{ $business->id }}"
                        {{ request('business_id') == $business->id ? 'selected' : '' }}>
                        {{ $business->hospital_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Status Filter -->
            <div>
                <select name="status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex space-x-2">
                <button type="submit"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    Filter
                </button>
                <a href="{{ route('staff.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition duration-200">
                    Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
    <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <span>{{ session('success') }}</span>
    </div>
    <button onclick="this.parentElement.style.display='none'" class="text-green-700 hover:text-green-900">
        <i class="fas fa-times"></i>
    </button>
</div>
@endif

@if(session('error'))
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
    <div class="flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <span>{{ session('error') }}</span>
    </div>
    <button onclick="this.parentElement.style.display='none'" class="text-red-700 hover:text-red-900">
        <i class="fas fa-times"></i>
    </button>
</div>
@endif

<!-- Staff Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    @if(Auth::user()->hasRole('super-admin'))
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business</th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($staff as $member)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $member->employee_id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $member->full_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $member->email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $member->phone }}
                    </td>
                    @if(Auth::user()->hasRole('super-admin'))
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $member->business->hospital_name }}
                    </td>
                    @endif
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($member->user && $member->user->roles->isNotEmpty())
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $member->user->roles->first()->name }}
                        </span>
                        @else
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                            No Role
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $member->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $member->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <!-- View Button -->
                            <a href="{{ route('staff.show', $member) }}"
                                class="text-blue-600 hover:text-blue-900 p-1 rounded" title="View">
                                <i class="fas fa-eye"></i>
                            </a>

                            <!-- Edit Button -->
                            <a href="{{ route('staff.edit', $member) }}"
                                class="text-yellow-600 hover:text-yellow-900 p-1 rounded" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <!-- Toggle Status Button -->
                            <form action="{{ route('staff.toggle-status', $member) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="{{ $member->is_active ? 'text-gray-600 hover:text-gray-900' : 'text-green-600 hover:text-green-900' }} p-1 rounded"
                                    title="{{ $member->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="fas {{ $member->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                </button>
                            </form>

                            <!-- Reset Password Button -->
                            <form action="{{ route('staff.reset-password', $member) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="text-purple-600 hover:text-purple-900 p-1 rounded"
                                    title="Reset Password">
                                    <i class="fas fa-key"></i>
                                </button>
                            </form>

                            <!-- Delete Button -->
                            <form action="{{ route('staff.destroy', $member) }}" method="POST" class="inline"
                                onsubmit="return confirm('Are you sure you want to delete this staff member?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-600 hover:text-red-900 p-1 rounded" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ Auth::user()->hasRole('super-admin') ? '8' : '7' }}"
                        class="px-6 py-4 text-center text-gray-500">
                        <div class="flex flex-col items-center py-8">
                            <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                            <p class="text-lg">No staff members found.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($staff->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $staff->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection