<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\Store;
use App\Models\Grower;

echo "=== VERIFICATION OF IMPORTED DATA ===\n\n";

echo "Stores created:\n";
Store::where('client_code', '!=', null)->each(function($store) {
    echo "- {$store->name} (Code: {$store->client_code}, Active: " . ($store->is_account_active ? 'Yes' : 'No') . ")\n";
});

echo "\nGrowers created:\n";
Grower::all()->each(function($grower) {
    echo "- {$grower->name} (Code: {$grower->code})\n";
});

echo "\nProducts created:\n";
Product::with(['store', 'grower'])->get()->each(function($product) {
    echo "- {$product->name}\n";
    echo "  Store: {$product->store->name}\n";
    echo "  Grower: " . ($product->grower ? $product->grower->name : 'N/A') . "\n";
    echo "  Transport: {$product->transport}\n";
    echo "  Price: €" . ($product->price ?: 'N/A') . "\n";
    echo "  Product Code: {$product->code}\n";
    echo "  ---\n";
});

echo "\nImport verification completed!\n";
