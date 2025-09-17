<?php


// Resolve requested URI and map to local uploads path safely
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

// Build absolute filesystem path using FCPATH
$fc = rtrim(str_replace('\\','/', FCPATH), '/').'/';
$file_path = $fc . $relative_path; // forward slashes are fine on Windows

// On Windows, filenames may be stored in legacy encodings. If not found with UTF-8,
// try CP1256 (common for Persian on Windows) as a fallback.
if (!file_exists($file_path) && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $cp1256_path = @iconv('UTF-8', 'CP1256//TRANSLIT', $file_path);
    if ($cp1256_path && file_exists($cp1256_path)) {
        $file_path = $cp1256_path;
    }
}

// Check file existence and serve
if (file_exists($file_path)) {
    $mime_type = function_exists('mime_content_type') ? mime_content_type($file_path) : 'application/octet-stream';
    header('Content-Type: ' . $mime_type);
    header('Content-Length: ' . filesize($file_path));
    header('Content-Disposition: inline; filename="' . basename($file_path) . '"');
    readfile($file_path);
    exit;
}

// Not found: log and 404
error_log('File not found: ' . $file_path);
header('HTTP/1.0 404 Not Found');
echo 'File not found: ' . $file_path;
exit;


?>
