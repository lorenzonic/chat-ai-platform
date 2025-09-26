<?php
// Minimal grower dashboard test to isolate the 500 error
header('Content-Type: text/plain');

try {
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    echo "🔍 Minimal Dashboard Test\n";
    echo "=========================\n\n";
    
    // Test 1: Simulate the exact route request
    $request = Request::create('/grower/dashboard', 'GET');
    
    // Test 2: Check authentication guard
    echo "Testing auth guard...\n";
    $grower = Auth::guard('grower')->user();
    echo "Current grower: " . ($grower ? $grower->name : 'none') . "\n";
    
    // Test 3: Get a test grower and login
    $testGrover = App\Models\Grower::first();
    if ($testGrover) {
        Auth::guard('grower')->login($testGrover);
        echo "✅ Test grower logged in: {$testGrover->name}\n";
    } else {
        echo "❌ No test grower found\n";
        exit;
    }
    
    // Test 4: Try to instantiate controller with auth
    try {
        $controller = new App\Http\Controllers\Grower\DashboardController();
        echo "✅ Controller instantiated\n";
        
        // Test 5: Try calling the index method directly
        $response = $controller->index();
        echo "✅ Controller->index() called successfully\n";
        echo "Response type: " . get_class($response) . "\n";
        
    } catch (Exception $e) {
        echo "❌ Controller error: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "❌ PHP Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>