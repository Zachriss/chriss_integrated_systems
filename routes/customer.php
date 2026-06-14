<?php

use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Customer\CustomerServiceController;
use App\Http\Controllers\Customer\CustomerProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::redirect('/', '/customer/dashboard');

    // Dashboard
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    // Services
    Route::get('/services', [CustomerServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{service}', [CustomerServiceController::class, 'show'])->name('services.show');

    // Service Requests
    Route::post('/service-requests', [CustomerServiceController::class, 'storeRequest'])->name('service-requests.store');
    Route::get('/my-requests', [CustomerServiceController::class, 'myRequests'])->name('my-requests');

    // Products
    Route::get('/my-products', [CustomerProductController::class, 'index'])->name('my-products');
    Route::get('/browse-products', [CustomerProductController::class, 'browse'])->name('products.browse');
    Route::post('/products/order', [CustomerProductController::class, 'order'])->name('products.order');
});