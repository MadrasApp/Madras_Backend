<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends CI_Controller {

    public $setting;

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_user','user');        
        $this->setting = $this->settings->data;            
    }

    public function index()
    {
        $key = FALSE; 
        $msg = "";
        $dashManifestUrl = "";

        if ($this->user->check_login()) {
            if (isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                $this->load->model('admin/m_media', 'media');

                $file_temp = $_FILES['file']['tmp_name'];
                $file_name = $_FILES['file']['name'];
                $file_base_name = pathinfo($file_name, PATHINFO_FILENAME); // File name without extension
                $dir = $this->user->data->username;

                $dir_post = $this->input->post('dir');

                if ($this->user->is_admin() && $dir_post)
                    $dir = $dir_post;

                // Directory structure: uploads/<username>/<year>/<month>/<video_name>/
                $dir_arr = array(
                    "uploads/",
                    $dir,
                    date("Y"),
                    date("m"),
                    $file_base_name
                );

                $directory = $this->media->mkDirArray($dir_arr);

                $file = $directory . "/" . $file_name;
                $file = $this->media->optimizedFileName($file);

                if (move_uploaded_file($file_temp, $file)) {
                    // Create DASH output directory
                    $dashDir = $directory . "/dash";
                    if (!is_dir($dashDir)) {
                        mkdir($dashDir, 0777, true);
                    }

                    // Define resolutions and bitrates
                    $resolutions = [
                        '480p' => ['scale' => '-vf scale=854:480', 'bitrate' => '800k'],
                        '720p' => ['scale' => '-vf scale=1280:720', 'bitrate' => '2000k'],
                        '1080p' => ['scale' => '-vf scale=1920:1080', 'bitrate' => '4500k']
                    ];

                    // Create command for each resolution
                    $ffmpegCommands = [];
                    foreach ($resolutions as $label => $options) {
                        $outputFile = $dashDir . "/" . $file_base_name . "_{$label}.mp4";
                        $ffmpegCommands[] = "ffmpeg -y -i " . escapeshellarg($file) . " "
                                          . $options['scale'] . " -map 0:v -c:v libx264 -preset fast -crf 23 -b:v " . $options['bitrate'] . " "
                                          . "-c:a aac -b:a 128k " . escapeshellarg($outputFile);
                    }

                    // Execute each encoding command
                    foreach ($ffmpegCommands as $command) {
                        $output = shell_exec($command . " 2>&1");
                        error_log("FFmpeg Output: " . $output);
                    }

                    // Generate DASH manifest from encoded files
                    $dashManifest = $dashDir . "/" . $file_base_name . ".mpd";
                    $encodedFiles = array_map(function ($label) use ($dashDir, $file_base_name) {
                        return $dashDir . "/" . $file_base_name . "_{$label}.mp4";
                    }, array_keys($resolutions));

                    $ffmpegDashCommand = "ffmpeg -y ";
                    foreach ($encodedFiles as $encodedFile) {
                        $ffmpegDashCommand .= "-f mp4 -i " . escapeshellarg($encodedFile) . " ";
                    }
                    $ffmpegDashCommand .= "-map 0 -map 1 -map 2 -c copy -f dash -use_timeline 1 -use_template 1 "
                                       . "-adaptation_sets \"id=0,streams=v id=1,streams=a\" "
                                       . escapeshellarg($dashManifest);

                    // Execute DASH manifest generation command
                    $dashOutput = shell_exec($ffmpegDashCommand . " 2>&1");
                    error_log("FFmpeg DASH Output: " . $dashOutput);

                    // Check if DASH manifest was created successfully
                    if (file_exists($dashManifest)) {
                        $dashManifestUrl = base_url("uploads/$dir/" . date("Y") . "/" . date("m") . "/" . $file_base_name . "/dash/$file_base_name.mpd");
                        $msg = "File uploaded and converted to DASH format successfully.";
                        $key = TRUE;
                    } else {
                        $msg = "File uploaded successfully, but DASH conversion failed.";
                        $key = FALSE;
                    }
                } else {
                    $msg = "Failed to move uploaded file.";
                    $key = FALSE;
                }
            } else {
                $msg = "No valid file was uploaded.";
            }
        } else {
            $msg = 'login-needed';
        }

        $done = $key ? 'done' : 'fail';
        $response = array(
            'files' => array(
                'dash_manifest_url' => $dashManifestUrl, // DASH manifest URL
                'msg' => $msg,
                'action' => $done,
            )
        );

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG);                
    }

    public function serve_mpd($username, $year, $month, $video_name) {
        $mpdPath = FCPATH . "uploads/$username/$year/$month/$video_name/dash/$video_name.mpd";

        if (file_exists($mpdPath)) {
            header("Content-Type: application/dash+xml");
            readfile($mpdPath);
        } else {
            show_404();
        }
    }
}
