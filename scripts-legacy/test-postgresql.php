<?php
/**
 * Test script per verificare compatibilità PostgreSQL
 * Questo script testa le funzionalità principali del database
 */

use Illuminate\Database\Capsule\Manager as Capsule;

try {
    echo "🔍 Test compatibilità PostgreSQL...\n";

    // Test 1: Connessione
    echo "1. Test connessione database...\n";
    $connection = config('database.connections.pgsql');

    if (!$connection) {
        throw new Exception("Configurazione PostgreSQL non trovata");
    }

    // Test 2: Query semplice
    echo "2. Test query base...\n";
    $result = DB::select('SELECT version()');
    echo "   PostgreSQL version: " . ($result[0]->version ?? 'N/A') . "\n";

    // Test 3: Tabelle principali
    echo "3. Test tabelle principali...\n";
    $tables = ['stores', 'qr_codes', 'products', 'orders', 'order_items', 'growers'];

    foreach ($tables as $table) {
        $count = DB::table($table)->count();
        echo "   - {$table}: {$count} record\n";
    }

    // Test 4: Foreign Keys
    echo "4. Test foreign keys...\n";
    $foreignKeys = DB::select("
        SELECT
            tc.table_name,
            kcu.column_name,
            ccu.table_name AS foreign_table_name,
            ccu.column_name AS foreign_column_name
        FROM
            information_schema.table_constraints AS tc
            JOIN information_schema.key_column_usage AS kcu
              ON tc.constraint_name = kcu.constraint_name
            JOIN information_schema.constraint_column_usage AS ccu
              ON ccu.constraint_name = tc.constraint_name
        WHERE constraint_type = 'FOREIGN KEY'
        AND tc.table_schema = 'public'
        LIMIT 5
    ");

    echo "   Foreign keys trovate: " . count($foreignKeys) . "\n";

    // Test 5: JSON fields (se utilizzati)
    echo "5. Test campi JSON...\n";
    $jsonTest = DB::table('stores')
        ->whereNotNull('chat_suggestions')
        ->first();

    if ($jsonTest && isset($jsonTest->chat_suggestions)) {
        echo "   JSON field test: OK\n";
    } else {
        echo "   JSON field test: Non trovati campi JSON\n";
    }

    echo "\n✅ Tutti i test PostgreSQL sono passati!\n";

} catch (Exception $e) {
    echo "\n❌ Errore nel test PostgreSQL: " . $e->getMessage() . "\n";
    exit(1);
}
