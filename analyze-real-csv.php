<?php

// Analisi del file CSV reale
echo "🔍 Analisi File CSV Reale\n\n";

$csvFile = 'c:\\Users\\Lorenzo\\Downloads\\ordine-test - Foglio1 (1).csv';

if (!file_exists($csvFile)) {
    echo "❌ File non trovato: $csvFile\n";
    exit(1);
}

// Leggi il CSV
$data = [];
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    while (($row = fgetcsv($handle, 2000, ",")) !== FALSE) {
        $data[] = $row;
    }
    fclose($handle);
}

echo "📊 Struttura File:\n";
echo "- Righe totali: " . count($data) . "\n";

// Analizza headers
$headers = $data[0];
echo "\n📋 Colonne trovate (" . count($headers) . "):\n";
foreach ($headers as $index => $header) {
    echo "  $index: $header\n";
}

// Analizza prime righe di dati
echo "\n📄 Prime 3 righe di dati:\n";
for ($i = 1; $i <= min(3, count($data) - 1); $i++) {
    echo "\nRiga $i:\n";
    foreach ($headers as $index => $header) {
        $value = isset($data[$i][$index]) ? $data[$i][$index] : '';
        echo "  $header: $value\n";
    }
}

// Analizza mapping con nostro database
echo "\n🔗 Mapping con Database:\n";

$mapping = [
    'Fornitore' => 'Grower (nome)',
    'Prodotto' => 'Product (nome)',
    'Codice' => 'Product (code)',
    'Quantità' => 'OrderItem (quantity)',
    'Cliente' => 'Store (name)',
    'CC' => 'Order (cc)',
    'PIA' => 'Order (pia)',
    'PRO' => 'Order (pro)',
    'Trasporto' => 'Order (transport)',
    'Data' => 'Order (delivery_date)',
    'Note' => 'Order (notes)',
    'EAN' => 'Product (ean)',
    '€ Vendita' => 'OrderItem (unit_price)',
    'Telefono' => 'Order (phone)',
    'H' => 'Product (height)',
    'CODE' => 'Product (ref_code)',
    'Piani' => 'OrderItem (?)',
    'Piante per cc' => 'Product (?)',
    'Indirizzo' => 'Store (address)'
];

foreach ($mapping as $csvCol => $dbField) {
    echo "  $csvCol → $dbField\n";
}

// Analizza valori unici per capire i pattern
echo "\n📈 Analisi Dati:\n";

// Fornitori unici
$fornitori = [];
$clienti = [];
$trasporti = [];

foreach (array_slice($data, 1) as $row) {
    if (isset($row[0]) && !empty($row[0])) $fornitori[$row[0]] = true;
    if (isset($row[8]) && !empty($row[8])) $clienti[$row[8]] = true;
    if (isset($row[12]) && !empty($row[12])) $trasporti[$row[12]] = true;
}

echo "- Fornitori unici: " . count($fornitori) . "\n";
echo "  " . implode(', ', array_slice(array_keys($fornitori), 0, 5)) . (count($fornitori) > 5 ? '...' : '') . "\n";

echo "- Clienti unici: " . count($clienti) . "\n";
echo "  " . implode(', ', array_slice(array_keys($clienti), 0, 3)) . (count($clienti) > 3 ? '...' : '') . "\n";

echo "- Trasporti unici: " . count($trasporti) . "\n";
echo "  " . implode(', ', array_keys($trasporti)) . "\n";

echo "\n🎯 CONCLUSIONI:\n";
echo "✅ Il file contiene dati completi per:\n";
echo "   - Growers (Fornitori)\n";
echo "   - Products (Prodotti con codici)\n";
echo "   - Orders (Ordini con CC/PIA/PRO)\n";
echo "   - OrderItems (Righe ordine con quantità e prezzi)\n";
echo "   - Stores (Clienti)\n";

echo "\n⚠️ Il nostro sistema attuale NON gestisce:\n";
echo "   - Creazione automatica Grower\n";
echo "   - Creazione automatica Product\n";
echo "   - Creazione OrderItem\n";
echo "   - Raggruppamento righe per ordine\n";

echo "\n🔧 MODIFICHE NECESSARIE:\n";
echo "1. Estendere ImportController per gestire Grower/Product/OrderItem\n";
echo "2. Raggruppare righe CSV per Cliente/CC/PIA/PRO (stesso ordine)\n";
echo "3. Creare Order una volta per gruppo\n";
echo "4. Creare OrderItem per ogni riga del gruppo\n";
echo "5. Aggiornare mapping colonne per tutti i campi\n";
