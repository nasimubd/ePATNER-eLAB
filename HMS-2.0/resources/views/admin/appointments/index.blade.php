@extends('admin.layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('admin.appointments.create') }}"
                class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-200">
                <i class="fas fa-plus mr-2"></i>
                New Appointment
            </a>
            <button onclick="exportAppointments()"
                class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl shadow-lg hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all duration-200">
                <i class="fas fa-download mr-2"></i>
                Export
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 border border-blue-200 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-600 text-sm font-semibold uppercase tracking-wide">Today's Total</p>
                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ $stats['today_total'] }}</p>
                </div>
                <div class="bg-blue-500 rounded-full p-3">
                    <i class="fas fa-calendar-day text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 border border-green-200 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-600 text-sm font-semibold uppercase tracking-wide">Completed Today</p>
                    <p class="text-3xl font-bold text-green-900 mt-2">{{ $stats['today_completed'] }}</p>
                </div>
                <div class="bg-green-500 rounded-full p-3">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 border border-purple-200 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-600 text-sm font-semibold uppercase tracking-wide">Upcoming</p>
                    <p class="text-3xl font-bold text-purple-900 mt-2">{{ $stats['upcoming'] }}</p>
                </div>
                <div class="bg-purple-500 rounded-full p-3">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-6 border border-orange-200 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-600 text-sm font-semibold uppercase tracking-wide">This Month</p>
                    <p class="text-3xl font-bold text-orange-900 mt-2">{{ $stats['this_month'] }}</p>
                </div>
                <div class="bg-orange-500 rounded-full p-3">
                    <i class="fas fa-calendar-alt text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-filter mr-2 text-gray-600"></i>
                Filters
            </h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('admin.appointments.index') }}" id="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                    <div class="col-span-1 lg:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <div class="relative">
                            <input type="text"
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                id="search"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Patient, doctor, complaint...">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                        <input type="date"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                            id="date_from"
                            name="date_from"
                            value="{{ request('date_from') }}">
                    </div>

                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                        <input type="date"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                            id="date_to"
                            name="date_to"
                            value="{{ request('date_to') }}">
                    </div>

                    <div>
                        <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-2">Doctor</label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                            id="doctor_id"
                            name="doctor_id">
                            <option value="">All Doctors</option>
                            @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                            id="status"
                            name="status">
                            <option value="">All Status</option>
                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>No Show</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-6">
                    <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-200">
                        <i class="fas fa-search mr-2"></i>
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.appointments.index') }}"
                        class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-list mr-2 text-gray-600"></i>
                Appointments List
            </h3>
        </div>

        @if($appointments->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase tracking-wider">Patient</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase tracking-wider">Doctor</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase tracking-wider">Fee</th>
                        <th class="px-4 py-2 text-center font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($appointments as $appointment)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-4 py-2 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">
                                {{ $appointment->appointment_date->format('M d, Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $appointment->appointment_time->format('h:i A') }}
                                <span class="bg-gray-100 text-gray-600 text-xs rounded-full px-2 py-0.5 ml-1">
                                    {{ $appointment->duration }}m
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $appointment->patient->name }}</div>
                            <div class="text-xs text-gray-500">{{ $appointment->patient->email }}</div>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $appointment->doctor->name }}</div>
                            <div class="text-xs text-gray-500">{{ $appointment->doctor->specialization }}</div>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            @php
                            $statusConfig = [
                            'scheduled' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fas fa-clock'],
                            'confirmed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fas fa-check'],
                            'completed' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fas fa-check-circle'],
                            'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fas fa-times'],
                            'no_show' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'icon' => 'fas fa-user-times']
                            ];
                            $config = $statusConfig[$appointment->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fas fa-question'];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $config['bg'] }} {{ $config['text'] }}">
                                <i class="{{ $config['icon'] }} mr-1"></i>
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">${{ number_format($appointment->consultation_fee, 2) }}</div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $appointment->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                <i class="fas fa-{{ $appointment->payment_status === 'paid' ? 'check' : 'clock' }} mr-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $appointment->payment_status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('admin.appointments.show', $appointment) }}"
                                    class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200"
                                    title="View Appointment">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>

                                @if($appointment->canBeEdited())
                                <a href="{{ route('admin.appointments.edit', $appointment) }}"
                                    class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors duration-200"
                                    title="Edit Appointment">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @endif

                                <!-- @if($appointment->status === 'scheduled')
                                <form method="POST" action="{{ route('admin.appointments.confirm', $appointment) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors duration-200"
                                        onclick="return confirm('Are you sure you want to confirm this appointment?')"
                                        title="Confirm Appointment">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif -->
                                <!-- 
                                @if(in_array($appointment->status, ['scheduled', 'confirmed']))
                                <form method="POST" action="{{ route('admin.appointments.complete', $appointment) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200"
                                        onclick="return confirm('Are you sure you want to mark this appointment as completed?')"
                                        title="Complete Appointment">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                </form> -->

                                <form method="POST" action="{{ route('admin.appointments.cancel', $appointment) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-colors duration-200"
                                        onclick="return confirm('Are you sure you want to cancel this appointment?')"
                                        title="Cancel Appointment">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif

                                @if($appointment->canBeDeleted())
                                <form method="POST" action="{{ route('admin.appointments.destroy', $appointment) }}"
                                    class="inline" onsubmit="return confirm('Are you sure you want to delete this appointment? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-200"
                                        title="Delete Appointment">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-semibold">{{ $appointments->firstItem() }}</span> to
                    <span class="font-semibold">{{ $appointments->lastItem() }}</span> of
                    <span class="font-semibold">{{ $appointments->total() }}</span> results
                </div>
                <div class="flex justify-center sm:justify-end">
                    {{ $appointments->links() }}
                </div>
            </div>
        </div>
        @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-calendar-times text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No appointments found</h3>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">
                There are no appointments matching your current filters. Try adjusting your search criteria or create a new appointment.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('admin.appointments.create') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Create New Appointment
                </a>
                <a href="{{ route('admin.appointments.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                    <i class="fas fa-refresh mr-2"></i>
                    Clear All Filters
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Quick Actions Floating Menu (Mobile) -->
<div class="fixed bottom-6 right-6 lg:hidden" x-data="{ open: false }">
    <div x-show="open" x-transition class="mb-4 space-y-3">
        <a href="{{ route('admin.appointments.create') }}"
            class="flex items-center justify-center w-12 h-12 bg-blue-500 text-white rounded-full shadow-lg hover:bg-blue-600 transition-all duration-200">
            <i class="fas fa-plus"></i>
        </a>
        <button onclick="exportAppointments()"
            class="flex items-center justify-center w-12 h-12 bg-green-500 text-white rounded-full shadow-lg hover:bg-green-600 transition-all duration-200">
            <i class="fas fa-download"></i>
        </button>
    </div>
    <button @click="open = !open"
        class="flex items-center justify-center w-14 h-14 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-full shadow-xl hover:from-blue-600 hover:to-blue-700 transform hover:scale-110 transition-all duration-200">
        <i class="fas fa-ellipsis-v" x-show="!open"></i>
        <i class="fas fa-times" x-show="open"></i>
    </button>
</div>
</div>

<!-- Hidden Form for Export -->
<form id="exportForm" method="GET" action="{{ route('admin.appointments.export') }}" style="display: none;">
    <input type="hidden" name="search" value="{{ request('search') }}">
    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
    <input type="hidden" name="doctor_id" value="{{ request('doctor_id') }}">
    <input type="hidden" name="status" value="{{ request('status') }}">
</form>
@endsection

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    // Export appointments
    function exportAppointments() {
        if (confirm('This will export all appointments matching your current filters to a CSV file. Continue?')) {
            document.getElementById('exportForm').submit();
        }
    }
</script>
@endpush