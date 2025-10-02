<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 CONTROLLO STRUTTURA order_items\n";
echo str_repeat("=", 50) . "\n\n";

// Check table structure
$columns = DB::select("DESCRIBE order_items");

echo "📋 Colonne nella tabella order_items:\n";
foreach ($columns as $column) {
    echo "   - {$column->Field} ({$column->Type}) " . ($column->Null === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
}

echo "\n📊 Sample data (primi 3 record):\n";
$samples = DB::table('order_items')->limit(3)->get();

if ($samples->count() > 0) {
    foreach ($samples as $item) {
        echo "   ID {$item->id}:\n";
        foreach ((array)$item as $field => $value) {
            if (strpos($field, 'price') !== false || strpos($field, 'prezzo') !== false || $field === 'unit_price') {
                echo "     {$field}: {$value}\n";
            }
        }
        echo "\n";
    }
} else {
    echo "   Nessun record trovato\n";
}

echo "🔧 Test query con diversi nomi colonna:\n";

// Test different price column names
$possiblePriceColumns = ['price', 'unit_price', 'prezzo_rivendita', 'prezzo', 'total_price'];

foreach ($possiblePriceColumns as $priceCol) {
    try {
        $result = DB::table('order_items')->selectRaw("SUM(quantity * {$priceCol}) as total")->first();
        echo "   ✅ {$priceCol}: {$result->total}\n";
    } catch (Exception $e) {
        echo "   ❌ {$priceCol}: Colonna non trovata\n";
    }
}
?>
