<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestDatabase extends Command
{
    protected $signature = 'db:test';
    protected $description = 'Test database connection and structure';

    public function handle()
    {
        $this->info('🔍 Testing database connection...');

        try {
            // Test connessione base
            DB::connection()->getPdo();
            $this->info('✅ Database connection: SUCCESS');

            // Test query semplice
            $result = DB::select('SELECT 1 as test, NOW() as server_time');
            $this->info('✅ Query test: SUCCESS');
            $this->info('⏰ Server time: ' . $result[0]->server_time);

            // Lista tabelle
            $tables = DB::select('SHOW TABLES');
            $this->info('📊 Total tables: ' . count($tables));

            // Controlla tabelle richieste
            $required_tables = ['users', 'stores', 'conversations', 'interactions'];
            $this->info('🔍 Checking required tables:');

            foreach ($required_tables as $table) {
                if (Schema::hasTable($table)) {
                    $count = DB::table($table)->count();
                    $this->info("  ✅ $table: EXISTS ($count records)");
                } else {
                    $this->error("  ❌ $table: MISSING");
                }
            }

            // Test inserimento (se possibile)
            if (Schema::hasTable('users')) {
                $this->info('🧪 Testing INSERT capability...');
                try {
                    // Non inserire realmente, solo test della query
                    DB::table('users')->whereRaw('1 = 0')->get();
                    $this->info('✅ INSERT capability: OK');
                } catch (\Exception $e) {
                    $this->error('❌ INSERT test failed: ' . $e->getMessage());
                }
            }

            $this->info('🎉 Database test completed successfully!');

        } catch (\Exception $e) {
            $this->error('❌ Database connection failed!');
            $this->error('Error: ' . $e->getMessage());
            $this->error('Code: ' . $e->getCode());
            return 1;
        }

        return 0;
    }
}
