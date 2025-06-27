<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AnalyticsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (not authenticated)
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);
    });

    // Authenticated admin routes
    Route::middleware('isAdmin')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');

        // QR Code management
        Route::resource('qr-codes', QrCodeController::class);
        Route::get('qr-codes/{qrCode}/download', [QrCodeController::class, 'download'])->name('qr-codes.download');
        Route::post('qr-codes/{qrCode}/regenerate', [QrCodeController::class, 'regenerate'])->name('qr-codes.regenerate');

        // Account management
        Route::prefix('accounts')->name('accounts.')->group(function () {
            Route::get('/', [AccountController::class, 'index'])->name('index');

            // Store account routes
            Route::get('stores/create', [AccountController::class, 'createStore'])->name('stores.create');
            Route::post('stores', [AccountController::class, 'storeStore'])->name('stores.store');
            Route::get('stores/{store}', [AccountController::class, 'showStore'])->name('stores.show');
            Route::get('stores/{store}/edit', [AccountController::class, 'editStore'])->name('stores.edit');
            Route::put('stores/{store}', [AccountController::class, 'updateStore'])->name('stores.update');
            Route::delete('stores/{store}', [AccountController::class, 'destroyStore'])->name('stores.destroy');
            Route::patch('stores/{store}/toggle-status', [AccountController::class, 'toggleStoreStatus'])->name('stores.toggle-status');
            Route::patch('stores/{store}/toggle-premium', [AccountController::class, 'toggleStorePremium'])->name('stores.toggle-premium');

            // Admin account routes
            Route::get('admins/create', [AccountController::class, 'createAdmin'])->name('admins.create');
            Route::post('admins', [AccountController::class, 'storeAdmin'])->name('admins.store');
            Route::get('admins/{admin}', [AccountController::class, 'showAdmin'])->name('admins.show');
        });

        // Analytics routes
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');
        Route::get('analytics/test', [\App\Http\Controllers\Admin\TestAnalyticsController::class, 'test'])->name('analytics.test');
    });
});
