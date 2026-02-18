@extends('admin.layouts.app')

@section('title', 'Create Transaction')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Create New Transaction</h2>
                    <p class="text-gray-600 mt-1">Add a new accounting transaction</p>
                </div>
                <a href="{{ route('admin.transactions.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Transactions
                </a>
            </div>
        </div>

        <form action="{{ route('admin.transactions.store') }}" method="POST" id="transactionForm" class="p-6">
            @csrf

            <!-- Transaction Details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div>
                    <label for="transaction_type" class="block text-sm font-medium text-gray-700 mb-2">Transaction Type <span class="text-red-500">*</span></label>
                    <select class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('transaction_type') border-red-500 @enderror"
                        id="transaction_type" name="transaction_type" required>
                        <option value="">-- Select Type --</option>
                        <option value="Payment" {{ old('transaction_type') == 'Payment' ? 'selected' : '' }}>Payment</option>
                        <option value="Receipt" {{ old('transaction_type') == 'Receipt' ? 'selected' : '' }}>Receipt</option>
                        <option value="Journal" {{ old('transaction_type') == 'Journal' ? 'selected' : '' }}>Journal</option>
                        <option value="Contra" {{ old('transaction_type') == 'Contra' ? 'selected' : '' }}>Contra</option>
                    </select>
                    @error('transaction_type')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-2">Transaction Date <span class="text-red-500">*</span></label>
                    <input type="date" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('transaction_date') border-red-500 @enderror"
                        id="transaction_date" name="transaction_date"
                        value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                    @error('transaction_date')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="narration" class="block text-sm font-medium text-gray-700 mb-2">Narration</label>
                    <div class="relative">
                        <input type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('narration') border-red-500 @enderror"
                            id="narration" name="narration"
                            value="{{ old('narration') }}" placeholder="Transaction description">
                        <button type="button" id="aiSuggestBtn" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs">
                            AI Suggestions
                        </button>
                    </div>
                    <div id="aiSuggestions" class="hidden mt-2 bg-white border border-gray-200 rounded-lg shadow-md p-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <button type="button" class="ai-suggestion-btn text-left px-3 py-1 text-sm hover:bg-gray-100 rounded" data-suggestion="Payment for services rendered">Payment for services</button>
                            <button type="button" class="ai-suggestion-btn text-left px-3 py-1 text-sm hover:bg-gray-100 rounded" data-suggestion="Receipt from customer payment">Receipt from customer</button>
                            <button type="button" class="ai-suggestion-btn text-left px-3 py-1 text-sm hover:bg-gray-100 rounded" data-suggestion="Journal entry for month end adjustment">Month end adjustment</button>
                            <button type="button" class="ai-suggestion-btn text-left px-3 py-1 text-sm hover:bg-gray-100 rounded" data-suggestion="Fund transfer between accounts">Fund transfer</button>
                        </div>
                    </div>
                    @error('narration')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Transaction Lines -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Transaction Lines</h3>
                    <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center" id="addLineBtn">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Line
                    </button>
                </div>

                <div id="transactionLines" class="space-y-4">
                    <!-- Initial lines will be added by JavaScript -->
                </div>

                <!-- Totals -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div class="bg-green-50 p-6 rounded-lg">
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 50 50" style="fill:#40C057;" class="mr-2">
                                    <path d="M25,2C12.317,2,2,12.317,2,25s10.317,23,23,23s23-10.317,23-23S37.683,2,25,2z M37,26H26v11h-2V26H13v-2h11V13h2v11h11V26z"></path>
                                </svg>
                                <h6 class="text-sm font-medium text-green-700">Total Debit</h6>
                            </div>
                            <h4 class="text-2xl font-bold text-green-600" id="totalDebit">৳0.00</h4>
                        </div>
                    </div>
                    <div class="bg-red-50 p-6 rounded-lg">
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 50 50" style="fill:#DC3545;" class="mr-2">
                                    <path d="M25,2C12.317,2,2,12.317,2,25s10.317,23,23,23s23-10.317,23-23S37.683,2,25,2z M37,26H13v-2h24V26z"></path>
                                </svg>
                                <h6 class="text-sm font-medium text-red-700">Total Credit</h6>
                            </div>
                            <h4 class="text-2xl font-bold text-red-600" id="totalCredit">৳0.00</h4>
                        </div>
                    </div>
                </div>

                <!-- Balance Check -->
                <div class="bg-blue-50 p-4 rounded-lg mt-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-blue-700">Balance Check:</span>
                        <span id="balance-status" class="text-sm font-bold"></span>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-between items-center mt-8">
                <a href="{{ route('admin.transactions.index') }}" class="text-gray-600 hover:text-gray-800">Cancel</a>
                <button type="submit" id="submitButton" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center justify-center">
                    <span class="inline-flex items-center">
                        <svg id="defaultIcon" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <svg id="spinnerIcon" class="hidden w-5 h-5 mr-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="buttonText">Save Transaction</span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Transaction Line Template -->
<template id="lineTemplate">
    <div class="transaction-line bg-gray-50 border border-gray-200 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Ledger <span class="text-red-500">*</span></label>
                <select class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 ledger-select" name="lines[INDEX][ledger_id]" required>
                    <option value="">-- Select Ledger --</option>
                    @foreach($ledgers as $ledger)
                    <option value="{{ $ledger->id }}"
                        data-type="{{ $ledger->type ?? 'all' }}"
                        data-category="{{ $ledger->category ?? 'all' }}">
                        {{ $ledger->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-green-700 mb-2">Debit Amount</label>
                <input type="number" class="w-full rounded-lg border-green-300 focus:border-green-500 focus:ring-green-500 debit-amount" name="lines[INDEX][debit_amount]"
                    step="0.01" min="0" placeholder="0.00">
            </div>
            <div>
                <label class="block text-sm font-medium text-red-700 mb-2">Credit Amount</label>
                <input type="number" class="w-full rounded-lg border-red-300 focus:border-red-500 focus:ring-red-500 credit-amount" name="lines[INDEX][credit_amount]"
                    step="0.01" min="0" placeholder="0.00">
            </div>
            <div>
                <button type="button" class="w-full bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg remove-line flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // AI Narration Suggestions
        const aiSuggestBtn = document.getElementById('aiSuggestBtn');
        const aiSuggestions = document.getElementById('aiSuggestions');
        const narrationInput = document.getElementById('narration');

        aiSuggestBtn.addEventListener('click', function() {
            aiSuggestions.classList.toggle('hidden');
        });

        document.querySelectorAll('.ai-suggestion-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                narrationInput.value = this.getAttribute('data-suggestion');
                aiSuggestions.classList.add('hidden');
            });
        });

        // Close suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!aiSuggestBtn.contains(e.target) && !aiSuggestions.contains(e.target)) {
                aiSuggestions.classList.add('hidden');
            }
        });
        let lineIndex = 0;
        const linesContainer = document.getElementById('transactionLines');
        const addLineBtn = document.getElementById('addLineBtn');
        const lineTemplate = document.getElementById('lineTemplate');
        const transactionTypeSelect = document.getElementById('transaction_type');

        // Add initial lines
        addLine();
        addLine();

        // Add line button click
        addLineBtn.addEventListener('click', addLine);

        // Transaction type change event for ledger filtering
        transactionTypeSelect.addEventListener('change', function() {
            const transactionType = this.value;
            filterLedgersByTransactionType(transactionType);

            // For payment transactions, auto-select cash ledger in first line
            if (transactionType === 'Payment') {
                const firstLine = document.querySelector('.transaction-line');
                if (firstLine) {
                    const ledgerSelect = firstLine.querySelector('.ledger-select');
                    const cashOption = Array.from(ledgerSelect.options).find(
                        opt => opt.text.includes('Cash') || opt.getAttribute('data-type') === 'Cash-in-Hand'
                    );
                    if (cashOption) {
                        ledgerSelect.value = cashOption.value;
                        $(ledgerSelect).trigger('change');
                    }
                    // Hide credit amount column for first line
                    firstLine.querySelector('.credit-amount').closest('div').style.display = 'none';
                }
                // Hide debit amount column for second line
                const secondLine = document.querySelectorAll('.transaction-line')[1];
                if (secondLine) {
                    secondLine.querySelector('.debit-amount').closest('div').style.display = 'none';
                }
            } else {
                // Show all amount columns if not Payment type
                document.querySelectorAll('.debit-amount, .credit-amount').forEach(input => {
                    input.closest('div').style.display = 'block';
                });
            }
        });

        // Filter ledgers based on transaction type
        function filterLedgersByTransactionType(transactionType) {
            const ledgerSelects = document.querySelectorAll('.ledger-select');
            let isFirstLine = true;

            ledgerSelects.forEach(select => {
                const options = select.querySelectorAll('option');
                const currentLine = select.closest('.transaction-line');
                const isDebitLine = currentLine.querySelector('.debit-amount');

                options.forEach(option => {
                    if (option.value === '') {
                        option.style.display = 'block';
                        return;
                    }

                    const ledgerType = option.getAttribute('data-type');
                    const ledgerCategory = option.getAttribute('data-category');
                    const ledgerName = option.text.toLowerCase();

                    // Show/hide options based on transaction type and line position
                    let shouldShow = true;

                    if (transactionType === 'Payment') {
                        // For payments:
                        // First line (debit) - show only cash/bank accounts
                        // Other lines - show all except cash/bank
                        if (isFirstLine && isDebitLine) {
                            shouldShow = ['cash', 'bank', 'cash-in-hand'].some(type =>
                                ledgerType.toLowerCase().includes(type) ||
                                ledgerCategory.toLowerCase().includes(type) ||
                                ledgerName.includes('cash') ||
                                ledgerName.includes('bank')
                            );
                        } else {
                            shouldShow = !['cash', 'bank', 'cash-in-hand'].some(type =>
                                ledgerType.toLowerCase().includes(type) ||
                                ledgerCategory.toLowerCase().includes(type) ||
                                ledgerName.includes('cash') ||
                                ledgerName.includes('bank')
                            );
                        }
                    } else if (transactionType === 'Receipt') {
                        // For receipts, typically show income, asset, and liability accounts
                        shouldShow = ['income', 'asset', 'liability', 'all'].includes(ledgerType) || ['income', 'asset', 'liability', 'all'].includes(ledgerCategory);
                    } else if (transactionType === 'Journal') {
                        // For journal entries, show all accounts
                        shouldShow = true;
                    } else if (transactionType === 'Contra') {
                        // For contra entries, typically show cash/bank accounts
                        shouldShow = ['asset', 'cash', 'bank', 'all'].includes(ledgerType) || ['asset', 'cash', 'bank', 'all'].includes(ledgerCategory);
                    } else {
                        // If no transaction type selected, show all
                        shouldShow = true;
                    }

                    option.style.display = shouldShow ? 'block' : 'none';
                });

                // Reset selected value if it's now hidden
                const selectedOption = select.querySelector('option:checked');
                if (selectedOption && selectedOption.style.display === 'none') {
                    select.value = '';
                }

                isFirstLine = false;
            });
        }

        function addLine() {
            const template = lineTemplate.content.cloneNode(true);
            const lineDiv = template.querySelector('.transaction-line');

            // Replace INDEX with actual index
            lineDiv.innerHTML = lineDiv.innerHTML.replace(/INDEX/g, lineIndex);

            // Add event listeners
            const removeBtn = lineDiv.querySelector('.remove-line');
            const debitInput = lineDiv.querySelector('.debit-amount');
            const creditInput = lineDiv.querySelector('.credit-amount');

            removeBtn.addEventListener('click', function() {
                if (document.querySelectorAll('.transaction-line').length > 2) {
                    lineDiv.remove();
                    calculateTotals();
                } else {
                    alert('At least 2 transaction lines are required.');
                }
            });

            // Prevent both debit and credit from having values
            debitInput.addEventListener('input', function() {
                if (this.value) {
                    creditInput.value = '';
                }
                calculateTotals();
            });

            creditInput.addEventListener('input', function() {
                if (this.value) {
                    debitInput.value = '';
                }
                calculateTotals();
            });

            linesContainer.appendChild(lineDiv);
            lineIndex++;

            // Apply current transaction type filter to the new line
            const currentTransactionType = transactionTypeSelect.value;
            if (currentTransactionType) {
                filterLedgersByTransactionType(currentTransactionType);
            }

            calculateTotals();
        }

        function calculateTotals() {
            let totalDebit = 0;
            let totalCredit = 0;

            document.querySelectorAll('.debit-amount').forEach(input => {
                totalDebit += parseFloat(input.value) || 0;
            });

            document.querySelectorAll('.credit-amount').forEach(input => {
                totalCredit += parseFloat(input.value) || 0;
            });

            document.getElementById('totalDebit').textContent = '৳' + totalDebit.toFixed(2);
            document.getElementById('totalCredit').textContent = '৳' + totalCredit.toFixed(2);

            // Update balance status
            const balanceStatus = document.getElementById('balance-status');
            const difference = Math.abs(totalDebit - totalCredit);

            if (difference < 0.01) {
                balanceStatus.textContent = 'Balanced ✓';
                balanceStatus.className = 'text-sm font-bold text-green-600';
            } else {
                balanceStatus.textContent = `Difference: ৳${difference.toFixed(2)}`;
                balanceStatus.className = 'text-sm font-bold text-red-600';
            }

            // Highlight totals if not balanced
            const debitElement = document.getElementById('totalDebit');
            const creditElement = document.getElementById('totalCredit');

            if (difference > 0.01) {
                debitElement.classList.remove('text-green-600');
                debitElement.classList.add('text-yellow-600');
                creditElement.classList.remove('text-red-600');
                creditElement.classList.add('text-yellow-600');
            } else {
                debitElement.classList.remove('text-yellow-600');
                debitElement.classList.add('text-green-600');
                creditElement.classList.remove('text-yellow-600');
                creditElement.classList.add('text-red-600');
            }
        }

        // Form validation with loading state
        document.getElementById('transactionForm').addEventListener('submit', function(e) {
            const lines = document.querySelectorAll('.transaction-line');
            if (lines.length < 2) {
                e.preventDefault();
                alert('At least 2 transaction lines are required.');
                return;
            }

            let totalDebit = 0;
            let totalCredit = 0;
            let hasValidLine = false;

            document.querySelectorAll('.debit-amount').forEach(input => {
                const value = parseFloat(input.value) || 0;
                totalDebit += value;
                if (value > 0) hasValidLine = true;
            });

            document.querySelectorAll('.credit-amount').forEach(input => {
                const value = parseFloat(input.value) || 0;
                totalCredit += value;
                if (value > 0) hasValidLine = true;
            });

            if (!hasValidLine) {
                e.preventDefault();
                alert('At least one line must have an amount.');
                return;
            }

            if (Math.abs(totalDebit - totalCredit) > 0.01) {
                e.preventDefault();
                alert('Total Debit and Credit amounts must be equal.');
                return;
            }

            // Show loading state
            const submitButton = document.getElementById('submitButton');
            const defaultIcon = document.getElementById('defaultIcon');
            const spinnerIcon = document.getElementById('spinnerIcon');
            const buttonText = document.getElementById('buttonText');

            defaultIcon.classList.add('hidden');
            spinnerIcon.classList.remove('hidden');
            buttonText.textContent = 'Saving...';
            submitButton.disabled = true;
        });
    });
</script>
@endpush