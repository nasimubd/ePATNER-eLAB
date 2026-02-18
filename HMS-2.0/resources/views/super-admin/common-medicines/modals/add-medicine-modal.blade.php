<div class="modal fade" id="addMedicineModal" tabindex="-1" role="dialog" aria-labelledby="addMedicineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMedicineModalLabel">
                    <i class="fas fa-plus me-2"></i>Add New Medicine
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addMedicineForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="addCompanyName">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="addCompanyName" name="company_name" required maxlength="100">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="addDosageForm">Dosage Form <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="addDosageForm" name="dosage_form" required maxlength="50" placeholder="e.g., Tablet, Capsule, Syrup">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="addBrandName">Brand Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="addBrandName" name="brand_name" required maxlength="150">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="addGenericName">Generic Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="addGenericName" name="generic_name" required maxlength="200">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="addDosageStrength">Dosage/Strength <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="addDosageStrength" name="dosage_strength" required maxlength="100" placeholder="e.g., 500mg, 250mg/5ml">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="addPackInfo">Pack Info <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="addPackInfo" name="pack_info" required maxlength="100" placeholder="e.g., 10 tablets, 100ml bottle">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="addIsActive" name="is_active" checked>
                                    <label class="custom-control-label" for="addIsActive">Active Status</label>
                                </div>
                                <small class="form-text text-muted">Uncheck to create medicine as inactive</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveMedicine()" id="saveMedicineBtn">
                    <i class="fas fa-save me-2"></i>Save Medicine
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function saveMedicine() {
        const form = $('#addMedicineForm');
        const formData = new FormData(form[0]);

        // Clear previous validation errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        // Disable save button
        $('#saveMedicineBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');

        $.ajax({
            url: '{{ route("super-admin.common-medicines.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#addMedicineModal').modal('hide');
                    showSuccess(response.message);
                    searchMedicines();
                    loadStats();
                    resetAddMedicineForm();
                } else {
                    showError('Failed to save medicine');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(field => {
                        const input = $(`#add${field.charAt(0).toUpperCase() + field.slice(1).replace('_', '')}`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[field][0]);
                    });
                } else {
                    showError('Failed to save medicine');
                }
            },
            complete: function() {
                $('#saveMedicineBtn').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Save Medicine');
            }
        });
    }

    function resetAddMedicineForm() {
        $('#addMedicineForm')[0].reset();
        $('#addIsActive').prop('checked', true);
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }

    $('#addMedicineModal').on('hidden.bs.modal', function() {
        resetAddMedicineForm();
    });
</script>