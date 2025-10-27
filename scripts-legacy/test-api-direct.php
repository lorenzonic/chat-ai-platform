<?php

require_once __DIR__ . '/vendor/autoload.php';

// Test diretto API Chatbot
$url = 'http://127.0.0.1:8000/api/chatbot/botanicaverde/message';
$data = [
    'message' => 'La mia monstera ha le foglie gialle',
    'session_id' => 'test_session_' . time()
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

echo "🧪 Testing Chatbot API...\n";
echo "URL: $url\n";
echo "Data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response:\n";

if ($response) {
    $decoded = json_decode($response, true);
    if ($decoded) {
        echo json_encode($decoded, JSON_PRETTY_PRINT) . "\n\n";

        if (isset($decoded['nlp'])) {
            echo "✅ NLP Data received!\n";
            echo "Intent: " . $decoded['nlp']['intent'] . "\n";
            echo "Keywords: " . implode(', ', $decoded['nlp']['keywords']) . "\n";
            echo "Source: " . $decoded['nlp']['source'] . "\n";
            echo "Suggestions count: " . count($decoded['nlp']['suggestions']) . "\n";
        } else {
            echo "❌ No NLP data in response!\n";
        }
    } else {
        echo "❌ Invalid JSON response:\n";
        echo $response . "\n";
    }
} else {
    echo "❌ No response received\n";
}
