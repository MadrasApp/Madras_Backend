<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Media_upload extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('m_user', 'user');
        $this->load->model('admin/m_media', 'media');
    }

    public function upload() {
        // Set PHP configuration for large uploads
        ini_set('max_execution_time', 300); // 5 minutes
        ini_set('memory_limit', '1024M'); // 1GB
        
        // Check if user is logged in
        if (!$this->user->check_login()) {
            $response = [
                'files' => [
                    'action' => 'fail',
                    'msg' => 'Login required'
                ]
            ];
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }

        if (empty($_FILES['file'])) {
            $response = [
                'files' => [
                    'action' => 'fail',
                    'msg' => 'No file uploaded'
                ]
            ];
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }

        // Validate file type/size
        $allowed_types = [
            // Images
            'jpg', 'jpeg', 'png', 'gif', 'webp',
            // Audio formats
            'mp3', 'wav', 'ogg', 'aac', 'm4a', 'flac', 'wma',
            // Video formats
            'mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv', 'm4v', '3gp',
            // Documents
            'pdf', 'doc', 'docx', 'txt'
        ];
        $max_size = 1000 * 1024 * 1024; // 1000MB

        $file = $_FILES['file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_types)) {
            $response = [
                'files' => [
                    'action' => 'fail',
                    'msg' => 'Invalid file type'
                ]
            ];
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }
        if ($file['size'] > $max_size) {
            $response = [
                'files' => [
                    'action' => 'fail',
                    'msg' => 'File too large'
                ]
            ];
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }

        try {
            // Create directory structure
            $dir = $this->user->data->username;
            $fileBaseName = pathinfo($file['name'], PATHINFO_FILENAME);
            $directory = $this->createDirectoryStructure($dir, $fileBaseName);
            
            // Move uploaded file
            $fullFilePath = $this->moveUploadedFile($file['tmp_name'], $file['name'], $directory);
            
            // Return success response
            $response = [
                'files' => [
                    'action' => 'done',
                    'msg' => 'File uploaded successfully',
                    'url' => $fullFilePath,
                    'key' => $fullFilePath
                ]
            ];
            
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
                
        } catch (Exception $e) {
            $response = [
                'files' => [
                    'action' => 'fail',
                    'msg' => $e->getMessage()
                ]
            ];
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }
    }

    private function createDirectoryStructure($dir, $fileBaseName)
    {
        $dirArr = ['uploads', $dir, date("Y"), date("m"), $fileBaseName];
        $directory = $this->media->mkDirArray($dirArr);
        if (!$directory) {
            throw new Exception("Failed to create directory structure.");
        }
        return $directory;
    }

    private function moveUploadedFile($tmpPath, $fileName, $directory)
    {
        $targetFile = $directory . "/" . $fileName;
        $targetFile = $this->media->optimizedFileName($targetFile);

        if (!move_uploaded_file($tmpPath, $targetFile)) {
            throw new Exception("Failed to move uploaded file.");
        }

        return $targetFile;
    }
}