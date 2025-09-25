<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Latest Orders:\n";
$orders = App\Models\Order::latest()->take(5)->get(['id', 'order_number', 'client', 'created_at']);
foreach ($orders as $order) {
    echo "ID: {$order->id}, Number: {$order->order_number}, Client: {$order->client}, Created: {$order->created_at}\n";
}

echo "\nLatest OrderItems:\n";
$items = App\Models\OrderItem::with('product')->latest()->take(5)->get(['id', 'order_id', 'quantity', 'created_at']);
foreach ($items as $item) {
    $productName = $item->product_snapshot['name'] ?? $item->product->name ?? 'Unknown Product';
    echo "ID: {$item->id}, Order: {$item->order_id}, Product: {$productName}, Qty: {$item->quantity}, Created: {$item->created_at}\n";
}
?>