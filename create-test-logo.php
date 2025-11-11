<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CREA LOGO DI TEST ===\n\n";

// Crea directory se non esiste
$logoDir = storage_path('app/public/stores/logos');
if (!file_exists($logoDir)) {
    mkdir($logoDir, 0755, true);
    echo "✅ Directory creata: $logoDir\n";
}

// Crea un'immagine di test (cerchio colorato con iniziale)
$size = 200;
$image = imagecreatetruecolor($size, $size);

// Colore sfondo (blu)
$bgColor = imagecolorallocate($image, 59, 130, 246); // Tailwind blue-500
$textColor = imagecolorallocate($image, 255, 255, 255); // Bianco

// Riempi sfondo
imagefilledrectangle($image, 0, 0, $size, $size, $bgColor);

// Cerchio bianco al centro
$white = imagecolorallocate($image, 255, 255, 255);
imagefilledellipse($image, $size/2, $size/2, $size - 40, $size - 40, $white);

// Testo "F" al centro (Flover)
$fontSize = 80;
$font = 5; // Font system
imagestring($image, $font, ($size/2) - 10, ($size/2) - 10, 'F', $bgColor);

// Salva
$filename = 'test-logo-flover.png';
$filepath = $logoDir . '/' . $filename;
imagepng($image, $filepath);
imagedestroy($image);

echo "✅ Logo di test creato: $filepath\n";
echo "📏 Dimensione: 200x200 px\n";
echo "🎨 Colore: Blu Tailwind\n\n";

// Aggiorna Store Flover (ID 1) con il logo
$store = \App\Models\Store::find(1);
if ($store) {
    $store->update(['logo' => 'stores/logos/' . $filename]);
    echo "✅ Logo assegnato a: {$store->name}\n";
    echo "🔗 Path DB: stores/logos/{$filename}\n";
} else {
    echo "⚠️  Store ID 1 non trovato\n";
}

echo "\n📍 TESTA ADESSO:\n";
echo "   1. Vai a: http://localhost:8000/admin/accounts/stores/1\n";
echo "   2. Dovresti vedere il logo nella sezione viola\n";
echo "   3. Stampa un'etichetta termica\n";
echo "   4. Controlla il QR code - deve avere il logo al centro!\n";
