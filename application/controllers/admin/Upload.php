<?php defined('BASEPATH') OR exit('No direct script access allowed');

use phpseclib3\Net\SFTP;

// Add require statement for SFTP if autoload is not working
if (!class_exists('phpseclib3\\Net\\SFTP')) {
    require_once(APPPATH . '../vendor/autoload.php');
}

/**
 * Class Upload
 *
 * Handles file uploads and converts them (audio/video) to DASH format using FFmpeg.
 * Uploads the resulting files to an SFTP server.
 */
class Upload extends CI_Controller
{
    public $setting;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_user', 'user');
        $this->load->model('admin/m_media', 'media');
    }

    public function index()
    {
        // Log current PHP configuration for debugging
        $this->logUploadConfiguration();

        // Default response data
        $success         = false;
        $msg             = '';
        $dashManifestUrl = '';

        // Check if user is logged in
        if (! $this->user->check_login()) {
            $msg = 'login-needed';
            $this->sendResponse($success, $msg, $dashManifestUrl);
            return;
        }

        // Enhanced file upload validation with detailed error reporting
        if (! isset($_FILES['file'])) {
            $msg = 'No file data received in request.';
            log_message('error', 'Upload failed: $_FILES array is empty');
            $this->sendResponse($success, $msg, $dashManifestUrl);
            return;
        }

        $file = $_FILES['file'];
        
        // Check for upload errors
        if (isset($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
            $errorMsg = $this->getUploadErrorMessage($file['error']);
            $msg = 'File upload error: ' . $errorMsg;
            log_message('error', 'Upload failed with error code ' . $file['error'] . ': ' . $errorMsg);
            $this->sendResponse($success, $msg, $dashManifestUrl);
            return;
        }

        // Check if file was actually uploaded
        if (! is_uploaded_file($file['tmp_name'])) {
            $msg = 'File upload validation failed. The file was not properly uploaded.';
            log_message('error', 'Upload failed: is_uploaded_file() returned false for ' . $file['tmp_name']);
            $this->sendResponse($success, $msg, $dashManifestUrl);
            return;
        }

        // Check if temporary file exists and is readable
        if (! file_exists($file['tmp_name']) || ! is_readable($file['tmp_name'])) {
            $msg = 'Temporary file is not accessible.';
            log_message('error', 'Upload failed: temp file not accessible - ' . $file['tmp_name']);
            $this->sendResponse($success, $msg, $dashManifestUrl);
            return;
        }

        // Check file size
        if ($file['size'] <= 0) {
            $msg = 'Uploaded file has invalid size (0 or negative).';
            log_message('error', 'Upload failed: file size is ' . $file['size']);
            $this->sendResponse($success, $msg, $dashManifestUrl);
            return;
        }

        // Log successful file detection
        log_message('info', 'File upload detected: ' . $file['name'] . ' (' . $file['size'] . ' bytes)');

        // Gather file data
        $fileTemp     = $file['tmp_name'];
        $fileName     = $file['name'];
        $fileBaseName = pathinfo($fileName, PATHINFO_FILENAME); // File name without extension

        // Determine the user directory (admin can override via POST)
        $dirPost = $this->input->post('dir', true);
        $dir     = $this->user->data->username;
        if ($this->user->is_admin() && ! empty($dirPost)) {
            $dir = $dirPost;
        }

        try {
            $directory = $this->createDirectoryStructure($dir, $fileBaseName);
            $fullFilePath = $this->moveUploadedFile($fileTemp, $fileName, $directory);

            if ($this->isImageFile($fileName)) {
                $this->uploadToSFTP($directory, $fileName);
                $msg = "Image uploaded successfully.";
                $success = true;
            } elseif ($this->isDocumentFile($fileName)) {
                // Handle document file upload
                $this->uploadToSFTP($directory, $fileName);
                $msg = "Document uploaded successfully.";
                $success = true;
            } elseif ($this->isVideoFile($fileName)) {
                $this->uploadToSFTP($directory, $fileName);
                $msg = "Video uploaded successfully.";
                $success = true;
            } elseif ($this->isAudioFile($fileName)) {
                $this->uploadToSFTP($directory, $fileName);
                $msg = "Audio uploaded successfully.";
                $success = true;
            } else {
                $msg = "Only image, document, video, and audio files are allowed.";
            }

        } catch (Exception $e) {
            // Catch potential errors and log them
            log_message('error', 'Error during file upload or conversion: ' . $e->getMessage());
            $msg = $e->getMessage();
        }

        // Output JSON response
        $this->sendResponse($success, $msg, $dashManifestUrl);
    }

    /**
     * Test endpoint to check upload configuration
     * Access via: /admin/upload/test
     */
    public function test()
    {
        // Check if user is logged in
        if (! $this->user->check_login()) {
            echo json_encode(['error' => 'login-needed']);
            return;
        }

        $config = [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_execution_time' => ini_get('max_execution_time'),
            'max_input_time' => ini_get('max_input_time'),
            'memory_limit' => ini_get('memory_limit'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            'file_uploads' => ini_get('file_uploads'),
            'upload_tmp_dir' => ini_get('upload_tmp_dir'),
            'temp_dir_writable' => is_writable(sys_get_temp_dir()),
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
        ];

        echo json_encode($config, JSON_PRETTY_PRINT);
    }

    private function uploadToSFTP($localDir, $fileBaseName)
    {
        $this->load->config('sftp');
        $sftpConfig = $this->config->item('sftp');

        $sftp_host = $sftpConfig['host'];
        $sftp_port = $sftpConfig['port'];
        $sftp_user = $sftpConfig['username'];
        $sftp_pass = $sftpConfig['password'];

        // Determine the remote directory
        $remoteDir = "/uploads/" . $this->user->data->username . '/' . date("Y/m/") . $fileBaseName;

        $sftp = new SFTP($sftp_host, $sftp_port);
        if (! $sftp->login($sftp_user, $sftp_pass)) {
            throw new Exception("Failed to connect to SFTP server.");
        }

        // Check if the directory contains a DASH folder or standalone files
        $dashDir = $localDir . "/dash";
        $filesToUpload = [];

        if (is_dir($dashDir)) {
            $filesToUpload = scandir($dashDir);
            $baseDir = $dashDir;
        } else {
            // If no DASH directory, upload standalone files in $localDir
            $filesToUpload = scandir($localDir);
            $baseDir = $localDir;
        }

        foreach ($filesToUpload as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $localFilePath = $baseDir . "/" . $file;
            $remoteFilePath = $remoteDir . "/" . $file;

            $sftp->mkdir(dirname($remoteFilePath), -1, true); // Ensure the directory exists on the server

            if ($sftp->put($remoteFilePath, file_get_contents($localFilePath))) {
                // File uploaded successfully
                unlink($localFilePath); // Optionally delete the local file after upload
            } else {
                throw new Exception("Failed to upload file to SFTP: $file");
            }
        }

        // Clean up directories if empty
        $this->deleteDirectoryRecursively($localDir);
    }

    private function deleteDirectoryRecursively($directory)
    {
        if (!is_dir($directory)) {
            return;
        }

        // Get all files and subdirectories in the current directory
        $files = array_diff(scandir($directory), array('.', '..'));

        // Loop through all files and delete them
        foreach ($files as $file) {
            $filePath = $directory . DIRECTORY_SEPARATOR . $file;
            if (is_dir($filePath)) {
                // Recursively delete subdirectories
                $this->deleteDirectoryRecursively($filePath);
            } else {
                // Delete the file
                unlink($filePath);
            }
        }

        // Once all contents are deleted, delete the directory itself
        rmdir($directory);
    }

    private function createDirectoryStructure($dir, $fileBaseName)
    {
        $dirArr = ['uploads', $dir, date("Y"), date("m"), $fileBaseName];
        $directory = $this->media->mkDirArray($dirArr);
        if (! $directory) {
            throw new Exception("Failed to create directory structure.");
        }
        return $directory;
    }

    private function moveUploadedFile($tmpPath, $fileName, $directory)
    {
        $targetFile = $directory . "/" . $fileName;
        $targetFile = $this->media->optimizedFileName($targetFile);

        if (! move_uploaded_file($tmpPath, $targetFile)) {
            throw new Exception("Failed to move uploaded file.");
        }

        return $targetFile;
    }

    private function isImageFile($filename)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'avif']);
    }

    private function isDocumentFile($filename)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, ['pdf', 'doc', 'docx', 'txt', 'xlsx', 'xls', 'pptx', 'csv']);
    }

    private function isVideoFile($filename)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, ['mp4', 'mkv', 'mov', 'avi', 'webm', 'flv']);
    }

    private function isAudioFile($filename)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, ['mp3', 'wav', 'aac', 'ogg', 'flac', 'm4a']);
    }

    private function sendResponse($success, $msg, $dashManifestUrl)
    {
        $response = [
            'files' => [
                'dash_manifest_url' => $dashManifestUrl,
                'msg'               => $msg,
                'action'            => $success ? 'done' : 'fail'
            ]
        ];

        echo json_encode(
            $response,
            JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG
        );
    }

    /**
     * Get human-readable upload error message
     */
    private function getUploadErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload';
            default:
                return 'Unknown upload error';
        }
    }

    /**
     * Log current PHP upload configuration for debugging
     */
    private function logUploadConfiguration()
    {
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
        
        log_message('info', 'PHP Upload Configuration: ' . json_encode($config));
    }
}
