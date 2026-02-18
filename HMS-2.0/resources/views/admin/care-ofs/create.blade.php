@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
        <div class="flex items-center justify-between bg-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Create New Care Of</h3>
            <a href="{{ route('admin.care-ofs.index') }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-arrow-left mr-2"></i> Back to Care Ofs
            </a>
        </div>

        <div class="p-6">
            <!-- Error Messages -->
            @if($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Info Message -->
            <div class="mb-6 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    <span>A ledger with type "commission agent" will be automatically created for this Care Of.</span>
                </div>
            </div>

            <form action="{{ route('admin.care-ofs.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Care Of Name <span class="text-red-600">*</span></label>
                        <input type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number <span class="text-red-600">*</span></label>
                        <input type="text"
                            id="phone_number"
                            name="phone_number"
                            value="{{ old('phone_number') }}"
                            required
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 @error('phone_number') border-red-500 @enderror">
                        @error('phone_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Address <span class="text-red-600">*</span></label>
                    <textarea
                        id="address"
                        name="address"
                        rows="3"
                        required
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                    @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Commission Settings -->
                <div class="border-t pt-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Commission Settings</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="commission_type" class="block text-sm font-medium text-gray-700">Commission Type <span class="text-red-600">*</span></label>
                            <select id="commission_type"
                                name="commission_type"
                                required
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm py-2 px-3 bg-white focus:border-indigo-500 focus:ring-indigo-500 @error('commission_type') border-red-500 @enderror">
                                <option value="">Select Commission Type</option>
                                <option value="fixed" {{ old('commission_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                <option value="percentage" {{ old('commission_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                            </select>
                            @error('commission_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="percentage_field" style="display: none;">
                            <label for="commission_rate" class="block text-sm font-medium text-gray-700">Commission Rate (%) <span class="text-red-600">*</span></label>
                            <input type="number"
                                id="commission_rate"
                                name="commission_rate"
                                value="{{ old('commission_rate') }}"
                                step="0.01"
                                min="0"
                                max="100"
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 @error('commission_rate') border-red-500 @enderror">
                            @error('commission_rate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="fixed_field" style="display: none;">
                            <label for="fixed_commission_amount" class="block text-sm font-medium text-gray-700">Fixed Commission Amount <span class="text-red-600">*</span></label>
                            <input type="number"
                                id="fixed_commission_amount"
                                name="fixed_commission_amount"
                                value="{{ old('fixed_commission_amount') }}"
                                step="0.01"
                                min="0"
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 @error('fixed_commission_amount') border-red-500 @enderror">
                            @error('fixed_commission_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-600">*</span></label>
                    <select id="status"
                        name="status"
                        required
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm py-2 px-3 bg-white focus:border-indigo-500 focus:ring-indigo-500 @error('status') border-red-500 @enderror">
                        <option value="">Select Status</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap gap-4 mt-6">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-save mr-2"></i> Create Care Of
                    </button>
                    <a href="{{ route('admin.care-ofs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const commissionTypeSelect = document.getElementById('commission_type');
        const percentageField = document.getElementById('percentage_field');
        const fixedField = document.getElementById('fixed_field');
        const commissionRateInput = document.getElementById('commission_rate');
        const fixedCommissionInput = document.getElementById('fixed_commission_amount');

        function toggleCommissionFields() {
            const selectedType = commissionTypeSelect.value;

            if (selectedType === 'percentage') {
                percentageField.style.display = 'block';
                fixedField.style.display = 'none';
                commissionRateInput.required = true;
                fixedCommissionInput.required = false;
                fixedCommissionInput.value = '';
            } else if (selectedType === 'fixed') {
                percentageField.style.display = 'none';
                fixedField.style.display = 'block';
                commissionRateInput.required = false;
                fixedCommissionInput.required = true;
                commissionRateInput.value = '';
            } else {
                percentageField.style.display = 'none';
                fixedField.style.display = 'none';
                commissionRateInput.required = false;
                fixedCommissionInput.required = false;
            }
        }

        commissionTypeSelect.addEventListener('change', toggleCommissionFields);

        // Initialize on page load
        toggleCommissionFields();
    });
</script>
@endsection