<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AnalyticsController;

echo "Testing Analytics View Rendering...\n\n";

try {
    // Create a mock request
    $request = new Request([
        'store_id' => 'all',
        'start_date' => \Carbon\Carbon::now()->subDays(30)->format('Y-m-d'),
        'end_date' => \Carbon\Carbon::now()->format('Y-m-d')
    ]);

    // Create controller instance
    $controller = new AnalyticsController();

    // Call the index method
    $response = $controller->index($request);

    if ($response instanceof \Illuminate\View\View) {
        echo "✅ Analytics view created successfully!\n";

        $data = $response->getData();

        // Check if all required data is present
        $requiredKeys = ['stats', 'stores', 'chartsData', 'startDate', 'endDate'];
        $missingKeys = array_diff($requiredKeys, array_keys($data));

        if (empty($missingKeys)) {
            echo "✅ All required data keys present\n";

            // Check stats
            $stats = $data['stats'];
            echo "📊 Stats summary:\n";
            echo "  - Total stores: " . $stats['total_stores'] . "\n";
            echo "  - Total scans: " . $stats['total_scans'] . "\n";
            echo "  - Total chats: " . $stats['total_chats'] . "\n";
            echo "  - Total interactions: " . $stats['total_interactions'] . "\n";

            // Check charts data
            $chartsData = $data['chartsData'];
            echo "\n📈 Charts data:\n";
            foreach ($chartsData as $chartName => $chartData) {
                if (is_object($chartData) && method_exists($chartData, 'count')) {
                    echo "  - {$chartName}: " . $chartData->count() . " items\n";
                } else {
                    echo "  - {$chartName}: available\n";
                }
            }

            echo "\n✅ Analytics dashboard data structure is correct!\n";

        } else {
            echo "❌ Missing required data keys: " . implode(', ', $missingKeys) . "\n";
        }

    } else {
        echo "❌ Unexpected response type: " . get_class($response) . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";

    if (strpos($e->getMessage(), 'view') !== false) {
        echo "\n🔍 View-related error. Check Blade syntax.\n";
    }
}

echo "\nTest complete.\n";
