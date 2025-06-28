<?php

use App\Http\Controllers\Admin\TrendsController;
use Illuminate\Support\Facades\Route;

// Dashboard demo route (bypass authentication for demo)
Route::get('/demo-trends-dashboard', function () {
    $controller = new TrendsController();

    // Mock request with 30 days filter
    $request = request();
    $request->merge(['days' => 30]);

    return $controller->index($request);
})->name('demo.trends');
