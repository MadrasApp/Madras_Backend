<?php

    $base_path = base_url() ; // Base path for this script
    $requested_url = $_SERVER['REQUEST_URI']; // Full request URI
    $relative_path = str_replace($base_path, '', $requested_url); // Remove base path
    $relative_path = ltrim($relative_path, '/'); // Remove leading slash if present
    // $outputString = str_replace("api/v2/fetchFile/", "", $relative_path);
    $file_path = '/lexoya/var/www/html/';
    // $file_path = str_replace("api/v2/fetchFile/", "", $file_path);


    if (file_exists($file_path)) {
        $mime_type = mime_content_type($file_path);
        header("Content-Type: $mime_type");
        header("Content-Length: " . filesize($file_path));
        header("Content-Disposition: inline; filename=\"" . basename($file_path) . "\""); // Display file directly in browser
        readfile($file_path);
        exit;
    } else {
        header("HTTP/1.0 404 Not Found");
        echo $file_path;
        exit;
    }


?>