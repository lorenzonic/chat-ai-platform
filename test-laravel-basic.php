<?php
// Test basic Laravel functionality
try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    echo "✅ Laravel bootstrap: OK\n";

    // Test database connection
    try {
        DB::connection()->getPdo();
        echo "✅ Database connection: OK\n";
    } catch (Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    }

    // Test if grower guard exists
    try {
        $guards = config('auth.guards');
        if (isset($guards['grower'])) {
            echo "✅ Grower guard configured: OK\n";
        } else {
            echo "❌ Grower guard not found\n";
        }
    } catch (Exception $e) {
        echo "❌ Auth config error: " . $e->getMessage() . "\n";
    }

    // Test Grower model
    try {
        $growerCount = App\Models\Grower::count();
        echo "✅ Grower model: OK (found {$growerCount} growers)\n";
    } catch (Exception $e) {
        echo "❌ Grower model error: " . $e->getMessage() . "\n";
    }

    // Test Product model and orderItems relationship
    try {
        $product = App\Models\Product::first();
        if ($product) {
            $product->orderItems; // Test the relationship
            echo "✅ Product orderItems relationship: OK\n";
        } else {
            echo "⚠️ No products found, but model is OK\n";
        }
    } catch (Exception $e) {
        echo "❌ Product orderItems relationship error: " . $e->getMessage() . "\n";
    }

    echo "\n🎯 All basic tests completed!\n";

} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
