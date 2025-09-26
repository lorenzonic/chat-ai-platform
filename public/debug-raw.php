<?php
// Direct database test without any Laravel dependencies
echo "<h1>Raw Database Connection Test</h1>\n";

try {
    // Railway database connection
    $databaseUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');

    if (!$databaseUrl) {
        echo "<p>❌ DATABASE_URL environment variable not found</p>\n";

        // Show all environment variables that start with DB
        echo "<h2>Available Environment Variables:</h2>\n";
        foreach ($_ENV as $key => $value) {
            if (strpos($key, 'DB') === 0 || strpos($key, 'DATABASE') === 0) {
                echo "<p>$key: " . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "</p>\n";
            }
        }

        // Also check getenv
        $envVars = ['DATABASE_URL', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
        echo "<h2>Checking getenv():</h2>\n";
        foreach ($envVars as $var) {
            $value = getenv($var);
            echo "<p>$var: " . ($value ? 'SET (' . strlen($value) . ' chars)' : 'NOT SET') . "</p>\n";
        }

        exit;
    }

    echo "<p>✅ DATABASE_URL found</p>\n";

    $parsed = parse_url($databaseUrl);
    if (!$parsed) {
        echo "<p>❌ Failed to parse DATABASE_URL</p>\n";
        exit;
    }

    $host = $parsed['host'] ?? '';
    $port = $parsed['port'] ?? 3306;
    $dbname = ltrim($parsed['path'] ?? '', '/');
    $username = $parsed['user'] ?? '';
    $password = $parsed['pass'] ?? '';

    echo "<p>Connecting to: $host:$port/$dbname as $username</p>\n";

    // Test PDO connection
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 30,
    ];

    $pdo = new PDO($dsn, $username, $password, $options);
    echo "<p>✅ Database connection successful!</p>\n";

    // Test basic query
    $stmt = $pdo->query("SELECT DATABASE() as current_db, NOW() as server_time");
    $result = $stmt->fetch();
    echo "<p>Connected to database: " . $result['current_db'] . "</p>\n";
    echo "<p>Server time: " . $result['server_time'] . "</p>\n";

    // Check if our tables exist
    $tables = ['growers', 'products', 'orders', 'order_items'];
    echo "<h2>Table Check:</h2>\n";

    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table LIMIT 1");
            $result = $stmt->fetch();
            echo "<p>✅ Table '$table': " . $result['count'] . " records</p>\n";
        } catch (Exception $e) {
            echo "<p>❌ Table '$table': " . $e->getMessage() . "</p>\n";
        }
    }

    // Check grower 34 specifically
    echo "<h2>Grower 34 Check:</h2>\n";

    $stmt = $pdo->prepare("SELECT id, name, email, created_at FROM growers WHERE id = 34");
    $stmt->execute();
    $grower = $stmt->fetch();

    if ($grower) {
        echo "<p>✅ Grower 34 exists:</p>\n";
        echo "<ul>\n";
        echo "<li>Name: " . htmlspecialchars($grower['name']) . "</li>\n";
        echo "<li>Email: " . htmlspecialchars($grower['email']) . "</li>\n";
        echo "<li>Created: " . $grower['created_at'] . "</li>\n";
        echo "</ul>\n";

        // Check grower's products
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE grower_id = 34");
        $stmt->execute();
        $result = $stmt->fetch();
        echo "<p>✅ Products for grower 34: " . $result['count'] . "</p>\n";

    } else {
        echo "<p>❌ Grower 34 not found!</p>\n";

        // Show available growers
        $stmt = $pdo->query("SELECT id, name FROM growers ORDER BY id LIMIT 10");
        $growers = $stmt->fetchAll();
        echo "<p>Available growers:</p>\n";
        echo "<ul>\n";
        foreach ($growers as $g) {
            echo "<li>ID: " . $g['id'] . " - " . htmlspecialchars($g['name']) . "</li>\n";
        }
        echo "</ul>\n";
    }

} catch (PDOException $e) {
    echo "<p>❌ Database Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "<p>Error Code: " . $e->getCode() . "</p>\n";
} catch (Exception $e) {
    echo "<p>❌ General Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>\n";
}

echo "<h2>System Information:</h2>\n";
echo "<p>PHP Version: " . PHP_VERSION . "</p>\n";
echo "<p>Server Time: " . date('Y-m-d H:i:s T') . "</p>\n";
echo "<p>Working Directory: " . getcwd() . "</p>\n";

if (function_exists('phpinfo')) {
    echo "<p>PDO available: " . (extension_loaded('pdo') ? '✅ Yes' : '❌ No') . "</p>\n";
    echo "<p>PDO MySQL available: " . (extension_loaded('pdo_mysql') ? '✅ Yes' : '❌ No') . "</p>\n";
}

echo "<p>Test completed: " . date('Y-m-d H:i:s') . "</p>\n";
?>
