@extends('admin.layouts.app')

@section('content')
@php
$user = Auth::user();
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-2 sm:p-4">
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl overflow-hidden border border-white/20 mb-6">
            <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-center sm:text-left">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">
                            <i class="fas fa-plus-circle mr-2"></i>Create OT Service
                        </h1>
                        <p class="text-blue-100 text-sm">Add a new operating theater service</p>
                    </div>

                    <div class="w-full sm:w-auto">
                        <a href="{{ route('admin.ot-services.index') }}"
                            class="group relative overflow-hidden bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto flex items-center justify-center">
                            <span class="relative flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to List
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Section --}}
        <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-lg border border-white/20 overflow-hidden">
            <form action="{{ route('admin.ot-services.store') }}" method="POST" id="otServiceForm">
                @csrf

                <div class="p-6">
                    {{-- Basic Information Section --}}
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-info-circle text-white text-sm"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">Basic Information</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Service Name --}}
                            <div class="space-y-2">
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-procedures mr-1 text-blue-500"></i>Service Name *
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('name') border-red-500 @enderror"
                                    placeholder="e.g., Appendectomy, Cardiac Surgery">
                                @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Complexity Level --}}
                            <div class="space-y-2">
                                <label for="complexity_level" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-layer-group mr-1 text-purple-500"></i>Complexity Level *
                                </label>
                                <select id="complexity_level" name="complexity_level" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('complexity_level') border-red-500 @enderror">
                                    <option value="">Select Complexity</option>
                                    <option value="minor" {{ old('complexity_level') == 'minor' ? 'selected' : '' }}>Minor</option>
                                    <option value="major" {{ old('complexity_level') == 'major' ? 'selected' : '' }}>Major</option>
                                    <option value="critical" {{ old('complexity_level') == 'critical' ? 'selected' : '' }}>Critical</option>
                                </select>
                                @error('complexity_level')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="space-y-2">
                                <label for="status" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-toggle-on mr-1 text-green-500"></i>Status *
                                </label>
                                <select id="status" name="status" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('status') border-red-500 @enderror">
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="space-y-2 md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-file-alt mr-1 text-gray-500"></i>Description
                                </label>
                                <textarea id="description" name="description" rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('description') border-red-500 @enderror"
                                    placeholder="Detailed description of the OT service...">{{ old('description') }}</textarea>
                                @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Fee Structure Section --}}
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-money-bill-wave text-white text-sm"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">Fee Structure</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Base Fee --}}
                            <div class="space-y-2">
                                <label for="base_fee" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-user-md mr-1 text-blue-500"></i>Base Service Fee (৳) *
                                </label>
                                <input type="number" id="base_fee" name="base_fee" value="{{ old('base_fee') }}" required min="0" step="0.01"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('base_fee') border-red-500 @enderror"
                                    placeholder="0.00">
                                @error('base_fee')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Room Fee --}}
                            <div class="space-y-2">
                                <label for="room_fee" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-bed mr-1 text-green-500"></i>Room Fee (৳) *
                                </label>
                                <input type="number" id="room_fee" name="room_fee" value="{{ old('room_fee') }}" required min="0" step="0.01"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('room_fee') border-red-500 @enderror"
                                    placeholder="0.00">
                                @error('room_fee')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Equipment Fee --}}
                            <div class="space-y-2">
                                <label for="equipment_fee" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-tools mr-1 text-yellow-500"></i>Equipment Fee (৳) *
                                </label>
                                <input type="number" id="equipment_fee" name="equipment_fee" value="{{ old('equipment_fee') }}" required min="0" step="0.01"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('equipment_fee') border-red-500 @enderror"
                                    placeholder="0.00">
                                @error('equipment_fee')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Total Fee Display --}}
                        <div class="mt-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-900">
                                    <i class="fas fa-calculator mr-2 text-green-600"></i>Total Fee
                                </span>
                                <span id="totalFee" class="text-2xl font-bold text-green-600">৳0.00</span>
                            </div>
                        </div>
                    </div>

                    {{-- Time Management Section --}}
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-600 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-clock text-white text-sm"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">Time Management</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Estimated Duration --}}
                            <div class="space-y-2">
                                <label for="estimated_duration_minutes" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-hourglass-half mr-1 text-blue-500"></i>Estimated Duration (minutes) *
                                </label>
                                <input type="number" id="estimated_duration_minutes" name="estimated_duration_minutes" value="{{ old('estimated_duration_minutes') }}" required min="1"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('estimated_duration_minutes') border-red-500 @enderror"
                                    placeholder="60">
                                @error('estimated_duration_minutes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Preparation Time --}}
                            <div class="space-y-2">
                                <label for="preparation_time_minutes" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-play mr-1 text-green-500"></i>Preparation Time (minutes) *
                                </label>
                                <input type="number" id="preparation_time_minutes" name="preparation_time_minutes" value="{{ old('preparation_time_minutes') }}" required min="0"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('preparation_time_minutes') border-red-500 @enderror"
                                    placeholder="15">
                                @error('preparation_time_minutes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Cleanup Time --}}
                            <div class="space-y-2">
                                <label for="cleanup_time_minutes" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-broom mr-1 text-yellow-500"></i>Cleanup Time (minutes) *
                                </label>
                                <input type="number" id="cleanup_time_minutes" name="cleanup_time_minutes" value="{{ old('cleanup_time_minutes') }}" required min="0"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('cleanup_time_minutes') border-red-500 @enderror"
                                    placeholder="15">
                                @error('cleanup_time_minutes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Total Time Display --}}
                        <div class="mt-4 p-4 bg-gradient-to-r from-orange-50 to-red-50 rounded-lg border border-orange-200">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-900">
                                    <i class="fas fa-stopwatch mr-2 text-orange-600"></i>Total Time Required
                                </span>
                                <span id="totalTime" class="text-2xl font-bold text-orange-600">0 minutes</span>
                            </div>
                        </div>
                    </div>

                    {{-- Equipment Requirements Section --}}
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-tools text-white text-sm"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">Equipment Requirements</h2>
                        </div>

                        <div class="space-y-4">
                            {{-- Required Equipment --}}
                            <div class="space-y-2">
                                <label for="required_equipment" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-wrench mr-1 text-purple-500"></i>Required Equipment
                                </label>
                                <div id="equipmentContainer" class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <input type="text" name="required_equipment[]"
                                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                            placeholder="e.g., Ventilator, ECG Monitor">
                                        <button type="button" onclick="removeEquipmentItem(this)"
                                            class="px-3 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" onclick="addEquipmentItem()"
                                    class="mt-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                    <i class="fas fa-plus mr-1"></i>Add Equipment
                                </button>
                            </div>

                            {{-- Required Staff --}}
                            <div class="space-y-2">
                                <label for="required_staff" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-user-friends mr-1 text-blue-500"></i>Required Staff
                                </label>
                                <div id="staffContainer" class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <input type="text" name="required_staff[]"
                                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                            placeholder="e.g., Anesthesiologist, Surgical Nurse">
                                        <button type="button" onclick="removeStaffItem(this)"
                                            class="px-3 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" onclick="addStaffItem()"
                                    class="mt-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                                    <i class="fas fa-plus mr-1"></i>Add Staff
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                    <a href="{{ route('admin.ot-services.index') }}"
                        class="w-full sm:w-auto px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 text-center">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>

                    <button type="submit" id="submitBtn"
                        class="w-full sm:w-auto px-6 py-3 border border-transparent rounded-lg text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-save mr-2"></i>
                        Create OT Service
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Error Messages Section --}}
@if ($errors->any())
<div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
    <div class="flex items-center mb-2">
        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
        <h3 class="text-lg font-medium text-red-800">Please fix the following errors:</h3>
    </div>
    <ul class="list-disc list-inside text-red-700 space-y-1">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Success Message Section --}}
@if (session('success'))
<div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
    <div class="flex items-center">
        <i class="fas fa-check-circle text-green-500 mr-2"></i>
        <p class="text-green-800">{{ session('success') }}</p>
    </div>
</div>
@endif

{{-- Error Message Section --}}
@if (session('error'))
<div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
    <div class="flex items-center">
        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
        <p class="text-red-800">{{ session('error') }}</p>
    </div>
</div>
@endif


@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .backdrop-blur-sm {
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }

    .bg-white\/90 {
        background: rgba(255, 255, 255, 0.9);
    }

    .bg-white\/80 {
        background: rgba(255, 255, 255, 0.8);
    }

    .focus\:ring-2:focus {
        outline: 2px solid transparent;
        outline-offset: 2px;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Calculate total fee
        function calculateTotal() {
            const baseFee = parseFloat(document.getElementById('base_fee').value) || 0;
            const roomFee = parseFloat(document.getElementById('room_fee').value) || 0;
            const equipmentFee = parseFloat(document.getElementById('equipment_fee').value) || 0;

            const total = baseFee + roomFee + equipmentFee;
            document.getElementById('totalFee').textContent = '৳' + total.toFixed(2);
        }

        // Calculate total time
        function calculateTotalTime() {
            const estimatedDuration = parseInt(document.getElementById('estimated_duration_minutes').value) || 0;
            const preparationTime = parseInt(document.getElementById('preparation_time_minutes').value) || 0;
            const cleanupTime = parseInt(document.getElementById('cleanup_time_minutes').value) || 0;

            const total = estimatedDuration + preparationTime + cleanupTime;
            document.getElementById('totalTime').textContent = total + ' minutes';
        }

        // Add event listeners for fee calculation
        document.getElementById('base_fee').addEventListener('input', calculateTotal);
        document.getElementById('room_fee').addEventListener('input', calculateTotal);
        document.getElementById('equipment_fee').addEventListener('input', calculateTotal);

        // Add event listeners for time calculation
        document.getElementById('estimated_duration_minutes').addEventListener('input', calculateTotalTime);
        document.getElementById('preparation_time_minutes').addEventListener('input', calculateTotalTime);
        document.getElementById('cleanup_time_minutes').addEventListener('input', calculateTotalTime);

        // Initialize calculations
        calculateTotal();
        calculateTotalTime();
    });

    function addEquipmentItem() {
        const container = document.getElementById('equipmentContainer');
        const div = document.createElement('div');
        div.className = 'flex items-center space-x-2';
        div.innerHTML = `
        <input type="text" name="required_equipment[]" 
            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
            placeholder="e.g., Ventilator, ECG Monitor">
        <button type="button" onclick="removeEquipmentItem(this)" 
            class="px-3 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
            <i class="fas fa-trash"></i>
        </button>
    `;
        container.appendChild(div);
    }

    function removeEquipmentItem(button) {
        const container = document.getElementById('equipmentContainer');
        if (container.children.length > 1) {
            button.closest('div').remove();
        }
    }

    function addStaffItem() {
        const container = document.getElementById('staffContainer');
        const div = document.createElement('div');
        div.className = 'flex items-center space-x-2';
        div.innerHTML = `
        <input type="text" name="required_staff[]" 
            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
            placeholder="e.g., Anesthesiologist, Surgical Nurse">
        <button type="button" onclick="removeStaffItem(this)" 
            class="px-3 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
            <i class="fas fa-trash"></i>
        </button>
    `;
        container.appendChild(div);
    }

    function removeStaffItem(button) {
        const container = document.getElementById('staffContainer');
        if (container.children.length > 1) {
            button.closest('div').remove();
        }
    }
</script>
@endpush
@endsection