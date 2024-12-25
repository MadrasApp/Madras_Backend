<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!doctype html>
<head>
	<meta charset="utf-8">
	<title><?php echo  isset($_title)? $config['title'] . " | " . $_title: $config['title'] ?></title>
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=yes" />
	<meta name="keywords" content="<?php echo  isset($meta_k)? $meta_k:$config['meta_key'] ?>">
	<meta name="description" content="<?php echo  isset($meta_d)? $meta_d:$config['meta_description'] ?>">
	<base href="<?php echo  base_url() ?>">
	
<?php
	
    $style[0]  = 'style/_master/css/bs/bootstrap.css';
	$style[1]  = 'style/_master/css/bs-rtl/bootstrap-rtl.css';
	$style[2]  = 'style/_master/font-awesome/css/font-awesome.css';
	$style[3]  = 'style/_master/font.css';
	$style[21] = 'style/_master/css/client/style.css';

	if(isset($css))
	{
        foreach ($css as $ck=>$cv)
        {
            unset($css[$ck]);
            $ck += 3;
            $css[$ck] = $cv;
        }
		$style = ($style+$css);
        ksort($style);
	}
	

	
	foreach ($style as $c)
    {
		echo '	<link rel="stylesheet" type="text/css" href="'.base_url() . $c . '">'."\r";
    }
	
    /*$i=0;
	echo '	<link rel="stylesheet" type="text/css" href="'.site_url('Includes/Css').'/';
    foreach ($style as $c)
    {
        $c = str_replace('/', ':',$c);
        echo $c . (($i<count($style)-1) ? '::':'');
        $i++;
    }
    echo '">'."\r";*/

	$js_script[] = 'js/jquery.min.js';
	$js_script[] = 'js/_client/bootstrap.min.js';
	$js_script[] = 'js/_client/public.js'; 

	if(isset($script))
	{
        foreach ($script as $sk=>$sv)
        {
            unset($script[$sk]);
            $sk += count($js_script) +1;
            $script[$sk] = $sv;
        }
        $js_script = $js_script + $script;
        ksort($js_script);
	}
    $this->session->set_userdata('js_files',$js_script);

    //echo '	<script type="text/javascript" src="'.site_url('Includes/js').'/'. implode('-',array_keys($js_script)).'"></script>'."\r";
	
	
	foreach ($js_script as $js)
    {
		echo '	<script type="text/javascript" src="'.base_url(). $js .'"></script>'."\r";
    }

?>
	<style>
		body{
			padding-top:0;
		}
		img.logo{
			max-height: 150px;
			margin-top:10px;
		} 
		@media (max-width:767px){
			img.logo{
				max-height: 120px;
			} 				
		}
		header{
			line-height: 67px;
			background-color: #2C2929;
			color: #fff;
			text-align: center;
			border-bottom: solid 2px #999;
		}
		header h1{
			font-size:16px;
			margin:0;
			line-height:inherit;
		}
	</style>
</head>
<body>

	<header>
		<h1><?php echo  $config['title'] ?></h1>
	</header>
	
	<div class="text-center">
		<p><img src="<?php echo  base_url() . $config['site_logo'] ?>" alt="<?php echo  $config['title'] ?>" class="logo"></p>
	</div>