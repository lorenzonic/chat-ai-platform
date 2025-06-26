<?php

use Illuminate\Database\Seeder;
use App\Models\Store;

class TestStoreSeeder extends Seeder
{
    public function run()
    {
        // Creo uno store di test se non esiste
        $store = Store::updateOrCreate(
            ['slug' => 'garden-center'],
            [
                'name' => 'Garden Center Test',
                'email' => 'test@gardencenter.com',
                'description' => 'Un garden center specializzato in piante e giardinaggio',
                'chat_enabled' => true,
                'assistant_name' => 'Verde',
                'chat_context' => 'Siamo esperti in piante da interno, piante grasse, cura del giardino e consulenza botanica',
                'chat_theme_color' => '#16a34a',
                'chat_ai_tone' => 'friendly',
                'is_premium' => true,
                'plan' => 'premium',
                'status' => 'active',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info("Store creato/aggiornato: {$store->name} (ID: {$store->id})");
        $this->command->info("Slug: {$store->slug}");
        $this->command->info("Chat abilitata: " . ($store->chat_enabled ? 'Sì' : 'No'));
    }
}
