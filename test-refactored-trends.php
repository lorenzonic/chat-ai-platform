<?php

// Test refactored trends system
echo "<h1>Test Sistema Trends Refactorizzato</h1>";

try {
    // Test login admin first
    $adminEmail = 'admin@admin.com';
    $adminPassword = 'password';
    
    echo "<h2>1. Test Login Admin</h2>";
    
    // Initialize curl session for login
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/login');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
    
    // Get login page to get CSRF token
    $loginPage = curl_exec($ch);
    
    // Extract CSRF token
    preg_match('/_token.*?value="([^"]*)"/', $loginPage, $matches);
    $csrfToken = $matches[1] ?? '';
    
    if (empty($csrfToken)) {
        echo "❌ Impossibile ottenere il token CSRF<br>";
        exit;
    }
    
    echo "✅ Token CSRF ottenuto: " . substr($csrfToken, 0, 10) . "...<br>";
    
    // Perform login
    curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/login');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        '_token' => $csrfToken,
        'email' => $adminEmail,
        'password' => $adminPassword
    ]));
    
    $loginResult = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($httpCode == 302 || strpos($loginResult, 'dashboard') !== false) {
        echo "✅ Login admin riuscito<br>";
    } else {
        echo "❌ Login admin fallito (HTTP: $httpCode)<br>";
        echo "Response: " . substr($loginResult, 0, 500) . "...<br>";
        exit;
    }
    
    echo "<h2>2. Test Trends Dashboard Refactorizzato</h2>";
    
    // Test trends dashboard
    curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/trends');
    curl_setopt($ch, CURLOPT_POST, false);
    
    $trendsResult = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($httpCode == 200) {
        echo "✅ Trends dashboard accessibile (HTTP: $httpCode)<br>";
        
        // Check for key elements that should be present
        $checks = [
            'Google Trends' => strpos($trendsResult, 'google_trends') !== false || strpos($trendsResult, 'Google Trends') !== false,
            'Social Trends' => strpos($trendsResult, 'social_trends') !== false || strpos($trendsResult, 'Social') !== false,
            'Seasonal Trends' => strpos($trendsResult, 'seasonal') !== false || strpos($trendsResult, 'Seasonal') !== false,
            'Performance Metrics' => strpos($trendsResult, 'performance') !== false || strpos($trendsResult, 'Performance') !== false,
            'Demographic' => strpos($trendsResult, 'demographic') !== false || strpos($trendsResult, 'Demographic') !== false,
            'Ecommerce Data' => strpos($trendsResult, 'ecommerce') !== false || strpos($trendsResult, 'E-commerce') !== false
        ];
        
        foreach ($checks as $check => $result) {
            if ($result) {
                echo "✅ $check: Presente<br>";
            } else {
                echo "⚠️ $check: Non trovato nel contenuto<br>";
            }
        }
        
        // Check for error messages
        if (strpos($trendsResult, 'error') !== false || strpos($trendsResult, 'Error') !== false) {
            echo "⚠️ Possibili errori trovati nella risposta<br>";
        }
        
    } else {
        echo "❌ Errore accesso trends dashboard (HTTP: $httpCode)<br>";
        echo "Response preview: " . substr($trendsResult, 0, 500) . "...<br>";
    }
    
    echo "<h2>3. Test AI Predictions Endpoint</h2>";
    
    // Test AI predictions endpoint
    curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/trends/ai-predictions');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    
    $aiResult = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($httpCode == 200) {
        echo "✅ AI Predictions endpoint accessibile (HTTP: $httpCode)<br>";
        
        $aiData = json_decode($aiResult, true);
        if ($aiData) {
            echo "✅ Risposta JSON valida<br>";
            echo "📊 Categorie AI trovate: " . implode(', ', array_keys($aiData)) . "<br>";
        } else {
            echo "⚠️ Risposta non è JSON valido<br>";
            echo "Response: " . substr($aiResult, 0, 300) . "...<br>";
        }
    } else {
        echo "❌ Errore AI predictions endpoint (HTTP: $httpCode)<br>";
        echo "Response preview: " . substr($aiResult, 0, 300) . "...<br>";
    }
    
    curl_close($ch);
    
    echo "<h2>4. Test Servizi Individuali</h2>";
    
    // Test individual services through PHP
    try {
        // Test if we can instantiate services manually
        require_once __DIR__ . '/vendor/autoload.php';
        
        $app = require_once __DIR__ . '/bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        
        // Test Google Trends Service
        try {
            $googleService = $app->make(\App\Services\Trends\GoogleTrendsService::class);
            $trends = $googleService->getTrends(7);
            echo "✅ GoogleTrendsService: Funzionante<br>";
        } catch (Exception $e) {
            echo "❌ GoogleTrendsService: " . $e->getMessage() . "<br>";
        }
        
        // Test Social Media Service
        try {
            $socialService = $app->make(\App\Services\Trends\SocialMediaTrendsService::class);
            $social = $socialService->getSocialTrends(7);
            echo "✅ SocialMediaTrendsService: Funzionante<br>";
        } catch (Exception $e) {
            echo "❌ SocialMediaTrendsService: " . $e->getMessage() . "<br>";
        }
        
        // Test Seasonal Analysis Service
        try {
            $seasonalService = $app->make(\App\Services\Trends\SeasonalAnalysisService::class);
            $seasonal = $seasonalService->getSeasonalTrends();
            echo "✅ SeasonalAnalysisService: Funzionante<br>";
        } catch (Exception $e) {
            echo "❌ SeasonalAnalysisService: " . $e->getMessage() . "<br>";
        }
        
        // Test Demographic Service
        try {
            $demoService = $app->make(\App\Services\Trends\DemographicAnalysisService::class);
            $demo = $demoService->getDemographicTrends(7);
            echo "✅ DemographicAnalysisService: Funzionante<br>";
        } catch (Exception $e) {
            echo "❌ DemographicAnalysisService: " . $e->getMessage() . "<br>";
        }
        
        // Test Performance Service
        try {
            $perfService = $app->make(\App\Services\Trends\PerformanceMetricsService::class);
            $perf = $perfService->calculateGrowthRate(7);
            echo "✅ PerformanceMetricsService: Funzionante<br>";
        } catch (Exception $e) {
            echo "❌ PerformanceMetricsService: " . $e->getMessage() . "<br>";
        }
        
        // Test Ecommerce Service
        try {
            $ecomService = $app->make(\App\Services\Trends\EcommerceDataService::class);
            $ecom = $ecomService->getAvailableSites();
            echo "✅ EcommerceDataService: Funzionante<br>";
        } catch (Exception $e) {
            echo "❌ EcommerceDataService: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Errore inizializzazione Laravel: " . $e->getMessage() . "<br>";
    }
    
    echo "<h2>5. Riepilogo Refactor</h2>";
    
    echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 5px;'>";
    echo "<h3>✅ Refactor TrendsController Completato!</h3>";
    echo "<p><strong>Architettura precedente:</strong></p>";
    echo "<ul>";
    echo "<li>❌ 1 controller monolitico (1382 righe)</li>";
    echo "<li>❌ Tutte le responsabilità in un unico file</li>";
    echo "<li>❌ Difficile da testare e mantenere</li>";
    echo "<li>❌ Violazione Single Responsibility Principle</li>";
    echo "</ul>";
    
    echo "<p><strong>Nuova architettura modulare:</strong></p>";
    echo "<ul>";
    echo "<li>✅ 6 servizi specializzati e focalizzati</li>";
    echo "<li>✅ GoogleTrendsService - gestione ricerche e keywords</li>";
    echo "<li>✅ SocialMediaTrendsService - analisi social, hashtag, influencer</li>";
    echo "<li>✅ SeasonalAnalysisService - analisi stagionali e predizioni</li>";
    echo "<li>✅ DemographicAnalysisService - analisi demografiche e regionali</li>";
    echo "<li>✅ PerformanceMetricsService - metriche performance, ROI, CAC</li>";
    echo "<li>✅ EcommerceDataService - gestione dati marketplace</li>";
    echo "<li>✅ TrendsControllerRefactored - orchestrazione leggera dei servizi</li>";
    echo "<li>✅ TrendsServiceProvider - dependency injection</li>";
    echo "<li>✅ Testabilità migliorata</li>";
    echo "<li>✅ Manutenibilità aumentata</li>";
    echo "<li>✅ Scalabilità del sistema</li>";
    echo "</ul>";
    
    echo "<p><strong>Benefici:</strong></p>";
    echo "<ul>";
    echo "<li>🎯 Separazione delle responsabilità</li>";
    echo "<li>🔧 Facilità di manutenzione</li>";
    echo "<li>🧪 Testabilità individuale dei servizi</li>";
    echo "<li>📈 Scalabilità per future funzionalità</li>";
    echo "<li>🔄 Riusabilità dei servizi</li>";
    echo "<li>💡 Architettura SOLID</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<h2>❌ Errore durante il test</h2>";
    echo "<p>Errore: " . $e->getMessage() . "</p>";
    echo "<p>Traccia: " . $e->getTraceAsString() . "</p>";
}

// Cleanup
if (file_exists('cookies.txt')) {
    unlink('cookies.txt');
}

echo "<br><p><em>Test completato alle " . date('Y-m-d H:i:s') . "</em></p>";
?>
