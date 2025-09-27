<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use phpseclib3\Net\SFTP;

/**
 * SFTP Helper Functions
 *
 * Provides utility functions for working with SFTP server
 * instead of local filesystem operations.
 */

if (!function_exists('sftp_file_exists')) {
    /**
     * Check if file exists on SFTP server with fallback path support
     *
     * @param string $filePath The file path to check
     * @return bool|string Returns true if file exists, false otherwise
     *                     If file exists with alternative path, returns the actual path
     */
    function sftp_file_exists($filePath)
    {
        static $sftp = null;
        static $sftpConfig = null;

        // Ensure autoloader is loaded
        if (!class_exists('phpseclib3\\Net\\SFTP')) {
            require_once(FCPATH . 'vendor/autoload.php');
        }

        // Load SFTP config if not already loaded
        if ($sftpConfig === null) {
            $config = include(APPPATH . 'config/sftp.php');
            $sftpConfig = isset($config['sftp']) ? $config['sftp'] : null;

            if (!$sftpConfig) {
                error_log('SFTP configuration not found in helper');
                return false;
            }
        }

        // Connect to SFTP if not already connected
        if ($sftp === null || !$sftp->isConnected()) {
            $sftp = new SFTP($sftpConfig['host'], $sftpConfig['port']);
            if (!$sftp->login($sftpConfig['username'], $sftpConfig['password'])) {
                error_log('SFTP login failed in sftp_file_exists');
                return false;
            }
        }

        // Clean the file path and handle Unicode characters
        $filePath = ltrim($filePath, '/');
        $filePath = mb_convert_encoding($filePath, 'UTF-8', 'auto');

        // First try the original path
        if ($sftp->file_exists($filePath)) {
            return $filePath;
        }

        // Try alternative path format: /uploads/admin/2025/09/k.jpg/k.jpg
        $pathParts = explode('/', $filePath);
        if (count($pathParts) >= 5) {
            // Extract: uploads/admin/2025/09/k.jpg
            $basePath = implode('/', array_slice($pathParts, 0, 5));
            $fileName = end($pathParts);

            // Create alternative path: uploads/admin/2025/09/k.jpg/k.jpg
            $alternativePath = $basePath . '/' . $fileName;

            if ($sftp->file_exists($alternativePath)) {
                return $alternativePath;
            }
        }

        // Try simplified path format: uploads/admin/2025/09/filename
        // This handles the case where files are stored directly in the year/month directory
        if (count($pathParts) >= 4) {
            $yearMonthPath = implode('/', array_slice($pathParts, 0, 4));
            $fileName = end($pathParts);
            $simplifiedPath = $yearMonthPath . '/' . $fileName;

            if ($sftp->file_exists($simplifiedPath)) {
                return $simplifiedPath;
            }
        }

        return false;
    }
}

if (!function_exists('sftp_get_file')) {
    /**
     * Get file content from SFTP server with fallback path support
     *
     * @param string $filePath The file path to retrieve
     * @return string|false Returns file content or false if not found
     */
    function sftp_get_file($filePath)
    {
        static $sftp = null;
        static $sftpConfig = null;

        // Ensure autoloader is loaded
        if (!class_exists('phpseclib3\\Net\\SFTP')) {
            require_once(FCPATH . 'vendor/autoload.php');
        }

        // Load SFTP config if not already loaded
        if ($sftpConfig === null) {
            $config = include(APPPATH . 'config/sftp.php');
            $sftpConfig = isset($config['sftp']) ? $config['sftp'] : null;

            if (!$sftpConfig) {
                error_log('SFTP configuration not found in helper');
                return false;
            }
        }

        // Connect to SFTP if not already connected
        if ($sftp === null || !$sftp->isConnected()) {
            $sftp = new SFTP($sftpConfig['host'], $sftpConfig['port']);
            if (!$sftp->login($sftpConfig['username'], $sftpConfig['password'])) {
                error_log('SFTP login failed in sftp_get_file');
                return false;
            }
        }

        // Clean the file path and handle Unicode characters
        $filePath = ltrim($filePath, '/');
        $filePath = mb_convert_encoding($filePath, 'UTF-8', 'auto');

        // First try the original path
        if ($sftp->file_exists($filePath)) {
            return $sftp->get($filePath);
        }

        // Try alternative path format: /uploads/admin/2025/09/k.jpg/k.jpg
        $pathParts = explode('/', $filePath);
        if (count($pathParts) >= 5) {
            // Extract: uploads/admin/2025/09/k.jpg
            $basePath = implode('/', array_slice($pathParts, 0, 5));
            $fileName = end($pathParts);

            // Create alternative path: uploads/admin/2025/09/k.jpg/k.jpg
            $alternativePath = $basePath . '/' . $fileName;

            if ($sftp->file_exists($alternativePath)) {
                return $sftp->get($alternativePath);
            }
        }

        // Try simplified path format: uploads/admin/2025/09/filename
        // This handles the case where files are stored directly in the year/month directory
        if (count($pathParts) >= 4) {
            $yearMonthPath = implode('/', array_slice($pathParts, 0, 4));
            $fileName = end($pathParts);
            $simplifiedPath = $yearMonthPath . '/' . $fileName;

            if ($sftp->file_exists($simplifiedPath)) {
                return $sftp->get($simplifiedPath);
            }
        }

        return false;
    }
}

if (!function_exists('sftp_get_file_size')) {
    /**
     * Get file size from SFTP server
     *
     * @param string $filePath The file path to check
     * @return int|false Returns file size or false if not found
     */
    function sftp_get_file_size($filePath)
    {
        static $sftp = null;
        static $sftpConfig = null;

        // Ensure autoloader is loaded
        if (!class_exists('phpseclib3\\Net\\SFTP')) {
            require_once(FCPATH . 'vendor/autoload.php');
        }

        // Load SFTP config if not already loaded
        if ($sftpConfig === null) {
            $config = include(APPPATH . 'config/sftp.php');
            $sftpConfig = isset($config['sftp']) ? $config['sftp'] : null;

            if (!$sftpConfig) {
                error_log('SFTP configuration not found in helper');
                return false;
            }
        }

        // Connect to SFTP if not already connected
        if ($sftp === null || !$sftp->isConnected()) {
            $sftp = new SFTP($sftpConfig['host'], $sftpConfig['port']);
            if (!$sftp->login($sftpConfig['username'], $sftpConfig['password'])) {
                error_log('SFTP login failed in sftp_get_file_size');
                return false;
            }
        }

        // Clean the file path and handle Unicode characters
        $filePath = ltrim($filePath, '/');
        $filePath = mb_convert_encoding($filePath, 'UTF-8', 'auto');

        // First try the original path
        if ($sftp->file_exists($filePath)) {
            $stat = $sftp->stat($filePath);
            return $stat ? $stat['size'] : false;
        }

        // Try alternative path format: /uploads/admin/2025/09/k.jpg/k.jpg
        $pathParts = explode('/', $filePath);
        if (count($pathParts) >= 5) {
            // Extract: uploads/admin/2025/09/k.jpg
            $basePath = implode('/', array_slice($pathParts, 0, 5));
            $fileName = end($pathParts);

            // Create alternative path: uploads/admin/2025/09/k.jpg/k.jpg
            $alternativePath = $basePath . '/' . $fileName;

            if ($sftp->file_exists($alternativePath)) {
                $stat = $sftp->stat($alternativePath);
                return $stat ? $stat['size'] : false;
            }
        }

        // Try simplified path format: uploads/admin/2025/09/filename
        // This handles the case where files are stored directly in the year/month directory
        if (count($pathParts) >= 4) {
            $yearMonthPath = implode('/', array_slice($pathParts, 0, 4));
            $fileName = end($pathParts);
            $simplifiedPath = $yearMonthPath . '/' . $fileName;

            if ($sftp->file_exists($simplifiedPath)) {
                $stat = $sftp->stat($simplifiedPath);
                return $stat ? $stat['size'] : false;
            }
        }

        return false;
    }
}
