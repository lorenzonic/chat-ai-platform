<?php

// Test store settings
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$store = \App\Models\Store::first();

if ($store) {
    echo "🏪 Store Settings:\n";
    echo "Name: " . $store->name . "\n";
    echo "Theme Color: " . ($store->chat_theme_color ?? 'default') . "\n";
    echo "Font: " . ($store->chat_font_family ?? 'default') . "\n";
    echo "Assistant Name: " . ($store->assistant_name ?? 'default') . "\n";
    echo "AI Tone: " . ($store->chat_ai_tone ?? 'default') . "\n";
    echo "Avatar: " . ($store->chat_avatar_image ?? 'none') . "\n";
    echo "Suggestions: " . ($store->chat_suggestions ? json_encode($store->chat_suggestions) : 'none') . "\n";
    echo "Chat Enabled: " . ($store->chat_enabled ? 'yes' : 'no') . "\n";
} else {
    echo "❌ No store found\n";
}
