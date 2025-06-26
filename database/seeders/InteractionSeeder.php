<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Interaction;
use App\Models\Store;
use Carbon\Carbon;

class InteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $store = Store::first();

        if (!$store) {
            echo "Nessuno store trovato. Esegui prima StoreSeeder.\n";
            return;
        }

        $questions = [
            'Quali sono i vostri orari?',
            'Dove vi trovate?',
            'Come curare un pothos?',
            'Che piante consigliate per principianti?',
            'Quanto costa una monstera?',
            'Fate consegne a domicilio?',
            'Avete vasi in ceramica?',
            'Come innaffiare le piante grasse?',
            'Che terra usare per le orchidee?',
            'Vendete fertilizzanti biologici?',
            'Come eliminare i parassiti dalle piante?',
            'Che piante stanno bene in bagno?',
            'Avete servizio di potatura?',
            'Quando rinvasare le piante?',
            'Che piante purificano l\'aria?'
        ];

        $answers = [
            'Siamo aperti dal lunedì al sabato dalle 9:00 alle 19:00.',
            'Ci troviamo in via Roma 123, nel centro città.',
            'Il pothos ama la luce indiretta e annaffiature moderate.',
            'Consiglio pothos, sansevieria e zz plant per iniziare.',
            'Le monstera variano da 15€ a 50€ secondo la dimensione.',
            'Sì, facciamo consegne gratuite sopra i 30€.',
            'Abbiamo una vasta selezione di vasi in ceramica.',
            'Le piante grasse si annaffiano solo quando il terreno è asciutto.',
            'Per le orchidee usa un terriccio specifico ben drenante.',
            'Abbiamo fertilizzanti biologici di ottima qualità.',
            'Usa olio di neem o sapone molle contro i parassiti.',
            'In bagno stanno bene felci, pothos e sansevieria.',
            'Offriamo servizio di potatura su appuntamento.',
            'Il rinvaso si fa in primavera quando le radici escono dai fori.',
            'Sansevieria, pothos e ficus benjamin purificano l\'aria.'
        ];

        $devices = ['mobile', 'desktop', 'tablet'];
        $browsers = ['Chrome', 'Firefox', 'Safari', 'Edge'];
        $os = ['Windows', 'macOS', 'iOS', 'Android', 'Linux'];
        $utmSources = ['google', 'facebook', 'instagram', 'direct', null];
        $utmMediums = ['organic', 'cpc', 'social', 'email', null];

        // Genera 100 interazioni negli ultimi 30 giorni
        for ($i = 0; $i < 100; $i++) {
            $questionIndex = array_rand($questions);
            $createdAt = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            Interaction::create([
                'store_id' => $store->id,
                'session_id' => 'session_' . uniqid(),
                'question' => $questions[$questionIndex],
                'answer' => $answers[$questionIndex],
                'ip' => rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255),
                'user_agent' => 'Mozilla/5.0 Test Agent',
                'utm_source' => $utmSources[array_rand($utmSources)],
                'utm_medium' => $utmMediums[array_rand($utmMediums)],
                'utm_campaign' => rand(0, 1) ? 'campaign_' . rand(1, 5) : null,
                'ref_code' => rand(0, 1) ? 'qr_' . rand(1, 10) : null,
                'qr_code_id' => null,
                'duration' => rand(30, 300), // 30 secondi a 5 minuti
                'device_type' => $devices[array_rand($devices)],
                'browser' => $browsers[array_rand($browsers)],
                'os' => $os[array_rand($os)],
                'metadata' => [
                    'test_data' => true,
                    'generated_at' => now()->toISOString()
                ],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        echo "Creati 100 record di interazioni di test per il store: {$store->name}\n";
    }
}
