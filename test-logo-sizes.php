<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$qrService = app(\App\Services\QrCodeService::class);

echo "=== TEST DIMENSIONI LOGO QR ===\n\n";

$url = 'https://chatai-plants.app/test-logo-grande';
$logoPath = storage_path('app/public/stores/logos/test-logo-flover.png');

if (!file_exists($logoPath)) {
    echo "❌ Logo test non trovato\n";
    exit(1);
}

// Test con diversi size logo
$sizes = [
    80 => 'Originale (20% area)',
    100 => 'Medio (25% area)',
    120 => 'Nuovo Default (30% area)',
    140 => 'Grande (35% area - RISCHIO)',
    160 => 'Molto Grande (40% area - NON RACCOMANDATO)'
];

foreach ($sizes as $size => $label) {
    echo "Logo {$size}px ({$label}):\n";

    try {
        $svg = $qrService->generateThermalPrintQrSvg($url, $logoPath, $size);

        // Verifica dimensione logo nell'SVG
        if (preg_match('/width="(\d+)".*?height="(\d+)"/', $svg, $matches)) {
            $logoW = $matches[1];
            $logoH = $matches[2];
            echo "  ✅ Generato: Logo {$logoW}x{$logoH}px\n";
        }

        $fileSize = strlen($svg);
        echo "  📦 Dimensione SVG: " . number_format($fileSize) . " bytes\n";

        // Calcola percentuale area
        $qrArea = 400 * 400;
        $logoArea = $size * $size;
        $percentage = ($logoArea / $qrArea) * 100;
        echo "  📊 Area logo: " . round($percentage, 1) . "%\n";

        if ($percentage > 30) {
            echo "  ⚠️  ATTENZIONE: Supera il 30% - potrebbe non scannerizzare!\n";
        } else {
            echo "  ✅ OK: Dentro il limite 30%\n";
        }

    } catch (\Exception $e) {
        echo "  ❌ ERRORE: " . $e->getMessage() . "\n";
    }

    echo "\n";
}

echo "=== RACCOMANDAZIONI ===\n";
echo "✅ 80px: Conservativo, sempre leggibile\n";
echo "✅ 100px: Bilanciato, logo più visibile\n";
echo "✅ 120px: NUOVO DEFAULT - Massima visibilità sicura (30%)\n";
echo "⚠️  140px: Rischio moderato, testare prima\n";
echo "❌ 160px+: NON raccomandato, rischio scansione\n";

echo "\n💡 Logo attuale: 120px (30% area) - OTTIMALE!\n";
