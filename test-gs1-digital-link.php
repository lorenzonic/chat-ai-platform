<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST GS1 DIGITAL LINK FORMAT ===\n\n";

// Trova un OrderItem con EAN
$orderItem = \App\Models\OrderItem::with(['product', 'store', 'order'])
    ->whereHas('product', function($query) {
        $query->whereNotNull('ean')
              ->where('ean', '!=', '');
    })
    ->first();

if (!$orderItem) {
    echo "❌ Nessun OrderItem con EAN trovato\n";
    exit(1);
}

echo "📦 OrderItem: #{$orderItem->id}\n";
echo "🏷️  Prodotto: " . ($orderItem->product_snapshot['name'] ?? 'N/A') . "\n";
echo "🏪 Store: {$orderItem->store->name}\n";

// Ottieni o crea QR code
$controller = new \App\Http\Controllers\Admin\ProductLabelController(
    app(\App\Services\QrCodeService::class)
);

$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('getOrderItemQrCode');
$method->setAccessible(true);

$qrCode = $method->invoke($controller, $orderItem);

if (!$qrCode) {
    echo "❌ QR Code non generato\n";
    exit(1);
}

echo "\n=== FORMATO URL ===\n";
$url = $qrCode->getQrUrl();

echo "URL completo:\n{$url}\n\n";

// Analizza URL
$parsed = parse_url($url);
$path = $parsed['path'] ?? '';
$query = $parsed['query'] ?? '';

echo "Analisi componenti:\n";
echo "  Schema: " . ($parsed['scheme'] ?? 'N/A') . "\n";
echo "  Host: " . ($parsed['host'] ?? 'N/A') . "\n";
echo "  Path: {$path}\n";
echo "  Query: {$query}\n";

// Verifica formato GS1 Digital Link
$pattern = '/\/([a-z0-9-]+)\/01\/(\d{14})/';
if (preg_match($pattern, $path, $matches)) {
    echo "\n✅✅✅ FORMATO GS1 DIGITAL LINK CORRETTO!\n";
    echo "  Store slug: {$matches[1]}\n";
    echo "  GTIN-14: {$matches[2]}\n";

    // Verifica GTIN-14
    $gtin14 = $matches[2];
    $indicator = substr($gtin14, 0, 1);
    $ean13 = substr($gtin14, 1);

    echo "\n  Indicator digit: {$indicator}";
    if ($indicator === '0') {
        echo " ✅ (consumer unit - corretto)\n";
    } else {
        echo " ⚠️  (non è 0 - potrebbe essere packaging)\n";
    }

    echo "  EAN-13 estratto: {$ean13}\n";

    // Verifica con EAN prodotto
    $productEan = $orderItem->product->ean ?? ($orderItem->product_snapshot['ean'] ?? 'N/A');
    echo "  EAN-13 prodotto: {$productEan}\n";

    if ($ean13 === $productEan) {
        echo "  ✅ MATCH PERFETTO!\n";
    } else {
        echo "  ❌ NON CORRISPONDONO!\n";
    }
} else {
    echo "\n❌ FORMATO NON CONFORME GS1 Digital Link\n";
    echo "   Atteso: /store-slug/01/01234567890123\n";
    echo "   Trovato: {$path}\n";
}

// Test formato QR
echo "\n=== TEST QR CODE ===\n";
$qrService = app(\App\Services\QrCodeService::class);
$svg = $qrService->generateThermalPrintQrSvg($url);
echo "QR SVG generato: " . strlen($svg) . " bytes\n";
echo "✅ QR code pronto per stampa termica\n";

echo "\n=== ESEMPIO URL ===\n";
echo "Se il dominio è chatai-plants.app e EAN è 8001234567890:\n";
echo "https://chatai-plants.app/flover-garden/01/08001234567890?ref=ABC123\n";
echo "                          ^^^^^^^^^^^ ^^ ^^^^^^^^^^^^^^\n";
echo "                          store-slug  AI GTIN-14\n";
