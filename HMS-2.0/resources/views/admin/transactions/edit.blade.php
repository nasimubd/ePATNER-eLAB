@extends('admin.layouts.app')

@section('title', 'Edit Transaction')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Transaction #{{ $transaction->id }}</h1>
            <p class="text-muted mb-0">Modify transaction details and lines</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.transactions.show', $transaction) }}" class="btn btn-info">
                <i class="fas fa-eye me-2"></i>View
            </a>
            <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <!-- Transaction Form -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.transactions.update', $transaction) }}" method="POST" id="transactionForm">
                @csrf
                @method('PUT')

                <!-- Transaction Details -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label for="transaction_type" class="form-label">Transaction Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('transaction_type') is-invalid @enderror"
                            id="transaction_type" name="transaction_type" required>
                            <option value="">Select Type</option>
                            <option value="Payment" {{ old('transaction_type', $transaction->transaction_type) == 'Payment' ? 'selected' : '' }}>Payment</option>
                            <option value="Receipt" {{ old('transaction_type', $transaction->transaction_type) == 'Receipt' ? 'selected' : '' }}>Receipt</option>
                            <option value="Journal" {{ old('transaction_type', $transaction->transaction_type) == 'Journal' ? 'selected' : '' }}>Journal</option>
                            <option value="Contra" {{ old('transaction_type', $transaction->transaction_type) == 'Contra' ? 'selected' : '' }}>Contra</option>
                        </select>
                        @error('transaction_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="transaction_date" class="form-label">Transaction Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('transaction_date') is-invalid @enderror"
                            id="transaction_date" name="transaction_date"
                            value="{{ old('transaction_date', $transaction->transaction_date) }}" required>
                        @error('transaction_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="narration" class="form-label">Narration</label>
                        <input type="text" class="form-control @error('narration') is-invalid @enderror"
                            id="narration" name="narration"
                            value="{{ old('narration', $transaction->narration) }}" placeholder="Transaction description">
                        @error('narration')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Transaction Lines -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Transaction Lines</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addLineBtn">
                            <i class="fas fa-plus me-1"></i>Add Line
                        </button>
                    </div>

                    <div id="transactionLines">
                        @foreach($transaction->transactionLines as $index => $line)
                        <div class="transaction-line border rounded p-3 mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Ledger <span class="text-danger">*</span></label>
                                    <select class="form-select ledger-select" name="lines[{{ $index }}][ledger_id]" required>
                                        <option value="">Select Ledger</option>
                                        @foreach($ledgers as $ledger)
                                        <option value="{{ $ledger->id }}"
                                            {{ old("lines.$index.ledger_id", $line->ledger_id) == $ledger->id ? 'selected' : '' }}>
                                            {{ $ledger->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Debit Amount</label>
                                    <input type="number" class="form-control debit-amount"
                                        name="lines[{{ $index }}][debit_amount]"
                                        step="0.01" min="0" placeholder="0.00"
                                        value="{{ old("lines.$index.debit_amount", $line->debit_amount > 0 ? $line->debit_amount : '') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Credit Amount</label>
                                    <input type="number" class="form-control credit-amount"
                                        name="lines[{{ $index }}][credit_amount]"
                                        step="0.01" min="0" placeholder="0.00"
                                        value="{{ old("lines.$index.credit_amount", $line->credit_amount > 0 ? $line->credit_amount : '') }}">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger remove-line w-100">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Totals -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Total Debit</h6>
                                    <h4 class="text-success mb-0" id="totalDebit">৳0.00</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Total Credit</h6>
                                    <h4 class="text-danger mb-0" id="totalCredit">৳0.00</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.transactions.show', $transaction) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Transaction Line Template -->
<template id="lineTemplate">
    <div class="transaction-line border rounded p-3 mb-3">
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Ledger <span class="text-danger">*</span></label>
                <select class="form-select ledger-select" name="lines[INDEX][ledger_id]" required>
                    <option value="">Select Ledger</option>
                    @foreach($ledgers as $ledger)
                    <option value="{{ $ledger->id }}">{{ $ledger->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Debit Amount</label>
                <input type="number" class="form-control debit-amount" name="lines[INDEX][debit_amount]"
                    step="0.01" min="0" placeholder="0.00">
            </div>
            <div class="col-md-3">
                <label class="form-label">Credit Amount</label>
                <input type="number" class="form-control credit-amount" name="lines[INDEX][credit_amount]"
                    step="0.01" min="0" placeholder="0.00">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger remove-line w-100">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let lineIndex = {
            {
                $transaction - > transactionLines - > count()
            }
        };
        const linesContainer = document.getElementById('transactionLines');
        const addLineBtn = document.getElementById('addLineBtn');
        const lineTemplate = document.getElementById('lineTemplate');

        // Initialize existing lines
        initializeExistingLines();
        calculateTotals();

        // Add line button click
        addLineBtn.addEventListener('click', addLine);

        function initializeExistingLines() {
            document.querySelectorAll('.transaction-line').forEach(lineDiv => {
                const removeBtn = lineDiv.querySelector('.remove-line');
                const debitInput = lineDiv.querySelector('.debit-amount');
                const creditInput = lineDiv.querySelector('.credit-amount');

                removeBtn.addEventListener('click', function() {
                    lineDiv.remove();
                    calculateTotals();
                });

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
                lineDiv.remove();
                calculateTotals();
            });

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

            // Highlight if not balanced
            const debitElement = document.getElementById('totalDebit');
            const creditElement = document.getElementById('totalCredit');

            if (Math.abs(totalDebit - totalCredit) > 0.01) {
                debitElement.classList.add('text-warning');
                creditElement.classList.add('text-warning');
                debitElement.classList.remove('text-success');
                creditElement.classList.remove('text-danger');
            } else {
                debitElement.classList.remove('text-warning');
                creditElement.classList.remove('text-warning');
                debitElement.classList.add('text-success');
                creditElement.classList.add('text-danger');
            }
        }

        // Form validation
        document.getElementById('transactionForm').addEventListener('submit', function(e) {
            const lines = document.querySelectorAll('.transaction-line');
            if (lines.length < 2) {
                e.preventDefault();
                alert('At least 2 transaction lines are required.');
                return;
            }

            let totalDebit = 0;
            let totalCredit = 0;

            document.querySelectorAll('.debit-amount').forEach(input => {
                totalDebit += parseFloat(input.value) || 0;
            });

            document.querySelectorAll('.credit-amount').forEach(input => {
                totalCredit += parseFloat(input.value) || 0;
            });

            if (Math.abs(totalDebit - totalCredit) > 0.01) {
                e.preventDefault();
                alert('Total Debit and Credit amounts must be equal.');
                return;
            }
        });
    });
</script>
@endpush