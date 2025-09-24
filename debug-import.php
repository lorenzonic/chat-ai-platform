<?php

require_once 'vendor/autoload.php';

// Test diretto del sistema di import
echo "🔍 Debug Import System\n\n";

// Test 1: Verifica se le classi sono caricate
echo "📋 Test 1: Class Loading\n";
try {
    $importController = new App\Http\Controllers\Admin\ImportController();
    echo "✅ ImportController loaded successfully\n";
} catch (Exception $e) {
    echo "❌ ImportController error: " . $e->getMessage() . "\n";
}

// Test 2: Verifica Models
echo "\n📊 Test 2: Models Check\n";
try {
    echo "- Order model: " . (class_exists('App\Models\Order') ? '✅' : '❌') . "\n";
    echo "- Store model: " . (class_exists('App\Models\Store') ? '✅' : '❌') . "\n";
    echo "- Excel facade: " . (class_exists('Maatwebsite\Excel\Facades\Excel') ? '✅' : '❌') . "\n";
} catch (Exception $e) {
    echo "❌ Models error: " . $e->getMessage() . "\n";
}

// Test 3: Verifica directory
echo "\n📁 Test 3: Directory Structure\n";
$dirs = [
    'storage/app' => storage_path('app'),
    'storage/app/temp' => storage_path('app/temp'),
    'storage/app/temp/imports' => storage_path('app/temp/imports'),
];

foreach ($dirs as $name => $path) {
    echo "- $name: " . (is_dir($path) ? '✅ exists' : '❌ missing') . "\n";
}

// Test 4: Verifica permissions
echo "\n🔒 Test 4: Permissions\n";
$tempDir = storage_path('app/temp/imports');
if (is_dir($tempDir)) {
    echo "- Directory writable: " . (is_writable($tempDir) ? '✅' : '❌') . "\n";
} else {
    echo "- Creating temp directory...\n";
    mkdir($tempDir, 0755, true);
    echo "- Directory created: " . (is_dir($tempDir) ? '✅' : '❌') . "\n";
}

// Test 5: Verifica funzione CSV
echo "\n📄 Test 5: CSV Reading Function\n";
$csvFile = 'test-orders-import.csv';
if (file_exists($csvFile)) {
    try {
        $data = [];
        if (($handle = fopen($csvFile, "r")) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $data[] = $row;
            }
            fclose($handle);
        }
        echo "✅ CSV read successfully: " . count($data) . " rows\n";
    } catch (Exception $e) {
        echo "❌ CSV error: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ CSV file not found: $csvFile\n";
}

echo "\n🎯 All tests completed!\n";
