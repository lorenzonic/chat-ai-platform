<?php
// Test dashboard with existing grower ID 39
echo "<h1>Dashboard Test with Grower ID 39</h1>\n";

try {
    // Railway database connection
    $databaseUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');
    $parsed = parse_url($databaseUrl);
    $host = $parsed['host'];
    $port = $parsed['port'] ?? 3306;
    $dbname = ltrim($parsed['path'], '/');
    $username = $parsed['user'];
    $password = $parsed['pass'];

    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "<p>✅ Database connected</p>\n";

    // Use existing grower ID 39
    $growerId = 39;

    // Get grower
    $stmt = $pdo->prepare("SELECT * FROM growers WHERE id = ? LIMIT 1");
    $stmt->execute([$growerId]);
    $grower = $stmt->fetch();

    if (!$grower) {
        echo "<p>❌ Grower $growerId not found</p>\n";
        exit;
    }

    echo "<p>✅ Grower found: " . htmlspecialchars($grower['name']) . "</p>\n";
    echo "<p>Email: " . htmlspecialchars($grower['email']) . "</p>\n";

    // Test all dashboard queries exactly as in the controller

    // 1. Total Products
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE grower_id = ?");
    $stmt->execute([$growerId]);
    $totalProducts = $stmt->fetch()['count'];
    echo "<p>✅ Total Products: $totalProducts</p>\n";

    // 2. Total Orders - using the FIXED query
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT orders.id) as count
        FROM orders
        INNER JOIN order_items ON orders.id = order_items.order_id
        INNER JOIN products ON order_items.product_id = products.id
        WHERE products.grower_id = ?
    ");
    $stmt->execute([$growerId]);
    $totalOrders = $stmt->fetch()['count'];
    echo "<p>✅ Total Orders: $totalOrders</p>\n";

    // 3. Products in Orders
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT products.id) as count
        FROM products
        INNER JOIN order_items ON products.id = order_items.product_id
        WHERE products.grower_id = ?
    ");
    $stmt->execute([$growerId]);
    $productsInOrders = $stmt->fetch()['count'];
    echo "<p>✅ Products in Orders: $productsInOrders</p>\n";

    // 4. Low Stock Products
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE grower_id = ? AND quantity <= 10 AND quantity > 0");
    $stmt->execute([$growerId]);
    $lowStockProducts = $stmt->fetch()['count'];
    echo "<p>✅ Low Stock Products: $lowStockProducts</p>\n";

    // 5. Out of Stock Products
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE grower_id = ? AND quantity = 0");
    $stmt->execute([$growerId]);
    $outOfStockProducts = $stmt->fetch()['count'];
    echo "<p>✅ Out of Stock Products: $outOfStockProducts</p>\n";

    // 6. Recent Products
    $stmt = $pdo->prepare("SELECT id, name, created_at FROM products WHERE grower_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$growerId]);
    $recentProducts = $stmt->fetchAll();
    echo "<p>✅ Recent Products: " . count($recentProducts) . "</p>\n";

    if (count($recentProducts) > 0) {
        echo "<ul>\n";
        foreach ($recentProducts as $product) {
            echo "<li>" . htmlspecialchars($product['name']) . " (ID: " . $product['id'] . ")</li>\n";
        }
        echo "</ul>\n";
    }

    // 7. Recent Orders - the FIXED query
    $stmt = $pdo->prepare("
        SELECT DISTINCT orders.id, orders.order_number, orders.created_at
        FROM orders
        INNER JOIN order_items ON orders.id = order_items.order_id
        INNER JOIN products ON order_items.product_id = products.id
        WHERE products.grower_id = ?
        ORDER BY orders.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$growerId]);
    $recentOrders = $stmt->fetchAll();
    echo "<p>✅ Recent Orders: " . count($recentOrders) . "</p>\n";

    if (count($recentOrders) > 0) {
        echo "<ul>\n";
        foreach ($recentOrders as $order) {
            echo "<li>Order #" . htmlspecialchars($order['order_number']) . " (ID: " . $order['id'] . ")</li>\n";
        }
        echo "</ul>\n";
    }

    echo "<h2>✅ All Dashboard Queries Successful!</h2>\n";
    echo "<p><strong>The dashboard should work now with grower ID 39!</strong></p>\n";

    // Show login URL for grower 39
    echo "<h2>Next Steps:</h2>\n";
    echo "<p>1. Login as grower 39: <a href='/grower/login'>/grower/login</a></p>\n";
    echo "<p>2. Test dashboard: <a href='/grower/dashboard'>/grower/dashboard</a></p>\n";
    echo "<p>3. Test etichette: <a href='/grower/products-stickers'>/grower/products-stickers</a></p>\n";

    echo "<h2>Grower 39 Credentials Needed</h2>\n";
    echo "<p>Check the database for grower 39 login credentials, or create test login for this grower.</p>\n";

} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "<p>File: " . htmlspecialchars($e->getFile()) . " Line: " . $e->getLine() . "</p>\n";
}

echo "<p>Test completed: " . date('Y-m-d H:i:s') . "</p>\n";
?>
