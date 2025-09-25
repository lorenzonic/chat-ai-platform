<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test the core import logic with proper CSV reading
echo "TESTING CORE IMPORT WITH PROPER CSV READING\n";
echo "==========================================\n";

$csvFile = 'test-real-csv.csv';
if (!file_exists($csvFile)) {
    echo "ERROR: CSV file not found\n";
    exit(1);
}

// Use the ImportController's proper CSV reading method
$controller = new App\Http\Controllers\Admin\ImportController();
$reflection = new ReflectionClass($controller);
$readMethod = $reflection->getMethod('readCsvFile');
$readMethod->setAccessible(true);

try {
    echo "Reading CSV with auto-delimiter detection...\n";
    $csvData = $readMethod->invoke($controller, $csvFile);
    
    $headers = array_shift($csvData);
    echo "Headers (" . count($headers) . "): " . json_encode($headers) . "\n";
    echo "Data rows: " . count($csvData) . "\n";
    echo "First row: " . json_encode($csvData[0] ?? []) . "\n";
    
    // Use the same mapping but now with properly parsed CSV
    $mapping = [
        'fornitore' => 0,
        'quantita' => 2, // Note: "Piani" is column 1, "Quantità" is column 2 
        'codice' => 3,
        'prodotto' => 4,
        'codice_cliente' => 5,
        'altezza' => 6,
        'cliente' => 8,
        'data' => 13,
        'ean' => 15,
        'prezzo_rivendita' => 16
    ];
    
    echo "Mapping: " . json_encode($mapping) . "\n";

    // Test processAdvancedOrderImport method with properly parsed data
    $importMethod = $reflection->getMethod('processAdvancedOrderImport');
    $importMethod->setAccessible(true);
    
    echo "\nCalling processAdvancedOrderImport with correct CSV data...\n";
    $result = $importMethod->invoke($controller, $csvData, $mapping);
    
    echo "SUCCESS! Import result:\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>