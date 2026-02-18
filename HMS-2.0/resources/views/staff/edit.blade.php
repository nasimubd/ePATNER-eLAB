@extends('admin.layouts.app')

@section('page-title', 'Edit Staff Member')
@section('page-description', 'Update staff member information and settings')

@section('content')
<!-- Header -->
<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Edit Staff Member</h1>
        <div class="flex space-x-2">
            <a href="{{ route('staff.show', $staff) }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <i class="fas fa-eye"></i>
                <span>View</span>
            </a>
            <a href="{{ route('staff.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <i class="fas fa-arrow-left"></i>
                <span>Back to List</span>
            </a>
        </div>
    </div>
</div>

<!-- Error Messages -->
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

<!-- Form -->
<div class="bg-white rounded-lg shadow-md">
    <form method="POST" action="{{ route('staff.update', $staff) }}" class="p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Business Selection -->
            <div class="md:col-span-2">
                <label for="business_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Business <span class="text-red-500">*</span>
                </label>
                <select id="business_id"
                    name="business_id"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('business_id') border-red-500 @enderror">
                    <option value="">Select Business</option>
                    @foreach($businesses as $business)
                    <option value="{{ $business->id }}"
                        {{ old('business_id', $staff->business_id) == $business->id ? 'selected' : '' }}>
                        {{ $business->hospital_name }}
                    </option>
                    @endforeach
                </select>
                @error('business_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Employee ID -->
            <div>
                <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Employee ID <span class="text-red-500">*</span>
                </label>
                <input id="employee_id"
                    type="text"
                    name="employee_id"
                    value="{{ old('employee_id', $staff->employee_id) }}"
                    required
                    placeholder="e.g., EMP001, DOC001"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('employee_id') border-red-500 @enderror">
                @error('employee_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role Selection -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                    Role <span class="text-red-500">*</span>
                </label>
                <select id="role"
                    name="role"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('role') border-red-500 @enderror">
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}"
                        {{ old('role', $staff->user?->roles?->first()?->name) == $role->name ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                    @endforeach
                </select>
                @error('role')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- First Name -->
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                    First Name <span class="text-red-500">*</span>
                </label>
                <input id="first_name"
                    type="text"
                    name="first_name"
                    value="{{ old('first_name', $staff->first_name) }}"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('first_name') border-red-500 @enderror">
                @error('first_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Last Name -->
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Last Name <span class="text-red-500">*</span>
                </label>
                <input id="last_name"
                    type="text"
                    name="last_name"
                    value="{{ old('last_name', $staff->last_name) }}"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('last_name') border-red-500 @enderror">
                @error('last_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $staff->email) }}"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                    Phone <span class="text-red-500">*</span>
                </label>
                <input id="phone"
                    type="text"
                    name="phone"
                    value="{{ old('phone', $staff->phone) }}"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="md:col-span-2">
                <label class="flex items-center">
                    <input type="checkbox"
                        name="is_active"
                        value="1"
                        {{ old('is_active', $staff->is_active) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('staff.show', $staff) }}"
                class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition duration-200">
                Cancel
            </a>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                Update Staff Member
            </button>
        </div>

        <!-- Current Info Display -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Current Information:</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                <div>
                    <span class="font-medium">Created:</span>
                    {{ $staff->created_at->format('M d, Y') }}
                </div>
                <div>
                    <span class="font-medium">Last Updated:</span>
                    {{ $staff->updated_at->format('M d, Y') }}
                </div>
                <div>
                    <span class="font-medium">Current Status:</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $staff->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $staff->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Quick Actions -->
<div class="mt-6 bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
    <div class="flex flex-wrap gap-4">
        <!-- Toggle Status -->
        <form action="{{ route('staff.toggle-status', $staff) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit"
                class="bg-{{ $staff->is_active ? 'yellow' : 'green' }}-600 hover:bg-{{ $staff->is_active ? 'yellow' : 'green' }}-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <i class="fas fa-{{ $staff->is_active ? 'ban' : 'check' }}"></i>
                <span>{{ $staff->is_active ? 'Deactivate' : 'Activate' }}</span>
            </button>
        </form>

        <!-- Reset Password -->
        <form action="{{ route('staff.reset-password', $staff) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit"
                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200"
                onclick="return confirm('Are you sure you want to reset this staff member\'s password?')">
                <i class="fas fa-key"></i>
                <span>Reset Password</span>
            </button>
        </form>

        <!-- Delete Staff -->
        <form action="{{ route('staff.destroy', $staff) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200"
                onclick="return confirm('Are you sure you want to delete this staff member? This action cannot be undone.')">
                <i class="fas fa-trash"></i>
                <span>Delete Staff</span>
            </button>
        </form>
    </div>
</div>
@endsection