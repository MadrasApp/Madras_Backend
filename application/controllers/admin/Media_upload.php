<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Aws\S3\S3Client;

class Media_upload extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Add authentication/authorization as needed
    }

    public function upload() {
        if (empty($_FILES['file'])) {
            return $this->output->set_status_header(400)->set_output(json_encode(['error' => 'No file uploaded']));
        }

        // Validate file type/size
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'pdf'];
        $max_size = 100 * 1024 * 1024; // 100MB

        $file = $_FILES['file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_types)) {
            return $this->output->set_status_header(400)->set_output(json_encode(['error' => 'Invalid file type']));
        }
        if ($file['size'] > $max_size) {
            return $this->output->set_status_header(400)->set_output(json_encode(['error' => 'File too large']));
        }

        // S3 config from environment variables
        require_once FCPATH . 'vendor/autoload.php';
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