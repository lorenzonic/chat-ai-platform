<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Imports\ProductsImport;

echo "Testing product import...\n";

$import = new ProductsImport();

try {
    $import->import(__DIR__ . '/test-orders.csv');

    echo "Import completed successfully!\n";
    echo "Imported: " . $import->getImportedCount() . " products\n";
    echo "Skipped: " . $import->getSkippedCount() . " rows\n";
    echo "New growers: " . $import->getNewGrowersCount() . "\n";
    echo "New stores: " . $import->getNewStoresCount() . "\n";

} catch (Exception $e) {
    echo "Error during import: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
