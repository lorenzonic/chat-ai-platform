<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

echo "Prodotti con Prezzemolo:\n";
$products = Product::where('name', 'like', '%prezzemolo%')->get(['id', 'name', 'ean']);

foreach ($products as $product) {
    echo "ID: {$product->id} - Name: {$product->name} - EAN: {$product->ean}\n";
}

echo "\nTutti i prodotti con P14:\n";
$productsP14 = Product::where('name', 'like', '%P14%')->get(['id', 'name', 'ean']);

foreach ($productsP14 as $product) {
    echo "ID: {$product->id} - Name: {$product->name} - EAN: {$product->ean}\n";
}
