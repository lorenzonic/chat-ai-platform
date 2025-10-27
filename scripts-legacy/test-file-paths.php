<?php

echo "🔍 Testing File Path Resolution\n";
echo "===============================\n\n";

// Test different path formats
$filePath = 'temp/imports/test-complete-import.csv';

echo "Testing path formats:\n";
echo "1. Original format: storage_path('app/' . \$filePath)\n";
$fullPath1 = storage_path('app/' . $filePath);
echo "   Result: $fullPath1\n";
echo "   Exists: " . (file_exists($fullPath1) ? "✅ YES" : "❌ NO") . "\n\n";

echo "2. DIRECTORY_SEPARATOR format: storage_path('app' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, \$filePath))\n";
$fullPath2 = storage_path('app' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $filePath));
echo "   Result: $fullPath2\n";
echo "   Exists: " . (file_exists($fullPath2) ? "✅ YES" : "❌ NO") . "\n\n";

echo "3. Manual Windows path construction:\n";
$basePath = realpath(__DIR__ . '/storage/app');
$windowsPath = $basePath . '\\temp\\imports\\test-complete-import.csv';
echo "   Result: $windowsPath\n";
echo "   Exists: " . (file_exists($windowsPath) ? "✅ YES" : "❌ NO") . "\n\n";

echo "Directory listing of storage/app/temp/imports:\n";
$importDir = __DIR__ . '/storage/app/temp/imports';
if (is_dir($importDir)) {
    $files = scandir($importDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "- $file\n";
        }
    }
} else {
    echo "Directory does not exist!\n";
}

echo "\nTesting file reading:\n";
if (file_exists($fullPath2)) {
    echo "Attempting to read file...\n";
    $handle = fopen($fullPath2, 'r');
    if ($handle) {
        echo "✅ File opened successfully\n";
        $firstLine = fgets($handle);
        echo "First line: " . trim($firstLine) . "\n";
        fclose($handle);
    } else {
        echo "❌ Failed to open file\n";
    }
} else {
    echo "❌ File does not exist at: $fullPath2\n";
}

echo "\n✅ Path test completed!\n";
