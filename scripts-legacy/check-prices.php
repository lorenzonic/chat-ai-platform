<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

echo "Sample product prices:\n";
Product::take(5)->get(['name', 'price'])->each(function($p) {
    echo "- {$p->name}: " . ($p->price ?? 'NULL') . "\n";
});

echo "\nChecking CSV mapping for 'e_vendita' field...\n";
