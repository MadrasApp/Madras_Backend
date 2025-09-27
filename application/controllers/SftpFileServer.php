<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use phpseclib3\Net\SFTP;

/**
 * SFTP File Server Controller
 *
 * Handles serving files from SFTP server instead of local filesystem.
 * Supports both file path formats:
 * - /uploads/admin/2025/09/k/k.jpg
 * - /uploads/admin/2025/09/k.jpg/k.jpg
 */
class SftpFileServer extends CI_Controller
{
    private $sftp;
    private $sftpConfig;

    public function __construct()
    {
        parent::__construct();
        $this->load->config('sftp');
        $this->sftpConfig = $this->config->item('sftp');

        // Ensure autoloader is loaded
        if (!class_exists('phpseclib3\\Net\\SFTP')) {
            require_once(FCPATH . 'vendor/autoload.php');
        }
    }

    /**
     * Connect to SFTP server
     */
    private function connectSFTP()
    {
        if ($this->sftp && $this->sftp->isConnected()) {
            return true;
        }

        $this->sftp = new SFTP($this->sftpConfig['host'], $this->sftpConfig['port']);

        if (!$this->sftp->login($this->sftpConfig['username'], $this->sftpConfig['password'])) {
            error_log('SFTP login failed');
            return false;
        }

        return true;
    }

    /**
     * Get file from SFTP server with fallback path support
     */
    public function getFile($filePath = null)
    {
        if (!$filePath) {
            $filePath = $this->input->get('path');
        }

        if (!$filePath) {
            $this->output->set_status_header(400);
            echo 'File path required';
            return;
        }

        // Clean and validate the file path
        $filePath = $this->sanitizePath($filePath);

        if (!$this->connectSFTP()) {
            $this->output->set_status_header(500);
            echo 'SFTP connection failed';
            return;
        }

        // Try to get the file with both path formats
        $fileContent = $this->getFileFromSFTP($filePath);

        if ($fileContent === false) {
            $this->output->set_status_header(404);
            echo 'File not found';
            return;
        }

        // Set appropriate headers
        $this->setFileHeaders($filePath, $fileContent);

        // Output file content
        echo $fileContent;
    }

    /**
     * Sanitize and validate file path
     */
    private function sanitizePath($path)
    {
        // Remove any leading slashes and normalize
        $path = ltrim($path, '/');

        // Prevent directory traversal
        if (strpos($path, '..') !== false) {
            throw new Exception('Invalid path');
        }

        // Ensure path starts with uploads
        if (strpos($path, 'uploads/') !== 0) {
            $path = 'uploads/' . $path;
        }

        return $path;
    }

    /**
     * Get file from SFTP with support for both path formats
     */
    private function getFileFromSFTP($filePath)
    {
        // First try the original path
        if ($this->sftp->file_exists($filePath)) {
            return $this->sftp->get($filePath);
        }

        // Try alternative path format: /uploads/admin/2025/09/k.jpg/k.jpg
        $pathParts = explode('/', $filePath);
        if (count($pathParts) >= 5) {
            // Extract: uploads/admin/2025/09/k.jpg
            $basePath = implode('/', array_slice($pathParts, 0, 5));
            $fileName = end($pathParts);

            // Create alternative path: uploads/admin/2025/09/k.jpg/k.jpg
            $alternativePath = $basePath . '/' . $fileName;

            if ($this->sftp->file_exists($alternativePath)) {
                return $this->sftp->get($alternativePath);
            }
        }

        return false;
    }

    /**
     * Set appropriate HTTP headers for file serving
     */
    private function setFileHeaders($filePath, $fileContent)
    {
        $fileName = basename($filePath);
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Set MIME type based on file extension
        $mimeTypes = [
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

        $mimeType = isset($mimeTypes[$extension]) ? $mimeTypes[$extension] : 'application/octet-stream';

        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . strlen($fileContent));
        header('Content-Disposition: inline; filename="' . $fileName . '"');
        header('Cache-Control: public, max-age=3600'); // Cache for 1 hour
    }

    /**
     * Check if file exists on SFTP server
     */
    public function fileExists($filePath = null)
    {
        if (!$filePath) {
            $filePath = $this->input->get('path');
        }

        if (!$filePath) {
            $this->output->set_status_header(400);
            echo json_encode(['exists' => false, 'error' => 'File path required']);
            return;
        }

        $filePath = $this->sanitizePath($filePath);

        if (!$this->connectSFTP()) {
            $this->output->set_status_header(500);
            echo json_encode(['exists' => false, 'error' => 'SFTP connection failed']);
            return;
        }

        $exists = $this->sftp->file_exists($filePath);

        // If not found, try alternative path format
        if (!$exists) {
            $pathParts = explode('/', $filePath);
            if (count($pathParts) >= 5) {
                $basePath = implode('/', array_slice($pathParts, 0, 5));
                $fileName = end($pathParts);
                $alternativePath = $basePath . '/' . $fileName;
                $exists = $this->sftp->file_exists($alternativePath);
            }
        }

        echo json_encode(['exists' => $exists]);
    }

    /**
     * Get file info from SFTP server
     */
    public function getFileInfo($filePath = null)
    {
        if (!$filePath) {
            $filePath = $this->input->get('path');
        }

        if (!$filePath) {
            $this->output->set_status_header(400);
            echo json_encode(['error' => 'File path required']);
            return;
        }

        $filePath = $this->sanitizePath($filePath);

        if (!$this->connectSFTP()) {
            $this->output->set_status_header(500);
            echo json_encode(['error' => 'SFTP connection failed']);
            return;
        }

        $stat = $this->sftp->stat($filePath);

        if ($stat === false) {
            // Try alternative path format
            $pathParts = explode('/', $filePath);
            if (count($pathParts) >= 5) {
                $basePath = implode('/', array_slice($pathParts, 0, 5));
                $fileName = end($pathParts);
                $alternativePath = $basePath . '/' . $fileName;
                $stat = $this->sftp->stat($alternativePath);
            }
        }

        if ($stat === false) {
            $this->output->set_status_header(404);
            echo json_encode(['error' => 'File not found']);
            return;
        }

        echo json_encode([
            'size' => $stat['size'],
            'mtime' => $stat['mtime'],
            'type' => $stat['type']
        ]);
    }
}

