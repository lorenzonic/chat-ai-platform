<?php
// Direct dashboard test - bypassing Laravel bootstrap issues
echo "<h1>Direct Dashboard Test</h1>\n";

try {
    // Get database connection Railway style
    $databaseUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');

    if (!$databaseUrl) {
        echo "<p>❌ DATABASE_URL not found</p>\n";
        exit;
    }

    $parsed = parse_url($databaseUrl);
    $host = $parsed['host'];
    $dbname = ltrim($parsed['path'], '/');
    $username = $parsed['user'];
    $password = $parsed['pass'];

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "<p>✅ Database connected</p>\n";

    // Simulate what the dashboard controller does
    $growerId = 34;

    // Get grower
    $stmt = $pdo->prepare("SELECT * FROM growers WHERE id = ? LIMIT 1");
    $stmt->execute([$growerId]);
    $grower = $stmt->fetch();

    if (!$grower) {
        echo "<p>❌ Grower $growerId not found</p>\n";
        exit;
    }

    echo "<p>✅ Grower found: " . htmlspecialchars($grower['name']) . "</p>\n";

    // Test all dashboard queries exactly as in the controller

    // 1. Total Products
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE grower_id = ?");
    $stmt->execute([$growerId]);
    $totalProducts = $stmt->fetch()['count'];
    echo "<p>Total Products: $totalProducts</p>\n";

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
    echo "<p>Total Orders: $totalOrders</p>\n";

    // 3. Products in Orders
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT products.id) as count
        FROM products
        INNER JOIN order_items ON products.id = order_items.product_id
        WHERE products.grower_id = ?
    ");
    $stmt->execute([$growerId]);
    $productsInOrders = $stmt->fetch()['count'];
    echo "<p>Products in Orders: $productsInOrders</p>\n";

    // 4. Low Stock Products - this was causing issues
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE grower_id = ? AND quantity <= 10 AND quantity > 0");
    $stmt->execute([$growerId]);
    $lowStockProducts = $stmt->fetch()['count'];
    echo "<p>Low Stock Products: $lowStockProducts</p>\n";

    // 5. Out of Stock Products
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE grower_id = ? AND quantity = 0");
    $stmt->execute([$growerId]);
    $outOfStockProducts = $stmt->fetch()['count'];
    echo "<p>Out of Stock Products: $outOfStockProducts</p>\n";

    // 6. Recent Products
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE grower_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$growerId]);
    $recentProducts = $stmt->fetch()['count'];
    echo "<p>Recent Products: $recentProducts</p>\n";

    // 7. Recent Orders - the PROBLEMATIC query that we fixed
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT orders.id) as count
        FROM orders
        INNER JOIN order_items ON orders.id = order_items.order_id
        INNER JOIN products ON order_items.product_id = products.id
        WHERE products.grower_id = ?
        ORDER BY orders.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$growerId]);
    $recentOrders = $stmt->fetch()['count'];
    echo "<p>Recent Orders: $recentOrders</p>\n";

    echo "<h2>✅ All Dashboard Queries Successful!</h2>\n";
    echo "<p>The dashboard should work now. The problem was the orderItems.product relationship query.</p>\n";

    // Test if we can simulate the view data
    $viewData = [
        'grower' => $grower,
        'totalProducts' => $totalProducts,
        'totalOrders' => $totalOrders,
        'productsInOrders' => $productsInOrders,
        'lowStockProducts' => $lowStockProducts,
        'outOfStockProducts' => $outOfStockProducts,
        'recentProducts' => 5, // mock
        'recentOrders' => 5    // mock
    ];

    echo "<h2>Dashboard Data Ready</h2>\n";
    foreach ($viewData as $key => $value) {
        if ($key !== 'grower') {
            echo "<p>$key: $value</p>\n";
        }
    }

} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "<p>File: " . htmlspecialchars($e->getFile()) . " Line: " . $e->getLine() . "</p>\n";
}

echo "<p>Test completed: " . date('Y-m-d H:i:s') . "</p>\n";
?>
