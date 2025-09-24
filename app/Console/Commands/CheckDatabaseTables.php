<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CheckDatabaseTables extends Command
{
    protected $signature = 'db:check-tables';
    protected $description = 'Check if all required database tables exist';

    public function handle()
    {
        $this->info('🔍 Checking database tables...');

        $requiredTables = [
            'users',
            'admins',
            'stores',
            'growers',
            'qr_codes',
            'chat_logs',
            'leads',
            'newsletters',
            'newsletter_sends',
            'orders',
            'order_items',
            'products',
            'trending_keywords',
            'migrations',
            'cache',
            'sessions',
            'jobs',
            'failed_jobs',
            'password_resets'
        ];

        $missingTables = [];
        $existingTables = [];

        foreach ($requiredTables as $table) {
            if (Schema::hasTable($table)) {
                $existingTables[] = $table;
                $this->info("✅ {$table}");
            } else {
                $missingTables[] = $table;
                $this->error("❌ {$table} - MISSING");
            }
        }

        $this->newLine();
        $this->info("📊 Summary:");
        $this->info("✅ Existing tables: " . count($existingTables));
        $this->error("❌ Missing tables: " . count($missingTables));

        if (!empty($missingTables)) {
            $this->newLine();
            $this->error("Missing tables: " . implode(', ', $missingTables));
            $this->info("Run 'php artisan migrate --force' to create missing tables");
            return 1;
        }

        $this->info("🎉 All required tables are present!");
        return 0;
    }
}
