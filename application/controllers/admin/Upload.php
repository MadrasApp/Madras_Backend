<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Upload
 *
 * Handles file uploads and converts them (audio/video) to DASH format using FFmpeg.
 */
class Upload extends CI_Controller
{
    /**
     * @var array Stores site settings.
     */
    public $setting;

    /**
     * Upload constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Load necessary models and libraries
        $this->load->model('m_user', 'user');
        $this->load->model('admin/m_media', 'media');
        
        // Example: if you have a Settings model
        // $this->setting = $this->settings->data;
    }

    /**
     * Main method for handling file uploads and DASH conversion.
     *
     * @return void Outputs JSON response.
     */
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
            // 1) Create the target directory structure
            $directory = $this->createDirectoryStructure($dir, $fileBaseName);

            // 2) Move the uploaded file to final location
            $fullFilePath = $this->moveUploadedFile($fileTemp, $fileName, $directory);

            // 3) Convert file (audio/video) to DASH
            $dashManifestUrl = $this->convertToDash($fullFilePath, $fileBaseName, $directory, $dir);

            if (! empty($dashManifestUrl)) {
                $msg     = "File uploaded and converted to DASH format successfully.";
                $success = true;
            } else {
                $msg     = "File uploaded successfully, but DASH conversion failed.";
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
     * Serve the MPD (DASH Manifest) file if it exists.
     *
     * @param string $username
     * @param string $year
     * @param string $month
     * @param string $video_name
     *
     * @return void
     */
    public function serve_mpd($username, $year, $month, $video_name)
    {
        $mpdPath = FCPATH . "uploads/$username/$year/$month/$video_name/dash/$video_name.mpd";

        if (file_exists($mpdPath)) {
            header("Content-Type: application/dash+xml");
            readfile($mpdPath);
        } else {
            show_404();
        }
    }

    /**
     * Creates the directory structure for the uploaded file.
     *
     * Directory structure: uploads/<username>/<year>/<month>/<file_base_name>/
     *
     * @param string $dir           Username or overridden directory name.
     * @param string $fileBaseName  Base name of the file (without extension).
     *
     * @return string Final directory path.
     * @throws Exception If directory creation fails.
     */
    private function createDirectoryStructure($dir, $fileBaseName)
    {
        $dirArr = [
            'uploads',
            $dir,
            date("Y"),
            date("m"),
            $fileBaseName
        ];

        // Create directories if they don't exist
        $directory = $this->media->mkDirArray($dirArr);
        if (! $directory) {
            throw new Exception("Failed to create directory structure.");
        }

        return $directory;
    }

    /**
     * Moves the uploaded file to the given directory, ensuring the file name is unique.
     *
     * @param string $tmpPath    Temporary file path.
     * @param string $fileName   Original file name.
     * @param string $directory  Target directory path.
     *
     * @return string The full path to the moved file.
     * @throws Exception If the file move fails.
     */
    private function moveUploadedFile($tmpPath, $fileName, $directory)
    {
        // Make sure the file name is optimized/unique
        $targetFile = $directory . "/" . $fileName;
        $targetFile = $this->media->optimizedFileName($targetFile);

        if (! move_uploaded_file($tmpPath, $targetFile)) {
            throw new Exception("Failed to move uploaded file.");
        }

        return $targetFile;
    }

    /**
     * Converts the uploaded file (audio/video) to DASH format using ffmpeg.
     *
     * @param string $sourceFile     Full path to the source video or audio file.
     * @param string $fileBaseName   Base name of the file (no extension).
     * @param string $directory      The parent directory holding the new file.
     * @param string $username       The username/directory segment for generating URL.
     *
     * @return string The DASH manifest URL if successful, empty string otherwise.
     */
    private function convertToDash($sourceFile, $fileBaseName, $directory, $username)
    {
        // Create DASH output directory
        $dashDir = $directory . "/dash";
        if (! is_dir($dashDir)) {
            if (! mkdir($dashDir, 0777, true)) {
                log_message('error', 'Failed to create DASH directory.');
                return '';
            }
        }

        $isAudio = $this->isAudioFile($sourceFile);
        $isVideo = $this->isVideoFile($sourceFile);

        // If it's not recognized audio or video, skip
        if (! $isAudio && ! $isVideo) {
            log_message('error', 'File is neither recognized video nor audio: ' . $sourceFile);
            return '';
        }

        // We'll store each encoded track (audio or video) in this array
        $encodedFiles = [];

        // If it's a video, produce multiple resolutions
        if ($isVideo) {
            $resolutions = [
                '480p'  => ['scale' => '-vf scale=854:480',   'bitrate' => '800k'],
                '720p'  => ['scale' => '-vf scale=1280:720',  'bitrate' => '2000k'],
                '1080p' => ['scale' => '-vf scale=1920:1080', 'bitrate' => '4500k'],
            ];

            foreach ($resolutions as $label => $options) {
                $outputFile = $dashDir . "/" . $fileBaseName . "_{$label}.mp4";

                // For video encoding with audio:
                // - map 0:v => video track
                // - map 0:a? => optional audio track
                // - c:v libx264 => video codec
                // - c:a aac => audio codec
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
        }
        // If it's audio, produce multiple bitrates
        else if ($isAudio) {
            $audioBitrates = [
                '64k'  => '64k',
                '128k' => '128k',
                '256k' => '256k'
            ];

            foreach ($audioBitrates as $label => $bitrate) {
                // Encode to MP4 container (or M4A) with AAC
                $outputFile = $dashDir . "/" . $fileBaseName . "_{$label}.m4a";

                // -vn => no video track
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

        // If we didn't produce any output, fail
        if (empty($encodedFiles)) {
            return '';
        }

        // Build final .mpd from all encoded files
        $dashManifest    = $dashDir . "/" . $fileBaseName . ".mpd";
        $ffmpegDashCmd   = 'ffmpeg -y ';

        // Each encoded file is a new input
        // => -f mp4 -i encodedFile
        foreach ($encodedFiles as $encodedFile) {
            $ffmpegDashCmd .= '-f mp4 -i ' . escapeshellarg($encodedFile) . ' ';
        }

        // We must map each input with -map X
        // For example, if we have 3 encoded files => -map 0 -map 1 -map 2
        // This depends on how many resolutions/bitrates we produced
        for ($i = 0; $i < count($encodedFiles); $i++) {
            $ffmpegDashCmd .= '-map ' . $i . ' ';
        }

        // Decide adaptation sets based on audio/video
        // If audio only => "id=0,streams=a"
        // If video => "id=0,streams=v id=1,streams=a"
        if ($isAudio) {
            // Audio-only
            $ffmpegDashCmd .= '-c copy -f dash -use_timeline 1 -use_template 1 '
                              . '-adaptation_sets "id=0,streams=a" ';
        } else {
            // Video + audio
            $ffmpegDashCmd .= '-c copy -f dash -use_timeline 1 -use_template 1 '
                              . '-adaptation_sets "id=0,streams=v id=1,streams=a" ';
        }

        // Finish the command
        $ffmpegDashCmd .= escapeshellarg($dashManifest) . ' 2>&1';

        // Execute
        $dashOutput = shell_exec($ffmpegDashCmd);
        log_message('error', "FFmpeg DASH Output: " . $dashOutput);

        // Check if .mpd exists
        if (! file_exists($dashManifest)) {
            log_message('error', "DASH manifest generation failed for {$fileBaseName}");
            return '';
        }

        // Construct final MPD URL
        return base_url(
            "uploads/{$username}/" . date("Y") . "/" . date("m") . "/{$fileBaseName}/dash/{$fileBaseName}.mpd"
        );
    }

    /**
     * Check if the file is recognized as an audio file by extension.
     *
     * @param string $filename Path to the file.
     *
     * @return bool
     */
    private function isAudioFile($filename)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        // Extend this list with all audio extensions you support
        return in_array($ext, ['mp3','m4a','aac','wav','flac','ogg','wma']);
    }

    /**
     * Check if the file is recognized as a video file by extension.
     *
     * @param string $filename Path to the file.
     *
     * @return bool
     */
    private function isVideoFile($filename)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        // Extend this list with all video extensions you support
        return in_array($ext, ['mp4','mov','avi','mkv','webm','flv','wmv']);
    }

    /**
     * Sends a JSON response and terminates the request.
     *
     * @param bool   $success         Whether the operation was successful.
     * @param string $msg             A message indicating success/failure details.
     * @param string $dashManifestUrl The DASH manifest URL if available.
     *
     * @return void
     */
    private function sendResponse($success, $msg, $dashManifestUrl)
    {
        $response = [
            'files' => [
                'dash_manifest_url' => $dashManifestUrl,
                'msg'               => $msg,
                'action'            => $success ? 'done' : 'fail'
            ]
        ];

        // Output JSON response
        echo json_encode(
            $response,
            JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG
        );
    }
}
