<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Grower;

class GrowerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crea grower di test
        Grower::create([
            'name' => 'Test Grower',
            'email' => 'grower@test.com',
            'password' => Hash::make('password123'),
            'phone' => '+39 123 456 7890',
            'address' => 'Via Test 123, 00100 Roma',
            'tax_code' => 'TSTGRW80A01H501X',
            'vat_number' => '12345678901',
            'is_active' => true,
        ]);

        $this->command->info('✅ Created test grower: grower@test.com / password123');
    }
}
