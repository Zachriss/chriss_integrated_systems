<?php

use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use App\Http\Controllers\SuperAdmin\RolePermissionController;
use App\Http\Controllers\SuperAdmin\SystemSettingController;
use App\Http\Controllers\SuperAdmin\BackupController;
use App\Http\Controllers\SuperAdmin\AuditLogController;
use App\Http\Controllers\SuperAdmin\ProductCategoryController;
use App\Http\Controllers\SuperAdmin\ProductInventoryController;
use App\Http\Controllers\SuperAdmin\ReportController;
use App\Http\Controllers\SuperAdmin\ServiceCategoryController as SuperAdminServiceCategoryController;
use App\Http\Controllers\SuperAdmin\ServiceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::redirect('/', '/super-admin/dashboard');

    // Dashboard
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Roles & Permissions
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RolePermissionController::class, 'roles'])->name('index');
        Route::get('/create', [RolePermissionController::class, 'createRole'])->name('create');
        Route::post('/', [RolePermissionController::class, 'storeRole'])->name('store');
        Route::get('/{role}/edit', [RolePermissionController::class, 'editRole'])->name('edit');
        Route::put('/{role}', [RolePermissionController::class, 'updateRole'])->name('update');
        Route::delete('/{role}', [RolePermissionController::class, 'destroyRole'])->name('destroy');
    });
    Route::get('/permissions', [RolePermissionController::class, 'permissions'])->name('permissions.index');
    Route::post('/assign-role', [RolePermissionController::class, 'assignUserRole'])->name('assign-role');

    // Operations
    Route::prefix('operations')->name('operations.')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'operationsDashboard'])->name('dashboard');
        Route::get('/tracking', [SuperAdminController::class, 'activityTracking'])->name('tracking');
    });

    // Cash Point Setup (Legacy)
    Route::prefix('cash-points')->name('cash-points.')->group(function () {
        Route::get('/accounts', [SuperAdminController::class, 'cashPointAccounts'])->name('accounts');
        Route::get('/opening-balance', [SuperAdminController::class, 'openingBalanceSetup'])->name('opening-balance');
        Route::get('/closing-reports', [SuperAdminController::class, 'closingBalanceReports'])->name('closing-reports');
        Route::get('/categories', [SuperAdminController::class, 'transactionCategories'])->name('categories');
    });

    // New Cash Point Management Module
    Route::prefix('cashpoint')->name('cashpoint.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\SuperAdminCashpointController::class, 'index'])->name('dashboard');
        Route::get('/sessions', [\App\Http\Controllers\SuperAdmin\SuperAdminCashpointController::class, 'allSessions'])->name('sessions');
        Route::get('/sessions-data', [\App\Http\Controllers\SuperAdmin\SuperAdminCashpointController::class, 'getSessionsData'])->name('sessions.data');
        Route::get('/sessions/{session}', [\App\Http\Controllers\SuperAdmin\SuperAdminCashpointController::class, 'show'])->name('sessions.show');
        Route::post('/sessions/{session}/reopen', [\App\Http\Controllers\SuperAdmin\SuperAdminCashpointController::class, 'reopenSession'])->name('sessions.reopen');
        Route::post('/sessions/{session}/correct', [\App\Http\Controllers\SuperAdmin\SuperAdminCashpointController::class, 'correctSession'])->name('sessions.correct');
        Route::post('/staff/{staff}/reset', [\App\Http\Controllers\SuperAdmin\SuperAdminCashpointController::class, 'resetBalances'])->name('staff.reset');
        Route::get('/providers', [\App\Http\Controllers\SuperAdmin\SuperAdminCashpointController::class, 'providers'])->name('providers');
        Route::post('/providers', [\App\Http\Controllers\SuperAdmin\SuperAdminCashpointController::class, 'storeProvider'])->name('providers.store');
        Route::put('/providers/{provider}', [\App\Http\Controllers\SuperAdmin\SuperAdminCashpointController::class, 'updateProvider'])->name('providers.update');
        Route::delete('/providers/{provider}', [\App\Http\Controllers\SuperAdmin\SuperAdminCashpointController::class, 'destroyProvider'])->name('providers.destroy');
        Route::get('/audit-logs', [\App\Http\Controllers\SuperAdmin\SuperAdminCashpointController::class, 'auditLogs'])->name('audit-logs');
    });

    // Services Management
    Route::prefix('services')->name('services.')->group(function () {
        // Service Categories (MUST be before service wildcard routes)
        Route::get('/categories', [SuperAdminServiceCategoryController::class, 'index'])->name('categories');
        Route::post('/categories', [SuperAdminServiceCategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{serviceCategory}', [SuperAdminServiceCategoryController::class, 'show'])->name('categories.show');
        Route::put('/categories/{serviceCategory}', [SuperAdminServiceCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{serviceCategory}', [SuperAdminServiceCategoryController::class, 'destroy'])->name('categories.destroy');
        Route::post('/categories/{serviceCategory}/toggle-status', [SuperAdminServiceCategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

        Route::get('/', [ServiceController::class, 'index'])->name('index');
        Route::post('/', [ServiceController::class, 'store'])->name('store');
        Route::get('/{service}', [ServiceController::class, 'show'])->name('show');
        Route::put('/{service}', [ServiceController::class, 'update'])->name('update');
        Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('destroy');
        Route::post('/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Inventory
    Route::prefix('inventory')->name('inventory.')->group(function () {
        // Product Management (AJAX)
        Route::get('/products', [ProductInventoryController::class, 'index'])->name('products');
        Route::get('/products/data', [ProductInventoryController::class, 'dataTable'])->name('products.data');
        Route::get('/products/stats', [ProductInventoryController::class, 'stats'])->name('products.stats');
        Route::post('/products', [ProductInventoryController::class, 'store'])->name('products.store');
        Route::get('/products/{product}', [ProductInventoryController::class, 'show'])->name('products.show');
        Route::put('/products/{product}', [ProductInventoryController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductInventoryController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/{product}/toggle-status', [ProductInventoryController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::post('/products/{product}/restock', [ProductInventoryController::class, 'restock'])->name('products.restock');

        // Category Management (AJAX)
        Route::get('/categories', [ProductCategoryController::class, 'index'])->name('categories');
        Route::get('/categories/data', [ProductCategoryController::class, 'dataTable'])->name('categories.data');
        Route::post('/categories', [ProductCategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}', [ProductCategoryController::class, 'show'])->name('categories.show');
        Route::put('/categories/{category}', [ProductCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [ProductCategoryController::class, 'destroy'])->name('categories.destroy');
        Route::post('/categories/{category}/toggle-status', [ProductCategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

        // Stock Management
        Route::get('/stock-in', [SuperAdminController::class, 'stockIn'])->name('stock-in');
        Route::get('/stock-out', [SuperAdminController::class, 'stockOut'])->name('stock-out');
        Route::get('/reports', [SuperAdminController::class, 'stockReports'])->name('reports');
    });

    // System Administration
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/overview', [SuperAdminController::class, 'systemOverview'])->name('overview');
        Route::get('/configuration', [SuperAdminController::class, 'systemConfiguration'])->name('configuration');
    });

    // System Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SystemSettingController::class, 'index'])->name('index');
        Route::get('/app-name', [SystemSettingController::class, 'appName'])->name('app-name');
        Route::get('/logo', [SystemSettingController::class, 'logo'])->name('logo');
        Route::get('/theme', [SystemSettingController::class, 'theme'])->name('theme');
        Route::get('/preferences', [SystemSettingController::class, 'preferences'])->name('preferences');
        Route::post('/update', [SystemSettingController::class, 'update'])->name('update');
        Route::get('/update', fn () => redirect()->route('super-admin.settings.index'))->name('update.get');
        // Section-specific updates
        Route::post('/update-branding', [SystemSettingController::class, 'updateBranding'])->name('update-branding');
        Route::post('/update-contact', [SystemSettingController::class, 'updateContact'])->name('update-contact');
        Route::post('/update-social', [SystemSettingController::class, 'updateSocial'])->name('update-social');
        Route::post('/update-hero', [SystemSettingController::class, 'updateHero'])->name('update-hero');
        Route::post('/update-about', [SystemSettingController::class, 'updateAbout'])->name('update-about');
        Route::post('/update-footer', [SystemSettingController::class, 'updateFooter'])->name('update-footer');
    });

    // Audit Logs
    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('/{auditTrail}', [AuditLogController::class, 'show'])->name('show');
        Route::get('/user-activities', [AuditLogController::class, 'userActivities'])->name('user-activities');
        Route::get('/system-actions', [AuditLogController::class, 'systemActions'])->name('system-actions');
        Route::get('/login-history', [AuditLogController::class, 'loginHistory'])->name('login-history');
    });

    // Backup & Restore
    Route::prefix('backups')->name('backups.')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::post('/create', [BackupController::class, 'create'])->name('create');
        Route::get('/{backup}/download', [BackupController::class, 'download'])->name('download');
        Route::post('/{backup}/restore', [BackupController::class, 'restore'])->name('restore');
        Route::delete('/{backup}', [BackupController::class, 'destroy'])->name('destroy');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/system', [SuperAdminController::class, 'systemNotifications'])->name('system');
        Route::get('/alerts', [SuperAdminController::class, 'userAlerts'])->name('alerts');
    });

    // System Maintenance
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/clear-cache', [SuperAdminController::class, 'clearCache'])->name('clear-cache');
        Route::get('/optimize', [SuperAdminController::class, 'optimizeSystem'])->name('optimize');
        Route::get('/mode', [SuperAdminController::class, 'maintenanceMode'])->name('mode');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/users', [ReportController::class, 'users'])->name('users');
        Route::get('/roles', [ReportController::class, 'roles'])->name('roles');
        Route::get('/audit-logs', [ReportController::class, 'auditLogs'])->name('audit-logs');
        Route::get('/system-usage', [ReportController::class, 'systemUsage'])->name('system-usage');
        Route::get('/system', [ReportController::class, 'system'])->name('system');
        Route::get('/cash-points', [ReportController::class, 'cashPoints'])->name('cash-points');
        Route::get('/{report}/export/{format}', [ReportController::class, 'export'])->whereIn('report', ['users', 'roles', 'audit-logs', 'system-usage', 'financial', 'system', 'cash-points'])->whereIn('format', ['pdf', 'excel'])->name('export');
    });
});
