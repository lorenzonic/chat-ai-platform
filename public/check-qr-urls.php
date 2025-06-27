<?php

/**
 * QR Code URL Fix Script for Production
 * This script checks and displays QR code URLs to identify issues
 */

echo "=== QR CODE URL CHECKER ===\n\n";

// Check if we can access the Laravel app
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

// Check APP_URL config
echo "=== CONFIGURATION CHECK ===\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "APP_ENV: " . config('app.env') . "\n";
echo "Current Request URL: " . (isset($_SERVER['HTTP_HOST']) ?
    (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] : 'N/A') . "\n\n";

// Check QR codes
echo "=== QR CODES CHECK ===\n";

try {
    $qrCodes = \App\Models\QrCode::with('store')->get();

    if ($qrCodes->isEmpty()) {
        echo "⚠️  No QR codes found\n";
        exit(0);
    }

    echo "Found {$qrCodes->count()} QR codes:\n\n";

    $problematicCount = 0;

    foreach ($qrCodes as $qrCode) {
        $url = $qrCode->getQrUrl();
        $isProblematic = str_contains($url, 'localhost') ||
                        str_contains($url, '${') ||
                        !str_starts_with($url, 'http');

        if ($isProblematic) {
            $problematicCount++;
            echo "❌ ";
        } else {
            echo "✅ ";
        }

        echo "QR Code #{$qrCode->id} - {$qrCode->name}\n";
        echo "   Store: {$qrCode->store->name} (/{$qrCode->store->slug})\n";
        echo "   Target URL: {$url}\n";
        echo "   Ref Code: {$qrCode->ref_code}\n\n";
    }

    if ($problematicCount > 0) {
        echo "⚠️  ISSUES FOUND: {$problematicCount} QR codes have problematic URLs\n\n";

        echo "=== RECOMMENDED ACTIONS ===\n";
        echo "1. Set correct APP_URL in environment variables\n";
        echo "2. Run: php artisan qr:fix-urls\n";
        echo "3. Regenerate QR images: php artisan qr:fix-urls --regenerate\n\n";
    } else {
        echo "🎉 All QR code URLs look correct!\n\n";
    }

    // Environment specific recommendations
    if (config('app.env') === 'production') {
        echo "=== PRODUCTION ENVIRONMENT DETECTED ===\n";
        echo "Make sure these environment variables are set correctly:\n";
        echo "- APP_URL should be your Railway app URL (e.g., https://yourapp.railway.app)\n";
        echo "- RAILWAY_STATIC_URL should be automatically set by Railway\n";
        echo "- If using \${RAILWAY_STATIC_URL}, make sure it's being interpolated correctly\n\n";
    }

} catch (Exception $e) {
    echo "✗ Error checking QR codes: " . $e->getMessage() . "\n";
}

echo "=== DEBUGGING INFO ===\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Laravel Version: " . app()->version() . "\n";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "\n";
echo "Script executed at: " . date('Y-m-d H:i:s') . "\n";

if (isset($_SERVER['HTTP_HOST'])) {
    echo "\nAccess this script at: " .
         (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') .
         $_SERVER['HTTP_HOST'] . '/check-qr-urls.php' . "\n";
}

echo "\n=== END OF CHECK ===\n";
