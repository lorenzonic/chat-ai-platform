<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$qr = \App\Models\QrCode::where('ean_code', '8054045574486')->first();
echo "=== TEST getQrUrl() ===\n\n";
echo "QR #{$qr->id}\n";
echo "qr_url DB:  {$qr->qr_url}\n";
echo "getQrUrl(): " . $qr->getQrUrl() . "\n\n";

if ($qr->qr_url === $qr->getQrUrl()) {
    echo "✅ MATCH! getQrUrl() usa qr_url ottimizzato\n";
} else {
    echo "❌ DIFFERENT! getQrUrl() genera URL diverso\n";
    echo "   Lunghezza DB: " . strlen($qr->qr_url) . "\n";
    echo "   Lunghezza getQrUrl: " . strlen($qr->getQrUrl()) . "\n";
}
