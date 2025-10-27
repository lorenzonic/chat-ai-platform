<?php

require_once 'vendor/autoload.php';

use App\Models\Store;
use Illuminate\Support\Str;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Create Test Store Account ===\n\n";

// Check if test store exists
$testStore = Store::where('email', 'test@store.com')->first();

if ($testStore) {
    echo "Test store already exists:\n";
    echo "Email: {$testStore->email}\n";
    echo "Name: {$testStore->name}\n";
    echo "Slug: {$testStore->slug}\n";
    echo "Active: " . ($testStore->is_active ? 'Yes' : 'No') . "\n";
    echo "Account Active: " . ($testStore->is_account_active ? 'Yes' : 'No') . "\n";

    // Enable the account if it's disabled
    if (!$testStore->is_account_active) {
        $testStore->is_account_active = true;
        $testStore->save();
        echo "\n✅ Account activated!\n";
    }
} else {
    // Create test store
    echo "Creating test store account...\n";

    $testStore = Store::create([
        'name' => 'Store Test',
        'slug' => 'store-test-' . time(),
        'email' => 'test@store.com',
        'password' => bcrypt('password123'),
        'client_code' => 'TEST_STORE_' . time(),
        'description' => 'Account di test per il sistema store',
        'is_active' => true,
        'is_account_active' => true, // Enable account immediately for testing
    ]);

    echo "✅ Test store created successfully!\n";
    echo "Email: {$testStore->email}\n";
    echo "Name: {$testStore->name}\n";
    echo "Slug: {$testStore->slug}\n";
}

echo "\n=== Login Credentials ===\n";
echo "URL: http://localhost:8000/store/login\n";
echo "Email: test@store.com\n";
echo "Password: password123\n";

echo "\n=== Store Dashboard URL ===\n";
echo "URL: http://localhost:8000/store/dashboard\n";

echo "\n=== Public Chatbot URL ===\n";
echo "URL: http://localhost:8000/{$testStore->slug}\n";

echo "\n✅ Test store ready for login testing!\n";
