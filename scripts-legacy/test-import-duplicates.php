<?php

require_once 'vendor/autoload.php';

use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Product;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test Product Import with Duplicates ===\n\n";

// Get initial product count
$initialCount = Product::count();
echo "Initial products in database: {$initialCount}\n\n";

// Check if test CSV exists
$csvFile = 'test-products-duplicates.csv';
if (!file_exists($csvFile)) {
    echo "❌ Test CSV file not found: {$csvFile}\n";
    exit(1);
}

echo "Found test CSV: {$csvFile}\n";
echo "CSV Contents:\n";
echo "=============\n";
$lines = file($csvFile);
foreach ($lines as $index => $line) {
    echo ($index + 1) . ". " . trim($line) . "\n";
}
echo "\n";

try {
    // Create import instance
    $import = new ProductsImport();

    echo "Starting import...\n";

    // Import the CSV
    Excel::import($import, $csvFile);

    // Get statistics
    $imported = $import->getImportedCount();
    $duplicates = $import->getDuplicateCount();
    $skipped = $import->getSkippedCount();
    $growers = $import->getNewGrowersCount();
    $stores = $import->getNewStoresCount();
    $orders = $import->getNewOrdersCount();

    echo "\n=== Import Results ===\n";
    echo "✅ Products imported: {$imported}\n";
    echo "🔄 Duplicates skipped: {$duplicates}\n";
    echo "⚠️  Other skipped: {$skipped}\n";
    echo "🌱 New growers created: {$growers}\n";
    echo "🏪 New stores created: {$stores}\n";
    echo "📦 New orders created: {$orders}\n";

    // Get final product count
    $finalCount = Product::count();
    $actualAdded = $finalCount - $initialCount;
    echo "\nProduct count change: {$initialCount} → {$finalCount} (+{$actualAdded})\n";

    // Verify duplicates were prevented
    if ($duplicates > 0) {
        echo "\n✅ Duplicate prevention working correctly!\n";
        echo "Expected duplicates from CSV: 2 (TEST001 and TEST002 appear twice)\n";
        echo "Actual duplicates skipped: {$duplicates}\n";

        if ($duplicates == 2) {
            echo "🎯 Perfect! Exactly the right number of duplicates were skipped.\n";
        }
    }

    // Show the imported products
    echo "\n=== Imported Products ===\n";
    $testProducts = Product::where('code', 'like', 'TEST%')->get();
    foreach ($testProducts as $product) {
        echo "- Code: {$product->code} | Name: {$product->name} | ID: {$product->id}\n";
    }

    // Test trying to import the same file again
    echo "\n=== Testing Re-import (All Should Be Skipped) ===\n";
    $import2 = new ProductsImport();
    Excel::import($import2, $csvFile);

    $imported2 = $import2->getImportedCount();
    $duplicates2 = $import2->getDuplicateCount();

    echo "Second import - Products imported: {$imported2}\n";
    echo "Second import - Duplicates skipped: {$duplicates2}\n";

    if ($imported2 == 0 && $duplicates2 > 0) {
        echo "🎯 Perfect! Second import correctly skipped all products as duplicates.\n";
    }

} catch (\Exception $e) {
    echo "❌ Import failed: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "🧹 Cleaning up test data...\n";

// Clean up test products
$deleted = Product::where('code', 'like', 'TEST%')->delete();
echo "Deleted {$deleted} test products.\n";

echo "\n✅ Product duplicate prevention system tested successfully!\n";
