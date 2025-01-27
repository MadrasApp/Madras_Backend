<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php 
if(isset($_GET['from']) && $_GET['from'] == 'miniapp'){
    $url = 'https://client.madras.app';
} else {
    $url = base_url('payment/go_to_app');
}
?>


<style>
        .container1 {
            max-width: 500px;
            width: 100%;
            padding: 20px;
            background: #fff;
            border-radius: 16px;
            box-shadow: rgba(0, 0, 0, 0.05) 0px 0px 0px 1px;
        }
        .row1 {
            display: flex;
            flex-direction: column;
            margin-top: 30px;
        }
        .alert1 {
            padding: 20px;
            background-color: #d803272f;
            color: #d42744;
            border: 1px solid #d803272f;
            border-radius: 4px;
            text-align: center;
        }
        .alert1 h3 {
            margin: 0;
            font-size: 20px;
        }
        .btn1 {
            transition: cubic-bezier(0.075, 0.82, 0.165, 1) 1s;
            display: inline-block;
            text-decoration: none;
            color: #fff;
            background-color: #00c896;
            padding: 15px;
            font-size: 18px;
            text-align: center;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }
        .btn1 i {
            margin-right: 8px;
        }
        .btn1:hover {
            background-color: #02daa4;
        }
    </style>

<div class="container1" style="max-width:500px">
	<div class="row1 mt-30">
			<div class="alert1 ">
				<h3><i class="fa fa-warning fa-lg ml-15"></i> <span><?php echo  $error ?></span> </h3> 
			</div>
			
			<a href="<?php echo $url ?>" class="btn1">
				<i class="fa fa-share"></i> <span> &nbsp; بازگشت به برنامه </span>
			</a>
	</div>
</div> 
