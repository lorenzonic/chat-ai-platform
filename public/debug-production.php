<?php
// Simple production-safe debug test
try {
    // Try to include Laravel's autoloader
    if (file_exists('../vendor/autoload.php')) {
        require_once '../vendor/autoload.php';
    } else {
        echo "<p>❌ Autoloader not found</p>\n";
        exit;
    }

    echo "<h1>Production Debug Test</h1>\n";
    echo "<p>✅ Autoloader loaded</p>\n";

    // Try to load .env
    if (class_exists('Dotenv\Dotenv')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();
        echo "<p>✅ Environment loaded</p>\n";
    } else {
        echo "<p>⚠️ Dotenv not available</p>\n";
    }

    // Check database connection
    if (class_exists('PDO')) {
        $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost';
        $dbname = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?? 'chat_ai_platform';
        $username = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME') ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? '';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            echo "<p>✅ Database connection OK</p>\n";

            // Test grower query
            $stmt = $pdo->prepare("SELECT id, name, email FROM growers WHERE id = 34 LIMIT 1");
            $stmt->execute();
            $grower = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($grower) {
                echo "<p>✅ Grower 34 found: " . htmlspecialchars($grower['name']) . "</p>\n";

                // Test products count
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE grower_id = 34");
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "<p>✅ Products count: " . $result['count'] . "</p>\n";

                // Test orders count (using orderItems.product relationship)
                $stmt = $pdo->prepare("
                    SELECT COUNT(DISTINCT orders.id) as count
                    FROM orders
                    INNER JOIN order_items ON orders.id = order_items.order_id
                    INNER JOIN products ON order_items.product_id = products.id
                    WHERE products.grower_id = 34
                ");
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "<p>✅ Orders count: " . $result['count'] . "</p>\n";

                // Test if quantity column exists and works
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE grower_id = 34 AND quantity <= 10 AND quantity > 0");
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "<p>✅ Low stock products: " . $result['count'] . "</p>\n";

            } else {
                echo "<p>❌ Grower 34 not found in database</p>\n";
            }

        } catch (PDOException $e) {
            echo "<p>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
        }

    } else {
        echo "<p>❌ PDO not available</p>\n";
    }

    // Show environment info
    echo "<h2>Environment Info</h2>\n";
    echo "<p>PHP Version: " . PHP_VERSION . "</p>\n";
    echo "<p>Working Directory: " . getcwd() . "</p>\n";
    echo "<p>File exists - bootstrap/app.php: " . (file_exists('../bootstrap/app.php') ? 'Yes' : 'No') . "</p>\n";
    echo "<p>File exists - vendor/autoload.php: " . (file_exists('../vendor/autoload.php') ? 'Yes' : 'No') . "</p>\n";

    if (function_exists('apache_get_modules')) {
        echo "<p>Web Server: Apache</p>\n";
    } elseif (isset($_SERVER['SERVER_SOFTWARE'])) {
        echo "<p>Web Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>\n";
    }

} catch (Exception $e) {
    echo "<p>❌ General error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "<p>File: " . htmlspecialchars($e->getFile()) . " Line: " . $e->getLine() . "</p>\n";
}

echo "<p>Test completed: " . date('Y-m-d H:i:s') . "</p>\n";
?>
