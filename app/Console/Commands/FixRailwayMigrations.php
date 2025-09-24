<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class FixRailwayMigrations extends Command
{
    protected $signature = 'railway:migrate-fix';
    protected $description = 'Fix and run missing migrations for Railway deployment';

    public function handle()
    {
        $this->info('🔧 Fixing Railway migrations...');
        
        try {
            // Clear migration cache
            $this->info('Clearing migration cache...');
            Artisan::call('migrate:reset', ['--force' => true]);
            $this->info('Migration cache cleared.');
            
            // Run fresh migrations
            $this->info('Running fresh migrations...');
            Artisan::call('migrate', ['--force' => true]);
            $this->info('✅ Migrations completed successfully!');
            
            // Check important tables
            $tables = ['stores', 'qr_codes', 'products', 'orders', 'order_items', 'growers'];
            $this->info('Verifying tables...');
            
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    $this->info("✅ Table '{$table}' exists");
                } else {
                    $this->error("❌ Table '{$table}' missing!");
                }
            }
            
        } catch (\Exception $e) {
            $this->error('Migration failed: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}