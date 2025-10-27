<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\OrderItem;

try {
    echo "=== Updating OrderItem store_id ===\n";

    $orderItem = OrderItem::first();
    if ($orderItem) {
        $order = $orderItem->order;
        $correctStoreId = $order->store_id;

        echo "OrderItem ID: {$orderItem->id}\n";
        echo "Current store_id: {$orderItem->store_id}\n";
        echo "Order store_id: {$correctStoreId}\n";

        $orderItem->update([
            'store_id' => $correctStoreId,
            'prezzo_rivendita' => $orderItem->unit_price * 1.2, // Esempio: 20% markup
            'ean' => 'TEST-EAN-' . $orderItem->product_id
        ]);

        echo "Updated store_id to: {$orderItem->store_id}\n";
        echo "Updated prezzo_rivendita to: €{$orderItem->prezzo_rivendita}\n";
        echo "Updated ean to: {$orderItem->ean}\n";

        echo "\n✅ OrderItem updated successfully!\n";
    } else {
        echo "No OrderItem found\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
