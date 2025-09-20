<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_media extends CI_Model
{

    public $setting;
    public $data;

    function __construct()
    {
        parent::__construct();
        $this->setting = $this->settings->data;
        $this->load->helper(array('file'));
    }

    public function scanDir($full_path = "", $have_thumb = FALSE)
    {
        $files = is_dir($full_path) ? scandir($full_path) : array();//Alireza Balvardi

        foreach ($files as $file) {
            if (is_dir($full_path . "/" . $file)) {
                if ($file != '..' && $file != '.')
                    $this->scanDir($full_path . "/" . $file);
            } else {
                $full = $full_path . "/" . $file;
                $enc = mb_detect_encoding($full);
                $full = iconv("CP1256", "$enc", $full);

                if (!$have_thumb && $this->isThumb($full)) {
                    continue;
                }
                $this->data['files'][] = $full;
            }
        }
    }

    public function scanPrimaryDir($full_path = "")
    {
        if (!is_dir($full_path)) return array();
        $files = @scandir($full_path);
        $folders = array();
        $return = array();

        if ($files)
            foreach ($files as $file) {
                $full = $full_path . "/" . $file;
                if (is_dir($full)) {
                    if ($file != '.' && $file != '..' && !$this->isDirEmpty($full))
                        $folders[] = (string)$file;
                }
            }
        natcasesort($folders);

        return array_values($folders);
    }

    public function isDirEmpty($dir)
    {
        if (!is_readable($dir)) return NULL;
        return (count(scandir($dir)) == 2);
    }


    public function Sort($sort = 'time', $type = 'desc')
    {
        $files = @$this->data['files'];
        $type = strtolower($type);

        switch ($sort) {
            case 'time':
            case 'date':

                if (!empty($files)) {
                    usort($files, function ($a, $b) {
                        global $type;
                        $result = $type == 'desc' ? @filemtime($a) > @filemtime($b) : @filemtime($a) < @filemtime($b);
                        $result = $result?1:-1;
                        return $result;
                    });
                }

                break;
            case 'size':

                if (!empty($files))
                    usort($files, function ($a, $b) {
                        global $type;
                        $result = $type == 'desc' ? @filesize($a) > @filesize($b) : @filesize($a) < @filesize($b);
                        $result = $result?1:-1;
                        return $result;
                    });

                break;
            case 'type':

                if (!empty($files))
                    usort($files, function ($a, $b) {

                        function a($s)
                        {
                            return pathinfo($s, PATHINFO_EXTENSION);
                        }

                        global $type;
                        $result = $type == 'desc' ? a($a) > a($a) : a($a) < a($a);
                        $result = $result?1:-1;
                        return $result;
                    });

                break;
        }
        $this->data['files'] = $files;
    }


    public function addInfo()
    {
        $files = $this->data['files'];
        $new_files = array();

        if (empty($files)) return;

        foreach ($files as $file) {
            $file_time = @filemtime($file);
            $file_name = pathinfo($file, PATHINFO_FILENAME);
            $file_type = pathinfo($file, PATHINFO_EXTENSION);
            $file_bytes = @filesize($file);

            $new_files[] =
                array(
                    'file' => $file,
                    'encode' => urlencode($file),
                    'time' => $file_time,
                    'type' => $file_type,
                    'name' => $file_name,
                    'size' => $file_bytes,
                    'mb_encod' => mb_detect_encoding($file),
                    'thumb150' => $this->getThumb($file, '150'),
                    'thumb300' => $this->getThumb($file, '300'),
                    'thumb600' => $this->getThumb($file, '600'),
                );
        }
        $this->data['files'] = $new_files;
    }

    public function setLimit($begin = 0, $total = 'all')
    {

        $files = $this->data['files'];

        if (empty($files)) return;

        $c = 0;
        $count = count($files);

        if ($total == 'all') $total = $count;

        $total += $begin;

        if ($total > $count) $total = $count;

        foreach ($files as $key => $value) {
            if ($c < $begin or $c >= $total)
                unset($files[$key]);
            $c++;
        }
        $this->data['files'] = $files;
    }

    public function filter($include = array(), $exclude = array())
    {
        $files = @$this->data['files'];

        if (empty($files)) return;

        if (!is_array($exclude)) $exclude = $exclude == '' ? array() : array($exclude);
        if (!is_array($include)) $include = $include == '' ? array() : array($include);

        foreach ($files as $key => $value) {
            $ext = strtolower(pathinfo($files[$key], PATHINFO_EXTENSION));

            if (count($include) > 0 && !in_array($ext, $include))
                unset($files[$key]);

            if (count($exclude) > 0 && in_array($ext, $exclude))
                unset($files[$key]);

        }

        $this->data['files'] = $files;
    }

    public function changeName($file, $name)
    {
        $file_dir = pathinfo($file, PATHINFO_DIRNAME);
        $file_name = pathinfo($file, PATHINFO_FILENAME);
        $file_ext = pathinfo($file, PATHINFO_EXTENSION);

        return $file_dir . "/" . $name . "." . $file_ext;
    }

    public function Rename($file, $new_name)
    {
        $new_name = $this->changeName($file, $new_name);
        if (@rename($file, $new_name))
            return TRUE;
        return FALSE;
    }

    public function checkExists($file)
    {
        $i = 2;
        $new_name = $file_name = pathinfo($file, PATHINFO_FILENAME);

        while (file_exists($file)) {
            $new_name = $file_name . "($i)";
            $file = $this->changeName($file, $new_name);
            $i++;
        }
        return array($new_name, $file);
    }

    public function autoName($file)
    {
        $new_name = $file_name = date("Y-m-d[H.i]");
        $i = 2;
        $file = $this->changeName($file, $file_name);

        while (file_exists($file)) {
            $new_name = $file_name . "($i)";
            $file = $this->changeName($file, $new_name);
            $i++;
        }
        return array($new_name, $file);
    }

    public function fixName($file)
    {
        if (!$this->isAscii($file))
            return $this->autoName($file);

        $file = mb_ereg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $file);
        $file = mb_ereg_replace("([\.]{2,})", '', $file);
        return $file;
    }

    public function isAscii($file)
    {
        return @mb_detect_encoding($file) == 'ASCII';
    }

    public function getThumb($file, $size = '150')
    {
        $file_name = pathinfo($file, PATHINFO_FILENAME);
        return $this->changeName($file, $file_name . '-' . $size);
    }

    public function isThumb($file)
    {
        $file_name = pathinfo($file, PATHINFO_FILENAME);

        $thumbs = array('-150', '-300', '-600');
        $last = substr($file_name, -4);

        if (in_array($last, $thumbs)) {
            $file_name = str_replace($last, '', $file_name);
            $base_file = $this->changeName($file, $file_name);

            if (file_exists($base_file))
                return TRUE;
        }
        return FALSE;
    }

    public function isImage($file)
    {
        /*$fh = fopen($file,'rb');
        if ($fh)
        {
            $bytes = fread($fh, 6);
            fclose($fh);

            if ($bytes === false)
            return FALSE;

            if (substr($bytes,0,3) == "\xff\xd8\xff") //'image/jpeg';
            return TRUE;

            if ($bytes == "\x89PNG\x0d\x0a") //'image/png';
            return TRUE;

            if ($bytes == "GIF87a" OR $bytes == "GIF89a") //'image/gif';
            return TRUE;

        }
        return FALSE;*/

        $a = @getimagesize($file);

        if (is_array($a) && isset($a[2]))
            if (in_array($a[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG))) {
                return true;
            }
        return false;

    }

    function mkDir($path, $mode = 0777, $recursive = true)
    {
        return @mkdir($path, $mode, $recursive) ? TRUE : FALSE;
    }

    function mkDirArray($dir_arr)
    {
        $directory = "";
        $i = 0;
        foreach ($dir_arr as $k => $v) {

            if ($i > 0) $directory .= "/";
            $directory .= $v;

            if (!file_exists($directory))
                $this->mkDir($directory);
            $i++;
        }
        return $directory;
    }

    public function creatThumb($file)
    {
        if (!file_exists($file) || !filesize($file))
            return;
        $this->load->helper(array('inc'));

        $thumb_s = $this->getThumb($file, '150');
        $thumb_m = $this->getThumb($file, '300');
        $thumb_l = $this->getThumb($file, '600');

        $SimpleImage = new SimpleImage();
        $SimpleImage->load($file);

        $w = $SimpleImage->getWidth();
        $h = $SimpleImage->getHeight();

        if ($w > $h) {
            $SimpleImage->resizeToWidth(600);
            $SimpleImage->save($thumb_l);

            $SimpleImage->resizeToWidth(300);
            $SimpleImage->save($thumb_m);
        } else {
            $SimpleImage->resizeToHeight(600);
            $SimpleImage->save($thumb_l);

            $SimpleImage->resizeToHeight(300);
            $SimpleImage->save($thumb_m);
        }
        $SimpleImage->crop(150, 150);
        $SimpleImage->save($thumb_s);
    }

    /**
     * @param $file
     *      the image path
     * @return null
     */

    public function optimizeImage($file)
    {
        if (!file_exists($file)) return NULL;

        $this->load->helper(array('inc'));
        $resized = FALSE;

        $SimpleImage = new SimpleImage();
        $SimpleImage->load($file);

        if ($SimpleImage->getType() == IMAGETYPE_PNG)
            return NULL;

        $w = $SimpleImage->getWidth();

        if ($w > 2000) {
            $SimpleImage->resizeToWidth(2000);
            $SimpleImage->save($file);
            $SimpleImage->load($file);
            $resized = TRUE;
        }

        $h = $SimpleImage->getHeight();

        if ($h > 2000) {
            $SimpleImage->resizeToHeight(2000);
            $SimpleImage->save($file);
            $resized = TRUE;
        }

        if (!$resized) {
            $size = filesize($file);

            if ($size > 1 * 1024 * 1024) {
                $newWidth = $w >= 1920 ? 1920 : $w;
                $SimpleImage->resizeToWidth($newWidth);
                $SimpleImage->save($file);
            }
        }
    }

    public function optimizedFileName($file = '')
    {
        if (!$this->isAscii($file)) {
            $file = $this->autoName($file);
            $file = $file[1];
        }

        $file_name = pathinfo($file, PATHINFO_FILENAME);

        $file_name = $this->normalizeString($file_name);

        if (strlen($file_name) > 50)
            $file_name = substr($file_name, 0, 50) . '[...]';

        $file = $this->changeName($file, $file_name);

        $file = $this->checkExists($file);

        return $file[1];
    }

    public function normalizeString($str = '')
    {
        $str = strip_tags($str);
        $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
        $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
        $str = strtolower($str);
        $str = html_entity_decode($str, ENT_QUOTES, "utf-8");
        $str = htmlentities($str, ENT_QUOTES, "utf-8");
        $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
        $str = str_replace(' ', '-', $str);
        $str = rawurlencode($str);
        $str = str_replace('%', '-', $str);
        return $str;
    }

    public function getFileIcon($file)
    {
        $ext = strtolower(@pathinfo($file, PATHINFO_EXTENSION));
        global $file_types;

        $icon = "";
        $cls = "";
        $ic = "";


        if ($ext == 'pdf') //Document PDF
        {
            $cls = "fa-file-pdf-o";
        } elseif ($ext == 'xls' or $ext == 'xlsx') //Microsoft Excel
        {
            $cls = "fa-file-excel-o";
        } elseif ($ext == 'doc' or $ext == 'docx') //Microsoft Word Document
        {
            $cls = "fa-file-word-o";
        } elseif ($ext == 'pps' or $ext == 'ppt' or $ext == 'pptx') //Microsoft	PowerPoint
        {
            $cls = "fa-file-powerpoint-o";
        } else {
            if (in_array($ext, $file_types['text_files'])) {
                $cls = "fa-file-text-o";
            } elseif (in_array($ext, $file_types['audio_files'])) {
                $cls = "fa-file-audio-o";
            } elseif (in_array($ext, $file_types['video_files'])) {
                $cls = "fa-file-video-o";
            } elseif (in_array($ext, $file_types['image_files'])) {
                $cls = "fa-file-picture-o";
            } elseif (in_array($ext, $file_types['code_files'])) {
                $cls = "fa-file-code-o";
            } elseif (in_array($ext, $file_types['zip_files'])) {
                $cls = "fa-file-archive-o";
            } elseif (in_array($ext, $file_types['set_files'])) {
                $ic = "fa-gear";
            }
        }

        if ($cls != "") {
            $icon = "<span class=\"fa-stack\">\n\t\t\t\t\t\t\t\t  <i class=\"fa $cls fa-stack-2x\"></i>\n\t\t\t\t\t\t\t\t</span>";

        } elseif ($ic != "") {
            $icon = "<span class=\"fa-stack\">\n\t\t\t\t\t\t\t\t  <i class=\"fa fa-gear fa-stack-1x\"></i>\n\t\t\t\t\t\t\t\t  <i class=\"fa fa-file-o fa-stack-2x\"></i>\n\t\t\t\t\t\t\t\t</span>";
        } else {
            $len = strlen($ext);
            $font_size = ((10 - $len) / 10) + (($len - 3) / 100);
            $font_size .= "em";
            $ext = strtoupper($ext);

            $icon = "<span class=\"fa-stack\">\n\t\t\t\t\t\t\t\t  <span class=\"fa-stack-1x filetype-text\" style=\"font-size:$font_size\">$ext</span>\n\t\t\t\t\t\t\t\t  <i class=\"fa fa-file-o fa-stack-2x\"></i>\n\t\t\t\t\t\t\t\t</span>";
        }

        return $icon;

    }//=== end func getIcon


    public function getTemplateFile($info, $type = 'images', $class = "", $attr = "", $op = TRUE)
    {
        $file = $info['file'];

        $cls = "media-item cell media-$type $class ";
        $cls .= $this->fileCanInsert($file) ? " can-insert " : "";

        $time = $this->settings->Date($info['time'], 'array');

        // Normalize to web-relative paths under uploads/
        $fc = rtrim(str_replace('\\','/', FCPATH), '/').'/';
        $fileRel = ltrim(str_replace($fc, '', str_replace('\\','/', $file)), '/');

        $data = array(
            'file' => $fileRel,
            'name' => $info['name'],
            'size' => $this->byteToSize($info['size']),
            'type' => $info['type'],
            'date' => implode('&', $time),
        );

        if ($type == 'images') {
            // Convert thumbs to relative as well
            $t150 = str_replace('\\','/', $info['thumb150']);
            $t300 = str_replace('\\','/', $info['thumb300']);
            $t600 = str_replace('\\','/', $info['thumb600']);
            $data['thumb150'] = ltrim(str_replace($fc, '', $t150), '/');
            $data['thumb300'] = ltrim(str_replace($fc, '', $t300), '/');
            $data['thumb600'] = ltrim(str_replace($fc, '', $t600), '/');
            $data['thumb150size'] = "150x150";
            $data['thumb300size'] = $this->imageSize($info['thumb300']);
            $data['thumb600size'] = $this->imageSize($info['thumb600']);
            $data['selfsize'] = $this->imageSize($file);
        }

        $result = "<div class=\"$cls\"  $attr ";

        foreach ($data as $key => $value) {
            $result .= " data-$key=\"$value\" ";
        }

        $result .= '>   
            <div class="media-item-name">' . $info['name'] . '</div>
			<div class="media-item-icon">';

        if ($type == 'images') {
            $thumb = $info['thumb150'];
            if (!file_exists($thumb)) $thumb = $info['file'];
            $fc = rtrim(str_replace('\\','/', FCPATH), '/').'/';
            $rel = ltrim(str_replace($fc, '', str_replace('\\','/', $thumb)), '/');
            $result .= '<img src="' . base_url($rel) . '">';

            $footer_info = $this->imageSize($file);
        } else {
            $result .= $this->getFileIcon($file);
            $footer_info = strtoupper($info['type']);
        }

        $result .=
            '</div>
			<div class="media-item-footer">
				 <div class="media-item-ext opacity">' . $footer_info . '</div>
				 <div class="media-item-size opacity">' . $this->byteToSize($info['size']) . '</div>
            </div>';


        if ($op) {
            $result .=
                '<i class="fa fa-gears media-item-option-btn"></i>
				 <div class="media-item-options"><i class="fa fa-eye" op=view></i>';
            if ($this->user->can('delete_file'))
                $result .= '<i class="fa fa-times" op=delete></i>';
            $result .= '<i class="fa fa-pencil" op=edit></i></div>';
        }

        $result .= '</div>';

        return $result;

    }

    public function deleteFile($file)
    {
        if (file_exists($file)) {
            if ($this->isImage($file)) {
                @unlink($this->getThumb($file, '150'));
                @unlink($this->getThumb($file, '300'));
                @unlink($this->getThumb($file, '600'));
            }
            if (@unlink($file)) {
                // attempt to remove parent directory if empty (photo-named folder)
                $parent = pathinfo($file, PATHINFO_DIRNAME);
                if (is_dir($parent)) {
                    $items = @scandir($parent);
                    if (is_array($items) && count(array_diff($items, array('.', '..'))) === 0) {
                        @rmdir($parent);
                    }
                }
                return TRUE;
            }
        }
        return FALSE;
    }

    public function imageSize($file, $return = "string")
    {
        // On Windows with Persian paths, try CP1256 fallback if UTF-8 fails
        $image = @getimagesize($file);
        if ($image === false && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $cp1256 = @iconv('UTF-8', 'CP1256//TRANSLIT', $file);
            if ($cp1256 && file_exists($cp1256)) {
                $image = @getimagesize($cp1256);
                if ($image !== false) {
                    $file = $cp1256;
                }
            }
        }
        if (is_array($image) && isset($image[1]))
            return $return == "string" ? $image[0] . "x" . $image[1] : array($image[0], $image[1]);
        return "";
    }

    public function fileCanInsert($file)
    {
        $allowed_files = array('jpe', 'jpg', 'jpeg', 'png', 'gif', 'mp3', 'mp4', 'flv');

        $ext = strtolower(@pathinfo($file, PATHINFO_EXTENSION));

        return in_array($ext, $allowed_files);

    }

    public function base64ToImg($base64_image_string, $output_file_without_extentnion, $path_with_end_slash = "")
    {
        $splited = explode(',', substr($base64_image_string, 5), 2);
        $mime = $splited[0];
        $data = $splited[1];

        $mime_split_without_base64 = explode(';', $mime, 2);
        $mime_split = explode('/', $mime_split_without_base64[0], 2);
        if (count($mime_split) == 2) {
            $extension = $mime_split[1];
            if ($extension == 'jpeg') $extension = 'jpg';
            $output_file_with_extentnion = $output_file_without_extentnion . '.' . $extension;
        }

        $file = $path_with_end_slash . $output_file_with_extentnion;

        $ifp = fopen($file, "w");
        fwrite($ifp, base64_decode($data));
        fclose($ifp);

        return $output_file_with_extentnion;
    }

    public function byteToSize($bytes)
    {
        $sizes = array('Bytes', 'KB', 'MB', 'GB', 'TB');
        if ($bytes == 0) return '0 Bytes';
        $i = floor(log($bytes) / log(1024));
        return round($bytes / pow(1024, $i)) . ' ' . $sizes[$i];
    }

    public function Pre($data, $die = 1)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        if ($die) {
            die();
        }
    }

}//=== end media model