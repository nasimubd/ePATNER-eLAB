@extends('admin.layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-8 py-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-white">Subscription Expired</h1>
                    <p class="text-red-100 mt-1">Your subscription has expired. Please make a payment to continue using the system.</p>
                </div>
            </div>
        </div>

        <div class="px-8 py-8">
            <!-- QR Code Section -->
            <div class="text-center mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Scan QR Code to Make Payment</h2>
                <div class="inline-block p-4 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <img src="{{ asset(\App\Models\Setting::getPaymentQrPath()) }}"
                        alt="Payment QR Code"
                        class="w-48 h-48 object-contain mx-auto"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <div class="hidden text-center text-gray-500 py-8">
                        <svg class="w-16 h-16 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 21h.01M12 3h.01M21 12h.01M3 12h.01M21 21h.01M3 3h.01"></path>
                        </svg>
                        <p>QR Code not available</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-4">Scan this QR code with your mobile banking app to make the payment</p>
            </div>

            <!-- Payment Form -->
            <form action="{{ route('business.payment.submit') }}" method="POST" id="paymentForm" class="space-y-6" data-monthly-fee="{{ auth()->user()->business->getMonthlyFee() }}">
                @csrf

                <!-- Business Info -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Business Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Hospital:</span>
                            <span class="font-medium text-gray-900 ml-2">{{ auth()->user()->business->hospital_name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Monthly Fee:</span>
                            <span class="font-medium text-gray-900 ml-2">৳{{ number_format(auth()->user()->business->getMonthlyFee(), 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Months Selection -->
                <div>
                    <label for="months" class="block text-sm font-medium text-gray-700 mb-2">
                        Number of Months
                    </label>
                    <select id="months" name="months" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">Select months</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ old('months') == $i ? 'selected' : '' }}>
                            {{ $i }} Month{{ $i > 1 ? 's' : '' }}
                            </option>
                            @endfor
                    </select>
                    @error('months')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount Display -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-blue-900">Total Amount:</span>
                        <span id="totalAmount" class="text-2xl font-bold text-blue-600">৳0.00</span>
                    </div>
                    <input type="hidden" name="amount" id="amountInput" value="0">
                </div>

                <!-- Transaction ID -->
                <div>
                    <label for="transaction_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Transaction ID
                    </label>
                    <input type="text" id="transaction_id" name="transaction_id"
                        value="{{ old('transaction_id') }}"
                        placeholder="Enter the transaction ID from your payment"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        maxlength="50">
                    <p class="mt-1 text-sm text-gray-600">Enter the transaction ID you received after making the payment</p>
                    @error('transaction_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Submit Payment Confirmation
                        </span>
                    </button>
                </div>

                <!-- Instructions -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-yellow-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-yellow-800">Important Instructions:</h4>
                            <ul class="mt-2 text-sm text-yellow-700 space-y-1">
                                <li>• Make sure to enter the correct Transaction ID</li>
                                <li>• Your payment will be reviewed by our admin team</li>
                                <li>• You will receive access once payment is approved</li>
                                <li>• For any issues, contact our support team</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const monthsSelect = document.getElementById('months');
        const totalAmountDisplay = document.getElementById('totalAmount');
        const amountInput = document.getElementById('amountInput');
        // Get monthly fee from data attribute on the form to avoid inline Blade in JS
        const formEl = document.getElementById('paymentForm');
        const monthlyFee = parseFloat(formEl?.dataset.monthlyFee || '0');

        function updateTotal() {
            const months = parseInt(monthsSelect.value) || 0;
            const total = months * monthlyFee;
            totalAmountDisplay.textContent = '৳' + total.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            amountInput.value = total.toFixed(2);
        }

        monthsSelect.addEventListener('change', updateTotal);

        // Set initial value if already selected
        updateTotal();
    });
</script>
@endsection