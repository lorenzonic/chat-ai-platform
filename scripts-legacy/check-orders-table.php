<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "=== ORDERS TABLE STRUCTURE ===\n";

    // Ottieni struttura tabella orders
    $columns = DB::select("DESCRIBE orders");

    echo "Columns found:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }

    // Verifica se grower_id esiste
    $hasGrowerColumn = collect($columns)->contains(function($col) {
        return $col->Field === 'grower_id';
    });

    echo "\nHas grower_id column: " . ($hasGrowerColumn ? "YES" : "NO") . "\n";

    if (!$hasGrowerColumn) {
        echo "\n❌ grower_id column is MISSING!\n";
        echo "Need to create migration to add it.\n";
    } else {
        echo "\n✅ grower_id column exists!\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
