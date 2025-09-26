<?php
// Test specifically for grower dashboard functionality
header('Content-Type: text/plain');

try {
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    echo "🏠 Grower Dashboard Test\n";
    echo "========================\n\n";
    
    // Test 1: Get a grower
    $grower = App\Models\Grower::first();
    if (!$grower) {
        echo "❌ No growers in database\n";
        exit;
    }
    echo "✅ Found grower: {$grower->name} (ID: {$grower->id})\n";
    
    // Test 2: Test authentication
    Auth::guard('grower')->login($grower);
    if (Auth::guard('grower')->check()) {
        echo "✅ Grower authentication works\n";
    } else {
        echo "❌ Grower authentication failed\n";
    }
    
    // Test 3: Test dashboard controller logic step by step
    echo "\n📊 Testing dashboard statistics:\n";
    
    try {
        $totalProducts = App\Models\Product::where('grower_id', $grower->id)->count();
        echo "✅ Total products: {$totalProducts}\n";
    } catch (Exception $e) {
        echo "❌ Products query failed: " . $e->getMessage() . "\n";
    }
    
    try {
        $totalOrders = App\Models\Order::whereHas('products', function($query) use ($grower) {
            $query->where('grower_id', $grower->id);
        })->count();
        echo "✅ Total orders: {$totalOrders}\n";
    } catch (Exception $e) {
        echo "❌ Orders query failed: " . $e->getMessage() . "\n";
    }
    
    try {
        $productsInOrders = $grower->products()->whereHas('orderItems')->count();
        echo "✅ Products in orders: {$productsInOrders}\n";
    } catch (Exception $e) {
        echo "❌ Products in orders query failed: " . $e->getMessage() . "\n";
    }
    
    // Test 4: Test controller instantiation
    try {
        $controller = new App\Http\Controllers\Grower\DashboardController();
        echo "✅ DashboardController can be instantiated\n";
    } catch (Exception $e) {
        echo "❌ DashboardController instantiation failed: " . $e->getMessage() . "\n";
    }
    
    // Test 5: Test middleware
    try {
        $middleware = new App\Http\Middleware\GrowerAuth();
        echo "✅ GrowerAuth middleware can be instantiated\n";
    } catch (Exception $e) {
        echo "❌ GrowerAuth middleware instantiation failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎯 Dashboard test complete!\n";
    
} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
?>