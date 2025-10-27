<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Testing Grower Dashboard...\n";

    // Get a grower
    $grower = App\Models\Grower::first();
    if (!$grower) {
        echo "No grower found in database\n";
        exit(1);
    }

    echo "Found grower: {$grower->name}\n";

    // Simulate authentication
    Auth::guard('grower')->login($grower);

    // Test the dashboard controller logic manually
    echo "Testing dashboard statistics...\n";

    $totalProducts = App\Models\Product::where('grower_id', $grower->id)->count() ?? 0;
    echo "Total products: {$totalProducts}\n";

    $totalOrders = App\Models\Order::whereHas('products', function($query) use ($grower) {
        $query->where('grower_id', $grower->id);
    })->count() ?? 0;
    echo "Total orders: {$totalOrders}\n";

    $productsInOrders = $grower->products()->whereHas('orderItems')->count() ?? 0;
    echo "Products in orders: {$productsInOrders}\n";

    echo "All dashboard queries successful!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
