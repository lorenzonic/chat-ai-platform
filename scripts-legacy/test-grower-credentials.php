<?php

// Test script per creare/mostrare credenziali grower
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Trova grower esistente e aggiorna solo email e password
$grower = \App\Models\Grower::first();

if ($grower) {
    $grower->update([
        'email' => 'test@grower.com',
        'password' => bcrypt('password123'),
    ]);

    echo "=== GROWER TEST CREDENTIALS ===\n";
    echo "Email: " . $grower->email . "\n";
    echo "Password: password123\n";
    echo "ID: " . $grower->id . "\n";
    echo "===============================\n";
    echo "Login URL: http://localhost:8000/grower/login\n";
} else {
    echo "No growers found in database\n";
}
