<?php

// Test per verificare il redirect post-login per admin e store

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

echo "=== TEST REDIRECT POST-LOGIN ===\n\n";

// Test 1: Verifica le rotte admin
echo "1. ROTTE ADMIN:\n";
$adminRoutes = collect(Route::getRoutes())->filter(function($route) {
    return str_starts_with($route->getName() ?? '', 'admin.');
});

foreach ($adminRoutes as $route) {
    $name = $route->getName();
    if (in_array($name, ['admin.login', 'admin.dashboard'])) {
        echo "   - {$name}: " . $route->uri() . "\n";
    }
}

// Test 2: Verifica le rotte store
echo "\n2. ROTTE STORE:\n";
$storeRoutes = collect(Route::getRoutes())->filter(function($route) {
    return str_starts_with($route->getName() ?? '', 'store.');
});

foreach ($storeRoutes as $route) {
    $name = $route->getName();
    if (in_array($name, ['store.login', 'store.dashboard'])) {
        echo "   - {$name}: " . $route->uri() . "\n";
    }
}

// Test 3: Genera URL per test
echo "\n3. URL GENERATI:\n";
try {
    $adminLoginUrl = route('admin.login');
    $adminDashboardUrl = route('admin.dashboard');
    $storeLoginUrl = route('store.login');
    $storeDashboardUrl = route('store.dashboard');
    
    echo "   - Admin Login: {$adminLoginUrl}\n";
    echo "   - Admin Dashboard: {$adminDashboardUrl}\n";
    echo "   - Store Login: {$storeLoginUrl}\n";
    echo "   - Store Dashboard: {$storeDashboardUrl}\n";
} catch (Exception $e) {
    echo "   ERRORE nella generazione URL: " . $e->getMessage() . "\n";
}

// Test 4: Verifica middleware
echo "\n4. MIDDLEWARE:\n";
$middlewareFiles = [
    'app/Http/Middleware/IsAdmin.php',
    'app/Http/Middleware/IsStore.php'
];

foreach ($middlewareFiles as $file) {
    if (file_exists($file)) {
        echo "   ✓ {$file} esiste\n";
    } else {
        echo "   ✗ {$file} NON esiste\n";
    }
}

// Test 5: Verifica controllers
echo "\n5. CONTROLLERS:\n";
$controllerFiles = [
    'app/Http/Controllers/Admin/Auth/LoginController.php',
    'app/Http/Controllers/Store/Auth/LoginController.php'
];

foreach ($controllerFiles as $file) {
    if (file_exists($file)) {
        echo "   ✓ {$file} esiste\n";
    } else {
        echo "   ✗ {$file} NON esiste\n";
    }
}

// Test 6: Simula redirect intended
echo "\n6. SIMULAZIONE REDIRECT:\n";
echo "   - redirect()->intended(route('admin.dashboard')) dovrebbe andare a: " . route('admin.dashboard') . "\n";
echo "   - redirect()->intended(route('store.dashboard')) dovrebbe andare a: " . route('store.dashboard') . "\n";

// Test 7: Verifica configurazione auth guards
echo "\n7. CONFIGURAZIONE AUTH:\n";
$authConfig = config('auth');
echo "   - Default guard: " . $authConfig['defaults']['guard'] . "\n";
echo "   - Guards disponibili:\n";
foreach ($authConfig['guards'] as $guard => $config) {
    echo "     * {$guard}: driver={$config['driver']}, provider={$config['provider']}\n";
}

echo "\n=== FINE TEST ===\n";
