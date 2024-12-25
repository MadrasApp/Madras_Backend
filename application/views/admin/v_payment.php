<?php
$this->load->helper('inc');

$inc = new inc;

$cols = array(
    '  ' => array(
        'field_name' => 'id',
        'th-attr' => 'style="width:40px;"',
		'td-attr' => 'style="padding:7px" align="center"',
		'link' => false,
		'html' => '<i class="fa fa-info-circle fa-2x text-info cu view-details" data-id="[ID]"></i>'
	),	
    'شماره فاکتور' => array(
        'field_name' => 'id',
        'th-attr' => 'style="width:60px"',
		'td-attr' => 'align="center" class="en"',
	),
    'شماره رسید' => array(
        'field_name' => 'ref_id',
        'th-attr' => 'style="width:100px"',
		'td-attr' => 'align="center" class="en"',
	),
    'قیمت' => array(
        'field_name' => 'price',
		'type'       => 'price',
        'th-attr' => 'style="width:100px;"',
		'td-attr' => 'align="center" class="en"',
	),	
    'کاربر' => array(
        'field_name' => 'username',
        'link' => false,
		'function' => function($col,$row){
			
			return '<a target="_blank" href="'.site_url('admin/users').'?username='.$col.'">'.$col.'</a>';
			
			//return '<b>' . $col . " </b> &nbsp; <i class='text-danger'>" . $row['name'] . ' ' . $row['family'] . "</i> &nbsp;";
		}
    ),
    'تلفن' => array(
        'field_name' => 'tel',
        'link' => false,
		'function' => function($col,$row){
			return 
			
			"<i class='text-danger'>" . $col . "</i> &nbsp; " .

			'<a href="mailto:' . $row['email'] . '">' . $row['email'] . '</a>';
		}
    ),	
    'وضعیت' => array(
        'field_name' => 'status',
        'link'       => false,
		'td-attr'    => 'align="center" style="width:40px"',
		'function'   => function($col,$row){
			
			if($col == '') return '<i class="fa fa-spinner fa-lg text-muted" title="در انتظار"></i>';
			
			if($col == 0) return '<i title="پرداخت موفق" class="fa fa-check-circle-o fa-lg text-success hand" onclick="HandleMeCancel([ID]);"></i>';
			
			if($col == 2) return '<i class="fa fa-mail-reply fa-lg text-primary" title="برگشت خورده"></i>';
			if($col == NULL) return '<i class="fa fa-ban fa-lg text-danger" title="پرداخت ناموفق"></i>';
		}
    ),	
    'نتیجه' => array(
        'field_name' => 'state',
        'link' => false,
        'html' => '<div class="wb">[FLD]</div>',
        'td-attr' => 'align="center"'
    ),
    'تاریخ ایجاد'  => array('field_name' => 'cdate', 'link' => true, 'type' => 'strtime', 'td-attr' => 'align="center" style="width:120px"'),
    'تاریخ پرداخت' => array('field_name' => 'pdate', 'link' => true, 'type' => 'strtime', 'td-attr' => 'align="center" style="width:120px"'),
);



	$q =
		"SELECT f.*,ROUND(f.price*($ownerpercent/100)) price, u.id as uid, u.email , u.tel, u.name, u.family, u.username
		FROM ci_factors f
		LEFT JOIN `ci_users` `u` ON `u`.`id`=`f`.`user_id` 
		$query";
	$export = '<div class="form-group"><p>&nbsp;</p><button type="button" class="btn btn-success exportexcel"><span>خروجی اکسل</span> <i class="fa fa-file-excel-o"></i></button></div>';
	echo str_replace('<hr',$export.'<hr',$searchHtml);
	$footer = array(
		'جمع صفحه' => array(
			'field_name' => 'price',
			'td-attr' => 'colspan="3"',
		)
	);
	
	if($subtitle){ ?>
		<div class="text-center" style="margin-bottom:20px">
			<h2><?php echo $subtitle;?></h2>
		</div>
	<?php } ?>
	<?php $inc->createTable($cols, $q , 'id="table" class="table light2" ', $tableName, 60,$footer);?>
	<form method="post" id="exportexcel" action="admin/api/factorXLSX/">
		<input type="hidden" name="query" value="<?php echo $query;?>" />
		<input type="hidden" name="ownerpercent" value="<?php echo $ownerpercent;?>" />
		<input type="hidden" name="backurl" value="<?php echo $_SERVER['QUERY_STRING'];?>" />
	</form>

<script type="text/javascript">

    $(document).ready(function () {
		
		$(document).on('click','.view-details',function(){
			
			var id = $(this).attr('data-id');
			
			details(id);
		});
		$('.exportexcel').click(exportexcel);
    });
	
	function HandleMeCancel(id){
		if(confirm("آیا قصد دارید فاکتور انتخابی را لغو نمایید؟")){
			$.ajax({
				type: "POST",
				url: 'admin/api/factorCancel/' + id,
				data: {},
				dataType: "json",
				success: function (data) {
					if (data == "login")
					{
						login(function () {
							details(id)
						});
					}
					else
					{
						alert(data.msg);
						if(data.status == 0){
						 location.reload();
						}
					}
				},
				error: function (a,b,c) {
					notify('خطا در اتصال', 2);
				}
			});        
		}
	}

    function details(id)
    {
		var $html = $('<div/>',{'id':'view-details'}).append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);
		
		$.ajax({
			type: "POST",
			url: 'admin/api/factorDetails/' + id,
			data: {},
			dataType: "json",
			success: function (data) {
				if (data == "login")
				{
					login(function () {
						details(id)
					});
				}
				else
				{
					if(data.done)
							$html.html(data.html);
					else
						$html.html(get_alert(data));
				}
			},
			error: function (a,b,c) {
				notify('خطا در اتصال', 2);
			}
		});        
	}

    function exportexcel(){
		$('#exportexcel').submit();
    }
</script>
