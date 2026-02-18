// Medical Invoice Patient Services Handler
window.medicalInvoicePatientServices = {
    // Check patient services (appointments and bookings)
    checkPatientServices: function (patientId) {
        console.log("Checking patient services for ID:", patientId);

        // Set loading state
        window.isPatientLoading = true;
        if (typeof window.updateSubmitButtonState === "function") {
            window.updateSubmitButtonState();
        }

        // Show loading indicator
        const loadingIndicator = document.getElementById(
            "patientLoadingIndicator"
        );
        if (loadingIndicator) {
            loadingIndicator.classList.remove("hidden");
        }

        // Make API call to check patient services
        fetch("/admin/medical/invoices/check-patient-appointments", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
                Accept: "application/json",
            },
            body: JSON.stringify({
                patient_id: patientId,
            }),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                console.log("Patient services response:", data);

                if (data.success) {
                    // Clear existing patient services
                    this.clearPatientServices();

                    // Add consultation lines
                    if (
                        data.consultation_lines &&
                        data.consultation_lines.length > 0
                    ) {
                        console.log(
                            "Adding consultation lines:",
                            data.consultation_lines
                        );
                        data.consultation_lines.forEach((consultation) => {
                            if (
                                typeof window.addConsultationLine === "function"
                            ) {
                                window.addConsultationLine(consultation);
                            }
                        });
                    }

                    // Add booking lines
                    if (data.booking_lines && data.booking_lines.length > 0) {
                        console.log(
                            "Adding booking lines:",
                            data.booking_lines
                        );
                        data.booking_lines.forEach((booking) => {
                            if (typeof window.addBookingLine === "function") {
                                window.addBookingLine(booking);
                            }
                        });
                    }

                    // Show success message
                    if (
                        data.has_pending_services ||
                        data.has_pending_appointments
                    ) {
                        Swal.fire({
                            icon: "info",
                            title: "Pending Services Found",
                            text: data.message,
                            timer: 4000,
                            showConfirmButton: false,
                            toast: true,
                            position: "top-end",
                        });
                    }

                    // Mark patient as selected
                    window.hasPatientSelected = true;
                } else {
                    throw new Error(
                        data.message || "Failed to check patient services"
                    );
                }
            })
            .catch((error) => {
                console.error("Error checking patient services:", error);

                // Still mark patient as selected even if service check fails
                window.hasPatientSelected = true;

                Swal.fire({
                    icon: "warning",
                    title: "Service Check Failed",
                    text: "Could not check patient services, but you can still create an invoice.",
                    timer: 4000,
                    showConfirmButton: false,
                    toast: true,
                    position: "top-end",
                });
            })
            .finally(() => {
                // Clear loading state
                window.isPatientLoading = false;
                if (typeof window.updateSubmitButtonState === "function") {
                    window.updateSubmitButtonState();
                }

                // Hide loading indicator
                const loadingIndicator = document.getElementById(
                    "patientLoadingIndicator"
                );
                if (loadingIndicator) {
                    loadingIndicator.classList.add("hidden");
                }
            });
    },

    // Clear patient services
    clearPatientServices: function () {
        console.log("Clearing patient services");

        const consultationLines =
            document.querySelectorAll(".consultation-line");
        const bookingLines = document.querySelectorAll(".booking-line");

        consultationLines.forEach((line) => line.remove());
        bookingLines.forEach((line) => line.remove());

        if (window.medicalCalculator) {
            window.medicalCalculator.scheduleCalculation();
        }
    },
};

// Make functions globally available
window.checkPatientServices =
    window.medicalInvoicePatientServices.checkPatientServices.bind(
        window.medicalInvoicePatientServices
    );
window.clearPatientServices =
    window.medicalInvoicePatientServices.clearPatientServices.bind(
        window.medicalInvoicePatientServices
    );
