<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\NotificationController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\PublicHomeController;
use App\Http\Controllers\PublicServiceController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicHomeController::class, 'index'])->name('home');
Route::post('/service-requests', [PublicHomeController::class, 'storeServiceRequest'])->name('public.service-requests.store');
Route::post('/contact', [PublicHomeController::class, 'storeContactMessage'])->name('public.contact.store');

// Marketplace (eBay-style)
Route::prefix('marketplace')->name('marketplace.')->group(function () {
    Route::get('/', [\App\Http\Controllers\MarketplaceController::class, 'index'])->name('index');
    Route::get('/products', [\App\Http\Controllers\MarketplaceController::class, 'products'])->name('products');
    Route::get('/services', [\App\Http\Controllers\MarketplaceController::class, 'services'])->name('services');
    Route::get('/api/search', [\App\Http\Controllers\MarketplaceController::class, 'search'])->name('search');
});

// Public Service Pages
Route::get('/services', [PublicServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [PublicServiceController::class, 'show'])->name('services.show');

// Shop routes
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/api/shop/search', [ShopController::class, 'search'])->name('shop.search');
Route::get('/api/shop/featured', [ShopController::class, 'featured'])->name('shop.featured');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/super-admin.php';
require __DIR__.'/staff.php';
require __DIR__.'/customer.php';
Route::get('/home', [PublicHomeController::class, 'index']);
Route::get('/about', fn () => redirect(url('/') . '#about-section'))->name('about');
Route::get('/developer', fn () => redirect(url('/') . '#contact'))->name('developer');

Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/dropdown-data', [NotificationController::class, 'dropdownData'])->name('notifications.dropdown-data');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/{notificationId}', [NotificationController::class, 'show'])->name('notifications.show');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::get('/e-learning', fn () => redirect()->route('login'))->name('e-learning');
