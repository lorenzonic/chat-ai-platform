<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🌱 CREDENZIALI GROWER BONINI\n";
echo str_repeat("=", 60) . "\n\n";

$boniniGrowersBP = App\Models\Grower::where('name', 'LIKE', '%Bonini Paolo%')->first();
$boniniGrowersFlor = App\Models\Grower::where('name', 'LIKE', '%Boniniflor%')->first();

if ($boniniGrowersBP) {
    echo "👤 BONINI PAOLO AZ. AGR. (BP)\n";
    echo "   ID: " . $boniniGrowersBP->id . "\n";
    echo "   Email: " . $boniniGrowersBP->email . "\n";
    echo "   Password: password123\n";
    echo "   Login URL: http://localhost:8000/grower/login\n";
    echo "   Production URL: [URL-RAILWAY]/grower/login\n\n";
}

if ($boniniGrowersFlor) {
    echo "👤 BONINIFLOR (TBR)\n";
    echo "   ID: " . $boniniGrowersFlor->id . "\n";
    echo "   Email: " . $boniniGrowersFlor->email . "\n";
    echo "   Password: password123\n";
    echo "   Login URL: http://localhost:8000/grower/login\n";
    echo "   Production URL: [URL-RAILWAY]/grower/login\n\n";
}

// Show all Bonini growers if there are more
$allBonini = App\Models\Grower::where('name', 'LIKE', '%Bonini%')->get();

echo "📊 RIEPILOGO COMPLETO BONINI GROWERS:\n";
echo str_repeat("-", 60) . "\n";

foreach ($allBonini as $grower) {
    echo "ID {$grower->id}: {$grower->name}\n";
    echo "  📧 Email: {$grower->email}\n";
    echo "  🔑 Password: password123\n";
    echo "  📦 Prodotti: " . App\Models\Product::where('grower_id', $grower->id)->count() . "\n";
    echo "  📋 Ordini: " . App\Models\OrderItem::where('grower_id', $grower->id)->count() . " order items\n";
    echo "\n";
}

echo "🚀 NOTA: Tutte le password dei growers usano il default 'password123'\n";
echo "🌐 Per production: sostituisci [URL-RAILWAY] con l'URL effettivo di Railway\n";
?>