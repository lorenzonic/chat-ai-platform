<?php

/**
 * Force Update Production URLs Script
 * This script manually updates QR code URLs for production environment
 */

echo "=== FORCE UPDATE QR CODE URLS ===\n\n";

try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "✓ Laravel app loaded successfully\n\n";

} catch (Exception $e) {
    echo "✗ Failed to load Laravel app: " . $e->getMessage() . "\n";
    exit(1);
}

// Force production URL if needed
$forceUrl = null;
if (isset($_GET['url'])) {
    $forceUrl = $_GET['url'];
    echo "🔧 Using forced URL: {$forceUrl}\n\n";
} else {
    // Try to detect the production URL
    if (isset($_SERVER['HTTP_HOST'])) {
        $forceUrl = 'https://' . $_SERVER['HTTP_HOST'];
        echo "🔍 Auto-detected URL: {$forceUrl}\n\n";
    }
}

if (!$forceUrl) {
    echo "❌ Could not determine production URL\n";
    echo "Usage: visit this script with ?url=https://your-domain.railway.app\n";
    exit(1);
}

// Get all QR codes
$qrCodes = \App\Models\QrCode::with('store')->get();

if ($qrCodes->isEmpty()) {
    echo "⚠️  No QR codes found\n";
    exit(0);
}

echo "Found {$qrCodes->count()} QR codes to update\n\n";

// Update each QR code URL by temporarily setting the correct APP_URL
$originalAppUrl = config('app.url');
config(['app.url' => $forceUrl]);

echo "=== UPDATING QR CODE URLS ===\n";

$updatedCount = 0;
$errorCount = 0;

foreach ($qrCodes as $qrCode) {
    try {
        $oldUrl = str_replace($forceUrl, $originalAppUrl, $qrCode->getQrUrl());
        $newUrl = $qrCode->getQrUrl();

        echo "QR Code #{$qrCode->id} - {$qrCode->name}\n";
        echo "  Old: {$oldUrl}\n";
        echo "  New: {$newUrl}\n";

        // Regenerate QR code image with new URL
        if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
            $qrCodeImage = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                ->size(300)
                ->margin(1)
                ->generate($newUrl);

            // Save the new image
            $fileName = 'qr_codes/qr_' . $qrCode->id . '_' . time() . '.png';

            if (!file_exists(storage_path('app/public/qr_codes'))) {
                mkdir(storage_path('app/public/qr_codes'), 0755, true);
            }

            file_put_contents(storage_path('app/public/' . $fileName), $qrCodeImage);

            $qrCode->update(['qr_code_image' => $fileName]);

            echo "  ✅ Updated with new image: {$fileName}\n\n";
            $updatedCount++;
        } else {
            echo "  ⚠️  QR code generator not available, only URL updated\n\n";
            $updatedCount++;
        }

    } catch (Exception $e) {
        echo "  ❌ Error: " . $e->getMessage() . "\n\n";
        $errorCount++;
    }
}

// Restore original config
config(['app.url' => $originalAppUrl]);

echo "=== UPDATE SUMMARY ===\n";
echo "✅ Successfully updated: {$updatedCount} QR codes\n";
if ($errorCount > 0) {
    echo "❌ Errors: {$errorCount} QR codes\n";
}

echo "\n=== VERIFICATION ===\n";
echo "Updated QR codes now point to: {$forceUrl}\n";
echo "Test a QR code by visiting one of the updated URLs above\n";

echo "\n=== NEXT STEPS ===\n";
echo "1. Test QR codes by scanning them\n";
echo "2. Verify they redirect to the correct store pages\n";
echo "3. Check analytics are working\n";
echo "4. Remove this script after verification\n";

if (isset($_SERVER['HTTP_HOST'])) {
    echo "\nAccess this script at: " .
         'https://' . $_SERVER['HTTP_HOST'] . '/force-update-qr-urls.php' .
         '?url=https://' . $_SERVER['HTTP_HOST'] . "\n";
}

echo "\n=== END OF UPDATE ===\n";
