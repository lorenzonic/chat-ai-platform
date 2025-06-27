<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

echo "Testing Admin Account Creation with Role...\n\n";

try {
    $testAdmin = Admin::create([
        'name' => 'Test Admin with Role',
        'email' => 'test-role-admin@example.com',
        'password' => Hash::make('password123'),
        'role' => 'super_admin',
    ]);

    echo "✓ Admin account created successfully!\n";
    echo "  - ID: {$testAdmin->id}\n";
    echo "  - Name: {$testAdmin->name}\n";
    echo "  - Email: {$testAdmin->email}\n";
    echo "  - Role: {$testAdmin->role}\n\n";

    // Cleanup
    $testAdmin->delete();
    echo "✓ Test admin cleaned up!\n";

} catch (Exception $e) {
    echo "✗ Admin account creation failed: " . $e->getMessage() . "\n\n";
}

echo "\nAdmin creation test completed!\n";
