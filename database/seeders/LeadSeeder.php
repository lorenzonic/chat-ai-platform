<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\Lead;

class LeadSeeder extends Seeder
{
    public function run()
    {
        // Prendi il primo store o creane uno
        $store = Store::first();

        if (!$store) {
            $store = Store::create([
                'name' => 'Test Garden Store',
                'slug' => 'test-garden-store',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'description' => 'Un negozio di piante per test',
                'is_active' => true,
                'is_premium' => true,
            ]);
        }

        // Crea alcuni lead di test
        $leads = [
            [
                'email' => 'mario.rossi@email.com',
                'name' => 'Mario Rossi',
                'whatsapp' => '+39 333 123 4567',
                'tag' => 'piante_interno',
                'latitude' => 45.4642,
                'longitude' => 9.1900,
                'country' => 'Italy',
                'country_code' => 'IT',
                'region' => 'Lombardia',
                'city' => 'Milano',
                'postal_code' => '20100',
                'timezone' => 'Europe/Rome',
            ],
            [
                'email' => 'giulia.verdi@email.com',
                'name' => 'Giulia Verdi',
                'whatsapp' => '+39 347 987 6543',
                'tag' => 'giardinaggio',
                'latitude' => 41.9028,
                'longitude' => 12.4964,
                'country' => 'Italy',
                'country_code' => 'IT',
                'region' => 'Lazio',
                'city' => 'Roma',
                'postal_code' => '00100',
                'timezone' => 'Europe/Rome',
            ],
            [
                'email' => 'luca.bianchi@email.com',
                'name' => 'Luca Bianchi',
                'latitude' => 40.8518,
                'longitude' => 14.2681,
                'country' => 'Italy',
                'country_code' => 'IT',
                'region' => 'Campania',
                'city' => 'Napoli',
                'postal_code' => '80100',
                'timezone' => 'Europe/Rome',
            ],
            [
                'email' => 'anna.ferrari@email.com',
                'name' => 'Anna Ferrari',
                'tag' => 'piante_grasse',
                'latitude' => 45.0703,
                'longitude' => 7.6869,
                'country' => 'Italy',
                'country_code' => 'IT',
                'region' => 'Piemonte',
                'city' => 'Torino',
                'postal_code' => '10100',
                'timezone' => 'Europe/Rome',
            ],
            [
                'email' => 'francesco.ricci@email.com',
                'name' => 'Francesco Ricci',
                'whatsapp' => '+39 339 111 2222',
                'tag' => 'orto_urbano',
                'latitude' => 44.4949,
                'longitude' => 11.3426,
                'country' => 'Italy',
                'country_code' => 'IT',
                'region' => 'Emilia-Romagna',
                'city' => 'Bologna',
                'postal_code' => '40100',
                'timezone' => 'Europe/Rome',
            ]
        ];

        foreach ($leads as $leadData) {
            Lead::create(array_merge($leadData, [
                'store_id' => $store->id,
                'source' => 'chatbot',
                'session_id' => 'test_session_' . rand(1000, 9999),
                'ip_address' => '127.0.0.1',
                'subscribed' => true,
                'last_interaction' => now(),
                'metadata' => [
                    'created_via' => 'seeder',
                    'test_data' => true,
                ]
            ]));
        }

        $this->command->info('Created ' . count($leads) . ' test leads for store: ' . $store->name);
    }
}
