<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== GROWERS DATABASE CHECK ===\n\n";

// Check for Bonini growers specifically
$boniniGrowersBP = App\Models\Grower::where('name', 'LIKE', '%Bonini Paolo%')->get();
$boniniGrowersFlor = App\Models\Grower::where('name', 'LIKE', '%Boniniflor%')->get();
$allBonini = App\Models\Grower::where('name', 'LIKE', '%Bonini%')->get();

if ($allBonini->isNotEmpty()) {
    echo "🌱 BONINI GROWERS FOUND:\n";
    echo str_repeat("-", 80) . "\n";
    echo sprintf("%-5s %-35s %-25s %-12s\n", "ID", "Name", "Email", "Password");
    echo str_repeat("-", 80) . "\n";
    
    foreach ($allBonini as $grower) {
        echo sprintf("%-5s %-35s %-25s %-12s\n",
            $grower->id,
            $grower->name ?: 'N/A',
            $grower->email ?: 'Not set',
            $grower->password ? 'Set' : 'Not set'
        );
    }
    echo "\n";
    
    // Show detailed credentials for each Bonini grower
    foreach ($allBonini as $grower) {
        echo "📋 GROWER ID {$grower->id} DETAILS:\n";
        echo "   Name: " . ($grower->name ?: 'N/A') . "\n";
        echo "   Company: " . ($grower->company_name ?: 'N/A') . "\n";
        echo "   Email: " . ($grower->email ?: 'NOT SET') . "\n";
        echo "   Password: " . ($grower->password ? 'SET (hash: ' . substr($grower->password, 0, 20) . '...)' : 'NOT SET') . "\n";
        echo "   Created: " . $grower->created_at->format('Y-m-d H:i:s') . "\n";
        
        // Check products
        $products = App\Models\Product::where('grower_id', $grower->id)->count();
        echo "   Products: $products\n";
        
        // Check order items
        $orderItems = App\Models\OrderItem::where('grower_id', $grower->id)->count();
        echo "   Order Items: $orderItems\n";
        echo "\n";
    }
} else {
    echo "❌ No Bonini growers found!\n\n";
    
    echo "📋 ALL GROWERS IN DATABASE:\n";
    echo str_repeat("-", 80) . "\n";
    echo sprintf("%-5s %-35s %-25s %-12s\n", "ID", "Name", "Email", "Password");
    echo str_repeat("-", 80) . "\n";
    
    $allGrowerss = App\Models\Grower::all();
    if ($allGrowerss->isEmpty()) {
        echo "No growers found in database.\n";
    } else {
        foreach ($allGrowerss as $grower) {
            echo sprintf("%-5s %-35s %-25s %-12s\n",
                $grower->id,
                $grower->name ?: 'N/A',
                $grower->email ?: 'Not set',
                $grower->password ? 'Set' : 'Not set'
            );
        }
    }
}
?>