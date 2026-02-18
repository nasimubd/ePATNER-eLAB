@extends('admin.layouts.app')

@section('content')
@php
$user = Auth::user();
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-2 sm:p-4">
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl overflow-hidden border border-white/20 mb-6">
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-700 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-center sm:text-left">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">
                            <i class="fas fa-plus-circle mr-2"></i>Create OT Room
                        </h1>
                        <p class="text-blue-100 text-sm">Add a new operating theater room</p>
                    </div>

                    <div class="w-full sm:w-auto">
                        <a href="{{ route('admin.ot-rooms.index') }}"
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
            <form action="{{ route('admin.ot-rooms.store') }}" method="POST" id="otRoomForm">
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
                            {{-- Room Name --}}
                            <div class="space-y-2">
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-hospital mr-1 text-blue-500"></i>Room Name *
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('name') border-red-500 @enderror"
                                    placeholder="e.g., Main Operating Theater, OT-1">
                                @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Room Number --}}
                            <div class="space-y-2">
                                <label for="room_number" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-hashtag mr-1 text-green-500"></i>Room Number *
                                </label>
                                <input type="text" id="room_number" name="room_number" value="{{ old('room_number') }}" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('room_number') border-red-500 @enderror"
                                    placeholder="e.g., OT-001, MAIN-OT">
                                @error('room_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Capacity --}}
                            <div class="space-y-2">
                                <label for="capacity" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-users mr-1 text-purple-500"></i>Capacity *
                                </label>
                                <input type="number" id="capacity" name="capacity" value="{{ old('capacity', 1) }}" required min="1"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('capacity') border-red-500 @enderror"
                                    placeholder="1">
                                @error('capacity')
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
                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
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
                                    placeholder="Detailed description of the OT room...">{{ old('description') }}</textarea>
                                @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Equipment Section --}}
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-tools text-white text-sm"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">Available Equipment</h2>
                        </div>

                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="equipment_available" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-wrench mr-1 text-green-500"></i>Equipment Available
                                </label>
                                <div id="equipmentContainer" class="space-y-2">
                                    @if(old('equipment_available'))
                                    @foreach(old('equipment_available') as $index => $equipment)
                                    @if(trim($equipment))
                                    <div class="flex items-center space-x-2 equipment-item">
                                        <input type="text" name="equipment_available[]" value="{{ $equipment }}"
                                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                            placeholder="e.g., Ventilator, ECG Monitor, Anesthesia Machine">
                                        <button type="button" onclick="removeEquipmentItem(this)"
                                            class="px-3 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    @endif
                                    @endforeach
                                    @else
                                    <div class="flex items-center space-x-2 equipment-item">
                                        <input type="text" name="equipment_available[]"
                                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                            placeholder="e.g., Ventilator, ECG Monitor, Anesthesia Machine">
                                        <button type="button" onclick="removeEquipmentItem(this)"
                                            class="px-3 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    @endif
                                </div>
                                <button type="button" onclick="addEquipmentItem()"
                                    class="mt-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                                    <i class="fas fa-plus mr-1"></i>Add Equipment
                                </button>
                                @error('equipment_available')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                    <a href="{{ route('admin.ot-rooms.index') }}"
                        class="w-full sm:w-auto px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 text-center">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>

                    <button type="submit" id="submitBtn"
                        class="w-full sm:w-auto px-6 py-3 border border-transparent rounded-lg text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-save mr-2"></i>
                        <span>Create OT Room</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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

    .equipment-item {
        animation: slideInUp 0.3s ease-out;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 640px) {
        .equipment-item {
            flex-direction: column;
            gap: 0.5rem;
        }

        .equipment-item button {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function addEquipmentItem() {
        const container = document.getElementById('equipmentContainer');
        const itemHtml = `
            <div class="flex items-center space-x-2 equipment-item">
                <input type="text" name="equipment_available[]"
                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        placeholder="e.g., Ventilator, ECG Monitor, Anesthesia Machine">
                <button type="button" onclick="removeEquipmentItem(this)"
                    class="px-3 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', itemHtml);
    }

    function removeEquipmentItem(button) {
        const container = document.getElementById('equipmentContainer');
        if (container.children.length > 1) {
            button.closest('.equipment-item').remove();
        } else {
            alert('At least one equipment field is required');
        }
    }

    // Form validation
    document.getElementById('otRoomForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i><span>Creating...</span>';

        // Clean up empty equipment fields
        const equipmentInputs = document.querySelectorAll('input[name="equipment_available[]"]');
        equipmentInputs.forEach(function(input) {
            if (!input.value.trim()) {
                input.remove();
            }
        });
    });

    // Basic form validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('otRoomForm');
        const inputs = form.querySelectorAll('input[required], select[required]');

        inputs.forEach(function(input) {
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.classList.add('border-red-500');
                    this.classList.remove('border-green-500');
                } else {
                    this.classList.add('border-green-500');
                    this.classList.remove('border-red-500');
                }
            });
        });
    });
</script>
@endpush
@endsection