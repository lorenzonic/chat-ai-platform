<?php

namespace App\Services\Trends;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Service per gestire i dati di Google Trends
 */
class GoogleTrendsService
{
    /**
     * Get Google Trends data for plant-related keywords
     */
    public function getTrends($days = 30)
    {
        $cacheKey = "google_trends_{$days}";

        return Cache::remember($cacheKey, 3600, function() use ($days) {
            try {
                // Use Python script to fetch Google Trends data
                $result = Process::run([
                    'python',
                    base_path('scripts/google_trends.py'),
                    '--days', $days,
                    '--keywords', 'piante,giardinaggio,botanica,verde,natura,fiori,cactus,succulente,bonsai,orchidee'
                ]);

                if ($result->successful()) {
                    $data = json_decode($result->output(), true);
                    $data['source'] = 'real';
                    Log::info('Google Trends Python script successful', ['data' => $data]);
                    return $data;
                }

                Log::warning('Google Trends Python script failed', [
                    'success' => $result->successful(),
                    'output' => $result->output(),
                    'error' => $result->errorOutput()
                ]);

                return $this->getFallbackData();

            } catch (\Exception $e) {
                Log::error('Google Trends fetch failed: ' . $e->getMessage());
                return $this->getFallbackData();
            }
        });
    }

    /**
     * Get fallback Google Trends data
     */
    private function getFallbackData()
    {
        $data = [
            'keywords' => [
                ['term' => 'piante', 'interest' => rand(60, 100), 'trend' => rand(0, 1) ? 'rising' : 'stable'],
                ['term' => 'giardinaggio', 'interest' => rand(40, 80), 'trend' => rand(0, 1) ? 'rising' : 'stable'],
                ['term' => 'botanica', 'interest' => rand(30, 70), 'trend' => rand(0, 1) ? 'rising' : 'stable'],
                ['term' => 'verde', 'interest' => rand(70, 100), 'trend' => rand(0, 1) ? 'rising' : 'stable'],
                ['term' => 'cactus', 'interest' => rand(50, 90), 'trend' => rand(0, 1) ? 'rising' : 'stable'],
            ],
            'average_interest' => rand(50, 85),
            'trend' => rand(0, 1) ? 'rising' : 'stable',
            'source' => 'demo'
        ];
        
        Log::info('Using Google Trends fallback data', ['fallback' => $data]);
        return $data;
    }

    /**
     * Get plant-related keywords with search volumes
     */
    public function getPlantKeywords()
    {
        return Cache::remember('plant_keywords', 86400, function() {
            return [
                'high_volume' => [
                    ['keyword' => 'piante', 'volume' => 250000, 'difficulty' => 'high', 'cpc' => 0.85],
                    ['keyword' => 'giardinaggio', 'volume' => 180000, 'difficulty' => 'medium', 'cpc' => 1.20],
                    ['keyword' => 'fiori', 'volume' => 220000, 'difficulty' => 'high', 'cpc' => 0.95],
                    ['keyword' => 'verde', 'volume' => 300000, 'difficulty' => 'high', 'cpc' => 0.75],
                ],
                'medium_volume' => [
                    ['keyword' => 'cactus', 'volume' => 95000, 'difficulty' => 'medium', 'cpc' => 1.15],
                    ['keyword' => 'succulente', 'volume' => 85000, 'difficulty' => 'low', 'cpc' => 1.35],
                    ['keyword' => 'bonsai', 'volume' => 78000, 'difficulty' => 'medium', 'cpc' => 1.50],
                    ['keyword' => 'orchidee', 'volume' => 65000, 'difficulty' => 'medium', 'cpc' => 1.80],
                ],
                'long_tail' => [
                    ['keyword' => 'come curare piante grasse', 'volume' => 15000, 'difficulty' => 'low', 'cpc' => 0.95],
                    ['keyword' => 'piante da appartamento facili', 'volume' => 12000, 'difficulty' => 'low', 'cpc' => 1.10],
                    ['keyword' => 'quando innaffiare cactus', 'volume' => 8000, 'difficulty' => 'low', 'cpc' => 0.80],
                    ['keyword' => 'migliori piante purificatrici aria', 'volume' => 18000, 'difficulty' => 'low', 'cpc' => 1.25],
                ],
                'trending' => [
                    ['keyword' => 'plant parent', 'volume' => 35000, 'growth' => 45, 'cpc' => 1.40],
                    ['keyword' => 'urban jungle', 'volume' => 28000, 'growth' => 38, 'cpc' => 1.60],
                    ['keyword' => 'plant therapy', 'volume' => 22000, 'growth' => 52, 'cpc' => 1.80],
                ],
            ];
        });
    }

    /**
     * Get marketplace trends (Amazon, eBay, etc.)
     */
    public function getMarketplaceTrends($days = 30)
    {
        $cacheKey = "marketplace_trends_{$days}";

        return Cache::remember($cacheKey, 3600, function() {
            // Simulate marketplace data
            return [
                'amazon' => [
                    'trending_products' => [
                        ['name' => 'Vasi per succulente', 'sales_rank' => '#1', 'growth' => 45],
                        ['name' => 'Fertilizzante organico', 'sales_rank' => '#3', 'growth' => 32],
                        ['name' => 'Lampade per piante', 'sales_rank' => '#5', 'growth' => 28],
                        ['name' => 'Terriccio specifico cactus', 'sales_rank' => '#8', 'growth' => 22],
                    ],
                    'categories' => [
                        ['name' => 'Giardinaggio', 'growth' => 18.5],
                        ['name' => 'Piante da interno', 'growth' => 35.2],
                        ['name' => 'Attrezzi giardinaggio', 'growth' => 12.8],
                    ],
                ],
                'ebay' => [
                    'hot_searches' => [
                        ['term' => 'cactus rari', 'searches' => 15000, 'growth' => 55],
                        ['term' => 'piante grasse', 'searches' => 25000, 'growth' => 42],
                        ['term' => 'bonsai', 'searches' => 18000, 'growth' => 28],
                    ],
                ],
                'etsy' => [
                    'trending_tags' => [
                        ['tag' => 'plant decor', 'listings' => 45000, 'growth' => 38],
                        ['tag' => 'botanical art', 'listings' => 32000, 'growth' => 25],
                        ['tag' => 'plant accessories', 'listings' => 28000, 'growth' => 41],
                    ],
                ],
            ];
        });
    }
}
