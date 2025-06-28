<?php

// Simple debug script to test the TrendsController data structure
require_once 'vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Create controller instance
$controller = new \App\Http\Controllers\Admin\TrendsController();

// Use reflection to access private methods
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('getFallbackGoogleTrends');
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
