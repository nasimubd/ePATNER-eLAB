@extends('super-admin.layouts.app')

@section('page-title', 'Letterhead Details')
@section('page-description', 'View letterhead information')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Letterhead Details</h3>
                <div class="flex space-x-2">
                    <a href="{{ route('super-admin.letterheads.edit', $letterhead) }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Edit</span>
                    </a>
                    <a href="{{ route('super-admin.letterheads.index') }}"
                        class="text-gray-600 hover:text-gray-900 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Back to Letterheads</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Business Information -->
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Business Information</h4>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Business</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $letterhead->business->hospital_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $letterhead->type === 'Invoice' ? 'bg-purple-100 text-purple-800' : 'bg-orange-100 text-orange-800' }}">
                                {{ $letterhead->type }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @if($letterhead->status === 'Active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Inactive
                            </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $letterhead->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Letterhead Information -->
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Letterhead Information</h4>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Business Name (Bangla)</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $letterhead->business_name_bangla }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Business Name (English)</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $letterhead->business_name_english }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Location</dt>
                        <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $letterhead->location }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Contacts -->
            @if($letterhead->getContactsArray())
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Contacts</h4>
                <div class="space-y-2">
                    @foreach($letterhead->getContactsArray() as $contact)
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span class="text-sm text-gray-900">{{ $contact }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Emails -->
            @if($letterhead->getEmailsArray())
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Emails</h4>
                <div class="space-y-2">
                    @foreach($letterhead->getEmailsArray() as $email)
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-sm text-gray-900">{{ $email }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="flex space-x-2">
                    <form action="{{ route('super-admin.letterheads.toggle-status', $letterhead) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="px-4 py-2 {{ $letterhead->status === 'Active' ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg flex items-center space-x-2 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($letterhead->status === 'Active')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                @endif
                            </svg>
                            <span>{{ $letterhead->status === 'Active' ? 'Deactivate' : 'Activate' }}</span>
                        </button>
                    </form>
                </div>
                <div class="flex space-x-2">
                    <form action="{{ route('super-admin.letterheads.destroy', $letterhead) }}" method="POST" class="inline"
                        onsubmit="return confirm('Are you sure you want to delete this letterhead?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg flex items-center space-x-2 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span>Delete</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection