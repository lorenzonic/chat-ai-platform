<?php

use App\Http\Controllers\Admin\TrendsControllerRefactored;
use Illuminate\Support\Facades\Route;

// Test route for Python scraping
Route::get('/test-python-scraping', function () {
    $controller = app(TrendsControllerRefactored::class);
    // Note: Using ecommerce service directly instead of testEcommerceData method
    $ecommerceService = app(\App\Services\Trends\EcommerceDataService::class);
    $ecommerceData = $ecommerceService->getEcommerceData(30, [], 'simulation');

    return response()->json([
        'success' => true,
        'data' => $ecommerceData,
        'message' => 'Python scraping test completed'
    ]);
});

// Quick Navigation Test Routes
Route::prefix('test')->name('test.')->group(function () {

    Route::get('navigation', function () {
        $routes = [
            'Home' => url('/'),
            'Admin Login' => route('admin.login'),
            'Admin Dashboard' => route('admin.dashboard'),
            'Trends Analytics' => route('admin.trends.index'),
            'Configure Sites' => route('admin.trends.configure'),
            'Analytics' => route('admin.analytics.index'),
            'Accounts' => route('admin.accounts.index'),
            'QR Codes' => route('admin.qr-codes.index'),
        ];

        return view('test.navigation', compact('routes'));
    })->name('navigation');

});
