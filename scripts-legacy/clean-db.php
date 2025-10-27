<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\Store;
use App\Models\Grower;

echo "Cleaning database for reimport...\n";

// Delete in proper order to respect foreign key constraints
Product::query()->delete();
Store::where('client_code', '!=', null)->delete();
Grower::query()->delete();

echo "Database cleaned successfully!\n";
