<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Aws\S3\S3Client;

class Media_upload extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Add authentication/authorization as needed
    }

    public function upload() {
        // Set PHP configuration for large uploads
        ini_set('max_execution_time', 300); // 5 minutes
        ini_set('memory_limit', '1024M'); // 1GB
        
        if (empty($_FILES['file'])) {
            return $this->output->set_status_header(400)->set_output(json_encode(['error' => 'No file uploaded']));
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
        $max_size = 1000 * 1024 * 1024; // 1000MB (increased from 100MB)

        $file = $_FILES['file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_types)) {
            return $this->output->set_status_header(400)->set_output(json_encode(['error' => 'Invalid file type']));
        }
        if ($file['size'] > $max_size) {
            return $this->output->set_status_header(400)->set_output(json_encode(['error' => 'File too large']));
        }

        // S3 config from environment variables
        require_once APPPATH . 'third_party/aws-autoloader.php';
        $s3Config = [
            'region'  => getenv('AWS_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key'    => getenv('AWS_ACCESS_KEY_ID'),
                'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
            ],
        ];
        // Add endpoint for Lexoya S3 or other S3-compatible services
        $endpoint = getenv('AWS_S3_ENDPOINT');
        if ($endpoint) {
            $s3Config['endpoint'] = $endpoint;
            $s3Config['use_path_style_endpoint'] = true;
        }
        $s3 = new S3Client($s3Config);
        $bucket = getenv('AWS_BUCKET');
        $key = 'madras/uploads/' . uniqid() . '_' . basename($file['name']);

        try {
            $result = $s3->putObject([
                'Bucket' => $bucket,
                'Key'    => $key,
                'SourceFile' => $file['tmp_name'],
                'ACL'    => 'public-read', // or your preferred ACL
            ]);
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['url' => $result['ObjectURL'], 'key' => $key]));
        } catch (Exception $e) {
            return $this->output->set_status_header(500)->set_output(json_encode(['error' => $e->getMessage()]));
        }
    }
} 