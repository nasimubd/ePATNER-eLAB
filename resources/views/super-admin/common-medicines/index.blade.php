@extends('super-admin.layouts.app')

@section('title', 'Common Medicine Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Common Medicine Management</h1>
                <p class="text-gray-600 mt-1">Import and manage common medicines database</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('super-admin.common-medicines.import') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-upload mr-2"></i>Import Medicines
                </a>
                <button type="button" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200" id="exportBtn">
                    <i class="fas fa-download mr-2"></i>Export All
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-blue-500 p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-blue-600 uppercase tracking-wide mb-1">Total Medicines</div>
                    <div class="text-2xl font-bold text-gray-900" id="totalMedicines">-</div>
                </div>
                <div class="flex-shrink-0">
                    <i class="fas fa-pills text-2xl text-gray-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border-l-4 border-green-500 p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-green-600 uppercase tracking-wide mb-1">Active Medicines</div>
                    <div class="text-2xl font-bold text-gray-900" id="activeMedicines">-</div>
                </div>
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-2xl text-gray-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border-l-4 border-cyan-500 p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-cyan-600 uppercase tracking-wide mb-1">Total Companies</div>
                    <div class="text-2xl font-bold text-gray-900" id="totalCompanies">-</div>
                </div>
                <div class="flex-shrink-0">
                    <i class="fas fa-building text-2xl text-gray-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border-l-4 border-yellow-500 p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-yellow-600 uppercase tracking-wide mb-1">Recent Additions</div>
                    <div class="text-2xl font-bold text-gray-900" id="recentAdditions">-</div>
                </div>
                <div class="flex-shrink-0">
                    <i class="fas fa-calendar text-2xl text-gray-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-blue-600">Search Medicines</h3>
        </div>
        <div class="p-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" id="searchInput" placeholder="Search by medicine ID, brand name, generic name, or company...">
                        <button class="absolute right-2 top-1/2 transform -translate-y-1/2 px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors duration-200" type="button" id="searchBtn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="md:w-48">
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" id="perPageSelect">
                        <option value="20">20 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Medicines Table -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-blue-600">Imported Medicines</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="medicinesTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medicine ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generic Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dosage Form</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Strength</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pack Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="medicinesTableBody">
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
                                <p class="text-gray-500">Loading medicines...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm text-gray-700 mb-4 sm:mb-0" id="paginationInfo">
                Showing 0 to 0 of 0 entries
            </div>
            <nav class="flex justify-center sm:justify-end">
                <ul class="flex items-center space-x-1" id="paginationLinks">
                    <!-- Pagination will be generated by JavaScript -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Replace the existing Import Modal section with this -->
<!-- Import Modal -->
<div class="modal-overlay fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="importModal" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full mx-auto">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-upload mr-2 text-blue-600"></i>Import Common Medicines
                </h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors duration-200" id="closeModalBtn">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-6">
                <form id="importForm" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-6">
                        <label for="importFile" class="block text-sm font-medium text-gray-700 mb-2">Select CSV/Excel File</label>
                        <div class="relative">
                            <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-lg cursor-pointer focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="importFile" name="file" accept=".csv,.xlsx,.xls" required>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Supported formats: CSV, Excel (.xlsx, .xls). Maximum file size: 10MB
                        </p>
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" id="hasMedicineId" name="has_medicine_id">
                            <span class="ml-2 text-sm text-gray-700">File includes Medicine ID column</span>
                        </label>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-4" id="importProgress" style="display: none;">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%" id="progressBar"></div>
                        </div>
                    </div>

                    <!-- Import Results -->
                    <div id="importResults" style="display: none;">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-medium text-blue-900 mb-2">Import Results:</h4>
                            <ul id="importResultsList" class="text-sm text-blue-800 space-y-1"></ul>
                        </div>
                    </div>
                </form>
            </div>

            <div class="flex flex-col sm:flex-row sm:justify-end gap-3 p-6 border-t border-gray-200">
                <button type="button" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium rounded-lg transition-colors duration-200" id="closeModalBtn2">Close</button>
                <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200" id="importMedicinesBtn">
                    <i class="fas fa-upload mr-2"></i>Import
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

        let currentPage = 1;

        // Check if jQuery is available
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded');
            return;
        }

        console.log('Initializing Common Medicines page...');

        // Initialize page
        loadStats();
        searchMedicines();

        // Event listeners
        $('#importBtn').on('click', function(e) {
            e.preventDefault();
            console.log('Import button clicked');
            openImportModal();
        });

        $('#exportBtn').on('click', function(e) {
            e.preventDefault();
            console.log('Export button clicked');
            exportMedicines();
        });

        $('#searchBtn').on('click', function(e) {
            e.preventDefault();
            searchMedicines();
        });

        $('#closeModalBtn, #closeModalBtn2').on('click', function(e) {
            e.preventDefault();
            closeImportModal();
        });

        $('#importMedicinesBtn').on('click', function(e) {
            e.preventDefault();
            importMedicines();
        });

        $('#perPageSelect').on('change', function() {
            searchMedicines();
        });

        // Search on Enter key
        $('#searchInput').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                searchMedicines();
            }
        });

        // Replace the existing modal functions with these
        function openImportModal() {
            console.log('Opening import modal');
            const modal = document.getElementById('importModal');
            if (modal) {
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            } else {
                console.error('Import modal not found');
            }
        }

        function closeImportModal() {
            console.log('Closing import modal');
            const modal = document.getElementById('importModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto'; // Restore scrolling
            }

            // Reset form
            const form = document.getElementById('importForm');
            if (form) form.reset();

            // Hide progress and results
            const progress = document.getElementById('importProgress');
            const results = document.getElementById('importResults');
            if (progress) progress.style.display = 'none';
            if (results) results.style.display = 'none';

            // Reset button
            const btn = document.getElementById('importMedicinesBtn');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-upload mr-2"></i>Import';
            }
        }
        // Load statistics
        function loadStats() {
            $.get('{{ route("super-admin.common-medicines.stats") }}')
                .done(function(response) {
                    if (response.success) {
                        $('#totalMedicines').text(response.data.total_medicines.toLocaleString());
                        $('#activeMedicines').text(response.data.active_medicines.toLocaleString());
                        $('#totalCompanies').text(response.data.total_companies.toLocaleString());
                        $('#recentAdditions').text(response.data.recent_additions.toLocaleString());
                    }
                })
                .fail(function() {
                    console.error('Failed to load statistics');
                });
        }

        // Search medicines
        function searchMedicines(page = 1) {
            const searchTerm = $('#searchInput').val();
            const perPage = $('#perPageSelect').val();

            const params = {
                search: searchTerm,
                per_page: perPage,
                page: page
            };

            $('#medicinesTableBody').html(`
            <tr>
                <td colspan="8" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
                        <p class="text-gray-500">Searching medicines...</p>
                    </div>
                </td>
            </tr>
        `);

            $.get('{{ route("super-admin.common-medicines.search") }}', params)
                .done(function(response) {
                    if (response.success) {
                        renderMedicinesTable(response.data);
                        renderPagination(response.pagination);
                        currentPage = response.pagination.current_page;
                    } else {
                        showError('Failed to load medicines');
                    }
                })
                .fail(function() {
                    showError('Failed to search medicines');
                });
        }

        // Render medicines table
        function renderMedicinesTable(medicines) {
            if (medicines.length === 0) {
                $('#medicinesTableBody').html(`
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500">No medicines found. Try importing some medicines first.</p>
                        </div>
                    </td>
                </tr>
            `);
                return;
            }

            let html = '';
            medicines.forEach(medicine => {
                const statusBadge = medicine.is_active ?
                    '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>' :
                    '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>';

                html += `
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <code class="text-sm bg-gray-100 px-2 py-1 rounded">${medicine.medicine_id}</code>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${medicine.brand_name}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${medicine.generic_name}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${medicine.company_name}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${medicine.dosage_form}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${medicine.dosage_strength}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${medicine.pack_info}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        ${statusBadge}
                    </td>
                </tr>
            `;
            });

            $('#medicinesTableBody').html(html);
        }

        // Render pagination
        function renderPagination(pagination) {
            const {
                current_page,
                last_page,
                per_page,
                total
            } = pagination;

            // Update pagination info
            const start = ((current_page - 1) * per_page) + 1;
            const end = Math.min(current_page * per_page, total);
            $('#paginationInfo').text(`Showing ${start} to ${end} of ${total} entries`);

            // Generate pagination links
            let paginationHtml = '';

            // Previous button
            if (current_page > 1) {
                paginationHtml += `
                <li>
                    <a href="#" data-page="${current_page - 1}" class="pagination-link relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 hover:text-gray-700 transition-colors duration-200">
                        Previous
                    </a>
                </li>
            `;
            }

            // Page numbers
            const startPage = Math.max(1, current_page - 2);
            const endPage = Math.min(last_page, current_page + 2);

            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === current_page ?
                    'relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 hover:bg-blue-700' :
                    'relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 hover:bg-gray-50 hover:text-gray-700';

                paginationHtml += `
                <li>
                    <a href="#" data-page="${i}" class="pagination-link ${activeClass} transition-colors duration-200">${i}</a>
                </li>
            `;
            }

            // Next button
            if (current_page < last_page) {
                paginationHtml += `
                <li>
                    <a href="#" data-page="${current_page + 1}" class="pagination-link relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 hover:text-gray-700 transition-colors duration-200">
                        Next
                    </a>
                </li>
            `;
            }

            $('#paginationLinks').html(paginationHtml);

            // Attach pagination click events
            $('.pagination-link').on('click', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                searchMedicines(page);
            });
        }

        // Import medicines
        function importMedicines() {
            const form = $('#importForm')[0];
            const formData = new FormData(form);

            if (!$('#importFile')[0].files.length) {
                showError('Please select a file to import');
                return;
            }

            // Show progress
            $('#importProgress').removeClass('hidden');
            $('#importResults').addClass('hidden');
            $('#importMedicinesBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Importing...');

            $.ajax({
                url: '{{ route("super-admin.common-medicines.import") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            const percentComplete = evt.loaded / evt.total * 100;
                            $('#progressBar').css('width', percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    if (response.success) {
                        showSuccess('Import completed successfully!');

                        // Show results
                        const results = response.data;
                        let resultHtml = '';
                        if (results.imported) resultHtml += `<li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i>Imported: ${results.imported} medicines</li>`;
                        if (results.updated) resultHtml += `<li class="flex items-center"><i class="fas fa-edit text-blue-500 mr-2"></i>Updated: ${results.updated} medicines</li>`;
                        if (results.skipped) resultHtml += `<li class="flex items-center"><i class="fas fa-skip-forward text-yellow-500 mr-2"></i>Skipped: ${results.skipped} medicines</li>`;
                        if (results.errors) resultHtml += `<li class="flex items-center"><i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>Errors: ${results.errors} medicines</li>`;

                        $('#importResultsList').html(resultHtml);
                        $('#importResults').removeClass('hidden');

                        // Refresh data
                        loadStats();
                        searchMedicines();

                        // Reset form after delay
                        setTimeout(() => {
                            closeImportModal();
                        }, 3000);
                    } else {
                        showError(response.message || 'Import failed');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Import failed';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showError(errorMessage);
                },
                complete: function() {
                    $('#importProgress').addClass('hidden');
                    $('#importMedicinesBtn').prop('disabled', false).html('<i class="fas fa-upload mr-2"></i>Import');
                }
            });
        }

        // Export medicines
        function exportMedicines() {
            const btn = $('#exportBtn')[0];
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Exporting...';
            btn.disabled = true;

            $.get('{{ route("super-admin.common-medicines.export") }}')
                .done(function(response) {
                    if (response.success) {
                        // Convert data to CSV
                        const csvContent = convertToCSV(response.data);
                        downloadCSV(csvContent, 'common-medicines-export.csv');
                        showSuccess(`Exported ${response.total} medicines successfully!`);
                    } else {
                        showError('Export failed');
                    }
                })
                .fail(function() {
                    showError('Export failed');
                })
                .always(function() {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                });
        }

        // Convert data to CSV
        function convertToCSV(data) {
            if (!data.length) return '';

            const headers = ['Medicine ID', 'Company Name', 'Dosage Form', 'Brand Name', 'Generic Name', 'Dosage Strength', 'Pack Info'];
            const csvRows = [headers.join(',')];

            data.forEach(row => {
                const values = [
                    row.medicine_id,
                    row.company_name,
                    row.dosage_form,
                    row.brand_name,
                    row.generic_name,
                    row.dosage_strength,
                    row.pack_info
                ].map(value => `"${value}"`);
                csvRows.push(values.join(','));
            });

            return csvRows.join('\n');
        }

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
            const toast = $(`
            <div class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>${message}</span>
                </div>
            </div>
        `);

            $('body').append(toast);
            setTimeout(() => toast.removeClass('translate-x-full'), 100);
            setTimeout(() => {
                toast.addClass('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function showError(message) {
            const toast = $(`
            <div class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>${message}</span>
                </div>
            </div>
        `);

            $('body').append(toast);
            setTimeout(() => toast.removeClass('translate-x-full'), 100);
            setTimeout(() => {
                toast.addClass('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Close modal when clicking outside
        $(document).on('click', '#importModal', function(e) {
            if (e.target === this) {
                closeImportModal();
            }
        });

        // Close modal with Escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && !$('#importModal').hasClass('hidden')) {
                closeImportModal();
            }
        });
    });
</script>
@endpush