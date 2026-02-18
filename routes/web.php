<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\BusinessController;
use App\Http\Controllers\SuperAdmin\AdminController;
use App\Http\Controllers\SuperAdmin\CommonMedicineController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\AdminsController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\Admin\MedicineController;
use App\Http\Controllers\Admin\LabTestController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\ReportTemplateController;
use App\Http\Controllers\Admin\LabReportController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Admin\MedicalInvoiceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingStatusController;
use App\Http\Controllers\WardServiceController;
use App\Http\Controllers\OtServiceController;
use App\Http\Controllers\OtRoomController;
use App\Http\Controllers\Admin\CareOfController;
use App\Http\Controllers\Admin\BusinessSettingsController;
use App\Http\Controllers\Admin\TopSheetController;
use App\Http\Controllers\Business\PaymentController;
use App\Http\Controllers\SuperAdmin\SubscriptionController;




Route::get('/', function () {
    if (\Illuminate\Support\Facades\Auth::check()) {
        $user = \Illuminate\Support\Facades\Auth::user();
        $role = $user->roles->first()->name ?? null;

        // Direct URL redirects instead of named routes to avoid potential redirect loops
        $redirectUrl = match ($role) {
            'super-admin' => '/super-admin/dashboard',
            'admin' => '/admin/dashboard',
            'Manager' => '/admin/dashboard',
            'LA' => '/admin/dashboard',
            default => '/login'
        };

        return redirect($redirectUrl);
    }
    return redirect('/login');
});


Route::get('/dashboard', function () {
    $user = \Illuminate\Support\Facades\Auth::user();
    $role = $user->roles->first()->name ?? null;

    // Direct URL redirects instead of named routes to avoid potential redirect loops
    $redirectUrl = match ($role) {
        'super-admin' => '/super-admin/dashboard',
        'admin' => '/admin/dashboard',
        'staff' => '/admin/inventory/inventory_transactions',
        default => '/login'
    };

    return redirect($redirectUrl);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Super Admin only routes
Route::middleware(['role:super-admin'])->group(function () {
    // Common Medicine Management Routes
    Route::prefix('super-admin/common-medicines')->name('super-admin.common-medicines.')->group(function () {
        Route::get('/', [App\Http\Controllers\SuperAdmin\CommonMedicineController::class, 'index'])->name('index');
        Route::get('/search', [App\Http\Controllers\SuperAdmin\CommonMedicineController::class, 'search'])->name('search');
        Route::post('/', [App\Http\Controllers\SuperAdmin\CommonMedicineController::class, 'store'])->name('store');

        Route::get('/import', [App\Http\Controllers\SuperAdmin\CommonMedicineController::class, 'showImport'])->name('import');
        // Bulk operations and utilities (MUST come before parameterized routes)
        Route::post('/import', [App\Http\Controllers\SuperAdmin\CommonMedicineController::class, 'import'])->name('import');
        Route::get('/export/data', [App\Http\Controllers\SuperAdmin\CommonMedicineController::class, 'export'])->name('export');
        Route::post('/bulk-delete', [App\Http\Controllers\SuperAdmin\CommonMedicineController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk-activate', [App\Http\Controllers\SuperAdmin\CommonMedicineController::class, 'bulkActivate'])->name('bulk-activate');
        Route::get('/stats/overview', [App\Http\Controllers\SuperAdmin\CommonMedicineController::class, 'getStats'])->name('stats');

        // Parameterized routes (MUST come last)
        Route::get('/{medicine}', [App\Http\Controllers\SuperAdmin\CommonMedicineController::class, 'show'])->name('show');
        Route::put('/{medicine}', [App\Http\Controllers\SuperAdmin\CommonMedicineController::class, 'update'])->name('update');
        Route::delete('/{medicine}', [App\Http\Controllers\SuperAdmin\CommonMedicineController::class, 'destroy'])->name('destroy');
    });



    Route::get('/super-admin/dashboard', [DashboardController::class, 'index'])->name('super-admin.dashboard');
    // Route::get('/super-admin/dashboard', [DashboardController::class, 'index'])->name('superadmin.dashboard');
    // All the Business routes - UPDATE THESE
    Route::get('/businesses', [BusinessController::class, 'index'])->name('super-admin.businesses.index');
    Route::get('/businesses/create', [BusinessController::class, 'create'])->name('super-admin.businesses.create');
    Route::post('/businesses', [BusinessController::class, 'store'])->name('super-admin.businesses.store');
    Route::get('/businesses/{business}', [BusinessController::class, 'show'])->name('super-admin.businesses.show');
    Route::get('/businesses/{business}/edit', [BusinessController::class, 'edit'])->name('super-admin.businesses.edit');
    Route::put('/businesses/{business}', [BusinessController::class, 'update'])->name('super-admin.businesses.update');
    Route::delete('/businesses/{business}', [BusinessController::class, 'destroy'])->name('super-admin.businesses.destroy');
    Route::patch('/businesses/{business}/toggle-status', [BusinessController::class, 'toggleStatus'])->name('super-admin.businesses.toggle-status');
    Route::get('/businesses/search', [BusinessController::class, 'search'])->name('super-admin.businesses.search');

    // Subscription Management Routes
    Route::prefix('subscriptions')->name('super-admin.subscriptions.')->group(function () {
        Route::get('/settings', [SubscriptionController::class, 'settings'])->name('settings');
        Route::put('/settings', [SubscriptionController::class, 'updateSettings'])->name('update-settings');
        Route::get('/pending-payments', [SubscriptionController::class, 'pendingPayments'])->name('pending-payments');
        Route::patch('/payments/{payment}/approve', [SubscriptionController::class, 'approvePayment'])->name('approve-payment');
        Route::patch('/payments/{payment}/reject', [SubscriptionController::class, 'rejectPayment'])->name('reject-payment');
        Route::get('/businesses', [SubscriptionController::class, 'businesses'])->name('businesses');
        Route::match(['patch', 'post'], '/businesses/{businessId}/update-subscription', [SubscriptionController::class, 'updateBusinessSubscription'])->name('update-business-subscription');
        Route::get('/payment-history', [SubscriptionController::class, 'paymentHistory'])->name('payment-history');
    });

    // Global Settings Management Routes
    Route::resource('settings', App\Http\Controllers\SuperAdmin\GlobalSettingsController::class)->names([
        'index' => 'super-admin.settings.index',
        'create' => 'super-admin.settings.create',
        'store' => 'super-admin.settings.store',
        'show' => 'super-admin.settings.show',
        'edit' => 'super-admin.settings.edit',
        'update' => 'super-admin.settings.update',
        'destroy' => 'super-admin.settings.destroy'
    ]);
    Route::patch('settings/{setting}/toggle', [App\Http\Controllers\SuperAdmin\GlobalSettingsController::class, 'toggle'])->name('super-admin.settings.toggle');

    // Letterhead Management Routes
    Route::resource('letterheads', App\Http\Controllers\SuperAdmin\LetterheadController::class)->names([
        'index' => 'super-admin.letterheads.index',
        'create' => 'super-admin.letterheads.create',
        'store' => 'super-admin.letterheads.store',
        'show' => 'super-admin.letterheads.show',
        'edit' => 'super-admin.letterheads.edit',
        'update' => 'super-admin.letterheads.update',
        'destroy' => 'super-admin.letterheads.destroy'
    ]);
    Route::patch('letterheads/{letterhead}/toggle-status', [App\Http\Controllers\SuperAdmin\LetterheadController::class, 'toggleStatus'])->name('super-admin.letterheads.toggle-status');

    // All the Admin routes
    Route::get('/admins', [AdminController::class, 'index'])->name('super-admin.admins.index');
    Route::get('/admins/create', [AdminController::class, 'create'])->name('super-admin.admins.create');
    Route::post('/admins', [AdminController::class, 'store'])->name('super-admin.admins.store');
    Route::get('/admins/{admin}/edit', [AdminController::class, 'edit'])->name('super-admin.admins.edit');
    Route::put('/admins/{admin}', [AdminController::class, 'update'])->name('super-admin.admins.update');
    Route::delete('/admins/{admin}', [AdminController::class, 'destroy'])->name('super-admin.admins.destroy');
});

// Add this inside the admin middleware group
Route::middleware(['auth', 'role:admin', 'subscription'])->group(function () {

    // Care Of Management Routes
    Route::resource('admin/care-ofs', CareOfController::class)->names([
        'index' => 'admin.care-ofs.index',
        'create' => 'admin.care-ofs.create',
        'store' => 'admin.care-ofs.store',
        'show' => 'admin.care-ofs.show',
        'edit' => 'admin.care-ofs.edit',
        'update' => 'admin.care-ofs.update',
        'destroy' => 'admin.care-ofs.destroy'
    ]);




    // Medicine Management Routes 
    Route::resource('medicines', MedicineController::class)->names([
        'index' => 'admin.medicines.index',
        'create' => 'admin.medicines.create',
        'store' => 'admin.medicines.store',
        'show' => 'admin.medicines.show',
        'edit' => 'admin.medicines.edit',
        'update' => 'admin.medicines.update',
        'destroy' => 'admin.medicines.destroy'
    ]);

    Route::resource('super-admin/staff', StaffController::class)->names([
        'index' => 'staff.index',
        'create' => 'staff.create',
        'store' => 'staff.store',
        'show' => 'staff.show',
        'edit' => 'staff.edit',
        'update' => 'staff.update',
        'destroy' => 'staff.destroy'
    ]);

    // Additional super-admin staff routes
    Route::patch('super-admin/staff/{staff}/toggle-status', [StaffController::class, 'toggleStatus'])
        ->name('staff.toggle-status');
    Route::patch('super-admin/staff/{staff}/reset-password', [StaffController::class, 'resetPassword'])
        ->name('staff.reset-password');


    // Additional doctor routes
    Route::patch('admin/doctors/{doctor}/toggle-status', [DoctorController::class, 'toggleStatus'])
        ->name('admin.doctors.toggle-status');

    // Additional medicine routes
    Route::patch('medicines/{medicine}/toggle-status', [MedicineController::class, 'toggleStatus'])
        ->name('admin.medicines.toggle-status');
    Route::patch('medicines/{medicine}/update-stock', [MedicineController::class, 'updateStock'])
        ->name('admin.medicines.update-stock');
    Route::post('medicines/{medicine}/generate-barcode', [MedicineController::class, 'generateBarcode'])
        ->name('admin.medicines.generate-barcode');
    Route::get('medicines/export', [MedicineController::class, 'export'])
        ->name('admin.medicines.export');
    Route::get('medicines/{medicine}/details', [MedicineController::class, 'getMedicine'])
        ->name('admin.medicines.details');
});

// Lab Management Routes - Updated middleware
// Lab Management Routes - Updated middleware

// Payment routes (available when subscription is expired)
Route::middleware(['auth', 'role:admin|Manager|LA'])->group(function () {
    Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('business.payment.form');
    Route::post('/payment', [PaymentController::class, 'submitPayment'])->name('business.payment.submit');
});

// Protected admin routes (require active subscription)
Route::middleware(['auth', 'role:admin|Manager|LA', 'subscription'])->group(function () {

    // Admin Dashboard
    Route::get('/admin/dashboard', [AdminsController::class, 'index'])->name('admin.dashboard');

    // Patient Export Route
    Route::get('/admin/patients/export', [PatientController::class, 'export'])->name('admin.patients.export');

    // Dashboard analytics routes
    Route::get('/admin/dashboard/stats', [AdminsController::class, 'getStats'])->name('admin.dashboard.stats');
    Route::get('/admin/dashboard/charts', [AdminsController::class, 'getCharts'])->name('admin.dashboard.charts');

    // Doctor Management Routes
    Route::resource('admin/doctors', DoctorController::class)->names([
        'index' => 'admin.doctors.index',
        'create' => 'admin.doctors.create',
        'store' => 'admin.doctors.store',
        'show' => 'admin.doctors.show',
        'edit' => 'admin.doctors.edit',
        'update' => 'admin.doctors.update',
        'destroy' => 'admin.doctors.destroy'
    ]);
    // Medical Invoice Management Routes
    Route::resource('admin/medical/invoices', MedicalInvoiceController::class)->names([
        'index' => 'admin.medical.invoices.index',
        'create' => 'admin.medical.invoices.create',
        'store' => 'admin.medical.invoices.store',
        'show' => 'admin.medical.invoices.show',
        'edit' => 'admin.medical.invoices.edit',
        'update' => 'admin.medical.invoices.update',
        'destroy' => 'admin.medical.invoices.destroy'
    ]);

    Route::get('admin/medical/invoices/{invoice}/print', [MedicalInvoiceController::class, 'print'])
        ->name('admin.medical.invoices.print');
    Route::get('admin/medical/invoices/{invoice}/print-a4', [MedicalInvoiceController::class, 'printA4'])
        ->name('admin.medical.invoices.print-a4');
    Route::get('admin/medical/invoices/{invoice}/print-a5', [MedicalInvoiceController::class, 'printA5'])
        ->name('admin.medical.invoices.print-a5');

    Route::post('admin/medical/invoices/{invoice}/collect', [MedicalInvoiceController::class, 'collectPayment'])
        ->name('admin.medical.invoices.collect');

    Route::post('admin/medical/invoices/{invoice}/apply-discount', [MedicalInvoiceController::class, 'applyDiscount'])
        ->name('admin.medical.invoices.apply-discount');
    // Additional Medical Invoice routes

    Route::get('/admin/medical/invoices/{invoice}/pdf', [MedicalInvoiceController::class, 'generatePDF'])->name('admin.medical.invoices.pdf');
    Route::get('/shared/invoice/{token}', [MedicalInvoiceController::class, 'sharedInvoice'])->name('admin.medical.invoices.shared');
    Route::get('/admin/medical/invoices/{invoice}/share-link', [MedicalInvoiceController::class, 'getShareableLink'])->name('admin.medical.invoices.share-link');

    // Print Request Management Routes
    Route::resource('admin/print-requests', App\Http\Controllers\Admin\PrintRequestController::class)->names([
        'index' => 'admin.print-requests.index',
        'create' => 'admin.print-requests.create',
        'store' => 'admin.print-requests.store',
        'show' => 'admin.print-requests.show',
        'edit' => 'admin.print-requests.edit',
        'update' => 'admin.print-requests.update',
        'destroy' => 'admin.print-requests.destroy'
    ]);

    // Additional Print Request routes for AJAX
    Route::post('admin/print-requests/{invoice}/request-print', [App\Http\Controllers\Admin\PrintRequestController::class, 'requestPrint'])
        ->name('admin.print-requests.request-print');


    Route::post('admin/medical/invoices/check-patient-appointments', [MedicalInvoiceController::class, 'checkPatientAppointments'])
        ->name('admin.medical.invoices.check-patient-appointments');
    Route::post('admin/medical/invoices/create-customer', [MedicalInvoiceController::class, 'createCustomer'])
        ->name('admin.medical.invoices.create-customer');
    Route::patch('admin/medical/invoices/{invoice}/update-status', [MedicalInvoiceController::class, 'updateStatus'])
        ->name('admin.medical.invoices.update-status');
    Route::get('admin/medical/invoices/{invoice}/print', [MedicalInvoiceController::class, 'print'])
        ->name('admin.medical.invoices.print');
    Route::get('admin/medical/invoices/{invoice}/download', [MedicalInvoiceController::class, 'download'])
        ->name('admin.medical.invoices.download');
    Route::get('admin/medical/invoices/{invoice}/duplicate', [MedicalInvoiceController::class, 'duplicate'])
        ->name('admin.medical.invoices.duplicate');
    Route::post('admin/medical/invoices/{invoice}/duplicate', [MedicalInvoiceController::class, 'storeDuplicate'])
        ->name('admin.medical.invoices.store-duplicate');
    Route::get('admin/medical/invoices/export', [MedicalInvoiceController::class, 'export'])
        ->name('admin.medical.invoices.export');
    Route::post('admin/medical/invoices/bulk-actions', [MedicalInvoiceController::class, 'bulkActions'])
        ->name('admin.medical.invoices.bulk-actions');

    // API routes for Medical Invoices
    Route::get('admin/medical/invoices/api/patients/search', [MedicalInvoiceController::class, 'searchPatients'])
        ->name('admin.medical.invoices.api.search-patients');
    Route::get('admin/medical/invoices/api/tests/search', [MedicalInvoiceController::class, 'searchTests'])
        ->name('admin.medical.invoices.api.search-tests');

    // Business Settings Routes
    Route::get('admin/business/settings', [BusinessSettingsController::class, 'edit'])
        ->name('admin.business.settings.edit');
    Route::put('admin/business/settings', [BusinessSettingsController::class, 'update'])
        ->name('admin.business.settings.update');

    // Business Payment Routes (without subscription middleware)
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('business/payment', [PaymentController::class, 'showPaymentForm'])
            ->name('business.payment.form')
            ->withoutMiddleware('subscription');
        Route::post('business/payment', [PaymentController::class, 'submitPayment'])
            ->name('business.payment.submit')
            ->withoutMiddleware('subscription');
    });
    Route::get('admin/medical/invoices/api/patients/{patient}/history', [MedicalInvoiceController::class, 'getPatientInvoiceHistory'])
        ->name('admin.medical.invoices.api.patient-history');
    Route::get('admin/medical/invoices/api/tests/{test}/details', [MedicalInvoiceController::class, 'getTestDetails'])
        ->name('admin.medical.invoices.api.test-details');

    // Ledger CRUD Routes
    Route::resource('admin/ledgers', LedgerController::class)->names([
        'index' => 'admin.ledgers.index',
        'create' => 'admin.ledgers.create',
        'store' => 'admin.ledgers.store',
        'show' => 'admin.ledgers.show',
        'edit' => 'admin.ledgers.edit',
        'update' => 'admin.ledgers.update',
        'destroy' => 'admin.ledgers.destroy'
    ]);

    // Additional ledger routes
    Route::post('admin/ledgers/{ledger}/recalculate', [LedgerController::class, 'recalculateBalance'])
        ->name('admin.ledgers.recalculate');

    // Transaction Management Routes
    Route::resource('admin/transactions', TransactionController::class)->names([
        'index' => 'admin.transactions.index',
        'create' => 'admin.transactions.create',
        'store' => 'admin.transactions.store',
        'show' => 'admin.transactions.show',
        'edit' => 'admin.transactions.edit',
        'update' => 'admin.transactions.update',
        'destroy' => 'admin.transactions.destroy'
    ]);

    // Additional transaction routes
    Route::get('admin/transactions/{transaction}/print', [TransactionController::class, 'print'])
        ->name('admin.transactions.print');
    Route::get('admin/transactions/{transaction}/print-a5', [TransactionController::class, 'printA5'])
        ->name('admin.transactions.print-a5');

    // Top Sheet Report Routes
    Route::prefix('admin/reports/top-sheet')->name('admin.reports.top-sheet.')->group(function () {
        Route::get('/', [TopSheetController::class, 'index'])->name('index');
        Route::get('/data', [TopSheetController::class, 'getData'])->name('data');
        Route::get('/print', [TopSheetController::class, 'print'])->name('print');
    });

    // Doctor schedule route for calendar
    Route::get('admin/appointments/api/doctor-schedule', [AppointmentController::class, 'getDoctorSchedule'])
        ->name('admin.appointments.doctor-schedule');


    // Lab Tests
    Route::resource('tests', LabTestController::class)->names([
        'index' => 'admin.lab-tests.index',
        'create' => 'admin.lab-tests.create',
        'store' => 'admin.lab-tests.store',
        'show' => 'admin.lab-tests.show',
        'edit' => 'admin.lab-tests.edit',
        'update' => 'admin.lab-tests.update',
        'destroy' => 'admin.lab-tests.destroy'
    ])->parameters(['tests' => 'labTest']);

    // Lab Test additional routes
    Route::patch('tests/{labTest}/toggle-status', [LabTestController::class, 'toggleStatus'])
        ->name('admin.lab-tests.toggle-status');
    Route::get('tests/{labTest}/check-stock', [LabTestController::class, 'checkStock'])
        ->name('admin.lab-tests.check-stock');
    Route::get('medicines/search', [LabTestController::class, 'getMedicines'])
        ->name('admin.lab-tests.medicines.search');
    Route::get('tests/export', [LabTestController::class, 'export'])
        ->name('admin.lab-tests.export');

    Route::post('/admin/lab-reports/bulk-actions', [LabReportController::class, 'bulkActions'])
        ->name('admin.lab-reports.bulk-actions');
    Route::get('/admin/lab-reports/{report}/download', [LabReportController::class, 'download'])
        ->name('admin.lab-reports.download');

    // Lab Report Templates
    Route::prefix('admin/lab-reports/templates')->name('admin.lab-reports.templates.')->group(function () {
        Route::get('/', [ReportTemplateController::class, 'index'])->name('index');
        Route::get('/create', [ReportTemplateController::class, 'create'])->name('create');
        Route::post('/', [ReportTemplateController::class, 'store'])->name('store');
        Route::get('/{template}', [ReportTemplateController::class, 'show'])->name('show');
        Route::get('/{template}/edit', [ReportTemplateController::class, 'edit'])->name('edit');
        Route::put('/{template}', [ReportTemplateController::class, 'update'])->name('update');
        Route::delete('/{template}', [ReportTemplateController::class, 'destroy'])->name('destroy');
        Route::patch('/{template}/toggle-status', [ReportTemplateController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{template}/duplicate', [ReportTemplateController::class, 'duplicate'])->name('duplicate');
        Route::post('/{template}/duplicate', [ReportTemplateController::class, 'storeDuplicate'])->name('store-duplicate');
    });

    // Lab Reports
    Route::prefix('admin/lab-reports')->name('admin.lab-reports.')->group(function () {
        Route::get('/', [LabReportController::class, 'index'])->name('index');
        Route::get('/create', [LabReportController::class, 'create'])->name('create');
        Route::post('/', [LabReportController::class, 'store'])->name('store');
        Route::get('/{labReport}', [LabReportController::class, 'show'])->name('show');
        Route::get('/{labReport}/edit', [LabReportController::class, 'edit'])->name('edit');
        Route::put('/{labReport}', [LabReportController::class, 'update'])->name('update');
        Route::delete('/{labReport}', [LabReportController::class, 'destroy'])->name('destroy');
        Route::patch('/{labReport}/status', [LabReportController::class, 'updateStatus'])->name('update-status');
        Route::get('/{labReport}/print', [LabReportController::class, 'print'])->name('print');
        Route::get('/{labReport}/duplicate', [LabReportController::class, 'duplicate'])->name('duplicate');
        Route::get('/export/csv', [LabReportController::class, 'export'])->name('export');
        Route::get('/{labReport}/print-with-letterhead', [LabReportController::class, 'printWithLetterhead'])->name('print-with-letterhead');
        // API routes - ADD THESE HERE

        Route::get('/api/patients/search', [LabReportController::class, 'searchPatients'])->name('api.search-patients');
        Route::get('/api/patients/{patient}/history', [LabReportController::class, 'getPatientHistory'])->name('api.patient-history');
        Route::get('/api/lab-id-data', [LabReportController::class, 'getLabIdData'])->name('api.lab-id-data');
        Route::get('/templates/api/test/{testId}/templates', [LabReportController::class, 'getTemplatesByTest'])->name('api.templates.by-test');
        Route::get('/templates/api/{templateId}/structure', [LabReportController::class, 'getTemplateStructure'])->name('api.template.structure');
    });


    // Appointment Management Routes
    Route::resource('admin/appointments', AppointmentController::class)->names([
        'index' => 'admin.appointments.index',
        'create' => 'admin.appointments.create',
        'store' => 'admin.appointments.store',
        'show' => 'admin.appointments.show',
        'edit' => 'admin.appointments.edit',
        'update' => 'admin.appointments.update',
        'destroy' => 'admin.appointments.destroy'
    ]);

    // Additional appointment routes
    Route::patch('admin/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])
        ->name('admin.appointments.confirm');
    Route::patch('admin/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])
        ->name('admin.appointments.complete');
    Route::patch('admin/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])
        ->name('admin.appointments.cancel');
    Route::patch('admin/appointments/{appointment}/no-show', [AppointmentController::class, 'noShow'])
        ->name('admin.appointments.no-show');
    Route::get('admin/appointments/export', [AppointmentController::class, 'export'])
        ->name('admin.appointments.export');
    Route::post('admin/appointments/bulk-actions', [AppointmentController::class, 'bulkActions'])
        ->name('admin.appointments.bulk-actions');
    Route::get('admin/appointments/api/available-slots', [AppointmentController::class, 'getAvailableSlots'])
        ->name('admin.appointments.api.available-slots');


    // Add these additional routes for slot management
    Route::get('admin/appointments/api/available-slots-range', [AppointmentController::class, 'getAvailableSlotsRange'])
        ->name('admin.appointments.available-slots-range');
    Route::post('admin/appointments/api/check-slot', [AppointmentController::class, 'checkSlotAvailability'])
        ->name('admin.appointments.check-slot');
    // Add this route with your other appointment routes
    Route::get('admin/appointments/api/available-slots', [AppointmentController::class, 'getAvailableSlots'])
        ->name('admin.appointments.available-slots');

    // ADD THIS NEW ROUTE
    Route::get('admin/appointments/api/calendar-data', [AppointmentController::class, 'getCalendarData'])
        ->name('admin.appointments.calendar-data');



    // Calendar Management Routes
    Route::get('/calendar', [AppointmentController::class, 'calendar'])->name('calendar.index');
    Route::get('/calendar/appointments', [AppointmentController::class, 'getCalendarData'])->name('calendar.appointments');
    Route::get('/calendar/export', [AppointmentController::class, 'export'])->name('calendar.export');

    // Search endpoints
    Route::get('/admin/search/doctors', [AppointmentController::class, 'searchDoctors'])->name('admin.search.doctors');
    Route::get('/admin/search/patients', [AppointmentController::class, 'searchPatients'])->name('admin.search.patients');
    // Quick appointment creation
    Route::post('/appointments/quick', [AppointmentController::class, 'quickStore'])->name('appointments.quick');
    Route::get('admin/appointments/{appointment}/duplicate', [AppointmentController::class, 'duplicate'])
        ->name('admin.appointments.duplicate');
    Route::get('admin/appointments/{appointment}/details', [AppointmentController::class, 'getAppointmentDetails'])
        ->name('admin.appointments.details');
    Route::get('/admin/calendar/appointments', [CalendarController::class, 'getAppointments'])->name('admin.calendar.appointments');
    Route::prefix('admin/calendar')->name('admin.calendar.')->group(function () {
        Route::get('/', [CalendarController::class, 'index'])->name('index');
        Route::get('/events', [CalendarController::class, 'getEvents'])->name('events');
        Route::get('/data', [CalendarController::class, 'getCalendarData'])->name('data');
        Route::get('/schedule-exceptions', [CalendarController::class, 'getScheduleExceptions'])->name('schedule-exceptions');
        Route::get('/available-slots', [CalendarController::class, 'getAvailableSlots'])->name('available-slots');
        Route::get('/stats', [CalendarController::class, 'getCalendarStats'])->name('stats');
        Route::get('/search', [CalendarController::class, 'searchAppointments'])->name('search');
        Route::get('/doctor-schedule', [CalendarController::class, 'getDoctorSchedule'])->name('doctor-schedule');
        Route::get('/export', [CalendarController::class, 'exportCalendar'])->name('export');
        Route::post('/appointments', [CalendarController::class, 'createAppointment'])->name('create-appointment');
        Route::patch('/appointments/{appointment}', [CalendarController::class, 'updateAppointment'])->name('update-appointment');
    });

    // Ward Services Management
    Route::resource('admin/ward-services', WardServiceController::class)->names([
        'index' => 'admin.ward-services.index',
        'create' => 'admin.ward-services.create',
        'store' => 'admin.ward-services.store',
        'show' => 'admin.ward-services.show',
        'edit' => 'admin.ward-services.edit',
        'update' => 'admin.ward-services.update',
        'destroy' => 'admin.ward-services.destroy'
    ]);

    // Ward Services API routes
    Route::get('admin/ward-services/{wardService}/available-slots', [WardServiceController::class, 'getAvailableSlots'])
        ->name('admin.ward-services.available-slots');

    // OT Services Management
    Route::resource('admin/ot-services', OtServiceController::class)->names([
        'index' => 'admin.ot-services.index',
        'create' => 'admin.ot-services.create',
        'store' => 'admin.ot-services.store',
        'show' => 'admin.ot-services.show',
        'edit' => 'admin.ot-services.edit',
        'update' => 'admin.ot-services.update',
        'destroy' => 'admin.ot-services.destroy'
    ]);

    // OT Services API routes
    Route::get('admin/ot-services/{otService}/calculate-fee', [OtServiceController::class, 'calculateFee'])
        ->name('admin.ot-services.calculate-fee');

    // OT Rooms Management
    Route::resource('admin/ot-rooms', OtRoomController::class)->names([
        'index' => 'admin.ot-rooms.index',
        'create' => 'admin.ot-rooms.create',
        'store' => 'admin.ot-rooms.store',
        'show' => 'admin.ot-rooms.show',
        'edit' => 'admin.ot-rooms.edit',
        'update' => 'admin.ot-rooms.update',
        'destroy' => 'admin.ot-rooms.destroy'
    ]);

    // Common Medicine Management Routes


    // OT Rooms API routes
    Route::get('admin/ot-rooms/{otRoom}/check-availability', [OtRoomController::class, 'checkAvailability'])
        ->name('admin.ot-rooms.check-availability');
    Route::get('admin/ot-rooms/{otRoom}/schedule', [OtRoomController::class, 'getSchedule'])
        ->name('admin.ot-rooms.schedule');

    // Bookings Management

    // Add these routes inside your existing middleware group Route::middleware(['auth', 'role:admin|staff|LA'])->group(function () {

    // API routes for booking form
    Route::get('/api/ward-services', [WardServiceController::class, 'apiIndex'])->name('api.ward-services');
    Route::get('/api/ward-services/{wardService}/details', [WardServiceController::class, 'apiDetails'])->name('api.ward-services.details');
    Route::get('/api/ward-services/{wardService}/slots', [WardServiceController::class, 'apiSlots'])->name('api.ward-services.slots');

    Route::get('/api/ot-services', [OtServiceController::class, 'apiIndex'])->name('api.ot-services');
    Route::get('/api/ot-services/{otService}/details', [OtServiceController::class, 'apiDetails'])->name('api.ot-services.details');
    Route::get('/api/ot-services/{otService}/slots', [OtServiceController::class, 'apiSlots'])->name('api.ot-services.slots');

    Route::get('/api/ot-rooms', [OtRoomController::class, 'apiIndex'])->name('api.ot-rooms');
    Route::get('/api/patients/{patient}/details', [PatientController::class, 'apiDetails'])->name('api.patients.details');

    // Your existing routes continue here...

    Route::resource('admin/bookings', BookingController::class)->names([
        'index' => 'bookings.index',
        'create' => 'bookings.create',
        'store' => 'bookings.store',
        'show' => 'bookings.show',
        'edit' => 'bookings.edit',
        'update' => 'bookings.update',
        'destroy' => 'bookings.destroy'
    ]);

    Route::get('bookings/{booking}/print', [BookingController::class, 'print'])->name('bookings.print');
    // API routes for bookings
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('ward-services', [WardServiceController::class, 'apiIndex'])->name('ward-services');
        Route::get('ward-services/{wardService}/details', [WardServiceController::class, 'apiDetails'])->name('ward-services.details');
        Route::get('ward-services/{wardService}/slots', [WardServiceController::class, 'apiSlots'])->name('ward-services.slots');

        Route::get('ot-services', [OtServiceController::class, 'apiIndex'])->name('ot-services');
        Route::get('ot-services/{otService}/details', [OtServiceController::class, 'apiDetails'])->name('ot-services.details');
        Route::get('ot-services/{otService}/slots', [OtServiceController::class, 'apiSlots'])->name('ot-services.slots');

        Route::get('ot-rooms', [OtRoomController::class, 'apiIndex'])->name('ot-rooms');
    });




    // Booking Status Management Routes (ADD THESE)
    Route::patch('admin/bookings/{booking}/confirm', [BookingController::class, 'confirm'])
        ->name('bookings.confirm');
    Route::patch('admin/bookings/{booking}/cancel', [BookingController::class, 'cancel'])
        ->name('bookings.cancel');
    Route::patch('admin/bookings/{booking}/complete', [BookingController::class, 'complete'])
        ->name('bookings.complete');
    Route::patch('admin/bookings/{booking}/no-show', [BookingController::class, 'markNoShow'])
        ->name('bookings.no-show');

    // Booking API routes (ADD THIS)
    Route::get('admin/bookings/api/service-details', [BookingController::class, 'getServiceDetails'])
        ->name('bookings.api.service-details');


    // Booking Status History Routes
    Route::get('admin/bookings/{booking}/status-history', [BookingStatusController::class, 'history'])
        ->name('admin.bookings.status-history');
    Route::patch('admin/bookings/{booking}/update-status', [BookingStatusController::class, 'updateWithReason'])
        ->name('admin.bookings.update-status');
    Route::post('admin/bookings/bulk-update-status', [BookingStatusController::class, 'bulkUpdate'])
        ->name('admin.bookings.bulk-update-status');

    // Booking Status API routes
    Route::get('admin/bookings/{booking}/status-options', [BookingStatusController::class, 'getStatusOptions'])
        ->name('admin.bookings.api.status-options');
    Route::get('admin/bookings/api/recent-activity', [BookingStatusController::class, 'getRecentActivity'])
        ->name('admin.bookings.api.recent-activity');
    Route::get('admin/bookings/{booking}/export-history', [BookingStatusController::class, 'exportHistory'])
        ->name('admin.bookings.export-history');
});


Route::middleware(['auth', 'role:admin|Manager'])->group(
    function () {

        // Patient Management Routes
        Route::resource('admin/patients', PatientController::class)->names([
            'index' => 'admin.patients.index',
            'create' => 'admin.patients.create',
            'store' => 'admin.patients.store',
            'show' => 'admin.patients.show',
            'edit' => 'admin.patients.edit',
            'update' => 'admin.patients.update',
            'destroy' => 'admin.patients.destroy'
        ]);

        // Additional patient routes
        Route::patch('admin/patients/{patient}/toggle-status', [PatientController::class, 'toggleStatus'])
            ->name('admin.patients.toggle-status');
        Route::get('admin/patients/search', [PatientController::class, 'search'])
            ->name('admin.patients.search');
        Route::get('admin/patients/{patient}/details', [PatientController::class, 'getPatient'])
            ->name('admin.patients.details');
        Route::get('admin/patients/export', [PatientController::class, 'export'])
            ->name('admin.patients.export');
        Route::post('admin/patients/bulk-actions', [PatientController::class, 'bulkActions'])
            ->name('admin.patients.bulk-actions');
        Route::get('admin/patients/{patient}/image', [PatientController::class, 'showImage'])
            ->name('admin.patients.image');
        Route::get('admin/patients/{patient}/report', [PatientController::class, 'generateReport'])
            ->name('admin.patients.report');
    }
);

require __DIR__ . '/auth.php';
