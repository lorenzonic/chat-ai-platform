<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Interaction;

// Update interactions to have latitude/longitude
$interactions = Interaction::where('store_id', 19)
    ->whereNull('latitude')
    ->get();

$cities = [
    ['lat' => 41.9028, 'lng' => 12.4964, 'city' => 'Roma'],
    ['lat' => 45.4642, 'lng' => 9.1900, 'city' => 'Milano'],
    ['lat' => 40.8518, 'lng' => 14.2681, 'city' => 'Napoli'],
    ['lat' => 45.0703, 'lng' => 7.6869, 'city' => 'Torino'],
    ['lat' => 44.4949, 'lng' => 11.3426, 'city' => 'Bologna'],
    ['lat' => 43.7696, 'lng' => 11.2558, 'city' => 'Firenze'],
];

$updated = 0;
foreach ($interactions as $interaction) {
    $city = $cities[array_rand($cities)];

    $interaction->update([
        'latitude' => $city['lat'] + (rand(-100, 100) / 10000),
        'longitude' => $city['lng'] + (rand(-100, 100) / 10000),
        'city' => $city['city'],
        'country' => 'Italia'
    ]);

    $updated++;
}

echo "✅ Aggiornate {$updated} interazioni con coordinate geografiche\n";
