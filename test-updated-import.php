<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\Store;
use App\Models\Grower;
use App\Imports\ProductsImport;

echo "=== TESTING UPDATED IMPORT LOGIC ===\n\n";

echo "Cleaning existing data...\n";
Product::query()->delete();
Store::where('client_code', '!=', null)->delete();
Grower::query()->delete();

echo "Running import with updated store creation logic...\n";
$import = new ProductsImport();

try {
    $import->import(__DIR__ . '/test-orders.csv');

    echo "Import completed successfully!\n";
    echo "Imported: " . $import->getImportedCount() . " products\n";
    echo "Skipped: " . $import->getSkippedCount() . " rows\n";
    echo "New growers: " . $import->getNewGrowersCount() . "\n";
    echo "New stores: " . $import->getNewStoresCount() . "\n\n";

    echo "Stores created with new logic:\n";
    Store::where('client_code', '!=', null)->each(function($store) {
        echo "- Name: '{$store->name}' | Code: '{$store->client_code}' | Email: '{$store->email}' | Active: " . ($store->is_active ? 'Yes' : 'No') . " | Account Active: " . ($store->is_account_active ? 'Yes' : 'No') . "\n";
    });

} catch (Exception $e) {
    echo "Error during import: " . $e->getMessage() . "\n";
}
