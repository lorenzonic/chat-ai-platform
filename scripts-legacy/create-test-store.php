use App\Models\Store;

// Creo uno store di test se non esiste
$store = Store::where('slug', 'garden-center')->first();

if (!$store) {
    echo "Creando store di test...\n";
    $store = Store::create([
        'name' => 'Garden Center Test',
        'slug' => 'garden-center',
        'email' => 'test@gardencenter.com',
        'description' => 'Un garden center specializzato in piante e giardinaggio',
        'chat_enabled' => true,
        'assistant_name' => 'Verde',
        'chat_context' => 'Siamo esperti in piante da interno, piante grasse, cura del giardino e consulenza botanica',
        'chat_theme_color' => '#16a34a',
        'chat_ai_tone' => 'friendly',
        'is_premium' => true,
        'plan' => 'premium'
    ]);
    echo "Store creato: {$store->name} ({$store->slug})\n";
} else {
    echo "Store esistente: {$store->name} ({$store->slug})\n";
}

echo "ID Store: {$store->id}\n";
echo "Chat abilitata: " . ($store->chat_enabled ? 'Sì' : 'No') . "\n";
echo "Piano: {$store->plan}\n";
