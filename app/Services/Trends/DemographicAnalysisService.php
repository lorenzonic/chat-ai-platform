<?php

namespace App\Services\Trends;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

/**
 * Service per gestire analisi demografiche e geografiche
 */
class DemographicAnalysisService
{
    /**
     * Analyze demographic trends for targeted marketing
     */
    public function getDemographicTrends($days = 30)
    {
        return Cache::remember("demographic_trends_{$days}", 3600, function() {
            return [
                'millennials' => $this->getMillennialTrends(),
                'gen_z' => $this->getGenZTrends(),
                'gen_x' => $this->getGenXTrends(),
                'baby_boomers' => $this->getBabyBoomerTrends(),
                'income_segments' => $this->getIncomeSegmentTrends(),
                'urban_vs_rural' => $this->getUrbanRuralTrends(),
            ];
        });
    }

    /**
     * Regional plant preferences across Italy
     */
    public function getRegionalPlantPreferences()
    {
        return Cache::remember('regional_preferences', 86400, function() {
            return [
                'nord_italia' => $this->getNorthItalyPreferences(),
                'centro_italia' => $this->getCentralItalyPreferences(),
                'sud_italia' => $this->getSouthItalyPreferences(),
                'isole' => $this->getIslandsPreferences(),
            ];
        });
    }

    /**
     * Get city-specific plant trends and preferences
     */
    public function getCitySpecificTrends($city, $days = 30)
    {
        $cityTrends = [
            'milano' => $this->getMilanoTrends(),
            'roma' => $this->getRomaTrends(),
            'napoli' => $this->getNapoliTrends(),
            'torino' => $this->getTorinoTrends(),
            'firenze' => $this->getFirenzeTrends(),
            'bologna' => $this->getBolognaTrends(),
            'venezia' => $this->getVeneziaTrends(),
            'palermo' => $this->getPalermoTrends(),
        ];

        return $cityTrends[$city] ?? $this->getGenericCityTrends();
    }

    /**
     * Get regional market analysis for better targeting
     */
    public function getRegionalMarketAnalysis($region, $days = 30)
    {
        $regionalAnalysis = [
            'nord' => $this->getNorthMarketAnalysis(),
            'centro' => $this->getCentralMarketAnalysis(),
            'sud' => $this->getSouthMarketAnalysis(),
        ];

        return $regionalAnalysis[$region] ?? $this->getGenericRegionalAnalysis();
    }

    /**
     * Get demographic information for cities
     */
    public function getCityDemographics($city)
    {
        $demographics = [
            'milano' => [
                'population' => 1400000,
                'avg_age' => 35,
                'income_level' => 'Alto',
                'education_level' => 'Università+',
                'housing_type' => 'Appartamenti',
                'green_space_per_capita' => '17.4 m²',
                'plant_buyer_profile' => 'Millennials professionali'
            ],
            'roma' => [
                'population' => 2800000,
                'avg_age' => 38,
                'income_level' => 'Medio-Alto',
                'education_level' => 'Mista',
                'housing_type' => 'Misto',
                'green_space_per_capita' => '13.8 m²',
                'plant_buyer_profile' => 'Famiglie e professionisti'
            ],
            'napoli' => [
                'population' => 950000,
                'avg_age' => 42,
                'income_level' => 'Medio',
                'education_level' => 'Mista',
                'housing_type' => 'Appartamenti tradizionali',
                'green_space_per_capita' => '11.2 m²',
                'plant_buyer_profile' => 'Famiglie tradizionali'
            ]
        ];
        
        return $demographics[$city] ?? [
            'population' => 100000,
            'plant_buyer_profile' => 'Misto'
        ];
    }

    private function getMillennialTrends()
    {
        return [
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
        ];
    }

    private function getGenZTrends()
    {
        return [
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
        ];
    }

    private function getGenXTrends()
    {
        return [
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
        ];
    }

    private function getBabyBoomerTrends()
    {
        return [
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
        ];
    }

    private function getIncomeSegmentTrends()
    {
        return [
            'high_income' => [
                'range' => '€50k+',
                'preferences' => ['Premium plants', 'Rare varieties', 'Professional services'],
                'avg_order_value' => '€75-150'
            ],
            'medium_income' => [
                'range' => '€25-50k',
                'preferences' => ['Quality plants', 'Popular varieties', 'DIY approach'],
                'avg_order_value' => '€35-75'
            ],
            'low_income' => [
                'range' => '<€25k',
                'preferences' => ['Budget plants', 'Easy care', 'Value packs'],
                'avg_order_value' => '€15-35'
            ]
        ];
    }

    private function getUrbanRuralTrends()
    {
        return [
            'urban' => [
                'characteristics' => ['Limited space', 'Indoor focus', 'Convenience important'],
                'top_categories' => ['Houseplants', 'Balcony plants', 'Vertical gardens'],
                'delivery_preference' => 'Same/next day'
            ],
            'suburban' => [
                'characteristics' => ['More space', 'Mixed indoor/outdoor', 'Family-oriented'],
                'top_categories' => ['Garden plants', 'Family herbs', 'Children-safe plants'],
                'delivery_preference' => 'Standard delivery'
            ],
            'rural' => [
                'characteristics' => ['Large spaces', 'Outdoor focus', 'Self-sufficient'],
                'top_categories' => ['Agricultural plants', 'Fruit trees', 'Bulk orders'],
                'delivery_preference' => 'Planned delivery'
            ]
        ];
    }

    // Regional preferences methods
    private function getNorthItalyPreferences()
    {
        return [
            'regions' => ['Lombardia', 'Piemonte', 'Veneto', 'Liguria'],
            'climate_factors' => ['Continental', 'Humid winters', 'Hot summers'],
            'popular_plants' => [
                'indoor' => ['Ficus', 'Dracaena', 'Philodendron', 'Monstera'],
                'outdoor' => ['Gerani', 'Ortensie', 'Azalee', 'Rododendri'],
                'herbs' => ['Basilico', 'Rosmarino', 'Salvia', 'Timo']
            ],
            'market_characteristics' => [
                'urban_gardening' => 'High demand',
                'balcony_plants' => 'Very high',
                'indoor_plants' => 'High',
                'traditional_gardening' => 'Medium'
            ]
        ];
    }

    private function getCentralItalyPreferences()
    {
        return [
            'regions' => ['Toscana', 'Lazio', 'Umbria', 'Marche'],
            'climate_factors' => ['Mediterranean', 'Mild winters', 'Dry summers'],
            'popular_plants' => [
                'indoor' => ['Olivo indoor', 'Limoni', 'Ficus', 'Palme'],
                'outdoor' => ['Lavanda', 'Rosmarino', 'Ulivi', 'Cipressi'],
                'herbs' => ['Origano', 'Maggiorana', 'Salvia', 'Alloro']
            ],
            'market_characteristics' => [
                'mediterranean_plants' => 'Very high',
                'drought_resistant' => 'High',
                'aromatic_herbs' => 'Very high',
                'traditional_varieties' => 'High'
            ]
        ];
    }

    private function getSouthItalyPreferences()
    {
        return [
            'regions' => ['Campania', 'Puglia', 'Calabria', 'Basilicata'],
            'climate_factors' => ['Mediterranean', 'Hot dry summers', 'Mild winters'],
            'popular_plants' => [
                'indoor' => ['Limoni', 'Ficus', 'Palme', 'Bougainvillea'],
                'outdoor' => ['Agrumi', 'Bougainvillea', 'Oleandri', 'Palme'],
                'herbs' => ['Basilico', 'Origano', 'Peperoncino', 'Pomodori']
            ],
            'market_characteristics' => [
                'citrus_plants' => 'Very high',
                'heat_resistant' => 'Very high',
                'edible_plants' => 'High',
                'flowering_shrubs' => 'High'
            ]
        ];
    }

    private function getIslandsPreferences()
    {
        return [
            'regions' => ['Sicilia', 'Sardegna'],
            'climate_factors' => ['Mediterranean island', 'Strong winds', 'Salt air'],
            'popular_plants' => [
                'indoor' => ['Agrumi nani', 'Palme indoor', 'Succulente'],
                'outdoor' => ['Palme', 'Pini marittimi', 'Piante resistenti vento'],
                'herbs' => ['Capperi', 'Rosmarino marino', 'Origano siciliano']
            ],
            'market_characteristics' => [
                'wind_resistant' => 'Very high',
                'salt_tolerant' => 'High',
                'endemic_species' => 'Medium',
                'drought_resistant' => 'Very high'
            ]
        ];
    }

    // City-specific trends methods
    private function getMilanoTrends()
    {
        return [
            'climate_zone' => 'Continentale temperato',
            'urban_characteristics' => ['Alta densità', 'Piccoli spazi', 'Inquinamento'],
            'top_plant_categories' => [
                'piante_purificatrici' => ['demand' => 95, 'growth' => '+45%'],
                'piante_da_ufficio' => ['demand' => 92, 'growth' => '+38%'],
                'piante_compatte' => ['demand' => 88, 'growth' => '+32%'],
                'vertical_gardens' => ['demand' => 85, 'growth' => '+55%']
            ],
            'market_insights' => [
                'avg_order_value' => '€45',
                'delivery_preference' => 'Same-day o next-day',
                'peak_shopping_hours' => '18:00-21:00',
                'preferred_categories' => 'Premium, design-focused'
            ]
        ];
    }

    private function getRomaTrends()
    {
        return [
            'climate_zone' => 'Mediterraneo',
            'urban_characteristics' => ['Clima mite', 'Terrazze ampie', 'Luce abbondante'],
            'top_plant_categories' => [
                'piante_mediterranee' => ['demand' => 98, 'growth' => '+42%'],
                'cactus_succulente' => ['demand' => 90, 'growth' => '+35%'],
                'erbe_aromatiche' => ['demand' => 95, 'growth' => '+50%'],
                'agrumi_ornamentali' => ['demand' => 88, 'growth' => '+40%']
            ],
            'market_insights' => [
                'avg_order_value' => '€52',
                'delivery_preference' => 'Flessibile, weekend OK',
                'peak_shopping_hours' => '16:00-19:00',
                'preferred_categories' => 'Tradizionale, mediterraneo'
            ]
        ];
    }

    private function getNapoliTrends()
    {
        return [
            'climate_zone' => 'Mediterraneo caldo',
            'urban_characteristics' => ['Clima caldo-umido', 'Balconi piccoli', 'Forte tradizione'],
            'top_plant_categories' => [
                'basilico_varieta' => ['demand' => 100, 'growth' => '+60%'],
                'peperoncini' => ['demand' => 95, 'growth' => '+45%'],
                'piante_resistenti_caldo' => ['demand' => 90, 'growth' => '+38%'],
                'fiori_tradizionali' => ['demand' => 88, 'growth' => '+30%']
            ],
            'market_insights' => [
                'avg_order_value' => '€28',
                'delivery_preference' => 'Quartiere, conoscenza locale',
                'peak_shopping_hours' => '9:00-11:00, 17:00-19:00',
                'preferred_categories' => 'Commestibile, tradizionale'
            ]
        ];
    }

    private function getTorinoTrends()
    {
        return [
            'climate_zone' => 'Continentale freddo',
            'urban_characteristics' => ['Inverni rigidi', 'Nebbia frequente', 'Architettura storica'],
            'top_plant_categories' => [
                'piante_resistenti_freddo' => ['demand' => 95, 'growth' => '+40%'],
                'piante_poca_luce' => ['demand' => 92, 'growth' => '+45%'],
                'sempreverdi_compatti' => ['demand' => 88, 'growth' => '+35%'],
                'piante_riscaldamento' => ['demand' => 85, 'growth' => '+30%']
            ],
            'market_insights' => [
                'avg_order_value' => '€38',
                'delivery_preference' => 'Pianificata, evitare freddo',
                'peak_shopping_hours' => '14:00-16:00',
                'preferred_categories' => 'Resistente, elegante'
            ]
        ];
    }

    private function getFirenzeTrends()
    {
        return [
            'climate_zone' => 'Mediterraneo continentale',
            'urban_characteristics' => ['Centro storico', 'Turismo', 'Cultura del bello'],
            'top_plant_categories' => [
                'piante_ornamentali' => ['demand' => 95, 'growth' => '+35%'],
                'piante_storiche' => ['demand' => 88, 'growth' => '+28%'],
                'erbe_toscane' => ['demand' => 92, 'growth' => '+40%']
            ]
        ];
    }

    private function getBolognaTrends()
    {
        return [
            'climate_zone' => 'Continentale padano',
            'urban_characteristics' => ['Città universitaria', 'Gastronomia', 'Innovazione'],
            'top_plant_categories' => [
                'erbe_culinarie' => ['demand' => 98, 'growth' => '+50%'],
                'piante_innovative' => ['demand' => 85, 'growth' => '+45%']
            ]
        ];
    }

    private function getVeneziaTrends()
    {
        return [
            'climate_zone' => 'Continentale umido',
            'urban_characteristics' => ['Umidità alta', 'Spazi limitati', 'Turismo'],
            'top_plant_categories' => [
                'piante_umidita' => ['demand' => 90, 'growth' => '+38%'],
                'piante_container' => ['demand' => 88, 'growth' => '+42%']
            ]
        ];
    }

    private function getPalermoTrends()
    {
        return [
            'climate_zone' => 'Mediterraneo arido',
            'urban_characteristics' => ['Caldo secco', 'Vento', 'Tradizione'],
            'top_plant_categories' => [
                'piante_aride' => ['demand' => 95, 'growth' => '+40%'],
                'agrumi_siciliani' => ['demand' => 100, 'growth' => '+55%']
            ]
        ];
    }

    private function getGenericCityTrends()
    {
        return [
            'climate_zone' => 'Temperato',
            'top_plant_categories' => [
                'piante_generiche' => ['demand' => 75, 'growth' => '+25%']
            ],
            'market_insights' => [
                'avg_order_value' => '€35',
                'preferred_categories' => 'Miste'
            ]
        ];
    }

    // Market analysis methods
    private function getNorthMarketAnalysis()
    {
        return [
            'economic_profile' => 'Alto potere d\'acquisto',
            'market_size' => 'Grande',
            'competition_level' => 'Alta',
            'price_sensitivity' => 'Bassa',
            'preferred_channels' => ['E-commerce', 'Garden center premium', 'Mercati specializzati'],
            'seasonal_revenue_pattern' => [
                'Q1' => '€850k-1.2M',
                'Q2' => '€1.5M-2.1M',
                'Q3' => '€1.2M-1.8M',
                'Q4' => '€1.1M-1.6M'
            ]
        ];
    }

    private function getCentralMarketAnalysis()
    {
        return [
            'economic_profile' => 'Medio-alto potere d\'acquisto',
            'market_size' => 'Medio-grande',
            'competition_level' => 'Media',
            'price_sensitivity' => 'Media',
            'preferred_channels' => ['Vivai tradizionali', 'E-commerce', 'Mercati locali'],
            'seasonal_revenue_pattern' => [
                'Q1' => '€420k-650k',
                'Q2' => '€780k-1.2M',
                'Q3' => '€650k-950k',
                'Q4' => '€550k-800k'
            ]
        ];
    }

    private function getSouthMarketAnalysis()
    {
        return [
            'economic_profile' => 'Medio potere d\'acquisto',
            'market_size' => 'Grande potenziale',
            'competition_level' => 'Bassa-Media',
            'price_sensitivity' => 'Alta',
            'preferred_channels' => ['Mercati locali', 'Vivai familiari', 'Passaparola'],
            'seasonal_revenue_pattern' => [
                'Q1' => '€280k-420k',
                'Q2' => '€520k-780k',
                'Q3' => '€450k-680k',
                'Q4' => '€380k-570k'
            ]
        ];
    }

    private function getGenericRegionalAnalysis()
    {
        return [
            'economic_profile' => 'Medio',
            'market_size' => 'Medio',
            'competition_level' => 'Media',
            'growth_opportunities' => []
        ];
    }
}
