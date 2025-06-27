<?php

// Versione semplificata per creare account
header('Content-Type: text/plain');

try {
    // Esegui comando artisan tramite shell
    $output = shell_exec('cd ' . dirname(__DIR__) . ' && php artisan tinker --execute="
        use App\Models\User;
        use App\Models\Store;
        use Illuminate\Support\Facades\Hash;

        // Crea admin
        \$admin = User::firstOrCreate(
            [\'email\' => \'admin@chataiplatform.com\'],
            [
                \'name\' => \'Admin\',
                \'password\' => Hash::make(\'admin123\'),
                \'user_type\' => \'admin\',
                \'email_verified_at\' => now(),
            ]
        );

        echo \'Admin: admin@chataiplatform.com / admin123\n\';

        // Crea store user
        \$storeUser = User::firstOrCreate(
            [\'email\' => \'store@test.com\'],
            [
                \'name\' => \'Test Store\',
                \'password\' => Hash::make(\'store123\'),
                \'user_type\' => \'store\',
                \'email_verified_at\' => now(),
            ]
        );

        // Crea store
        \$store = Store::firstOrCreate(
            [\'user_id\' => \$storeUser->id],
            [
                \'name\' => \'Test Store\',
                \'description\' => \'Negozio di test per la demo\',
                \'phone\' => \'+39 333 123 4567\',
                \'email\' => \'store@test.com\',
                \'address\' => \'Via Test 123, Milano\',
                \'website\' => \'https://teststore.com\',
                \'category\' => \'retail\',
                \'is_active\' => true,
            ]
        );

        echo \'Store: store@test.com / store123\n\';
        echo \'Accounts created successfully!\';
    "');

    echo "=== ACCOUNT CREATION RESULT ===\n";
    echo $output;
    echo "\n\n=== LOGIN URLs ===\n";
    echo "Admin: https://web-production-9c70.up.railway.app/admin/login\n";
    echo "Store: https://web-production-9c70.up.railway.app/store/login\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
