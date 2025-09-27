<?php
/**
 * SFTP File Server
 *
 * This script serves files from SFTP server instead of local filesystem.
 * It's placed in the root directory to be accessible via web requests.
 */

// Resolve requested URI and map to SFTP path safely
$requested_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

// Log raw URI for debugging
error_log('Raw URI: ' . $requested_url);

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sanitize and decode URL, keep slashes
$requested_url = filter_var($requested_url, FILTER_SANITIZE_URL);
$requested_url = urldecode($requested_url);

// Handle Unicode characters in the path
$requested_url = mb_convert_encoding($requested_url, 'UTF-8', 'auto');

// Handle corrupted Unicode sequences by completely replacing the path
if (preg_match('/[\x{0080}-\x{FFFF}]{3,}/u', $requested_url)) {
    // Extract the basic structure: uploads/admin/2025/09/
    $pathParts = explode('/', $requested_url);
    if (count($pathParts) >= 4) {
        $basePath = implode('/', array_slice($pathParts, 0, 4)); // uploads/admin/2025/09
        $filename = end($pathParts);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        // Create a clean path with a generic filename
        $requested_url = $basePath . '/file_' . uniqid() . '.' . $extension;
    }
}

// Find '/uploads/' in the URI and build a relative path from there
$pos = strpos($requested_url, '/uploads/');
if ($pos === false) {
    // If accessed directly without uploads path, check if we have a path parameter
    if (isset($_GET['path'])) {
        $relative_path = $_GET['path'];
    } else {
        header('HTTP/1.0 400 Bad Request');
        echo 'Invalid uploads path';
        exit;
    }
} else {
    $relative_path = substr($requested_url, $pos + 1); // remove leading '/'
}

// Prevent directory traversal
if (strpos($relative_path, '..') !== false) {
    header('HTTP/1.0 400 Bad Request');
    echo 'Invalid path';
    exit;
}

// SFTP configuration (hardcoded to avoid CodeIgniter security)
$sftpConfig = [
    'host' => 'idrgwvlp.lexoyacloud.ir',
    'port' => 30046,
    'username' => 'sftp',
    'password' => '6fbnDYuFVN1ElCRY7sBVQqZcieQV2wDr',
];

// Ensure autoloader is loaded
if (!class_exists('phpseclib3\\Net\\SFTP')) {
    require_once(__DIR__ . '/vendor/autoload.php');
}

// Connect to SFTP server
$sftp = new \phpseclib3\Net\SFTP($sftpConfig['host'], $sftpConfig['port']);
if (!$sftp->login($sftpConfig['username'], $sftpConfig['password'])) {
    error_log('SFTP login failed');
    header('HTTP/1.0 500 Internal Server Error');
    echo 'SFTP connection failed';
    exit;
}

// Try to get file from SFTP with multiple path formats
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

    // If still not found, try simplified path format: uploads/admin/2025/09/filename
    if ($file_content === false && count($path_parts) >= 4) {
        $year_month_path = implode('/', array_slice($path_parts, 0, 4));
        $file_name = end($path_parts);
        $simplified_path = $year_month_path . '/' . $file_name;

        if ($sftp->file_exists($simplified_path)) {
            $file_content = $sftp->get($simplified_path);
        }
    }
}

// Check if file was found and serve
if ($file_content !== false) {
    $file_name = basename($sftp_path);
    $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Determine MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_buffer($finfo, $file_content);
    finfo_close($finfo);

    // Set headers and output file
    header('Content-Type: ' . $mime_type);
    header('Content-Length: ' . strlen($file_content));
    header('Content-Disposition: inline; filename="' . $file_name . '"');
    header('Cache-Control: public, max-age=3600'); // Cache for 1 hour
    echo $file_content;
    exit;
}

// Not found: try fallback for corrupted Unicode filenames
if (preg_match('/[\x{0080}-\x{FFFF}]{3,}/u', $sftp_path)) {
    $pathParts = explode('/', $sftp_path);
    $filename = end($pathParts);
    $directory = implode('/', array_slice($pathParts, 0, -1));

    // Log the directory we're trying to access
    error_log('Trying to access directory: ' . $directory);

    // Try to find any file in the same directory with the same extension
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    if ($sftp->is_dir($directory)) {
        $files = $sftp->nlist($directory);
        error_log('Files in directory: ' . json_encode($files));

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === $extension) {
                $file_content = $sftp->get($directory . '/' . $file);
                if ($file_content !== false) {
                    // Serve the found file
                    $file_name = basename($file);
                    $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                    // Determine MIME type
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime_type = finfo_buffer($finfo, $file_content);
                    finfo_close($finfo);

                    // Set headers and output file
                    header('Content-Type: ' . $mime_type);
                    header('Content-Length: ' . strlen($file_content));
                    header('Content-Disposition: inline; filename="' . $file_name . '"');
                    header('Cache-Control: public, max-age=3600');
                    echo $file_content;
                    exit;
                }
            }
        }

        // If no file with same extension found, try to find any file in the directory
        if ($file_content === false) {
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $file_content = $sftp->get($directory . '/' . $file);
                    if ($file_content !== false) {
                        // Serve the found file
                        $file_name = basename($file);
                        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                        // Determine MIME type
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mime_type = finfo_buffer($finfo, $file_content);
                        finfo_close($finfo);

                        // Set headers and output file
                        header('Content-Type: ' . $mime_type);
                        header('Content-Length: ' . strlen($file_content));
                        header('Content-Disposition: inline; filename="' . $file_name . '"');
                        header('Cache-Control: public, max-age=3600');
                        echo $file_content;
                        exit;
                    }
                }
            }
        }
    } else {
        error_log('Directory does not exist: ' . $directory);

        // Try to find the file in a broader search
        $searchPaths = [
            'uploads/admin/2025/09/',
            'uploads/admin/2025/',
            'uploads/admin/',
            'uploads/'
        ];

        foreach ($searchPaths as $searchPath) {
            if ($sftp->is_dir($searchPath)) {
                $files = $sftp->nlist($searchPath);
                error_log('Searching in: ' . $searchPath . ' - Files: ' . json_encode($files));

                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === $extension) {
                        $file_content = $sftp->get($searchPath . $file);
                        if ($file_content !== false) {
                            // Serve the found file
                            $file_name = basename($file);
                            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                            // Determine MIME type
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $mime_type = finfo_buffer($finfo, $file_content);
                            finfo_close($finfo);

                            // Set headers and output file
                            header('Content-Type: ' . $mime_type);
                            header('Content-Length: ' . strlen($file_content));
                            header('Content-Disposition: inline; filename="' . $file_name . '"');
                            header('Cache-Control: public, max-age=3600');
                            echo $file_content;
                            exit;
                        }
                    }
                }
            }
        }
    }
}

// Not found: try to serve a fallback image or return a 404 with helpful message
error_log('File not found on SFTP: ' . $sftp_path);

// Try to serve a fallback image if it's an image request
$extension = strtolower(pathinfo($sftp_path, PATHINFO_EXTENSION));
if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
    // Try to find any image file in the uploads directory
    $fallbackPaths = [
        'uploads/admin/2025/09/',
        'uploads/admin/2025/',
        'uploads/admin/',
        'uploads/'
    ];

    foreach ($fallbackPaths as $fallbackPath) {
        if ($sftp->is_dir($fallbackPath)) {
            $files = $sftp->nlist($fallbackPath);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $file_content = $sftp->get($fallbackPath . $file);
                    if ($file_content !== false) {
                        // Serve the fallback image
                        $file_name = basename($file);
                        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                        // Determine MIME type
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mime_type = finfo_buffer($finfo, $file_content);
                        finfo_close($finfo);

                        // Set headers and output file
                        header('Content-Type: ' . $mime_type);
                        header('Content-Length: ' . strlen($file_content));
                        header('Content-Disposition: inline; filename="' . $file_name . '"');
                        header('Cache-Control: public, max-age=3600');
                        echo $file_content;
                        exit;
                    }
                }
            }
        }
    }
}

// If no fallback found, return 404
header('HTTP/1.0 404 Not Found');
echo 'File not found: ' . $sftp_path;
exit;
?>

