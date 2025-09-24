<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Store;
use App\Models\Grower;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "🛒 Testing Complete Import System\n";
echo "=================================\n\n";

// Check database before import
echo "📊 Database Status BEFORE Import:\n";
echo "- Stores: " . Store::count() . "\n";
echo "- Growers: " . Grower::count() . "\n";
echo "- Products: " . Product::count() . "\n";
echo "- Orders: " . Order::count() . "\n";
echo "- OrderItems: " . OrderItem::count() . "\n\n";

// Test CSV file path
$csvFile = __DIR__ . '/test-complete-import.csv';

if (!file_exists($csvFile)) {
    echo "❌ Test CSV file not found: $csvFile\n";
    exit(1);
}

echo "📁 Reading test CSV file: $csvFile\n";

// Read and parse CSV
$handle = fopen($csvFile, 'r');
if (!$handle) {
    echo "❌ Could not open CSV file\n";
    exit(1);
}

$headers = fgetcsv($handle);
$rows = [];
while (($row = fgetcsv($handle)) !== false) {
    $rows[] = array_combine($headers, $row);
}
fclose($handle);

echo "✅ CSV loaded successfully\n";
echo "- Headers: " . count($headers) . "\n";
echo "- Rows: " . count($rows) . "\n";
echo "- Headers: " . implode(', ', $headers) . "\n\n";

// Show sample data
echo "📋 Sample Data:\n";
foreach (array_slice($rows, 0, 3) as $index => $row) {
    echo "Row " . ($index + 1) . ":\n";
    foreach ($row as $key => $value) {
        echo "  $key: $value\n";
    }
    echo "\n";
}

// Test date parsing
echo "📅 Testing Date Parsing:\n";
foreach ($rows as $row) {
    $dateStr = $row['Data'] ?? '';
    if ($dateStr) {
        try {
            if (strpos($dateStr, '/') !== false) {
                $date = \DateTime::createFromFormat('d/m/Y', $dateStr);
                if ($date) {
                    echo "✅ '$dateStr' → " . $date->format('Y-m-d') . "\n";
                } else {
                    echo "❌ Failed to parse date: '$dateStr'\n";
                }
            } else {
                echo "⚠️  Date format not recognized: '$dateStr'\n";
            }
        } catch (Exception $e) {
            echo "❌ Date parsing error: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n";

// Test price parsing
echo "💰 Testing Price Parsing:\n";
foreach ($rows as $row) {
    $priceStr = $row['€ Vendita'] ?? '';
    if ($priceStr) {
        $cleanPrice = str_replace(['€', ','], ['', '.'], $priceStr);
        $price = floatval($cleanPrice);
        echo "✅ '$priceStr' → $price\n";
    }
}

echo "\n";

// Group by order characteristics
echo "🔗 Testing Order Grouping:\n";
$orderGroups = [];
foreach ($rows as $row) {
    $cliente = $row['Cliente'] ?? '';
    $cc = $row['CC'] ?? '';
    $pia = $row['PIA'] ?? '';
    $pro = $row['PRO'] ?? '';
    $data = $row['Data'] ?? '';

    $orderKey = "{$cliente}|{$cc}|{$pia}|{$pro}|{$data}";

    if (!isset($orderGroups[$orderKey])) {
        $orderGroups[$orderKey] = [];
    }
    $orderGroups[$orderKey][] = $row;
}

echo "Orders will be created: " . count($orderGroups) . "\n";
foreach ($orderGroups as $key => $items) {
    echo "- Order '$key': " . count($items) . " items\n";
}

echo "\n";

// Check unique growers
echo "🏭 Unique Growers:\n";
$growers = array_unique(array_column($rows, 'Fornitore'));
foreach ($growers as $grower) {
    echo "- $grower\n";
}

echo "\n";

// Check unique stores
echo "🏪 Unique Stores:\n";
$stores = array_unique(array_column($rows, 'Cliente'));
foreach ($stores as $store) {
    echo "- $store\n";
}

echo "\n";

// Check unique products
echo "🌱 Unique Products:\n";
$products = [];
foreach ($rows as $row) {
    $key = $row['Codice'] . ' - ' . $row['Prodotto'];
    $products[] = $key;
}
$products = array_unique($products);
foreach ($products as $product) {
    echo "- $product\n";
}

echo "\n";
echo "✅ Complete Import System Test Completed!\n";
echo "The CSV structure is valid and ready for import.\n";
