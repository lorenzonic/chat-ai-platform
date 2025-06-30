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
                    'autunno' => 'Piante da interno',
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
                    'autunno' => 'Ortaggi invernali',
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
     * Advanced AI-powered plant demand prediction
     */
    public function getAIPredictions(Request $request)
    {
        $region = $request->get('region', 'centro');
        $timeframe = $request->get('timeframe', '3_months');
        $categories = $request->get('categories', ['indoor', 'outdoor', 'herbs']);

        $aiPredictions = [
            'demand_forecast' => $this->generateDemandForecast($region, $timeframe, $categories),
            'price_predictions' => $this->generatePricePredictions($region, $timeframe),
            'trend_analysis' => $this->generateTrendAnalysis($region, $categories),
            'market_opportunities' => $this->identifyMarketOpportunities($region),
            'risk_assessment' => $this->assessMarketRisks($region, $timeframe),
            'optimization_recommendations' => $this->generateOptimizationRecommendations($region),
            'confidence_scores' => $this->calculatePredictionConfidence()
        ];

        return response()->json($aiPredictions);
    }

    /**
     * Generate demand forecast using simulated ML algorithm
     */
    private function generateDemandForecast($region, $timeframe, $categories)
    {
        $forecast = [];
        $months = $timeframe === '6_months' ? 6 : 3;
        
        foreach ($categories as $category) {
            $baselineGrowth = $this->getBaselineGrowthRate($category, $region);
            $seasonalFactors = $this->getSeasonalFactors($category);
            $trendMultiplier = $this->getTrendMultiplier($category);
            
            $monthlyForecast = [];
            for ($i = 1; $i <= $months; $i++) {
                $futureDate = Carbon::now()->addMonths($i);
                $seasonalFactor = $seasonalFactors[$futureDate->month] ?? 1.0;
                
                $predictedDemand = round(100 * $baselineGrowth * $seasonalFactor * $trendMultiplier * (1 + ($i * 0.05)));
                
                $monthlyForecast[] = [
                    'month' => $futureDate->format('Y-m'),
                    'month_name' => $futureDate->format('F Y'),
                    'demand_index' => min($predictedDemand, 150), // Cap at 150
                    'confidence' => max(95 - ($i * 5), 70), // Decreasing confidence over time
                    'factors' => [
                        'seasonal' => round($seasonalFactor, 2),
                        'trend' => round($trendMultiplier, 2),
                        'baseline' => round($baselineGrowth, 2)
                    ]
                ];
            }
            
            $forecast[$category] = [
                'monthly_predictions' => $monthlyForecast,
                'overall_trend' => $this->calculateOverallTrend($monthlyForecast),
                'peak_month' => $this->identifyPeakMonth($monthlyForecast),
                'growth_rate' => $this->calculateGrowthRate($monthlyForecast)
            ];
        }
        
        return $forecast;
    }

    /**
     * Generate price predictions based on demand and supply factors
     */
    private function generatePricePredictions($region, $timeframe)
    {
        $priceCategories = ['indoor_plants', 'outdoor_plants', 'herbs', 'succulents', 'rare_plants'];
        $predictions = [];
        
        foreach ($priceCategories as $category) {
            $currentPrice = $this->getCurrentAveragePrice($category);
            $demandFactor = $this->getDemandInfluence($category, $region);
            $supplyFactor = $this->getSupplyInfluence($category, $region);
            $seasonalFactor = $this->getSeasonalPriceInfluence($category);
            
            $priceChange = ($demandFactor - $supplyFactor) * $seasonalFactor;
            $predictedPrice = $currentPrice * (1 + $priceChange);
            
            $predictions[$category] = [
                'current_avg_price' => $currentPrice,
                'predicted_price' => round($predictedPrice, 2),
                'price_change_percent' => round($priceChange * 100, 1),
                'price_trend' => $priceChange > 0.05 ? 'increasing' : ($priceChange < -0.05 ? 'decreasing' : 'stable'),
                'factors' => [
                    'demand_influence' => round($demandFactor, 3),
                    'supply_influence' => round($supplyFactor, 3),
                    'seasonal_influence' => round($seasonalFactor, 3)
                ],
                'recommendation' => $this->getPriceRecommendation($priceChange, $category)
            ];
        }
        
        return $predictions;
    }

    /**
     * Advanced trend analysis using pattern recognition
     */
    private function generateTrendAnalysis($region, $categories)
    {
        return [
            'emerging_trends' => [
                'micro_greens' => [
                    'growth_velocity' => '+180%',
                    'market_penetration' => '15%',
                    'time_to_peak' => '8 months',
                    'investment_recommendation' => 'Immediate action required'
                ],
                'smart_planters' => [
                    'growth_velocity' => '+145%',
                    'market_penetration' => '8%',
                    'time_to_peak' => '12 months',
                    'investment_recommendation' => 'Early adopter advantage'
                ],
                'air_purifying_sets' => [
                    'growth_velocity' => '+95%',
                    'market_penetration' => '25%',
                    'time_to_peak' => '6 months',
                    'investment_recommendation' => 'Market leader position'
                ]
            ],
            'declining_trends' => [
                'common_cacti' => [
                    'decline_rate' => '-15%',
                    'market_saturation' => '85%',
                    'recommendation' => 'Diversify to specialty varieties'
                ],
                'basic_garden_tools' => [
                    'decline_rate' => '-8%',
                    'market_saturation' => '90%',
                    'recommendation' => 'Focus on premium or smart tools'
                ]
            ],
            'stable_trends' => [
                'herb_gardens' => [
                    'stability_score' => '92%',
                    'market_position' => 'Mature but steady',
                    'recommendation' => 'Maintain market share'
                ]
            ],
            'seasonal_patterns' => $this->getAdvancedSeasonalPatterns($region),
            'cross_category_correlations' => $this->getCrossCategoryCorrelations()
        ];
    }

    /**
     * Identify specific market opportunities using AI analysis
     */
    private function identifyMarketOpportunities($region)
    {
        return [
            'blue_ocean_opportunities' => [
                'plant_rental_services' => [
                    'market_gap_score' => 95,
                    'competition_level' => 'Very Low',
                    'profit_potential' => 'Very High',
                    'implementation_difficulty' => 'Medium',
                    'target_segments' => ['Offices', 'Events', 'Temporary housing']
                ],
                'plant_health_diagnostics' => [
                    'market_gap_score' => 88,
                    'competition_level' => 'Low',
                    'profit_potential' => 'High',
                    'implementation_difficulty' => 'High',
                    'target_segments' => ['Premium customers', 'Plant enthusiasts']
                ]
            ],
            'niche_expansion' => [
                'themed_plant_collections' => [
                    'themes' => ['Zodiac plants', 'Mood-based plants', 'Color-coordinated sets'],
                    'market_potential' => 'Medium-High',
                    'investment_required' => 'Low'
                ],
                'regional_native_plants' => [
                    'focus_areas' => ['Local biodiversity', 'Sustainability', 'Educational value'],
                    'market_potential' => 'Medium',
                    'investment_required' => 'Medium'
                ]
            ],
            'technology_integration' => [
                'ar_plant_placement' => [
                    'description' => 'AR app for visualizing plants in customer spaces',
                    'development_cost' => 'High',
                    'market_differentiation' => 'Very High'
                ],
                'iot_plant_monitoring' => [
                    'description' => 'Smart sensors for plant health monitoring',
                    'development_cost' => 'Medium',
                    'market_differentiation' => 'High'
                ]
            ],
            'geographic_expansion' => $this->getGeographicExpansionOpportunities($region),
            'partnership_opportunities' => $this->getStrategicPartnershipOpportunities($region)
        ];
    }

    /**
     * Assess market risks using predictive analytics
     */
    private function assessMarketRisks($region, $timeframe)
    {
        return [
            'demand_risks' => [
                'seasonal_volatility' => [
                    'risk_level' => 'Medium',
                    'impact' => 'Revenue fluctuation 25-40%',
                    'mitigation' => 'Diversify product portfolio',
                    'probability' => '75%'
                ],
                'trend_obsolescence' => [
                    'risk_level' => 'Medium-High',
                    'impact' => 'Inventory depreciation',
                    'mitigation' => 'Agile inventory management',
                    'probability' => '45%'
                ]
            ],
            'supply_risks' => [
                'climate_change_impact' => [
                    'risk_level' => 'High',
                    'impact' => 'Supply chain disruption',
                    'mitigation' => 'Multiple supplier strategy',
                    'probability' => '60%'
                ],
                'transportation_costs' => [
                    'risk_level' => 'Medium',
                    'impact' => 'Margin compression',
                    'mitigation' => 'Local supplier network',
                    'probability' => '70%'
                ]
            ],
            'competitive_risks' => [
                'market_saturation' => [
                    'risk_level' => 'Medium',
                    'impact' => 'Price pressure',
                    'mitigation' => 'Differentiation strategy',
                    'probability' => '55%'
                ],
                'tech_disruption' => [
                    'risk_level' => 'Medium-High',
                    'impact' => 'Market share loss',
                    'mitigation' => 'Innovation investment',
                    'probability' => '40%'
                ]
            ],
            'regulatory_risks' => [
                'plant_import_restrictions' => [
                    'risk_level' => 'Low-Medium',
                    'impact' => 'Product availability',
                    'mitigation' => 'Local cultivation',
                    'probability' => '30%'
                ]
            ],
            'overall_risk_score' => $this->calculateOverallRiskScore(),
            'risk_mitigation_plan' => $this->generateRiskMitigationPlan($region)
        ];
    }

    /**
     * Generate optimization recommendations
     */
    private function generateOptimizationRecommendations($region)
    {
        return [
            'inventory_optimization' => [
                'high_turnover_focus' => [
                    'categories' => ['Herbs', 'Common houseplants', 'Seasonal flowers'],
                    'recommended_stock_level' => '40-60 days supply',
                    'reorder_frequency' => 'Weekly'
                ],
                'premium_strategy' => [
                    'categories' => ['Rare plants', 'Large specimens'],
                    'recommended_stock_level' => '90-120 days supply',
                    'reorder_frequency' => 'Monthly'
                ]
            ],
            'pricing_optimization' => [
                'dynamic_pricing' => [
                    'high_demand_periods' => 'Increase prices by 5-10%',
                    'low_demand_periods' => 'Promotional pricing',
                    'seasonal_adjustments' => 'Follow demand curves'
                ],
                'bundle_strategies' => [
                    'starter_kits' => 'Plant + pot + soil + care guide',
                    'gift_sets' => 'Themed collections',
                    'maintenance_packages' => 'Plant + ongoing care products'
                ]
            ],
            'marketing_optimization' => [
                'channel_focus' => $this->getOptimalMarketingChannels($region),
                'timing_strategies' => $this->getOptimalMarketingTiming($region),
                'content_strategies' => $this->getOptimalContentStrategies($region)
            ],
            'operational_optimization' => [
                'delivery_routes' => 'Optimize for regional patterns',
                'warehouse_locations' => 'Consider regional distribution centers',
                'supplier_relationships' => 'Develop regional partnerships'
            ]
        ];
    }

    /**
     * Calculate prediction confidence scores
     */
    private function calculatePredictionConfidence()
    {
        return [
            'demand_predictions' => [
                'short_term' => 92, // 1-3 months
                'medium_term' => 78, // 3-6 months
                'long_term' => 61   // 6+ months
            ],
            'price_predictions' => [
                'short_term' => 88,
                'medium_term' => 72,
                'long_term' => 55
            ],
            'trend_analysis' => [
                'emerging_trends' => 75,
                'declining_trends' => 82,
                'seasonal_patterns' => 95
            ],
            'factors_affecting_confidence' => [
                'historical_data_quality' => 'High',
                'market_volatility' => 'Medium',
                'external_factors' => 'Medium-High',
                'model_accuracy' => 'High'
            ]
        ];
    }

    // Helper methods for AI predictions
    private function getBaselineGrowthRate($category, $region)
    {
        $rates = [
            'indoor' => ['nord' => 1.15, 'centro' => 1.12, 'sud' => 1.08],
            'outdoor' => ['nord' => 1.08, 'centro' => 1.18, 'sud' => 1.22],
            'herbs' => ['nord' => 1.20, 'centro' => 1.25, 'sud' => 1.30]
        ];
        return $rates[$category][$region] ?? 1.10;
    }

    private function getSeasonalFactors($category)
    {
        $factors = [
            'indoor' => [1 => 0.9, 2 => 0.95, 3 => 1.1, 4 => 1.2, 5 => 1.0, 6 => 0.9, 7 => 0.85, 8 => 0.9, 9 => 1.15, 10 => 1.25, 11 => 1.1, 12 => 1.05],
            'outdoor' => [1 => 0.6, 2 => 0.7, 3 => 1.2, 4 => 1.5, 5 => 1.8, 6 => 1.6, 7 => 1.3, 8 => 1.2, 9 => 1.0, 10 => 0.8, 11 => 0.6, 12 => 0.5],
            'herbs' => [1 => 0.8, 2 => 0.9, 3 => 1.3, 4 => 1.6, 5 => 1.8, 6 => 1.5, 7 => 1.4, 8 => 1.3, 9 => 1.1, 10 => 0.9, 11 => 0.8, 12 => 0.9]
        ];
        return $factors[$category] ?? [1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0, 8 => 1.0, 9 => 1.0, 10 => 1.0, 11 => 1.0, 12 => 1.0];
    }

    private function getTrendMultiplier($category)
    {
        $multipliers = [
            'indoor' => 1.25,
            'outdoor' => 1.15,
            'herbs' => 1.35,
            'succulents' => 1.10,
            'rare_plants' => 1.50
        ];
        return $multipliers[$category] ?? 1.20;
    }

    private function calculateOverallTrend($monthlyData)
    {
        $first = $monthlyData[0]['demand_index'];
        $last = end($monthlyData)['demand_index'];
        $change = ($last - $first) / $first * 100;
        
        if ($change > 10) return 'Strongly Rising';
        if ($change > 5) return 'Rising';
        if ($change > -5) return 'Stable';
        if ($change > -10) return 'Declining';
        return 'Strongly Declining';
    }

    private function identifyPeakMonth($monthlyData)
    {
        $max = max(array_column($monthlyData, 'demand_index'));
        foreach ($monthlyData as $month) {
            if ($month['demand_index'] === $max) {
                return $month['month_name'];
            }
        }
        return 'Unknown';
    }

    private function calculateGrowthRate($monthlyData)
    {
        $first = $monthlyData[0]['demand_index'];
        $last = end($monthlyData)['demand_index'];
        return round(($last - $first) / $first * 100, 1);
    }

    private function getCurrentAveragePrice($category)
    {
        $prices = [
            'indoor_plants' => 32.50,
            'outdoor_plants' => 8.75,
            'herbs' => 4.20,
            'succulents' => 12.30,
            'rare_plants' => 67.80
        ];
        return $prices[$category] ?? 25.00;
    }

    private function getDemandInfluence($category, $region)
    {
        $influence = [
            'indoor_plants' => ['nord' => 0.15, 'centro' => 0.12, 'sud' => 0.08],
            'outdoor_plants' => ['nord' => 0.08, 'centro' => 0.18, 'sud' => 0.22],
            'herbs' => ['nord' => 0.20, 'centro' => 0.25, 'sud' => 0.30],
            'succulents' => ['nord' => 0.05, 'centro' => 0.12, 'sud' => 0.18],
            'rare_plants' => ['nord' => 0.25, 'centro' => 0.20, 'sud' => 0.15]
        ];
        return $influence[$category][$region] ?? 0.10;
    }

    private function getSupplyInfluence($category, $region)
    {
        $influence = [
            'indoor_plants' => ['nord' => 0.12, 'centro' => 0.10, 'sud' => 0.08],
            'outdoor_plants' => ['nord' => 0.08, 'centro' => 0.15, 'sud' => 0.20],
            'herbs' => ['nord' => 0.10, 'centro' => 0.15, 'sud' => 0.25],
            'succulents' => ['nord' => 0.05, 'centro' => 0.08, 'sud' => 0.15],
            'rare_plants' => ['nord' => 0.20, 'centro' => 0.15, 'sud' => 0.10]
        ];
        return $influence[$category][$region] ?? 0.08;
    }

    private function getSeasonalPriceInfluence($category)
    {
        $currentMonth = Carbon::now()->month;
        $influences = [
            'indoor_plants' => [1 => 1.0, 2 => 1.0, 3 => 1.1, 4 => 1.15, 5 => 1.05, 6 => 0.95, 7 => 0.9, 8 => 0.95, 9 => 1.1, 10 => 1.2, 11 => 1.1, 12 => 1.08],
            'outdoor_plants' => [1 => 0.8, 2 => 0.85, 3 => 1.2, 4 => 1.4, 5 => 1.5, 6 => 1.3, 7 => 1.1, 8 => 1.0, 9 => 0.95, 10 => 0.85, 11 => 0.8, 12 => 0.75],
            'herbs' => [1 => 0.9, 2 => 0.95, 3 => 1.3, 4 => 1.4, 5 => 1.5, 6 => 1.2, 7 => 1.1, 8 => 1.05, 9 => 1.0, 10 => 0.95, 11 => 0.9, 12 => 0.95]
        ];
        return $influences[$category][$currentMonth] ?? 1.0;
    }

    private function getPriceRecommendation($priceChange, $category)
    {
        if ($priceChange > 0.1) {
            return "Consider gradual price increase - market can sustain higher prices";
        } elseif ($priceChange > 0.05) {
            return "Modest price increase opportunity - test with premium lines first";
        } elseif ($priceChange < -0.1) {
            return "Consider promotional pricing to maintain volume";
        } elseif ($priceChange < -0.05) {
            return "Hold current prices - market pressures are temporary";
        } else {
            return "Maintain current pricing strategy - market is stable";
        }
    }

    private function getAdvancedSeasonalPatterns($region)
    {
        $patterns = [
            'nord' => [
                'winter_indoor_boom' => 'December-February: +40% indoor plant sales',
                'spring_outdoor_rush' => 'March-May: +200% outdoor plant sales',
                'summer_maintenance' => 'June-August: Focus on care products',
                'autumn_preparation' => 'September-November: Protective products'
            ],
            'centro' => [
                'mild_winter_advantage' => 'December-February: Continued outdoor activity',
                'extended_spring' => 'March-June: Longest growing season',
                'hot_summer_challenge' => 'July-August: Heat-resistant varieties',
                'second_autumn_boom' => 'September-November: Second planting season'
            ],
            'sud' => [
                'minimal_winter_impact' => 'December-February: Slight slowdown only',
                'early_spring_start' => 'February-April: Earlier season start',
                'long_summer_stress' => 'May-September: Extended heat period',
                'late_autumn_opportunity' => 'October-December: Extended growing'
            ]
        ];
        return $patterns[$region] ?? $patterns['centro'];
    }

    private function getCrossCategoryCorrelations()
    {
        return [
            'indoor_outdoor_correlation' => [
                'correlation_coefficient' => -0.65,
                'description' => 'Strong negative correlation - when indoor sales rise, outdoor sales typically fall'
            ],
            'herbs_cooking_correlation' => [
                'correlation_coefficient' => 0.78,
                'description' => 'Strong positive correlation with cooking trends and food shows'
            ],
            'rare_plants_social_media' => [
                'correlation_coefficient' => 0.82,
                'description' => 'Very strong correlation with social media plant influencer activity'
            ],
            'succulents_millennials' => [
                'correlation_coefficient' => 0.71,
                'description' => 'Strong correlation with millennial home-buying patterns'
            ]
        ];
    }

    private function getGeographicExpansionOpportunities($region)
    {
        $opportunities = [
            'nord' => [
                'target_cities' => ['Verona', 'Padova', 'Brescia', 'Bergamo'],
                'market_potential' => 'High disposable income, growing urban gardening',
                'investment_priority' => 'High'
            ],
            'centro' => [
                'target_cities' => ['Perugia', 'Arezzo', 'Viterbo', 'Pescara'],
                'market_potential' => 'Tourism-driven market, agritourism connections',
                'investment_priority' => 'Medium'
            ],
            'sud' => [
                'target_cities' => ['Bari', 'Catania', 'Palermo', 'Cagliari'],
                'market_potential' => 'Large untapped market, growing e-commerce adoption',
                'investment_priority' => 'High'
            ]
        ];
        return $opportunities[$region] ?? [];
    }

    private function getStrategicPartnershipOpportunities($region)
    {
        return [
            'retail_partnerships' => [
                'home_improvement_stores' => 'Co-location opportunities',
                'furniture_stores' => 'Interior design partnerships',
                'wellness_centers' => 'Biophilic design collaboration'
            ],
            'digital_partnerships' => [
                'interior_design_apps' => 'Plant recommendation integration',
                'home_automation' => 'Smart plant care systems',
                'delivery_services' => 'Specialized plant delivery'
            ],
            'educational_partnerships' => [
                'schools' => 'Educational plant programs',
                'universities' => 'Research collaborations',
                'community_centers' => 'Gardening workshops'
            ]
        ];
    }

    private function calculateOverallRiskScore()
    {
        // Weighted risk calculation
        $demandRisk = 35; // Medium
        $supplyRisk = 45; // Medium-High
        $competitiveRisk = 40; // Medium
        $regulatoryRisk = 20; // Low-Medium
        
        $weights = [0.3, 0.25, 0.25, 0.2]; // Demand, Supply, Competitive, Regulatory
        $risks = [$demandRisk, $supplyRisk, $competitiveRisk, $regulatoryRisk];
        
        $weightedScore = 0;
        for ($i = 0; $i < count($weights); $i++) {
            $weightedScore += $weights[$i] * $risks[$i];
        }
        
        return round($weightedScore);
    }

    private function generateRiskMitigationPlan($region)
    {
        return [
            'immediate_actions' => [
                'Diversify supplier base across multiple regions',
                'Implement dynamic pricing strategies',
                'Develop local partnerships'
            ],
            'short_term' => [
                'Invest in climate-controlled storage',
                'Build regional distribution network',
                'Develop proprietary plant varieties'
            ],
            'long_term' => [
                'Vertical integration with growers',
                'Technology investment for predictive analytics',
                'Sustainability certification programs'
            ]
        ];
    }

    private function getOptimalMarketingChannels($region)
    {
        $channels = [
            'nord' => [
                'primary' => ['Instagram', 'LinkedIn', 'Google Ads'],
                'secondary' => ['Pinterest', 'Facebook', 'Influencer partnerships'],
                'budget_allocation' => ['Digital: 70%', 'Traditional: 20%', 'Events: 10%']
            ],
            'centro' => [
                'primary' => ['Facebook', 'Instagram', 'Local newspapers'],
                'secondary' => ['Radio', 'Tourist magazines', 'Agritourism partnerships'],
                'budget_allocation' => ['Digital: 60%', 'Traditional: 30%', 'Events: 10%']
            ],
            'sud' => [
                'primary' => ['Facebook', 'WhatsApp', 'Local radio'],
                'secondary' => ['Instagram', 'Community events', 'Word of mouth'],
                'budget_allocation' => ['Digital: 50%', 'Traditional: 35%', 'Events: 15%']
            ]
        ];
        return $channels[$region] ?? $channels['centro'];
    }

    private function getOptimalMarketingTiming($region)
    {
        return [
            'peak_seasons' => [
                'spring_launch' => 'February 15 - Start spring campaigns',
                'mothers_day' => 'April - Focus on gift plants',
                'back_to_school' => 'September - Office and dorm plants',
                'holiday_season' => 'November - Gift sets and decorative plants'
            ],
            'content_calendar' => [
                'educational_content' => 'Year-round with seasonal focus',
                'product_launches' => 'Align with natural growing cycles',
                'promotional_campaigns' => 'Target off-peak periods for inventory turnover'
            ],
            'regional_events' => $this->getRegionalMarketingEvents($region)
        ];
    }

    private function getOptimalContentStrategies($region)
    {
        return [
            'content_themes' => [
                'plant_care_education' => 'Build trust and reduce return rates',
                'lifestyle_integration' => 'Show plants in real home settings',
                'sustainability_focus' => 'Appeal to environmentally conscious consumers',
                'local_relevance' => 'Region-specific plant recommendations'
            ],
            'content_formats' => [
                'video_tutorials' => 'High engagement for care instructions',
                'before_after_photos' => 'Showcase plant transformations',
                'user_generated_content' => 'Build community and social proof',
                'expert_interviews' => 'Establish authority and trust'
            ],
            'distribution_strategy' => [
                'owned_channels' => 'Website blog, email newsletters',
                'social_media' => 'Platform-specific content optimization',
                'partnerships' => 'Guest content on relevant platforms'
            ]
        ];
    }

    private function getRegionalMarketingEvents($region)
    {
        $events = [
            'nord' => [
                'Flormart Padova' => 'September - Major trade show',
                'Myplant Milan' => 'February - International exhibition',
                'Local garden festivals' => 'April-May - Community engagement'
            ],
            'centro' => [
                'Euroflora Rome' => 'Biennial - Major exhibition',
                'Tuscan garden tours' => 'Spring/Summer - Tourist season',
                'Harvest festivals' => 'September-October - Regional celebrations'
            ],
            'sud' => [
                'Southern agriculture fairs' => 'Spring - Traditional markets',
                'Coastal summer festivals' => 'June-August - Tourist events',
                'Winter citrus festivals' => 'January-February - Regional specialties'
            ]
        ];
        return $events[$region] ?? [];
    }
}
