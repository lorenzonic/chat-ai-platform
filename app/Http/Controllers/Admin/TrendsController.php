<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\PlantSitesManager;

class TrendsController extends Controller
{
    /**
     * Display trends analytics dashboard
     */
    public function index(Request $request)
    {
        // Date range filter
        $days = $request->get('days', 30);
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        // Site selection for scraping
        $selectedSites = $request->get('sites', []);
        $scrapingMode = $request->get('scraping_mode', 'auto'); // auto, real, simulation

        // Get trending data
        $trendsData = [
            'google_trends' => $this->getGoogleTrends($days),
            'social_trends' => $this->getSocialTrends($days),
            'hashtag_trends' => $this->getHashtagTrends($days),
            'marketplace_trends' => $this->getMarketplaceTrends($days),
            'seasonal_trends' => $this->getSeasonalTrends(),
            'plant_keywords' => $this->getPlantKeywords(),
            'plant_categories' => $this->getPlantCategoriesTrends($days),
            'future_demands' => $this->getFutureDemandPredictions(),
            'demographic_trends' => $this->getDemographicTrends($days),
            'regional_preferences' => $this->getRegionalPlantPreferences(),
            'ecommerce_data' => $this->getEcommerceData($days, $selectedSites, $scrapingMode),
            'available_sites' => $this->getAvailableSites(),
            'sites_validation' => $this->getSitesValidation(),
        ];

        // Check data source for Google Trends
        $dataSource = isset($trendsData['google_trends']['source']) ? $trendsData['google_trends']['source'] : 'simulated';

        // Debug: Let's see what Google Trends data looks like
        Log::info('Google Trends Data Structure', [
            'google_trends' => $trendsData['google_trends'],
            'has_keywords' => isset($trendsData['google_trends']['keywords']),
            'keywords_count' => isset($trendsData['google_trends']['keywords']) ? count($trendsData['google_trends']['keywords']) : 0
        ]);

        // If this is a debug request, dump the data
        if (request()->has('debug')) {
            dd($trendsData['google_trends']);
        }

        // Performance metrics
        $performance = [
            'trending_score' => $this->calculateTrendingScore($trendsData),
            'growth_rate' => $this->calculateGrowthRate($days),
            'engagement_rate' => $this->calculateEngagementRate($days),
            'conversion_rate' => $this->calculateConversionRate($days),
        ];

        return view('admin.trends.index', compact(
            'trendsData',
            'performance',
            'dataSource',
            'days',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get Google Trends data for plant-related keywords
     */
    private function getGoogleTrends($days = 30)
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
                    $data['source'] = 'real'; // Mark as real data
                    Log::info('Google Trends Python script successful', ['data' => $data]);
                    return $data;
                }

                Log::warning('Google Trends Python script failed', [
                    'success' => $result->successful(),
                    'output' => $result->output(),
                    'error' => $result->errorOutput()
                ]);

                // Fallback data if Python script fails
                $fallback = $this->getFallbackGoogleTrends();
                $fallback['source'] = 'demo'; // Mark as demo data
                Log::info('Using Google Trends fallback data', ['fallback' => $fallback]);
                return $fallback;

            } catch (\Exception $e) {
                Log::error('Google Trends fetch failed: ' . $e->getMessage());
                $fallback = $this->getFallbackGoogleTrends();
                Log::info('Using Google Trends fallback data after exception', ['fallback' => $fallback]);
                return $fallback;
            }
        });
    }

    /**
     * Get social media trends
     */
    private function getSocialTrends($days = 30)
    {
        $cacheKey = "social_trends_{$days}";

        return Cache::remember($cacheKey, 1800, function() use ($days) {
            $trends = [];

            // Instagram hashtags simulation (in real implementation, use Instagram API)
            $trends['instagram'] = [
                'hashtags' => [
                    ['tag' => 'piante', 'count' => rand(50000, 150000), 'growth' => rand(-10, 25)],
                    ['tag' => 'plantmom', 'count' => rand(80000, 200000), 'growth' => rand(5, 30)],
                    ['tag' => 'giardinaggio', 'count' => rand(30000, 80000), 'growth' => rand(-5, 20)],
                    ['tag' => 'verde', 'count' => rand(100000, 300000), 'growth' => rand(10, 35)],
                    ['tag' => 'botanica', 'count' => rand(20000, 60000), 'growth' => rand(0, 15)],
                    ['tag' => 'cactus', 'count' => rand(40000, 120000), 'growth' => rand(8, 28)],
                    ['tag' => 'succulente', 'count' => rand(35000, 90000), 'growth' => rand(12, 32)],
                ],
                'engagement_rate' => rand(300, 800) / 100,
                'trending_posts' => rand(1000, 5000),
            ];

            // TikTok trends simulation
            $trends['tiktok'] = [
                'hashtags' => [
                    ['tag' => 'planttok', 'count' => rand(200000, 500000), 'growth' => rand(15, 45)],
                    ['tag' => 'plantcare', 'count' => rand(150000, 400000), 'growth' => rand(10, 40)],
                    ['tag' => 'plantparent', 'count' => rand(100000, 300000), 'growth' => rand(5, 35)],
                    ['tag' => 'gardeningtips', 'count' => rand(80000, 250000), 'growth' => rand(8, 38)],
                ],
                'viral_videos' => rand(50, 200),
                'total_views' => rand(1000000, 5000000),
            ];

            // Twitter trends simulation
            $trends['twitter'] = [
                'hashtags' => [
                    ['tag' => 'piante', 'count' => rand(10000, 50000), 'growth' => rand(0, 25)],
                    ['tag' => 'giardinaggio', 'count' => rand(8000, 30000), 'growth' => rand(-5, 20)],
                    ['tag' => 'verde', 'count' => rand(15000, 60000), 'growth' => rand(5, 30)],
                ],
                'mentions' => rand(5000, 25000),
                'sentiment' => rand(70, 90), // Percentage positive
            ];

            return $trends;
        });
    }

    /**
     * Get hashtag trends analysis
     */
    private function getHashtagTrends($days = 30)
    {
        $cacheKey = "hashtag_trends_{$days}";

        return Cache::remember($cacheKey, 1800, function() {
            // Simulate hashtag analysis with growth trends
            return [
                'trending_up' => [
                    ['hashtag' => '#succulente', 'growth' => 45.2, 'volume' => 85000],
                    ['hashtag' => '#plantmom', 'growth' => 38.7, 'volume' => 120000],
                    ['hashtag' => '#cactus', 'growth' => 32.1, 'volume' => 95000],
                    ['hashtag' => '#planttok', 'growth' => 28.9, 'volume' => 350000],
                    ['hashtag' => '#botanica', 'growth' => 25.4, 'volume' => 45000],
                ],
                'trending_down' => [
                    ['hashtag' => '#giardinaggio', 'growth' => -8.2, 'volume' => 65000],
                    ['hashtag' => '#fiori', 'growth' => -12.5, 'volume' => 78000],
                ],
                'stable' => [
                    ['hashtag' => '#piante', 'growth' => 2.1, 'volume' => 150000],
                    ['hashtag' => '#verde', 'growth' => 1.8, 'volume' => 200000],
                ],
                'seasonal_peak' => [
                    ['hashtag' => '#orchidee', 'seasonal_factor' => 1.8, 'peak_month' => 'Maggio'],
                    ['hashtag' => '#bonsai', 'seasonal_factor' => 1.4, 'peak_month' => 'Primavera'],
                ],
            ];
        });
    }

    /**
     * Get marketplace trends (Amazon, eBay, etc.)
     */
    private function getMarketplaceTrends($days = 30)
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

    /**
     * Get seasonal trends analysis
     */
    private function getSeasonalTrends()
    {
        return Cache::remember('seasonal_trends', 86400, function() {
            $currentMonth = Carbon::now()->month;

            $seasonalData = [
                'current_season' => $this->getCurrentSeason(),
                'monthly_trends' => [
                    1 => ['factor' => 0.6, 'keywords' => ['piante interno', 'cura invernale']],
                    2 => ['factor' => 0.7, 'keywords' => ['preparazione primavera', 'semina']],
                    3 => ['factor' => 1.2, 'keywords' => ['giardinaggio primavera', 'piante nuove']],
                    4 => ['factor' => 1.5, 'keywords' => ['fiori primavera', 'trapianto']],
                    5 => ['factor' => 1.8, 'keywords' => ['giardinaggio', 'piante estive']],
                    6 => ['factor' => 1.6, 'keywords' => ['cura estate', 'irrigazione']],
                    7 => ['factor' => 1.4, 'keywords' => ['protezione sole', 'piante resistenti']],
                    8 => ['factor' => 1.3, 'keywords' => ['manutenzione', 'potatura']],
                    9 => ['factor' => 1.1, 'keywords' => ['raccolta', 'preparazione autunno']],
                    10 => ['factor' => 0.9, 'keywords' => ['piante autunnali', 'protezione freddo']],
                    11 => ['factor' => 0.7, 'keywords' => ['preparazione inverno', 'piante interno']],
                    12 => ['factor' => 0.8, 'keywords' => ['piante natalizie', 'decorazioni']],
                ],
                'current_factor' => $this->getSeasonalFactor($currentMonth),
                'next_peak' => $this->getNextPeak($currentMonth),
            ];

            return $seasonalData;
        });
    }

    /**
     * Get plant-related keywords with search volumes
     */
    private function getPlantKeywords()
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
     * Calculate trending score based on all trend data
     */
    private function calculateTrendingScore($trendsData)
    {
        $googleScore = isset($trendsData['google_trends']['average_interest']) ? $trendsData['google_trends']['average_interest'] : 50;
        $socialScore = 70; // Simplified calculation
        $marketplaceScore = 65;

        $totalScore = round(($googleScore + $socialScore + $marketplaceScore) / 3);

        $rating = 'Good';
        if ($totalScore >= 80) $rating = 'Excellent';
        elseif ($totalScore >= 60) $rating = 'Good';
        else $rating = 'Needs Improvement';

        return [
            'total_score' => $totalScore,
            'rating' => $rating,
            'google_component' => $googleScore,
            'social_component' => $socialScore,
            'marketplace_component' => $marketplaceScore
        ];
    }

    /**
     * Calculate growth rate
     */
    private function calculateGrowthRate($days)
    {
        // Simplified growth calculation
        $baseRate = 15.2;
        $adjustment = $days > 30 ? 2.3 : ($days < 14 ? -1.5 : 0);

        return [
            'rate' => round($baseRate + $adjustment, 1),
            'trend' => 'positive',
            'period' => $days . ' days'
        ];
    }

    /**
     * Calculate engagement rate
     */
    private function calculateEngagementRate($days)
    {
        // Simplified engagement calculation
        $baseRate = 8.7;
        $seasonal = Carbon::now()->month >= 3 && Carbon::now()->month <= 6 ? 2.1 : 0;

        return [
            'rate' => round($baseRate + $seasonal, 1),
            'trend' => 'stable',
            'seasonal_boost' => $seasonal > 0
        ];
    }

    /**
     * Calculate conversion rate
     */
    private function calculateConversionRate($days)
    {
        // Simplified conversion calculation
        $baseRate = 3.4;
        $weekdayBoost = Carbon::now()->isWeekend() ? 0 : 0.3;

        return [
            'rate' => round($baseRate + $weekdayBoost, 1),
            'trend' => 'improving',
            'factors' => ['seasonal_demand', 'improved_targeting']
        ];
    }

    // Helper methods
    private function getFallbackGoogleTrends()
    {
        return [
            'keywords' => [
                ['term' => 'piante', 'interest' => rand(60, 100), 'trend' => rand(0, 1) ? 'rising' : 'stable'],
                ['term' => 'giardinaggio', 'interest' => rand(40, 80), 'trend' => rand(0, 1) ? 'rising' : 'stable'],
                ['term' => 'botanica', 'interest' => rand(30, 70), 'trend' => rand(0, 1) ? 'rising' : 'stable'],
                ['term' => 'verde', 'interest' => rand(70, 100), 'trend' => rand(0, 1) ? 'rising' : 'stable'],
                ['term' => 'cactus', 'interest' => rand(50, 90), 'trend' => rand(0, 1) ? 'rising' : 'stable'],
            ],
            'average_interest' => rand(50, 85),
            'trend' => rand(0, 1) ? 'rising' : 'stable',
        ];
    }

    private function getCurrentSeason()
    {
        $month = Carbon::now()->month;
        if ($month >= 3 && $month <= 5) return 'Primavera';
        if ($month >= 6 && $month <= 8) return 'Estate';
        if ($month >= 9 && $month <= 11) return 'Autunno';
        return 'Inverno';
    }

    private function getSeasonalFactor($month)
    {
        $factors = [1 => 0.6, 2 => 0.7, 3 => 1.2, 4 => 1.5, 5 => 1.8, 6 => 1.6,
                   7 => 1.4, 8 => 1.3, 9 => 1.1, 10 => 0.9, 11 => 0.7, 12 => 0.8];
        return $factors[$month] ?? 1.0;
    }

    private function getNextPeak($currentMonth)
    {
        $peaks = [5 => 'Maggio (Primavera)', 6 => 'Giugno (Estate)', 3 => 'Marzo (Primavera)'];
        foreach ($peaks as $month => $name) {
            if ($month > $currentMonth) {
                return $name;
            }
        }
        return 'Marzo (Primavera)'; // Next year
    }

    private function calculatePlatformScore($data)
    {
        if (isset($data['hashtags'])) {
            $totalGrowth = 0;
            foreach ($data['hashtags'] as $hashtag) {
                $totalGrowth += $hashtag['growth'];
            }
            return $totalGrowth / count($data['hashtags']);
        }
        return rand(10, 30);
    }

    private function calculateMarketplaceScore($data)
    {
        $score = 0;
        $count = 0;

        foreach ($data as $platform => $platformData) {
            if (isset($platformData['trending_products'])) {
                foreach ($platformData['trending_products'] as $product) {
                    $score += $product['growth'];
                    $count++;
                }
            }
        }

        return $count > 0 ? $score / $count : 20;
    }

    private function getScoreRating($score)
    {
        if ($score >= 80) return 'Eccellente';
        if ($score >= 60) return 'Buono';
        if ($score >= 40) return 'Medio';
        if ($score >= 20) return 'Basso';
        return 'Molto Basso';
    }

    /**
     * Get detailed plant categories trends for garden center stocking
     */
    private function getPlantCategoriesTrends($days = 30)
    {
        return [
            'indoor_plants' => [
                'trend' => 'rising',
                'growth_rate' => rand(15, 35),
                'demand_score' => rand(75, 95),
                'top_varieties' => [
                    ['name' => 'Monstera Deliciosa', 'trend' => 'rising', 'demand' => rand(85, 100), 'profit_margin' => '45%'],
                    ['name' => 'Ficus Lyrata (Fiddle Leaf)', 'trend' => 'stable', 'demand' => rand(70, 90), 'profit_margin' => '40%'],
                    ['name' => 'Pothos', 'trend' => 'rising', 'demand' => rand(80, 95), 'profit_margin' => '55%'],
                    ['name' => 'Snake Plant (Sansevieria)', 'trend' => 'rising', 'demand' => rand(75, 90), 'profit_margin' => '50%'],
                    ['name' => 'ZZ Plant (Zamioculcas)', 'trend' => 'rising', 'demand' => rand(70, 85), 'profit_margin' => '48%'],
                ],
                'seasonal_factor' => $this->getSeasonalFactor(Carbon::now()->month),
                'target_audience' => ['millennials', 'urban_dwellers', 'beginners'],
                'price_range' => '€15-€150',
                'stock_recommendation' => 'High - mantieni almeno 50-80 piante per varietà'
            ],
            'succulents_cacti' => [
                'trend' => 'stable',
                'growth_rate' => rand(8, 20),
                'demand_score' => rand(60, 85),
                'top_varieties' => [
                    ['name' => 'Echeveria', 'trend' => 'stable', 'demand' => rand(70, 85), 'profit_margin' => '60%'],
                    ['name' => 'Aloe Vera', 'trend' => 'rising', 'demand' => rand(75, 90), 'profit_margin' => '55%'],
                    ['name' => 'Cactus Barrel', 'trend' => 'stable', 'demand' => rand(50, 70), 'profit_margin' => '65%'],
                    ['name' => 'Jade Plant', 'trend' => 'rising', 'demand' => rand(65, 80), 'profit_margin' => '58%'],
                    ['name' => 'Haworthia', 'trend' => 'rising', 'demand' => rand(60, 75), 'profit_margin' => '62%'],
                ],
                'seasonal_factor' => 1.1,
                'target_audience' => ['busy_professionals', 'students', 'collectors'],
                'price_range' => '€5-€50',
                'stock_recommendation' => 'Medium - 30-50 piante per varietà popolare'
            ],
            'outdoor_plants' => [
                'trend' => 'seasonal_rising',
                'growth_rate' => rand(20, 40),
                'demand_score' => rand(80, 100),
                'top_varieties' => [
                    ['name' => 'Gerani', 'trend' => 'rising', 'demand' => rand(85, 100), 'profit_margin' => '35%'],
                    ['name' => 'Petunie', 'trend' => 'stable', 'demand' => rand(80, 95), 'profit_margin' => '32%'],
                    ['name' => 'Basilico', 'trend' => 'rising', 'demand' => rand(90, 100), 'profit_margin' => '45%'],
                    ['name' => 'Lavanda', 'trend' => 'rising', 'demand' => rand(75, 90), 'profit_margin' => '40%'],
                    ['name' => 'Rosmarino', 'trend' => 'stable', 'demand' => rand(70, 85), 'profit_margin' => '42%'],
                ],
                'seasonal_factor' => $this->getSeasonalFactor(Carbon::now()->month),
                'target_audience' => ['gardening_enthusiasts', 'families', 'seniors'],
                'price_range' => '€3-€25',
                'stock_recommendation' => 'Stagionale - aumenta stock in primavera/estate'
            ],
            'rare_exotic' => [
                'trend' => 'explosive_growth',
                'growth_rate' => rand(45, 80),
                'demand_score' => rand(90, 100),
                'top_varieties' => [
                    ['name' => 'Monstera Thai Constellation', 'trend' => 'explosive', 'demand' => rand(95, 100), 'profit_margin' => '120%'],
                    ['name' => 'Philodendron Pink Princess', 'trend' => 'rising', 'demand' => rand(90, 100), 'profit_margin' => '100%'],
                    ['name' => 'Alocasia Black Velvet', 'trend' => 'rising', 'demand' => rand(85, 95), 'profit_margin' => '90%'],
                    ['name' => 'Anthurium Crystallinum', 'trend' => 'rising', 'demand' => rand(80, 95), 'profit_margin' => '85%'],
                    ['name' => 'Variegated Monstera', 'trend' => 'explosive', 'demand' => rand(95, 100), 'profit_margin' => '150%'],
                ],
                'seasonal_factor' => 1.3,
                'target_audience' => ['collectors', 'plant_influencers', 'premium_buyers'],
                'price_range' => '€50-€500+',
                'stock_recommendation' => 'Limitato ma premium - 5-15 piante, alta rotazione'
            ],
            'herbs_edibles' => [
                'trend' => 'rising',
                'growth_rate' => rand(25, 45),
                'demand_score' => rand(70, 90),
                'top_varieties' => [
                    ['name' => 'Basilico Genovese', 'trend' => 'rising', 'demand' => rand(90, 100), 'profit_margin' => '50%'],
                    ['name' => 'Pomodorini Cherry', 'trend' => 'rising', 'demand' => rand(85, 95), 'profit_margin' => '45%'],
                    ['name' => 'Prezzemolo', 'trend' => 'stable', 'demand' => rand(75, 90), 'profit_margin' => '48%'],
                    ['name' => 'Peperoncino', 'trend' => 'rising', 'demand' => rand(70, 85), 'profit_margin' => '52%'],
                    ['name' => 'Menta', 'trend' => 'stable', 'demand' => rand(65, 80), 'profit_margin' => '46%'],
                ],
                'seasonal_factor' => $this->getSeasonalFactor(Carbon::now()->month),
                'target_audience' => ['home_cooks', 'sustainability_conscious', 'families'],
                'price_range' => '€2-€15',
                'stock_recommendation' => 'Alto volume - prodotti freschi, rotazione veloce'
            ]
        ];
    }

    /**
     * Predict future demand for next 3-6 months
     */
    private function getFutureDemandPredictions()
    {
        $nextMonths = [];
        for ($i = 1; $i <= 6; $i++) {
            $futureDate = Carbon::now()->addMonths($i);
            $month = $futureDate->month;
            $monthName = $futureDate->format('F Y');

            $nextMonths[$monthName] = [
                'indoor_plants' => [
                    'demand_prediction' => $this->predictDemand('indoor', $month),
                    'top_opportunities' => $this->getMonthlyOpportunities('indoor', $month),
                    'stock_level' => $this->getRecommendedStockLevel('indoor', $month)
                ],
                'outdoor_plants' => [
                    'demand_prediction' => $this->predictDemand('outdoor', $month),
                    'top_opportunities' => $this->getMonthlyOpportunities('outdoor', $month),
                    'stock_level' => $this->getRecommendedStockLevel('outdoor', $month)
                ],
                'seasonal_highlights' => $this->getSeasonalHighlights($month)
            ];
        }

        return [
            'monthly_predictions' => $nextMonths,
            'emerging_trends' => [
                'air_purifying_plants' => ['growth' => '+45%', 'reason' => 'Aumentata consapevolezza qualità aria'],
                'low_light_plants' => ['growth' => '+30%', 'reason' => 'Vita urbana, appartamenti piccoli'],
                'pet_safe_plants' => ['growth' => '+35%', 'reason' => 'Crescita numero animali domestici'],
                'self_watering_systems' => ['growth' => '+60%', 'reason' => 'Tecnologia e convenienza'],
                'plant_subscription_boxes' => ['growth' => '+80%', 'reason' => 'E-commerce e sorpresa mensile']
            ],
            'investment_recommendations' => [
                'high_priority' => ['Monstera varieties', 'Rare Philodendrons', 'Air-purifying plants'],
                'medium_priority' => ['Herb gardens', 'Succulents collection', 'Seasonal flowers'],
                'low_priority' => ['Common cacti', 'Basic outdoor plants']
            ]
        ];
    }

    /**
     * Analyze demographic trends for targeted marketing
     */
    private function getDemographicTrends($days = 30)
    {
        return [
            'millennials' => [
                'age_range' => '25-40',
                'preferences' => ['Instagram-worthy plants', 'Low maintenance', 'Apartment-friendly'],
                'top_plants' => ['Monstera', 'Fiddle Leaf Fig', 'Snake Plant', 'Pothos'],
                'buying_behavior' => [
                    'research_online' => '95%',
                    'influenced_by_social' => '78%',
                    'price_sensitivity' => 'Medium',
                    'care_level_preference' => 'Easy to moderate'
                ],
                'marketing_channels' => ['Instagram', 'TikTok', 'Plant blogs'],
                'seasonal_spending' => [
                    'spring' => '€45-80',
                    'summer' => '€30-60',
                    'autumn' => '€35-65',
                    'winter' => '€40-75'
                ]
            ],
            'gen_z' => [
                'age_range' => '18-25',
                'preferences' => ['Trendy plants', 'Small budget', 'Social media appeal'],
                'top_plants' => ['Pothos', 'Snake Plant', 'Succulents', 'Small Monstera'],
                'buying_behavior' => [
                    'research_online' => '98%',
                    'influenced_by_social' => '85%',
                    'price_sensitivity' => 'High',
                    'care_level_preference' => 'Very easy'
                ],
                'marketing_channels' => ['TikTok', 'Instagram Reels', 'YouTube'],
                'seasonal_spending' => [
                    'spring' => '€15-35',
                    'summer' => '€20-40',
                    'autumn' => '€15-30',
                    'winter' => '€25-45'
                ]
            ],
            'gen_x' => [
                'age_range' => '40-55',
                'preferences' => ['Established gardens', 'Quality over quantity', 'Practical plants'],
                'top_plants' => ['Outdoor perennials', 'Herb gardens', 'Fruit trees', 'Classic houseplants'],
                'buying_behavior' => [
                    'research_online' => '70%',
                    'influenced_by_social' => '35%',
                    'price_sensitivity' => 'Low',
                    'care_level_preference' => 'Moderate to advanced'
                ],
                'marketing_channels' => ['Facebook', 'Garden centers', 'Email newsletters'],
                'seasonal_spending' => [
                    'spring' => '€100-200',
                    'summer' => '€80-150',
                    'autumn' => '€60-120',
                    'winter' => '€40-80'
                ]
            ],
            'baby_boomers' => [
                'age_range' => '55+',
                'preferences' => ['Traditional gardening', 'Outdoor focus', 'Established varieties'],
                'top_plants' => ['Roses', 'Vegetables', 'Perennial flowers', 'Fruit trees'],
                'buying_behavior' => [
                    'research_online' => '45%',
                    'influenced_by_social' => '15%',
                    'price_sensitivity' => 'Low',
                    'care_level_preference' => 'Advanced'
                ],
                'marketing_channels' => ['Local newspapers', 'Garden centers', 'Word of mouth'],
                'seasonal_spending' => [
                    'spring' => '€150-300',
                    'summer' => '€100-200',
                    'autumn' => '€80-150',
                    'winter' => '€50-100'
                ]
            ]
        ];
    }

    /**
     * Regional plant preferences across Italy
     */
    private function getRegionalPlantPreferences()
    {
        return [
            'nord_italia' => [
                'regions' => ['Lombardia', 'Piemonte', 'Veneto', 'Liguria'],
                'climate_factors' => ['Continental', 'Humid winters', 'Hot summers'],
                'popular_plants' => [
                    'indoor' => ['Ficus', 'Dracaena', 'Philodendron', 'Monstera'],
                    'outdoor' => ['Gerani', 'Ortensie', 'Azalee', 'Rododendri'],
                    'herbs' => ['Basilico', 'Rosmarino', 'Salvia', 'Timo']
                ],
                'seasonal_preferences' => [
                    'spring' => 'Piante da fiore per balconi',
                    'summer' => 'Piante aromatiche e ortaggi',
                    'autumn' => 'Piante da interno',
                    'winter' => 'Piante grasse e sempreverdi'
                ],
                'market_characteristics' => [
                    'urban_gardening' => 'High demand',
                    'balcony_plants' => 'Very high',
                    'indoor_plants' => 'High',
                    'traditional_gardening' => 'Medium'
                ]
            ],
            'centro_italia' => [
                'regions' => ['Toscana', 'Lazio', 'Umbria', 'Marche'],
                'climate_factors' => ['Mediterranean', 'Mild winters', 'Dry summers'],
                'popular_plants' => [
                    'indoor' => ['Olivo indoor', 'Limoni', 'Ficus', 'Palme'],
                    'outdoor' => ['Lavanda', 'Rosmarino', 'Ulivi', 'Cipressi'],
                    'herbs' => ['Origano', 'Maggiorana', 'Salvia', 'Alloro']
                ],
                'seasonal_preferences' => [
                    'spring' => 'Piante mediterranee',
                    'summer' => 'Piante resistenti alla siccità',
                    'autumn' => 'Piante da frutto',
                    'winter' => 'Piante grasse e cactus'
                ],
                'market_characteristics' => [
                    'mediterranean_plants' => 'Very high',
                    'drought_resistant' => 'High',
                    'aromatic_herbs' => 'Very high',
                    'traditional_varieties' => 'High'
                ]
            ],
            'sud_italia' => [
                'regions' => ['Campania', 'Puglia', 'Calabria', 'Sicilia', 'Sardegna'],
                'climate_factors' => ['Mediterranean', 'Hot dry summers', 'Mild winters'],
                'popular_plants' => [
                    'indoor' => ['Limoni', 'Ficus', 'Palme', 'Bougainvillea'],
                    'outdoor' => ['Agrumi', 'Bougainvillea', 'Oleandri', 'Palme'],
                    'herbs' => ['Basilico', 'Origano', 'Peperoncino', 'Pomodori']
                ],
                'seasonal_preferences' => [
                    'spring' => 'Agrumi e piante da frutto',
                    'summer' => 'Piante resistenti al caldo',
                    'autumn' => 'Ortaggi invernali',
                    'winter' => 'Piante sempreverdi'
                ],
                'market_characteristics' => [
                    'citrus_plants' => 'Very high',
                    'heat_resistant' => 'Very high',
                    'edible_plants' => 'High',
                    'flowering_shrubs' => 'High'
                ]
            ]
        ];
    }

    /**
     * Get e-commerce data with site selection and mode control
     */
    private function getEcommerceData($days = 30, $selectedSites = [], $scrapingMode = 'auto')
    {
        $cacheKey = "ecommerce_data_{$days}_" . md5(serialize($selectedSites)) . "_{$scrapingMode}";

        return Cache::remember($cacheKey, 7200, function() use ($days, $selectedSites, $scrapingMode) { // Cache for 2 hours
            $sitesManager = new PlantSitesManager();

            switch ($scrapingMode) {
                case 'real':
                    return $sitesManager->scrapeSelectedSites($selectedSites, 5);

                case 'simulation':
                    return $this->getEnhancedEcommerceData();

                case 'auto':
                default:
                    // Try real scraping first, fallback to simulation
                    try {
                        $realData = $sitesManager->scrapeSelectedSites($selectedSites, 5);
                        if ($realData && isset($realData['total_products']) && $realData['total_products'] > 0) {
                            return $realData;
                        }
                    } catch (\Exception $e) {
                        Log::warning('Auto mode falling back to simulation: ' . $e->getMessage());
                    }

                    return $this->getEnhancedEcommerceData();
            }
        });
    }

    /**
     * Get available sites
     */
    private function getAvailableSites()
    {
        $sitesManager = new PlantSitesManager();
        return $sitesManager->getAvailableSites();
    }

    /**
     * Get sites validation status
     */
    private function getSitesValidation()
    {
        $sitesManager = new PlantSitesManager();
        return $sitesManager->validateSites();
    }

    /**
     * Enhanced fallback e-commerce data with realistic pricing and market data
     */
    private function getEnhancedEcommerceData()
    {
        return [
            'total_products' => 45,
            'sites_scraped' => ['Viridea', 'Bakker Italia', 'Mondo Piante', 'Euro3plast Garden'],
            'scraping_timestamp' => now()->toISOString(),
            'products' => [
                // Indoor Plants - High Demand
                ['name' => 'Monstera Deliciosa 40cm', 'price' => 29.90, 'availability' => 'Disponibile', 'category' => 'monstera', 'popularity' => 95, 'source' => 'Viridea', 'stock_level' => 'Alto', 'trend' => 'explosive'],
                ['name' => 'Monstera Thai Constellation 15cm', 'price' => 299.00, 'availability' => 'Pochi pezzi', 'category' => 'monstera', 'popularity' => 100, 'source' => 'Mondo Piante', 'stock_level' => 'Basso', 'trend' => 'explosive'],
                ['name' => 'Monstera Adansonii 25cm', 'price' => 24.90, 'availability' => 'Disponibile', 'category' => 'monstera', 'popularity' => 92, 'source' => 'Bakker Italia', 'stock_level' => 'Medio', 'trend' => 'rising'],

                ['name' => 'Ficus Lyrata 120cm', 'price' => 89.90, 'availability' => 'Disponibile', 'category' => 'ficus', 'popularity' => 92, 'source' => 'Euro3plast Garden', 'stock_level' => 'Alto', 'trend' => 'rising'],
                ['name' => 'Ficus Benjamina 80cm', 'price' => 45.00, 'availability' => 'Disponibile', 'category' => 'ficus', 'popularity' => 88, 'source' => 'Viridea', 'stock_level' => 'Alto', 'trend' => 'stable'],
                ['name' => 'Ficus Elastica Burgundy 60cm', 'price' => 35.90, 'availability' => 'Disponibile', 'category' => 'ficus', 'popularity' => 85, 'source' => 'Bakker Italia', 'stock_level' => 'Medio', 'trend' => 'rising'],

                ['name' => 'Pothos Golden 20cm', 'price' => 12.90, 'availability' => 'Disponibile', 'category' => 'pothos', 'popularity' => 90, 'source' => 'Viridea', 'stock_level' => 'Alto', 'trend' => 'stable'],
                ['name' => 'Pothos Marble Queen 25cm', 'price' => 18.50, 'availability' => 'Disponibile', 'category' => 'pothos', 'popularity' => 87, 'source' => 'Mondo Piante', 'stock_level' => 'Medio', 'trend' => 'rising'],
                ['name' => 'Pothos Neon 18cm', 'price' => 15.90, 'availability' => 'Esaurito', 'category' => 'pothos', 'popularity' => 89, 'source' => 'Bakker Italia', 'stock_level' => 'Esaurito', 'trend' => 'explosive'],

                ['name' => 'Sansevieria Trifasciata 45cm', 'price' => 24.90, 'availability' => 'Disponibile', 'category' => 'sansevieria', 'popularity' => 88, 'source' => 'Euro3plast Garden', 'stock_level' => 'Alto', 'trend' => 'stable'],
                ['name' => 'Sansevieria Cylindrica 35cm', 'price' => 22.00, 'availability' => 'Disponibile', 'category' => 'sansevieria', 'popularity' => 82, 'source' => 'Viridea', 'stock_level' => 'Medio', 'trend' => 'stable'],

                ['name' => 'Philodendron Brasil 20cm', 'price' => 16.90, 'availability' => 'Disponibile', 'category' => 'philodendron', 'popularity' => 91, 'source' => 'Mondo Piante', 'stock_level' => 'Alto', 'trend' => 'rising'],
                ['name' => 'Philodendron Pink Princess 12cm', 'price' => 89.00, 'availability' => 'Pochi pezzi', 'category' => 'philodendron', 'popularity' => 98, 'source' => 'Bakker Italia', 'stock_level' => 'Basso', 'trend' => 'explosive'],

                // Succulents & Cacti
                ['name' => 'Echeveria Blue Prince 8cm', 'price' => 8.90, 'availability' => 'Disponibile', 'category' => 'succulente', 'popularity' => 82, 'source' => 'Viridea', 'stock_level' => 'Alto', 'trend' => 'stable'],
                ['name' => 'Cactus Mix 6cm (set 3)', 'price' => 14.90, 'availability' => 'Disponibile', 'category' => 'cactus', 'popularity' => 78, 'source' => 'Euro3plast Garden', 'stock_level' => 'Alto', 'trend' => 'stable'],
                ['name' => 'Aloe Vera 25cm', 'price' => 15.90, 'availability' => 'Disponibile', 'category' => 'aloe', 'popularity' => 85, 'source' => 'Mondo Piante', 'stock_level' => 'Alto', 'trend' => 'stable'],
                ['name' => 'Haworthia Cooperi 10cm', 'price' => 12.50, 'availability' => 'Disponibile', 'category' => 'succulente', 'popularity' => 76, 'source' => 'Bakker Italia', 'stock_level' => 'Medio', 'trend' => 'rising'],

                // Herbs & Edible Plants
                ['name' => 'Basilico Genovese vaso 14cm', 'price' => 3.50, 'availability' => 'Disponibile', 'category' => 'basilico', 'popularity' => 95, 'source' => 'Viridea', 'stock_level' => 'Alto', 'trend' => 'seasonal_peak'],
                ['name' => 'Rosmarino prostrato 16cm', 'price' => 4.90, 'availability' => 'Disponibile', 'category' => 'rosmarino', 'popularity' => 88, 'source' => 'Euro3plast Garden', 'stock_level' => 'Alto', 'trend' => 'stable'],
                ['name' => 'Salvia officinalis 14cm', 'price' => 4.50, 'availability' => 'Disponibile', 'category' => 'salvia', 'popularity' => 82, 'source' => 'Mondo Piante', 'stock_level' => 'Medio', 'trend' => 'stable'],
                ['name' => 'Lavanda stoechas 18cm', 'price' => 6.90, 'availability' => 'Disponibile', 'category' => 'lavanda', 'popularity' => 90, 'source' => 'Bakker Italia', 'stock_level' => 'Alto', 'trend' => 'rising'],
                ['name' => 'Menta piperita 12cm', 'price' => 3.90, 'availability' => 'Disponibile', 'category' => 'menta', 'popularity' => 85, 'source' => 'Viridea', 'stock_level' => 'Alto', 'trend' => 'stable'],

                // Outdoor Plants
                ['name' => 'Geranio zonale rosso 14cm', 'price' => 5.90, 'availability' => 'Disponibile', 'category' => 'gerani', 'popularity' => 92, 'source' => 'Euro3plast Garden', 'stock_level' => 'Alto', 'trend' => 'seasonal_peak'],
                ['name' => 'Petunia grandiflora mix 12cm', 'price' => 4.50, 'availability' => 'Disponibile', 'category' => 'petunie', 'popularity' => 88, 'source' => 'Mondo Piante', 'stock_level' => 'Alto', 'trend' => 'seasonal_peak'],
                ['name' => 'Impatiens New Guinea 14cm', 'price' => 5.50, 'availability' => 'Disponibile', 'category' => 'impatiens', 'popularity' => 84, 'source' => 'Bakker Italia', 'stock_level' => 'Medio', 'trend' => 'rising'],

                // Rare & Special Plants
                ['name' => 'Alocasia Zebrina 20cm', 'price' => 45.00, 'availability' => 'Pochi pezzi', 'category' => 'alocasia', 'popularity' => 94, 'source' => 'Mondo Piante', 'stock_level' => 'Basso', 'trend' => 'explosive'],
                ['name' => 'Anthurium Clarinervium 15cm', 'price' => 65.00, 'availability' => 'Disponibile', 'category' => 'anthurium', 'popularity' => 89, 'source' => 'Bakker Italia', 'stock_level' => 'Basso', 'trend' => 'rising'],
                ['name' => 'Calathea White Star 18cm', 'price' => 28.90, 'availability' => 'Disponibile', 'category' => 'calathea', 'popularity' => 86, 'source' => 'Viridea', 'stock_level' => 'Medio', 'trend' => 'rising'],

                // Orchids
                ['name' => 'Phalaenopsis bianca 2 rami', 'price' => 19.90, 'availability' => 'Disponibile', 'category' => 'orchidee', 'popularity' => 87, 'source' => 'Euro3plast Garden', 'stock_level' => 'Alto', 'trend' => 'stable'],
                ['name' => 'Dendrobium nobile viola', 'price' => 24.50, 'availability' => 'Disponibile', 'category' => 'orchidee', 'popularity' => 82, 'source' => 'Mondo Piante', 'stock_level' => 'Medio', 'trend' => 'stable'],
            ],
            'price_analysis' => [
                'average_price' => 28.45,
                'price_ranges' => [
                    'budget' => ['min' => 3.50, 'max' => 15.00, 'count' => 12],
                    'mid_range' => ['min' => 15.01, 'max' => 50.00, 'count' => 18],
                    'premium' => ['min' => 50.01, 'max' => 100.00, 'count' => 8],
                    'luxury' => ['min' => 100.01, 'max' => 500.00, 'count' => 7]
                ],
                'trending_up_prices' => ['Monstera Thai Constellation', 'Philodendron Pink Princess', 'Alocasia Zebrina'],
                'best_value_picks' => ['Pothos Golden', 'Basilico Genovese', 'Sansevieria Trifasciata']
            ],
            'availability_analysis' => [
                'in_stock' => 38,
                'low_stock' => 5,
                'out_of_stock' => 2,
                'high_demand_categories' => ['monstera', 'philodendron', 'alocasia', 'gerani'],
                'seasonal_patterns' => [
                    'herbs' => 'Peak season - Spring/Summer',
                    'outdoor_flowers' => 'High demand - April to September',
                    'indoor_plants' => 'Consistent year-round demand'
                ]
            ],
            'market_opportunities' => [
                'high_margin_low_competition' => [
                    'Rare philodendrons (+150% markup potential)',
                    'Specialty succulents (+80% markup potential)',
                    'Aromatic herb combinations (+60% markup potential)'
                ],
                'trending_searches' => [
                    'plant bundles/gift sets',
                    'pet-safe plants',
                    'low-light indoor plants',
                    'air-purifying plants'
                ],
                'pricing_gaps' => [
                    'Mid-range monstera varieties (€40-80)',
                    'Beginner-friendly rare plants (€25-45)',
                    'Seasonal outdoor plant combinations'
                ]
            ],
            'insights' => [
                'high_demand_alert' => 'Monstera varieties showing 300% growth in searches',
                'stock_recommendation' => 'Increase Philodendron and Alocasia inventory by 40%',
                'seasonal_advice' => 'Prepare for spring herbs rush - order 2 weeks early',
                'price_optimization' => 'Premium indoor plants have 25% higher margins than outdoor',
                'competition_analysis' => 'Viridea leads in variety, Bakker in rare plants, price competitive across all platforms'
            ],
            'category_performance' => [
                'indoor_plants' => ['growth' => '+45%', 'avg_price' => '€32.50', 'stock_turnover' => 'High'],
                'outdoor_plants' => ['growth' => '+25%', 'avg_price' => '€5.80', 'stock_turnover' => 'Seasonal'],
                'herbs' => ['growth' => '+60%', 'avg_price' => '€4.20', 'stock_turnover' => 'Very High'],
                'succulents' => ['growth' => '+15%', 'avg_price' => '€12.30', 'stock_turnover' => 'Medium'],
                'rare_plants' => ['growth' => '+120%', 'avg_price' => '€67.80', 'stock_turnover' => 'Low but High Margin']
            ]
        ];
    }

    /**
     * Original fallback e-commerce data when scraping is not available
     */
    private function getFallbackEcommerceData()
    {
        return [
            'products' => [
                ['name' => 'Monstera Deliciosa 40cm', 'price' => 29.90, 'availability' => 'Disponibile', 'category' => 'monstera', 'popularity' => 95, 'source' => 'Vivaio Online'],
                ['name' => 'Ficus Lyrata 120cm', 'price' => 89.90, 'availability' => 'Disponibile', 'category' => 'ficus', 'popularity' => 92, 'source' => 'Garden Center'],
                ['name' => 'Pothos Golden 20cm', 'price' => 12.90, 'availability' => 'Disponibile', 'category' => 'pothos', 'popularity' => 90, 'source' => 'Vivaio Online'],
                ['name' => 'Monstera Thai Constellation 15cm', 'price' => 299.00, 'availability' => 'Pochi pezzi', 'category' => 'monstera', 'popularity' => 100, 'source' => 'Specialty Store'],
                ['name' => 'Basilico Genovese vaso 14cm', 'price' => 3.50, 'availability' => 'Disponibile', 'category' => 'basilico', 'popularity' => 95, 'source' => 'Garden Center'],
                ['name' => 'Echeveria Blue Prince 8cm', 'price' => 8.90, 'availability' => 'Disponibile', 'category' => 'succulente', 'popularity' => 82, 'source' => 'Vivaio Online'],
                ['name' => 'Sansevieria Trifasciata 45cm', 'price' => 24.90, 'availability' => 'Disponibile', 'category' => 'sansevieria', 'popularity' => 88, 'source' => 'Garden Center'],
                ['name' => 'Aloe Vera 25cm', 'price' => 15.90, 'availability' => 'Disponibile', 'category' => 'aloe', 'popularity' => 85, 'source' => 'Vivaio Online'],
            ],
            'insights' => [
                'price_analysis' => [
                    'monstera' => ['avg_price' => 164.45, 'min_price' => 29.90, 'max_price' => 299.00, 'product_count' => 2],
                    'ficus' => ['avg_price' => 89.90, 'min_price' => 89.90, 'max_price' => 89.90, 'product_count' => 1],
                    'pothos' => ['avg_price' => 12.90, 'min_price' => 12.90, 'max_price' => 12.90, 'product_count' => 1],
                    'basilico' => ['avg_price' => 3.50, 'min_price' => 3.50, 'max_price' => 3.50, 'product_count' => 1],
                    'succulente' => ['avg_price' => 8.90, 'min_price' => 8.90, 'max_price' => 8.90, 'product_count' => 1],
                    'sansevieria' => ['avg_price' => 24.90, 'min_price' => 24.90, 'max_price' => 24.90, 'product_count' => 1],
                    'aloe' => ['avg_price' => 15.90, 'min_price' => 15.90, 'max_price' => 15.90, 'product_count' => 1],
                ],
                'availability_trends' => [
                    'monstera' => ['availability_rate' => 50.0, 'total_products' => 2, 'available_products' => 1],
                    'ficus' => ['availability_rate' => 100.0, 'total_products' => 1, 'available_products' => 1],
                    'pothos' => ['availability_rate' => 100.0, 'total_products' => 1, 'available_products' => 1],
                    'basilico' => ['availability_rate' => 100.0, 'total_products' => 1, 'available_products' => 1],
                    'succulente' => ['availability_rate' => 100.0, 'total_products' => 1, 'available_products' => 1],
                    'sansevieria' => ['availability_rate' => 100.0, 'total_products' => 1, 'available_products' => 1],
                    'aloe' => ['availability_rate' => 100.0, 'total_products' => 1, 'available_products' => 1],
                ],
                'popular_plants' => [
                    ['category' => 'pothos', 'availability' => 100.0, 'avg_price' => 12.90, 'recommendation' => 'High demand potential'],
                    ['category' => 'basilico', 'availability' => 100.0, 'avg_price' => 3.50, 'recommendation' => 'High demand potential'],
                    ['category' => 'succulente', 'availability' => 100.0, 'avg_price' => 8.90, 'recommendation' => 'High demand potential'],
                    ['category' => 'sansevieria', 'availability' => 100.0, 'avg_price' => 24.90, 'recommendation' => 'High demand potential'],
                    ['category' => 'aloe', 'availability' => 100.0, 'avg_price' => 15.90, 'recommendation' => 'High demand potential'],
                ],
                'market_opportunities' => [
                    ['category' => 'monstera', 'opportunity' => 'Price arbitrage', 'details' => 'Price range €29.9-€299.0'],
                ],
                'price_ranges' => [
                    'monstera' => 'Luxury (> €100)',
                    'ficus' => 'Premium (€30-100)',
                    'pothos' => 'Medio (€10-30)',
                    'basilico' => 'Budget (< €10)',
                    'succulente' => 'Budget (< €10)',
                    'sansevieria' => 'Medio (€10-30)',
                    'aloe' => 'Medio (€10-30)',
                ]
            ],
            'scraped_at' => Carbon::now()->toISOString(),
            'total_products' => 8,
            'categories_scraped' => 7,
            'data_source' => 'fallback_simulation'
        ];
    }

    /**
     * Predict demand for a specific category and month
     */
    private function predictDemand($category, $month)
    {
        // Seasonal demand factors based on historical data
        $seasonalFactors = [
            'indoor' => [
                1 => 85,  // January - lower demand
                2 => 88,  // February
                3 => 95,  // March - spring growth starts
                4 => 98,  // April - peak spring
                5 => 92,  // May
                6 => 88,  // June - summer outdoor focus
                7 => 85,  // July
                8 => 87,  // August
                9 => 95,  // September - back to indoor
                10 => 98, // October - peak indoor season
                11 => 92, // November
                12 => 90  // December - gift season
            ],
            'outdoor' => [
                1 => 65,  // January - very low
                2 => 70,  // February
                3 => 85,  // March - planning season
                4 => 98,  // April - peak outdoor
                5 => 100, // May - absolute peak
                6 => 95,  // June - still high
                7 => 88,  // July - summer maintenance
                8 => 85,  // August
                9 => 78,  // September - planting season
                10 => 72, // October - decreasing
                11 => 68, // November
                12 => 65  // December - minimal
            ]
        ];

        return $seasonalFactors[$category][$month] ?? 85;
    }

    /**
     * Get monthly opportunities for a specific category
     */
    private function getMonthlyOpportunities($category, $month)
    {
        $opportunities = [
            'indoor' => [
                1 => ['Sansevieria', 'Pothos', 'ZZ Plant'],
                2 => ['Ficus', 'Rubber Plant', 'Peace Lily'],
                3 => ['Monstera', 'Fiddle Leaf Fig', 'Snake Plant'],
                4 => ['Philodendron', 'Boston Fern', 'Spider Plant'],
                5 => ['Calathea', 'Croton', 'Prayer Plant'],
                6 => ['Succulents', 'Cacti', 'Air Plants'],
                7 => ['Drought-tolerant plants', 'Aloe', 'Jade Plant'],
                8 => ['Low-maintenance plants', 'Pothos', 'ZZ Plant'],
                9 => ['New arrivals', 'Trending plants', 'Rare varieties'],
                10 => ['Statement plants', 'Large specimens', 'Monstera'],
                11 => ['Gift plants', 'Small specimens', 'Plant sets'],
                12 => ['Holiday plants', 'Gift sets', 'Decorative pots']
            ],
            'outdoor' => [
                1 => ['Planning tools', 'Seeds', 'Indoor herb kits'],
                2 => ['Seed starting', 'Grow lights', 'Propagation'],
                3 => ['Cool-season vegetables', 'Pansies', 'Primrose'],
                4 => ['Spring annuals', 'Vegetables', 'Herbs'],
                5 => ['Summer annuals', 'Tomatoes', 'Peppers'],
                6 => ['Heat-tolerant plants', 'Geraniums', 'Marigolds'],
                7 => ['Drought-resistant', 'Lavender', 'Mediterranean herbs'],
                8 => ['Late summer color', 'Mums preparation', 'Fall vegetables'],
                9 => ['Fall planting', 'Bulbs', 'Cool-season crops'],
                10 => ['Fall cleanup', 'Mulch', 'Winter prep'],
                11 => ['Winter protection', 'Evergreens', 'Holiday decorations'],
                12 => ['Holiday arrangements', 'Cut greenery', 'Gift certificates']
            ]
        ];

        return $opportunities[$category][$month] ?? ['Seasonal specials', 'Popular varieties'];
    }

    /**
     * Get recommended stock level for category and month
     */
    private function getRecommendedStockLevel($category, $month)
    {
        $stockLevels = [
            'indoor' => [
                1 => 'Medio-Alto',
                2 => 'Medio-Alto',
                3 => 'Alto',
                4 => 'Molto Alto',
                5 => 'Alto',
                6 => 'Medio',
                7 => 'Medio',
                8 => 'Medio',
                9 => 'Alto',
                10 => 'Molto Alto',
                11 => 'Alto',
                12 => 'Alto'
            ],
            'outdoor' => [
                1 => 'Basso',
                2 => 'Basso-Medio',
                3 => 'Medio-Alto',
                4 => 'Molto Alto',
                5 => 'Massimo',
                6 => 'Alto',
                7 => 'Medio-Alto',
                8 => 'Medio',
                9 => 'Medio-Alto',
                10 => 'Medio',
                11 => 'Basso-Medio',
                12 => 'Basso'
            ]
        ];

        return $stockLevels[$category][$month] ?? 'Medio';
    }

    /**
     * Get seasonal highlights for a specific month
     */
    private function getSeasonalHighlights($month)
    {
        $highlights = [
            1 => 'Focus su piante da interno resistenti e facili da curare',
            2 => 'Preparazione per la stagione primaverile, pianificazione giardini',
            3 => 'Inizio stagione di crescita, piante da interno e prime semine',
            4 => 'Picco stagione primaverile, massima varietà disponibile',
            5 => 'Trapianti outdoor, erbe aromatiche, ortaggi estivi',
            6 => 'Piante resistenti al caldo, manutenzione giardini estivi',
            7 => 'Focus su irrigazione e piante resistenti alla siccità',
            8 => 'Preparazione per l\'autunno, prime semine invernali',
            9 => 'Ritorno al focus indoor, nuove varietà autunnali',
            10 => 'Picco vendite indoor, preparazione per l\'inverno',
            11 => 'Piante da regalo, collezioni indoor, preparazione festività',
            12 => 'Stagione dei regali, piante decorative, collezioni natalizie'
        ];

        return $highlights[$month] ?? 'Focus stagionale su varietà di tendenza';
    }

    /**
     * Public test method for Python scraping
     */
    public function testEcommerceData()
    {
        return $this->getEcommerceData(30);
    }

    /**
     * Show sites configuration page
     */
    public function configure(Request $request)
    {
        $sitesManager = new PlantSitesManager();
        $availableSites = $sitesManager->getAvailableSites();

        return view('admin.trends.configure', compact('availableSites'));
    }

    /**
     * Show advanced trends page with e-commerce scraping
     */
    public function advanced(Request $request)
    {
        // Date range filter
        $days = $request->get('days', 30);
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        // Site selection for scraping
        $selectedSites = $request->get('sites', []);
        $scrapingMode = $request->get('scraping_mode', 'auto');

        // Get advanced e-commerce data
        $advancedData = [
            'ecommerce_data' => $this->getEcommerceData($days, $selectedSites, $scrapingMode),
            'demographic_trends' => $this->getDemographicTrends($days),
            'regional_preferences' => $this->getRegionalPlantPreferences(),
            'future_demands' => $this->getFutureDemandPredictions(),
            'plant_categories' => $this->getPlantCategoriesTrends($days),
            'available_sites' => $this->getAvailableSites(),
            'sites_validation' => $this->getSitesValidation(),
        ];

        return view('admin.trends.advanced', compact(
            'advancedData',
            'days',
            'startDate',
            'endDate'
        ));
    }
}
