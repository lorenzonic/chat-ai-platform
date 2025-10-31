<?php

/**
 * Generate Test Analytics Data
 * Crea dati di test per le analytics
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Store;
use App\Models\Interaction;
use App\Models\ChatLog;

echo "🌱 GENERAZIONE DATI TEST ANALYTICS\n";
echo "===================================\n\n";

// Get first store
$store = Store::first();

if (!$store) {
    echo "❌ Nessuno store trovato\n";
    exit;
}

echo "🏪 Store: {$store->name} (ID: {$store->id})\n\n";

// Domande di test
$testQuestions = [
    "Come si cura una rosa?",
    "Quando annaffiare il basilico?",
    "Quali sono le piante grasse più facili?",
    "Come potare la lavanda?",
    "Il geranio ha bisogno di sole?",
    "Come curare un'orchidea in casa?",
    "Consigli per coltivare il ficus?",
    "Il pothos ha bisogno di molta acqua?",
    "Come si cura una rosa?", // duplicate per test
    "Quando annaffiare il basilico?", // duplicate per test
];

echo "📝 Creazione interactions di test...\n";

foreach ($testQuestions as $index => $question) {
    $response = "Ecco alcuni consigli per " . strtolower($question);

    Interaction::create([
        'store_id' => $store->id,
        'session_id' => 'test_session_' . ($index % 3), // 3 sessioni diverse
        'question' => $question,
        'answer' => $response,
        'device' => ['Desktop', 'Mobile', 'Tablet'][rand(0, 2)],
        'browser' => ['Chrome', 'Firefox', 'Safari'][rand(0, 2)],
        'os' => ['Windows', 'macOS', 'iOS', 'Android'][rand(0, 3)],
        'ip_address' => '192.168.1.' . rand(1, 254),
        'created_at' => now()->subDays(rand(0, 7)),
    ]);
}

echo "✅ Creati " . count($testQuestions) . " interactions\n\n";

// Chat logs con menzioni di piante
$plantChats = [
    ['user' => 'Ciao, vorrei comprare delle rose rosse', 'ai' => 'Abbiamo bellissime rose rosse!'],
    ['user' => 'Avete del basilico fresco?', 'ai' => 'Sì, abbiamo basilico genovese'],
    ['user' => 'Cercavo della lavanda', 'ai' => 'La lavanda è molto profumata'],
    ['user' => 'Vorrei dei gerani per il balcone', 'ai' => 'I gerani sono perfetti per i balconi'],
    ['user' => 'Ho un cactus che sta male', 'ai' => 'Probabilmente il cactus ha ricevuto troppa acqua'],
    ['user' => 'Vorrei un\'orchidea come regalo', 'ai' => 'Le orchidee sono regali eleganti'],
    ['user' => 'Il mio ficus perde le foglie', 'ai' => 'Il ficus potrebbe aver bisogno di più luce'],
    ['user' => 'Come si propaga il pothos?', 'ai' => 'Il pothos si propaga facilmente per talea'],
];

echo "💬 Creazione chat logs di test...\n";

foreach ($plantChats as $index => $chat) {
    ChatLog::create([
        'store_id' => $store->id,
        'session_id' => 'chat_session_' . ($index % 4),
        'user_message' => $chat['user'],
        'ai_response' => $chat['ai'],
        'created_at' => now()->subDays(rand(0, 14)),
    ]);
}

echo "✅ Creati " . count($plantChats) . " chat logs\n\n";

// Riepilogo finale
echo "📊 RIEPILOGO DATI\n";
echo "-----------------\n";
echo "Total Interactions: " . Interaction::where('store_id', $store->id)->count() . "\n";
echo "Total ChatLogs: " . ChatLog::where('store_id', $store->id)->count() . "\n";
echo "Domande uniche: " . Interaction::where('store_id', $store->id)
    ->whereNotNull('question')
    ->distinct('question')
    ->count('question') . "\n";

echo "\n✅ Dati di test generati con successo!\n";
echo "🌐 Vai su /store/analytics per visualizzare i risultati\n";
