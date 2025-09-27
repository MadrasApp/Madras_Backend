<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use phpseclib3\Net\SFTP;

// Resolve requested URI and map to SFTP path safely
$requested_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

// Log raw URI for debugging
error_log('Raw URI: ' . $requested_url);

// Sanitize and decode URL, keep slashes
$requested_url = filter_var($requested_url, FILTER_SANITIZE_URL);
$requested_url = urldecode($requested_url);

// Find '/uploads/' in the URI and build a relative path from there
$pos = strpos($requested_url, '/uploads/');
if ($pos === false) {
    header('HTTP/1.0 400 Bad Request');
    echo 'Invalid uploads path';
    exit;
}

$relative_path = substr($requested_url, $pos + 1); // remove leading '/'

// Prevent directory traversal
if (strpos($relative_path, '..') !== false) {
    header('HTTP/1.0 400 Bad Request');
    echo 'Invalid path';
    exit;
}

// Load SFTP configuration
$sftp_config = include(APPPATH . 'config/sftp.php');
$sftpConfig = isset($sftp_config['sftp']) ? $sftp_config['sftp'] : null;

if (!$sftpConfig) {
    error_log('SFTP configuration not found');
    header('HTTP/1.0 500 Internal Server Error');
    echo 'SFTP configuration error';
    exit;
}

// Ensure autoloader is loaded
if (!class_exists('phpseclib3\\Net\\SFTP')) {
    require_once(FCPATH . 'vendor/autoload.php');
}

// Connect to SFTP server
$sftp = new \phpseclib3\Net\SFTP($sftpConfig['host'], $sftpConfig['port']);
if (!$sftp->login($sftpConfig['username'], $sftpConfig['password'])) {
    error_log('SFTP login failed');
    header('HTTP/1.0 500 Internal Server Error');
    echo 'SFTP connection failed';
    exit;
}

// Try to get file from SFTP with both path formats
$file_content = false;
$sftp_path = $relative_path;

// First try the original path
if ($sftp->file_exists($sftp_path)) {
    $file_content = $sftp->get($sftp_path);
} else {
    // Try alternative path format: /uploads/admin/2025/09/k.jpg/k.jpg
    $path_parts = explode('/', $sftp_path);
    if (count($path_parts) >= 5) {
        // Extract: uploads/admin/2025/09/k.jpg
        $base_path = implode('/', array_slice($path_parts, 0, 5));
        $file_name = end($path_parts);

        // Create alternative path: uploads/admin/2025/09/k.jpg/k.jpg
        $alternative_path = $base_path . '/' . $file_name;

        if ($sftp->file_exists($alternative_path)) {
            $file_content = $sftp->get($alternative_path);
        }
    }
}

// Check if file was found and serve
if ($file_content !== false) {
    $file_name = basename($sftp_path);
    $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Set MIME type based on file extension
    $mime_types = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'mp3' => 'audio/mpeg',
        'wav' => 'audio/wav',
        'ogg' => 'audio/ogg',
        'aac' => 'audio/aac',
        'flac' => 'audio/flac',
        'm4a' => 'audio/mp4',
        'mp4' => 'video/mp4',
        'webm' => 'video/webm',
        'pdf' => 'application/pdf'
    ];

    $mime_type = isset($mime_types[$extension]) ? $mime_types[$extension] : 'application/octet-stream';

    header('Content-Type: ' . $mime_type);
    header('Content-Length: ' . strlen($file_content));
    header('Content-Disposition: inline; filename="' . $file_name . '"');
    header('Cache-Control: public, max-age=3600'); // Cache for 1 hour
    echo $file_content;
    exit;
}

// Not found: log and 404
error_log('File not found on SFTP: ' . $sftp_path);
header('HTTP/1.0 404 Not Found');
echo 'File not found: ' . $sftp_path;
exit;

?>
