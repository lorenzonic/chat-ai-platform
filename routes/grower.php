<?php

use App\Http\Controllers\Grower\DashboardController;
use App\Http\Controllers\Grower\OrderController;
use App\Http\Controllers\Grower\ProductController;
use App\Http\Controllers\Grower\AuthController;
use App\Models\Grower;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Grower Routes
|--------------------------------------------------------------------------
|
| Routes for grower portal functionality
|
*/

// Grower Authentication Routes (outside middleware)
Route::prefix('grower')->name('grower.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Route temporanea per testare login grower
Route::get('/grower/test-login', function () {
    $grower = Grower::first();
    if ($grower) {
        Auth::guard('grower')->login($grower);
        return redirect()->route('grower.dashboard')->with('success', 'Logged in as test grower');
    }
    return 'No growers found';
})->name('grower.test.login');

Route::middleware(['web', 'growerAuth'])->prefix('grower')->name('grower.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
    Route::resource('products', ProductController::class);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Product Labels Routes (legacy products)
    Route::prefix('products-stickers')->name('products.stickers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Grower\ProductLabelController::class, 'index'])->name('index');
        Route::get('/bulk-print', [\App\Http\Controllers\Grower\ProductLabelController::class, 'bulkPrint'])->name('bulk-print');
        Route::get('/{product}', [\App\Http\Controllers\Grower\ProductLabelController::class, 'show'])->name('show');
    });


});
