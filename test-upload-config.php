<?php
/**
 * Upload Configuration Test Script
 * 
 * This script tests the PHP upload configuration to ensure large file uploads work properly.
 * Run this inside your Docker container to verify settings.
 */

echo "=== PHP Upload Configuration Test ===\n\n";

// Test PHP configuration
$config = [
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_execution_time' => ini_get('max_execution_time'),
    'max_input_time' => ini_get('max_input_time'),
    'memory_limit' => ini_get('memory_limit'),
    'max_file_uploads' => ini_get('max_file_uploads'),
    'file_uploads' => ini_get('file_uploads'),
    'upload_tmp_dir' => ini_get('upload_tmp_dir')
];

echo "PHP Configuration:\n";
foreach ($config as $key => $value) {
    echo sprintf("%-20s: %s\n", $key, $value);
}

echo "\n=== Directory Tests ===\n";

// Test temp directory
$tempDir = sys_get_temp_dir();
echo "Temp directory: $tempDir\n";
echo "Temp directory exists: " . (is_dir($tempDir) ? 'YES' : 'NO') . "\n";
echo "Temp directory writable: " . (is_writable($tempDir) ? 'YES' : 'NO') . "\n";
echo "Temp directory permissions: " . substr(sprintf('%o', fileperms($tempDir)), -4) . "\n";

// Test uploads directory
$uploadsDir = '/var/www/html/uploads';
echo "\nUploads directory: $uploadsDir\n";
echo "Uploads directory exists: " . (is_dir($uploadsDir) ? 'YES' : 'NO') . "\n";
echo "Uploads directory writable: " . (is_writable($uploadsDir) ? 'YES' : 'NO') . "\n";
echo "Uploads directory permissions: " . substr(sprintf('%o', fileperms($uploadsDir)), -4) . "\n";

// Test file size limits
echo "\n=== File Size Tests ===\n";
$maxUploadSize = parseSize($config['upload_max_filesize']);
$maxPostSize = parseSize($config['post_max_size']);
$memoryLimit = parseSize($config['memory_limit']);

echo "Max upload size (bytes): " . number_format($maxUploadSize) . "\n";
echo "Max post size (bytes): " . number_format($maxPostSize) . "\n";
echo "Memory limit (bytes): " . number_format($memoryLimit) . "\n";

// Test if 30MB file would be allowed
$testFileSize = 30 * 1024 * 1024; // 30MB
echo "\n30MB file test:\n";
echo "30MB file size (bytes): " . number_format($testFileSize) . "\n";
echo "Would 30MB file be allowed for upload: " . ($testFileSize <= $maxUploadSize ? 'YES' : 'NO') . "\n";
echo "Would 30MB file be allowed for POST: " . ($testFileSize <= $maxPostSize ? 'YES' : 'NO') . "\n";

echo "\n=== Recommendations ===\n";
if ($testFileSize > $maxUploadSize) {
    echo "❌ upload_max_filesize is too small for 30MB files\n";
} else {
    echo "✅ upload_max_filesize is sufficient for 30MB files\n";
}

if ($testFileSize > $maxPostSize) {
    echo "❌ post_max_size is too small for 30MB files\n";
} else {
    echo "✅ post_max_size is sufficient for 30MB files\n";
}

if ($memoryLimit < $testFileSize * 2) {
    echo "❌ memory_limit might be too low for processing 30MB files\n";
} else {
    echo "✅ memory_limit is sufficient for processing 30MB files\n";
}

echo "\n=== Test Complete ===\n";

/**
 * Parse size string (e.g., "1000M") to bytes
 */
function parseSize($size) {
    $size = trim($size);
    $last = strtolower($size[strlen($size)-1]);
    $size = (int)$size;
    
    switch($last) {
        case 'g':
            $size *= 1024;
        case 'm':
            $size *= 1024;
        case 'k':
            $size *= 1024;
    }
    
    return $size;
}
?> 