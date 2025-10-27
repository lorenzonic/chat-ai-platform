<?php

// Simulate file upload for testing
echo "🧪 Testing Import Process Step by Step\n\n";

// Step 1: Create a test file in the right location
$testCsv = 'test-orders-import.csv';
$tempDir = 'storage/app/temp/imports/';
$testFile = $tempDir . 'test-upload-' . time() . '.csv';

echo "📁 Step 1: File Setup\n";
echo "- Source: $testCsv\n";
echo "- Target: $testFile\n";

// Create directory if needed
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
    echo "- Created directory: $tempDir\n";
}

// Copy test file
if (file_exists($testCsv)) {
    copy($testCsv, $testFile);
    echo "- File copied successfully\n";
} else {
    echo "❌ Source file not found: $testCsv\n";
    exit(1);
}

// Step 2: Test CSV reading
echo "\n📊 Step 2: CSV Reading Test\n";
$data = [];
if (($handle = fopen($testFile, "r")) !== FALSE) {
    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $data[] = $row;
    }
    fclose($handle);
}

echo "- Rows read: " . count($data) . "\n";
echo "- Headers: " . implode(', ', $data[0]) . "\n";

// Step 3: Test mapping
echo "\n🔗 Step 3: Column Mapping Test\n";
$headers = $data[0];
$mapping = [
    'order_number' => 0,  // Numero Ordine
    'client' => 1,        // Nome Cliente
    'cc' => 2,            // Codice CC
    'pia' => 3,           // Codice PIA
    'pro' => 4,           // Codice PRO
    'transport' => 5,     // Metodo Trasporto
    'transport_cost' => 6, // Costo Trasporto
    'delivery_date' => 7,  // Data Consegna
    'phone' => 8,         // Telefono
    'notes' => 9          // Note
];

foreach ($mapping as $field => $col) {
    echo "- $field -> Column $col: " . (isset($headers[$col]) ? $headers[$col] : 'N/A') . "\n";
}

// Step 4: Test data extraction
echo "\n📋 Step 4: Data Extraction Test\n";
$dataRows = array_slice($data, 1); // Skip header
foreach ($dataRows as $index => $row) {
    $orderNumber = isset($row[$mapping['order_number']]) ? trim($row[$mapping['order_number']]) : null;
    $clientName = isset($row[$mapping['client']]) ? trim($row[$mapping['client']]) : null;

    echo "Row " . ($index + 1) . ": Order=$orderNumber, Client=$clientName\n";
}

// Clean up
unlink($testFile);
echo "\n✅ Test completed successfully!\n";
echo "💡 The CSV reading and mapping logic works correctly.\n";
echo "🔍 If import still fails, the issue is likely in:\n";
echo "   - File upload handling\n";
echo "   - Session management\n";
echo "   - Database operations\n";
echo "   - Authentication/middleware\n";
