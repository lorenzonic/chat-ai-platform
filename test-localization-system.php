<?php

require_once 'vendor/autoload.php';

// Setup Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\App;

echo "Testing Localization System\n";
echo "============================\n\n";

try {
    // Test 1: Check available locales
    echo "1. Testing available locales...\n";
    $availableLocales = config('app.available_locales');
    foreach ($availableLocales as $code => $name) {
        echo "✓ {$code}: {$name}\n";
    }
    echo "\n";

    // Test 2: Check default locale
    echo "2. Testing default locale...\n";
    $defaultLocale = config('app.locale');
    $fallbackLocale = config('app.fallback_locale');
    echo "✓ Default locale: {$defaultLocale}\n";
    echo "✓ Fallback locale: {$fallbackLocale}\n";
    echo "✓ Current app locale: " . App::getLocale() . "\n\n";

    // Test 3: Test Italian translations
    echo "3. Testing Italian translations...\n";
    App::setLocale('it');

    $italianTests = [
        'common.dashboard' => 'Dashboard',
        'common.login' => 'Accedi',
        'common.logout' => 'Esci',
        'common.edit' => 'Modifica',
        'common.delete' => 'Elimina',
        'admin.grower_management' => 'Gestione Produttori',
        'admin.grower_details' => 'Dettagli Produttore',
        'auth.sign_in' => 'Accedi',
        'grower.dashboard' => 'Dashboard Produttore',
    ];

    foreach ($italianTests as $key => $expected) {
        $translated = __($key);
        if ($translated === $key) {
            echo "✗ {$key}: Translation missing\n";
        } else {
            echo "✓ {$key}: {$translated}\n";
        }
    }
    echo "\n";

    // Test 4: Test English translations
    echo "4. Testing English translations...\n";
    App::setLocale('en');

    $englishTests = [
        'common.dashboard' => 'Dashboard',
        'common.login' => 'Login',
        'common.logout' => 'Logout',
        'common.edit' => 'Edit',
        'common.delete' => 'Delete',
        'admin.grower_management' => 'Grower Management',
        'admin.grower_details' => 'Grower Details',
        'auth.sign_in' => 'Sign In',
        'grower.dashboard' => 'Grower Dashboard',
    ];

    foreach ($englishTests as $key => $expected) {
        $translated = __($key);
        if ($translated === $key) {
            echo "✗ {$key}: Translation missing\n";
        } else {
            echo "✓ {$key}: {$translated}\n";
        }
    }
    echo "\n";

    // Test 5: Check translation files exist
    echo "5. Testing translation files...\n";
    $translationFiles = [
        'resources/lang/it/common.php' => 'Italian common translations',
        'resources/lang/it/auth.php' => 'Italian auth translations',
        'resources/lang/it/admin.php' => 'Italian admin translations',
        'resources/lang/it/grower.php' => 'Italian grower translations',
        'resources/lang/en/common.php' => 'English common translations',
        'resources/lang/en/auth.php' => 'English auth translations',
        'resources/lang/en/admin.php' => 'English admin translations',
        'resources/lang/en/grower.php' => 'English grower translations',
    ];

    foreach ($translationFiles as $file => $description) {
        if (file_exists($file)) {
            echo "✓ {$description}: Found\n";
        } else {
            echo "✗ {$description}: Missing\n";
        }
    }
    echo "\n";

    // Test 6: Test middleware functionality
    echo "6. Testing middleware setup...\n";

    $middleware = app()->make('Illuminate\Foundation\Http\Kernel')->getGlobalMiddleware();
    $middlewareAliases = app('router')->getMiddleware();

    if (isset($middlewareAliases['setLocale'])) {
        echo "✓ SetLocale middleware alias registered\n";
    } else {
        echo "✗ SetLocale middleware alias not found\n";
    }

    if (class_exists('App\\Http\\Middleware\\SetLocale')) {
        echo "✓ SetLocale middleware class exists\n";
    } else {
        echo "✗ SetLocale middleware class not found\n";
    }
    echo "\n";

    // Test 7: Test language routes
    echo "7. Testing language routes...\n";

    try {
        $switchRoute = route('language.switch', 'it');
        echo "✓ Language switch route: {$switchRoute}\n";
    } catch (Exception $e) {
        echo "✗ Language switch route: Not found\n";
    }

    try {
        $currentRoute = route('language.current');
        echo "✓ Language current route: {$currentRoute}\n";
    } catch (Exception $e) {
        echo "✗ Language current route: Not found\n";
    }
    echo "\n";

    // Test 8: Test language controller
    echo "8. Testing language controller...\n";

    if (class_exists('App\\Http\\Controllers\\LanguageController')) {
        echo "✓ LanguageController class exists\n";

        $controller = app('App\\Http\\Controllers\\LanguageController');
        if (method_exists($controller, 'switch')) {
            echo "✓ LanguageController switch method exists\n";
        } else {
            echo "✗ LanguageController switch method missing\n";
        }

        if (method_exists($controller, 'current')) {
            echo "✓ LanguageController current method exists\n";
        } else {
            echo "✗ LanguageController current method missing\n";
        }
    } else {
        echo "✗ LanguageController class not found\n";
    }
    echo "\n";

    // Test 9: Test view component
    echo "9. Testing view components...\n";

    if (file_exists('resources/views/components/language-selector.blade.php')) {
        echo "✓ Language selector component exists\n";
    } else {
        echo "✗ Language selector component missing\n";
    }
    echo "\n";

    echo "Localization system test completed!\n";
    echo "======================================\n";
    echo "Current locale: " . App::getLocale() . "\n";
    echo "Available locales: " . implode(', ', array_keys($availableLocales)) . "\n";
    echo "Translation files: " . count(array_filter($translationFiles, 'file_exists')) . "/" . count($translationFiles) . " present\n";

} catch (Exception $e) {
    echo "Error during testing: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
