<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class FixAdminAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:fix {--create-super : Create a super admin account}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix admin accounts and ensure role column exists';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Fixing admin accounts...');

        // Check if role column exists
        if (!Schema::hasColumn('admins', 'role')) {
            $this->error('❌ Role column does not exist in admins table!');
            $this->info('Run: php artisan migrate');
            return 1;
        }

        $this->info('✅ Role column exists');

        // Update existing admins without role
        $updated = Admin::whereNull('role')->orWhere('role', '')->update(['role' => 'admin']);
        if ($updated > 0) {
            $this->info("✅ Updated {$updated} admin records with default role");
        }

        // Create super admin if requested
        if ($this->option('create-super')) {
            $this->createSuperAdmin();
        }

        // Display current admins
        $this->displayAdmins();

        $this->info('🎉 Admin accounts fixed successfully!');
        return 0;
    }

    private function createSuperAdmin()
    {
        $email = $this->ask('Super admin email', 'admin@chatai.platform');

        if (Admin::where('email', $email)->exists()) {
            $this->warn("⚠️  Admin with email {$email} already exists");
            return;
        }

        $password = $this->secret('Super admin password');
        if (!$password) {
            $password = 'AdminChat2025!';
            $this->info("Using default password: {$password}");
        }

        $name = $this->ask('Super admin name', 'Super Admin');

        Admin::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'super_admin',
        ]);

        $this->info("✅ Super admin created successfully!");
        $this->table(['Field', 'Value'], [
            ['Email', $email],
            ['Password', $password],
            ['Role', 'super_admin']
        ]);
    }

    private function displayAdmins()
    {
        $admins = Admin::orderBy('created_at', 'desc')->get();

        if ($admins->isEmpty()) {
            $this->warn('⚠️  No admin accounts found');
            return;
        }

        $this->info('📋 Current admin accounts:');
        $this->table(
            ['ID', 'Name', 'Email', 'Role', 'Created'],
            $admins->map(function($admin) {
                return [
                    $admin->id,
                    $admin->name,
                    $admin->email,
                    $admin->role,
                    $admin->created_at->format('Y-m-d H:i:s')
                ];
            })->toArray()
        );
    }
}
