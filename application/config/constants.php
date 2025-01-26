<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


/*
|--------------------------------------------------------------------------
| Eitaa Auto Auth Config
|--------------------------------------------------------------------------
*/

define('EITAA_TOKEN', '60930039:laKi6ig-Ml)Q8[?-EMpqNKn-UL(vPo}-dD7Xsx8-A%hpXLw-1WvuO4V-YxXSC9E-@v6g5mz-C5p*R7q-4KAfOhm-Oa~PsRi-oV9R^/T-eyunIYD-0%PfYWo-vHk{JF1-W1g7B,s-yYlYmIb-BZLs(V0-EUGJh');
define('SECRET_KEY', '626f6f726f2062616261206d616e20696e6f20616e6a616d206e656d6964616d');
// define('CDN_URL', 'https://hls.zipak.info?path=');
define('CDN_URL', 'https://hls.zipak.info/fetch_image/');

/*
|--------------------------------------------------------------------------
| CMS Config
|--------------------------------------------------------------------------
*/

define('PHPASS_HASH_STRENGTH', 8);     //hash setting
define('PHPASS_HASH_PORTABLE', FALSE); //hash setting

/*========= GROUPS  ===========*/
define('INDEX_ID', 1);

$POST_TYPES = 
array(
	'book' => array(
		's_name'  => 'کتاب',
		'g_name'  => 'کتابها',
		'icon'    => 'book',
		'support' => array('title','thumb','icon','excerpt','category','dl_box'),
        'single'  => FALSE,
        'meta'    => array(
            'price' => array(
                'name'        => 'قیمت (تومان)',
                'placeholder' => 'صفر یعنی رایگان',
                'type'        => 'number',
                'validation'  => 'trim|xss_clean|numeric|required',
                'class'       => 'OnlyNum',
            ),
            /*'discount' => array(
                'name'        => 'تخفیف (%)',
                'placeholder' => 'مقدار تخفیف به %',
                'type'        => 'text',
                'validation'  => 'trim|xss_clean|numeric',
                'class'       => 'OnlyNum',
            ),*/
            'startpage' => array(
                'name'        => 'شماره صفحه شروع کتاب',
                'placeholder' => 'شماره صفحه شروع کتاب',
                'type'        => 'text',
                'validation'  => 'trim|xss_clean|numeric',
                'class'       => 'OnlyNum',
            ),
            'author' => array(
                'name'        => 'عنوان',
                'placeholder' => '',
                'type'        => 'text',
                'validation'  => 'trim|xss_clean',
                'class'       => '',
            ),
            'finaltest' => array(
                'name'        => 'آزمون نهایی',
                'placeholder' => 'آزمون نهایی',
                'type'        => 'radio',
                'validation'  => 'trim|xss_clean|numeric',
            ),
            'timesecond' => array(
                'name'        => 'زمان هر سوال (ثانیه)',
                'placeholder' => 'زمان هر سوال (ثانیه)',
                'type'        => 'text',
                'validation'  => 'trim|xss_clean|numeric',
                'class'       => 'OnlyNum',
            ),
            'acceptpercent' => array(
                'name'        => 'درصد قبولی آزمون',
                'placeholder' => 'درصد قبولی آزمون',
                'type'        => 'text',
                'validation'  => 'trim|xss_clean|numeric',
                'class'       => 'OnlyNum',
            ),
            'isvideo' => array(
                'name'        => 'کتاب ویدئویی',
                'placeholder' => 'کتاب ویدئویی',
                'type'        => 'radio',
                'validation'  => 'trim|xss_clean|numeric',
            ),
            'allowpage' => array(
                'name'        => 'انتخاب صفحه',
                'placeholder' => 'انتخاب صفحه',
                'type'        => 'radio',
                'validation'  => 'trim|xss_clean|numeric',
            ),
            'allowbuy' => array(
                'name'        => 'قابل فروش است',
                'placeholder' => 'قابل فروش است',
                'type'        => 'radio',
                'validation'  => 'trim|xss_clean|numeric',
				'default'	  => 1
            ),
            'allowmembership' => array(
                'name'        => 'نیاز به عضویت',
                'placeholder' => 'نیاز به عضویت دارد',
                'type'        => 'radio',
                'validation'  => 'trim|xss_clean|numeric',
				'default'	  => 0
            ),
        ),
        'nashr'    => array(
            'publisher' => array(
                'name'        => 'ناشر',
                'type'        => 'dropdown',
                'validation'  => 'trim|xss_clean|numeric|required',
                'table'       => 'publisher'
            ),
            'writer' => array(
                'name'        => 'نویسنده',
                'type'        => 'multiselect',
                'validation'  => 'trim|xss_clean|numeric|required',
                'table'       => 'writer'
            ),
            'translator' => array(
                'name'        => 'مترجم',
                'type'        => 'multiselect',
                'validation'  => 'trim|xss_clean|numeric|required',
                'table'       => 'translator'
            ),
            'publishdate' => array(
                'name'        => 'تاریخ نشر',
                'type'        => 'calendar',
                'validation'  => 'trim|xss_clean|numeric|required'
            ),
            'localprice' => array(
                'name'        => 'قیمت چاپی(تومان)',
                'placeholder' => 'صفر یعنی رایگان',
                'type'        => 'number',
                'validation'  => 'trim|xss_clean|numeric|required',
                'class'       => 'OnlyNum',
            ),
            'pages' => array(
                'name'        => 'تعداد صفحه',
                'placeholder' => 'تعداد صفحه',
                'type'        => 'number',
                'validation'  => 'trim|xss_clean|numeric|required',
                'class'       => 'OnlyNum',
            ),
		)
	),

    'notification' => array(
        's_name'  => 'پیام',
        'g_name'  => 'پیام ها',
        'icon'    => 'bell',
        'support' => array('title','excerpt'),
        'single'  => FALSE
    ),

	/*'product' => array(
		's_name'  => 'محصول',
		'g_name'  => 'محصولات',
		'icon'    => 'cubes',
		'menu'    => array('orders'=>array('name'=>'سفارشات','icon'=>'reorder')),		
		'support' => array('title','editor','thumb','media','excerpt','category','tag','dl_box','product_settings','seo'),
	),
    'lp' => array(
        's_name'  => 'صفحه فرود',
        'g_name'  => 'صفحه فرود',
        'icon'    => 'plane',
        'support' => array('title','thumb','icon','excerpt'),
        'meta'    => array(
            'link' => array(
                'name'        => 'لینک',
                'placeholder' => 'لینک مورد نظر برای هدایت کاربر',
                'type'        => 'url',
                'validation'  => 'trim|xss_clean|required',
                'class'       => 'en',
            )
        ),
        'single'  => FALSE,
    ),*/
);


$file_types = 
array(
	'text_files'  =>array('doc','docx','log','msg','odt','pages','rtf','tex','txt','wpd','wps','pdf'),
	'data_files'  =>array('csv','dat','gbr','ged','key','keychain','pps','ppt','pptx','sdf','tar','tax2012','tax2014','vcf','xml'),
	'audio_files' =>array('aif','iff','m3u','m4a','mid','mp3','mpa','ra','wav','wma'),	
	'video_files' =>array('3g2','3gp','asf','asx','avi','flv','m4v','mov','mp4','mpg','rm','Real','srt','swf','vob','wmv'),	
	'image_files' =>array('bmp','gif','jpg','png','tif','tiff','jpe','jpeg'),	
	'code_files'  =>array('asp','aspx','cer','cfm','csr','css','htm','html','js','jsp','php','rss','xhtml','c','class','cpp','cs','dtd','fla','h','java','lua','m','pl','py'),	
	'zip_files'   =>array('7z','cbr','deb','gz','pkg','rar','rpm','sitx','zip','zipx'),	
	'font_files'  =>array('fnt','fon','otf','ttf'),	
	'set_files'   =>array('cfg','ini','prf','config'),	
);
?>