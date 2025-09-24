<?php

// Test per il parsing dei numeri
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST NUMERIC PARSING ===\n\n";

// Creiamo un'istanza della classe import per testare il metodo
$import = new App\Imports\ProductsImport();

// Utilizziamo la riflessione per accedere al metodo privato
$reflectionClass = new ReflectionClass($import);
$method = $reflectionClass->getMethod('parseNumericValue');
$method->setAccessible(true);

// Test values
$testValues = [
    '12.50',     // Standard decimal
    '12,50',     // Comma decimal
    '12',        // Integer
    '12.5',      // Short decimal
    '0',         // Zero
    '',          // Empty string
    null,        // Null
    'abc',       // Text
    '12.50€',    // With currency
    ' 12.50 ',   // With spaces
    '1,234.50',  // Thousands separator
    '-12.50',    // Negative
];

foreach ($testValues as $testValue) {
    $result = $method->invoke($import, $testValue, 'float', null);
    $displayValue = $testValue === null ? 'NULL' : "'$testValue'";
    echo "Input: $displayValue -> Output: " . ($result === null ? 'NULL' : $result) . "\n";
}

echo "\n✅ Test completed!\n";
