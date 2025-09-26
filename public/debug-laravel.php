<?php
// Direct Laravel test - production safe
$basePath = realpath(__DIR__ . '/..');

// Check if we can create a basic Laravel app instance
if (file_exists($basePath . '/vendor/autoload.php')) {
    require_once $basePath . '/vendor/autoload.php';
    
    echo "<h1>Direct Laravel Dashboard Test</h1>\n";
    
    try {
        // Set up basic Laravel environment
        $app = new Illuminate\Foundation\Application($basePath);
        
        // Bind the kernel
        $app->singleton(
            Illuminate\Contracts\Http\Kernel::class,
            App\Http\Kernel::class
        );
        
        $app->singleton(
            Illuminate\Contracts\Console\Kernel::class,
            App\Console\Kernel::class
        );
        
        $app->singleton(
            Illuminate\Contracts\Debug\ExceptionHandler::class,
            App\Exceptions\Handler::class
        );
        
        // Bootstrap the application
        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        
        echo "<p>✅ Laravel app created</p>\n";
        
        // Simulate the grower dashboard request
        $request = Illuminate\Http\Request::create('/grower/dashboard', 'GET');
        
        // Try to handle the request
        $response = $kernel->handle($request);
        
        echo "<p>✅ Request handled</p>\n";
        echo "<p>Status Code: " . $response->getStatusCode() . "</p>\n";
        echo "<p>Content Length: " . strlen($response->getContent()) . "</p>\n";
        
        if ($response->getStatusCode() === 200) {
            echo "<p>✅ Dashboard working correctly!</p>\n";
        } else {
            echo "<p>❌ Dashboard returned status: " . $response->getStatusCode() . "</p>\n";
            if ($response->getStatusCode() === 500) {
                echo "<p>Content preview:</p>\n";
                echo "<pre>" . htmlspecialchars(substr($response->getContent(), 0, 1000)) . "</pre>\n";
            }
        }
        
        $kernel->terminate($request, $response);
        
    } catch (Exception $e) {
        echo "<p>❌ Laravel setup error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
        echo "<p>File: " . htmlspecialchars($e->getFile()) . " Line: " . $e->getLine() . "</p>\n";
        
        // Try fallback approach - direct database test
        echo "<h2>Fallback Database Test</h2>\n";
        echo "<p>Attempting direct database connection...</p>\n";
        
        // Try to read environment variables
        if (file_exists($basePath . '/.env')) {
            $envFile = file_get_contents($basePath . '/.env');
            if (preg_match('/DB_HOST=(.+)/', $envFile, $matches)) {
                $host = trim($matches[1]);
                echo "<p>DB Host from .env: $host</p>\n";
            }
        }
    }
    
} else {
    echo "<h1>Autoloader Not Found</h1>\n";
    echo "<p>❌ Cannot find vendor/autoload.php</p>\n";
    echo "<p>Base path: $basePath</p>\n";
    echo "<p>Looking for: " . $basePath . '/vendor/autoload.php' . "</p>\n";
}

echo "<p>Test completed: " . date('Y-m-d H:i:s') . "</p>\n";
?>