<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Interaction;
use Illuminate\Support\Facades\Schema;

echo "=== Checking Interaction table structure ===\n";

// Check if columns exist
$hasLatitude = Schema::hasColumn('interactions', 'latitude');
$hasLongitude = Schema::hasColumn('interactions', 'longitude');
$hasCity = Schema::hasColumn('interactions', 'city');
$hasCountry = Schema::hasColumn('interactions', 'country');

echo "latitude column exists: " . ($hasLatitude ? 'Yes' : 'No') . "\n";
echo "longitude column exists: " . ($hasLongitude ? 'Yes' : 'No') . "\n";
echo "city column exists: " . ($hasCity ? 'Yes' : 'No') . "\n";
echo "country column exists: " . ($hasCountry ? 'Yes' : 'No') . "\n";

if (!$hasLatitude || !$hasLongitude) {
    echo "❌ Geographic columns missing! Need to add them to interactions table.\n";
    exit;
}

echo "\n=== Updating interactions with test data ===\n";

$cities = [
    ['lat' => 41.9028, 'lng' => 12.4964, 'city' => 'Roma'],
    ['lat' => 45.4642, 'lng' => 9.1900, 'city' => 'Milano'],
    ['lat' => 40.8518, 'lng' => 14.2681, 'city' => 'Napoli'],
    ['lat' => 45.0703, 'lng' => 7.6869, 'city' => 'Torino'],
    ['lat' => 44.4949, 'lng' => 11.3426, 'city' => 'Bologna'],
    ['lat' => 43.7696, 'lng' => 11.2558, 'city' => 'Firenze'],
];

$interactions = Interaction::where('store_id', 19)->get();
$updated = 0;

foreach ($interactions as $interaction) {
    $city = $cities[array_rand($cities)];

    $lat = $city['lat'] + (rand(-100, 100) / 10000);
    $lng = $city['lng'] + (rand(-100, 100) / 10000);

    try {
        $interaction->latitude = $lat;
        $interaction->longitude = $lng;
        $interaction->city = $city['city'];
        $interaction->country = 'Italia';
        $interaction->save();

        echo "Updated interaction {$interaction->id} -> {$city['city']} ({$lat}, {$lng})\n";
        $updated++;

    } catch (Exception $e) {
        echo "Error updating interaction {$interaction->id}: " . $e->getMessage() . "\n";
    }
}

echo "\n✅ Updated $updated interactions\n";

// Verify
$withGeo = Interaction::where('store_id', 19)->whereNotNull('latitude')->count();
echo "Interactions with geo data: $withGeo\n";
