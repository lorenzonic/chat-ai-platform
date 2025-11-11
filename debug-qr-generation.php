<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECK ULTIMO QR CODE GENERATO ===\n\n";

$latestQr = \App\Models\QrCode::with('store')->latest()->first();

if (!$latestQr) {
    echo "❌ Nessun QR code trovato\n";
    exit(1);
}

echo "📦 Ultimo QR Code generato:\n";
echo "ID: #{$latestQr->id}\n";
echo "Created: {$latestQr->created_at}\n";
echo "Store: {$latestQr->store->name}\n";
echo "Store Slug: {$latestQr->store->slug}\n";
echo "Store Short Code: {$latestQr->store->short_code}\n";
echo "Ref Code: {$latestQr->ref_code}\n";
echo "EAN Code: " . ($latestQr->ean_code ?? 'N/A') . "\n";
echo "\n";

echo "🔗 URL nel Database:\n";
echo "qr_url: " . ($latestQr->qr_url ?? 'NULL') . "\n";
echo "Lunghezza: " . strlen($latestQr->qr_url ?? '') . " caratteri\n\n";

// Verifica formato
if (empty($latestQr->qr_url)) {
    echo "❌ PROBLEMA: qr_url è vuoto!\n";
} elseif (preg_match('/\/([a-z]\d+)\/01\/(\d{14})/', $latestQr->qr_url, $matches)) {
    echo "✅ Formato ottimizzato rilevato!\n";
    echo "   Short Code: {$matches[1]}\n";
    echo "   GTIN-14: {$matches[2]}\n";
} else {
    echo "⚠️  URL NON usa formato ottimizzato\n";
    echo "   Formato atteso: /{short_code}/01/{gtin14}\n";
}

echo "\n=== CHECK ULTIMI 5 QR CODES ===\n\n";

$latest5 = \App\Models\QrCode::with('store')->latest()->take(5)->get();

foreach ($latest5 as $qr) {
    $isOptimized = preg_match('/\/([a-z]\d+)\/01\//', $qr->qr_url ?? '');
    $status = $isOptimized ? '✅' : '❌';
    $length = strlen($qr->qr_url ?? '');

    echo "{$status} QR #{$qr->id} | {$qr->created_at->format('Y-m-d H:i')} | {$length} char\n";
    echo "   URL: " . ($qr->qr_url ?? 'NULL') . "\n";
    echo "   Store: {$qr->store->slug} (short: {$qr->store->short_code})\n\n";
}

echo "=== ANALISI PROBLEMA ===\n";

// Check ProductLabelController
$controllerPath = app_path('Http/Controllers/Admin/ProductLabelController.php');
if (file_exists($controllerPath)) {
    $content = file_get_contents($controllerPath);

    if (strpos($content, 'generateOptimizedQrUrl') !== false) {
        echo "✅ ProductLabelController usa generateOptimizedQrUrl\n";
    } else {
        echo "❌ ProductLabelController NON chiama generateOptimizedQrUrl\n";
        echo "   Soluzione: modificare getOrderItemQrCode()\n";
    }
}

echo "\n";
