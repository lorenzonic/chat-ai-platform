<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Store;
use Carbon\Carbon;

echo "Testing Store Performance Query...\n\n";

try {
    $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
    $endDate = Carbon::now()->format('Y-m-d');

    echo "Date range: {$startDate} to {$endDate}\n";

    $stores = Store::with(['qrScans' => function($q) use ($startDate, $endDate) {
        $q->whereBetween('created_at', [$startDate, $endDate]);
    }, 'chatLogs' => function($q) use ($startDate, $endDate) {
        $q->whereBetween('created_at', [$startDate, $endDate]);
    }, 'interactions' => function($q) use ($startDate, $endDate) {
        $q->whereBetween('created_at', [$startDate, $endDate]);
    }, 'newsletters' => function($q) use ($startDate, $endDate) {
        $q->whereBetween('created_at', [$startDate, $endDate]);
    }])
    ->get();

    echo "Found " . $stores->count() . " stores\n\n";

    foreach ($stores as $store) {
        $scans = $store->qrScans->count();
        $chats = $store->chatLogs->count();
        $interactions = $store->interactions->count();
        $newsletters = $store->newsletters->count();

        echo "Store: {$store->name}\n";
        echo "  - Scans: {$scans}\n";
        echo "  - Chats: {$chats}\n";
        echo "  - Interactions: {$interactions}\n";
        echo "  - Newsletters: {$newsletters}\n\n";
    }

    echo "✅ Store performance query working!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    if (strpos($e->getMessage(), 'newsletters') !== false) {
        echo "\n🔍 Possible issue with newsletters relationship.\n";
    }
}

echo "\nTest complete.\n";
