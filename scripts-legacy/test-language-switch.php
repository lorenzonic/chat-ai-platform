<?php

require_once 'vendor/autoload.php';

// Setup Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

echo "Testing Language Switch\n";
echo "=======================\n\n";

try {
    // Test 1: Check current locale
    echo "1. Current locale test...\n";
    echo "Default locale from config: " . config('app.locale') . "\n";
    echo "Current app locale: " . App::getLocale() . "\n";
    echo "Available locales: " . implode(', ', array_keys(config('app.available_locales'))) . "\n\n";

    // Test 2: Switch to Italian
    echo "2. Switching to Italian...\n";
    Session::put('locale', 'it');
    App::setLocale('it');
    echo "Session locale: " . Session::get('locale') . "\n";
    echo "App locale: " . App::getLocale() . "\n";
    echo "Test translation: " . __('common.dashboard') . "\n\n";

    // Test 3: Switch to English
    echo "3. Switching to English...\n";
    Session::put('locale', 'en');
    App::setLocale('en');
    echo "Session locale: " . Session::get('locale') . "\n";
    echo "App locale: " . App::getLocale() . "\n";
    echo "Test translation: " . __('common.dashboard') . "\n\n";

    // Test 4: Test specific translations
    echo "4. Testing specific translations...\n";

    // Test Italian
    App::setLocale('it');
    echo "Italian:\n";
    echo "  Dashboard: " . __('common.dashboard') . "\n";
    echo "  Login: " . __('common.login') . "\n";
    echo "  Logout: " . __('common.logout') . "\n";

    // Test English
    App::setLocale('en');
    echo "English:\n";
    echo "  Dashboard: " . __('common.dashboard') . "\n";
    echo "  Login: " . __('common.login') . "\n";
    echo "  Logout: " . __('common.logout') . "\n\n";

    // Test 5: Check routes
    echo "5. Testing routes...\n";
    try {
        echo "Language switch route (IT): " . route('language.switch', 'it') . "\n";
        echo "Language switch route (EN): " . route('language.switch', 'en') . "\n";
        echo "Current language route: " . route('language.current') . "\n\n";
    } catch (Exception $e) {
        echo "Route error: " . $e->getMessage() . "\n\n";
    }

    // Test 6: Check component exists
    echo "6. Component test...\n";
    if (file_exists('resources/views/components/language-selector.blade.php')) {
        echo "✓ Language selector component exists\n";
    } else {
        echo "✗ Language selector component missing\n";
    }

    // Test 7: Check if middleware is applied
    echo "\n7. Middleware test...\n";
    $kernel = app('Illuminate\Foundation\Http\Kernel');
    $middlewareGroups = $kernel->getMiddlewareGroups();

    if (isset($middlewareGroups['web']) && in_array('App\\Http\\Middleware\\SetLocale', $middlewareGroups['web'])) {
        echo "✓ SetLocale middleware is applied to web group\n";
    } else {
        echo "✗ SetLocale middleware not found in web group\n";
    }

    echo "\n=================\n";
    echo "Test completed!\n";
    echo "=================\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
