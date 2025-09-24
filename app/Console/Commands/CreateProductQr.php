<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\QrCode;
use App\Http\Controllers\Admin\ProductLabelController;

class CreateProductQr extends Command
{
    protected $signature = 'create:product-qr {product_id}';
    protected $description = 'Create QR code for a specific product';

    public function handle()
    {
        $productId = $this->argument('product_id');

        $product = Product::find($productId);

        if (!$product) {
            $this->error("Prodotto con ID {$productId} non trovato!");
            return 1;
        }

        $this->info("Creazione QR code per prodotto: {$product->name}");

        // Simula la logica del controller
        $existingQr = QrCode::where('product_id', $product->id)
                           ->where('store_id', $product->store_id)
                           ->first();

        if ($existingQr) {
            $this->info("QR code già esistente:");
            $this->line("- Nome: {$existingQr->name}");
            $this->line("- Domanda: {$existingQr->question}");
            $this->line("- EAN: {$existingQr->ean_code}");
            $this->line("- URL: {$existingQr->getQrUrl()}");
            return 0;
        }

        // Genera nuovo QR code
        $baseRefCode = 'PROD-' . strtoupper(substr(md5($product->name . $product->id), 0, 8));
        $refCode = $baseRefCode;
        $counter = 1;

        while (QrCode::where('ref_code', $refCode)->exists()) {
            $refCode = $baseRefCode . '-' . $counter;
            $counter++;
        }

        $clientCode = $product->client ?? 'N/A';
        $qrName = $product->name . ' - ' . $clientCode;
        $question = "Come si cura " . $product->name . "?";

        $qrCode = QrCode::create([
            'store_id' => $product->store_id,
            'order_id' => $product->order_id ?? null,
            'product_id' => $product->id,
            'name' => $qrName,
            'question' => $question,
            'ref_code' => $refCode,
            'is_active' => true,
            'ean_code' => $product->ean,
        ]);

        $this->info("QR code creato con successo!");
        $this->line("- ID: {$qrCode->id}");
        $this->line("- Nome: {$qrCode->name}");
        $this->line("- Domanda: {$qrCode->question}");
        $this->line("- EAN: {$qrCode->ean_code}");
        $this->line("- Product ID: {$qrCode->product_id}");
        $this->line("- Ref Code: {$qrCode->ref_code}");
        $this->line("- URL: {$qrCode->getQrUrl()}");

        return 0;
    }
}
