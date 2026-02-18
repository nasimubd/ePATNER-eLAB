@extends('super-admin.layouts.app')

@section('page-title', 'Add New Letterhead')
@section('page-description', 'Create a new letterhead for invoices or lab reports')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Add New Letterhead</h3>
                <a href="{{ route('super-admin.letterheads.index') }}"
                    class="text-gray-600 hover:text-gray-900 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back to Letterheads</span>
                </a>
            </div>
        </div>

        <form action="{{ route('super-admin.letterheads.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Business Selection -->
            <div>
                <label for="business_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Business <span class="text-red-500">*</span>
                </label>
                <select id="business_id"
                    name="business_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 @error('business_id') border-red-500 @enderror"
                    required>
                    <option value="">Select a business</option>
                    @foreach($businesses as $business)
                    <option value="{{ $business->id }}" {{ old('business_id') == $business->id ? 'selected' : '' }}>
                        {{ $business->hospital_name }}
                    </option>
                    @endforeach
                </select>
                @error('business_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Business Name (Bangla) -->
            <div>
                <label for="business_name_bangla" class="block text-sm font-medium text-gray-700 mb-2">
                    Business Name (Bangla) <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    id="business_name_bangla"
                    name="business_name_bangla"
                    value="{{ old('business_name_bangla') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 @error('business_name_bangla') border-red-500 @enderror"
                    placeholder="Enter business name in Bangla"
                    required>
                @error('business_name_bangla')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Business Name (English) -->
            <div>
                <label for="business_name_english" class="block text-sm font-medium text-gray-700 mb-2">
                    Business Name (English) <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    id="business_name_english"
                    name="business_name_english"
                    value="{{ old('business_name_english') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 @error('business_name_english') border-red-500 @enderror"
                    placeholder="Enter business name in English"
                    required>
                @error('business_name_english')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                    Location <span class="text-red-500">*</span>
                </label>
                <textarea id="location"
                    name="location"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 @error('location') border-red-500 @enderror"
                    placeholder="Enter business location/address"
                    required>{{ old('location') }}</textarea>
                @error('location')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                    Type <span class="text-red-500">*</span>
                </label>
                <select id="type"
                    name="type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 @error('type') border-red-500 @enderror"
                    required>
                    <option value="">Select type</option>
                    <option value="Invoice" {{ old('type') == 'Invoice' ? 'selected' : '' }}>Invoice</option>
                    <option value="Lab Report" {{ old('type') == 'Lab Report' ? 'selected' : '' }}>Lab Report</option>
                </select>
                @error('type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contacts -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Contacts
                </label>
                <div id="contacts-container">
                    @if(old('contacts'))
                    @foreach(old('contacts') as $index => $contact)
                    <div class="contact-item flex space-x-2 mb-2">
                        <input type="text"
                            name="contacts[]"
                            value="{{ $contact }}"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                            placeholder="Enter contact number">
                        <button type="button" class="remove-contact px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    @endforeach
                    @endif
                </div>
                <button type="button" id="add-contact" class="mt-2 px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    Add Contact
                </button>
                @error('contacts.*')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Emails -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Emails
                </label>
                <div id="emails-container">
                    @if(old('emails'))
                    @foreach(old('emails') as $index => $email)
                    <div class="email-item flex space-x-2 mb-2">
                        <input type="email"
                            name="emails[]"
                            value="{{ $email }}"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                            placeholder="Enter email address">
                        <button type="button" class="remove-email px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    @endforeach
                    @endif
                </div>
                <button type="button" id="add-email" class="mt-2 px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    Add Email
                </button>
                @error('emails.*')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <div class="flex items-center">
                    <input id="status_active"
                        name="status"
                        type="radio"
                        value="Active"
                        {{ old('status', 'Inactive') == 'Active' ? 'checked' : '' }}
                        class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                    <label for="status_active" class="ml-2 block text-sm text-gray-900">
                        Active
                    </label>
                </div>
                <div class="flex items-center mt-2">
                    <input id="status_inactive"
                        name="status"
                        type="radio"
                        value="Inactive"
                        {{ old('status', 'Inactive') == 'Inactive' ? 'checked' : '' }}
                        class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                    <label for="status_inactive" class="ml-2 block text-sm text-gray-900">
                        Inactive
                    </label>
                </div>
                <p class="mt-1 text-sm text-gray-500">Only one active letterhead per type is allowed per business</p>
                @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('super-admin.letterheads.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Cancel
                </a>
                <button type="submit"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Create Letterhead
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Add/Remove Contacts
    document.getElementById('add-contact').addEventListener('click', function() {
        const container = document.getElementById('contacts-container');
        const contactItem = document.createElement('div');
        contactItem.className = 'contact-item flex space-x-2 mb-2';
        contactItem.innerHTML = `
            <input type="text" name="contacts[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500" placeholder="Enter contact number">
            <button type="button" class="remove-contact px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        `;
        container.appendChild(contactItem);
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-contact')) {
            e.target.closest('.contact-item').remove();
        }
    });

    // Add/Remove Emails
    document.getElementById('add-email').addEventListener('click', function() {
        const container = document.getElementById('emails-container');
        const emailItem = document.createElement('div');
        emailItem.className = 'email-item flex space-x-2 mb-2';
        emailItem.innerHTML = `
            <input type="email" name="emails[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500" placeholder="Enter email address">
            <button type="button" class="remove-email px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        `;
        container.appendChild(emailItem);
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-email')) {
            e.target.closest('.email-item').remove();
        }
    });
</script>
@endsection