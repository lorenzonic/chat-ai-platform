<?php
/**
 * Plant E-commerce Scraper Simulator
 * Simulates real e-commerce data from Italian plant retailers
 */

function simulateEcommerceScraping() {
    // Simulate data from real Italian plant retailers
    $sites = ['Viridea', 'Bakker Italia', 'Mondo Piante', 'Euro3plast Garden'];

    $plants = [
        // Indoor plants - high demand
        ['name' => 'Monstera Deliciosa 40cm', 'price' => 29.90, 'availability' => 'Disponibile', 'category' => 'monstera', 'popularity' => 95, 'trend' => 'explosive'],
        ['name' => 'Monstera Thai Constellation 15cm', 'price' => 299.00, 'availability' => 'Pochi pezzi', 'category' => 'monstera', 'popularity' => 100, 'trend' => 'explosive'],
        ['name' => 'Ficus Lyrata 120cm', 'price' => 89.90, 'availability' => 'Disponibile', 'category' => 'ficus', 'popularity' => 92, 'trend' => 'rising'],
        ['name' => 'Pothos Golden 20cm', 'price' => 12.90, 'availability' => 'Disponibile', 'category' => 'pothos', 'popularity' => 90, 'trend' => 'stable'],
        ['name' => 'Sansevieria Trifasciata 45cm', 'price' => 24.90, 'availability' => 'Disponibile', 'category' => 'sansevieria', 'popularity' => 88, 'trend' => 'stable'],

        // Rare plants
        ['name' => 'Philodendron Pink Princess 12cm', 'price' => 89.00, 'availability' => 'Pochi pezzi', 'category' => 'philodendron', 'popularity' => 98, 'trend' => 'explosive'],
        ['name' => 'Alocasia Zebrina 20cm', 'price' => 45.00, 'availability' => 'Pochi pezzi', 'category' => 'alocasia', 'popularity' => 94, 'trend' => 'explosive'],

        // Herbs & edible
        ['name' => 'Basilico Genovese vaso 14cm', 'price' => 3.50, 'availability' => 'Disponibile', 'category' => 'basilico', 'popularity' => 95, 'trend' => 'seasonal_peak'],
        ['name' => 'Rosmarino prostrato 16cm', 'price' => 4.90, 'availability' => 'Disponibile', 'category' => 'rosmarino', 'popularity' => 88, 'trend' => 'stable'],
        ['name' => 'Lavanda stoechas 18cm', 'price' => 6.90, 'availability' => 'Disponibile', 'category' => 'lavanda', 'popularity' => 90, 'trend' => 'rising'],

        // Succulents
        ['name' => 'Echeveria Blue Prince 8cm', 'price' => 8.90, 'availability' => 'Disponibile', 'category' => 'succulente', 'popularity' => 82, 'trend' => 'stable'],
        ['name' => 'Aloe Vera 25cm', 'price' => 15.90, 'availability' => 'Disponibile', 'category' => 'aloe', 'popularity' => 85, 'trend' => 'stable'],

        // Outdoor plants
        ['name' => 'Geranio zonale rosso 14cm', 'price' => 5.90, 'availability' => 'Disponibile', 'category' => 'gerani', 'popularity' => 92, 'trend' => 'seasonal_peak'],
        ['name' => 'Petunia grandiflora mix 12cm', 'price' => 4.50, 'availability' => 'Disponibile', 'category' => 'petunie', 'popularity' => 88, 'trend' => 'seasonal_peak'],
    ];

    // Assign random sources
    foreach ($plants as &$plant) {
        $plant['source'] = $sites[array_rand($sites)];
        $plant['stock_level'] = $plant['availability'] == 'Disponibile' ? 'Alto' : ($plant['availability'] == 'Pochi pezzi' ? 'Basso' : 'Esaurito');
        $plant['scraped_at'] = date('Y-m-d H:i:s');
    }

    return [
        'total_products' => count($plants),
        'sites_scraped' => $sites,
        'scraping_timestamp' => date('c'),
        'products' => $plants,
        'price_analysis' => [
            'average_price' => array_sum(array_column($plants, 'price')) / count($plants),
            'price_ranges' => [
                'budget' => ['min' => 3.50, 'max' => 15.00, 'count' => count(array_filter($plants, fn($p) => $p['price'] <= 15))],
                'mid_range' => ['min' => 15.01, 'max' => 50.00, 'count' => count(array_filter($plants, fn($p) => $p['price'] > 15 && $p['price'] <= 50))],
                'premium' => ['min' => 50.01, 'max' => 100.00, 'count' => count(array_filter($plants, fn($p) => $p['price'] > 50 && $p['price'] <= 100))],
                'luxury' => ['min' => 100.01, 'max' => 500.00, 'count' => count(array_filter($plants, fn($p) => $p['price'] > 100))]
            ],
            'trending_up_prices' => ['Monstera Thai Constellation', 'Philodendron Pink Princess', 'Alocasia Zebrina'],
            'best_value_picks' => ['Pothos Golden', 'Basilico Genovese', 'Sansevieria Trifasciata']
        ],
        'availability_analysis' => [
            'in_stock' => count(array_filter($plants, fn($p) => $p['availability'] == 'Disponibile')),
            'low_stock' => count(array_filter($plants, fn($p) => $p['availability'] == 'Pochi pezzi')),
            'out_of_stock' => count(array_filter($plants, fn($p) => $p['availability'] == 'Esaurito')),
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
        'category_performance' => [
            'indoor_plants' => ['growth' => '+45%', 'avg_price' => '€32.50', 'stock_turnover' => 'High'],
            'outdoor_plants' => ['growth' => '+25%', 'avg_price' => '€5.80', 'stock_turnover' => 'Seasonal'],
            'herbs' => ['growth' => '+60%', 'avg_price' => '€4.20', 'stock_turnover' => 'Very High'],
            'succulents' => ['growth' => '+15%', 'avg_price' => '€12.30', 'stock_turnover' => 'Medium'],
            'rare_plants' => ['growth' => '+120%', 'avg_price' => '€67.80', 'stock_turnover' => 'Low but High Margin']
        ],
        'insights' => [
            'high_demand_alert' => 'Monstera varieties showing 300% growth in searches',
            'stock_recommendation' => 'Increase Philodendron and Alocasia inventory by 40%',
            'seasonal_advice' => 'Prepare for spring herbs rush - order 2 weeks early',
            'price_optimization' => 'Premium indoor plants have 25% higher margins than outdoor',
            'competition_analysis' => 'Viridea leads in variety, Bakker in rare plants, price competitive across all platforms'
        ]
    ];
}

// Save to temporary file for Laravel to read
$data = simulateEcommerceScraping();
$outputDir = __DIR__ . '/../storage/app/temp';

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

file_put_contents($outputDir . '/ecommerce_advanced.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "✅ E-commerce data simulation completed!\n";
echo "📊 Generated data for " . $data['total_products'] . " products from " . count($data['sites_scraped']) . " sites\n";
echo "💾 Data saved to: storage/app/temp/ecommerce_advanced.json\n";
echo "🚀 Ready to view in dashboard!\n";

return $data;
?>
