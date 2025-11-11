<?php
/**
 * Test QR Scans Geographic Data for Map
 *
 * Verifica che le scansioni QR vengano mostrate sulla mappa analytics
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "═══════════════════════════════════════════════════════════════\n";
echo "   TEST QR SCANS GEOGRAPHIC DATA - STORE ANALYTICS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Test 1: Verifica tabella qr_scans
echo "✓ Test 1: Database qr_scans table\n";
$totalQrScans = DB::table('qr_scans')->count();
echo "  ℹ Total QR scans in database: {$totalQrScans}\n";

$scansWithGeo = DB::table('qr_scans')
    ->whereNotNull('geo_location')
    ->count();
echo "  ℹ QR scans with geo_location: {$scansWithGeo}\n\n";

// Test 2: Verifica scans per store
echo "✓ Test 2: QR Scans by Store\n";
$storesWithScans = DB::table('qr_scans')
    ->select('store_id', DB::raw('COUNT(*) as scan_count'))
    ->groupBy('store_id')
    ->get();

echo "  Stores with QR scans:\n";
foreach ($storesWithScans as $storeData) {
    $store = \App\Models\Store::find($storeData->store_id);
    if ($store) {
        echo "  • {$store->name} (ID: {$store->id}): {$storeData->scan_count} scans\n";
    }
}
echo "\n";

// Test 3: Verifica formato geo_location
echo "✓ Test 3: geo_location field structure\n";
$sampleScan = DB::table('qr_scans')
    ->whereNotNull('geo_location')
    ->first();

if ($sampleScan) {
    $geoLocation = json_decode($sampleScan->geo_location, true);
    echo "  Sample geo_location structure:\n";
    echo "  " . str_repeat('─', 60) . "\n";
    print_r($geoLocation);

    $hasLat = isset($geoLocation['lat']);
    $hasLng = isset($geoLocation['lng']);
    $hasCity = isset($geoLocation['city']);
    $hasCountry = isset($geoLocation['country']);

    echo "\n  Field validation:\n";
    echo "  • Has 'lat': " . ($hasLat ? "✓ YES" : "✗ NO") . "\n";
    echo "  • Has 'lng': " . ($hasLng ? "✓ YES" : "✗ NO") . "\n";
    echo "  • Has 'city': " . ($hasCity ? "✓ YES" : "✗ NO") . "\n";
    echo "  • Has 'country': " . ($hasCountry ? "✓ YES" : "✗ NO") . "\n";
} else {
    echo "  ⚠ No QR scans with geo_location found\n";
}
echo "\n";

// Test 4: Simula chiamata analytics API
echo "✓ Test 4: Simulate Analytics API Call\n";
$testStore = \App\Models\Store::whereHas('qrScans')->first();

if ($testStore) {
    echo "  Testing with store: {$testStore->name} (ID: {$testStore->id})\n";

    $qrScans = \App\Models\QrScan::where('store_id', $testStore->id)->get();
    echo "  • Total QR scans: {$qrScans->count()}\n";

    $scansWithGeo = $qrScans->filter(function($scan) {
        $geo = $scan->geo_location;
        return $geo && isset($geo['lat']) && isset($geo['lng']);
    });

    echo "  • QR scans with valid coordinates: {$scansWithGeo->count()}\n";

    // Mostra coordinate esempio
    if ($scansWithGeo->count() > 0) {
        echo "\n  Sample coordinates:\n";
        echo "  " . str_repeat('─', 60) . "\n";
        foreach ($scansWithGeo->take(5) as $scan) {
            $geo = $scan->geo_location;
            echo "  • Lat: {$geo['lat']}, Lng: {$geo['lng']}";
            if (isset($geo['city'])) echo " - {$geo['city']}";
            if (isset($geo['country'])) echo ", {$geo['country']}";
            echo "\n";
        }
    }
} else {
    echo "  ⚠ No store found with QR scans\n";
}
echo "\n";

// Test 5: Verifica AnalyticsController updates
echo "✓ Test 5: AnalyticsController code check\n";
$controllerPath = __DIR__ . '/app/Http/Controllers/Store/AnalyticsController.php';
$controllerContent = file_get_contents($controllerPath);

$hasQrScanImport = str_contains($controllerContent, 'use App\Models\QrScan');
$hasQrScansVariable = str_contains($controllerContent, '$qrScans = QrScan::where');
$hasGeographicUpdate = str_contains($controllerContent, 'qr_scans_count');
$hasResponseQrScans = str_contains($controllerContent, "'qr_scans' =>");

echo "  • Imports QrScan model: " . ($hasQrScanImport ? "✓ YES" : "✗ NO") . "\n";
echo "  • Queries QR scans: " . ($hasQrScansVariable ? "✓ YES" : "✗ NO") . "\n";
echo "  • Geographic data includes QR scans: " . ($hasGeographicUpdate ? "✓ YES" : "✗ NO") . "\n";
echo "  • JSON response includes QR scans: " . ($hasResponseQrScans ? "✓ YES" : "✗ NO") . "\n";
echo "\n";

// Test 6: Create sample QR scan with geo_location (if none exists)
echo "✓ Test 6: Sample data check\n";
if ($scansWithGeo->count() == 0) {
    echo "  ⚠ No QR scans with geo_location found!\n";
    echo "  You can create sample data by scanning a QR code\n";
    echo "  Or manually insert test data:\n\n";

    echo "  INSERT INTO qr_scans (store_id, qr_code_id, ip_address, geo_location, device_type, created_at, updated_at)\n";
    echo "  VALUES (1, 1, '93.45.123.45', \n";
    echo "    '{\"lat\": 45.4642, \"lng\": 9.1900, \"city\": \"Milano\", \"country\": \"Italy\"}',\n";
    echo "    'mobile', NOW(), NOW());\n\n";
} else {
    echo "  ✓ QR scans with geo_location exist and are ready for map display\n";
}
echo "\n";

echo "═══════════════════════════════════════════════════════════════\n";
echo "   SUMMARY\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "✓ Sistema QR Scans Map:\n";
echo "  1. Database qr_scans: {$totalQrScans} records\n";
echo "  2. Scans with geo_location: {$scansWithGeo}\n";
echo "  3. AnalyticsController: " . ($hasQrScanImport && $hasQrScansVariable ? "✓ Updated" : "⚠ Needs update") . "\n";
echo "  4. Geographic data includes QR scans: " . ($hasGeographicUpdate ? "✓ YES" : "✗ NO") . "\n\n";

$scansWithGeoCount = isset($scansWithGeo) && is_object($scansWithGeo) ? $scansWithGeo->count() : $scansWithGeo;

if ($hasQrScanImport && $hasQrScansVariable && $hasGeographicUpdate && $scansWithGeoCount > 0) {
    echo "✅ ALL SYSTEMS READY!\n";
    echo "   QR scans will appear on the analytics map\n\n";

    echo "📍 Map will show:\n";
    echo "   • Leads (from lead submissions)\n";
    echo "   • Interactions (from chatbot usage)\n";
    echo "   • QR Scans (from QR code scans) ← NEW!\n\n";

    echo "🎯 Each map marker will display:\n";
    echo "   • Location (city, country)\n";
    echo "   • Leads count\n";
    echo "   • Interactions count\n";
    echo "   • QR scans count ← NEW!\n";
    echo "   • Total activity\n\n";
} else {
    echo "⚠ SETUP INCOMPLETE\n";
    if (!$hasQrScanImport || !$hasQrScansVariable) {
        echo "   • AnalyticsController needs QrScan integration\n";
    }
    if (!$hasGeographicUpdate) {
        echo "   • getGeographicData() method needs update\n";
    }
    if ($scansWithGeoCount == 0) {
        echo "   • No QR scans with geo_location in database\n";
    }
}

echo "\n═══════════════════════════════════════════════════════════════\n";
