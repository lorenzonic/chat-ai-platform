<?php

require_once 'vendor/autoload.php';

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Grower;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "=== Grower Credentials Creator ===\n\n";

try {
    // First, let's check if grower ID 34 exists
    $grower = Grower::find(34);

    if (!$grower) {
        echo "❌ Grower with ID 34 not found!\n";
        echo "Let me show you available growers:\n\n";

        $growers = Grower::orderBy('id')->get();

        if ($growers->isEmpty()) {
            echo "No growers found in database.\n";
        } else {
            echo "Available Growers:\n";
            echo str_repeat("-", 80) . "\n";
            echo sprintf("%-5s %-30s %-30s %-20s\n", "ID", "Name", "Company", "Email");
            echo str_repeat("-", 80) . "\n";

            foreach ($growers as $g) {
                echo sprintf("%-5s %-30s %-30s %-20s\n",
                    $g->id,
                    $g->name ?: 'N/A',
                    $g->company_name ?: 'N/A',
                    $g->email ?: 'N/A'
                );
            }
        }
        exit(1);
    }

    echo "✅ Found Grower ID 34:\n";
    echo "   Name: " . ($grower->name ?: 'N/A') . "\n";
    echo "   Company: " . ($grower->company_name ?: 'N/A') . "\n";
    echo "   Email: " . ($grower->email ?: 'N/A') . "\n";
    echo "   Current Password Set: " . ($grower->password ? 'Yes' : 'No') . "\n\n";

    // Create or update credentials
    $email = $grower->email ?: 'grower34@example.com';
    $password = 'password123'; // Default password

    // If no email exists, set one
    if (!$grower->email) {
        echo "📧 Setting email to: $email\n";
        $grower->email = $email;
    }

    // Set password
    echo "🔑 Setting password to: $password\n";
    $grower->password = Hash::make($password);

    // Save the grower
    $grower->save();

    echo "\n✅ Credentials created successfully!\n\n";
    echo "=== LOGIN CREDENTIALS ===\n";
    echo "Email: " . $grower->email . "\n";
    echo "Password: $password\n";
    echo "Login URL: http://localhost:8000/grower/login\n\n";

    echo "=== Grower Details ===\n";
    echo "ID: " . $grower->id . "\n";
    echo "Name: " . ($grower->name ?: 'Not set') . "\n";
    echo "Company: " . ($grower->company_name ?: 'Not set') . "\n";
    echo "Email: " . $grower->email . "\n";
    echo "Created: " . $grower->created_at->format('Y-m-d H:i:s') . "\n";
    echo "Updated: " . $grower->updated_at->format('Y-m-d H:i:s') . "\n";

    // Check if grower has products
    $productsCount = DB::table('products')->where('grower_id', 34)->count();
    echo "Products: $productsCount\n";

    // Check if grower has order items
    $orderItemsCount = DB::table('order_items')->where('grower_id', 34)->count();
    echo "Order Items: $orderItemsCount\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
