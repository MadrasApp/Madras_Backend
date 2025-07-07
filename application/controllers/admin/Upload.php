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

        // Check for a valid file in $_FILES
        if (! isset($_FILES['file']) || ! is_uploaded_file($_FILES['file']['tmp_name'])) {
            $msg = 'No valid file was uploaded.';
            $this->sendResponse($success, $msg, $dashManifestUrl);
            return;
        }

        // Gather file data
        $fileTemp     = $_FILES['file']['tmp_name'];
        $fileName     = $_FILES['file']['name'];
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

    private function uploadToSFTP($localDir, $fileBaseName)
    {
        $this->load->config('sftp');
        $sftpConfig = $this->config->item('sftp');

        $sftp_host = $sftpConfig['host'];
        $sftp_port = $sftpConfig['port'];
        $sftp_user = $sftpConfig['username'];
        $sftp_pass = $sftpConfig['password'];

        // Determine the remote directory
        $remoteDir = "/lexoya/var/www/html/uploads/" . $this->user->data->username . '/' . date("Y/m/") . $fileBaseName;

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
}
