<?php

// Test script to verify orders functionality
require_once 'vendor/autoload.php';

use App\Models\Store;
use App\Models\Order;
use App\Models\Product;
use App\Models\Grower;
use Illuminate\Support\Facades\DB;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Orders System...\n\n";

try {
    // Check if we have any stores
    $storesCount = Store::count();
    echo "Stores in database: {$storesCount}\n";

    // Check if we have any orders
    $ordersCount = Order::count();
    echo "Orders in database: {$ordersCount}\n";

    // Check if we have any products
    $productsCount = Product::count();
    echo "Products in database: {$productsCount}\n";

    // Check if we have any growers
    $growersCount = Grower::count();
    echo "Growers in database: {$growersCount}\n";

    if ($ordersCount > 0) {
        echo "\nFirst 3 orders:\n";
        $orders = Order::with(['store', 'products'])->take(3)->get();
        foreach ($orders as $order) {
            echo "- Order {$order->order_number}: {$order->store->name} ({$order->products->count()} products) - €" . number_format((float) $order->total_amount, 2) . "\n";
        }
    }

    echo "\nTesting Order Statistics:\n";
    $stats = [
        'total' => Order::count(),
        'pending' => Order::where('status', 'pending')->count(),
        'confirmed' => Order::where('status', 'confirmed')->count(),
        'shipped' => Order::where('status', 'shipped')->count(),
        'delivered' => Order::where('status', 'delivered')->count(),
    ];

    foreach ($stats as $key => $value) {
        echo "- {$key}: {$value}\n";
    }

    echo "\n✅ Orders system is working correctly!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
