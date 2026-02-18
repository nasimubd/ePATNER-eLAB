<script>
    let currentViewingMedicineId = null;

    function viewMedicine(medicineId) {
        currentViewingMedicineId = medicineId;

        $.get(`{{ url('super-admin/common-medicines') }}/${medicineId}`)
            .done(function(response) {
                if (response.success) {
                    const medicine = response.data;
                    $('#viewMedicineId').text(medicine.medicine_id);
                    $('#viewBrandName').text(medicine.brand_name);
                    $('#viewGenericName').text(medicine.generic_name);
                    $('#viewCompanyName').text(medicine.company_name);
                    $('#viewDosageForm').text(medicine.dosage_form);
                    $('#viewDosageStrength').text(medicine.dosage_strength);
                    $('#viewPackInfo').text(medicine.pack_info);
                    $('#viewStatus').html(medicine.is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>');
                    $('#viewCreatedAt').text(new Date(medicine.created_at).toLocaleString());
                    $('#viewUpdatedAt').text(new Date(medicine.updated_at).toLocaleString());

                    $('#viewMedicineModal').modal('show');
                } else {
                    showError('Failed to load medicine details');
                }
            })
            .fail(function() {
                showError('Failed to load medicine details');
            });
    }

    function editMedicineFromView() {
        $('#viewMedicineModal').modal('hide');
        setTimeout(() => {
            editMedicine(currentViewingMedicineId);
        }, 300);
    }
</script>