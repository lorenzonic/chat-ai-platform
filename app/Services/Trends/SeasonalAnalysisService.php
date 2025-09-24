<?php

namespace App\Services\Trends;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

/**
 * Service per gestire analisi stagionali e predizioni
 */
class SeasonalAnalysisService
{
    /**
     * Get seasonal trends analysis
     */
    public function getSeasonalTrends()
    {
        return Cache::remember('seasonal_trends', 86400, function() {
            $currentMonth = Carbon::now()->month;

            return [
                'current_season' => $this->getCurrentSeason(),
                'monthly_trends' => $this->getMonthlyTrends(),
                'current_factor' => $this->getSeasonalFactor($currentMonth),
                'next_peak' => $this->getNextPeak($currentMonth),
                'seasonal_opportunities' => $this->getSeasonalOpportunities(),
                'climate_impact' => $this->getClimateImpact(),
            ];
        });
    }

    /**
     * Get future demand predictions for next 3-6 months
     */
    public function getFutureDemandPredictions($months = 6)
    {
        $nextMonths = [];
        for ($i = 1; $i <= $months; $i++) {
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
            'emerging_trends' => $this->getEmergingTrends(),
            'investment_recommendations' => $this->getInvestmentRecommendations()
        ];
    }

    /**
     * Get seasonal opportunities for each region
     */
    public function getRegionalSeasonalPatterns($region)
    {
        $patterns = [
            'nord' => [
                'inverno_lungo' => 'Novembre-Marzo: Focus indoor plants',
                'primavera_tardiva' => 'Aprile-Maggio: Boom outdoor',
                'estate_mite' => 'Giugno-Agosto: Maintenance mode',
                'autunno_precoce' => 'Settembre-Ottobre: Preparation'
            ],
            'centro' => [
                'inverno_mite' => 'Dicembre-Febbraio: Evergreen focus',
                'primavera_lunga' => 'Marzo-Maggio: Extended season',
                'estate_calda' => 'Giugno-Agosto: Drought resistant',
                'autunno_lungo' => 'Settembre-Novembre: Second season'
            ],
            'sud' => [
                'inverno_breve' => 'Gennaio-Febbraio: Minimal slowdown',
                'primavera_precoce' => 'Marzo-Aprile: Early start',
                'estate_lunga' => 'Maggio-Settembre: Extended heat',
                'autunno_mite' => 'Ottobre-Dicembre: Extended growing'
            ]
        ];

        return $patterns[$region] ?? $patterns['centro'];
    }

    private function getCurrentSeason()
    {
        $month = Carbon::now()->month;
        if ($month >= 3 && $month <= 5) return 'Primavera';
        if ($month >= 6 && $month <= 8) return 'Estate';
        if ($month >= 9 && $month <= 11) return 'Autunno';
        return 'Inverno';
    }

    private function getMonthlyTrends()
    {
        return [
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
        ];
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

    private function getSeasonalOpportunities()
    {
        $currentMonth = Carbon::now()->month;
        $season = $this->getCurrentSeason();

        $opportunities = [
            'Primavera' => [
                'high_demand' => ['Outdoor plants', 'Herb gardens', 'Flower bulbs'],
                'marketing_focus' => 'New beginnings, fresh starts',
                'pricing_strategy' => 'Premium pricing for new arrivals'
            ],
            'Estate' => [
                'high_demand' => ['Heat-resistant plants', 'Irrigation systems', 'Shade plants'],
                'marketing_focus' => 'Outdoor living, vacation homes',
                'pricing_strategy' => 'Maintenance and care focus'
            ],
            'Autunno' => [
                'high_demand' => ['Indoor plants', 'Fall decorations', 'Winter prep'],
                'marketing_focus' => 'Cozy indoor spaces, preparation',
                'pricing_strategy' => 'Bundle deals for indoor collections'
            ],
            'Inverno' => [
                'high_demand' => ['Houseplants', 'Holiday decorations', 'Gift plants'],
                'marketing_focus' => 'Indoor comfort, gift giving',
                'pricing_strategy' => 'Gift packaging and premium sets'
            ]
        ];

        return $opportunities[$season] ?? $opportunities['Primavera'];
    }

    private function getClimateImpact()
    {
        return [
            'temperature_trends' => 'Increasing average temperatures affecting plant selection',
            'precipitation_changes' => 'More irregular rainfall patterns',
            'extreme_weather' => 'Increased frequency of heat waves and storms',
            'adaptation_strategies' => [
                'Drought-resistant varieties',
                'Indoor climate control',
                'Flexible planting schedules'
            ]
        ];
    }

    private function predictDemand($category, $month)
    {
        // Seasonal demand factors based on historical data
        $seasonalFactors = [
            'indoor' => [
                1 => 85, 2 => 88, 3 => 95, 4 => 98, 5 => 92, 6 => 88,
                7 => 85, 8 => 87, 9 => 95, 10 => 98, 11 => 92, 12 => 90
            ],
            'outdoor' => [
                1 => 65, 2 => 70, 3 => 85, 4 => 98, 5 => 100, 6 => 95,
                7 => 88, 8 => 85, 9 => 78, 10 => 72, 11 => 68, 12 => 65
            ]
        ];

        return $seasonalFactors[$category][$month] ?? 85;
    }

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

    private function getRecommendedStockLevel($category, $month)
    {
        $stockLevels = [
            'indoor' => [
                1 => 'Medio-Alto', 2 => 'Medio-Alto', 3 => 'Alto', 4 => 'Molto Alto',
                5 => 'Alto', 6 => 'Medio', 7 => 'Medio', 8 => 'Medio',
                9 => 'Alto', 10 => 'Molto Alto', 11 => 'Alto', 12 => 'Alto'
            ],
            'outdoor' => [
                1 => 'Basso', 2 => 'Basso-Medio', 3 => 'Medio-Alto', 4 => 'Molto Alto',
                5 => 'Massimo', 6 => 'Alto', 7 => 'Medio-Alto', 8 => 'Medio',
                9 => 'Medio-Alto', 10 => 'Medio', 11 => 'Basso-Medio', 12 => 'Basso'
            ]
        ];

        return $stockLevels[$category][$month] ?? 'Medio';
    }

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

    private function getEmergingTrends()
    {
        return [
            'air_purifying_plants' => ['growth' => '+45%', 'reason' => 'Aumentata consapevolezza qualità aria'],
            'low_light_plants' => ['growth' => '+30%', 'reason' => 'Vita urbana, appartamenti piccoli'],
            'pet_safe_plants' => ['growth' => '+35%', 'reason' => 'Crescita numero animali domestici'],
            'self_watering_systems' => ['growth' => '+60%', 'reason' => 'Tecnologia e convenienza'],
            'plant_subscription_boxes' => ['growth' => '+80%', 'reason' => 'E-commerce e sorpresa mensile']
        ];
    }

    private function getInvestmentRecommendations()
    {
        return [
            'high_priority' => ['Monstera varieties', 'Rare Philodendrons', 'Air-purifying plants'],
            'medium_priority' => ['Herb gardens', 'Succulents collection', 'Seasonal flowers'],
            'low_priority' => ['Common cacti', 'Basic outdoor plants']
        ];
    }

    /**
     * Get detailed plant categories trends for garden center stocking
     */
    public function getPlantCategoriesTrends($days = 30)
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
}
