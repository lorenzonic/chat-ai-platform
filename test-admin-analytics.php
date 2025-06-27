<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Store;
use App\Models\QrCode;
use App\Models\QrScan;
use App\Models\ChatLog;
use App\Models\Interaction;
use App\Models\Newsletter;
use Carbon\Carbon;

echo "Testing Admin Analytics Controller...\n\n";

try {
    // Get first store or create one
    $store = Store::first();
    if (!$store) {
        echo "No store found. Creating a test store...\n";
        $store = Store::create([
            'name' => 'Test Store',
            'slug' => 'test-store',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
    }

    echo "Using store: {$store->name} (ID: {$store->id})\n\n";

    // Test analytics controller method
    $controller = new \App\Http\Controllers\Admin\AnalyticsController();
    $request = new \Illuminate\Http\Request([
        'store_id' => 'all',
        'start_date' => Carbon::now()->subDays(30)->format('Y-m-d'),
        'end_date' => Carbon::now()->format('Y-m-d')
    ]);

    echo "Calling analytics index method...\n";

    // Test with reflection to bypass authentication
    $result = $controller->index($request);

    if ($result instanceof \Illuminate\View\View) {
        echo "✅ Analytics view created successfully!\n";

        $data = $result->getData();
        echo "📊 Stats data: " . json_encode($data['stats'], JSON_PRETTY_PRINT) . "\n\n";
        echo "🏪 Total stores available: " . $data['stores']->count() . "\n";
        echo "📈 Charts data keys: " . implode(', ', array_keys($data['chartsData'])) . "\n";
        echo "⏰ Recent activity items: " . count($data['recentActivity']) . "\n";

    } else {
        echo "❌ Unexpected result type: " . get_class($result) . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "🔍 Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nTesting complete.\n";
