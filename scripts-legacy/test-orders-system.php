<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\Store;
use App\Models\Grower;
use App\Models\Order;
use App\Imports\ProductsImport;

echo "=== TESTING ORDER CREATION SYSTEM ===\n\n";

echo "Cleaning existing data...\n";
Product::query()->delete();
Order::query()->delete();
Store::where('client_code', '!=', null)->delete();
Grower::query()->delete();

echo "Running import with order creation...\n";
$import = new ProductsImport();

try {
    $import->import(__DIR__ . '/test-orders-grouped.csv');

    // Update order totals after import
    $import->updateOrderTotals();

    echo "Import completed successfully!\n";
    echo "Imported: " . $import->getImportedCount() . " products\n";
    echo "Skipped: " . $import->getSkippedCount() . " rows\n";
    echo "New growers: " . $import->getNewGrowersCount() . "\n";
    echo "New stores: " . $import->getNewStoresCount() . "\n";
    echo "New orders: " . $import->getNewOrdersCount() . "\n\n";

    echo "=== ORDERS CREATED ===\n";
    Order::with(['store', 'products'])->get()->each(function($order) {
        echo "Order: {$order->order_number}\n";
        echo "  Store: {$order->store->name} ({$order->store->client_code})\n";
        echo "  Delivery Date: " . ($order->delivery_date ? $order->delivery_date->format('d/m/Y') : 'N/A') . "\n";
        echo "  Status: {$order->status}\n";
        echo "  Transport: {$order->transport}\n";
        echo "  Total Items: {$order->total_items}\n";
        echo "  Total Amount: €" . ($order->total_amount ?: '0.00') . "\n";
        echo "  Products:\n";
        $order->products->each(function($product) {
            echo "    - {$product->name} (Qty: {$product->quantity}, Price: €" . ($product->price ?: '0.00') . ")\n";
        });
        echo "  ---\n";
    });

} catch (Exception $e) {
    echo "Error during import: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
