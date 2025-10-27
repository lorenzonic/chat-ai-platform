<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "UPDATED Current database counts:\n";
echo "Orders: " . App\Models\Order::count() . "\n";
echo "OrderItems: " . App\Models\OrderItem::count() . "\n";
echo "Products: " . App\Models\Product::count() . "\n";
echo "Stores: " . App\Models\Store::count() . "\n";
echo "Growers: " . App\Models\Grower::count() . "\n";

echo "\nLatest Orders:\n";
$orders = App\Models\Order::latest()->take(3)->get(['id', 'order_number', 'client', 'created_at']);
foreach ($orders as $order) {
    echo "ID: {$order->id}, Number: {$order->order_number}, Client: {$order->client}, Created: {$order->created_at}\n";
}
?>