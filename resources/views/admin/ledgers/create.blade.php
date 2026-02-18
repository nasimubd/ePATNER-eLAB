@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
        <div class="flex items-center justify-between bg-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Create New Ledger</h3>
            <a href="{{ route('admin.ledgers.index') }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-arrow-left mr-2"></i> Back to Ledgers
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

            <form action="{{ route('admin.ledgers.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Ledger Name <span class="text-red-600">*</span></label>
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
                        <label for="ledger_type" class="block text-sm font-medium text-gray-700">Ledger Type <span class="text-red-600">*</span></label>
                        <select id="ledger_type"
                            name="ledger_type"
                            required
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm py-2 px-3 bg-white focus:border-indigo-500 focus:ring-indigo-500 @error('ledger_type') border-red-500 @enderror">
                            <option value="">Select Ledger Type</option>
                            @foreach(\App\Models\Ledger::getLedgerTypes() as $key => $value)
                            <option value="{{ $key }}" {{ old('ledger_type') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                            @endforeach
                        </select>
                        @error('ledger_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="balance_type" class="block text-sm font-medium text-gray-700">Balance Type <span class="text-red-600">*</span></label>
                        <select id="balance_type"
                            name="balance_type"
                            required
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm py-2 px-3 bg-white focus:border-indigo-500 focus:ring-indigo-500 @error('balance_type') border-red-500 @enderror">
                            <option value="">Select Balance Type</option>
                            <option value="Dr" {{ old('balance_type') == 'Dr' ? 'selected' : '' }}>Dr (Debit)</option>
                            <option value="Cr" {{ old('balance_type') == 'Cr' ? 'selected' : '' }}>Cr (Credit)</option>
                        </select>
                        @error('balance_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="opening_balance" class="block text-sm font-medium text-gray-700">Opening Balance</label>
                        <input type="number"
                            id="opening_balance"
                            name="opening_balance"
                            value="{{ old('opening_balance', 0) }}"
                            step="0.01"
                            min="0"
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 @error('opening_balance') border-red-500 @enderror">
                        @error('opening_balance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="current_balance" class="block text-sm font-medium text-gray-700">Current Balance</label>
                        <input type="number"
                            id="current_balance"
                            name="current_balance"
                            value="{{ old('current_balance', 0) }}"
                            step="0.01"
                            min="0"
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 @error('current_balance') border-red-500 @enderror">
                        @error('current_balance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                            <option value="default" {{ old('status') == 'default' ? 'selected' : '' }}>Default</option>
                        </select>
                        @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="contact" class="block text-sm font-medium text-gray-700">Contact</label>
                        <input type="text"
                            id="contact"
                            name="contact"
                            value="{{ old('contact') }}"
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 @error('contact') border-red-500 @enderror">
                        @error('contact')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                        <input type="text"
                            id="location"
                            name="location"
                            value="{{ old('location') }}"
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 @error('location') border-red-500 @enderror">
                        @error('location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-wrap gap-4 mt-6">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-save mr-2"></i> Create Ledger
                    </button>
                    <a href="{{ route('admin.ledgers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-set balance type based on ledger type
        const ledgerTypeSelect = document.getElementById('ledger_type');
        const balanceTypeSelect = document.getElementById('balance_type');

        const drLedgers = [
            'Bank Accounts',
            'Cash-in-Hand',
            'Expenses',
            'Fixed Assets',
            'Investments',
            'Loans & Advances (Asset)',
            'Purchase Accounts',
            'Sundry Debtors (Customer)'
        ];

        ledgerTypeSelect.addEventListener('change', function() {
            const selectedType = this.value;
            if (drLedgers.includes(selectedType)) {
                balanceTypeSelect.value = 'Dr';
            } else if (selectedType) {
                balanceTypeSelect.value = 'Cr';
            }
        });
    });
</script>
@endsection