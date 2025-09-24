<?php

// Script di debug per controllare il sistema di ordini
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUG ORDERS SYSTEM ===\n\n";

try {
    // Test modelli
    echo "1. Testing Models:\n";
    echo "Orders: " . App\Models\Order::count() . "\n";
    echo "Stores: " . App\Models\Store::count() . "\n";
    echo "Products: " . App\Models\Product::count() . "\n";
    echo "Growers: " . App\Models\Grower::count() . "\n\n";

    // Test controller
    echo "2. Testing Controller:\n";
    $controller = new App\Http\Controllers\Admin\OrderController();
    echo "OrderController created successfully\n\n";

    // Test route
    echo "3. Testing Route:\n";
    $routes = Route::getRoutes();
    $orderRoutes = [];
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'admin/orders')) {
            $orderRoutes[] = $route->uri() . ' -> ' . $route->getActionName();
        }
    }
    echo "Order routes found: " . count($orderRoutes) . "\n";
    foreach ($orderRoutes as $route) {
        echo "- $route\n";
    }

    echo "\n4. Testing View File:\n";
    $viewPath = resource_path('views/admin/orders/index.blade.php');
    if (file_exists($viewPath)) {
        echo "View file exists: $viewPath\n";
        echo "File size: " . filesize($viewPath) . " bytes\n";
    } else {
        echo "❌ View file not found: $viewPath\n";
    }

    echo "\n✅ Debug completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
