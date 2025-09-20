<?php defined('BASEPATH') OR exit('No direct script access allowed');

function closeTags( $html )
{
    preg_match_all ( "#<([a-z]+)( .*)?(?!/)>#iU", $html, $result );
    $openedtags = $result[1];

    preg_match_all ( "#</([a-z]+)>#iU", $html, $result );
    $closedtags = $result[1];
    $len_opened = count ( $openedtags );

    if( count ( $closedtags ) == $len_opened )
        return $html;

    $openedtags = array_reverse ( $openedtags );

    for( $i = 0; $i < $len_opened; $i++ )
    {
        if ( !in_array ( $openedtags[$i], $closedtags ) )
            $html .= "</" . $openedtags[$i] . ">";
        else
            unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
    }
    $html = str_replace('</br>', '',$html);
    return $html;
}

function byteToSize($bytes)
{
    $sizes = array ('Bytes', 'KB', 'MB', 'GB', 'TB');
    if ($bytes == 0) return '0 Bytes';
    $i = floor(log($bytes) / log(1024));
    return round($bytes / pow(1024, $i),2).' '.$sizes[$i];
}

function html($str = "")
{
    $str = htmlspecialchars($str,ENT_QUOTES,'UTF-8');
    $str = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $str);
    $str = nl2br($str);
    return $str;
}

function stripHTMLtags($str)
{
    $t = preg_replace('/<[^<|>]+?>/', '', htmlspecialchars_decode($str));
    $t = preg_replace("/&#?[a-z0-9]{2,8};/i", '', $t);
    //$t = htmlentities($t, ENT_QUOTES, "UTF-8");
    return $t;
}


function STU($str)
{
    //$str = preg_replace('/[^\\pL0-9]+/u', '-', $str);
    //$str = trim($str, "-");
    //$str = str_replace(' ','-',trim($str));
    $URL_F = array('-' ,'–' ,' ');
    $URL_R = array('،' ,'،' ,'-');
    $str = str_replace($URL_F,$URL_R,trim($str));
    $str = rawurlencode($str);
    return $str;
}

function UTS($str)
{
    $URL_F = array('-' ,'–' ,' ');
    $URL_R = array('،' ,'،' ,'-');
    $str = rawurldecode($str);
    $str = str_replace($URL_R,$URL_F,trim($str));
    return $str;
}

function thumb($file,$size = '150')
{
    $file_name = pathinfo($file,PATHINFO_FILENAME);
    $file_dir  = pathinfo($file,PATHINFO_DIRNAME);
    $file_ext  = pathinfo($file,PATHINFO_EXTENSION);

    return $file_name?"$file_dir/$file_name-$size.$file_ext":"";
}

function full_media_url($path)
{
    if (!$path) return null;
    // If already an absolute URL, return as-is
    if (preg_match('#^https?://#i', $path)) return $path;

    // Normalize leading slash for filesystem check
    $relativePath = ltrim($path, '/');

    // Prefer local file if it exists (legacy uploads)
    if (defined('FCPATH')) {
        $local = rtrim(FCPATH, '/\\') . '/' . $relativePath;
        if (file_exists($local)) {
            return base_url() . $relativePath;
        }
    }

    // Fallback to CDN base for new uploads
    if (defined('CDN_URL')) {
        return rtrim(CDN_URL, '/').'/'.$relativePath;
    }

    // Last resort, return as relative URL
    return $relativePath;
}

function m_int($str) {
    return preg_replace("/[^0-9]/","",$str);
}
