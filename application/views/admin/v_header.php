<!doctype html>
<html>
<head>
<meta charset="utf-8">
<base href="<?php echo  base_url() ?>">
<title><?php echo $title.@$_title ?></title>
<meta name="robots" content="noindex,nofollow">
<meta name="googlebot" content="noindex,nofollow">
<?php if( isset($favicon) ):  ?>
<link rel="icon" href="<?php echo base_url() . $favicon ?>">
<?php endif;  ?>
<?php

$style = array(
	'_master/font.css',
	'_master/css/_admin/bootstrap.css',
	'_master/css/_admin/bootstrap-rtl.css',
	'_master/css/_admin/public.css',
	'_master/css/_admin/admin.css',
	'_master/css/_admin/theme.css',
	'_master/font-awesome/css/font-awesome.min.css',
	'scroll/jquery.mCustomScrollbar.min.css',
    'ui.1.12.1/jquery-ui.css'//Alireza Balvardi
);
	if(isset($css))
	{
		$style = array_merge($css,$style);
	}

	foreach ($style as $c)
	echo '<link type="text/css" rel="stylesheet" href="'.base_url().'style/'.$c.'">'."\r";

$js_script = array(
	'jquery.min.js',	
	'jquery-ui.js',
	//'_client/bootstrap.min.js',
	'_admin/public.js',
	'_admin/admin.js',
	'_admin/media.js',
	'mCustomScrollbar.min.js',	
	'jquery.sticky-kit.min.js',	
	'jQuery-Uploader/jquery.ui.widget.js',
	'jQuery-Uploader/jquery.iframe-transport.js',
	'jQuery-Uploader/jquery.fileupload.js',
	'jQuery-Uploader/jquery.fileupload-process.js',
	'jQuery-Uploader/load-image.all.min.js',
	'jQuery-Uploader/canvas-to-blob.min.js',
	'jQuery-Uploader/bootstrap.min.js',
	'jQuery-Uploader/jquery.fileupload-image.js',
	'jQuery-Uploader/jquery.fileupload-audio.js',
	'jQuery-Uploader/jquery.fileupload-video.js',
	'jQuery-Uploader/jquery.fileupload-validate.js',
    'jquery.ui.datepicker-cc.all.min.js'
);
	if(isset($script))
	{
        $js_script = array_merge($script,$js_script);
	}
	
	foreach ($js_script as $js)
	echo '<script type="text/javascript" src="'.base_url().'js/'.$js.'"></script>'."\r";

?>
    <script type="text/javascript">var AURL = '<?php echo  base_url('admin/api') ?>' </script>
</head>
<body>

<div class="container-fluid">
    <div class="row">