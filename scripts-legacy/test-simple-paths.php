<?php

echo "🔍 Testing File Path Resolution (Simple)\n";
echo "======================================\n\n";

// Test path construction
$basePath = __DIR__ . '/storage/app';
$filePath = 'temp/imports/test-complete-import.csv';

echo "Base path: $basePath\n";
echo "File path: $filePath\n\n";

echo "Testing path formats:\n";

echo "1. Simple concatenation: \$basePath . '/' . \$filePath\n";
$fullPath1 = $basePath . '/' . $filePath;
echo "   Result: $fullPath1\n";
echo "   Exists: " . (file_exists($fullPath1) ? "✅ YES" : "❌ NO") . "\n\n";

echo "2. Windows-style: \$basePath . '\\' . str_replace('/', '\\', \$filePath)\n";
$fullPath2 = $basePath . '\\' . str_replace('/', '\\', $filePath);
echo "   Result: $fullPath2\n";
echo "   Exists: " . (file_exists($fullPath2) ? "✅ YES" : "❌ NO") . "\n\n";

echo "3. DIRECTORY_SEPARATOR: \$basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, \$filePath)\n";
$fullPath3 = $basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $filePath);
echo "   Result: $fullPath3\n";
echo "   Exists: " . (file_exists($fullPath3) ? "✅ YES" : "❌ NO") . "\n\n";

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

echo "\n✅ Path test completed!\n";
