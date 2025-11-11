<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Admin;

echo "🔐 Reset Admin Password\n";
echo "=====================\n\n";

$admin = Admin::where('email', 'admin@chatai.com')->first();

if (!$admin) {
    echo "❌ Admin non trovato!\n";
    exit(1);
}

echo "✅ Admin trovato: {$admin->name} ({$admin->email})\n";
echo "📝 Aggiorno password a: 'password'\n\n";

$admin->password = bcrypt('password');
$admin->save();

echo "✅ Password aggiornata con successo!\n";
echo "📧 Email: admin@chatai.com\n";
echo "🔑 Password: password\n";
