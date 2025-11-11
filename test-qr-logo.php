<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$qrService = app(\App\Services\QrCodeService::class);

echo "=== TEST QR CON E SENZA LOGO ===\n\n";

// Test 1: QR SENZA LOGO
echo "1️⃣ Test QR SENZA LOGO (store senza logo):\n";
$url = 'https://chatai-plants.app/test-no-logo';
$svgNoLogo = $qrService->generateThermalPrintQrSvg($url, null);

// Controlla se ha logo
$hasLogoTag = strpos($svgNoLogo, '<image') !== false;
preg_match('/viewBox="([^"]+)"/', $svgNoLogo, $matches);
$viewBox = $matches[1] ?? 'N/A';

echo "   viewBox: {$viewBox}\n";
echo "   Contiene tag <image>: " . ($hasLogoTag ? '❌ SI (ERRORE!)' : '✅ NO (corretto)') . "\n";
echo "   Dimensione: " . strlen($svgNoLogo) . " bytes\n";

if (!$hasLogoTag) {
    echo "   ✅✅✅ PERFETTO! QR senza spazio logo al centro\n";
} else {
    echo "   ❌❌❌ ERRORE! QR ha spazio logo anche se non richiesto\n";
}

echo "\n";

// Test 2: QR CON LOGO
echo "2️⃣ Test QR CON LOGO (store con logo esistente):\n";

// Trova un logo reale
$logoPath = storage_path('app/public/stores/logos/test-logo-flover.png');
if (!file_exists($logoPath)) {
    echo "   ⚠️  Logo test non trovato: {$logoPath}\n";
    echo "   Creo logo di test...\n";

    // Crea directory se non esiste
    $logoDir = dirname($logoPath);
    if (!file_exists($logoDir)) {
        mkdir($logoDir, 0755, true);
    }

    // Crea immagine semplice
    $img = imagecreatetruecolor(100, 100);
    $blue = imagecolorallocate($img, 59, 130, 246);
    imagefilledrectangle($img, 0, 0, 100, 100, $blue);
    imagepng($img, $logoPath);
    imagedestroy($img);
    echo "   ✅ Logo creato\n";
}

$svgWithLogo = $qrService->generateThermalPrintQrSvg($url, $logoPath);

$hasLogoTag = strpos($svgWithLogo, '<image') !== false;
preg_match('/viewBox="([^"]+)"/', $svgWithLogo, $matches);
$viewBox = $matches[1] ?? 'N/A';

echo "   Logo path: {$logoPath}\n";
echo "   viewBox: {$viewBox}\n";
echo "   Contiene tag <image>: " . ($hasLogoTag ? '✅ SI (corretto)' : '❌ NO (ERRORE!)') . "\n";
echo "   Dimensione: " . strlen($svgWithLogo) . " bytes\n";

if ($hasLogoTag) {
    // Cerca dimensioni logo
    preg_match('/width="(\d+)".*?height="(\d+)"/', $svgWithLogo, $logoSize);
    if (isset($logoSize[1])) {
        echo "   Logo dimensione: {$logoSize[1]}x{$logoSize[2]} px\n";
    }
    echo "   ✅✅✅ PERFETTO! QR con logo al centro\n";
} else {
    echo "   ❌❌❌ ERRORE! QR dovrebbe avere logo ma non c'è\n";
}

echo "\n";

// Test 3: QR con logo path INVALIDO (deve comportarsi come senza logo)
echo "3️⃣ Test QR CON LOGO PATH INVALIDO (file non esiste):\n";
$invalidPath = storage_path('app/public/stores/logos/non-esiste.png');
$svgInvalidLogo = $qrService->generateThermalPrintQrSvg($url, $invalidPath);

$hasLogoTag = strpos($svgInvalidLogo, '<image') !== false;
echo "   Logo path: {$invalidPath} (non esiste)\n";
echo "   Contiene tag <image>: " . ($hasLogoTag ? '❌ SI (ERRORE!)' : '✅ NO (corretto)') . "\n";
echo "   Dimensione: " . strlen($svgInvalidLogo) . " bytes\n";

if (!$hasLogoTag) {
    echo "   ✅✅✅ PERFETTO! Fallback a QR normale\n";
} else {
    echo "   ❌❌❌ ERRORE! Dovrebbe ignorare logo invalido\n";
}

echo "\n=== RIEPILOGO ===\n";
echo "Senza logo: " . (strpos($svgNoLogo, '<image') === false ? '✅ OK' : '❌ FAIL') . "\n";
echo "Con logo:   " . (strpos($svgWithLogo, '<image') !== false ? '✅ OK' : '❌ FAIL') . "\n";
echo "Logo invalido: " . (strpos($svgInvalidLogo, '<image') === false ? '✅ OK' : '❌ FAIL') . "\n";
