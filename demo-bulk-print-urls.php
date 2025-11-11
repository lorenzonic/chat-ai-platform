<?php
// Quick Bulk Print Demo URL Generator

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🚀 Bulk Print Quick Demo\n";
echo "================================\n\n";

// Get a sample grower for filtering
$grower = DB::table('growers')->first();
$store = DB::table('stores')->where('is_label_store', true)->first();

if ($grower && $store) {
    echo "📋 Sample Test URLs:\n\n";

    // URL 1: With grower filter
    $url1 = route('admin.products.bulk-print', ['grower_id' => $grower->id]);
    echo "1️⃣ Bulk Print (Grower Filter):\n";
    echo "   {$url1}\n\n";

    // URL 2: With store filter
    $url2 = route('admin.products.bulk-print', ['store_id' => $store->id]);
    echo "2️⃣ Bulk Print (Store Filter):\n";
    echo "   {$url2}\n\n";

    // URL 3: No filters (all)
    $url3 = route('admin.products.bulk-print');
    echo "3️⃣ Bulk Print (All Items):\n";
    echo "   {$url3}\n\n";

    // Count for each
    $countGrower = DB::table('order_items')
        ->join('stores', 'order_items.store_id', '=', 'stores.id')
        ->where('stores.is_label_store', true)
        ->where('order_items.grower_id', $grower->id)
        ->count();

    $countStore = DB::table('order_items')
        ->join('stores', 'order_items.store_id', '=', 'stores.id')
        ->where('stores.is_label_store', true)
        ->where('order_items.store_id', $store->id)
        ->count();

    $countAll = DB::table('order_items')
        ->join('stores', 'order_items.store_id', '=', 'stores.id')
        ->where('stores.is_label_store', true)
        ->count();

    echo "📊 Expected Results:\n";
    echo "   - Grower '{$grower->name}': {$countGrower} OrderItems\n";
    echo "   - Store '{$store->name}': {$countStore} OrderItems\n";
    echo "   - All Items: {$countAll} OrderItems\n\n";

    echo "✅ Copy one of the URLs above and paste in browser!\n";
} else {
    echo "❌ No test data found in database\n";
}

echo "\n🖨️ Remember: Bulk print uses THERMAL LABELS 50mm x 25mm\n";
