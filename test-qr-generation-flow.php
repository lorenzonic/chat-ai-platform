<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST GENERAZIONE NUOVO QR CODE ===\n\n";

// Trova un OrderItem per test
$orderItem = \App\Models\OrderItem::with(['product', 'store', 'order'])
    ->whereHas('product', function($query) {
        $query->whereNotNull('ean')
              ->where('ean', '!=', '');
    })
    ->inRandomOrder()
    ->first();

if (!$orderItem) {
    echo "❌ Nessun OrderItem con EAN trovato\n";
    exit(1);
}

echo "📦 Test OrderItem:\n";
echo "ID: #{$orderItem->id}\n";
echo "Prodotto: " . ($orderItem->product_snapshot['name'] ?? 'N/A') . "\n";
echo "Store: {$orderItem->store->name}\n";
echo "EAN: " . ($orderItem->product->ean ?? 'N/A') . "\n\n";

// Simula generazione QR come fa ProductLabelController
$qrCodeService = app(\App\Services\QrCodeService::class);

echo "=== STEP 1: Cerca QR esistente ===\n";
$existingQr = \App\Models\QrCode::where('order_id', $orderItem->order_id)
    ->where('store_id', $orderItem->store_id)
    ->where('product_id', $orderItem->product_id)
    ->first();

if ($existingQr) {
    echo "✅ QR esistente trovato: #{$existingQr->id}\n";
    echo "   URL database: {$existingQr->qr_url}\n";
    echo "   getQrUrl(): " . $existingQr->getQrUrl() . "\n";

    if ($existingQr->qr_url === $existingQr->getQrUrl()) {
        echo "   ✅ getQrUrl() usa qr_url del database\n";
    } else {
        echo "   ❌ getQrUrl() NON usa qr_url del database!\n";
    }

    $qrCode = $existingQr;
} else {
    echo "ℹ️  QR non esiste, creo nuovo...\n\n";

    echo "=== STEP 2: Crea QR Code ===\n";

    $productName = $orderItem->product_snapshot['name'] ?? 'Test Product';
    $productEan = $orderItem->product->ean;
    $refCode = 'TEST-' . strtoupper(substr(md5(uniqid()), 0, 8));

    $qrCode = \App\Models\QrCode::create([
        'store_id' => $orderItem->store_id,
        'order_id' => $orderItem->order_id,
        'product_id' => $orderItem->product_id,
        'name' => $productName,
        'question' => "Come si cura {$productName}?",
        'ref_code' => $refCode,
        'is_active' => true,
        'ean_code' => $productEan,
    ]);

    echo "✅ QR creato: #{$qrCode->id}\n";
    echo "   qr_url iniziale: " . ($qrCode->qr_url ?? 'NULL') . "\n\n";

    echo "=== STEP 3: Genera URL ottimizzato ===\n";
    $optimizedUrl = $qrCodeService->generateOptimizedQrUrl($qrCode);
    echo "URL ottimizzato generato:\n";
    echo "   {$optimizedUrl}\n";
    echo "   Lunghezza: " . strlen($optimizedUrl) . " caratteri\n\n";

    echo "=== STEP 4: Salva URL ottimizzato ===\n";
    $qrCode->qr_url = $optimizedUrl;
    $qrCode->save();
    echo "✅ URL salvato nel database\n\n";

    echo "=== STEP 5: Refresh e verifica ===\n";
    $qrCode->refresh();
    echo "qr_url nel DB: {$qrCode->qr_url}\n";
    echo "getQrUrl():    " . $qrCode->getQrUrl() . "\n";

    if ($qrCode->qr_url === $qrCode->getQrUrl()) {
        echo "✅✅✅ SUCCESS! getQrUrl() usa qr_url ottimizzato!\n";
    } else {
        echo "❌ PROBLEMA: getQrUrl() genera URL diverso\n";
    }
}

echo "\n=== VERIFICA FORMATO ===\n";
$url = $qrCode->getQrUrl();
echo "URL finale: {$url}\n";

if (preg_match('/\/([a-z]\d+)\/01\/(\d{14})/', $url, $matches)) {
    echo "✅ Formato ottimizzato!\n";
    echo "   Short Code: {$matches[1]}\n";
    echo "   GTIN-14: {$matches[2]}\n";
    echo "   Lunghezza: " . strlen($url) . " caratteri\n";

    // Verifica vs formato lungo
    $longUrl = url("/{$qrCode->store->slug}/01/" . $matches[2] . "?ref={$qrCode->ref_code}");
    $saved = strlen($longUrl) - strlen($url);
    $percent = round(($saved / strlen($longUrl)) * 100, 1);

    echo "\n📊 Risparmio:\n";
    echo "   URL lungo:  {$longUrl}\n";
    echo "   Lunghezza:  " . strlen($longUrl) . " char\n";
    echo "   Risparmio:  -{$saved} char (-{$percent}%)\n";
} else {
    echo "❌ Formato NON ottimizzato\n";
}

echo "\n=== TEST GENERAZIONE IMMAGINE ===\n";
echo "URL che sarà codificato nel QR: {$qrCode->getQrUrl()}\n";
echo "Lunghezza: " . strlen($qrCode->getQrUrl()) . " caratteri\n";

if (strlen($qrCode->getQrUrl()) < 60) {
    echo "✅ URL abbastanza corto per QR ottimale (< 60 char)\n";
} elseif (strlen($qrCode->getQrUrl()) < 80) {
    echo "⚠️  URL medio (60-80 char) - QR leggibile ma denso\n";
} else {
    echo "❌ URL troppo lungo (> 80 char) - QR molto denso\n";
}

// Cleanup se è un test
if (!$existingQr && isset($qrCode)) {
    echo "\n🧹 Pulizia QR di test...\n";
    $qrCode->delete();
    echo "✅ QR test #{$qrCode->id} eliminato\n";
}
