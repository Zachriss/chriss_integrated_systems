<?php

use App\Http\Controllers\Staff\StaffDashboardController;
use App\Http\Controllers\Staff\StaffIncomeController;
use App\Http\Controllers\Staff\StaffInventoryController;
use App\Http\Controllers\Staff\StaffServiceController;
use App\Http\Controllers\Staff\StaffTaskAssignmentController;
use App\Http\Controllers\Staff\StaffTaskController;
use App\Http\Controllers\Staff\CashTransactionController;
use App\Http\Controllers\Staff\StaffCashClosingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::redirect('/', '/staff/dashboard');

    // Dashboard
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');

    // Tasks
    Route::get('/tasks', [StaffTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [StaffTaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{task}/update-status', [StaffTaskController::class, 'updateStatus'])->name('tasks.update-status');

    // Task Assignments (Assigned Categories)
    Route::prefix('task-assignments')->name('task-assignments.')->group(function () {
        Route::get('/', [StaffTaskAssignmentController::class, 'dashboard'])->name('dashboard');
        Route::get('/list', [StaffTaskAssignmentController::class, 'index'])->name('index');
        Route::get('/category/{categoryId}', [StaffTaskAssignmentController::class, 'showCategory'])->name('show-category');
        Route::get('/assigned-categories', [StaffTaskAssignmentController::class, 'getAssignedCategories'])->name('assigned-categories');
    });

    // Income
    Route::get('/income/create', [StaffIncomeController::class, 'create'])->name('income.create');
    Route::post('/income', [StaffIncomeController::class, 'store'])->name('income.store');
    Route::get('/income/history', [StaffIncomeController::class, 'history'])->name('income.history');
    Route::put('/income/{id}', [StaffIncomeController::class, 'update'])->name('income.update');
    Route::get('/income/{id}/edit', [StaffIncomeController::class, 'edit'])->name('income.edit');

    // Services
    Route::get('/services', [StaffServiceController::class, 'index'])->name('services');
    Route::get('/services/{service}/requests', [StaffServiceController::class, 'serviceRequests'])->name('services.requests');
    Route::get('/service-requests', [StaffServiceController::class, 'requests'])->name('service-requests');
    Route::get('/service-requests/{request}', [StaffServiceController::class, 'requestShow'])->name('service-requests.show');
    Route::post('/service-requests/{request}/mark-seen', [StaffServiceController::class, 'markSeen'])->name('service-requests.mark-seen');
    Route::post('/service-requests/{request}/respond', [StaffServiceController::class, 'respond'])->name('service-requests.respond');
    Route::post('/service-requests/{request}/update-status', [StaffServiceController::class, 'updateRequestStatus'])->name('service-requests.update-status');
    Route::get('/services-by-category/{categoryId}', [StaffServiceController::class, 'servicesByCategory'])->name('services.by-category');

    // Inventory
    Route::get('/inventory', [StaffInventoryController::class, 'index'])->name('inventory');
    Route::post('/inventory/{product}/stock-out', [StaffInventoryController::class, 'stockOut'])->name('inventory.stock-out');

    // Cash Point - Read Only Dashboard + Closing
    Route::prefix('cashpoint')->name('cashpoint.')->group(function () {
        // READ-ONLY: View today's balances
        Route::get('/', [CashTransactionController::class, 'dashboard'])->name('dashboard');
        // READ-ONLY: View transaction history from database
        Route::get('/history', [CashTransactionController::class, 'history'])->name('history');

        // Dedicated Closing Routes (END OF DAY ONLY - no transactions)
        Route::get('/closing', [StaffCashClosingController::class, 'create'])->name('closing.create');
        Route::post('/closing', [StaffCashClosingController::class, 'store'])->name('closing.store');
    });
});
