<?php

// Test script per creare alcuni order items di esempio
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;

try {
    echo "Creating test order items...\n";

    // Trova un ordine e alcuni prodotti
    $order = Order::with('grower')->first();
    if (!$order) {
        echo "No orders found!\n";
        exit;
    }

    $products = Product::where('grower_id', $order->grower_id)->limit(3)->get();
    if ($products->count() == 0) {
        echo "No products found for grower!\n";
        exit;
    }

    echo "Order: {$order->order_number}\n";
    echo "Grower: {$order->grower_id}\n";
    echo "Products found: {$products->count()}\n";

    // Crea order items
    foreach ($products as $index => $product) {
        $quantity = rand(1, 5);
        $unitPrice = $product->price ?: rand(5, 50);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'sku' => $product->code . '-' . $order->id,
            'notes' => 'Test order item ' . ($index + 1),
            'is_active' => true
        ]);

        echo "Created OrderItem ID: {$orderItem->id} - {$product->name} x{$quantity} @ €{$unitPrice}\n";
    }

    echo "\n✅ Test order items created successfully!\n";
    echo "Order ID: {$order->id}\n";
    echo "You can now test with: /grower/products-stickers?order_id={$order->id}\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "File: " . $e->getFile() . "\n";
}
