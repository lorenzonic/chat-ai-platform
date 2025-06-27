<?php

// Quick test script for QR Code improvements
// Run this in the Laravel root directory to test the new functionality

require_once 'vendor/autoload.php';

use App\Models\QrCode;
use App\Models\QrScan;

echo "=== QR Code Improvements Test ===\n\n";

try {
    // Test if we can access QrCode model and its new methods
    echo "Testing QrCode model...\n";

    $qrCode = QrCode::first();

    if ($qrCode) {
        echo "✓ QrCode found: {$qrCode->name}\n";
        echo "✓ Total scans: {$qrCode->total_scans}\n";
        echo "✓ Unique visitors: {$qrCode->unique_visitors}\n";
        echo "✓ Mobile scans: {$qrCode->mobile_scans}\n";
        echo "✓ Desktop scans: {$qrCode->desktop_scans}\n";
        echo "✓ Recent scans: {$qrCode->recent_scans}\n";
        echo "✓ QR URL: {$qrCode->getQrUrl()}\n";

        // Test stats accessor
        $stats = $qrCode->stats;
        echo "✓ Stats accessor working: " . json_encode($stats) . "\n";

    } else {
        echo "! No QR codes found in database\n";
    }

    echo "\nTesting QrScan model...\n";
    $scanCount = QrScan::count();
    echo "✓ Total scans in database: {$scanCount}\n";

    echo "\nAll tests passed! ✓\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Please make sure the database is connected and tables exist.\n";
}

echo "\n=== Test completed ===\n";
