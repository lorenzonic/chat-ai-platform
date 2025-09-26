<?php
// Railway-compatible debug test
try {
    // Try to include Laravel's autoloader
    if (file_exists('../vendor/autoload.php')) {
        require_once '../vendor/autoload.php';
    } else {
        echo "<p>❌ Autoloader not found</p>\n";
        exit;
    }
    
    echo "<h1>Railway Production Debug Test</h1>\n";
    echo "<p>✅ Autoloader loaded</p>\n";
    
    // Railway uses environment variables directly, not .env files
    echo "<h2>Environment Variables</h2>\n";
    
    // Get database config from environment
    $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');
    $dbname = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE');
    $username = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME');
    $password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD');
    
    echo "<p>DB_HOST: " . ($host ? '✅ Set' : '❌ Not set') . "</p>\n";
    echo "<p>DB_DATABASE: " . ($dbname ? '✅ Set' : '❌ Not set') . "</p>\n";
    echo "<p>DB_USERNAME: " . ($username ? '✅ Set' : '❌ Not set') . "</p>\n";
    echo "<p>DB_PASSWORD: " . ($password ? '✅ Set' : '❌ Not set') . "</p>\n";
    
    // If DATABASE_URL is set (Railway style), parse it
    if (isset($_ENV['DATABASE_URL']) || getenv('DATABASE_URL')) {
        $databaseUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');
        echo "<p>DATABASE_URL: ✅ Set</p>\n";
        
        $parsed = parse_url($databaseUrl);
        if ($parsed) {
            $host = $parsed['host'] ?? 'localhost';
            $dbname = ltrim($parsed['path'] ?? '', '/');
            $username = $parsed['user'] ?? '';
            $password = $parsed['pass'] ?? '';
            
            echo "<p>Parsed - Host: $host, DB: $dbname</p>\n";
        }
    }
    
    // Test database connection
    if (class_exists('PDO') && $host && $dbname) {
        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            echo "<p>✅ Database connection OK</p>\n";
            
            // Test grower query
            $stmt = $pdo->prepare("SELECT id, name, email FROM growers WHERE id = 34 LIMIT 1");
            $stmt->execute();
            $grower = $stmt->fetch();
            
            if ($grower) {
                echo "<p>✅ Grower 34 found: " . htmlspecialchars($grower['name'] ?? 'No name') . "</p>\n";
                
                // Test products count
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE grower_id = 34");
                $stmt->execute();
                $result = $stmt->fetch();
                echo "<p>✅ Products count: " . $result['count'] . "</p>\n";
                
                // Test order_items exists
                $stmt = $pdo->prepare("SHOW TABLES LIKE 'order_items'");
                $stmt->execute();
                $tableExists = $stmt->fetch();
                echo "<p>order_items table: " . ($tableExists ? '✅ Exists' : '❌ Missing') . "</p>\n";
                
                if ($tableExists) {
                    // Test orders count (using orderItems.product relationship)
                    $stmt = $pdo->prepare("
                        SELECT COUNT(DISTINCT orders.id) as count 
                        FROM orders 
                        INNER JOIN order_items ON orders.id = order_items.order_id 
                        INNER JOIN products ON order_items.product_id = products.id 
                        WHERE products.grower_id = 34
                    ");
                    $stmt->execute();
                    $result = $stmt->fetch();
                    echo "<p>✅ Orders count: " . $result['count'] . "</p>\n";
                    
                    // Test if quantity column exists and works
                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE grower_id = 34 AND quantity <= 10 AND quantity > 0");
                    $stmt->execute();
                    $result = $stmt->fetch();
                    echo "<p>✅ Low stock products: " . $result['count'] . "</p>\n";
                }
                
            } else {
                echo "<p>❌ Grower 34 not found in database</p>\n";
            }
            
        } catch (PDOException $e) {
            echo "<p>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
        }
        
    } else {
        echo "<p>❌ Cannot connect - missing database config</p>\n";
        if (!class_exists('PDO')) echo "<p>❌ PDO not available</p>\n";
        if (!$host) echo "<p>❌ Host not configured</p>\n";
        if (!$dbname) echo "<p>❌ Database name not configured</p>\n";
    }
    
    // Show environment info
    echo "<h2>System Info</h2>\n";
    echo "<p>PHP Version: " . PHP_VERSION . "</p>\n";
    echo "<p>Working Directory: " . getcwd() . "</p>\n";
    echo "<p>File exists - bootstrap/app.php: " . (file_exists('../bootstrap/app.php') ? '✅ Yes' : '❌ No') . "</p>\n";
    echo "<p>File exists - vendor/autoload.php: " . (file_exists('../vendor/autoload.php') ? '✅ Yes' : '❌ No') . "</p>\n";
    echo "<p>File exists - .env: " . (file_exists('../.env') ? '✅ Yes' : '❌ No (Normal for Railway)') . "</p>\n";
    
    if (isset($_SERVER['SERVER_SOFTWARE'])) {
        echo "<p>Web Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>\n";
    }
    
    // Show some environment variables
    echo "<h2>Key Environment Variables</h2>\n";
    $envVars = ['APP_ENV', 'APP_DEBUG', 'APP_URL', 'DB_CONNECTION', 'RAILWAY_ENVIRONMENT'];
    foreach ($envVars as $var) {
        $value = $_ENV[$var] ?? getenv($var);
        echo "<p>$var: " . ($value ? htmlspecialchars($value) : 'Not set') . "</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p>❌ General error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "<p>File: " . htmlspecialchars($e->getFile()) . " Line: " . $e->getLine() . "</p>\n";
}

echo "<p>Test completed: " . date('Y-m-d H:i:s') . "</p>\n";
?>