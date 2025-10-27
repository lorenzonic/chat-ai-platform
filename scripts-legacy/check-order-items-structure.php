<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "order_items table structure:\n";
$columns = DB::select('DESCRIBE order_items');
foreach ($columns as $col) {
    echo $col->Field . ' (' . $col->Type . ')' . "\n";
}

echo "\nLatest OrderItems (first 3):\n";
$items = App\Models\OrderItem::latest()->take(3)->get();
foreach ($items as $item) {
    echo "ID: {$item->id}, Order: {$item->order_id}, Created: {$item->created_at}\n";
    echo "  Attributes: " . json_encode($item->getAttributes()) . "\n";
}
?>