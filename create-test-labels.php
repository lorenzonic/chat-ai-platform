<?php

require_once 'vendor/autoload.php';

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use App\Models\Grower;
use App\Models\Product;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "🏷️ Creazione ordini di test per stampa etichette...\n\n";

    // Get or create test store
    $store = Store::first();
    if (!$store) {
        echo "❌ Nessun store trovato. Crea prima uno store.\n";
        exit;
    }

    // Get or create test grower
    $grower = Grower::first();
    if (!$grower) {
        echo "❌ Nessun grower trovato. Crea prima un grower.\n";
        exit;
    }

    echo "✅ Store: {$store->name} (ID: {$store->id})\n";
    echo "✅ Grower: {$grower->name} (ID: {$grower->id})\n\n";

    // Create test order
    $order = Order::create([
        'store_id' => $store->id,
        'grower_id' => $grower->id,
        'order_number' => 'TEST-' . date('Ymd') . '-' . rand(100, 999),
        'delivery_date' => now()->addDays(7),
        'status' => 'pending',
        'notes' => 'Ordine di test per stampa etichette',
        'total_amount' => 0, // Will calculate later
    ]);

    echo "📦 Ordine creato: {$order->order_number}\n\n";

    // Test products with different quantities
    $testProducts = [
        [
            'name' => 'Rosa Rossa',
            'variety' => 'Red Rose Premium',
            'code' => 'ROSA-RED-001',
            'ean' => '8001234567890',
            'quantity' => 5,
            'price' => 15.50
        ],
        [
            'name' => 'Basilico Genovese',
            'variety' => 'Ocimum basilicum',
            'code' => 'BASI-GEN-002',
            'ean' => '8001234567891',
            'quantity' => 12,
            'price' => 3.75
        ],
        [
            'name' => 'Lavanda Inglese',
            'variety' => 'Lavandula angustifolia',
            'code' => 'LAVA-ENG-003',
            'ean' => '8001234567892',
            'quantity' => 8,
            'price' => 8.90
        ],
        [
            'name' => 'Geranio Rosso',
            'variety' => 'Pelargonium zonale',
            'code' => 'GERA-RED-004',
            'ean' => '8001234567893',
            'quantity' => 20,
            'price' => 4.25
        ],
        [
            'name' => 'Cactus Mix',
            'variety' => 'Assortimento varietà',
            'code' => 'CACT-MIX-005',
            'ean' => '8001234567894',
            'quantity' => 3,
            'price' => 12.00
        ]
    ];

    $totalAmount = 0;

    foreach ($testProducts as $productData) {
        // Create or find product
        $product = Product::firstOrCreate([
            'code' => $productData['code']
        ], [
            'grower_id' => $grower->id,
            'store_id' => $store->id,
            'name' => $productData['name'],
            'code' => $productData['code'],
            'ean' => $productData['ean'],
            'price' => $productData['price'],
            'quantity' => 100, // Stock quantity
            'is_active' => true
        ]);

        // Create order item
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'store_id' => $store->id,
            'grower_id' => $grower->id,
            'product_id' => $product->id,
            'quantity' => $productData['quantity'],
            'unit_price' => $productData['price'],
            'prezzo_rivendita' => $productData['price'],
            'total_price' => $productData['quantity'] * $productData['price'],
            'ean' => $productData['ean'],
            'product_snapshot' => [
                'name' => $productData['name'],
                'variety' => $productData['variety'],
                'code' => $productData['code'],
                'ean' => $productData['ean'],
                'price' => $productData['price']
            ]
        ]);

        $itemTotal = $productData['quantity'] * $productData['price'];
        $totalAmount += $itemTotal;

        echo "🌱 {$productData['name']} - Qty: {$productData['quantity']} - €{$productData['price']} cad. (Totale: €{$itemTotal})\n";
        echo "   OrderItem ID: {$orderItem->id} (per testing)\n";
    }

    // Update order total
    $order->update(['total_amount' => $totalAmount]);

    echo "\n💰 Totale ordine: €{$totalAmount}\n";
    echo "\n🎯 Test URLs:\n";

    // Show test URLs for each order item
    $orderItems = OrderItem::where('order_id', $order->id)->get();
    foreach ($orderItems as $item) {
        echo "  • {$item->product_snapshot['name']} ({$item->quantity}x):\n";
        echo "    Standard: http://127.0.0.1:8000/admin/products-stickers/{$item->id}\n";
        echo "    Termica:  http://127.0.0.1:8000/admin/products-stickers/{$item->id}/thermal\n\n";
    }

    echo "✅ Setup completato! Ora puoi testare la stampa etichette.\n";

} catch (Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
