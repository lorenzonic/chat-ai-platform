<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Store::create([
            'name' => 'Botanica Verde',
            'slug' => 'botanicaverde',
            'email' => 'info@botanicaverde.com',
            'password' => bcrypt('password'),
            'description' => 'Negozio specializzato in piante da interno ed esterno, con consulenza personalizzata per la cura del verde.',
            'is_active' => true,
            'is_premium' => true,
            'email_verified_at' => now(),
        ]);

        \App\Models\Store::create([
            'name' => 'Garden Paradise',
            'slug' => 'gardenparadise',
            'email' => 'contact@gardenparadise.com',
            'password' => bcrypt('password'),
            'description' => 'Il tuo paradiso del giardinaggio con le migliori piante e accessori.',
            'is_active' => true,
            'is_premium' => false,
            'email_verified_at' => now(),
        ]);
    }
}
