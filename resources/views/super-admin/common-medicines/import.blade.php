@extends('super-admin.layouts.app')

@section('title', 'Import Common Medicines')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Import Common Medicines</h1>
                <p class="text-gray-600 mt-1">Upload CSV or Excel file to import medicines data</p>
            </div>
            <a href="{{ route('super-admin.common-medicines.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to Medicines
            </a>
        </div>
    </div>

    <!-- Import Form Card -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-blue-600">
                <i class="fas fa-upload mr-2"></i>Upload File
            </h3>
        </div>

        <div class="p-6">
            <form id="importForm" enctype="multipart/form-data">
                @csrf

                <!-- File Upload Section -->
                <div class="mb-8">
                    <label for="importFile" class="block text-sm font-medium text-gray-700 mb-3">
                        Select CSV/Excel File
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors duration-200">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="importFile" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload a file</span>
                                    <input id="importFile" name="file" type="file" class="sr-only" accept=".csv,.xlsx,.xls" required>
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">CSV, XLSX, XLS up to 10MB</p>
                        </div>
                    </div>

                    <!-- File Info Display -->
                    <div id="fileInfo" class="mt-3 hidden">
                        <div class="flex items-center p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <i class="fas fa-file-alt text-blue-600 mr-3"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-blue-900" id="fileName"></p>
                                <p class="text-xs text-blue-700" id="fileSize"></p>
                            </div>
                            <button type="button" id="removeFile" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Options Section -->
                <div class="mb-8">
                    <h4 class="text-sm font-medium text-gray-700 mb-4">Import Options</h4>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" id="hasMedicineId" name="has_medicine_id">
                            <span class="ml-3 text-sm text-gray-700">File includes Medicine ID column</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" id="skipDuplicates" name="skip_duplicates" checked>
                            <span class="ml-3 text-sm text-gray-700">Skip duplicate entries</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" id="validateData" name="validate_data" checked>
                            <span class="ml-3 text-sm text-gray-700">Validate data before import</span>
                        </label>
                    </div>
                </div>

                <!-- Expected Format Section -->
                <div class="mb-8">
                    <h4 class="text-sm font-medium text-gray-700 mb-4">Expected File Format</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-3">Your CSV/Excel file should contain the following columns:</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                            <div class="bg-white px-2 py-1 rounded border">Medicine ID</div>
                            <div class="bg-white px-2 py-1 rounded border">Company Name</div>
                            <div class="bg-white px-2 py-1 rounded border">Dosage Form</div>
                            <div class="bg-white px-2 py-1 rounded border">Brand Name</div>
                            <div class="bg-white px-2 py-1 rounded border">Generic Name</div>
                            <div class="bg-white px-2 py-1 rounded border">Dosage Strength</div>
                            <div class="bg-white px-2 py-1 rounded border">Pack Info</div>
                            <div class="bg-white px-2 py-1 rounded border">Status (Optional)</div>
                        </div>
                        <p class="text-xs text-gray-500 mt-3">
                            <i class="fas fa-info-circle mr-1"></i>
                            Medicine ID column is optional if you check the option above
                        </p>
                    </div>
                </div>

                <!-- Progress Section -->
                <div id="importProgress" class="mb-6 hidden">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600 mr-3"></div>
                            <span class="text-sm font-medium text-blue-900">Importing medicines...</span>
                        </div>
                        <div class="w-full bg-blue-200 rounded-full h-2">
                            <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p class="text-xs text-blue-700 mt-2" id="progressText">Preparing import...</p>
                    </div>
                </div>

                <!-- Results Section -->
                <div id="importResults" class="mb-6 hidden">
                    <div class="border rounded-lg p-4" id="resultsContainer">
                        <h4 class="font-medium mb-3" id="resultsTitle">Import Results</h4>
                        <div id="resultsList"></div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row sm:justify-end gap-3">
                    <a href="{{ route('super-admin.common-medicines.index') }}" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium rounded-lg transition-colors duration-200 text-center">
                        Cancel
                    </a>
                    <button type="submit" id="importBtn" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-upload mr-2"></i>Start Import
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sample File Download -->
    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-download text-yellow-600 mt-1 mr-3"></i>
            <div>
                <h4 class="text-sm font-medium text-yellow-800">Need a sample file?</h4>
                <p class="text-sm text-yellow-700 mt-1">Download a sample CSV file to see the expected format.</p>
                <button type="button" id="downloadSample" class="mt-2 text-sm text-yellow-800 hover:text-yellow-900 font-medium underline">
                    Download Sample CSV
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        'use strict';

        const fileInput = document.getElementById('importFile');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const removeFileBtn = document.getElementById('removeFile');
        const importForm = document.getElementById('importForm');
        const importBtn = document.getElementById('importBtn');
        const progressSection = document.getElementById('importProgress');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const resultsSection = document.getElementById('importResults');

        // File input change handler
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                displayFileInfo(file);
            }
        });

        // Remove file handler
        removeFileBtn.addEventListener('click', function() {
            fileInput.value = '';
            fileInfo.classList.add('hidden');
        });

        // Display file information
        function displayFileInfo(file) {
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileInfo.classList.remove('hidden');
        }

        // Format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Form submission
        importForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!fileInput.files.length) {
                showError('Please select a file to import');
                return;
            }

            startImport();
        });

        // Start import process
        function startImport() {
            const formData = new FormData(importForm);

            // Show progress
            progressSection.classList.remove('hidden');
            resultsSection.classList.add('hidden');
            importBtn.disabled = true;
            importBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Importing...';

            // Simulate progress updates
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress > 90) progress = 90;
                updateProgress(progress, 'Processing data...');
            }, 500);

            // Make the actual request
            fetch('{{ route("super-admin.common-medicines.import") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    clearInterval(progressInterval);
                    updateProgress(100, 'Import completed!');

                    setTimeout(() => {
                        progressSection.classList.add('hidden');
                        showResults(data);
                        resetForm();
                    }, 1000);
                })
                .catch(error => {
                    clearInterval(progressInterval);
                    progressSection.classList.add('hidden');
                    showError('Import failed: ' + error.message);
                    resetForm();
                });
        }

        // Update progress
        function updateProgress(percent, text) {
            progressBar.style.width = percent + '%';
            progressText.textContent = text;
        }

        // Show results
        function showResults(data) {
            if (data.success) {
                const results = data.data;
                const resultsContainer = document.getElementById('resultsContainer');
                const resultsTitle = document.getElementById('resultsTitle');
                const resultsList = document.getElementById('resultsList');

                resultsContainer.className = 'border border-green-200 bg-green-50 rounded-lg p-4';
                resultsTitle.className = 'font-medium text-green-900 mb-3';
                resultsTitle.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Import Completed Successfully';

                let html = '<div class="space-y-2 text-sm text-green-800">';
                if (results.imported) html += `<div class="flex items-center"><i class="fas fa-plus-circle text-green-600 mr-2"></i>Imported: ${results.imported} medicines</div>`;
                if (results.updated) html += `<div class="flex items-center"><i class="fas fa-edit text-blue-600 mr-2"></i>Updated: ${results.updated} medicines</div>`;
                if (results.skipped) html += `<div class="flex items-center"><i class="fas fa-skip-forward text-yellow-600 mr-2"></i>Skipped: ${results.skipped} medicines</div>`;
                if (results.errors) html += `<div class="flex items-center"><i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>Errors: ${results.errors} medicines</div>`;
                html += '</div>';

                resultsList.innerHTML = html;
                resultsSection.classList.remove('hidden');

                // Show success message
                showSuccess('Import completed successfully!');

                // Redirect after delay
                setTimeout(() => {
                    window.location.href = '{{ route("super-admin.common-medicines.index") }}';
                }, 3000);
            } else {
                showError(data.message || 'Import failed');
            }
        }

        // Reset form
        function resetForm() {
            importBtn.disabled = false;
            importBtn.innerHTML = '<i class="fas fa-upload mr-2"></i>Start Import';
        }

        // Download sample file
        document.getElementById('downloadSample').addEventListener('click', function() {
            const csvContent = `Medicine ID,Company Name,Dosage Form,Brand Name,Generic Name,Dosage Strength,Pack Info,Status
MED001,ABC Pharma,Tablet,Paracetamol 500,Paracetamol,500mg,10 tablets,Active
MED002,XYZ Pharma,Capsule,Amoxicillin 250,Amoxicillin,250mg,20 capsules,Active
MED003,DEF Pharma,Syrup,Cough Syrup,Dextromethorphan,100ml,1 bottle,Active`;

            downloadCSV(csvContent, 'sample-medicines.csv');
        });

        // Download CSV file
        function downloadCSV(csvContent, filename) {
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Utility functions for notifications
        function showSuccess(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full';
            toast.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>${message}</span>
                        </div>
                    `;

            document.body.appendChild(toast);
            setTimeout(() => toast.classList.remove('translate-x-full'), 100);
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function showError(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full';
            toast.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span>${message}</span>
                        </div>
                    `;

            document.body.appendChild(toast);
            setTimeout(() => toast.classList.remove('translate-x-full'), 100);
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Drag and drop functionality
        const dropZone = document.querySelector('.border-dashed');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('border-blue-400', 'bg-blue-50');
        }

        function unhighlight(e) {
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                fileInput.files = files;
                displayFileInfo(files[0]);
            }
        }
    });
</script>
@endpush