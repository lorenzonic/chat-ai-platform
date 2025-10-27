<?php

// Test rapido per verificare la relazione Store->products()
use App\Models\Store;
use App\Models\Product;

echo "Testing Store->products() relationship...\n";

try {
    // Test 1: Caricare un Store e controllare se ha products()
    $store = Store::first();
    if ($store) {
        echo "Store trovato: {$store->name}\n";

        $products = $store->products()->limit(5)->get();
        echo "Prodotti del store: " . $products->count() . "\n";

        foreach ($products as $product) {
            echo "- {$product->name} (ID: {$product->id})\n";
        }
    }

    // Test 2: WhereHas test (quello che falliva nel controller)
    echo "\nTesting whereHas('products')...\n";
    $storesWithProducts = Store::whereHas('products')->limit(3)->get();
    echo "Store con prodotti: " . $storesWithProducts->count() . "\n";

    foreach ($storesWithProducts as $store) {
        echo "- {$store->name} ha " . $store->products()->count() . " prodotti\n";
    }

    echo "\n✅ Test completato con successo!\n";

} catch (Exception $e) {
    echo "\n❌ Errore: " . $e->getMessage() . "\n";
    echo "Linea: " . $e->getLine() . "\n";
    echo "File: " . $e->getFile() . "\n";
}
