@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Edit Ledger: {{ $ledger->name }}</h3>
                    <div>
                        <a href="{{ route('admin.ledgers.show', $ledger) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('admin.ledgers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Ledgers
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Error Messages -->
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('admin.ledgers.update', $ledger) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Ledger Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control @error('name') is-invalid @enderror"
                                        id="name"
                                        name="name"
                                        value="{{ old('name', $ledger->name) }}"
                                        required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ledger_type">Ledger Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('ledger_type') is-invalid @enderror"
                                        id="ledger_type"
                                        name="ledger_type"
                                        required>
                                        <option value="">Select Ledger Type</option>
                                        @foreach(\App\Models\Ledger::getLedgerTypes() as $key => $value)
                                        <option value="{{ $key }}" {{ old('ledger_type', $ledger->ledger_type) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('ledger_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="balance_type">Balance Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('balance_type') is-invalid @enderror"
                                        id="balance_type"
                                        name="balance_type"
                                        required>
                                        <option value="">Select Balance Type</option>
                                        <option value="Dr" {{ old('balance_type', $ledger->balance_type) == 'Dr' ? 'selected' : '' }}>Dr (Debit)</option>
                                        <option value="Cr" {{ old('balance_type', $ledger->balance_type) == 'Cr' ? 'selected' : '' }}>Cr (Credit)</option>
                                    </select>
                                    @error('balance_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror"
                                        id="status"
                                        name="status"
                                        required>
                                        <option value="">Select Status</option>
                                        <option value="active" {{ old('status', $ledger->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $ledger->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="default" {{ old('status', $ledger->status) == 'default' ? 'selected' : '' }}>Default</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact">Contact</label>
                                    <input type="text"
                                        class="form-control @error('contact') is-invalid @enderror"
                                        id="contact"
                                        name="contact"
                                        value="{{ old('contact', $ledger->contact) }}">
                                    @error('contact')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location">Location</label>
                                    <input type="text"
                                        class="form-control @error('location') is-invalid @enderror"
                                        id="location"
                                        name="location"
                                        value="{{ old('location', $ledger->location) }}">
                                    @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Read-only Balance Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="opening_balance_display">Opening Balance</label>
                                    <input type="text"
                                        class="form-control"
                                        id="opening_balance_display"
                                        value="{{ number_format($ledger->opening_balance ?? 0, 2) }}"
                                        readonly>
                                    <small class="form-text text-muted">Opening balance cannot be edited after creation.</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="current_balance_display">Current Balance</label>
                                    <input type="text"
                                        class="form-control"
                                        id="current_balance_display"
                                        value="{{ number_format($ledger->current_balance, 2) }}"
                                        readonly>
                                    <small class="form-text text-muted">Current balance is calculated automatically from transactions.</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Ledger
                            </button>
                            <a href="{{ route('admin.ledgers.show', $ledger) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
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