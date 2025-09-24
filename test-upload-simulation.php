<?php

// Test per simulare il processo di upload e import direttamente
$testCsvPath = __DIR__ . '/test-complete-import.csv';
$tempImportsDir = __DIR__ . '/storage/app/temp/imports';

echo "🧪 Testing Complete Import Process\n";
echo "================================\n\n";

if (!file_exists($testCsvPath)) {
    echo "❌ Test CSV file not found: $testCsvPath\n";
    exit(1);
}

echo "✅ Source CSV file exists: $testCsvPath\n";

// Simulate the storage process
$fileName = time() . '_' . uniqid() . '.csv';
$destinationPath = $tempImportsDir . '/' . $fileName;

echo "📁 Destination path: $destinationPath\n";

// Ensure directory exists
if (!is_dir($tempImportsDir)) {
    echo "📂 Creating imports directory...\n";
    mkdir($tempImportsDir, 0755, true);
}

// Copy file to simulate upload
if (copy($testCsvPath, $destinationPath)) {
    echo "✅ File copied successfully to: $destinationPath\n";

    // Verify file exists and is readable
    if (file_exists($destinationPath)) {
        echo "✅ File verification: EXISTS\n";

        $handle = fopen($destinationPath, 'r');
        if ($handle) {
            echo "✅ File can be opened for reading\n";
            $firstLine = fgets($handle);
            echo "📋 First line: " . trim($firstLine) . "\n";
            fclose($handle);

            // Test file path formats that Laravel would use
            $relativePath = 'temp/imports/' . $fileName;
            echo "\n🔍 Testing Laravel storage paths:\n";

            // Method 1: Simple concatenation (what Laravel storage_path does)
            $basePath = __DIR__ . '/storage/app';
            $fullPath1 = $basePath . '/' . $relativePath;
            echo "1. $fullPath1 -> " . (file_exists($fullPath1) ? "✅ EXISTS" : "❌ NOT FOUND") . "\n";

            // Method 2: DIRECTORY_SEPARATOR format
            $fullPath2 = $basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
            echo "2. $fullPath2 -> " . (file_exists($fullPath2) ? "✅ EXISTS" : "❌ NOT FOUND") . "\n";

            echo "\n🎯 The relative path that should work in Laravel: $relativePath\n";

        } else {
            echo "❌ Cannot open file for reading\n";
        }
    } else {
        echo "❌ File verification: NOT FOUND\n";
    }
} else {
    echo "❌ Failed to copy file\n";
}

echo "\n✅ Test completed!\n";
