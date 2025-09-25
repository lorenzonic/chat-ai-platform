<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test the core import logic that the web interface uses
echo "TESTING CORE IMPORT LOGIC\n";
echo "========================\n";

// 1. Load CSV data
$csvFile = 'test-real-csv.csv';
if (!file_exists($csvFile)) {
    echo "ERROR: CSV file not found\n";
    exit(1);
}

$csvData = [];
$handle = fopen($csvFile, 'r');
$headers = fgetcsv($handle, 0, ';');
echo "Headers: " . json_encode($headers) . "\n";

while (($row = fgetcsv($handle, 0, ';')) !== false) {
    $csvData[] = $row;
}
fclose($handle);

echo "CSV loaded: " . count($csvData) . " rows\n";

// 2. Use the same mapping that would come from the web form
$mapping = [
    'fornitore' => 0,
    'quantita' => 1, 
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

// 3. Test processAdvancedOrderImport method directly (this is what processes the data)
try {
    $controller = new App\Http\Controllers\Admin\ImportController();
    
    // Use reflection to call the private method
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('processAdvancedOrderImport');
    $method->setAccessible(true);
    
    echo "\nCalling processAdvancedOrderImport...\n";
    $result = $method->invoke($controller, $csvData, $mapping);
    
    echo "SUCCESS! Import result:\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    echo "ERROR in processAdvancedOrderImport: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>