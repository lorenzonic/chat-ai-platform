<?php

/**
 * Test Analytics Data Generation
 * Verifica i dati real delle analytics
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Store;
use App\Models\Interaction;
use App\Models\ChatLog;
use Illuminate\Support\Facades\DB;

echo "🧪 TEST ANALYTICS DATA\n";
echo "=====================\n\n";

// Get first store
$store = Store::first();

if (!$store) {
    echo "❌ Nessuno store trovato nel database\n";
    exit;
}

echo "🏪 Store: {$store->name} (ID: {$store->id})\n\n";

// Test 1: Frequent Questions
echo "📋 TEST 1: Domande Frequenti\n";
echo "----------------------------\n";

$questions = Interaction::where('store_id', $store->id)
    ->whereNotNull('question')
    ->where('question', '!=', '')
    ->select('question', DB::raw('count(*) as count'))
    ->groupBy('question')
    ->orderByDesc('count')
    ->limit(10)
    ->get();

echo "Totale domande uniche: " . $questions->count() . "\n";

if ($questions->count() > 0) {
    foreach ($questions as $q) {
        echo "  • {$q->question} ({$q->count} volte)\n";
    }
} else {
    echo "  ⚠️ Nessuna domanda trovata negli interactions\n";
}

echo "\n";

// Test 2: Popular Plants
echo "🌱 TEST 2: Piante Più Ricercate\n";
echo "-------------------------------\n";

$plantKeywords = [
    'rosa', 'rose', 'basilico', 'lavanda', 'geranio', 'cactus', 'orchidea',
    'ficus', 'pothos', 'succulenta', 'succulente', 'petunia', 'begonia'
];

$plantCounts = [];

// Search in interactions
foreach ($plantKeywords as $plant) {
    $count = Interaction::where('store_id', $store->id)
        ->where(function($query) use ($plant) {
            $query->where('question', 'LIKE', "%{$plant}%")
                  ->orWhere('answer', 'LIKE', "%{$plant}%");
        })
        ->count();

    if ($count > 0) {
        $plantCounts[$plant] = ($plantCounts[$plant] ?? 0) + $count;
    }
}

// Search in chat logs
foreach ($plantKeywords as $plant) {
    $count = ChatLog::where('store_id', $store->id)
        ->where(function($query) use ($plant) {
            $query->where('user_message', 'LIKE', "%{$plant}%")
                  ->orWhere('ai_response', 'LIKE', "%{$plant}%");
        })
        ->count();

    if ($count > 0) {
        $plantCounts[$plant] = ($plantCounts[$plant] ?? 0) + $count;
    }
}

arsort($plantCounts);
$topPlants = array_slice($plantCounts, 0, 8, true);

echo "Totale piante trovate: " . count($topPlants) . "\n";

if (count($topPlants) > 0) {
    foreach ($topPlants as $plant => $count) {
        echo "  • " . ucfirst($plant) . " ({$count} menzioni)\n";
    }
} else {
    echo "  ⚠️ Nessuna pianta trovata nelle conversazioni\n";
}

echo "\n";

// Test 3: Database Counts
echo "📊 TEST 3: Contatori Database\n";
echo "-----------------------------\n";
echo "Total Interactions: " . Interaction::where('store_id', $store->id)->count() . "\n";
echo "Total ChatLogs: " . ChatLog::where('store_id', $store->id)->count() . "\n";
echo "Interactions con domande: " . Interaction::where('store_id', $store->id)->whereNotNull('question')->count() . "\n";

echo "\n✅ Test completato!\n";
