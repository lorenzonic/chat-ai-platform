<?php

// Test per verificare i MIME types supportati
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== MIME TYPES TEST ===\n\n";

// Test file examples
$testFiles = [
    'test.csv' => 'text/csv',
    'test.xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'test.xls' => 'application/vnd.ms-excel',
];

echo "File di test supportati:\n";
foreach ($testFiles as $filename => $mimeType) {
    echo "- {$filename}: {$mimeType}\n";
}

echo "\nTipi MIME supportati dalla validazione:\n";
$allowedMimes = [
    'text/csv',
    'text/plain',
    'application/csv',
    'application/excel',
    'application/vnd.ms-excel',
    'application/vnd.msexcel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
];

foreach ($allowedMimes as $mime) {
    echo "- {$mime}\n";
}

echo "\nEstensioni supportate: csv, xlsx, xls\n";

echo "\nPer risolvere l'errore:\n";
echo "1. Assicurati che il file abbia l'estensione corretta (.csv, .xlsx, .xls)\n";
echo "2. Verifica che il file sia effettivamente un CSV o Excel valido\n";
echo "3. Prova a salvare il file con 'Salva con nome' e seleziona esplicitamente il formato CSV\n";
echo "4. Se usi Excel, salva come 'CSV (delimitato da virgole)' o 'Excel Workbook (.xlsx)'\n";

echo "\n✅ Test completed!\n";
