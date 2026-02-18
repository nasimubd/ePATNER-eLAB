<form id="medicalInvoiceForm" action="{{ route('admin.medical.invoices.store') }}" method="POST" class="bg-white h-full">
    @csrf

    <!-- Header Info -->
    <div class="p-3 border-b space-y-3">
        <div>
            <label class="text-sm font-medium text-gray-700">Invoice Date</label>
            <input type="datetime-local" name="invoice_date"
                value="{{ now()->format('Y-m-d\TH:i') }}"
                class="w-full rounded border-gray-300 text-sm py-2 mt-1">
        </div>

        <div>
            <label class="text-sm font-medium text-gray-700">Payment Method</label>
            <select name="payment_method" class="w-full rounded border-gray-300 text-sm py-2 mt-1">
                <option value="cash">Cash</option>
                <option value="credit" selected>Credit</option>
            </select>
        </div>

        <div>
            <label class="text-sm font-medium text-gray-700">Patient</label>
            <select name="patient_id" id="patientSelect" class="w-full mt-1">
                <option value="">Select Patient</option>
                <option value="create_new" class="text-blue-600 font-medium">+ Create New Patient</option>
                @foreach($patients as $patient)
                <option value="{{ $patient->id }}">
                    ID: {{ $patient->patient_id }} - {{ $patient->full_name }}
                    @if($patient->phone) - {{ $patient->phone }} @endif
                </option>
                @endforeach
            </select>

            <!-- Loading indicator for patient selection -->
            <div id="patientLoadingIndicator" class="hidden mt-2">
                <div class="flex items-center text-sm text-blue-600">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Checking services...
                </div>
            </div>
        </div>

        @if(isset($careOfs) && $careOfs->count() > 0)
        <div>
            <label class="text-sm font-medium text-gray-700">Care Of (Optional)</label>
            <select name="care_of_id" id="careOfSelect" class="w-full mt-1">
                <option value="">Select Care Of</option>
                @foreach($careOfs as $careOf)
                <option value="{{ $careOf->id }}"
                    data-commission-rate="{{ $careOf->commission_rate }}"
                    data-commission-type="{{ $careOf->commission_type }}"
                    data-fixed-amount="{{ $careOf->fixed_commission_amount }}">
                    {{ $careOf->name }} - {{ $careOf->commission_type === 'fixed' ? '৳' . number_format($careOf->fixed_commission_amount, 2) : $careOf->commission_rate . '%' }}
                </option>
                @endforeach
            </select>
        </div>
        @endif

        @if(isset($doctors) && $doctors->count() > 0)
        <div>
            <label class="text-sm font-medium text-gray-700">Advised By Doctor (Optional)</label>
            <select name="doctor_id" id="doctorSelect" class="w-full mt-1">
                <option value="">Select Doctor</option>
                @foreach($doctors as $doctor)
                <option value="{{ $doctor->id }}">
                    Dr. {{ $doctor->name }}
                    @if($doctor->specialization) - {{ $doctor->specialization }} @endif
                </option>
                @endforeach
            </select>
        </div>
        @endif
    </div>

    <!-- Services Container -->
    <div id="servicesContainer" class="flex-1 overflow-y-auto p-3 space-y-3">
        <!-- Service lines will be added here dynamically -->
        <div class="text-center text-gray-500 py-8" id="emptyState">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <p class="text-sm">No services added yet</p>
            <p class="text-xs text-gray-400">Select a patient first, then add services from the left panel</p>
        </div>
    </div>

    <!-- Totals Section -->
    <div class="border-t bg-gray-50 p-3 sticky bottom-0">
        <div class="space-y-2">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Sub Total:</span>
                <span id="subtotal" class="font-medium">৳0.00</span>
            </div>

            <!-- Commission Display (Internal Only) -->
            @if(isset($careOfs) && $careOfs->count() > 0)
            <div id="commissionDisplay" class="hidden bg-orange-50 p-3 rounded border border-orange-200 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-orange-700">Commission (Internal):</span>
                    <span id="commissionAmount" class="font-bold text-sm text-orange-700">৳0.00</span>
                </div>

                <!-- Commission Details -->
                <div id="commissionDetails" class="text-xs text-orange-600 space-y-1">
                    <div class="flex justify-between">
                        <span>Care Of:</span>
                        <span id="commissionCareOfName">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Type:</span>
                        <span id="commissionType">-</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Rate/Amount:</span>
                        <div class="flex items-center space-x-1">
                            <input type="number" id="commissionRateInput"
                                class="w-16 px-1 py-0.5 text-xs border border-orange-300 rounded text-center"
                                step="0.01" min="0" style="display: none;">
                            <span id="commissionRateDisplay">-</span>
                            <button type="button" id="editCommissionBtn"
                                class="text-orange-600 hover:text-orange-800 ml-1"
                                title="Edit commission rate">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Commission Options -->
                <div class="flex items-center justify-between pt-2 border-t border-orange-200">
                    <label class="flex items-center text-xs text-orange-700">
                        <input type="checkbox" id="addCommissionToLedger" checked
                            class="mr-1 text-orange-600 focus:ring-orange-500">
                        Add to Care Of ledger
                    </label>
                    <div class="text-xs text-orange-600">
                        * Not added to customer bill
                    </div>
                </div>
            </div>
            @endif

            <div class="flex justify-between items-center">
                <span class="text-gray-600">Discount:</span>
                <input type="number" name="discount" id="discount"
                    class="w-24 text-right rounded border-gray-300 py-1 text-sm"
                    value="0" step="0.01" min="0" readonly
                    title="Discount is disabled"
                    onmouseover="this.style.cursor='not-allowed'"
                    onclick="this.style.cursor='not-allowed'">
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Round Off:</span>
                <input type="number" name="round_off" id="roundOff"
                    class="w-24 text-right rounded border-gray-300 py-1 text-sm"
                    value="0" step="0.01">
            </div>
            <div class="flex justify-between items-center pt-2 border-t">
                <span class="font-medium">Grand Total:</span>
                <span id="grandTotal" class="text-lg font-bold text-blue-600">৳0.00</span>
            </div>
        </div>

        <div class="flex flex-col space-y-3 mt-4">
            <!-- Submit Invoice Button -->
            <button type="button" id="submitInvoice"
                class="w-full bg-gradient-to-r from-gray-300 to-gray-400 text-gray-500 font-bold py-3 px-6 rounded-lg cursor-not-allowed flex items-center justify-center group"
                disabled>
                <span class="inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span id="submitButtonText">Select Patient First</span>
                </span>
            </button>

            <!-- Clear Button -->
            <button type="button" onclick="clearInvoiceForm()" id="clearBtn"
                class="w-full bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 text-gray-700 font-bold py-3 px-6 rounded-lg transition duration-300 ease-in-out transform hover:scale-105 shadow-md flex items-center justify-center group">
                <span class="inline-flex items-center">
                    <svg class="w-5 h-5 mr-2 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Clear Form</span>
                </span>
            </button>
        </div>
    </div>

    <input type="hidden" name="subtotal" id="subtotalInput">
    <input type="hidden" name="grand_total" id="grandTotalInput">
    <input type="hidden" name="commission_amount" id="commissionAmountInput">
</form>

<!-- Customer Creation Modal -->
<div id="customerCreationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Create New Patient</h3>
                <button type="button" id="closeCustomerModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Customer Creation Form -->
            <form id="customerCreationForm" class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">First Name *</label>
                        <input type="text" name="first_name" id="customerFirstName" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Last Name (Optional)</label>
                        <input type="text" name="last_name" id="customerLastName"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone Number *</label>
                    <input type="tel" name="phone" id="customerPhone" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Gender *</label>
                        <select name="gender" id="customerGender" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Age *</label>
                        <input type="number" name="age" id="customerAge" required min="1" max="150"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Address (Optional)</label>
                    <textarea name="address" id="customerAddress" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                </div>

                @if(isset($doctors) && $doctors->count() > 0)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Advised By Doctor (Optional)</label>
                    <select name="advised_by_doctor_id" id="customerAdvisedByDoctor"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Select Doctor</option>
                        @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">
                            Dr. {{ $doctor->name }}
                            @if($doctor->specialization) - {{ $doctor->specialization }} @endif
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                    <button type="button" id="cancelCustomerCreation"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" id="saveCustomerBtn"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span id="saveCustomerText">Create Patient</span>
                        <svg id="saveCustomerSpinner" class="hidden animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Template for consultation line -->
<template id="consultationLineTemplate">
    <div class="service-line-item consultation-line border border-blue-200 rounded-lg p-3 bg-blue-50" data-appointment-id="">
        <!-- Consultation Header -->
        <div class="flex justify-between items-start mb-2">
            <div class="flex-grow">
                <h4 class="service-name font-medium text-blue-800 text-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="consultation-name"></span>
                </h4>
                <p class="service-category text-xs text-blue-600">Consultation Fee</p>
            </div>
            <button type="button" onclick="removeServiceLine(this)"
                class="text-red-500 hover:text-red-700 p-1 ml-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Consultation Details -->
        <div class="grid grid-cols-3 gap-3 items-center">
            <div>
                <label class="text-xs text-gray-600">Quantity:</label>
                <input type="number" class="quantity w-full rounded border-gray-300 text-sm py-1"
                    min="1" value="1" required readonly>
            </div>
            <div>
                <label class="text-xs text-gray-600">Consultation Fee:</label>
                <input type="number" step="0.01" class="unit-price w-full rounded border-gray-300 text-sm py-1"
                    min="0" required>
            </div>
            <div class="text-right">
                <label class="text-xs text-gray-600">Line Total:</label>
                <div class="text-sm font-bold text-blue-600">৳<span class="line-total">0.00</span></div>
            </div>
        </div>
    </div>
</template>

<!-- Template for booking line -->
<template id="bookingLineTemplate">
    <div class="service-line-item booking-line border border-green-200 rounded-lg p-3 bg-green-50" data-booking-id="">
        <!-- Booking Header -->
        <div class="flex justify-between items-start mb-2">
            <div class="flex-grow">
                <h4 class="service-name font-medium text-green-800 text-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2" />
                    </svg>
                    <span class="booking-name"></span>
                </h4>
                <p class="service-category text-xs text-green-600">Booking Service</p>
                <p class="booking-details text-xs text-gray-500"></p>
            </div>
            <button type="button" onclick="removeServiceLine(this)"
                class="text-red-500 hover:text-red-700 p-1 ml-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Booking Details -->
        <div class="grid grid-cols-3 gap-3 items-center">
            <div>
                <label class="text-xs text-gray-600">Quantity:</label>
                <input type="number" class="quantity w-full rounded border-gray-300 text-sm py-1"
                    min="1" value="1" required readonly>
            </div>
            <div>
                <label class="text-xs text-gray-600">Service Fee:</label>
                <input type="number" step="0.01" class="unit-price w-full rounded border-gray-300 text-sm py-1"
                    min="0" required>
            </div>
            <div class="text-right">
                <label class="text-xs text-gray-600">Line Total:</label>
                <div class="text-sm font-bold text-green-600">৳<span class="line-total">0.00</span></div>
            </div>
        </div>
    </div>
</template>

<!-- Template for test line -->
<template id="testLineTemplate">
    <div class="service-line-item test-line border border-gray-200 rounded-lg p-3 bg-white" data-test-id="">
        <!-- Test Header -->
        <div class="flex justify-between items-start mb-2">
            <div class="flex-grow">
                <h4 class="service-name font-medium text-gray-800 text-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="test-name"></span>
                </h4>
                <p class="service-category text-xs text-gray-500"></p>
            </div>
            <button type="button" onclick="removeServiceLine(this)"
                class="text-red-500 hover:text-red-700 p-1 ml-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Test Details -->
        <div class="grid grid-cols-3 gap-3 items-center">
            <div>
                <label class="text-xs text-gray-600">Quantity:</label>
                <input type="number" class="quantity w-full rounded border-gray-300 text-sm py-1"
                    min="1" value="1" required>
            </div>
            <div>
                <label class="text-xs text-gray-600">Unit Price:</label>
                <input type="number" step="0.01" class="unit-price w-full rounded border-gray-300 text-sm py-1"
                    min="0" required>
            </div>
            <div class="text-right">
                <label class="text-xs text-gray-600">Line Total:</label>
                <div class="text-sm font-bold text-blue-600">৳<span class="line-total">0.00</span></div>
            </div>
        </div>
    </div>
</template>

<!-- Template for commission line -->
<template id="commissionLineTemplate">
    <div class="service-line-item commission-line border border-orange-200 rounded-lg p-3 bg-orange-50" data-care-of-id="">
        <!-- Commission Header -->
        <div class="flex justify-between items-start mb-2">
            <div class="flex-grow">
                <h4 class="service-name font-medium text-orange-800 text-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                    <span class="commission-name"></span>
                </h4>
                <p class="service-category text-xs text-orange-600">Commission Fee</p>
            </div>
            <button type="button" onclick="removeServiceLine(this)"
                class="text-red-500 hover:text-red-700 p-1 ml-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Commission Details -->
        <div class="grid grid-cols-3 gap-3 items-center">
            <div>
                <label class="text-xs text-gray-600">Quantity:</label>
                <input type="number" class="quantity w-full rounded border-gray-300 text-sm py-1"
                    min="1" value="1" required readonly>
            </div>
            <div>
                <label class="text-xs text-gray-600">Commission Amount:</label>
                <input type="number" step="0.01" class="unit-price w-full rounded border-gray-300 text-sm py-1"
                    min="0" required>
            </div>
            <div class="text-right">
                <label class="text-xs text-gray-600">Line Total:</label>
                <div class="text-sm font-bold text-orange-600">৳<span class="line-total">0.00</span></div>
            </div>
        </div>
    </div>
</template>

<script>
    // Global state variables
    window.isPatientLoading = false;
    window.hasPatientSelected = false;
    window.currentCommissionRate = 0;
    window.currentCommissionType = 'percentage';
    window.currentFixedCommissionAmount = 0;

    document.addEventListener('DOMContentLoaded', function() {
        console.log('Invoice form script loaded');

        // Initialize button state
        updateSubmitButtonState();

        // Patient selection handler
        const patientSelect = document.getElementById('patientSelect');
        if (patientSelect) {
            console.log('Patient select found, adding event listener');

            // Handle both regular change and Select2 change events
            patientSelect.addEventListener('change', function() {
                console.log('Patient select changed (native event):', this.value);
                handlePatientChange(this.value);
            });

            // Also handle Select2 specific events
            $('#patientSelect').on('select2:select', function(e) {
                console.log('Patient select changed (Select2 event):', e.params.data.id);
                handlePatientChange(e.params.data.id);
            });

            $('#patientSelect').on('select2:clear', function(e) {
                console.log('Patient select cleared (Select2 event)');
                handlePatientChange('');
            });
        } else {
            console.error('Patient select element not found!');
        }

        // Care Of selection handler
        const careOfSelect = document.getElementById('careOfSelect');
        if (careOfSelect) {
            careOfSelect.addEventListener('change', function() {
                handleCareOfChange(this);
            });

            $('#careOfSelect').on('select2:select', function(e) {
                handleCareOfChange(this);
            });

            $('#careOfSelect').on('select2:clear', function(e) {
                handleCareOfChange(this);
            });
        }

        // Add event listeners for discount and round off inputs
        document.getElementById('discount')?.addEventListener('input', function() {
            if (window.medicalCalculator) {
                window.medicalCalculator.scheduleCalculation();
            }
        });

        document.getElementById('roundOff')?.addEventListener('input', function() {
            if (window.medicalCalculator) {
                window.medicalCalculator.scheduleCalculation();
            }
        });

        // Submit form handler
        document.getElementById('submitInvoice')?.addEventListener('click', function() {
            if (!this.disabled) {
                submitInvoiceForm();
            }
        });

        // Hide empty state when services are added
        const observer = new MutationObserver(function(mutations) {
            const container = document.getElementById('servicesContainer');
            const emptyState = document.getElementById('emptyState');
            const hasServices = container.querySelectorAll('.service-line-item').length > 0;

            if (emptyState) {
                emptyState.style.display = hasServices ? 'none' : 'block';
            }
        });

        const servicesContainer = document.getElementById('servicesContainer');
        if (servicesContainer) {
            observer.observe(servicesContainer, {
                childList: true
            });
        }
    });

    function handlePatientChange(patientId) {
        console.log('handlePatientChange called with:', patientId);

        if (patientId) {
            // Patient selected - check for services
            if (typeof window.checkPatientServices === 'function') {
                window.checkPatientServices(patientId);
            } else {
                console.error('checkPatientServices function not available');
                // Fallback - just mark patient as selected
                window.hasPatientSelected = true;
                updateSubmitButtonState();
            }
        } else {
            // Patient cleared
            if (typeof window.clearPatientServices === 'function') {
                window.clearPatientServices();
            }
            window.hasPatientSelected = false;
            updateSubmitButtonState();
        }
    }

    // Update the handleCareOfChange function to not add commission as a line item
    function handleCareOfChange(selectElement) {
        const selectedOption = selectElement.selectedOptions[0];

        if (selectedOption && selectedOption.value) {
            window.currentCommissionRate = parseFloat(selectedOption.dataset.commissionRate) || 0;
            window.currentCommissionType = selectedOption.dataset.commissionType || 'percentage';
            window.currentFixedCommissionAmount = parseFloat(selectedOption.dataset.fixedAmount) || 0;

            console.log('Care Of selected:', {
                rate: window.currentCommissionRate,
                type: window.currentCommissionType,
                fixedAmount: window.currentFixedCommissionAmount
            });

            // Don't add commission line for fixed amount - just track it
            // Commission will be calculated and sent separately
        } else {
            // Clear commission data
            window.currentCommissionRate = 0;
            window.currentCommissionType = 'percentage';
            window.currentFixedCommissionAmount = 0;

            // Remove existing commission lines
            const commissionLines = document.querySelectorAll('.commission-line');
            commissionLines.forEach(line => line.remove());
        }

        // Recalculate totals to update commission
        if (window.medicalCalculator) {
            window.medicalCalculator.scheduleCalculation();
        }
    }



    function updateSubmitButtonState() {
        const submitBtn = document.getElementById('submitInvoice');
        const submitText = document.getElementById('submitButtonText');

        if (!submitBtn || !submitText) return;

        console.log('Updating submit button state:', {
            isPatientLoading: window.isPatientLoading,
            hasPatientSelected: window.hasPatientSelected
        });

        if (window.isPatientLoading) {
            // Loading state
            submitBtn.disabled = true;
            submitBtn.className = 'w-full bg-gradient-to-r from-gray-300 to-gray-400 text-gray-500 font-bold py-3 px-6 rounded-lg cursor-not-allowed flex items-center justify-center group';
            submitText.textContent = 'Checking services...';
            submitBtn.querySelector('svg').classList.add('animate-spin');
        } else if (!window.hasPatientSelected) {
            // No patient selected
            submitBtn.disabled = true;
            submitBtn.className = 'w-full bg-gradient-to-r from-gray-300 to-gray-400 text-gray-500 font-bold py-3 px-6 rounded-lg cursor-not-allowed flex items-center justify-center group';
            submitText.textContent = 'Select Patient First';
            submitBtn.querySelector('svg').classList.remove('animate-spin');
        } else {
            // Ready to generate invoice
            submitBtn.disabled = false;
            submitBtn.className = 'w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 ease-in-out transform hover:scale-105 shadow-md flex items-center justify-center group';
            submitText.textContent = 'Generate Invoice';
            submitBtn.querySelector('svg').classList.remove('animate-spin');
        }
    }

    // Make updateSubmitButtonState globally available
    window.updateSubmitButtonState = updateSubmitButtonState;

    // Global function to add test line
    window.addTestLine = function(testData) {
        console.log('Adding test line:', testData);

        // Check for duplicate test
        if (isDuplicateTest(testData.testId)) {
            Swal.fire({
                icon: 'warning',
                title: 'Test Already Added',
                text: 'This test is already in the invoice. Please modify the existing line.',
                showConfirmButton: true,
                confirmButtonText: 'OK'
            });
            return;
        }

        const template = document.getElementById('testLineTemplate');
        if (!template) {
            console.error('Test line template not found');
            return;
        }

        const newLine = template.content.cloneNode(true).querySelector('.test-line');
        if (!newLine) {
            console.error('Could not create test line from template');
            return;
        }

        // Set test data
        newLine.dataset.testId = testData.testId.toString();
        newLine.dataset.serviceType = 'lab_test';
        newLine.dataset.serviceName = testData.testName; // Store service name in dataset

        const testNameElement = newLine.querySelector('.test-name');
        const testCategoryElement = newLine.querySelector('.service-category');
        const quantityElement = newLine.querySelector('.quantity');
        const unitPriceElement = newLine.querySelector('.unit-price');

        if (testNameElement) testNameElement.textContent = testData.testName;
        if (testCategoryElement) testCategoryElement.textContent = testData.category;
        if (quantityElement) quantityElement.value = testData.quantity || 1;
        if (unitPriceElement) unitPriceElement.value = testData.price;

        // Add event listeners
        addEventListenersToServiceLine(newLine);

        // Add to container
        addServiceLineToContainer(newLine);

        // Show success feedback
        Swal.fire({
            icon: 'success',
            title: 'Test Added',
            text: `${testData.testName} has been added to the invoice.`,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    };


    // Global function to add booking line
    window.addBookingLine = function(bookingData) {
        console.log('Adding booking line:', bookingData);

        const template = document.getElementById('bookingLineTemplate');
        if (!template) {
            console.error('Booking line template not found');
            return;
        }

        const newLine = template.content.cloneNode(true).querySelector('.booking-line');
        if (!newLine) {
            console.error('Could not create booking line from template');
            return;
        }

        // Set booking data
        newLine.dataset.bookingId = bookingData.booking_id;
        newLine.dataset.serviceType = 'booking';
        newLine.dataset.serviceName = bookingData.service_name; // Store service name in dataset

        const bookingName = newLine.querySelector('.booking-name');
        const bookingDetails = newLine.querySelector('.booking-details');
        const unitPriceElement = newLine.querySelector('.unit-price');

        if (bookingName) {
            bookingName.textContent = bookingData.service_name;
        }

        if (bookingDetails) {
            bookingDetails.textContent = bookingData.service_details || `${bookingData.booking_date} ${bookingData.booking_time || ''}`;
        }

        if (unitPriceElement) {
            unitPriceElement.value = bookingData.service_fee;
        }

        // Add event listeners
        addEventListenersToServiceLine(newLine);

        // Add to container
        addServiceLineToContainer(newLine);

        // Show success feedback
        Swal.fire({
            icon: 'success',
            title: 'Booking Added',
            text: `${bookingData.service_name} has been added to the invoice.`,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    };

    // Global function to add consultation line
    // Global function to add consultation line
    window.addConsultationLine = function(consultationData) {
        console.log('Adding consultation line:', consultationData);

        const template = document.getElementById('consultationLineTemplate');
        if (!template) {
            console.error('Consultation line template not found');
            return;
        }

        const newLine = template.content.cloneNode(true).querySelector('.consultation-line');
        if (!newLine) {
            console.error('Could not create consultation line from template');
            return;
        }

        // Set consultation data
        newLine.dataset.appointmentId = consultationData.appointment_id;
        newLine.dataset.serviceType = 'consultation';
        newLine.dataset.serviceName = consultationData.service_name; // Store service name in dataset

        const consultationName = newLine.querySelector('.consultation-name');
        const unitPriceElement = newLine.querySelector('.unit-price');

        if (consultationName) {
            consultationName.textContent = consultationData.service_name;
        }

        if (unitPriceElement) {
            unitPriceElement.value = consultationData.consultation_fee;
        }

        // Add event listeners
        addEventListenersToServiceLine(newLine);

        // Add to container
        addServiceLineToContainer(newLine);

        // Show success feedback
        Swal.fire({
            icon: 'info',
            title: 'Consultation Added',
            text: `${consultationData.service_name} has been added to the invoice.`,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    };


    // Global function to add commission line
    // Global function to add commission line
    window.addCommissionLine = function(commissionData) {
        console.log('Adding commission line:', commissionData);

        // Remove existing commission lines first
        const existingCommissionLines = document.querySelectorAll('.commission-line');
        existingCommissionLines.forEach(line => line.remove());

        const template = document.getElementById('commissionLineTemplate');
        if (!template) {
            console.error('Commission line template not found');
            return;
        }

        const newLine = template.content.cloneNode(true).querySelector('.commission-line');
        if (!newLine) {
            console.error('Could not create commission line from template');
            return;
        }

        // Set commission data
        newLine.dataset.careOfId = commissionData.care_of_id;
        newLine.dataset.serviceType = 'commission';
        newLine.dataset.serviceName = commissionData.service_name;
        newLine.dataset.commissionType = commissionData.commission_type || 'percentage';
        newLine.dataset.commissionRate = commissionData.commission_rate || 0;

        const commissionName = newLine.querySelector('.commission-name');
        const unitPriceElement = newLine.querySelector('.unit-price');

        if (commissionName) {
            commissionName.textContent = commissionData.service_name;
        }

        if (unitPriceElement) {
            unitPriceElement.value = commissionData.commission_amount;

            // For percentage commission, make it read-only since it's calculated
            if (commissionData.commission_type === 'percentage') {
                unitPriceElement.readOnly = true;
                unitPriceElement.style.backgroundColor = '#f3f4f6';
                unitPriceElement.title = 'Calculated automatically based on subtotal';
            }
        }

        // Add event listeners
        addEventListenersToServiceLine(newLine);

        // Add to container
        addServiceLineToContainer(newLine);
    };


    // Helper function to add event listeners to service lines
    function addEventListenersToServiceLine(line) {
        const quantityElement = line.querySelector('.quantity');
        const unitPriceElement = line.querySelector('.unit-price');

        if (quantityElement) {
            quantityElement.addEventListener('input', () => {
                if (window.medicalCalculator) {
                    window.medicalCalculator.scheduleCalculation();
                }
            });
        }

        if (unitPriceElement) {
            unitPriceElement.addEventListener('input', () => {
                if (window.medicalCalculator) {
                    window.medicalCalculator.scheduleCalculation();
                }
            });
        }
    }

    // Helper function to add service line to container
    function addServiceLineToContainer(line) {
        const container = document.getElementById('servicesContainer');
        if (!container) {
            console.error('Services container not found');
            return;
        }

        // Add service line at the top (but after any existing consultation/booking lines)
        const existingLines = container.querySelectorAll('.service-line-item');
        const consultationLines = container.querySelectorAll('.consultation-line');
        const bookingLines = container.querySelectorAll('.booking-line');
        const commissionLines = container.querySelectorAll('.commission-line');

        // Insert order: consultations first, then bookings, then tests, then commission
        if (line.classList.contains('consultation-line')) {
            // Add consultation at the very top
            const firstChild = container.firstChild;
            if (firstChild && firstChild.id !== 'emptyState') {
                container.insertBefore(line, firstChild);
            } else {
                container.appendChild(line);
            }
        } else if (line.classList.contains('booking-line')) {
            // Add booking after consultations but before tests
            const lastConsultation = consultationLines[consultationLines.length - 1];
            if (lastConsultation && lastConsultation.nextSibling) {
                container.insertBefore(line, lastConsultation.nextSibling);
            } else if (consultationLines.length > 0) {
                container.insertBefore(line, consultationLines[0].nextSibling);
            } else {
                const firstChild = container.firstChild;
                if (firstChild && firstChild.id !== 'emptyState') {
                    container.insertBefore(line, firstChild);
                } else {
                    container.appendChild(line);
                }
            }
        } else if (line.classList.contains('commission-line')) {
            // Add commission at the very end
            container.appendChild(line);
        } else {
            // Add test after consultations and bookings but before commission
            const firstCommission = commissionLines[0];
            if (firstCommission) {
                container.insertBefore(line, firstCommission);
            } else {
                container.appendChild(line);
            }
        }

        // Recalculate totals
        if (window.medicalCalculator) {
            window.medicalCalculator.scheduleCalculation();
        }
    }

    // Helper function to check for duplicate tests
    function isDuplicateTest(testId) {
        console.log('Checking for duplicate test ID:', testId);

        const existingTests = document.querySelectorAll('#servicesContainer .test-line');
        console.log('Existing test lines found:', existingTests.length);

        for (let i = 0; i < existingTests.length; i++) {
            const existingTestId = existingTests[i].dataset.testId;
            console.log('Comparing with existing test ID:', existingTestId);

            if (existingTestId && existingTestId === testId.toString()) {
                console.log('Duplicate found!');
                return true;
            }
        }

        console.log('No duplicate found');
        return false;
    }

    // Global function to clear patient services
    window.clearPatientServices = function() {
        console.log('Clearing patient services');

        const consultationLines = document.querySelectorAll('.consultation-line');
        const bookingLines = document.querySelectorAll('.booking-line');

        consultationLines.forEach(line => line.remove());
        bookingLines.forEach(line => line.remove());

        if (window.medicalCalculator) {
            window.medicalCalculator.scheduleCalculation();
        }
    };

    // Function to remove service line
    function removeServiceLine(button) {
        const line = button.closest('.service-line-item');
        if (line) {
            const serviceName = line.querySelector('.service-name').textContent;

            Swal.fire({
                title: 'Remove Service?',
                text: `Are you sure you want to remove "${serviceName}" from the invoice?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    line.remove();
                    if (window.medicalCalculator) {
                        window.medicalCalculator.scheduleCalculation();
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Removed',
                        text: 'Service has been removed from the invoice.',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            });
        }
    }

    // Function to clear invoice form
    function clearInvoiceForm() {
        Swal.fire({
            title: 'Clear Invoice Form?',
            text: 'This will remove all services from the invoice.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, clear it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Clear all service lines
                document.getElementById('servicesContainer').innerHTML = `
                    <div class="text-center text-gray-500 py-8" id="emptyState">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-sm">No services added yet</p>
                        <p class="text-xs text-gray-400">Select a patient first, then add services from the left panel</p>
                    </div>
                `;

                // Reset patient selection
                document.getElementById('patientSelect').value = '';
                $('#patientSelect').trigger('change');

                // Reset care of selection
                const careOfSelect = document.getElementById('careOfSelect');
                if (careOfSelect) {
                    careOfSelect.value = '';
                    $('#careOfSelect').trigger('change');
                }

                // Reset form values
                document.getElementById('discount').value = '0';
                document.getElementById('roundOff').value = '0';

                // Reset commission data
                window.currentCommissionRate = 0;
                window.currentCommissionType = 'percentage';
                window.currentFixedCommissionAmount = 0;

                // Reset states
                window.hasPatientSelected = false;
                updateSubmitButtonState();

                // Recalculate totals
                if (window.medicalCalculator) {
                    window.medicalCalculator.calculateTotals();
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Cleared',
                    text: 'Invoice form has been cleared.',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        });
    }

    // Function to submit invoice form
    function submitInvoiceForm() {
        const form = document.getElementById('medicalInvoiceForm');
        const servicesContainer = document.getElementById('servicesContainer');
        const serviceLines = servicesContainer.querySelectorAll('.service-line-item');

        if (serviceLines.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Services Added',
                text: 'Please add at least one service to the invoice.',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Collect all service line data (excluding commission lines)
        const testLines = [];

        serviceLines.forEach((line, index) => {
            const serviceType = line.dataset.serviceType || 'lab_test';

            // Skip commission lines - they're not part of customer invoice
            if (serviceType === 'commission') {
                return;
            }

            const quantity = parseFloat(line.querySelector('.quantity').value) || 1;
            const unitPrice = parseFloat(line.querySelector('.unit-price').value) || 0;
            const lineDiscount = 0;

            // Get service name from the line
            let serviceName = '';
            if (serviceType === 'consultation') {
                serviceName = line.querySelector('.consultation-name')?.textContent || 'Consultation Service';
            } else if (serviceType === 'booking') {
                serviceName = line.querySelector('.booking-name')?.textContent || 'Booking Service';
            } else if (serviceType === 'lab_test') {
                serviceName = line.querySelector('.test-name')?.textContent || 'Lab Test';
            }

            const lineData = {
                service_type: serviceType,
                service_name: serviceName,
                quantity: quantity,
                unit_price: unitPrice,
                line_discount: lineDiscount
            };

            // Add service-specific data
            if (serviceType === 'lab_test') {
                lineData.lab_test_id = line.dataset.testId;
            } else if (serviceType === 'consultation') {
                lineData.appointment_id = line.dataset.appointmentId;
            } else if (serviceType === 'booking') {
                lineData.booking_id = line.dataset.bookingId;
            }

            testLines.push(lineData);
        });

        // Show loading state
        const submitBtn = document.getElementById('submitInvoice');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Creating Invoice...';

        // Calculate commission amount (both percentage and fixed)
        let commissionAmount = 0;
        const careOfId = form.querySelector('[name="care_of_id"]')?.value;

        if (careOfId) {
            // Try to get from hidden input first
            const commissionAmountInput = document.getElementById('commissionAmountInput');
            if (commissionAmountInput && commissionAmountInput.value && parseFloat(commissionAmountInput.value) > 0) {
                commissionAmount = parseFloat(commissionAmountInput.value) || 0;
            } else {
                // Fallback: calculate manually
                const subtotal = parseFloat(document.getElementById('subtotalInput')?.value) || 0;

                if (window.currentCommissionType === 'fixed' && window.currentFixedCommissionAmount > 0) {
                    commissionAmount = window.currentFixedCommissionAmount;
                } else if (window.currentCommissionType === 'percentage' && window.currentCommissionRate > 0) {
                    commissionAmount = (subtotal * window.currentCommissionRate) / 100;
                }
            }
        }

        console.log('Commission calculation debug:', {
            careOfId: careOfId,
            commissionType: window.currentCommissionType,
            commissionRate: window.currentCommissionRate,
            fixedAmount: window.currentFixedCommissionAmount,
            calculatedCommission: commissionAmount,
            hiddenInputValue: document.getElementById('commissionAmountInput')?.value,
            subtotal: parseFloat(document.getElementById('subtotalInput')?.value) || 0
        });

        // Prepare request data as JSON
        const requestData = {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            invoice_date: form.querySelector('[name="invoice_date"]').value,
            payment_method: form.querySelector('[name="payment_method"]').value,
            patient_id: form.querySelector('[name="patient_id"]').value,
            care_of_id: careOfId,
            doctor_id: form.querySelector('[name="doctor_id"]').value || null, // Add doctor_id
            discount: parseFloat(form.querySelector('[name="discount"]').value) || 0,
            round_off: parseFloat(form.querySelector('[name="round_off"]').value) || 0,
            commission_amount: commissionAmount, // Send commission amount (percentage or fixed)
            test_lines: testLines // Only service lines, no commission lines
        };

        console.log('Submitting invoice data WITH commission:', requestData);

        // Submit form as JSON
        fetch(form.action, {
                method: 'POST',
                body: JSON.stringify(requestData),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Invoice Created!',
                        text: `Invoice ${data.invoice_number} has been created successfully.`,
                        confirmButtonText: 'View Invoice'
                    }).then((result) => {
                        if (result.isConfirmed && data.redirect_url) {
                            window.location.href = data.redirect_url;
                        }
                    });
                } else {
                    throw new Error(data.message || 'Failed to create invoice');
                }
            })
            .catch(error => {
                console.error('Invoice creation error:', error);

                Swal.fire({
                    icon: 'error',
                    title: 'Invoice Creation Failed',
                    text: error.message || 'An error occurred while creating the invoice. Please try again.',
                    confirmButtonText: 'OK'
                });
            })
            .finally(() => {
                // Restore button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
    }



    // Make functions globally available
    window.removeServiceLine = removeServiceLine;
    window.clearInvoiceForm = clearInvoiceForm;
    window.submitInvoiceForm = submitInvoiceForm;
</script>

<style>
    /* Loading states */
    .cursor-not-allowed {
        cursor: not-allowed !important;
    }

    .cursor-not-allowed:hover {
        transform: none !important;
    }

    /* Service line styling */
    .consultation-line {
        border-left: 4px solid #3b82f6;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    }

    .booking-line {
        border-left: 4px solid #10b981;
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    }

    .test-line {
        border-left: 4px solid #6b7280;
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    }

    .commission-line {
        border-left: 4px solid #f59e0b;
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    }

    .consultation-line .service-name {
        color: #1e40af;
    }

    .booking-line .service-name {
        color: #047857;
    }

    .test-line .service-name {
        color: #374151;
    }

    .commission-line .service-name {
        color: #92400e;
    }

    /* Animation for smooth transitions */
    .service-line-item {
        transition: all 0.3s ease;
    }

    .service-line-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .consultation-line:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    }

    .booking-line:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
    }

    .test-line:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(107, 114, 128, 0.15);
    }

    .commission-line:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15);
    }

    /* Loading spinner animation */
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Hide loading indicator by default */
    .hidden {
        display: none !important;
    }

    /* Commission display styling */
    #commissionDisplay {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        border: 1px solid #f59e0b;
    }

    #commissionDisplay.hidden {
        display: none !important;
    }

    /* Service type icons */
    .consultation-line svg {
        color: #3b82f6;
    }

    .booking-line svg {
        color: #10b981;
    }

    .test-line svg {
        color: #6b7280;
    }

    .commission-line svg {
        color: #f59e0b;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .service-line-item .grid-cols-3 {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .service-line-item .grid-cols-3>div {
            text-align: left;
        }

        .service-line-item .text-right {
            text-align: left !important;
        }
    }

    /* Input focus states */
    .service-line-item input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 1px #3b82f6;
    }

    /* Button hover effects */
    .service-line-item button:hover {
        transform: scale(1.1);
    }

    /* Smooth transitions for all interactive elements */
    .service-line-item input,
    .service-line-item button {
        transition: all 0.2s ease;
    }
</style>