<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Store;
use App\Models\Interaction;
use App\Models\Lead;

// Get the first store
$store = Store::first();

if (!$store) {
    echo "No store found. Please create a store first.\n";
    exit(1);
}

echo "Testing Analytics for Store: {$store->name}\n";
echo "Store ID: {$store->id}\n\n";

// Test interactions count
$interactionsCount = Interaction::where('store_id', $store->id)->count();
echo "Total Interactions: {$interactionsCount}\n";

// Test leads count
$leadsCount = Lead::where('store_id', $store->id)->count();
echo "Total Leads: {$leadsCount}\n";

// Test unique visitors (using correct column name)
$uniqueVisitors = Interaction::where('store_id', $store->id)
    ->distinct('ip')
    ->count();
echo "Unique Visitors: {$uniqueVisitors}\n";

// Test this month's data
$thisMonth = \Carbon\Carbon::now()->startOfMonth();
$thisMonthInteractions = Interaction::where('store_id', $store->id)
    ->where('created_at', '>=', $thisMonth)
    ->count();
echo "This Month Interactions: {$thisMonthInteractions}\n";

// Test QR scans
$qrScans = Interaction::where('store_id', $store->id)
    ->whereNotNull('qr_code_id')
    ->count();
echo "QR Scans: {$qrScans}\n\n";

echo "All tests passed! Analytics data is accessible.\n";
