<?php
// Simple web test to diagnose the 500 error
header('Content-Type: text/plain');

try {
    echo "🔍 Web Diagnostic Test\n";
    echo "=====================\n\n";

    // Test 1: Basic PHP
    echo "✅ PHP is working\n";
    echo "PHP Version: " . phpversion() . "\n\n";

    // Test 2: Check if Laravel files exist
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        echo "✅ Composer autoload exists\n";
    } else {
        echo "❌ Composer autoload missing\n";
        exit;
    }

    if (file_exists(__DIR__ . '/../bootstrap/app.php')) {
        echo "✅ Laravel bootstrap exists\n";
    } else {
        echo "❌ Laravel bootstrap missing\n";
        exit;
    }

    // Test 3: Try to load Laravel
    try {
        require_once __DIR__ . '/../vendor/autoload.php';
        echo "✅ Composer autoload loaded\n";

        $app = require_once __DIR__ . '/../bootstrap/app.php';
        echo "✅ Laravel app created\n";

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        echo "✅ Laravel bootstrapped\n";

    } catch (Exception $e) {
        echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
        exit;
    }

    // Test 4: Database connection
    try {
        DB::connection()->getPdo();
        echo "✅ Database connected\n";
    } catch (Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    }

    // Test 5: Check environment
    echo "\n🌍 Environment Info:\n";
    echo "APP_ENV: " . config('app.env', 'not set') . "\n";
    echo "APP_DEBUG: " . (config('app.debug') ? 'true' : 'false') . "\n";
    echo "DB_CONNECTION: " . config('database.default', 'not set') . "\n";

    // Test 6: Check models
    try {
        $growerCount = App\Models\Grower::count();
        echo "✅ Found {$growerCount} growers\n";
    } catch (Exception $e) {
        echo "❌ Grower model error: " . $e->getMessage() . "\n";
    }

    // Test 7: Check auth config
    try {
        $guards = config('auth.guards');
        if (isset($guards['grower'])) {
            echo "✅ Grower guard configured\n";
        } else {
            echo "❌ Grower guard missing\n";
        }
    } catch (Exception $e) {
        echo "❌ Auth config error: " . $e->getMessage() . "\n";
    }

    echo "\n🎯 Diagnostic complete!\n";

} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "❌ PHP Error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
