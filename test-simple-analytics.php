<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Store;
use App\Models\QrScan;
use App\Models\ChatLog;
use App\Models\Interaction;
use App\Models\Newsletter;

echo "Simple Analytics Test...\n\n";

try {
    // Test basic model counts
    echo "Store count: " . Store::count() . "\n";
    echo "QrScan count: " . QrScan::count() . "\n";
    echo "ChatLog count: " . ChatLog::count() . "\n";
    echo "Interaction count: " . Interaction::count() . "\n";
    echo "Newsletter count: " . Newsletter::count() . "\n\n";

    // Test date range query
    $startDate = \Carbon\Carbon::now()->subDays(30)->format('Y-m-d');
    $endDate = \Carbon\Carbon::now()->format('Y-m-d');

    echo "Date range: {$startDate} to {$endDate}\n";
    echo "QrScans in range: " . QrScan::whereBetween('created_at', [$startDate, $endDate])->count() . "\n";
    echo "ChatLogs in range: " . ChatLog::whereBetween('created_at', [$startDate, $endDate])->count() . "\n";

    echo "\n✅ Basic model access working!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\nTest complete.\n";
