<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Carica configurazione Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$apiKey = config('services.gemini.api_key');
$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

echo "Testing Gemini API...\n";
echo "API Key configured: " . ($apiKey ? "Yes" : "No") . "\n\n";

if (!$apiKey) {
    echo "ERROR: No API key configured!\n";
    exit(1);
}

$payload = [
    'contents' => [
        [
            'parts' => [
                ['text' => 'Test: What color is the sky?']
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.7,
        'topK' => 40,
        'topP' => 0.95,
        'maxOutputTokens' => 100,
    ]
];

try {
    echo "Making API request...\n";

    $response = Http::timeout(30)
        ->withHeaders([
            'Content-Type' => 'application/json',
        ])
        ->post($apiUrl . '?key=' . $apiKey, $payload);

    echo "Response status: " . $response->status() . "\n";
    echo "Response body: " . $response->body() . "\n";

    if ($response->successful()) {
        $data = $response->json();
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            echo "\n✅ SUCCESS! Response: " . $data['candidates'][0]['content']['parts'][0]['text'] . "\n";
        } else {
            echo "\n❌ UNEXPECTED RESPONSE FORMAT\n";
        }
    } else {
        echo "\n❌ API REQUEST FAILED!\n";
        echo "Status: " . $response->status() . "\n";
        echo "Body: " . $response->body() . "\n";
    }

} catch (Exception $e) {
    echo "\n❌ EXCEPTION: " . $e->getMessage() . "\n";
}
