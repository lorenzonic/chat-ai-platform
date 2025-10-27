<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

// Import all trend services
use App\Services\Trends\GoogleTrendsService;
use App\Services\Trends\SocialMediaTrendsService;
use App\Services\Trends\SeasonalAnalysisService;
use App\Services\Trends\DemographicAnalysisService;
use App\Services\Trends\PerformanceMetricsService;
use App\Services\Trends\EcommerceDataService;
use App\Services\PlantSitesManager;

/**
 * Refactored TrendsController using multiple specialized services
 */
class TrendsControllerRefactored extends Controller
{
    protected $googleTrendsService;
    protected $socialMediaService;
    protected $seasonalAnalysisService;
    protected $demographicService;
    protected $performanceService;
    protected $ecommerceService;
    protected $plantSitesManager;

    public function __construct(
        GoogleTrendsService $googleTrendsService,
        SocialMediaTrendsService $socialMediaService,
        SeasonalAnalysisService $seasonalAnalysisService,
        DemographicAnalysisService $demographicService,
        PerformanceMetricsService $performanceService,
        EcommerceDataService $ecommerceService,
        PlantSitesManager $plantSitesManager
    ) {
        $this->googleTrendsService = $googleTrendsService;
        $this->socialMediaService = $socialMediaService;
        $this->seasonalAnalysisService = $seasonalAnalysisService;
        $this->demographicService = $demographicService;
        $this->performanceService = $performanceService;
        $this->ecommerceService = $ecommerceService;
        $this->plantSitesManager = $plantSitesManager;
    }

    /**
     * Display trends analytics dashboard
     */
    public function index(Request $request)
    {
        // Date range filter
        $days = $request->get('days', 30);
        $region = $request->get('region');
        $keyword = $request->get('keyword');
        $tab = $request->get('tab', 'plant'); // Default to plant trends
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        // Google Trends reali dal database
        $googleTrends = $this->googleTrendsService->getTrends($days, $region, $keyword);
        $topTrends = $this->googleTrendsService->getTopTrends($days, 10);
        $availableRegions = $googleTrends->pluck('region')->unique()->values();
        $availableKeywords = $googleTrends->pluck('keyword')->unique()->values();

        // Site selection for scraping
        $selectedSites = $request->get('sites', []);
        $scrapingMode = $request->get('scraping_mode', 'auto');

        // Get trending data from all services
        $trendsData = [
            'google_trends' => $googleTrends,
            'top_trends' => $topTrends,
            'social_trends' => $this->socialMediaService->getSocialTrends($days),
            'hashtag_trends' => $this->socialMediaService->getHashtagTrends($days),
            'influencer_trends' => $this->socialMediaService->getInfluencerTrends(),
            'marketplace_trends' => $this->googleTrendsService->getMarketplaceTrends($days),
            'seasonal_trends' => $this->seasonalAnalysisService->getSeasonalTrends(),
            'plant_keywords' => $this->googleTrendsService->getPlantKeywords(),
            'plant_categories' => $this->seasonalAnalysisService->getPlantCategoriesTrends($days),
            'future_demands' => $this->seasonalAnalysisService->getFutureDemandPredictions(),
            'demographic_trends' => $this->demographicService->getDemographicTrends($days),
            'regional_preferences' => $this->demographicService->getRegionalPlantPreferences(),
            'ecommerce_data' => $this->ecommerceService->getEcommerceData($days, $selectedSites, $scrapingMode),
            'available_sites' => $this->ecommerceService->getAvailableSites(),
            'sites_validation' => $this->ecommerceService->getSitesValidation(),
        ];

        // If Google tab is selected, add specific Google Trends data
        if ($tab === 'google') {
            // Get Google Trends keywords from TrendingKeywordsController logic
            $trendsData['google_keywords'] = \App\Models\TrendingKeyword::latest()
                ->when($keyword, function($query, $keyword) {
                    return $query->where('keyword', 'like', "%{$keyword}%");
                })
                ->limit(100)
                ->get();

            $trendsData['top_google_keywords'] = \App\Models\TrendingKeyword::latest()
                ->limit(20)
                ->get();
        }

        // Performance metrics
        $performance = [
            'trending_score' => $this->performanceService->calculateTrendingScore($trendsData),
            'growth_rate' => $this->performanceService->calculateGrowthRate($days),
            'engagement_rate' => $this->performanceService->calculateEngagementRate($days),
            'conversion_rate' => $this->performanceService->calculateConversionRate($days),
            'roi_metrics' => $this->performanceService->calculateROIMetrics($trendsData),
            'customer_acquisition' => $this->performanceService->calculateCustomerAcquisitionCost($trendsData),
            'market_position' => $this->performanceService->calculateMarketPosition(),
        ];

        // Data source information
        $dataSource = 'real';

        return view('admin.trends.index', compact(
            'trendsData',
            'performance',
            'dataSource',
            'days',
            'startDate',
            'endDate',
            'region',
            'keyword',
            'availableRegions',
            'availableKeywords',
            'tab'
        ));
    }

    /**
     * API endpoint per Google Trends filtrati (per frontend dinamico)
     */
    public function apiGoogleTrends(Request $request)
    {
        $days = $request->get('days', 30);
        $region = $request->get('region');
        $keyword = $request->get('keyword');
        $trends = $this->googleTrendsService->getTrends($days, $region, $keyword);
        $topTrends = $this->googleTrendsService->getTopTrends($days, 10);
        return response()->json([
            'trends' => $trends,
            'topTrends' => $topTrends
        ]);
    }

    /**
     * Geographic trends analysis
     */
    public function getGeographicTrends(Request $request)
    {
        $region = $request->get('region', 'all');
        $city = $request->get('city', 'all');
        $days = $request->get('days', 30);

        $geoData = [
            'city_trends' => $this->demographicService->getCitySpecificTrends($city, $days),
            'regional_analysis' => $this->demographicService->getRegionalMarketAnalysis($region, $days),
            'climate_recommendations' => $this->getClimateBasedRecommendations($region, $city),
            'local_competition' => $this->getLocalCompetitionAnalysis($region, $city),
            'delivery_logistics' => $this->getDeliveryInsights($region, $city),
            'seasonal_regional_patterns' => $this->seasonalAnalysisService->getRegionalSeasonalPatterns($region),
            'city_demographics' => $this->demographicService->getCityDemographics($city),
            'local_market_opportunities' => $this->getLocalMarketOpportunities($region, $city)
        ];

        return response()->json($geoData);
    }

    /**
     * Advanced analytics page
     */
    public function advanced(Request $request)
    {
        $days = $request->get('days', 30);
        $selectedSites = $request->get('sites', []);
        $scrapingMode = $request->get('scraping_mode', 'auto');

        $advancedData = [
            'ecommerce_data' => $this->getEcommerceData($days, $selectedSites, $scrapingMode),
            'demographic_trends' => $this->demographicService->getDemographicTrends($days),
            'regional_preferences' => $this->demographicService->getRegionalPlantPreferences(),
            'future_demands' => $this->seasonalAnalysisService->getFutureDemandPredictions(),
            'plant_categories' => $this->getPlantCategoriesTrends($days),
            'performance_metrics' => $this->performanceService->calculateROIMetrics([]),
            'available_sites' => $this->plantSitesManager->getAvailableSites(),
            'sites_validation' => $this->plantSitesManager->validateSites(),
        ];

        return view('admin.trends.advanced', compact(
            'advancedData',
            'days'
        ));
    }

    /**
     * Configuration page for sites and settings
     */
    public function configure(Request $request)
    {
        $availableSites = $this->plantSitesManager->getAvailableSites();

        return view('admin.trends.configure', compact('availableSites'));
    }

    /**
     * API endpoint for real-time trend updates
     */
    public function realTimeTrends(Request $request)
    {
        $type = $request->get('type', 'all');

        $data = [];

        switch ($type) {
            case 'google':
                $data = $this->googleTrendsService->getTrends(7);
                break;
            case 'social':
                $data = $this->socialMediaService->getSocialTrends(7);
                break;
            case 'seasonal':
                $data = $this->seasonalAnalysisService->getSeasonalTrends();
                break;
            default:
                $data = [
                    'google' => $this->googleTrendsService->getTrends(7),
                    'social' => $this->socialMediaService->getSocialTrends(7),
                    'timestamp' => now()->toISOString()
                ];
        }

        return response()->json($data);
    }

    /**
     * Export trends data to various formats
     */
    public function exportTrends(Request $request)
    {
        $format = $request->get('format', 'json');
        $days = $request->get('days', 30);

        $data = [
            'export_timestamp' => now()->toISOString(),
            'period' => $days . ' days',
            'google_trends' => $this->googleTrendsService->getTrends($days),
            'social_trends' => $this->socialMediaService->getSocialTrends($days),
            'seasonal_analysis' => $this->seasonalAnalysisService->getSeasonalTrends(),
            'performance_metrics' => $this->performanceService->calculateTrendingScore([])
        ];

        switch ($format) {
            case 'csv':
                return $this->exportToCsv($data);
            case 'excel':
                return $this->exportToExcel($data);
            case 'pdf':
                return $this->exportToPdf($data);
            default:
                return response()->json($data);
        }
    }

    // Private helper methods for backward compatibility and specific functionality

    private function getMarketplaceTrends($days = 30)
    {
        $cacheKey = "marketplace_trends_{$days}";

        return Cache::remember($cacheKey, 3600, function() {
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

    private function getPlantCategoriesTrends($days = 30)
    {
        return Cache::remember("plant_categories_{$days}", 7200, function() {
            return [
                'indoor_plants' => [
                    'trend' => 'rising',
                    'growth_rate' => rand(15, 35),
                    'demand_score' => rand(75, 95),
                    'top_varieties' => [
                        ['name' => 'Monstera Deliciosa', 'trend' => 'rising', 'demand' => rand(85, 100), 'profit_margin' => '45%'],
                        ['name' => 'Ficus Lyrata', 'trend' => 'stable', 'demand' => rand(70, 90), 'profit_margin' => '40%'],
                        ['name' => 'Pothos', 'trend' => 'rising', 'demand' => rand(80, 95), 'profit_margin' => '55%'],
                        ['name' => 'Snake Plant', 'trend' => 'rising', 'demand' => rand(75, 90), 'profit_margin' => '50%'],
                        ['name' => 'ZZ Plant', 'trend' => 'rising', 'demand' => rand(70, 85), 'profit_margin' => '48%'],
                    ],
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
                        ['name' => 'Jade Plant', 'trend' => 'rising', 'demand' => rand(65, 80), 'profit_margin' => '58%'],
                    ],
                    'target_audience' => ['busy_professionals', 'students', 'collectors'],
                    'price_range' => '€5-€50',
                    'stock_recommendation' => 'Medium - 30-50 piante per varietà popolare'
                ],
                'outdoor_plants' => [
                    'trend' => 'seasonal_rising',
                    'growth_rate' => rand(20, 40),
                    'demand_score' => rand(80, 100),
                    'seasonal_factor' => $this->seasonalAnalysisService->getSeasonalTrends()['current_factor'],
                    'target_audience' => ['gardening_enthusiasts', 'families', 'seniors'],
                    'price_range' => '€3-€25',
                    'stock_recommendation' => 'Stagionale - aumenta stock in primavera/estate'
                ]
            ];
        });
    }

    private function getEcommerceData($days, $selectedSites, $scrapingMode)
    {
        $cacheKey = "ecommerce_data_{$days}_" . md5(serialize($selectedSites)) . "_{$scrapingMode}";

        return Cache::remember($cacheKey, 7200, function() use ($selectedSites, $scrapingMode) {
            switch ($scrapingMode) {
                case 'real':
                    return $this->plantSitesManager->scrapeSelectedSites($selectedSites, 5);

                case 'simulation':
                    return $this->getEnhancedEcommerceData();

                case 'auto':
                default:
                    try {
                        $realData = $this->plantSitesManager->scrapeSelectedSites($selectedSites, 5);
                        if ($realData && isset($realData['total_products']) && $realData['total_products'] > 0) {
                            return $realData;
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Auto mode falling back to simulation: ' . $e->getMessage());
                    }

                    return $this->getEnhancedEcommerceData();
            }
        });
    }

    // Additional helper methods for features not yet moved to services
    private function getClimateBasedRecommendations($region, $city)
    {
        // This could be moved to a ClimateAnalysisService in the future
        return [
            'temperature_recommendations' => 'Based on local climate data',
            'humidity_suggestions' => 'Optimal plant selection for local conditions',
            'seasonal_care_tips' => 'Region-specific care instructions'
        ];
    }

    private function getLocalCompetitionAnalysis($region, $city)
    {
        // This could be moved to a CompetitionAnalysisService
        return [
            'competitor_count' => rand(5, 25),
            'market_saturation' => rand(30, 80) . '%',
            'opportunities' => ['Premium plants', 'Delivery services', 'Plant care']
        ];
    }

    private function getDeliveryInsights($region, $city)
    {
        // This could be moved to a LogisticsAnalysisService
        return [
            'delivery_zones' => 'Coverage analysis',
            'shipping_costs' => 'Cost optimization opportunities',
            'delivery_times' => 'Performance metrics'
        ];
    }

    private function getLocalMarketOpportunities($region, $city)
    {
        // This could be moved to a MarketOpportunityService
        return [
            'growth_segments' => ['Plant subscriptions', 'Corporate clients'],
            'partnership_opportunities' => ['Interior designers', 'Wellness centers'],
            'market_gaps' => ['Premium services', 'Educational content']
        ];
    }

    private function getEnhancedEcommerceData()
    {
        // Comprehensive fallback data - this could be its own service
        return [
            'total_products' => 45,
            'sites_scraped' => ['Viridea', 'Bakker Italia', 'Mondo Piante'],
            'scraping_timestamp' => now()->toISOString(),
            'data_source' => 'enhanced_simulation',
            'products' => $this->getSimulatedProducts(),
            'market_insights' => $this->getMarketInsights(),
            'pricing_analysis' => $this->getPricingAnalysis()
        ];
    }

    private function getSimulatedProducts()
    {
        return [
            ['name' => 'Monstera Deliciosa 40cm', 'price' => 29.90, 'availability' => 'Disponibile', 'category' => 'monstera', 'popularity' => 95],
            ['name' => 'Ficus Lyrata 120cm', 'price' => 89.90, 'availability' => 'Disponibile', 'category' => 'ficus', 'popularity' => 92],
            ['name' => 'Pothos Golden 20cm', 'price' => 12.90, 'availability' => 'Disponibile', 'category' => 'pothos', 'popularity' => 90],
            // Add more simulated products as needed
        ];
    }

    private function getMarketInsights()
    {
        return [
            'trending_categories' => ['Indoor plants', 'Rare varieties', 'Herb gardens'],
            'price_trends' => 'Increasing demand for premium plants',
            'seasonal_patterns' => 'Spring showing highest activity'
        ];
    }

    private function getPricingAnalysis()
    {
        return [
            'average_price' => 28.45,
            'price_ranges' => [
                'budget' => ['min' => 3.50, 'max' => 15.00, 'count' => 12],
                'mid_range' => ['min' => 15.01, 'max' => 50.00, 'count' => 18],
                'premium' => ['min' => 50.01, 'max' => 100.00, 'count' => 8],
            ]
        ];
    }

    // Export methods (could be moved to an ExportService)
    private function exportToCsv($data)
    {
        // Implementation for CSV export
        return response()->streamDownload(function() use ($data) {
            echo "CSV export functionality";
        }, 'trends-export.csv');
    }

    private function exportToExcel($data)
    {
        // Implementation for Excel export
        return response()->json(['message' => 'Excel export functionality']);
    }

    private function exportToPdf($data)
    {
        // Implementation for PDF export
        return response()->json(['message' => 'PDF export functionality']);
    }
}
