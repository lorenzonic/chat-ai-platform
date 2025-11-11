<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\QrCodeService;

echo "🧪 Test QrCodeService con endroid/qr-code\n";
echo "==========================================\n\n";

$qrService = app(QrCodeService::class);

// Test 1: QR Termic Print SVG
echo "1. Test QR Termica (SVG)...\n";
try {
    $thermalSvg = $qrService->generateThermalPrintQrSvg('https://example.com/test');
    $svgSize = strlen($thermalSvg);
    echo "   ✅ SVG generato: " . number_format($svgSize) . " bytes\n";
    echo "   📊 Preview: " . substr($thermalSvg, 0, 100) . "...\n\n";
} catch (\Exception $e) {
    echo "   ❌ Errore: " . $e->getMessage() . "\n\n";
}

// Test 2: QR PNG
echo "2. Test QR PNG...\n";
try {
    $pngData = $qrService->generateThermalPrintQrPng('https://example.com/test-png');
    $pngSize = strlen($pngData);
    echo "   ✅ PNG generato: " . number_format($pngSize) . " bytes\n";
    echo "   📦 Tipo: " . (substr($pngData, 0, 8) === "\x89PNG\r\n\x1a\n" ? 'Valid PNG' : 'Invalid') . "\n\n";
} catch (\Exception $e) {
    echo "   ❌ Errore: " . $e->getMessage() . "\n\n";
}

// Test 3: QR con salvataggio file
echo "3. Test salvataggio file...\n";
try {
    $filename = $qrService->generateAndSaveQrImage(
        'https://example.com/test-save',
        'test-qr-' . time(),
        'svg'
    );
    echo "   ✅ File salvato: storage/app/public/" . $filename . "\n";

    // Verifica esistenza
    if (\Storage::disk('public')->exists($filename)) {
        $fileSize = \Storage::disk('public')->size($filename);
        echo "   📁 Dimensione file: " . number_format($fileSize) . " bytes\n";

        // Cleanup
        \Storage::disk('public')->delete($filename);
        echo "   🧹 File di test rimosso\n\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Errore: " . $e->getMessage() . "\n\n";
}

// Test 4: Verifica compatibilità con ProductLabelController
echo "4. Test compatibilità controller...\n";
try {
    $orderItem = \App\Models\OrderItem::with(['order', 'store', 'product'])->first();

    if ($orderItem) {
        echo "   📦 OrderItem trovato: ID " . $orderItem->id . "\n";
        echo "   🏪 Store: " . ($orderItem->store->name ?? 'N/A') . "\n";
        echo "   📦 Prodotto: " . ($orderItem->product->name ?? 'N/A') . "\n";

        // Simula generazione QR come farebbe il controller
        $testUrl = 'https://example.com/product/' . $orderItem->id;
        $qrSvg = $qrService->generateThermalPrintQrSvg($testUrl);
        echo "   ✅ QR generato per OrderItem: " . strlen($qrSvg) . " bytes\n\n";
    } else {
        echo "   ⚠️ Nessun OrderItem nel database\n\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Errore: " . $e->getMessage() . "\n\n";
}

echo "==========================================\n";
echo "✅ Test completati!\n";
echo "\n💡 Per testare visivamente:\n";
echo "   php artisan serve\n";
echo "   Vai su: http://localhost:8000/admin/products-stickers\n";
