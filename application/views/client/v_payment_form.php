<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$RedirectURL = base_url() . 'payment/verify/section/' . $factor->section;
if(isset($_GET['from']) && $_GET['from'] == 'miniapp'){
    $RedirectURL .= '?from=miniapp';
}
?>
<div class="container">
	<div class="row mt-30">
		<div class="col-xs-12 mt-30">
			<form action="https://sep.shaparak.ir/payment.aspx" method="post">
				<input type="hidden" name="Amount" value="<?php echo  $factor->price*10; ?>" />
				<input type="hidden" name="ResNum" value="<?php echo  $factor->id; ?>">
				<input type="hidden" name="RedirectURL" value="<?php echo $RedirectURL; ?>"/>
				<input type="hidden" name="MID" value="<?php echo $config['saman_id']; ?>"/>
			</form>
			
			<div class="alert alert-info text-center">
				<h3><i class="fa fa-info-circle fa-lg ml-10"></i> <span>در حال انتقال به بانک، لطفا صبر کنید ...</span> </h3> 
			</div>
			
		</div>
	</div>
</div>
<script type="text/javascript">window.onload = formSubmit; function formSubmit() { document.forms[0].submit(); }</script>
