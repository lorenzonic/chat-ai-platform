<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test Store Login & Analytics ===\n\n";

try {
    // Trova uno store di test
    $store = \App\Models\Store::where('email', 'test@store.com')->first();

    if (!$store) {
        echo "❌ Test store not found. Creating one...\n";
        $store = \App\Models\Store::create([
            'name' => 'Test Store Analytics',
            'email' => 'test@store.com',
            'password' => bcrypt('password123'),
            'slug' => 'test-store-analytics-' . time(),
            'is_active' => true,
            'is_account_active' => true,
        ]);
        echo "✅ Test store created!\n";
    }

    echo "Store found: {$store->name} ({$store->email})\n";
    echo "Store ID: {$store->id}\n";
    echo "Store Slug: {$store->slug}\n";

    // Test analytics controller directly
    echo "\n=== Test Analytics Controller ===\n";

    // Simula login
    \Auth::guard('store')->login($store);
    echo "✅ Store logged in\n";

    // Test controller method
    $controller = new \App\Http\Controllers\Store\AnalyticsController();
    $request = \Illuminate\Http\Request::create('/store/analytics', 'GET');

    try {
        $response = $controller->index($request);
        echo "✅ Analytics controller response type: " . get_class($response) . "\n";

        if ($response instanceof \Illuminate\View\View) {
            echo "✅ View returned: " . $response->name() . "\n";
            echo "✅ View data keys: " . implode(', ', array_keys($response->getData())) . "\n";
        }

    } catch (Exception $e) {
        echo "❌ Controller error: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }

    // Test che ci siano dati per analytics
    echo "\n=== Test Analytics Data ===\n";

    $chatLogs = \App\Models\ChatLog::where('store_id', $store->id)->count();
    $leads = \App\Models\Lead::where('store_id', $store->id)->count();

    echo "Chat logs for store: $chatLogs\n";
    echo "Leads for store: $leads\n";

    if ($chatLogs == 0) {
        echo "⚠️  No chat logs found - creating test data...\n";
        \App\Models\ChatLog::create([
            'store_id' => $store->id,
            'session_id' => 'test-' . time(),
            'user_message' => 'Test user message',
            'ai_response' => 'Test AI response',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Browser',
        ]);
        echo "✅ Test chat log created\n";
    }

    echo "\n=== Login URL ===\n";
    echo "Login at: http://127.0.0.1:8002/store/login\n";
    echo "Email: {$store->email}\n";
    echo "Password: password123\n";
    echo "Then visit: http://127.0.0.1:8002/store/analytics\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
