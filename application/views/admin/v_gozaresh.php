<?php
/*
echo "<pre>";
print_r();
echo "</pre>";
die;
*/
global $nasherX;
$this->load->helper('inc');

$inc = new inc;
$nasherX = $nasher;
$cols = array(
    'ردیف' => array(
        'field_name' => 'counter',
        'th-attr' => 'style="width:40px;"',
		'td-attr' => 'style="padding:7px" align="center"',
		'link' => false,
		'function' => function($col,$row,$counter){
			return $counter+1;
		}
	),	
    'جزئیات' => array(
        'field_name' => 'factor_id',
        'th-attr' => 'style="width:40px;"',
		'td-attr' => 'style="padding:7px" align="center"',
		'link' => false,
		'function' => function($col,$row){
			return '<i class="fa fa-info-circle fa-2x text-info cu view-details" data-id="'.$col.'"></i>';
		}
	),	
    'شماره فاکتور' => array(
        'field_name' => 'factor_id',
        'th-attr' => 'style="width:100px;"',
		'td-attr' => 'style="padding:7px" align="center"',
	),
    'قیمت' => array(
        'field_name' => 'discount',
		'type'       => 'price',
        'th-attr' => 'style="width:40px;"',
		'td-attr' => 'style="padding:7px" align="center" data-price="[ID]" class="pricelist"',
	),	
    'کاربر' => array(
        'field_name' => 'author',
        'th-attr' => 'style="width:200px;"',
		'td-attr' => 'style="padding:7px" align="center"',
        'link' => false,
		'function' => function($col,$row){
			global $nasherX;
			$nasher = array();
			foreach($nasherX as $k=>$v){
				$nasher[$v->author] = array($v->username,"$v->username [ $v->displayname ]");
			}
			return '<a target="_blank" href="'.site_url('admin/users').'?username='.$nasher[$col][0].'">'.$nasher[$col][1].'</a>';
			
			//return '<b>' . $col . " </b> &nbsp; <i class='text-danger'>" . $row['name'] . ' ' . $row['family'] . "</i> &nbsp;";
		}
    ),
    'کتاب' => array(
        'field_name' => 'title',
        'td-attr' => 'style="text-align:right;"',
	),	
    'تاریخ پرداخت' => array(
		'field_name' => 'pdate',
		'link' => false,
		'type' => 'date',
		'td-attr' =>'align="center" style="width:120px"',
	)
);
?>
<center><h2>گزارش مالی</h2></center>
<?php
	$export = '<div class="form-group"><p>&nbsp;</p><button type="button" class="btn btn-success exportexcel"><span>خروجی اکسل</span> <i class="fa fa-file-excel-o"></i></button></div>';
	echo str_replace('<hr',$export.'<hr',$searchHtml);
	$footer = array(
		'جمع صفحه' => array(
			'field_name' => 'discount',
			'td-attr' => 'colspan="3"',
		),
		
	);

	$q =
		"SELECT d.*,f.cdate,f.pdate, p.author,p.title,0 AS `counter`
		FROM ci_factor_detail d
		INNER JOIN `ci_factors` `f` ON f.id = d.factor_id
		INNER JOIN `ci_posts` `p` ON p.id = d.book_id 
		$query";
	$inc->createTable($cols, $q , 'id="table" class="table light2" ', $tableName, $showall , $footer);
?>
	<form method="post" id="exportexcel" action="admin/api/gozareshXLSX/">
		<input type="hidden" name="query" value="<?php echo $query;?>" />
		<input type="hidden" name="ownerpercent" value="<?php echo $ownerpercent;?>" />
		<input type="hidden" name="backurl" value="<?php echo $_SERVER['QUERY_STRING'];?>" />
	</form>


<script type="text/javascript">

    $(document).ready(function () {

		$('select[name="parent_category"]').change(function(e) {
			$('select[name="category"]').val("");
			$('select[name="category"] option').remove();
			val = $(this).val();
			if(val.length){
				$.ajax({
					type: "POST",
					url: 'admin/api/LoadSubCategories/' + val,
					dataType: "json",
					success: function (data) {
						if(data.done){
							msg = data.msg;
							for(i=0;i<msg.length;i++){
								$('select[name="category"]').append('<option value="'+msg[i]['id']+'">'+msg[i]['name']+'</option>');
							}
						}
					}
				});
			} else {
				$('select[name="category"]').append('<option value="">بدون انتخاب</option>');
			}
		});
		
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