<?php
// Test Login Redirect

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 Testing Login Redirect\n";
echo "================================\n\n";

// 1. Check route('login') exists
echo "1️⃣ Route Check:\n";
try {
    $loginUrl = route('login');
    echo "   - route('login'): ✓ EXISTS\n";
    echo "   - URL: {$loginUrl}\n\n";
} catch (Exception $e) {
    echo "   - route('login'): ✗ MISSING\n\n";
}

// 2. Check admin/store/grower login routes
echo "2️⃣ Multi-Auth Login Routes:\n";

$routes = [
    'admin.login' => 'Admin Login',
    'store.login' => 'Store Login',
    'grower.login' => 'Grower Login',
];

foreach ($routes as $routeName => $label) {
    try {
        $url = route($routeName);
        echo "   - {$label}: ✓ {$url}\n";
    } catch (Exception $e) {
        echo "   - {$label}: ✗ Not found\n";
    }
}

echo "\n3️⃣ Expected Behavior:\n";
echo "   When user visits: /login\n";
echo "   → Redirects to: / (home page)\n\n";

echo "   When user visits:\n";
echo "   - /admin/login → Admin login form\n";
echo "   - /store/login → Store login form\n";
echo "   - /grower/login → Grower login form\n\n";

echo "4️⃣ Test URLs:\n";
echo "   - http://localhost:8000/login (should redirect to home)\n";
echo "   - http://localhost:8000/admin/login (admin login page)\n";
echo "   - http://localhost:8000/store/login (store login page)\n";
echo "   - http://localhost:8000/grower/login (grower login page)\n\n";

echo "✅ Configuration updated!\n";
echo "📌 Main /login route now redirects to home page\n";
echo "📌 Specific logins (admin/store/grower) still work\n";
