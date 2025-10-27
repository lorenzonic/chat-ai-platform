<?php

require_once 'vendor/autoload.php';

use App\Models\Order;
use App\Models\OrderItem;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "🗑️ Cancellazione dati orders e order_items...\n\n";

    // Count before deletion
    $ordersCount = Order::count();
    $orderItemsCount = OrderItem::count();

    echo "📊 Stato attuale:\n";
    echo "  Orders: {$ordersCount}\n";
    echo "  Order Items: {$orderItemsCount}\n\n";

    if ($ordersCount === 0 && $orderItemsCount === 0) {
        echo "✅ Nessun dato da cancellare - tabelle già vuote!\n";
        exit;
    }

    // Ask for confirmation
    echo "⚠️  ATTENZIONE: Questa operazione cancellerà TUTTI gli ordini e order items!\n";
    echo "Vuoi continuare? (yes/no): ";

    $handle = fopen("php://stdin", "r");
    $confirmation = trim(fgets($handle));
    fclose($handle);

    if (strtolower($confirmation) !== 'yes' && strtolower($confirmation) !== 'y') {
        echo "❌ Operazione annullata dall'utente.\n";
        exit;
    }

    echo "\n🔄 Iniziando cancellazione...\n\n";

    // Delete order items first (foreign key constraint)
    echo "1️⃣ Cancellazione order_items...\n";
    $deletedItems = OrderItem::count();
    OrderItem::query()->delete();
    echo "   ✅ Cancellati {$deletedItems} order items\n\n";

    // Delete orders
    echo "2️⃣ Cancellazione orders...\n";
    $deletedOrders = Order::count();
    Order::query()->delete();
    echo "   ✅ Cancellati {$deletedOrders} orders\n\n";

    // Verify deletion
    $remainingOrders = Order::count();
    $remainingItems = OrderItem::count();

    echo "📊 Stato finale:\n";
    echo "  Orders: {$remainingOrders}\n";
    echo "  Order Items: {$remainingItems}\n\n";

    if ($remainingOrders === 0 && $remainingItems === 0) {
        echo "✅ Cancellazione completata con successo!\n";
        echo "🎉 Tutte le tabelle orders e order_items sono ora vuote.\n";
    } else {
        echo "⚠️ Alcuni record potrebbero non essere stati cancellati.\n";
    }

} catch (Exception $e) {
    echo "❌ Errore durante la cancellazione: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
