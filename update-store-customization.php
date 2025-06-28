<?php

// Update store with custom suggestions
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$store = \App\Models\Store::first();

if ($store) {
    // Aggiungi suggerimenti personalizzati
    $customSuggestions = [
        '🌱 Che piante sono perfette per iniziare?',
        '🌿 Piante purificatrici d\'aria per casa?',
        '🌸 Fiori per balcone soleggiato?',
        '🪴 Piante grasse facili da curare?',
        '🌳 Alberi da frutto per giardino?'
    ];

    $store->update([
        'chat_suggestions' => json_encode($customSuggestions),
        'chat_theme_color' => '#2563eb', // Blu moderno
        'chat_font_family' => 'Poppins',
        'assistant_name' => 'Verde Bot',
        'chat_ai_tone' => 'friendly'
    ]);

    echo "✅ Store updated with custom settings:\n";
    echo "Theme Color: " . $store->chat_theme_color . "\n";
    echo "Font: " . $store->chat_font_family . "\n";
    echo "Assistant: " . $store->assistant_name . "\n";
    echo "Suggestions count: " . count($customSuggestions) . "\n";

    // Test anche un secondo store se esiste
    $store2 = \App\Models\Store::skip(1)->first();
    if ($store2) {
        $store2->update([
            'chat_theme_color' => '#dc2626', // Rosso
            'chat_font_family' => 'Roboto',
            'assistant_name' => 'Fiore AI',
            'chat_suggestions' => json_encode([
                '💐 Bouquet per matrimonio?',
                '🌹 Rose rosse disponibili?',
                '🎁 Composizioni regalo?',
                '🌺 Fiori di stagione?'
            ])
        ]);
        echo "✅ Second store updated too: " . $store2->name . "\n";
    }

} else {
    echo "❌ No store found\n";
}
