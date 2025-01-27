<?php

    // Base path for your server
    $base_path = CDN_URL; // Replace with your actual base URL
    $requested_url = $_SERVER['REQUEST_URI']; // Full requested URL

    // Debug: Log the raw URI
    error_log("Raw URI: " . $requested_url);

    // Sanitize or encode the requested URL
    $requested_url = filter_var($requested_url, FILTER_SANITIZE_URL);

    // Remove base path and leading slash
    $relative_path = str_replace($base_path, '', $requested_url);
    $relative_path = ltrim($relative_path, '/');

    // Define the local file path (where the media files are stored on your server)
    $file_path = '/lexoya/var/www/html/' . $relative_path;

    // Check if the file exists
    if (file_exists($file_path)) {
        // Get the MIME type of the file
        $mime_type = mime_content_type($file_path);

        // Set headers to serve the file
        header("Content-Type: $mime_type");
        header("Content-Length: " . filesize($file_path));
        header("Content-Disposition: inline; filename=\"" . basename($file_path) . "\"");

        // Read and output the file content
        readfile($file_path);
        exit;
    } else {
        // Debug: Log file not found
        error_log("File not found: $file_path");
        
        // Return a 404 error
        header("HTTP/1.0 404 Not Found");
        echo "File not found: $file_path";
        exit;
    }

?>
