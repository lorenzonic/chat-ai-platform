<?php

// Simple test route to debug the trends data using refactored services
use Illuminate\Support\Facades\Route;
use App\Services\Trends\GoogleTrendsService;

Route::get('/debug-trends', function () {
    $googleTrendsService = app(GoogleTrendsService::class);
    
    $data = $googleTrendsService->getTrends(30);

    return response()->json([
        'success' => true,
        'data' => $data,
        'has_keywords' => isset($data['keywords']),
        'keywords_count' => isset($data['keywords']) ? count($data['keywords']) : 0,
        'first_keyword' => isset($data['keywords'][0]) ? $data['keywords'][0] : null,
    ]);
});
