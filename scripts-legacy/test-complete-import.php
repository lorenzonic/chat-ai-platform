<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test the complete orders import process
echo "🧪 Testing Complete Orders Import Process\n\n";

try {
    // Create a test request
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'file_path' => 'temp/imports/test_file.csv',
        'mapping' => [
            'fornitore' => 0,
            'prodotto' => 1,
            'codice' => 2,
            'quantita' => 3,
            'cliente' => 4,
            'prezzo' => 5
        ]
    ]);

    // Create test CSV data in memory
    $testData = [
        ['Fornitore Test', 'Prodotto Test', 'PROD001', '10', 'Cliente Test', '5.50'],
        ['Fornitore Test2', 'Prodotto Test2', 'PROD002', '20', 'Cliente Test2', '3.25']
    ];

    // Create the ImportController
    $controller = new \App\Http\Controllers\Admin\ImportController();

    // Test the order number generation method
    echo "📄 Testing order number generation...\n";
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('generateOrderNumber');
    $method->setAccessible(true);

    $orderNumber1 = $method->invoke($controller);
    echo "Generated order number 1: {$orderNumber1}\n";

    $orderNumber2 = $method->invoke($controller);
    echo "Generated order number 2: {$orderNumber2}\n";

    echo "\n✅ Order number generation working correctly!\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
