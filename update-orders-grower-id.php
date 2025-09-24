<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

try {
    echo "=== UPDATING ORDERS WITH GROWER_ID ===\n";

    // Trova tutti gli ordini senza grower_id
    $orders = Order::whereNull('grower_id')->with('products')->get();

    echo "Found {$orders->count()} orders without grower_id\n\n";

    $updated = 0;
    $skipped = 0;

    foreach ($orders as $order) {
        echo "Order #{$order->id} ({$order->order_number}):\n";

        // Prendi il primo prodotto per determinare il grower
        $firstProduct = $order->products()->first();

        if ($firstProduct && $firstProduct->grower_id) {
            // Aggiorna l'ordine con grower_id
            $order->update(['grower_id' => $firstProduct->grower_id]);
            echo "  → Updated with grower_id: {$firstProduct->grower_id}\n";
            $updated++;
        } else {
            echo "  → No products with grower_id found, skipping\n";
            $skipped++;
        }
    }

    echo "\n=== SUMMARY ===\n";
    echo "Updated: {$updated} orders\n";
    echo "Skipped: {$skipped} orders\n";

    // Verifica finale
    $ordersWithGrowerCount = Order::whereNotNull('grower_id')->count();
    $totalOrders = Order::count();

    echo "\nFinal status:\n";
    echo "Orders with grower_id: {$ordersWithGrowerCount}/{$totalOrders}\n";

    if ($ordersWithGrowerCount > 0) {
        echo "\n✅ Migration successful! Orders now have grower_id.\n";
    } else {
        echo "\n⚠️ No orders were updated. Check if products have grower_id.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
