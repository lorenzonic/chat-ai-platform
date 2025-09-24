<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\QrCode;

class TestProductQrSystem extends Command
{
    protected $signature = 'test:product-qr';
    protected $description = 'Test the product QR code system';

    public function handle()
    {
        $this->info('=== TEST SISTEMA QR CODE PRODOTTI ===');
        $this->newLine();

        // Verifica prodotti disponibili
        $productsCount = Product::count();
        $this->info("Prodotti totali nel database: {$productsCount}");
        $this->newLine();

        if ($productsCount > 0) {
            // Prendi un prodotto di esempio
            $product = Product::first();
            $this->info('Prodotto di test:');
            $this->line("- ID: {$product->id}");
            $this->line("- Nome: {$product->name}");
            $this->line("- EAN: {$product->ean}");
            $this->line("- Cliente: {$product->client}");
            $this->line("- Store ID: {$product->store_id}");
            $this->newLine();

            // Verifica se esiste già un QR code per questo prodotto
            $existingQr = QrCode::where('product_id', $product->id)->first();

            if ($existingQr) {
                $this->info('QR Code esistente trovato:');
                $this->line("- ID: {$existingQr->id}");
                $this->line("- Nome: {$existingQr->name}");
                $this->line("- Domanda: {$existingQr->question}");
                $this->line("- EAN Code: {$existingQr->ean_code}");
                $this->line("- Product ID: {$existingQr->product_id}");
                $this->line("- Ref Code: {$existingQr->ref_code}");
                $this->line("- URL: {$existingQr->getQrUrl()}");
                $this->newLine();
            } else {
                $this->info('Nessun QR Code esistente per questo prodotto.');
                $this->newLine();
            }

            // Simula la creazione di un QR code come farebbe il controller
            $this->info('=== SIMULAZIONE CREAZIONE QR CODE ===');

            // Genera unique ref_code per il prodotto
            $baseRefCode = 'PROD-' . strtoupper(substr(md5($product->name . $product->id), 0, 8));
            $refCode = $baseRefCode;
            $counter = 1;

            // Assicura che il ref_code sia unico
            while (QrCode::where('ref_code', $refCode)->exists()) {
                $refCode = $baseRefCode . '-' . $counter;
                $counter++;
            }

            // Genera nome: "nome prodotto - codice cliente"
            $clientCode = $product->client ?? 'N/A';
            $qrName = $product->name . ' - ' . $clientCode;

            // Genera domanda: "come si cura [nome del prodotto]"
            $question = "Come si cura " . $product->name . "?";

            $this->info('Dati QR Code che verrebbero creati:');
            $this->line("- Nome: {$qrName}");
            $this->line("- Domanda: {$question}");
            $this->line("- EAN Code: {$product->ean}");
            $this->line("- Product ID: {$product->id}");
            $this->line("- Ref Code: {$refCode}");
            $this->newLine();

        } else {
            $this->error('Nessun prodotto trovato nel database.');
            $this->info('Per testare il sistema, è necessario avere almeno un prodotto nel database.');
        }

        $this->info('Test completato!');
        return 0;
    }
}
