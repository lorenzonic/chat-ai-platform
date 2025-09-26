<?php
// Test the exact grower dashboard route handling
header('Content-Type: text/plain');

try {
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    echo "🔍 Route Handler Test\n";
    echo "====================\n\n";

    // Test 1: Check if route exists
    $routes = Route::getRoutes();
    $dashboardRoute = $routes->getByName('grower.dashboard');
    if ($dashboardRoute) {
        echo "✅ Route 'grower.dashboard' exists\n";
        echo "URI: " . $dashboardRoute->uri() . "\n";
        echo "Methods: " . implode(',', $dashboardRoute->methods()) . "\n";
        echo "Middleware: " . implode(',', $dashboardRoute->middleware()) . "\n\n";
    } else {
        echo "❌ Route 'grower.dashboard' not found\n";
        exit;
    }

    // Test 2: Login a grower first
    $grower = App\Models\Grower::first();
    if ($grower) {
        Auth::guard('grower')->login($grower);
        echo "✅ Grower logged in: {$grower->name}\n";
    }

    // Test 3: Create request and handle it
    $request = Request::create('/grower/dashboard', 'GET');
    $request->setLaravelSession($app['session']->driver());

    try {
        echo "Handling request...\n";
        $response = $kernel->handle($request);
        echo "✅ Request handled successfully\n";
        echo "Status: " . $response->getStatusCode() . "\n";
        echo "Response type: " . get_class($response) . "\n";
    } catch (Exception $e) {
        echo "❌ Request handling failed: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }

} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
