<?php
// Simple debug test - minimal Laravel bootstrap
require_once '../bootstrap/app.php';

echo "<h1>Minimal Dashboard Debug</h1>\n";

try {
    // Get grower
    $grower = \App\Models\Grower::find(34);
    if (!$grower) {
        echo "<p>❌ Grower 34 not found</p>\n";
        exit;
    }
    echo "<p>✅ Grower: {$grower->name}</p>\n";

    // Test each query individually with error handling
    try {
        $totalProducts = $grower->products()->count();
        echo "<p>✅ Total Products: {$totalProducts}</p>\n";
    } catch (Exception $e) {
        echo "<p>❌ Error in totalProducts: " . $e->getMessage() . "</p>\n";
    }

    try {
        $totalOrders = \App\Models\Order::whereHas('orderItems.product', function($query) use ($grower) {
            $query->where('grower_id', $grower->id);
        })->count();
        echo "<p>✅ Total Orders: {$totalOrders}</p>\n";
    } catch (Exception $e) {
        echo "<p>❌ Error in totalOrders: " . $e->getMessage() . "</p>\n";
    }

    try {
        $productsInOrders = $grower->products()
            ->whereHas('orderItems')
            ->count();
        echo "<p>✅ Products in Orders: {$productsInOrders}</p>\n";
    } catch (Exception $e) {
        echo "<p>❌ Error in productsInOrders: " . $e->getMessage() . "</p>\n";
    }

    try {
        $lowStockProducts = \App\Models\Product::where('grower_id', $grower->id)
            ->where('quantity', '<=', 10)
            ->where('quantity', '>', 0)
            ->count();
        echo "<p>✅ Low Stock Products: {$lowStockProducts}</p>\n";
    } catch (Exception $e) {
        echo "<p>❌ Error in lowStockProducts: " . $e->getMessage() . "</p>\n";
    }

    try {
        $outOfStockProducts = \App\Models\Product::where('grower_id', $grower->id)
            ->where('quantity', 0)
            ->count();
        echo "<p>✅ Out of Stock Products: {$outOfStockProducts}</p>\n";
    } catch (Exception $e) {
        echo "<p>❌ Error in outOfStockProducts: " . $e->getMessage() . "</p>\n";
    }

    try {
        $recentProducts = \App\Models\Product::where('grower_id', $grower->id)
            ->latest()
            ->take(5)
            ->get();
        echo "<p>✅ Recent Products: " . $recentProducts->count() . "</p>\n";
    } catch (Exception $e) {
        echo "<p>❌ Error in recentProducts: " . $e->getMessage() . "</p>\n";
    }

    try {
        $recentOrders = \App\Models\Order::whereHas('orderItems.product', function($query) use ($grower) {
            $query->where('grower_id', $grower->id);
        })
        ->with(['store', 'orderItems.product' => function($query) use ($grower) {
            $query->where('grower_id', $grower->id);
        }])
        ->latest()
        ->take(5)
        ->get();
        echo "<p>✅ Recent Orders: " . $recentOrders->count() . "</p>\n";
    } catch (Exception $e) {
        echo "<p>❌ Error in recentOrders: " . $e->getMessage() . "</p>\n";
    }

    echo "<p>✅ All individual queries passed!</p>\n";

    // Now test the controller method directly
    try {
        $controller = new \App\Http\Controllers\Grower\DashboardController();

        // Set authenticated grower (simulate middleware)
        auth('grower')->login($grower);

        $response = $controller->index();
        echo "<p>✅ Controller method executed successfully</p>\n";
        echo "<p>Response type: " . get_class($response) . "</p>\n";

        if ($response instanceof \Illuminate\View\View) {
            echo "<p>✅ Response is a View</p>\n";

            // Try to get view data
            $viewData = $response->getData();
            echo "<p>View data keys: " . implode(', ', array_keys($viewData)) . "</p>\n";

            // Try to render
            $content = $response->render();
            echo "<p>✅ View rendered successfully (length: " . strlen($content) . ")</p>\n";
        }

    } catch (Exception $e) {
        echo "<p>❌ Error in controller/view: " . $e->getMessage() . "</p>\n";
        echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>\n";
        echo "<pre>" . $e->getTraceAsString() . "</pre>\n";
    }

} catch (Exception $e) {
    echo "<p>❌ General error: " . $e->getMessage() . "</p>\n";
    echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>\n";
}

echo "<p>Test completed: " . date('Y-m-d H:i:s') . "</p>\n";
?>
