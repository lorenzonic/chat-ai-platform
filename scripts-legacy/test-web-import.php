<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simulate what happens in the web import process
echo "TESTING WEB IMPORT SIMULATION\n";
echo "=============================\n";

// 1. Simulate file upload and session storage (like uploadOrdersFile method)
$csvFile = 'test-real-csv.csv';
if (!file_exists($csvFile)) {
    echo "ERROR: CSV file not found\n";
    exit(1);
}

$csvData = [];
$handle = fopen($csvFile, 'r');
$headers = fgetcsv($handle, 0, ';');
while (($row = fgetcsv($handle, 0, ';')) !== false) {
    $csvData[] = $row;
}
fclose($handle);

echo "CSV loaded: " . count($csvData) . " rows, " . count($headers) . " columns\n";

// 2. Store in session like web process does
session_start();
session(['import_headers' => $headers]);
session(['import_csv_data' => $csvData]);
session(['import_file_path' => 'temp/test-import.csv']);

echo "Session data stored\n";

// 3. Simulate the mapping process
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

echo "Mapping configured\n";

// 4. Test processFromSessionData method directly
try {
    $controller = new App\Http\Controllers\Admin\ImportController();
    
    // Use reflection to call the private method
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('processFromSessionData');
    $method->setAccessible(true);
    
    echo "Calling processFromSessionData...\n";
    $result = $method->invoke($controller, $mapping);
    
    echo "SUCCESS! Import result:\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    echo "ERROR in processFromSessionData: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

// 5. Check final database state
echo "\nFinal database counts:\n";
echo "Orders: " . App\Models\Order::count() . "\n";
echo "OrderItems: " . App\Models\OrderItem::count() . "\n";
echo "Products: " . App\Models\Product::count() . "\n";
?>