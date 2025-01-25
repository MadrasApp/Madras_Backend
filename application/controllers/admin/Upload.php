<?php defined('BASEPATH') OR exit('No direct script access allowed');

use phpseclib3\Net\SFTP;

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
            } else {
                $dashManifestUrl = $this->convertToDash($fullFilePath, $fileBaseName, $directory, $dir);
                if (! empty($dashManifestUrl)) {
                    $this->uploadToSFTP($directory, $fileBaseName);
                    $msg = "File uploaded and converted to DASH format successfully.";
                    $success = true;
                } else {
                    $msg = "File uploaded successfully, but DASH conversion failed.";
                }
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
        // Load SFTP configuration
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

    private function convertToDash($sourceFile, $fileBaseName, $directory, $username)
    {
        $dashDir = $directory . "/dash";
        if (! is_dir($dashDir)) {
            if (! mkdir($dashDir, 0777, true)) {
                log_message('error', 'Failed to create DASH directory.');
                return '';
            }
        }

        $isAudio = $this->isAudioFile($sourceFile);
        $isVideo = $this->isVideoFile($sourceFile);

        if (! $isAudio && ! $isVideo) {
            log_message('error', 'File is neither recognized video nor audio: ' . $sourceFile);
            return '';
        }

        $encodedFiles = [];

        if ($isVideo) {
            $resolutions = [
                '480p'  => ['scale' => '-vf scale=854:480',   'bitrate' => '800k'],
                '720p'  => ['scale' => '-vf scale=1280:720',  'bitrate' => '2000k'],
                '1080p' => ['scale' => '-vf scale=1920:1080', 'bitrate' => '4500k'],
            ];

            foreach ($resolutions as $label => $options) {
                $outputFile = $dashDir . "/" . $fileBaseName . "_{$label}.mp4";

                $command = sprintf(
                    'ffmpeg -y -i %s %s -map 0:v -map 0:a? -c:v libx264 -preset fast -crf 23 -b:v %s -c:a aac -b:a 128k %s 2>&1',
                    escapeshellarg($sourceFile),
                    $options['scale'],
                    escapeshellarg($options['bitrate']),
                    escapeshellarg($outputFile)
                );

                $output = shell_exec($command);
                log_message('error', "FFmpeg Output ({$label}): " . $output);

                if (file_exists($outputFile)) {
                    $encodedFiles[] = $outputFile;
                } else {
                    log_message('error', "Failed to create {$label} version.");
                }
            }
        } else if ($isAudio) {
            $audioBitrates = [
                '64k'  => '64k',
                '128k' => '128k',
                '256k' => '256k'
            ];

            foreach ($audioBitrates as $label => $bitrate) {
                $outputFile = $dashDir . "/" . $fileBaseName . "_{$label}.m4a";

                $command = sprintf(
                    'ffmpeg -y -i %s -vn -c:a aac -b:a %s %s 2>&1',
                    escapeshellarg($sourceFile),
                    escapeshellarg($bitrate),
                    escapeshellarg($outputFile)
                );

                $output = shell_exec($command);
                log_message('error', "FFmpeg Audio Output ({$label}): " . $output);

                if (file_exists($outputFile)) {
                    $encodedFiles[] = $outputFile;
                } else {
                    log_message('error', "Failed to create {$label} version of the audio file.");
                }
            }
        }

        if (empty($encodedFiles)) {
            return '';
        }

        $dashManifest = $dashDir . "/" . $fileBaseName . ".mpd";
        $ffmpegDashCmd = 'ffmpeg -y ';

        foreach ($encodedFiles as $encodedFile) {
            $ffmpegDashCmd .= '-f mp4 -i ' . escapeshellarg($encodedFile) . ' ';
        }

        for ($i = 0; $i < count($encodedFiles); $i++) {
            $ffmpegDashCmd .= '-map ' . $i . ' ';
        }

        if ($isAudio) {
            $ffmpegDashCmd .= '-c copy -f dash -use_timeline 1 -use_template 1 -adaptation_sets "id=0,streams=a" ';
        } else {
            $ffmpegDashCmd .= '-c copy -f dash -use_timeline 1 -use_template 1 -adaptation_sets "id=0,streams=v id=1,streams=a" ';
        }

        $ffmpegDashCmd .= escapeshellarg($dashManifest) . ' 2>&1';

        $dashOutput = shell_exec($ffmpegDashCmd);
        log_message('error', "FFmpeg DASH Output: " . $dashOutput);

        if (! file_exists($dashManifest)) {
            log_message('error', "DASH manifest generation failed for {$fileBaseName}");
            return '';
        }

        return base_url(
            "uploads/{$username}/" . date("Y") . "/" . date("m") . "/{$fileBaseName}/dash/{$fileBaseName}.mpd"
        );
    }

    private function isAudioFile($filename)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, ['mp3','m4a','aac','wav','flac','ogg','wma']);
    }

    private function isVideoFile($filename)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, ['mp4','mov','avi','mkv','webm','flv','wmv']);
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
