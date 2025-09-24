<?php

require_once 'vendor/autoload.php';

// Setup Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Grower;
use Illuminate\Support\Facades\Hash;

echo "Testing Grower Authentication System\n";
echo "=====================================\n\n";

try {
    // Test 1: Create a test grower or find existing one
    echo "1. Getting test grower...\n";

    $grower = Grower::where('email', 'test@grower.com')->first();

    if (!$grower) {
        $grower = Grower::create([
            'name' => 'Test Grower Company ' . time(),
            'code' => 'TEST' . time(),
            'email' => 'test@grower.com',
            'password' => Hash::make('password123'),
            'phone' => '+39 123 456 7890',
            'address' => 'Via delle Piante 123',
            'city' => 'Milan',
            'country' => 'Italy',
            'notes' => 'Test grower account created for testing',
            'is_active' => true,
        ]);
        echo "✓ New test grower created with ID: {$grower->id}\n";
    } else {
        echo "✓ Using existing test grower with ID: {$grower->id}\n";
    }

    echo "✓ Name: {$grower->name}\n";
    echo "✓ Email: {$grower->email}\n";
    echo "✓ Code: {$grower->code}\n\n";

    // Test 2: Check password hashing
    echo "2. Testing password authentication...\n";

    if (Hash::check('password123', $grower->password)) {
        echo "✓ Password hash verification works correctly\n\n";
    } else {
        echo "✗ Password hash verification failed\n\n";
    }

    // Test 3: Test authentication guard
    echo "3. Testing authentication configuration...\n";

    $guardConfig = config('auth.guards.grower');
    if ($guardConfig) {
        echo "✓ Grower guard is configured\n";
        echo "  Driver: {$guardConfig['driver']}\n";
        echo "  Provider: {$guardConfig['provider']}\n\n";
    } else {
        echo "✗ Grower guard not found in configuration\n\n";
    }

    // Test 4: Test provider configuration
    $providerConfig = config('auth.providers.growers');
    if ($providerConfig) {
        echo "✓ Grower provider is configured\n";
        echo "  Driver: {$providerConfig['driver']}\n";
        echo "  Model: {$providerConfig['model']}\n\n";
    } else {
        echo "✗ Grower provider not found in configuration\n\n";
    }

    // Test 5: Test routes
    echo "4. Testing routes...\n";

    $routes = [
        'grower.login' => 'Login page',
        'grower.dashboard' => 'Dashboard',
        'admin.accounts.growers.create' => 'Admin create grower',
        'admin.accounts.growers.show' => 'Admin show grower',
        'admin.accounts.growers.edit' => 'Admin edit grower',
    ];

    foreach ($routes as $routeName => $description) {
        try {
            $route = route($routeName, $routeName === 'admin.accounts.growers.show' || $routeName === 'admin.accounts.growers.edit' ? $grower->id : []);
            echo "✓ {$description}: {$route}\n";
        } catch (Exception $e) {
            echo "✗ {$description}: Route not found\n";
        }
    }

    echo "\n5. Testing file existence...\n";

    $files = [
        'app/Models/Grower.php' => 'Grower model',
        'app/Http/Controllers/Grower/AuthController.php' => 'Grower auth controller',
        'app/Http/Controllers/Grower/DashboardController.php' => 'Grower dashboard controller',
        'app/Http/Middleware/GrowerAuth.php' => 'Grower auth middleware',
        'resources/views/grower/login.blade.php' => 'Grower login view',
        'resources/views/grower/dashboard.blade.php' => 'Grower dashboard view',
        'resources/views/admin/accounts/show-grower.blade.php' => 'Admin grower detail view',
        'resources/views/admin/accounts/create-grower.blade.php' => 'Admin create grower view',
        'resources/views/admin/accounts/edit-grower.blade.php' => 'Admin edit grower view',
    ];

    foreach ($files as $file => $description) {
        if (file_exists($file)) {
            echo "✓ {$description}: Found\n";
        } else {
            echo "✗ {$description}: Missing\n";
        }
    }

    echo "\n6. Database structure check...\n";

    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('growers');
    $requiredColumns = ['id', 'name', 'email', 'password', 'code', 'phone', 'address', 'city', 'country', 'notes', 'is_active', 'created_at', 'updated_at'];

    foreach ($requiredColumns as $column) {
        if (in_array($column, $columns)) {
            echo "✓ Column '{$column}': Present\n";
        } else {
            echo "✗ Column '{$column}': Missing\n";
        }
    }

    echo "\nTest completed successfully!\n";
    echo "Grower ID for further testing: {$grower->id}\n";
    echo "Login credentials: test@grower.com / password123\n";

} catch (Exception $e) {
    echo "Error during testing: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
