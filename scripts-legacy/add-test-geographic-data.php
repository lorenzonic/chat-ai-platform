<?php

use App\Models\Store;
use App\Models\Interaction;
use App\Models\Lead;

// Test geographic data for map
echo "Adding test geographic data...\n";

$store = Store::where('slug', 'garden-center')->first();

if (!$store) {
    echo "Store not found. Please create a test store first.\n";
    exit(1);
}

// Test data for Italian cities
$testData = [
    [
        'city' => 'Roma',
        'region' => 'Lazio',
        'country' => 'Italia',
        'country_code' => 'IT',
        'latitude' => 41.9028,
        'longitude' => 12.4964,
        'interactions' => 5,
        'leads' => 2
    ],
    [
        'city' => 'Milano',
        'region' => 'Lombardia',
        'country' => 'Italia',
        'country_code' => 'IT',
        'latitude' => 45.4642,
        'longitude' => 9.1900,
        'interactions' => 8,
        'leads' => 3
    ],
    [
        'city' => 'Napoli',
        'region' => 'Campania',
        'country' => 'Italia',
        'country_code' => 'IT',
        'latitude' => 40.8518,
        'longitude' => 14.2681,
        'interactions' => 3,
        'leads' => 1
    ],
    [
        'city' => 'Torino',
        'region' => 'Piemonte',
        'country' => 'Italia',
        'country_code' => 'IT',
        'latitude' => 45.0703,
        'longitude' => 7.6869,
        'interactions' => 6,
        'leads' => 2
    ],
    [
        'city' => 'Firenze',
        'region' => 'Toscana',
        'country' => 'Italia',
        'country_code' => 'IT',
        'latitude' => 43.7696,
        'longitude' => 11.2558,
        'interactions' => 4,
        'leads' => 3
    ]
];

foreach ($testData as $location) {
    echo "Adding data for {$location['city']}...\n";

    // Create interactions for this location
    for ($i = 0; $i < $location['interactions']; $i++) {
        Interaction::create([
            'store_id' => $store->id,
            'session_id' => 'test-session-' . $location['city'] . '-' . $i,
            'question' => 'Test question from ' . $location['city'],
            'answer' => 'Test answer',
            'ip' => '192.168.1.' . rand(1, 255),
            'user_agent' => 'Mozilla/5.0 Test Browser',
            'device_type' => ['mobile', 'desktop', 'tablet'][rand(0, 2)],
            'browser' => ['Chrome', 'Firefox', 'Safari'][rand(0, 2)],
            'latitude' => $location['latitude'] + (rand(-100, 100) / 10000), // Small random offset
            'longitude' => $location['longitude'] + (rand(-100, 100) / 10000),
            'city' => $location['city'],
            'region' => $location['region'],
            'country' => $location['country'],
            'country_code' => $location['country_code'],
            'created_at' => now()->subDays(rand(0, 30)),
        ]);
    }

    // Create leads for this location
    for ($i = 0; $i < $location['leads']; $i++) {
        Lead::create([
            'store_id' => $store->id,
            'name' => 'Test User ' . $location['city'] . ' ' . $i,
            'email' => 'test' . $i . '@' . strtolower($location['city']) . '.com',
            'session_id' => 'test-session-lead-' . $location['city'] . '-' . $i,
            'source' => 'chatbot',
            'latitude' => $location['latitude'] + (rand(-100, 100) / 10000),
            'longitude' => $location['longitude'] + (rand(-100, 100) / 10000),
            'city' => $location['city'],
            'region' => $location['region'],
            'country' => $location['country'],
            'country_code' => $location['country_code'],
            'created_at' => now()->subDays(rand(0, 30)),
        ]);
    }
}

echo "Test geographic data added successfully!\n";
echo "Total interactions: " . Interaction::where('store_id', $store->id)->count() . "\n";
echo "Total leads: " . Lead::where('store_id', $store->id)->count() . "\n";
echo "Geographic interactions: " . Interaction::where('store_id', $store->id)->whereNotNull('latitude')->count() . "\n";
echo "Geographic leads: " . Lead::where('store_id', $store->id)->whereNotNull('latitude')->count() . "\n";
