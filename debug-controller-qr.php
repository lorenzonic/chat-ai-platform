<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Trova OrderItem #75
$orderItem = \App\Models\OrderItem::find(75);

if (!$orderItem) {
    echo "❌ OrderItem #75 non trovato\n";
    exit(1);
}

echo "=== TEST LABEL DATA GENERATION ===\n\n";
echo "📦 OrderItem: {$orderItem->id}\n";
echo "🏷️ Prodotto: " . ($orderItem->product_snapshot['name'] ?? 'N/A') . "\n\n";

// Testa il controller
$controller = new \App\Http\Controllers\Admin\ProductLabelController(
    app(\App\Services\QrCodeService::class)
);

// Usa reflection per chiamare metodo privato
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('prepareLabelData');
$method->setAccessible(true);

$labelData = $method->invoke($controller, $orderItem);

echo "🔍 QR Code SVG generato:\n";
$svg = $labelData['qrcode']['svg'];

preg_match('/viewBox="([^"]+)"/', $svg, $matches);
if (isset($matches[1])) {
    echo "✅ viewBox: " . $matches[1] . "\n";

    if (strpos($matches[1], '429') !== false || strpos($matches[1], '400') !== false) {
        echo "✅✅✅ NUOVO QR endroid!\n";
    } else if (strpos($matches[1], '200') !== false) {
        echo "❌❌❌ VECCHIO QR SimpleSoftwareIO!\n";
    }
} else {
    echo "❌ viewBox non trovato\n";
}

echo "\n📏 Dimensione: " . strlen($svg) . " bytes\n";
echo "\n🔍 Prime 200 caratteri:\n";
echo substr($svg, 0, 200) . "...\n";
