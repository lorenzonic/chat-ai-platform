<?php

require_once 'vendor/autoload.php';

// Boot Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Database Schema Investigation ===\n\n";

try {
    echo "📋 Checking growers table columns...\n";
    $columns = Schema::getColumnListing('growers');
    echo "Found " . count($columns) . " columns:\n";
    foreach ($columns as $column) {
        echo "   - $column\n";
    }

    echo "\n📊 Sample grower records...\n";
    $growers = DB::table('growers')->limit(3)->get();
    if ($growers->count() > 0) {
        $firstGrower = $growers->first();
        echo "Sample columns in actual data:\n";
        foreach ($firstGrower as $key => $value) {
            echo "   - $key: " . (is_string($value) ? substr($value, 0, 50) : $value) . "\n";
        }
    } else {
        echo "No grower records found.\n";
    }

    echo "\n📋 Checking stores table columns...\n";
    $storeColumns = Schema::getColumnListing('stores');
    echo "Found " . count($storeColumns) . " columns:\n";
    foreach ($storeColumns as $column) {
        echo "   - $column\n";
    }

    echo "\n📋 Checking products table columns...\n";
    $productColumns = Schema::getColumnListing('products');
    echo "Found " . count($productColumns) . " columns:\n";
    foreach ($productColumns as $column) {
        echo "   - $column\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n✅ Schema investigation complete!\n";
