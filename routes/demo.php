<?php

use App\Http\Controllers\Admin\TrendsControllerRefactored;
use Illuminate\Support\Facades\Route;

// Dashboard demo route (bypass authentication for demo)
Route::get('/demo-trends-dashboard', function () {
    $controller = app(TrendsControllerRefactored::class);

    // Mock request with 30 days filter
    $request = request();
    $request->merge(['days' => 30]);

    return $controller->index($request);
})->name('demo.trends');
