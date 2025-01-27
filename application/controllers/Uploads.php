<?php

    // Base path for your server
    $base_path = 'https://modir.madras.app/'; // Replace with your actual base URL
    $requested_url = $_SERVER['REQUEST_URI']; // Full requested URL
    $relative_path = str_replace($base_path, '', $requested_url); // Remove base path
    $relative_path = ltrim($relative_path, '/'); // Remove leading slash if present

    // Define the local file path (where the media files are stored on your server)
    $file_path = '/lexoya/var/www/html/' . $relative_path;

    // Check if the file exists
    if (file_exists($file_path)) {
        // Get the MIME type of the file
        $mime_type = mime_content_type($file_path);

        // Set headers to serve the file
        header("Content-Type: $mime_type");
        header("Content-Length: " . filesize($file_path));
        header("Content-Disposition: inline; filename=\"" . basename($file_path) . "\""); // Serve the file directly

        // Read and output the file content
        readfile($file_path);
        exit;
    } else {
        // If the file does not exist, return a 404 error
        header("HTTP/1.0 404 Not Found");
        echo "File not found: $file_path";
        exit;
    }

?>
