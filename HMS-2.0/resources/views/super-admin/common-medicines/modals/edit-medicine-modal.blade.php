<div class="modal fade" id="editMedicineModal" tabindex="-1" role="dialog" aria-labelledby="editMedicineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMedicineModalLabel">
                    <i class="fas fa-edit me-2"></i>Edit Medicine
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editMedicineForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editMedicineId" name="medicine_id">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="editMedicineIdDisplay">Medicine ID</label>
                                <input type="text" class="form-control" id="editMedicineIdDisplay" readonly>
                                <small class="form-text text-muted">Medicine ID cannot be changed</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editCompanyName">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editCompanyName" name="company_name" required maxlength="100">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editDosageForm">Dosage Form <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editDosageForm" name="dosage_form" required maxlength="50" placeholder="e.g., Tablet, Capsule, Syrup">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editBrandName">Brand Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editBrandName" name="brand_name" required maxlength="150">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editGenericName">Generic Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editGenericName" name="generic_name" required maxlength="200">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editDosageStrength">Dosage/Strength <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editDosageStrength" name="dosage_strength" required maxlength="100" placeholder="e.g., 500mg, 250mg/5ml">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editPackInfo">Pack Info <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editPackInfo" name="pack_info" required maxlength="100" placeholder="e.g., 10 tablets, 100ml bottle">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="editIsActive" name="is_active">
                                    <label class="custom-control-label" for="editIsActive">Active Status</label>
                                </div>
                                <small class="form-text text-muted">Toggle to activate/deactivate medicine</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateMedicine()" id="updateMedicineBtn">
                    <i class="fas fa-save me-2"></i>Update Medicine
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function updateMedicine() {
        const form = $('#editMedicineForm');
        const medicineId = $('#editMedicineId').val();
        const formData = new FormData(form[0]);

        // Clear previous validation errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        // Disable update button
        $('#updateMedicineBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Updating...');

        $.ajax({
            url: `{{ url('super-admin/common-medicines') }}/${medicineId}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#editMedicineModal').modal('hide');
                    showSuccess(response.message);
                    searchMedicines();
                    loadStats();
                } else {
                    showError('Failed to update medicine');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(field => {
                        const input = $(`#edit${field.charAt(0).toUpperCase() + field.slice(1).replace('_', '')}`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[field][0]);
                    });
                } else {
                    showError('Failed to update medicine');
                }
            },
            complete: function() {
                $('#updateMedicineBtn').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Update Medicine');
            }
        });
    }

    // Populate edit form when modal is shown
    function editMedicine(medicineId) {
        $.get(`{{ url('super-admin/common-medicines') }}/${medicineId}`)
            .done(function(response) {
                if (response.success) {
                    const medicine = response.data;
                    $('#editMedicineId').val(medicine.id || medicine.medicine_id);
                    $('#editMedicineIdDisplay').val(medicine.medicine_id);
                    $('#editCompanyName').val(medicine.company_name);
                    $('#editDosageForm').val(medicine.dosage_form);
                    $('#editBrandName').val(medicine.brand_name);
                    $('#editGenericName').val(medicine.generic_name);
                    $('#editDosageStrength').val(medicine.dosage_strength);
                    $('#editPackInfo').val(medicine.pack_info);
                    $('#editIsActive').prop('checked', medicine.is_active);

                    $('#editMedicineModal').modal('show');
                } else {
                    showError('Failed to load medicine details');
                }
            })
            .fail(function() {
                showError('Failed to load medicine details');
            });
    }

    $('#editMedicineModal').on('hidden.bs.modal', function() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    });
</script>