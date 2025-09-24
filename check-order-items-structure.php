<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== Checking order_items table structure ===\n";

    // Check current order_items
    $orderItems = DB::table('order_items')->get();
    echo "Order items count: " . $orderItems->count() . "\n";

    if ($orderItems->count() > 0) {
        $firstItem = $orderItems->first();
        echo "First item store_id: " . ($firstItem->store_id ?? 'NULL') . "\n";
        echo "First item prezzo_rivendita: " . ($firstItem->prezzo_rivendita ?? 'NULL') . "\n";
        echo "First item ean: " . ($firstItem->ean ?? 'NULL') . "\n";
    }

    // Check foreign keys
    $foreignKeys = DB::select("
        SELECT
            CONSTRAINT_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_NAME = 'order_items'
        AND TABLE_SCHEMA = DATABASE()
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");

    echo "\n=== Foreign Keys ===\n";
    foreach ($foreignKeys as $fk) {
        echo "- {$fk->CONSTRAINT_NAME}: {$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
    }

    // Check if we need to populate store_id
    $itemsWithoutStore = DB::table('order_items')
        ->whereNull('store_id')
        ->count();

    echo "\nItems without store_id: {$itemsWithoutStore}\n";

    if ($itemsWithoutStore > 0) {
        echo "Populating store_id from orders...\n";
        $updated = DB::statement('
            UPDATE order_items oi
            JOIN orders o ON oi.order_id = o.id
            SET oi.store_id = o.store_id
            WHERE oi.store_id IS NULL
        ');
        echo "Update completed\n";

        // Verify
        $itemsWithoutStore = DB::table('order_items')
            ->whereNull('store_id')
            ->count();
        echo "Items still without store_id: {$itemsWithoutStore}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
