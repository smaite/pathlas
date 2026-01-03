<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LabRegistrationController;
use App\Http\Controllers\SuperAdminController;

// Auth Routes
Route::get('/', function() {
    return redirect()->route('login');
});

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Lab Registration (Public)
Route::get('register-lab', [LabRegistrationController::class, 'showRegistrationForm'])->name('register-lab');
Route::post('register-lab', [LabRegistrationController::class, 'register'])->name('register-lab.store');

// Subscription Status Pages
Route::middleware('auth')->group(function() {
    Route::get('subscription/pending', function() {
        return view('subscription.pending');
    })->name('subscription.pending');
    
    Route::get('subscription/expired', function() {
        return view('subscription.expired');
    })->name('subscription.expired');
});

// Public report verification
Route::get('verify', [ReportController::class, 'verify'])->name('reports.verify');
Route::get('verify/{report}', [ReportController::class, 'verify']);
Route::get('report-pdf/{report}', [ReportController::class, 'publicDownload'])->name('reports.public-download');
Route::get('receipt-pdf/{booking_id}', [BookingController::class, 'publicReceipt'])->name('bookings.public-receipt');

// Super Admin Routes (No subscription check)
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->group(function() {
    Route::get('/', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('/labs', [SuperAdminController::class, 'labs'])->name('superadmin.labs');
    Route::get('/labs/{lab}', [SuperAdminController::class, 'showLab'])->name('superadmin.lab-details');
    Route::post('/labs/{lab}/verify', [SuperAdminController::class, 'verifyLab'])->name('superadmin.verify-lab');
    Route::post('/labs/{lab}/reject', [SuperAdminController::class, 'rejectLab'])->name('superadmin.reject-lab');
    Route::post('/labs/{lab}/extend', [SuperAdminController::class, 'extendSubscription'])->name('superadmin.extend-subscription');
    Route::post('/labs/{lab}/revoke', [SuperAdminController::class, 'revokeSubscription'])->name('superadmin.revoke-subscription');
    Route::post('/labs/{lab}/toggle-status', [SuperAdminController::class, 'toggleLabStatus'])->name('superadmin.toggle-lab-status');
});

// Protected Routes (with subscription check)
Route::middleware(['auth', 'role', 'subscription'])->group(function() {
    
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Patients
    Route::get('patients/search', [PatientController::class, 'search'])->name('patients.search');
    Route::resource('patients', PatientController::class);

    // Tests
    Route::get('tests/search', [TestController::class, 'search'])->name('tests.search');
    Route::get('tests/categories', [TestController::class, 'categories'])->name('tests.categories');
    Route::post('tests/categories', [TestController::class, 'storeCategory'])->name('tests.categories.store');
    Route::put('tests/categories/{category}', [TestController::class, 'updateCategory'])->name('tests.categories.update');
    Route::resource('tests', TestController::class);
    
    // Test Packages
    Route::resource('packages', \App\Http\Controllers\TestPackageController::class);
    Route::post('packages/{package}/toggle-status', [\App\Http\Controllers\TestPackageController::class, 'toggleStatus'])->name('packages.toggle-status');
    
    // Test Parameters
    Route::post('tests/{test}/parameters', [\App\Http\Controllers\TestParameterController::class, 'store'])->name('tests.parameters.store');
    Route::put('tests/{test}/parameters/{parameter}', [\App\Http\Controllers\TestParameterController::class, 'update'])->name('tests.parameters.update');
    Route::delete('tests/{test}/parameters/{parameter}', [\App\Http\Controllers\TestParameterController::class, 'destroy'])->name('tests.parameters.destroy');
    Route::post('tests/{test}/parameters/reorder', [\App\Http\Controllers\TestParameterController::class, 'reorder'])->name('tests.parameters.reorder');

    // Bookings
    Route::get('bookings/{booking}/invoice', [BookingController::class, 'invoice'])->name('bookings.invoice');
    Route::get('bookings/{booking}/invoice/pdf', [BookingController::class, 'invoicePdf'])->name('bookings.invoice.pdf');
    Route::get('bookings/{booking}/receipt', [BookingController::class, 'receipt'])->name('bookings.receipt');
    Route::post('bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.status');
    Route::post('bookings/{booking}/payment', [BookingController::class, 'addPayment'])->name('bookings.payment');
    Route::resource('bookings', BookingController::class);

    // Results (Lab Technician)
    Route::middleware(['role:technician,admin,receptionist'])->group(function() {
        Route::get('results/pending', [ResultController::class, 'pending'])->name('results.pending');
        Route::get('results/{result}/entry', [ResultController::class, 'entry'])->name('results.entry');
        Route::post('results/{result}', [ResultController::class, 'store'])->name('results.store');
        Route::post('results/bulk', [ResultController::class, 'bulkEntry'])->name('results.bulk');
        Route::post('results/check-flag', [ResultController::class, 'checkFlag'])->name('results.check-flag');
        // Parameter-based result entry
        Route::get('results/parameters/{bookingTest}', [ResultController::class, 'parameters'])->name('results.parameters');
        Route::post('results/parameters/{bookingTest}', [ResultController::class, 'storeParameters'])->name('results.store-parameters');
        // Edit completed results
        Route::get('results/{bookingTest}/edit', [ResultController::class, 'edit'])->name('results.edit');
        Route::post('results/{bookingTest}/edit', [ResultController::class, 'updateEdit'])->name('results.update-edit');
    });

    // Approvals (Pathologist) - Only if lab requires approval
    Route::middleware(['role:pathologist,admin'])->group(function() {
        Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::get('approvals/{booking}', [ApprovalController::class, 'show'])->name('approvals.show');
        Route::post('approvals/{result}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('approvals/{result}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
        Route::post('approvals/bulk', [ApprovalController::class, 'bulkApprove'])->name('approvals.bulk');
        Route::post('approvals/{booking}/approve-all', [ApprovalController::class, 'approveBooking'])->name('approvals.approve-booking');
    });

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/{booking}/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('reports/{report}/show', [ReportController::class, 'show'])->name('reports.show');
    Route::get('reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');
    Route::get('reports/{booking}/preview', [ReportController::class, 'preview'])->name('reports.preview');
    Route::post('reports/{report}/regenerate', [ReportController::class, 'regenerate'])->name('reports.regenerate');

    // Lab Admin Routes
    Route::middleware(['role:admin'])->group(function() {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('activity-logs', [UserController::class, 'activityLogs'])->name('activity-logs');

        // Lab Settings (for their own lab)
        Route::get('settings', [\App\Http\Controllers\LabController::class, 'settings'])->name('lab.settings');
        Route::put('settings', [\App\Http\Controllers\LabController::class, 'updateSettings'])->name('lab.settings.update');

        // Lab Test Customization (per-lab pricing/settings)
        Route::get('lab-tests', [\App\Http\Controllers\LabTestOverrideController::class, 'index'])->name('lab-tests.index');
        Route::get('lab-tests/{test}/edit', [\App\Http\Controllers\LabTestOverrideController::class, 'edit'])->name('lab-tests.edit');
        Route::put('lab-tests/{test}', [\App\Http\Controllers\LabTestOverrideController::class, 'update'])->name('lab-tests.update');
        Route::delete('lab-tests/{test}/reset', [\App\Http\Controllers\LabTestOverrideController::class, 'reset'])->name('lab-tests.reset');
        Route::post('lab-tests/bulk-prices', [\App\Http\Controllers\LabTestOverrideController::class, 'bulkUpdatePrices'])->name('lab-tests.bulk-prices');
    });

    // Report Customization (all lab users can access)
    Route::get('report-customization', [\App\Http\Controllers\LabController::class, 'reportCustomization'])->name('lab.report-customization');
    Route::put('report-customization', [\App\Http\Controllers\LabController::class, 'updateReportCustomization'])->name('lab.report-customization.update');
    Route::get('report-preview', [\App\Http\Controllers\LabController::class, 'previewReport'])->name('lab.report-preview');

    // Profile - Login Activity (all users)
    Route::get('profile/login-activity', [\App\Http\Controllers\LoginActivityController::class, 'index'])->name('profile.login-activity');
    Route::delete('profile/login-activity', [\App\Http\Controllers\LoginActivityController::class, 'clearAll'])->name('profile.login-activity.clear');
});
