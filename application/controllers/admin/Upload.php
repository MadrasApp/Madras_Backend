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

            // Generate thumbnails for images (enables previews and confirms non-zero size)
            if ($this->isImageFile($fileName)) {
                $this->media->creatThumb($fullFilePath);
            }

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

        // Determine the remote directory to mirror local: /uploads/{user}/Y/m/{basename}[ /dash ]
        $normalizedLocal = rtrim(str_replace('\\','/', $localDir), '/');
        $segments = array_values(array_filter(explode('/', $normalizedLocal), function($s){ return $s !== ''; }));
        // Find index of 'uploads' and extract following parts robustly
        $uploadsIndex = array_search('uploads', $segments);
        $userDir = $this->user->data->username;
        $yearDir = date('Y');
        $monthDir = date('m');
        $baseNameDir = basename($normalizedLocal);
        if ($uploadsIndex !== false) {
            $userDir     = isset($segments[$uploadsIndex+1]) ? $segments[$uploadsIndex+1] : $userDir;
            $yearDir     = isset($segments[$uploadsIndex+2]) ? $segments[$uploadsIndex+2] : $yearDir;
            $monthDir    = isset($segments[$uploadsIndex+3]) ? $segments[$uploadsIndex+3] : $monthDir;
            $baseNameDir = isset($segments[$uploadsIndex+4]) ? $segments[$uploadsIndex+4] : $baseNameDir;
        }
        // Build remote base directory including basename and optional dash
        $remoteBaseDir = "/uploads/{$userDir}/{$yearDir}/{$monthDir}/{$baseNameDir}";

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
            $remoteDir = $remoteBaseDir . "/dash";
        } else {
            // If no DASH directory, upload standalone files in $localDir
            $filesToUpload = scandir($localDir);
            $baseDir = $localDir;
            $remoteDir = $remoteBaseDir;
        }

        $uploadedFiles = [];
        $failedFiles   = [];

        foreach ($filesToUpload as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $localFilePath  = $baseDir . "/" . $file;
            $remoteFilePath = $remoteDir . "/" . $file;

            // Ensure the directory exists on the server
            $sftp->mkdir(dirname($remoteFilePath), -1, true);

            $putOk = $sftp->put($remoteFilePath, file_get_contents($localFilePath));

            // Some servers may still have the file even if put() returned false.
            // Verify existence via stat() as a fallback heuristic.
            if (! $putOk) {
                try {
                    $stat = $sftp->stat($remoteFilePath);
                    if (is_array($stat) && isset($stat['size']) && $stat['size'] > 0) {
                        $putOk = true;
                    }
                } catch (\Throwable $e) {
                    // ignore stat errors; we'll treat as failed below
                }
            }

            if ($putOk) {
                $uploadedFiles[] = $file; 
                // Keep local files so the media browser (which scans local uploads) can display them
            } else {
                $failedFiles[] = $file;
            }
        }

        // If none of the files made it to the remote, fail the whole operation.
        if (count($uploadedFiles) === 0 && count($failedFiles) > 0) {
            $list = implode(', ', $failedFiles);
            throw new Exception("Failed to upload file(s) to SFTP: " . $list);
        }

        // If some files failed but at least one succeeded, continue without throwing.
        // Caller will still report success for the overall upload to avoid false negatives in UI.

        // Do not delete local directory; media browser depends on local files to list images
    }

    public function deleteFromSFTPByLocalPath($localFilePath)
    {
        $this->load->config('sftp');
        $sftpConfig = $this->config->item('sftp');

        $sftp_host = $sftpConfig['host'];
        $sftp_port = $sftpConfig['port'];
        $sftp_user = $sftpConfig['username'];
        $sftp_pass = $sftpConfig['password'];

        $normalized = str_replace('\\','/', $localFilePath);
        // Expect local like: FCPATH/uploads/{user}/Y/m/{basename}/{filename}
        $fc = rtrim(str_replace('\\','/', FCPATH), '/').'/';
        if (strpos($normalized, $fc) === 0) {
            $normalized = substr($normalized, strlen($fc));
        }
        if (strpos($normalized, 'uploads/') !== 0) return FALSE;

        $segments = explode('/', $normalized);
        // uploads, user, Y, m, basename, filename
        if (count($segments) < 6) return FALSE;
        $userDir = $segments[1];
        $year    = $segments[2];
        $month   = $segments[3];
        $base    = $segments[4];
        $filename= $segments[5];

        // Remote has no basename folder: /uploads/{user}/{Y}/{m}/filename
        $remoteDir = "/uploads/{$userDir}/{$year}/{$month}";
        $remoteFile = $remoteDir . '/' . $filename;

        $sftp = new SFTP($sftp_host, $sftp_port);
        if (! $sftp->login($sftp_user, $sftp_pass)) {
            return FALSE;
        }

        // delete thumbnails too if present
        $pathinfo = pathinfo($filename);
        $name = $pathinfo['filename'];
        $ext  = isset($pathinfo['extension']) ? ('.'.$pathinfo['extension']) : '';
        $thumbs = array(
            $remoteDir . '/' . $name . '-150' . $ext,
            $remoteDir . '/' . $name . '-300' . $ext,
            $remoteDir . '/' . $name . '-600' . $ext,
        );
        foreach ($thumbs as $t) { @ $sftp->delete($t); }

        @ $sftp->delete($remoteFile);

        // attempt to remove remote dir if empty (only the leaf month)
        $list = $sftp->nlist($remoteDir);
        if (is_array($list)) {
            $nonDot = array_diff($list, array('.', '..'));
            if (count($nonDot) === 0) {
                @ $sftp->rmdir($remoteDir);
            }
        }
        return TRUE;
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
        // Sanitize base directory name to avoid non-ASCII issues on Windows/Linux
        $safeBase = $this->media->normalizeString((string)$fileBaseName);
        if ($safeBase === '' || $safeBase === null) {
            $safeBase = date("Y-m-d[H.i]");
        }
        $dirArr = ['uploads', $dir, date("Y"), date("m"), $safeBase];
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

