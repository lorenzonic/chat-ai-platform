<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test Analytics Route ===\n\n";

try {
    // Test se le route analytics esistono
    $routes = \Route::getRoutes();
    $analyticsRoutes = [];

    foreach ($routes as $route) {
        if (str_contains($route->getName() ?? '', 'analytics')) {
            $analyticsRoutes[] = [
                'name' => $route->getName(),
                'uri' => $route->uri(),
                'methods' => implode('|', $route->methods())
            ];
        }
    }

    echo "Analytics routes found:\n";
    foreach ($analyticsRoutes as $route) {
        echo "- {$route['name']}: {$route['methods']} {$route['uri']}\n";
    }

    echo "\n=== Test Database Connection ===\n";

    // Test database connection
    try {
        \DB::connection()->getPdo();
        echo "✅ Database connection OK\n";
        echo "Database: " . \DB::connection()->getDatabaseName() . "\n";

        // Test stores table
        $storeCount = \App\Models\Store::count();
        echo "Stores in database: $storeCount\n";

    } catch (Exception $e) {
        echo "❌ Database error: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
