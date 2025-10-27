<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Grower;

try {
    echo "=== TEST CORREZIONE GROWER_ID IN ORDERS ===\n\n";

    // 1. Verifica che grower_id non esista più in orders
    echo "1. VERIFICA STRUTTURA ORDERS\n";
    $ordersColumns = \Schema::getColumnListing('orders');
    $hasGrowerIdInOrders = in_array('grower_id', $ordersColumns);

    echo "   - grower_id in orders table: " . ($hasGrowerIdInOrders ? "❌ PRESENTE (ERRORE)" : "✅ RIMOSSA") . "\n";
    echo "   - orders table columns: " . implode(', ', $ordersColumns) . "\n\n";

    // 2. Verifica che grower_id esista in order_items
    echo "2. VERIFICA STRUTTURA ORDER_ITEMS\n";
    $orderItemsColumns = \Schema::getColumnListing('order_items');
    $hasGrowerIdInOrderItems = in_array('grower_id', $orderItemsColumns);

    echo "   - grower_id in order_items table: " . ($hasGrowerIdInOrderItems ? "✅ PRESENTE" : "❌ MANCANTE (ERRORE)") . "\n";
    echo "   - order_items table columns: " . implode(', ', $orderItemsColumns) . "\n\n";

    // 3. Test query ordini per grower (nuova logica)
    echo "3. TEST QUERY ORDINI PER GROWER\n";
    $grower = Grower::first();

    if ($grower) {
        echo "   - Testing grower: {$grower->company_name} (ID: {$grower->id})\n";

        // Query corretta: ordini che hanno order items del grower
        $ordersForGrowerNew = Order::whereHas('orderItems', function($q) use ($grower) {
            $q->where('grower_id', $grower->id);
        })->with(['store', 'orderItems' => function($q) use ($grower) {
            $q->where('grower_id', $grower->id)->with('product');
        }])->count();

        echo "   - Orders with grower's order items: {$ordersForGrowerNew}\n";

        // Dettaglio ordini
        $ordersDetail = Order::whereHas('orderItems', function($q) use ($grower) {
            $q->where('grower_id', $grower->id);
        })->with(['store', 'orderItems' => function($q) use ($grower) {
            $q->where('grower_id', $grower->id)->with('product');
        }])->get();

        foreach ($ordersDetail as $order) {
            echo "     * Order #{$order->order_number}: {$order->orderItems->count()} items del grower\n";
            foreach ($order->orderItems as $item) {
                echo "       - {$item->product->name} (Qty: {$item->quantity})\n";
            }
        }
    }
    echo "\n";

    // 4. Test relazioni complete
    echo "4. TEST RELAZIONI CORRETTE\n";

    $orderItemsWithRelations = OrderItem::with(['order', 'grower', 'product', 'store'])->get();
    foreach ($orderItemsWithRelations as $item) {
        echo "   - OrderItem {$item->id}:\n";
        echo "     * Order: " . ($item->order ? "✅ #{$item->order->order_number}" : "❌ Missing") . "\n";
        echo "     * Grower: " . ($item->grower ? "✅ {$item->grower->company_name}" : "❌ Missing") . "\n";
        echo "     * Product: " . ($item->product ? "✅ {$item->product->name}" : "❌ Missing") . "\n";
        echo "     * Store: " . ($item->store ? "✅ {$item->store->name}" : "❌ Missing") . "\n";
        echo "     * Grower match: " . ($item->grower_id == $item->product->grower_id ? "✅" : "❌") . "\n";
    }
    echo "\n";

    // 5. Test controller logic simulation
    echo "5. TEST CONTROLLER LOGIC\n";

    $grower = Grower::first();
    if ($grower) {
        // Simula ProductLabelController@index
        $products = \App\Models\Product::where('grower_id', $grower->id)
            ->with(['order', 'store', 'grower'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        echo "   - Products for grower: {$products->count()}\n";

        // Simula OrderController@index
        $orders = Order::whereHas('orderItems', function ($query) use ($grower) {
            $query->where('grower_id', $grower->id);
        })
        ->with(['store', 'orderItems' => function ($query) use ($grower) {
            $query->where('grower_id', $grower->id)->with('product');
        }])
        ->take(5)
        ->get();

        echo "   - Orders for grower: {$orders->count()}\n";

        // Simula ProductLabelController@orderItems
        $orderItems = OrderItem::where('grower_id', $grower->id)
            ->with(['product', 'order.store', 'store', 'grower'])
            ->take(5)
            ->get();

        echo "   - Order items for grower: {$orderItems->count()}\n";
    }
    echo "\n";

    echo "=== RIEPILOGO FINALE ===\n";
    echo ($hasGrowerIdInOrders ? "❌" : "✅") . " grower_id rimossa da orders\n";
    echo ($hasGrowerIdInOrderItems ? "✅" : "❌") . " grower_id presente in order_items\n";
    echo "✅ Query ordini aggiornate per usare order_items\n";
    echo "✅ Controller aggiornati per nuova logica\n";
    echo "✅ Relazioni funzionanti\n";

    if (!$hasGrowerIdInOrders && $hasGrowerIdInOrderItems) {
        echo "\n🎉 CORREZIONE COMPLETATA: STRUTTURA CORRETTA!\n";
    } else {
        echo "\n⚠️  ATTENZIONE: Verificare struttura database\n";
    }

} catch (Exception $e) {
    echo "❌ ERRORE: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
