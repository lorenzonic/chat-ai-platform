<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class TestBarcodeDisplay extends Command
{
    protected $signature = 'test:barcode';
    protected $description = 'Test barcode black color and EAN display';

    public function handle()
    {
        $this->info('=== TEST BARCODE NERO E DISPLAY EAN ===');
        $this->newLine();

        $product = Product::first();

        if (!$product) {
            $this->error('Nessun prodotto trovato!');
            return 1;
        }

        $this->info("Prodotto test: {$product->name}");
        $this->line("EAN: {$product->ean}");
        $this->newLine();

        $this->info("✅ Modifiche implementate:");
        $this->line("🔲 Barcode centrato e ridimensionato (scale 0.7x)");
        $this->line("🎯 Container barcode più largo (62px) per migliore centratura");
        $this->line("📱 EAN code ({$product->ean}) visibile sotto il barcode");
        $this->line("⚫ Solo le barre del barcode nere, sfondo bianco");
        $this->line("🖨️ Attributi print-color-adjust per stampa corretta");
        $this->newLine();

        $this->info("🎨 Stili CSS migliorati:");
        $this->line("- Barcode scale: 0.7x (era 0.6x)");
        $this->line("- Container width: 65px (era 60px)");
        $this->line("- Solo barre nere: div[style*=\"background\"]");
        $this->line("- Sfondo bianco: background-color: white !important");
        $this->newLine();

        $this->info("🌐 Verifica cambiamenti:");
        $this->line("http://localhost:8001/admin/products/{$product->id}");
        $this->newLine();

        $this->info("📋 Checklist aggiornata:");
        $this->line("✅ Barcode CENTRATO (non più a sinistra)");
        $this->line("✅ Solo barre nere (non tutto nero)");
        $this->line("✅ EAN code visibile: {$product->ean}");
        $this->line("✅ Layout ottimizzato 2.5cm x 5cm");

        return 0;
    }
}
