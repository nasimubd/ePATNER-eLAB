@extends('admin.layouts.app')

@section('page-title', $labTest->test_name)
@section('page-description', 'Laboratory test details and required medicines')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.lab-tests.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Tests
            </a>

            @if($labTest->is_active)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Active
            </span>
            @else
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                Inactive
            </span>
            @endif
        </div>

        <div class="flex space-x-3">
            <button onclick="checkStock()" class="inline-flex items-center px-4 py-2 border border-yellow-300 rounded-md shadow-sm text-sm font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Check Stock
            </button>

            <a href="{{ route('admin.lab-tests.edit', $labTest) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Test
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Test Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information Card -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Test Information</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Test Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $labTest->test_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Test Code</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $labTest->test_code }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Department</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $labTest->department ?: 'Not specified' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sample Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $labTest->sample_type ?: 'Not specified' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Price</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">${{ number_format($labTest->price, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Duration</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($labTest->duration_minutes)
                                {{ $labTest->duration_minutes }} minutes
                                @else
                                Not specified
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $labTest->created_at->format('M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $labTest->updated_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Description Card -->
            @if($labTest->description)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Description</h3>
                </div>
                <div class="px-6 py-4">
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $labTest->description }}</p>
                </div>
            </div>
            @endif

            <!-- Instructions Cards -->
            @if($labTest->instructions || $labTest->preparation_instructions)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($labTest->instructions)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Test Instructions</h3>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $labTest->instructions }}</p>
                    </div>
                </div>
                @endif

                @if($labTest->preparation_instructions)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Preparation Instructions</h3>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $labTest->preparation_instructions }}</p>
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <button onclick="toggleStatus()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white {{ $labTest->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}">
                        @if($labTest->is_active)
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                        </svg>
                        Deactivate Test
                        @else
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Activate Test
                        @endif
                    </button>

                    <form action="{{ route('admin.lab-tests.destroy', $labTest) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this lab test? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete Test
                        </button>
                    </form>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Statistics</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Required Medicines</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $labTest->medicines->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Medicine Cost</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">
                                ${{ number_format($labTest->medicines->sum(function($medicine) {
                                    return $medicine->unit_price * $medicine->pivot->quantity_required;
                                }), 2) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Profit Margin</dt>
                            <dd class="mt-1 text-2xl font-semibold text-green-600">
                                @php
                                $totalCost = $labTest->medicines->sum(function($medicine) {
                                return $medicine->unit_price * $medicine->pivot->quantity_required;
                                });
                                $profit = $labTest->price - $totalCost;
                                $margin = $totalCost > 0 ? ($profit / $labTest->price) * 100 : 100;
                                @endphp
                                {{ number_format($margin, 1) }}%
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Required Medicines -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Required Medicines</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $labTest->medicines->count() }} {{ Str::plural('medicine', $labTest->medicines->count()) }}
                </span>
            </div>
        </div>

        @if($labTest->medicines->count() > 0)
        <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medicine</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Required</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available Stock</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Cost</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($labTest->medicines as $medicine)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $medicine->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $medicine->generic_name }}</div>
                                    @if($medicine->pivot->usage_instructions)
                                    <div class="text-xs text-blue-600 mt-1">{{ $medicine->pivot->usage_instructions }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $medicine->pivot->quantity_required }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $medicine->stock_quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${{ number_format($medicine->unit_price, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${{ number_format($medicine->unit_price * $medicine->pivot->quantity_required, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($medicine->stock_quantity >= $medicine->pivot->quantity_required)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                In Stock
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                Short by {{ $medicine->pivot->quantity_required - $medicine->stock_quantity }}
                            </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Medicines Required</h3>
            <p class="text-gray-500 mb-4">This test doesn't require any medicines to perform.</p>
            <a href="{{ route('admin.lab-tests.edit', $labTest) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200">
                Add Medicines
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Stock Check Modal -->
<div id="stockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Stock Availability Check</h3>
                <button onclick="closeStockModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="stockResults" class="space-y-4">
                <!-- Stock results will be populated here -->
            </div>
            <div class="flex justify-end mt-6">
                <button onclick="closeStockModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@php
$medicinesArray = $labTest->medicines->map(function($medicine) {
return [
'name' => $medicine->name,
'required' => $medicine->pivot->quantity_required,
'available' => $medicine->stock_quantity,
];
});
@endphp
<script>
    // Prepare medicines data in PHP and pass as JSON

    const medicines = @json($medicinesArray);

    function checkStock() {
        const modal = document.getElementById('stockModal');
        const resultsContainer = document.getElementById('stockResults');

        // Show modal
        modal.classList.remove('hidden');

        // Clear previous results
        resultsContainer.innerHTML = '<div class="text-center py-4"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div><p class="mt-2 text-gray-600">Checking stock availability...</p></div>';

        // Simulate stock check (in real implementation, this would be an AJAX call)
        setTimeout(() => {
            let html = '';
            let allInStock = true;

            medicines.forEach(function(medicine) {
                const inStock = medicine.available >= medicine.required;
                if (!inStock) {
                    allInStock = false;
                }

                html += `
                <div class="flex items-center justify-between p-4 border rounded-lg ${inStock ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'}">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            ${inStock ? 
                                '<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>' :
                                '<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>'
                            }
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium ${inStock ? 'text-green-800' : 'text-red-800'}">${medicine.name}</p>
                            <p class="text-sm ${inStock ? 'text-green-600' : 'text-red-600'}">
                                Required: ${medicine.required} | Available: ${medicine.available}
                            </p>
                        </div>
                    </div>
                    <div class="text-sm font-medium ${inStock ? 'text-green-800' : 'text-red-800'}">
                        ${inStock ? 'In Stock' : 'Short by ' + (medicine.required - medicine.available)}
                    </div>
                </div>
                `;
            });

            // Add summary
            html = `
            <div class="mb-4 p-4 rounded-lg ${allInStock ? 'bg-green-100 border border-green-200' : 'bg-red-100 border border-red-200'}">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        ${allInStock ? 
                            '<svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>' :
                            '<svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>'
                        }
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium ${allInStock ? 'text-green-800' : 'text-red-800'}">
                            ${allInStock ? 'All medicines are in stock' : 'Some medicines are out of stock'}
                        </h3>
                        <p class="text-sm ${allInStock ? 'text-green-700' : 'text-red-700'}">
                            ${allInStock ? 'This test can be performed immediately.' : 'Please restock the missing medicines before performing this test.'}
                        </p>
                    </div>
                </div>
            </div>
            ` + html;

            resultsContainer.innerHTML = html;
        }, 1000);
    }

    function closeStockModal() {
        document.getElementById('stockModal').classList.add('hidden');
    }

    function toggleStatus() {
        if (confirm('Are you sure you want to {{ $labTest->is_active ? "deactivate" : "activate" }} this lab test?')) {
            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.lab-tests.toggle-status", $labTest) }}';

            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // Add method override
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'PATCH';
            form.appendChild(methodField);

            document.body.appendChild(form);
            form.submit();
        }
    }

    // Close modal when clicking outside
    document.getElementById('stockModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeStockModal();
        }
    });
</script>
@endsection