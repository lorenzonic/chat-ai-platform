<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Interaction;

$count = Interaction::where('store_id', 19)->count();
$withGeo = Interaction::where('store_id', 19)->whereNotNull('latitude')->count();
$sample = Interaction::where('store_id', 19)->whereNotNull('latitude')->first();

echo "Total interactions: $count\n";
echo "With geo: $withGeo\n";

if ($sample) {
    echo "Sample: ID {$sample->id}, lat: {$sample->latitude}, lng: {$sample->longitude}, city: {$sample->city}\n";
} else {
    echo "No sample found\n";

    // Show all interactions
    $all = Interaction::where('store_id', 19)->get(['id', 'latitude', 'longitude', 'city']);
    foreach ($all as $interaction) {
        echo "ID {$interaction->id}: lat={$interaction->latitude}, lng={$interaction->longitude}, city={$interaction->city}\n";
    }
}
