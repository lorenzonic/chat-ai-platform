<?php

// Simple debug script to test the refactored Trends services
require_once 'vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Create service instances using dependency injection
$googleTrendsService = $app->make(\App\Services\Trends\GoogleTrendsService::class);
$socialMediaService = $app->make(\App\Services\Trends\SocialMediaTrendsService::class);
$seasonalService = $app->make(\App\Services\Trends\SeasonalAnalysisService::class);
$method->setAccessible(true);

// Test the method
$result = $method->invoke($controller);

echo "Google Trends Fallback Data Structure:\n";
echo json_encode($result, JSON_PRETTY_PRINT);
echo "\n\nKeywords structure check:\n";

if (isset($result['keywords'])) {
    foreach ($result['keywords'] as $index => $keyword) {
        echo "Keyword {$index}: ";
        echo "term=" . ($keyword['term'] ?? 'MISSING') . ", ";
        echo "interest=" . ($keyword['interest'] ?? 'MISSING') . ", ";
        echo "trend=" . ($keyword['trend'] ?? 'MISSING') . "\n";
    }
} else {
    echo "No keywords found in result\n";
}
