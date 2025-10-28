<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\TrendingKeywordsController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (not authenticated)
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);
    });

    // Authenticated admin routes
    Route::middleware('isAdmin')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');

        // Profile routes
        Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('profile/password', [ProfileController::class, 'editPassword'])->name('profile.password.edit');
        Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

        // QR Code management
        Route::resource('qr-codes', QrCodeController::class);
        Route::get('qr-codes/{qrCode}/download', [QrCodeController::class, 'download'])->name('qr-codes.download');
        Route::post('qr-codes/{qrCode}/regenerate', [QrCodeController::class, 'regenerate'])->name('qr-codes.regenerate');
        // QR code label (stampa etichetta)
        Route::get('qr-codes/{qrCode}/label', [App\Http\Controllers\Admin\QrCodeController::class, 'label'])->name('qr-codes.label');

        // Account management
        Route::prefix('accounts')->name('accounts.')->group(function () {
            Route::get('/', [AccountController::class, 'index'])->name('index');

            // Store account routes
            Route::get('stores/create', [AccountController::class, 'createStore'])->name('stores.create');
            Route::post('stores', [AccountController::class, 'storeStore'])->name('stores.store');
            Route::get('stores/{store}', [AccountController::class, 'showStore'])->name('stores.show');
            Route::get('stores/{store}/edit', [AccountController::class, 'editStore'])->name('stores.edit');
            Route::put('stores/{store}', [AccountController::class, 'updateStore'])->name('stores.update');
            Route::delete('stores/{store}', [AccountController::class, 'destroyStore'])->name('stores.destroy');
            Route::patch('stores/{store}/toggle-status', [AccountController::class, 'toggleStoreStatus'])->name('stores.toggle-status');
            Route::patch('stores/{store}/toggle-premium', [AccountController::class, 'toggleStorePremium'])->name('stores.toggle-premium');

            // Admin account routes
            Route::get('admins/create', [AccountController::class, 'createAdmin'])->name('admins.create');
            Route::post('admins', [AccountController::class, 'storeAdmin'])->name('admins.store');
            Route::get('admins/{admin}', [AccountController::class, 'showAdmin'])->name('admins.show');

            // Grower account routes
            Route::get('growers/create', [\App\Http\Controllers\Admin\GrowerController::class, 'create'])->name('growers.create');
            Route::post('growers', [\App\Http\Controllers\Admin\GrowerController::class, 'store'])->name('growers.store');
            Route::get('growers/{grower}', [\App\Http\Controllers\Admin\GrowerController::class, 'show'])->name('growers.show');
            Route::get('growers/{grower}/edit', [\App\Http\Controllers\Admin\GrowerController::class, 'edit'])->name('growers.edit');
            Route::put('growers/{grower}', [\App\Http\Controllers\Admin\GrowerController::class, 'update'])->name('growers.update');
            Route::delete('growers/{grower}', [\App\Http\Controllers\Admin\GrowerController::class, 'destroy'])->name('growers.destroy');
        });

        // Analytics routes
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');

        // Trends Analytics routes (using refactored controller)
        Route::get('trends', [\App\Http\Controllers\Admin\TrendsControllerRefactored::class, 'index'])->name('trends.index');
        Route::get('trends/ai-predictions', [\App\Http\Controllers\Admin\TrendsControllerRefactored::class, 'getAIPredictions'])->name('trends.ai-predictions');
        Route::get('trends/advanced', [\App\Http\Controllers\Admin\TrendsControllerRefactored::class, 'advanced'])->name('trends.advanced');
        Route::get('trends/configure', [\App\Http\Controllers\Admin\TrendsControllerRefactored::class, 'configure'])->name('trends.configure');
        Route::post('trends/sites', [\App\Http\Controllers\Admin\TrendsControllerRefactored::class, 'storeSite'])->name('trends.sites.store');
        Route::delete('trends/sites/{key}', [\App\Http\Controllers\Admin\TrendsControllerRefactored::class, 'destroySite'])->name('trends.sites.destroy');
        Route::get('trends/api-google-trends', [\App\Http\Controllers\Admin\TrendsControllerRefactored::class, 'apiGoogleTrends'])->name('trends.api-google-trends');

        // Google Trends routes
        Route::prefix('trending-keywords')->name('trending-keywords.')->group(function () {
            Route::get('/', [TrendingKeywordsController::class, 'index'])->name('index');
            Route::get('/api/chart-data', [TrendingKeywordsController::class, 'chartData'])->name('chart-data');
            Route::post('/update', [TrendingKeywordsController::class, 'updateTrends'])->name('update');
            Route::post('/update-script', [TrendingKeywordsController::class, 'runUpdateScript'])->name('update-script');
            Route::delete('/cleanup', [TrendingKeywordsController::class, 'cleanup'])->name('cleanup');
            Route::get('/{keyword}', [TrendingKeywordsController::class, 'show'])->name('show')->where('keyword', '.*');
        });

        // Grower management routes
        Route::prefix('growers')->name('growers.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\GrowerController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\GrowerController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\GrowerController::class, 'store'])->name('store');
            Route::get('/{grower}', [\App\Http\Controllers\Admin\GrowerController::class, 'show'])->name('show');
            Route::get('/{grower}/edit', [\App\Http\Controllers\Admin\GrowerController::class, 'edit'])->name('edit');
            Route::put('/{grower}', [\App\Http\Controllers\Admin\GrowerController::class, 'update'])->name('update');
            Route::delete('/{grower}', [\App\Http\Controllers\Admin\GrowerController::class, 'destroy'])->name('destroy');
        });

        // Order management routes
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\OrderController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\OrderController::class, 'store'])->name('store');
            Route::get('/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('show');
            Route::get('/{order}/edit', [\App\Http\Controllers\Admin\OrderController::class, 'edit'])->name('edit');
            Route::put('/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'update'])->name('update');
            Route::delete('/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('destroy');
        });

        // Products management routes (NUOVO)
        Route::prefix('all-products')->name('all-products.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\ProductController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\ProductController::class, 'store'])->name('store');
            Route::get('/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'show'])->name('show');
            Route::get('/{product}/edit', [\App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('destroy');
        });

        // Product label routes
        Route::prefix('products-stickers')->name('products.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ProductLabelController::class, 'index'])->name('index');
            Route::get('/{orderItem}', [\App\Http\Controllers\Admin\ProductLabelController::class, 'show'])->name('show');
            Route::get('/{orderItem}/edit', [\App\Http\Controllers\Admin\ProductLabelController::class, 'edit'])->name('edit');
            Route::put('/{orderItem}', [\App\Http\Controllers\Admin\ProductLabelController::class, 'update'])->name('update');
            Route::get('/{orderItem}/thermal', [\App\Http\Controllers\Admin\ProductLabelController::class, 'thermalPrint'])->name('thermal');
        });

                // Import routes
        Route::prefix('import')->name('import.')->group(function () {
            Route::get('/', [ImportController::class, 'index'])->name('index');

            // Products import
            Route::get('/products', [ImportController::class, 'showProductsImport'])->name('products');
            Route::post('/products', [ImportController::class, 'importProducts'])->name('products.process');

            // Orders import (multi-step flow)
            Route::get('/orders', [ImportController::class, 'showOrdersImport'])->name('orders');
            Route::post('/orders/upload', [ImportController::class, 'uploadOrdersFile'])->name('orders.upload');
            Route::get('/orders/mapping', [ImportController::class, 'showMapping'])->name('mapping');
            Route::post('/orders/mapping', [ImportController::class, 'processMapping'])->name('mapping.process');
            Route::post('/orders/process', [ImportController::class, 'processOrdersImport'])->name('orders.process');

            // Template download
            Route::get('/template', [ImportController::class, 'downloadTemplate'])->name('template');

            // Debug route
            Route::get('/debug-upload/{filename?}', [ImportController::class, 'debugUpload'])->name('debug.upload');
        });

        // Debug route per verificare file temporanei
        Route::get('debug/temp-files', function () {
            $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'imports');

            $info = [
                'temp_dir_exists' => is_dir($tempDir),
                'temp_dir_path' => $tempDir,
                'files' => [],
                'session_info' => session('import_file_info'),
                'storage_app_path' => storage_path('app'),
            ];

            if (is_dir($tempDir)) {
                $files = scandir($tempDir);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        $filePath = $tempDir . DIRECTORY_SEPARATOR . $file;
                        $info['files'][] = [
                            'name' => $file,
                            'size' => filesize($filePath),
                            'modified' => date('Y-m-d H:i:s', filemtime($filePath)),
                            'exists' => file_exists($filePath),
                            'readable' => is_readable($filePath),
                        ];
                    }
                }
            }

            return response()->json($info, 200, [], JSON_PRETTY_PRINT);
        })->name('debug.temp-files');
    });

    // Offer management routes
    Route::prefix('offers')->name('offers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\OfferController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\OfferController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\OfferController::class, 'store'])->name('store');
        Route::get('/{offer}', [\App\Http\Controllers\Admin\OfferController::class, 'show'])->name('show');
        Route::get('/{offer}/edit', [\App\Http\Controllers\Admin\OfferController::class, 'edit'])->name('edit');
        Route::put('/{offer}', [\App\Http\Controllers\Admin\OfferController::class, 'update'])->name('update');
        Route::delete('/{offer}', [\App\Http\Controllers\Admin\OfferController::class, 'destroy'])->name('destroy');
        Route::patch('/{offer}/toggle-status', [\App\Http\Controllers\Admin\OfferController::class, 'toggleStatus'])->name('toggle-status');
    });
});
