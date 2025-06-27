<?php

// Test script to verify account management functionality
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing account management functionality...\n\n";

// Test Store model
try {
    $storeCount = \App\Models\Store::count();
    echo "✓ Store model working - Found {$storeCount} stores\n";
} catch (Exception $e) {
    echo "✗ Store model error: " . $e->getMessage() . "\n";
}

// Test Admin model
try {
    $adminCount = \App\Models\Admin::count();
    echo "✓ Admin model working - Found {$adminCount} admins\n";
} catch (Exception $e) {
    echo "✗ Admin model error: " . $e->getMessage() . "\n";
}

// Test QrCode model
try {
    $qrCount = \App\Models\QrCode::count();
    echo "✓ QrCode model working - Found {$qrCount} QR codes\n";
} catch (Exception $e) {
    echo "✗ QrCode model error: " . $e->getMessage() . "\n";
}

// Test AccountController instantiation
try {
    $controller = new \App\Http\Controllers\Admin\AccountController();
    echo "✓ AccountController instantiation successful\n";
} catch (Exception $e) {
    echo "✗ AccountController error: " . $e->getMessage() . "\n";
}

echo "\nTest completed!\n";
