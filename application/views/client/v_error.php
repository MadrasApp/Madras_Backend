<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container" style="max-width:500px">
	<div class="row mt-30">
		<div class="col-xs-12 mt-30 mb-30">
			<div class="alert alert-danger text-center">
				<h3><i class="fa fa-warning fa-lg ml-15"></i> <span><?php echo  $error ?></span> </h3> 
			</div>
			
			<a href="<?php echo  base_url('payment/go_to_app') ?>" class="btn btn-lg btn-primary btn-block">
				<i class="fa fa-share"></i> <span> &nbsp; بازگشت به برنامه </span>
			</a>
		</div>
	</div>
</div> 