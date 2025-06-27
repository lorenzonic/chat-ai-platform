<?php

/**
 * Test Account Creation Script
 * This script tests the creation of new admin and store accounts
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin;
use App\Models\Store;
use Illuminate\Support\Facades\Hash;

echo "=== ACCOUNT MANAGEMENT TEST ===\n\n";

// Test creating a new store account
echo "Testing Store Account Creation...\n";
try {
    $testStore = Store::create([
        'name' => 'Test Store via Script',
        'email' => 'teststore@example.com',
        'password' => Hash::make('password123'),
        'slug' => 'test-store-script',
        'description' => 'A test store created via script',
        'website' => 'https://teststore.example.com',
        'phone' => '+1234567890',
        'address' => '123 Test Street',
        'city' => 'Test City',
        'country' => 'Test Country',
        'is_active' => true,
        'is_premium' => false,
    ]);

    echo "✓ Store account created successfully!\n";
    echo "  - ID: {$testStore->id}\n";
    echo "  - Name: {$testStore->name}\n";
    echo "  - Slug: {$testStore->slug}\n";
    echo "  - Email: {$testStore->email}\n\n";

} catch (Exception $e) {
    echo "✗ Store account creation failed: " . $e->getMessage() . "\n\n";
}

// Test creating a new admin account
echo "Testing Admin Account Creation...\n";
try {
    $testAdmin = Admin::create([
        'name' => 'Test Admin via Script',
        'email' => 'testadmin@example.com',
        'password' => Hash::make('password123'),
        'role' => 'admin',
    ]);

    echo "✓ Admin account created successfully!\n";
    echo "  - ID: {$testAdmin->id}\n";
    echo "  - Name: {$testAdmin->name}\n";
    echo "  - Email: {$testAdmin->email}\n";
    echo "  - Role: {$testAdmin->role}\n\n";

} catch (Exception $e) {
    echo "✗ Admin account creation failed: " . $e->getMessage() . "\n\n";
}

// Display current counts
echo "=== CURRENT ACCOUNT COUNTS ===\n";
echo "Stores: " . Store::count() . "\n";
echo "Admins: " . Admin::count() . "\n";
echo "Active Stores: " . Store::where('is_active', true)->count() . "\n";
echo "Premium Stores: " . Store::where('is_premium', true)->count() . "\n";

echo "\nTest completed!\n";
echo "\nYou can now test the admin panel at: http://localhost:8000/admin/login\n";
echo "Or use the following URLs:\n";
echo "- Admin Dashboard: http://localhost:8000/admin/dashboard\n";
echo "- Manage Accounts: http://localhost:8000/admin/accounts\n";
echo "- Create Store: http://localhost:8000/admin/accounts/stores/create\n";
echo "- Create Admin: http://localhost:8000/admin/accounts/admins/create\n\n";

// Cleanup test accounts (optional)
$cleanup = false; // Set to true if you want to clean up test accounts
if ($cleanup) {
    echo "Cleaning up test accounts...\n";
    Store::where('email', 'teststore@example.com')->delete();
    Admin::where('email', 'testadmin@example.com')->delete();
    echo "✓ Test accounts cleaned up!\n";
}
