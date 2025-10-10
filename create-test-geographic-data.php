<?php

require_once 'vendor/autoload.php';

use App\Models\Lead;
use App\Models\Interaction;
use App\Models\Store;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Get the first store (store ID 19 from the logs)
    $store = Store::find(19);

    if (!$store) {
        echo "❌ Store non trovato\n";
        exit;
    }

    echo "✅ Store trovato: {$store->name} (ID: {$store->id})\n";

    // Create test geographic data
    $testLocations = [
        ['lat' => 41.9028, 'lng' => 12.4964, 'city' => 'Roma', 'country' => 'Italia'],
        ['lat' => 45.4642, 'lng' => 9.1900, 'city' => 'Milano', 'country' => 'Italia'],
        ['lat' => 40.8518, 'lng' => 14.2681, 'city' => 'Napoli', 'country' => 'Italia'],
        ['lat' => 45.0703, 'lng' => 7.6869, 'city' => 'Torino', 'country' => 'Italia'],
        ['lat' => 44.4949, 'lng' => 11.3426, 'city' => 'Bologna', 'country' => 'Italia'],
        ['lat' => 43.7696, 'lng' => 11.2558, 'city' => 'Firenze', 'country' => 'Italia'],
    ];

    echo "\n🌍 Creazione dati geografici di test...\n";

    // Create test interactions with geographic data
    foreach ($testLocations as $i => $location) {
        // Create 2-5 interactions per location
        $interactionCount = rand(2, 5);

        for ($j = 0; $j < $interactionCount; $j++) {
            Interaction::create([
                'store_id' => $store->id,
                'question' => "Domanda di test da {$location['city']} #{$j}",
                'answer' => "Risposta automatica per {$location['city']}",
                'ip' => '192.168.1.' . rand(1, 254),
                'latitude' => $location['lat'] + (rand(-100, 100) / 10000), // Small variation
                'longitude' => $location['lng'] + (rand(-100, 100) / 10000),
                'city' => $location['city'],
                'country' => $location['country'],
                'device_type' => ['mobile', 'desktop', 'tablet'][rand(0, 2)],
                'browser' => ['Chrome', 'Firefox', 'Safari', 'Edge'][rand(0, 3)],
                'duration' => rand(30, 300),
                'created_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        // Create 1-3 leads per location
        $leadCount = rand(1, 3);

        for ($j = 0; $j < $leadCount; $j++) {
            Lead::create([
                'store_id' => $store->id,
                'name' => "Cliente Test {$location['city']} #{$j}",
                'email' => "test{$i}_{$j}@{$location['city']}.com",
                'whatsapp' => '+39' . rand(300, 399) . rand(1000000, 9999999),
                'latitude' => $location['lat'] + (rand(-50, 50) / 10000),
                'longitude' => $location['lng'] + (rand(-50, 50) / 10000),
                'city' => $location['city'],
                'country' => $location['country'],
                'source' => ['chatbot', 'qr_code', 'website'][rand(0, 2)],
                'created_at' => now()->subDays(rand(1, 15)),
            ]);
        }

        echo "  📍 {$location['city']}: {$interactionCount} interazioni, {$leadCount} lead\n";
    }

    // Count totals
    $totalInteractions = Interaction::where('store_id', $store->id)->count();
    $totalLeads = Lead::where('store_id', $store->id)->count();
    $withGeo = Interaction::where('store_id', $store->id)->whereNotNull('latitude')->count();

    echo "\n📊 Totali creati:\n";
    echo "  💬 Interazioni: {$totalInteractions} (con geo: {$withGeo})\n";
    echo "  🎯 Lead: {$totalLeads}\n";
    echo "\n✅ Dati di test creati con successo!\n";

} catch (Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
