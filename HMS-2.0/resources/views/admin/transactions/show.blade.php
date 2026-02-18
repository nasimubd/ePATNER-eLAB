@extends('admin.layouts.app')

@section('title', 'Transaction Details')

@section('content')
<div class="container mx-auto px-4">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-0">Transaction #{{ $transaction->id }}</h1>
            <p class="text-gray-600 mb-0">{{ $transaction->transaction_type }} - {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.transactions.edit', $transaction) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('admin.transactions.print', $transaction) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded" target="_blank">
                <i class="fas fa-print mr-2"></i>Print
            </a>
            @if(auth()->user()->business && auth()->user()->business->enable_a5_printing)
            <a href="{{ route('admin.transactions.print-a5', $transaction) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded" target="_blank">
                <i class="fas fa-print mr-2"></i>Print A5
            </a>
            @endif
            <a href="{{ route('admin.transactions.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <!-- Transaction Details -->
        <div class="md:col-span-4">
            <div class="bg-white shadow-sm rounded-lg mb-4">
                <div class="bg-gray-50 px-4 py-3 border-b">
                    <h5 class="mb-0 font-semibold">Transaction Details</h5>
                </div>
                <div class="p-4">
                    <table class="w-full">
                        <tr>
                            <td class="py-2"><strong>ID:</strong></td>
                            <td class="py-2">#{{ $transaction->id }}</td>
                        </tr>
                        <tr>
                            <td class="py-2"><strong>Type:</strong></td>
                            <td class="py-2">
                                @php
                                $typeColors = [
                                'Payment' => 'bg-red-500',
                                'Receipt' => 'bg-green-500',
                                'Journal' => 'bg-blue-500',
                                'Contra' => 'bg-yellow-500'
                                ];
                                @endphp
                                <span class="px-2 py-1 rounded text-white text-sm {{ $typeColors[$transaction->transaction_type] ?? 'bg-gray-500' }}">
                                    {{ $transaction->transaction_type }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-2"><strong>Date:</strong></td>
                            <td class="py-2">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2"><strong>Amount:</strong></td>
                            <td class="py-2"><strong class="text-blue-600">৳{{ number_format($transaction->amount, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td class="py-2"><strong>Created:</strong></td>
                            <td class="py-2">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @if($transaction->narration)
                        <tr>
                            <td class="py-2"><strong>Narration:</strong></td>
                            <td class="py-2">{{ $transaction->narration }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Transaction Lines -->
        <div class="md:col-span-8">
            <div class="bg-white shadow-sm rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b">
                    <h5 class="mb-0 font-semibold">Transaction Lines</h5>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="text-left py-3 px-2">Ledger</th>
                                    <th class="text-right py-3 px-2">Debit</th>
                                    <th class="text-right py-3 px-2">Credit</th>
                                    <th class="text-left py-3 px-2">Narration</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $totalDebit = 0;
                                $totalCredit = 0;
                                @endphp
                                @foreach($transaction->transactionLines as $line)
                                @php
                                $totalDebit += $line->debit_amount;
                                $totalCredit += $line->credit_amount;
                                @endphp
                                <tr class="hover:bg-gray-50 border-b">
                                    <td class="py-3 px-2">
                                        <div>
                                            <strong>{{ $line->ledger->name }}</strong>
                                            @if($line->ledger->ledger_type)
                                            <br><small class="text-gray-500">{{ $line->ledger->ledger_type }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-right py-3 px-2">
                                        @if($line->debit_amount > 0)
                                        <span class="text-green-600 font-bold">৳{{ number_format($line->debit_amount, 2) }}</span>
                                        @else
                                        <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="text-right py-3 px-2">
                                        @if($line->credit_amount > 0)
                                        <span class="text-red-600 font-bold">৳{{ number_format($line->credit_amount, 2) }}</span>
                                        @else
                                        <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-2">
                                        <span class="text-gray-500">{{ $line->narration ?? '-' }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-2">Total</th>
                                    <th class="text-right text-green-600 py-3 px-2">৳{{ number_format($totalDebit, 2) }}</th>
                                    <th class="text-right text-red-600 py-3 px-2">৳{{ number_format($totalCredit, 2) }}</th>
                                    <th class="py-3 px-2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if(abs($totalDebit - $totalCredit) > 0.01)
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded mt-3">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Warning:</strong> This transaction is not balanced.
                        Difference: ৳{{ number_format(abs($totalDebit - $totalCredit), 2) }}
                    </div>
                    @else
                    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded mt-3">
                        <i class="fas fa-check-circle mr-2"></i>
                        <strong>Balanced:</strong> This transaction is properly balanced.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection