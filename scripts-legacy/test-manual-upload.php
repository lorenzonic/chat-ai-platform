<?php

echo "🧪 Testing File Upload via HTTP POST\n";
echo "===================================\n\n";

$testFile = __DIR__ . '/test-complete-import.csv';
if (!file_exists($testFile)) {
    echo "❌ Test file not found: $testFile\n";
    exit(1);
}

echo "📁 Test file: $testFile\n";
echo "📏 File size: " . filesize($testFile) . " bytes\n\n";

// Simulate the upload process by creating a temporary file in the uploads directory
$tempDir = __DIR__ . '/storage/app/temp/imports';
$fileName = 'manual_test_' . time() . '.csv';
$destination = $tempDir . '/' . $fileName;

echo "🎯 Destination: $destination\n";

if (!is_dir($tempDir)) {
    echo "📂 Creating temp directory...\n";
    mkdir($tempDir, 0755, true);
}

if (copy($testFile, $destination)) {
    echo "✅ File copied successfully\n";

    // List files in temp directory
    echo "\n📋 Files in temp/imports:\n";
    $files = scandir($tempDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $filePath = $tempDir . '/' . $file;
            echo "- $file (" . filesize($filePath) . " bytes, " . date('Y-m-d H:i:s', filemtime($filePath)) . ")\n";
        }
    }

    echo "\n✅ Manual upload test completed successfully!\n";
    echo "🔍 You can check http://localhost:8000/admin/debug/temp-files to see this file in the debug interface.\n";
} else {
    echo "❌ Failed to copy file\n";
}

echo "\n📝 Next step: Test via web interface with the fixed upload method.\n";
