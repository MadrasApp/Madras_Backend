<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php 
if(isset($_GET['from']) && $_GET['from'] == 'miniapp'){
    $url = 'https://client.madras.app';
} else {
    $url = base_url('payment/go_to_app');
}
?>
<div class="container" style="max-width:500px">
	<div class="row mb-30">
		<div class="col-xs-12 mt-10 mb-30 pb-30">
		
			<div class="alert alert-success text-center">
				<h3><i class="fa fa-warning fa-lg ml-15"></i> <span>پرداخت با موفیت انجام شد</span> </h3> 
			</div>
			
			<table class="table table-bordered table-striped table-hover">
				<tr>
					<td>شماره فاکتور</td>
					<td><?php echo  $factor->id ?></td>
				</tr>
				<tr>
					<td>مبلغ پرداخت شده</td>
					<td><?php echo  number_format($factor->price) ?> تومان</td>
				</tr>
				<tr>
					<td>قیمت بدون تخفیف</td>
					<td><?php echo  number_format($factor->cprice) ?> تومان</td>
				</tr>				
				<tr>
					<td>تاریخ ایجاد</td>
					<td><?php echo  jdate('d F y - H:i',$factor->cdate) ?></td>
				</tr>
				<tr>
					<td>تاریخ پرداخت</td>
					<td><?php echo  jdate('d F y - H:i') ?></td>
				</tr>				
			</table>
		
			<a href="<?php echo $url ?>" class="btn btn-lg btn-primary btn-block">
				<i class="fa fa-share"></i> <span> &nbsp; بازگشت به برنامه </span>
			</a>
			
		</div>
	</div>
</div> 