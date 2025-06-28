<?php

// Simple test route to debug the trends data
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TrendsController;

Route::get('/debug-trends', function () {
    $controller = new TrendsController();

    // Use reflection to access private method
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('getGoogleTrends');
    $method->setAccessible(true);

    $data = $method->invoke($controller, 30);

    return response()->json([
        'success' => true,
        'data' => $data,
        'has_keywords' => isset($data['keywords']),
        'keywords_count' => isset($data['keywords']) ? count($data['keywords']) : 0,
        'first_keyword' => isset($data['keywords'][0]) ? $data['keywords'][0] : null,
    ]);
});
