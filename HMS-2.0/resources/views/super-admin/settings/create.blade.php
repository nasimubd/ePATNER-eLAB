@extends('super-admin.layouts.app')

@section('page-title', 'Create Global Setting')
@section('page-description', 'Add a new global setting')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Create New Global Setting</h2>
            <p class="mt-1 text-sm text-gray-600">Add a new setting that will apply globally to all businesses.</p>
        </div>

        <form action="{{ route('super-admin.settings.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Key -->
            <div>
                <label for="key" class="block text-sm font-medium text-gray-700">Setting Key</label>
                <input type="text" name="key" id="key" value="{{ old('key') }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                    placeholder="e.g., enable_feature_x" required>
                @error('key')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Use lowercase letters, numbers, and underscores only. This key will be used to access the setting in code.</p>
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Data Type</label>
                <select name="type" id="type"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" required>
                    <option value="string" {{ old('type') === 'string' ? 'selected' : '' }}>String</option>
                    <option value="integer" {{ old('type') === 'integer' ? 'selected' : '' }}>Integer</option>
                    <option value="decimal" {{ old('type') === 'decimal' ? 'selected' : '' }}>Decimal</option>
                    <option value="boolean" {{ old('type') === 'boolean' ? 'selected' : '' }}>Boolean</option>
                    <option value="json" {{ old('type') === 'json' ? 'selected' : '' }}>JSON</option>
                </select>
                @error('type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Value -->
            <div id="value-container">
                <label for="value" class="block text-sm font-medium text-gray-700">Value</label>
                <input type="text" name="value" id="value" value="{{ old('value') }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                @error('value')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Enter the value for this setting. For boolean settings, use '1' for true or '0' for false.</p>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                <textarea name="description" id="description" rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                    placeholder="Describe what this setting does...">{{ old('description') }}</textarea>
                @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('super-admin.settings.index') }}"
                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Cancel
                </a>
                <button type="submit"
                    class="bg-red-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Create Setting
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const valueContainer = document.getElementById('value-container');
        const valueInput = document.getElementById('value');

        function updateValueInput() {
            const selectedType = typeSelect.value;
            let inputType = 'text';
            let placeholder = '';
            let helpText = '';

            switch (selectedType) {
                case 'boolean':
                    placeholder = '1 (true) or 0 (false)';
                    helpText = 'Enter 1 for true/enabled or 0 for false/disabled.';
                    break;
                case 'integer':
                    inputType = 'number';
                    placeholder = 'e.g., 100';
                    helpText = 'Enter a whole number.';
                    break;
                case 'decimal':
                    inputType = 'number';
                    placeholder = 'e.g., 99.99';
                    helpText = 'Enter a decimal number.';
                    break;
                case 'json':
                    placeholder = '{"key": "value"}';
                    helpText = 'Enter valid JSON data.';
                    break;
                default:
                    placeholder = 'Enter text value';
                    helpText = 'Enter the string value for this setting.';
            }

            valueInput.type = inputType;
            valueInput.placeholder = placeholder;

            // Update help text
            const helpTextElement = valueContainer.querySelector('.text-gray-500');
            if (helpTextElement) {
                helpTextElement.textContent = helpText;
            }
        }

        typeSelect.addEventListener('change', updateValueInput);
        updateValueInput(); // Initialize on page load
    });
</script>
@endsection