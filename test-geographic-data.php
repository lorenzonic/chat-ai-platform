<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Store;
use App\Models\Lead;
use App\Models\QrScan;
use Carbon\Carbon;

echo "Testing Geographic Data for Analytics Map...\n\n";

try {
    $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
    $endDate = Carbon::now()->format('Y-m-d');

    echo "Date range: {$startDate} to {$endDate}\n\n";

    // Test Lead data with coordinates
    $leadsWithCoords = Lead::whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();

    echo "Leads with coordinates: {$leadsWithCoords}\n";

    if ($leadsWithCoords > 0) {
        $sampleLead = Lead::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->first();
        echo "Sample lead coords: " . (float)$sampleLead->latitude . ", " . (float)$sampleLead->longitude . "\n";
    }

    // Test QrScan data with geo_location
    $qrScansWithGeo = QrScan::whereNotNull('geo_location')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();

    echo "QR Scans with geo_location: {$qrScansWithGeo}\n";

    if ($qrScansWithGeo > 0) {
        $sampleScan = QrScan::whereNotNull('geo_location')->first();
        echo "Sample scan geo_location: " . json_encode($sampleScan->geo_location) . "\n";
    }

    echo "\n✅ Geographic data test completed!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\nTest complete.\n";
