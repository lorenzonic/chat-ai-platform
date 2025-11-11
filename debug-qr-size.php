<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Testa direttamente QrCodeService
$qrService = app(\App\Services\QrCodeService::class);

$url = 'https://chatai-plants.app/test';
$svg = $qrService->generateThermalPrintQrSvg($url);

echo "=== TEST QR CODE SERVICE ===\n\n";

// Cerca viewBox nell'SVG
preg_match('/viewBox="([^"]+)"/', $svg, $matches);
if (isset($matches[1])) {
    echo "✅ viewBox trovato: " . $matches[1] . "\n";

    if ($matches[1] === '0 0 400 400') {
        echo "✅✅✅ CORRETTO! QR endroid con size 400\n";
    } else if ($matches[1] === '0 0 200 200') {
        echo "❌❌❌ ERRORE! Ancora SimpleSoftwareIO (size 200)\n";
    } else {
        echo "⚠️ Size diverso: " . $matches[1] . "\n";
    }
} else {
    echo "❌ viewBox NON trovato nell'SVG\n";
}

echo "\n📏 Dimensione SVG: " . strlen($svg) . " bytes\n";
echo "\n🔍 Prime 300 caratteri:\n";
echo substr($svg, 0, 300) . "...\n";
