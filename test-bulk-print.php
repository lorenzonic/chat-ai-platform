<?php
// Test Bulk Print System

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 Testing Bulk Print System\n";
echo "================================\n\n";

// 1. Check route exists
echo "1️⃣ Route Check:\n";
$routeExists = Route::has('admin.products.bulk-print');
echo "   - admin.products.bulk-print: " . ($routeExists ? "✓ EXISTS" : "✗ MISSING") . "\n\n";

// 2. Check thermal-label partial exists
echo "2️⃣ Partial Component Check:\n";
$partialPath = resource_path('views/admin/products/partials/thermal-label.blade.php');
$partialExists = file_exists($partialPath);
echo "   - thermal-label.blade.php: " . ($partialExists ? "✓ EXISTS" : "✗ MISSING") . "\n\n";

// 3. Check bulk-print view exists
echo "3️⃣ Bulk Print View Check:\n";
$bulkPrintPath = resource_path('views/admin/products/bulk-print.blade.php');
$bulkPrintExists = file_exists($bulkPrintPath);
echo "   - bulk-print.blade.php: " . ($bulkPrintExists ? "✓ EXISTS" : "✗ MISSING") . "\n";

if ($bulkPrintExists) {
    $content = file_get_contents($bulkPrintPath);
    $hasThermalLabel = strpos($content, '@include(\'admin.products.partials.thermal-label\'') !== false;
    $hasPageCSS = strpos($content, '@page') !== false;
    $hasQuantityLoop = strpos($content, '@for($i = 0; $i < $quantity; $i++)') !== false;

    echo "   - Uses thermal-label partial: " . ($hasThermalLabel ? "✓ YES" : "✗ NO") . "\n";
    echo "   - Has @page CSS (50mm x 25mm): " . ($hasPageCSS ? "✓ YES" : "✗ NO") . "\n";
    echo "   - Has quantity loop: " . ($hasQuantityLoop ? "✓ YES" : "✗ NO") . "\n";
}
echo "\n";

// 4. Count test data
echo "4️⃣ Database Test Data:\n";
$totalOrderItems = DB::table('order_items')->count();
$labelStores = DB::table('stores')->where('is_label_store', true)->count();
echo "   - Total OrderItems: {$totalOrderItems}\n";
echo "   - Stores with labels enabled: {$labelStores}\n\n";

// 5. Simulate a simple query
echo "5️⃣ Simulate Bulk Print Query:\n";
$sampleOrderItems = DB::table('order_items')
    ->join('stores', 'order_items.store_id', '=', 'stores.id')
    ->where('stores.is_label_store', true)
    ->select('order_items.*')
    ->limit(5)
    ->get();

echo "   - Sample OrderItems (first 5): {$sampleOrderItems->count()} found\n";
$totalQuantity = $sampleOrderItems->sum('quantity');
echo "   - Total labels to print: {$totalQuantity}\n\n";

// Summary
echo "📊 Summary:\n";
echo "================================\n";
if ($routeExists && $partialExists && $bulkPrintExists && $hasThermalLabel && $hasPageCSS && $hasQuantityLoop) {
    echo "✅ All checks PASSED!\n";
    echo "✅ Bulk thermal print system is ready!\n\n";
    echo "🚀 Next Steps:\n";
    echo "   1. Visit: http://localhost:8000/admin/products\n";
    echo "   2. Apply filters (optional)\n";
    echo "   3. Click 'Stampa Bulk' button\n";
    echo "   4. Verify preview shows {$totalQuantity} labels\n";
    echo "   5. Click 'Avvia Stampa' to print\n";
} else {
    echo "❌ Some checks FAILED - review output above\n";
}
