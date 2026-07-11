<?php

use App\Http\Controllers\Admin\AdminActivityLogController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminExpenseController;
use App\Http\Controllers\Admin\AdminFinanceController;
use App\Http\Controllers\Admin\AdminStaffReportController;
use App\Http\Controllers\Admin\AdminStaffTaskController;
use App\Http\Controllers\Admin\CashOpeningController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\AdminServiceRequestController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\ServicesController;
use App\Http\Controllers\Admin\StaffActivitiesController;
use App\Http\Controllers\Admin\StaffCategoryAssignmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Cash Point - New Module (Admin manages openings, views transactions & closings)
    Route::prefix('cashpoint')->name('cashpoint.')->group(function () {
        Route::get('/', [CashOpeningController::class, 'index'])->name('index');
        Route::post('/openings', [CashOpeningController::class, 'store'])->name('openings.store');
        Route::post('/openings/{opening}/lock', [CashOpeningController::class, 'lock'])->name('openings.lock');
        Route::post('/openings/{opening}/unlock', [CashOpeningController::class, 'unlock'])->name('openings.unlock');
        Route::get('/all-sessions', [CashOpeningController::class, 'index'])->name('all-sessions');
    });

    Route::prefix('service-requests')->name('service-requests.')->group(function () {
        Route::get('/', [AdminServiceRequestController::class, 'index'])->name('index');
        Route::get('/{serviceRequest}', [AdminServiceRequestController::class, 'show'])->name('show');
        Route::put('/{serviceRequest}', [AdminServiceRequestController::class, 'update'])->name('update');
        Route::get('/staff/list', [AdminServiceRequestController::class, 'staffList'])->name('staff-list');
    });

    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [ServicesController::class, 'index'])->name('index');
        Route::get('/{service}', [ServicesController::class, 'show'])->name('show');
        Route::put('/requests/{serviceRequest}', [ServicesController::class, 'updateRequest'])->name('update-request');
        Route::get('/reports/income', [ServicesController::class, 'incomeReport'])->name('income-report');
    });

    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('low-stock');
        Route::get('/{product}', [InventoryController::class, 'show'])->name('show');
        Route::post('/{product}/stock-in', [InventoryController::class, 'stockIn'])->name('stock-in');
        Route::post('/{product}/stock-out', [InventoryController::class, 'stockOut'])->name('stock-out');
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('/daily-cash', [ReportsController::class, 'dailyCash'])->name('daily-cash');
        Route::get('/transactions', [ReportsController::class, 'transactionReport'])->name('transactions');
        Route::get('/service-performance', [ReportsController::class, 'servicePerformance'])->name('service-performance');
    });

    Route::prefix('staff-category-assignments')->name('staff-category-assignments.')->group(function () {
        Route::get('/', [StaffCategoryAssignmentController::class, 'index'])->name('index');
        Route::post('/', [StaffCategoryAssignmentController::class, 'store'])->name('store');
        Route::get('/{staffCategoryAssignment}', [StaffCategoryAssignmentController::class, 'show'])->name('show');
        Route::put('/{staffCategoryAssignment}', [StaffCategoryAssignmentController::class, 'update'])->name('update');
        Route::delete('/{staffCategoryAssignment}', [StaffCategoryAssignmentController::class, 'destroy'])->name('destroy');
        Route::post('/{staffCategoryAssignment}/toggle-status', [StaffCategoryAssignmentController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/data/all', [StaffCategoryAssignmentController::class, 'getAssignments'])->name('data');
        Route::get('/report', [StaffCategoryAssignmentController::class, 'report'])->name('report');
    });

    Route::prefix('staff-activities')->name('staff-activities.')->group(function () {
        Route::get('/', [StaffActivitiesController::class, 'index'])->name('index');
        Route::get('/staff-list', [StaffActivitiesController::class, 'staffList'])->name('staff-list');
        Route::get('/staff/{staff}/edit', [StaffActivitiesController::class, 'edit'])->name('edit');
        Route::put('/staff/{staff}', [StaffActivitiesController::class, 'update'])->name('update');
        Route::post('/staff/{staff}/toggle-status', [StaffActivitiesController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('/staff/{staff}', [StaffActivitiesController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('staff-tasks')->name('staff-tasks.')->group(function () {
        Route::get('/', [AdminStaffTaskController::class, 'index'])->name('index');
        Route::get('/create', [AdminStaffTaskController::class, 'create'])->name('create');
        Route::post('/', [AdminStaffTaskController::class, 'store'])->name('store');
        Route::get('/{task}', [AdminStaffTaskController::class, 'show'])->name('show');
        Route::get('/{task}/edit', [AdminStaffTaskController::class, 'edit'])->name('edit');
        Route::put('/{task}', [AdminStaffTaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [AdminStaffTaskController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('staff-reports')->name('staff-reports.')->group(function () {
        Route::get('/', [AdminStaffReportController::class, 'index'])->name('index');
        Route::get('/daily-income', [AdminStaffReportController::class, 'dailyIncome'])->name('daily-income');
        Route::get('/per-staff', [AdminStaffReportController::class, 'perStaff'])->name('per-staff');
        Route::get('/per-service', [AdminStaffReportController::class, 'perService'])->name('per-service');
        Route::get('/per-category', [AdminStaffReportController::class, 'perCategory'])->name('per-category');
        Route::get('/staff/{staff}', [AdminStaffReportController::class, 'staffDetail'])->name('staff-detail');
    });

    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [AdminExpenseController::class, 'index'])->name('index');
        Route::post('/', [AdminExpenseController::class, 'store'])->name('store');
        Route::put('/{expense}', [AdminExpenseController::class, 'update'])->name('update');
        Route::delete('/{expense}', [AdminExpenseController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/profit-loss', [AdminFinanceController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/financial-report', [AdminFinanceController::class, 'financialReport'])->name('financial-report');
    });

    Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
        Route::get('/', [AdminActivityLogController::class, 'index'])->name('index');
    });

    // CMS Management Routes
    Route::prefix('contact-messages')->name('contact-messages.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminContactMessageController::class, 'index'])->name('index');
        Route::get('/{contactMessage}', [\App\Http\Controllers\Admin\AdminContactMessageController::class, 'show'])->name('show');
        Route::post('/{contactMessage}/mark-read', [\App\Http\Controllers\Admin\AdminContactMessageController::class, 'markAsRead'])->name('mark-read');
        Route::post('/{contactMessage}/approve', [\App\Http\Controllers\Admin\AdminContactMessageController::class, 'approve'])->name('approve');
        Route::post('/{contactMessage}/approve-convert', [\App\Http\Controllers\Admin\AdminContactMessageController::class, 'approveAndConvert'])->name('approve-convert');
        Route::delete('/{contactMessage}', [\App\Http\Controllers\Admin\AdminContactMessageController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('testimonials')->name('testimonials.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminTestimonialController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AdminTestimonialController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AdminTestimonialController::class, 'store'])->name('store');
        Route::get('/{testimonial}/edit', [\App\Http\Controllers\Admin\AdminTestimonialController::class, 'edit'])->name('edit');
        Route::put('/{testimonial}', [\App\Http\Controllers\Admin\AdminTestimonialController::class, 'update'])->name('update');
        Route::delete('/{testimonial}', [\App\Http\Controllers\Admin\AdminTestimonialController::class, 'destroy'])->name('destroy');
        Route::post('/{testimonial}/toggle-approval', [\App\Http\Controllers\Admin\AdminTestimonialController::class, 'toggleApproval'])->name('toggle-approval');
        Route::post('/{testimonial}/toggle-status', [\App\Http\Controllers\Admin\AdminTestimonialController::class, 'toggleStatus'])->name('toggle-status');
    });

    Route::prefix('links')->name('links.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminLinkController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AdminLinkController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AdminLinkController::class, 'store'])->name('store');
        Route::get('/{link}/edit', [\App\Http\Controllers\Admin\AdminLinkController::class, 'edit'])->name('edit');
        Route::put('/{link}', [\App\Http\Controllers\Admin\AdminLinkController::class, 'update'])->name('update');
        Route::delete('/{link}', [\App\Http\Controllers\Admin\AdminLinkController::class, 'destroy'])->name('destroy');
        Route::post('/{link}/toggle-status', [\App\Http\Controllers\Admin\AdminLinkController::class, 'toggleStatus'])->name('toggle-status');
    });

    // About Section — redirects to homepage (managed in System Settings)
    Route::prefix('about')->name('about.')->group(function () {
        Route::get('/', fn () => redirect(url('/') . '#about-section'))
            ->middleware('super_admin')
            ->name('index');
        Route::post('/update', [\App\Http\Controllers\Admin\AdminAboutSectionController::class, 'update'])
            ->middleware('super_admin')
            ->name('update');
    });
});
