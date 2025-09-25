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
$items = App\Models\OrderItem::latest()->take(5)->get(['id', 'order_id', 'product_name', 'quantity', 'created_at']);
foreach ($items as $item) {
    echo "ID: {$item->id}, Order: {$item->order_id}, Product: {$item->product_name}, Qty: {$item->quantity}, Created: {$item->created_at}\n";
}
?>