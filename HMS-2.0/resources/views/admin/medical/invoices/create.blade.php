@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="bg-blue-600 shadow-sm">
        <div class="flex justify-center items-center p-2">
            <div class="bg-blue-500 rounded-full">
                <div class="px-4 py-1.5 text-sm md:text-base md:px-6 md:py-2 rounded-full font-medium bg-white text-blue-600">
                    Medical Invoice Generation
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white">
        <!-- Mobile Toggle Button (visible only on mobile) -->
        <button id="toggleFormBtn" class="md:hidden fixed right-0 top-1/2 transform -translate-y-1/2 bg-blue-600 text-white p-2 rounded-l-lg shadow-lg z-50">
            <svg class="w-6 h-6 toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2">
            <!-- Test List Section -->
            <div class="lg:col-span-1 border-r">
                @include('admin.medical.invoices.partials.test-selection')
            </div>

            <!-- Invoice Form Section -->
            <div id="invoiceForm" class="lg:col-span-1 transform translate-x-full md:translate-x-0 fixed md:relative top-0 right-0 h-full w-full md:w-auto bg-white transition-transform duration-300 ease-in-out z-40 overflow-y-auto">
                @include('admin.medical.invoices.partials.invoice-form')
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@vite('resources/js/medical-invoice-patient-services.js')
@endpush




<script>
    // Medical Invoice Calculator (Updated to handle commission)
    class MedicalInvoiceCalculator {
        constructor() {
            this.isCalculating = false;
            this.debounceTimer = null;
            this.init();
        }

        init() {
            this.setupEventListeners();
        }

        setupEventListeners() {
            const servicesContainer = document.getElementById('servicesContainer');
            if (servicesContainer) {
                servicesContainer.addEventListener('input', (e) => {
                    if (e.target.classList.contains('quantity') ||
                        e.target.classList.contains('unit-price')) {
                        this.scheduleCalculation();
                    }
                });
            }

            // Add event listeners for discount and round off inputs
            const discountInput = document.getElementById('discount');
            const roundOffInput = document.getElementById('roundOff');

            if (discountInput) {
                discountInput.addEventListener('input', () => {
                    this.scheduleCalculation();
                });
                discountInput.addEventListener('change', () => {
                    this.scheduleCalculation();
                });
            }

            if (roundOffInput) {
                roundOffInput.addEventListener('input', () => {
                    this.scheduleCalculation();
                });
                roundOffInput.addEventListener('change', () => {
                    this.scheduleCalculation();
                });
            }
        }

        scheduleCalculation() {
            if (this.debounceTimer) {
                clearTimeout(this.debounceTimer);
            }
            this.debounceTimer = setTimeout(() => {
                this.calculateTotals();
            }, 150);
        }

        // Update the calculateTotals method to ensure commission is calculated
        calculateTotals() {
            if (this.isCalculating) return;
            this.isCalculating = true;

            try {
                let subtotal = 0;
                const serviceLines = document.querySelectorAll('.service-line-item:not(.commission-line)');

                // Calculate line totals and subtotal (excluding commission lines)
                serviceLines.forEach((line) => {
                    const quantity = parseFloat(line.querySelector('.quantity')?.value) || 0;
                    const unitPrice = parseFloat(line.querySelector('.unit-price')?.value) || 0;
                    const lineTotal = quantity * unitPrice;

                    const lineTotalElement = line.querySelector('.line-total');
                    if (lineTotalElement) {
                        lineTotalElement.textContent = lineTotal.toFixed(2);
                    }

                    subtotal += lineTotal;
                });

                // Calculate commission amount (for internal tracking only)
                let commissionAmount = 0;
                const careOfId = document.getElementById('careOfSelect')?.value;

                if (careOfId && (window.currentCommissionRate > 0 || window.currentFixedCommissionAmount > 0)) {
                    if (window.currentCommissionType === 'fixed') {
                        commissionAmount = window.currentFixedCommissionAmount || 0;
                    } else {
                        commissionAmount = (subtotal * window.currentCommissionRate) / 100;
                    }
                }

                // Get discount and round off values
                const discountElement = document.getElementById('discount');
                const roundOffElement = document.getElementById('roundOff');

                const discount = parseFloat(discountElement?.value) || 0;
                const roundOff = parseFloat(roundOffElement?.value) || 0;

                // Calculate grand total for CUSTOMER (subtotal - discount + roundOff)
                // Commission is NOT added to customer's bill
                const customerGrandTotal = subtotal - discount + roundOff;

                // Update display elements
                const subtotalElement = document.getElementById('subtotal');
                const grandTotalElement = document.getElementById('grandTotal');
                const subtotalInputElement = document.getElementById('subtotalInput');
                const grandTotalInputElement = document.getElementById('grandTotalInput');
                const commissionAmountElement = document.getElementById('commissionAmount');
                const commissionAmountInputElement = document.getElementById('commissionAmountInput');
                const commissionDisplayElement = document.getElementById('commissionDisplay');

                if (subtotalElement) {
                    subtotalElement.textContent = '৳' + subtotal.toFixed(2);
                }

                if (grandTotalElement) {
                    grandTotalElement.textContent = '৳' + customerGrandTotal.toFixed(2);
                }

                if (subtotalInputElement) {
                    subtotalInputElement.value = subtotal.toFixed(2);
                }

                if (grandTotalInputElement) {
                    // This is what the customer pays (without commission)
                    grandTotalInputElement.value = customerGrandTotal.toFixed(2);
                }

                // Update commission display and hidden input
                if (commissionAmountElement) {
                    commissionAmountElement.textContent = '৳' + commissionAmount.toFixed(2);
                }

                if (commissionAmountInputElement) {
                    commissionAmountInputElement.value = commissionAmount.toFixed(2);
                    console.log('Commission amount set in hidden input:', commissionAmount.toFixed(2));
                }

                if (commissionDisplayElement) {
                    if (commissionAmount > 0) {
                        commissionDisplayElement.classList.remove('hidden');
                    } else {
                        commissionDisplayElement.classList.add('hidden');
                    }
                }

                console.log('Calculation completed:', {
                    subtotal: subtotal.toFixed(2),
                    discount: discount.toFixed(2),
                    roundOff: roundOff.toFixed(2),
                    commissionAmount: commissionAmount.toFixed(2),
                    customerGrandTotal: customerGrandTotal.toFixed(2),
                    commissionType: window.currentCommissionType,
                    commissionRate: window.currentCommissionRate,
                    fixedAmount: window.currentFixedCommissionAmount
                });

            } catch (error) {
                console.error('Calculation error:', error);
            } finally {
                this.isCalculating = false;
            }
        }
    }

    let medicalCalculator;

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded - Initializing medical invoice system');

        // Initialize calculator
        medicalCalculator = new MedicalInvoiceCalculator();

        // Make calculator globally available
        window.medicalCalculator = medicalCalculator;

        // Initialize Select2 for patient selection
        $('#patientSelect').select2({
            placeholder: 'Search patient by ID, name or phone',
            allowClear: true,
            width: '100%'
        });

        // Initialize Select2 for care of selection if it exists
        const careOfSelect = document.getElementById('careOfSelect');
        if (careOfSelect) {
            $('#careOfSelect').select2({
                placeholder: 'Select Care Of (Optional)',
                allowClear: true,
                width: '100%'
            });
        }

        // Care of selection change handler
        $('#careOfSelect').on('change', function() {
            const careOfId = this.value;
            console.log('Care Of selected:', careOfId);

            if (careOfId) {
                const selectedOption = this.options[this.selectedIndex];
                const commissionRate = parseFloat(selectedOption.dataset.commissionRate) || 0;
                const commissionType = selectedOption.dataset.commissionType || 'percentage';
                const fixedAmount = parseFloat(selectedOption.dataset.fixedAmount) || 0;
                const careOfName = selectedOption.text.split(' - ')[0];

                // Store commission data globally
                window.currentCommissionRate = commissionRate;
                window.currentCommissionType = commissionType;
                window.currentFixedCommissionAmount = fixedAmount;
                window.currentCareOfName = careOfName;

                // Update commission display
                updateCommissionDisplay(careOfName, commissionType, commissionRate, fixedAmount);

                console.log('Commission data updated:', {
                    careOfName,
                    commissionType,
                    commissionRate,
                    fixedAmount
                });
            } else {
                // Clear commission data
                window.currentCommissionRate = 0;
                window.currentCommissionType = 'percentage';
                window.currentFixedCommissionAmount = 0;
                window.currentCareOfName = '';

                // Hide commission display
                const commissionDisplay = document.getElementById('commissionDisplay');
                if (commissionDisplay) {
                    commissionDisplay.classList.add('hidden');
                }

                console.log('Commission data cleared');
            }

            // Recalculate totals
            if (window.medicalCalculator) {
                window.medicalCalculator.scheduleCalculation();
            }
        });

        // Initialize Select2 for doctor selection if it exists
        const doctorSelect = document.getElementById('doctorSelect');
        console.log('Doctor select element:', doctorSelect);
        if (doctorSelect) {
            console.log('Initializing doctor select2');
            $('#doctorSelect').select2({
                placeholder: 'Select Doctor (Optional)',
                allowClear: true,
                width: '100%'
            });

            // Doctor selection change handler
            $('#doctorSelect').on('change', function() {
                const doctorId = this.value;
                console.log('Doctor selected:', doctorId);

                if (doctorId) {
                    const selectedOption = this.options[this.selectedIndex];
                    const doctorName = selectedOption ? selectedOption.text : 'Unknown Doctor';
                    console.log('Doctor name:', doctorName);
                } else {
                    console.log('Doctor cleared');
                }
            });
        } else {
            console.log('Doctor select element not found');
        }

        // Patient selection change handler
        $('#patientSelect').on('change', function() {
            const patientId = this.value;
            console.log('Patient selected:', patientId);

            if (patientId === 'create_new') {
                // Show customer creation modal
                showCustomerCreationModal();
                // Reset the select to empty
                $(this).val('').trigger('change');
                return;
            }

            if (patientId) {
                // Check for patient services
                if (typeof window.checkPatientServices === 'function') {
                    window.checkPatientServices(patientId);
                } else {
                    // Fallback - just mark patient as selected
                    window.hasPatientSelected = true;
                    if (typeof window.updateSubmitButtonState === 'function') {
                        window.updateSubmitButtonState();
                    }
                }
            } else {
                // Patient cleared
                if (typeof window.clearPatientServices === 'function') {
                    window.clearPatientServices();
                }
                window.hasPatientSelected = false;
                if (typeof window.updateSubmitButtonState === 'function') {
                    window.updateSubmitButtonState();
                }
            }
        });

        // Mobile toggle functionality
        const toggleBtn = document.getElementById('toggleFormBtn');
        const formSection = document.getElementById('invoiceForm');
        const toggleIcon = document.querySelector('.toggle-icon');

        if (toggleBtn && formSection && toggleIcon) {
            toggleBtn.addEventListener('click', function() {
                // Check if form is currently open or closed
                const isFormOpen = !formSection.classList.contains('translate-x-full');

                if (isFormOpen) {
                    // Close the form
                    formSection.classList.add('translate-x-full');
                    toggleIcon.classList.remove('rotate-180');
                } else {
                    // Open the form
                    formSection.classList.remove('translate-x-full');
                    toggleIcon.classList.add('rotate-180');
                }
            });

            // Close form when clicking outside on mobile
            formSection.addEventListener('click', function(e) {
                if (e.target === formSection) {
                    formSection.classList.add('translate-x-full');
                    toggleIcon.classList.remove('rotate-180');
                }
            });

            // Close form on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !formSection.classList.contains('translate-x-full')) {
                    formSection.classList.add('translate-x-full');
                    toggleIcon.classList.remove('rotate-180');
                }
            });
        }

        // Submit invoice
        document.getElementById('submitInvoice')?.addEventListener('click', function(e) {
            e.preventDefault();
            if (!this.disabled) {
                submitMedicalInvoice();
            }
        });

        // Initialize submit button state
        window.hasPatientSelected = false;
        if (typeof window.updateSubmitButtonState === 'function') {
            window.updateSubmitButtonState();
        }

        // Customer creation modal event listeners
        setupCustomerCreationModal();
    });

    function submitMedicalInvoice() {
        const submitBtn = document.getElementById('submitInvoice');
        const submitText = document.getElementById('submitButtonText');
        const submitIcon = submitBtn.querySelector('svg');

        // Set loading state
        submitBtn.disabled = true;
        submitBtn.className = 'w-full bg-gradient-to-r from-gray-400 to-gray-500 text-white font-bold py-3 px-6 rounded-lg cursor-not-allowed flex items-center justify-center group';
        submitText.textContent = 'Generating Invoice...';
        submitIcon.classList.add('animate-spin');

        const patientId = document.getElementById('patientSelect').value;
        const invoiceDate = document.querySelector('input[name="invoice_date"]').value;
        const paymentMethod = document.querySelector('select[name="payment_method"]').value;
        const careOfId = document.getElementById('careOfSelect')?.value || null;
        const doctorId = document.getElementById('doctorSelect')?.value || null;

        console.log('Submitting invoice with doctor_id:', doctorId);

        if (!patientId) {
            resetSubmitButton();
            Swal.fire({
                icon: 'error',
                title: 'Patient Required',
                text: 'Please select a patient for the invoice.'
            });
            return;
        }

        const testLines = getInvoiceTestLines();
        const consultationLines = getConsultationLines();
        const bookingLines = getBookingLines();
        const allLines = [...consultationLines, ...bookingLines, ...testLines];

        if (allLines.length === 0) {
            resetSubmitButton();
            Swal.fire({
                icon: 'error',
                title: 'No Items Added',
                text: 'Please add at least one service to the invoice.'
            });
            return;
        }

        // Calculate commission amount (both percentage and fixed)
        let commissionAmount = 0;

        if (careOfId) {
            // Try to get from hidden input first
            const commissionAmountInput = document.getElementById('commissionAmountInput');
            if (commissionAmountInput && commissionAmountInput.value && parseFloat(commissionAmountInput.value) > 0) {
                commissionAmount = parseFloat(commissionAmountInput.value) || 0;
            } else {
                // Fallback: calculate manually
                const subtotal = parseFloat(document.getElementById('subtotalInput').value) || 0;

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
            subtotal: parseFloat(document.getElementById('subtotalInput').value) || 0
        });

        // Check if commission should be added to ledger
        const addCommissionToLedger = document.getElementById('addCommissionToLedger')?.checked ?? true;

        const invoiceData = {
            patient_id: patientId,
            invoice_date: invoiceDate,
            payment_method: paymentMethod,
            care_of_id: careOfId,
            doctor_id: doctorId,
            subtotal: parseFloat(document.getElementById('subtotalInput').value),
            discount: parseFloat(document.getElementById('discount').value) || 0,
            round_off: parseFloat(document.getElementById('roundOff').value) || 0,
            commission_amount: commissionAmount, // Send commission amount (percentage or fixed)
            add_commission_to_ledger: addCommissionToLedger, // Option to add commission to ledger
            grand_total: parseFloat(document.getElementById('grandTotalInput').value),
            test_lines: allLines
        };

        console.log('Submitting invoice data WITH commission:', invoiceData);

        // Submit via AJAX
        fetch('{{ route("admin.medical.invoices.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(invoiceData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Medical invoice created successfully!',
                        showConfirmButton: true,
                        confirmButtonText: 'View Invoice'
                    }).then(() => {
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        } else {
                            window.location.href = '{{ route("admin.medical.invoices.index") }}';
                        }
                    });
                } else {
                    throw new Error(data.message || 'Failed to create invoice');
                }
            })
            .catch(error => {
                console.error('Invoice creation error:', error);
                resetSubmitButton();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to create invoice. Please try again.'
                });
            });
    }



    function getConsultationLines() {
        const lines = [];
        const consultationLines = document.querySelectorAll('.consultation-line:not(.commission-line)');

        consultationLines.forEach(line => {
            const appointmentId = line.dataset.appointmentId;
            const quantity = parseFloat(line.querySelector('.quantity').value) || 1;
            const unitPrice = parseFloat(line.querySelector('.unit-price').value) || 0;
            const lineTotal = quantity * unitPrice;

            if (appointmentId && unitPrice > 0) {
                lines.push({
                    appointment_id: appointmentId,
                    service_type: 'consultation',
                    service_name: line.querySelector('.consultation-name').textContent,
                    quantity: quantity,
                    unit_price: unitPrice,
                    line_total: lineTotal,
                    line_discount: 0
                });
            }
        });

        return lines;
    }

    function getBookingLines() {
        const lines = [];
        const bookingLines = document.querySelectorAll('.booking-line:not(.commission-line)');

        bookingLines.forEach(line => {
            const bookingId = line.dataset.bookingId;
            const quantity = parseFloat(line.querySelector('.quantity').value) || 1;
            const unitPrice = parseFloat(line.querySelector('.unit-price').value) || 0;
            const lineTotal = quantity * unitPrice;

            if (bookingId && unitPrice > 0) {
                lines.push({
                    booking_id: bookingId,
                    service_type: 'booking',
                    service_name: line.querySelector('.booking-name').textContent,
                    quantity: quantity,
                    unit_price: unitPrice,
                    line_total: lineTotal,
                    line_discount: 0
                });
            }
        });

        return lines;
    }

    function getInvoiceTestLines() {
        const lines = [];
        // Only get service lines, NOT commission lines
        const serviceLines = document.querySelectorAll('.service-line-item:not(.commission-line)');

        serviceLines.forEach(line => {
            const serviceType = line.dataset.serviceType;

            // Skip commission lines completely
            if (serviceType === 'commission') {
                return;
            }

            const quantity = parseFloat(line.querySelector('.quantity').value) || 1;
            const unitPrice = parseFloat(line.querySelector('.unit-price').value) || 0;
            const lineTotal = quantity * unitPrice;

            if (unitPrice > 0) {
                const lineData = {
                    service_type: serviceType,
                    service_name: line.dataset.serviceName || '',
                    quantity: quantity,
                    unit_price: unitPrice,
                    line_total: lineTotal,
                    line_discount: 0
                };

                // Add service-specific data
                if (serviceType === 'lab_test') {
                    lineData.lab_test_id = parseInt(line.dataset.testId);
                } else if (serviceType === 'consultation') {
                    lineData.appointment_id = line.dataset.appointmentId;
                } else if (serviceType === 'booking') {
                    lineData.booking_id = line.dataset.bookingId;
                }

                lines.push(lineData);
            }
        });

        return lines;
    }



    // Fixed duplicate check function
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

    function addTestToInvoice(testData) {
        console.log('Adding test to invoice:', testData);

        // Check for duplicate test with proper string comparison
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
            console.error('Could not create new line from template');
            return;
        }

        console.log('Setting test ID:', testData.testId);
        newLine.dataset.testId = testData.testId.toString();

        // Set test details
        const testNameElement = newLine.querySelector('.test-name');
        const testCategoryElement = newLine.querySelector('.service-category');
        const quantityElement = newLine.querySelector('.quantity');
        const unitPriceElement = newLine.querySelector('.unit-price');

        if (testNameElement) testNameElement.textContent = testData.testName;
        if (testCategoryElement) testCategoryElement.textContent = testData.category;
        if (quantityElement) quantityElement.value = testData.quantity || 1;
        if (unitPriceElement) unitPriceElement.value = testData.price;

        // Add event listeners
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

        const container = document.getElementById('servicesContainer');
        if (container) {
            container.appendChild(newLine);
            if (window.medicalCalculator) {
                window.medicalCalculator.scheduleCalculation();
            }
            console.log('Test added successfully');

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
        } else {
            console.error('Services container not found');
        }
    }

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
                // Clear all lines
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
                if (careOfSelect) {
                    careOfSelect.value = '';
                    $('#careOfSelect').trigger('change');
                }

                // Reset doctor selection
                const doctorSelect = document.getElementById('doctorSelect');
                if (doctorSelect) {
                    doctorSelect.value = '';
                    $('#doctorSelect').trigger('change');
                }

                // Reset form values
                document.getElementById('discount').value = '0';
                document.getElementById('roundOff').value = '0';

                // Reset care of selection
                const careOfSelect = document.getElementById('careOfSelect');
                if (careOfSelect) {
                    careOfSelect.value = '';
                    $('#careOfSelect').trigger('change');
                }

                // Reset commission data
                window.currentCommissionRate = 0;
                window.currentCommissionType = 'percentage';
                window.currentFixedCommissionAmount = 0;
                window.currentCareOfName = '';

                // Hide commission display
                const commissionDisplay = document.getElementById('commissionDisplay');
                if (commissionDisplay) {
                    commissionDisplay.classList.add('hidden');
                }

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

    // Initialize global variables
    window.isPatientLoading = false;
    window.hasPatientSelected = false;
    window.currentCommissionRate = 0;
    window.currentCommissionType = 'percentage';
    window.currentFixedCommissionAmount = 0;
    window.currentCareOfName = '';

    // Customer Creation Modal Functions
    function showCustomerCreationModal() {
        document.getElementById('customerCreationModal').classList.remove('hidden');
        document.getElementById('customerFirstName').focus();
    }

    function hideCustomerCreationModal() {
        document.getElementById('customerCreationModal').classList.add('hidden');
        // Reset form
        document.getElementById('customerCreationForm').reset();
    }

    function setupCustomerCreationModal() {
        // Close modal events
        document.getElementById('closeCustomerModal').addEventListener('click', hideCustomerCreationModal);
        document.getElementById('cancelCustomerCreation').addEventListener('click', hideCustomerCreationModal);

        // Close modal when clicking outside
        document.getElementById('customerCreationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideCustomerCreationModal();
            }
        });

        // Handle form submission
        document.getElementById('customerCreationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            createNewCustomer();
        });
    }

    function createNewCustomer() {
        const form = document.getElementById('customerCreationForm');
        const formData = new FormData(form);
        const saveBtn = document.getElementById('saveCustomerBtn');
        const saveText = document.getElementById('saveCustomerText');
        const saveSpinner = document.getElementById('saveCustomerSpinner');

        // Set loading state
        saveBtn.disabled = true;
        saveText.textContent = 'Creating...';
        saveSpinner.classList.remove('hidden');

        // Convert FormData to regular object
        const customerData = {};
        formData.forEach((value, key) => {
            customerData[key] = value;
        });

        // Calculate date of birth from age
        const age = parseInt(customerData.age);
        const currentDate = new Date();
        const birthYear = currentDate.getFullYear() - age;
        customerData.date_of_birth = `${birthYear}-01-01`; // Default to January 1st

        // Get the selected doctor for auto-selection after patient creation
        const selectedDoctorId = customerData.advised_by_doctor_id;

        fetch('{{ route("admin.medical.invoices.create-customer") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(customerData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add new customer to select dropdown
                    const patientSelect = document.getElementById('patientSelect');
                    const newOption = document.createElement('option');
                    newOption.value = data.patient.id;
                    newOption.textContent = `ID: ${data.patient.patient_id} - ${data.patient.full_name}${data.patient.phone ? ' - ' + data.patient.phone : ''}`;

                    // Insert after "Create New Patient" option
                    patientSelect.insertBefore(newOption, patientSelect.children[2]);

                    // Select the new patient
                    $('#patientSelect').val(data.patient.id).trigger('change');

                    // Auto-select the doctor if one was chosen during patient creation
                    if (selectedDoctorId) {
                        const doctorSelect = document.getElementById('doctorSelect');
                        if (doctorSelect) {
                            $('#doctorSelect').val(selectedDoctorId).trigger('change');
                        }
                    }

                    // Hide modal
                    hideCustomerCreationModal();

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Patient Created!',
                        text: `Patient ${data.patient.full_name} has been created successfully. A ledger has also been created automatically.`,
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(data.message || 'Failed to create patient');
                }
            })
            .catch(error => {
                console.error('Error creating customer:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to create patient. Please try again.'
                });
            })
            .finally(() => {
                // Reset button state
                saveBtn.disabled = false;
                saveText.textContent = 'Create Patient';
                saveSpinner.classList.add('hidden');
            });
    }

    // Commission Display and Editing Functions
    function updateCommissionDisplay(careOfName, commissionType, commissionRate, fixedAmount) {
        const commissionDisplay = document.getElementById('commissionDisplay');
        const commissionCareOfName = document.getElementById('commissionCareOfName');
        const commissionTypeElement = document.getElementById('commissionType');
        const commissionRateDisplay = document.getElementById('commissionRateDisplay');

        if (commissionDisplay && commissionCareOfName && commissionTypeElement && commissionRateDisplay) {
            // Show commission display
            commissionDisplay.classList.remove('hidden');

            // Update commission details
            commissionCareOfName.textContent = careOfName;
            commissionTypeElement.textContent = commissionType === 'fixed' ? 'Fixed Amount' : 'Percentage';

            if (commissionType === 'fixed') {
                commissionRateDisplay.textContent = '৳' + fixedAmount.toFixed(2);
            } else {
                commissionRateDisplay.textContent = commissionRate.toFixed(2) + '%';
            }

            // Setup commission editing
            setupCommissionEditing();
        }
    }

    function setupCommissionEditing() {
        const editBtn = document.getElementById('editCommissionBtn');
        const rateDisplay = document.getElementById('commissionRateDisplay');
        const rateInput = document.getElementById('commissionRateInput');

        if (editBtn && rateDisplay && rateInput) {
            // Remove existing event listeners
            editBtn.replaceWith(editBtn.cloneNode(true));
            const newEditBtn = document.getElementById('editCommissionBtn');

            newEditBtn.addEventListener('click', function() {
                // Switch to edit mode
                rateDisplay.style.display = 'none';
                rateInput.style.display = 'inline-block';

                // Set current value in input
                if (window.currentCommissionType === 'fixed') {
                    rateInput.value = window.currentFixedCommissionAmount || 0;
                } else {
                    rateInput.value = window.currentCommissionRate || 0;
                }

                rateInput.focus();
                rateInput.select();
            });

            // Handle input changes
            rateInput.addEventListener('blur', function() {
                saveCommissionRate();
            });

            rateInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    saveCommissionRate();
                }
                if (e.key === 'Escape') {
                    cancelCommissionEdit();
                }
            });
        }
    }

    function saveCommissionRate() {
        const rateInput = document.getElementById('commissionRateInput');
        const rateDisplay = document.getElementById('commissionRateDisplay');

        if (rateInput && rateDisplay) {
            const newValue = parseFloat(rateInput.value) || 0;

            // Update global variables
            if (window.currentCommissionType === 'fixed') {
                window.currentFixedCommissionAmount = newValue;
                rateDisplay.textContent = '৳' + newValue.toFixed(2);
            } else {
                window.currentCommissionRate = newValue;
                rateDisplay.textContent = newValue.toFixed(2) + '%';
            }

            // Switch back to display mode
            rateInput.style.display = 'none';
            rateDisplay.style.display = 'inline-block';

            // Recalculate totals
            if (window.medicalCalculator) {
                window.medicalCalculator.scheduleCalculation();
            }

            console.log('Commission rate updated:', {
                type: window.currentCommissionType,
                rate: window.currentCommissionRate,
                fixedAmount: window.currentFixedCommissionAmount
            });
        }
    }

    function cancelCommissionEdit() {
        const rateInput = document.getElementById('commissionRateInput');
        const rateDisplay = document.getElementById('commissionRateDisplay');

        if (rateInput && rateDisplay) {
            // Switch back to display mode without saving
            rateInput.style.display = 'none';
            rateDisplay.style.display = 'inline-block';
        }
    }
</script>

<style>
    @media (max-width: 768px) {
        .container-fluid {
            overflow-x: hidden;
        }

        #invoiceForm {
            width: 100%;
            box-shadow: -4px 0 6px -1px rgba(0, 0, 0, 0.1);
        }
    }

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

    .consultation-line .service-name {
        color: #1e40af;
    }

    .booking-line .service-name {
        color: #047857;
    }

    .test-line .service-name {
        color: #374151;
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

    /* Hide loading indicator by default */
    .hidden {
        display: none !important;
    }
</style>
@endsection