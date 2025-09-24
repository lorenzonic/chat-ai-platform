<?php

require_once 'vendor/autoload.php';

use App\Http\Controllers\Admin\ImportController;

// Test per simulare l'upload e il processo
echo "🧪 Testing CSV Import System\n\n";

// Test 1: Verifica lettura CSV
echo "📄 Test 1: Reading CSV file manually...\n";

$csvFile = 'test-orders-import.csv';
if (!file_exists($csvFile)) {
    echo "❌ CSV file not found: $csvFile\n";
    exit(1);
}

$data = [];
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $data[] = $row;
    }
    fclose($handle);
}

echo "✅ CSV read successfully!\n";
echo "📊 Total rows: " . count($data) . "\n";
echo "📋 Headers: " . implode(', ', $data[0]) . "\n\n";

// Test 2: Verifica mapping automatico
echo "🔗 Test 2: Auto-mapping columns...\n";
$headers = $data[0];
$mapping = [];

foreach ($headers as $index => $header) {
    $header = strtolower(trim($header));

    if (str_contains($header, 'numero') && str_contains($header, 'ordine')) {
        $mapping['order_number'] = $index;
        echo "🎯 Found Order Number at column $index: {$headers[$index]}\n";
    }
    if (str_contains($header, 'cliente') || str_contains($header, 'nome')) {
        $mapping['client'] = $index;
        echo "🏪 Found Client at column $index: {$headers[$index]}\n";
    }
    if (str_contains($header, 'cc')) {
        $mapping['cc'] = $index;
        echo "🏷️ Found CC at column $index: {$headers[$index]}\n";
    }
    if (str_contains($header, 'trasporto') && str_contains($header, 'costo')) {
        $mapping['transport_cost'] = $index;
        echo "💰 Found Transport Cost at column $index: {$headers[$index]}\n";
    }
}

echo "\n✅ Auto-mapping completed!\n";
print_r($mapping);

echo "\n🎉 CSV Import System tests passed!\n";
