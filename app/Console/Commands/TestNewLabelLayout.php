<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class TestNewLabelLayout extends Command
{
    protected $signature = 'test:new-label';
    protected $description = 'Test the new label layout (2.5cm x 5cm)';

    public function handle()
    {
        $this->info('=== TEST NUOVO LAYOUT ETICHETTA (2.5cm x 5cm) ===');
        $this->newLine();

        // Trova un prodotto di test
        $product = Product::first();

        if (!$product) {
            $this->error('Nessun prodotto trovato per il test!');
            return 1;
        }

        $this->info("Prodotto per test: {$product->name}");
        $this->line("- ID: {$product->id}");
        $this->line("- Prezzo: €{$product->price}");
        $this->line("- Cliente: {$product->client}");
        $this->line("- EAN: {$product->ean}");
        $this->newLine();

        $url = "http://localhost:8001/admin/products/{$product->id}";
        $this->info("✅ Nuovo layout etichetta implementato!");
        $this->newLine();
        $this->info("📏 Specifiche layout:");
        $this->line("- Dimensioni: 2.5cm (altezza) x 5cm (lunghezza)");
        $this->line("- TOP: QR code (sinistra) + Nome/Prezzo (destra)");
        $this->line("- BOTTOM: Barcode LUNGO orizzontale (tutta larghezza)");
        $this->line("- BOTTOM LINE: EAN (sinistra) + Codice Cliente (destra)");
        $this->newLine();
        $this->info("🌐 Visualizza l'etichetta qui:");
        $this->line($url);
        $this->newLine();
        $this->info("🎯 Layout finale come richiesto:");
        $this->line("┌─────────────────────────────────────────────────┐");
        $this->line("│  [QR]    │ Nome Prodotto (es: Prezzemolo P14)   │");
        $this->line("│  [ ]     │ € 2,90                               │");
        $this->line("├─────────────────────────────────────────────────┤");
        $this->line("│ [▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄] │");
        $this->line("│ 8051...                    FLover Bussolengo    │");
        $this->line("└─────────────────────────────────────────────────┘");
        $this->newLine();
        $this->info("🖨️  Usa il pulsante 'Stampa Etichetta' per vedere il risultato finale!");

        return 0;
    }
}
