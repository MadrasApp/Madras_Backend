<?php
/**
echo "<pre>";
print_r();
echo "</pre>";
 * Created by Talkhabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 12:05 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('inc');
global $supplierX,$nezamX,$tecatX,$classcountX,$offer;
$supplierX=$supplier;
$tecatX=$tecat;
$nezamX=$nezam;
$classcountX = $classcount;
$inc = new inc;
$offer = array(0=>'عادی',1=>'متوسط',2=>'خوب',3=>'عالی',4=>'ممتاز',5=>'ویژه');

$cols['شماره'] =
    array(
        'field_name' => 'id',
		'th-attr' => 'style="width:50px"',
        'link' => true
    );
$cols['وضعیت'] =
	array(
		'field_name'=>'published',
		'function'=>function($col,$row)
		{

			$published =
				$row['published'] == 1 ?
					'<i class="fa fa-check-circle fa-lg text-success" title="تایید شده"></i>' :
					'<i class="fa fa-times-circle fa-lg text-danger" title="تایید نشده"></i>';
			return $published;
		},
		'td-attr'=>'align="center" style="width:70px;"'
	);
$cols['اسم دوره'] =
    array(
        'field_name' => 'tecatid',
        'link' => false,
		'function'=>function($col,$row)
		{
			global $supplierX,$tecatX;
			return @$tecatX[$row["tecatid"]];
		}
    );
$cols['نظام'] =
    array(
        'field_name' => 'nezamid',
        'link' => false,
		'function'=>function($col,$row)
		{
			global $supplierX,$nezamX;
			return @$nezamX[$row["nezamid"]];
		}
    );
$cols['سال تحصیلی'] =
    array(
        'field_name' => 'tahsili_year',
		'th-attr' => 'style="width:100px"',
		'function'=>function($col,$row)
		{
			return $row["tahsili_year"].'-'.($row["tahsili_year"]+1);
		}
    );
$cols['کلاسها'] =
    array(
        'field_name' => 'classcount',
        'link' => false,
		'function'=>function($col,$row)
		{
			global $classcountX;
			if(isset($classcountX[$row["id"]]))
			$col = intval($classcountX[$row["id"]]);
			return '<a href="admin/dorehclass?c.dorehid='.$row["id"].'" class="img-thumbnail p-2"><strong>'.$col.'</strong></a>';
		}
    );
$cols['محل برگزاری'] =
    array(
        'field_name' => 'placeid',
        'link' => false,
		'function'=>function($col,$row)
		{
			global $supplierX;
			return @$supplierX[$row["placeid"]];
		}
    );

$cols['سطح پیشنهادی'] =
    array(
        'field_name' => 'offer',
        'link' => false,
		'function'=>function($col,$row)
		{
			global $offer;
			return @$offer[$row["offer"]];
		}
    );
$cols['مدیر'] =
    array(
        'field_name' => 'supplierid',
        'link' => false,
		'function'=>function($col,$row)
		{
			global $supplierX;
			return @$supplierX[$row["supplierid"]];
		}
    );
$cols['تاریخ ثبت'] =
    array(
        'field_name' => 'createdate',
        'link' => true,
        'type' => 'date',
        'th-attr' => 'style="width:150px"'
    );
$cols['ویرایش'] =
    array(
			'field_name' => 'id',
			'link'    => false,
			'th-attr' => 'style="width:50px"',
			'html'    => '<i class="fa fa-edit text-success cu" onClick="edit_doreh(this,[ID])"></i>'
		);
$cols['حذف'] =
    array(
			'field_name' => 'id',
			'link'    => false,
			'th-attr' => 'style="width:50px"',
			'html'    => '<i class="fa fa-trash text-danger cu" onClick="delete_doreh(this,[ID])"></i>'
		);

 $q ="SELECT c.* FROM ci_doreh c $query";
$tecats = $this->db->order_by('name','asc')->get('tecat')->result();
$nezams = $this->db->order_by('name','asc')->get('nezam')->result();
$dorehmasters = $this->db->where('optype',2)->order_by('title','asc')->get('supplier')->result();
$topmaster = new stdClass;
$topmaster->id = 0;
$topmaster->title = "ندارد";
$datasupplier = array(array($topmaster));
foreach($dorehmasters as $k=>$v){
	$stype = $supplierrules[$v->id];
	foreach($stype as $k0=>$v0) {
        $datasupplier[$v0][] = $v;
    }
}

$dorehmasters = $this->db->where('optype',1)->order_by('title','asc')->get('supplier')->result();
$topmaster = new stdClass;
$topmaster->id = 0;
$topmaster->title = "ندارد";
array_unshift($dorehmasters,$topmaster);
	echo $searchHtml;
?>

<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">
		دوره ها 
		<a class="btn-sm btn-warning pull-left" onclick="new_doreh();">دوره جدید</a>
		<div class="clearfix"></div>
	</h3>
  </div>
</div>


<?php $inc->createTable($cols, $q, 'id="table" class="table light2" ', $tableName, 60); ?>

<?php $canReply = false ?>

<div class="hidden">
    <div class="view-sample">
		<h2 align="center">ثبت دوره</h2>
		<form class="clearfix">
			<div class="row col-md-offset-2">
				<div class="col-md-6">
						<div class="form-group">
							<table class="table" dir="rtl">
								<tr>
									<th width="120">* نام دوره</th>
									<td>
										<select name="tecatid" class="form-control update-el tecatid">
										<?php foreach($tecats as $k=>$v){ ?>
										<option value="<?php echo $v->id?>"><?php echo $v->name?></option>
										<?php } ?>
										</select>
									</td>
								</tr>
								<tr>
									<th width="120">* نظام</th>
									<td>
										<select name="nezamid" class="form-control update-el nezamid">
										<?php foreach($nezams as $k=>$v){ ?>
										<option value="<?php echo $v->id?>"><?php echo $v->name?></option>
										<?php } ?>
										</select>
									</td>
								</tr>
								<tr>
									<td><div class="btn-danger p-1">سطح پیشنهادی</div></td>
									<td>
										<select name="offer" class="form-control update-el offer" dir="rtl">
										<?php foreach($offer as $i=>$v){ ?>
										<option value="<?php echo $i;?>"><?php echo $v;?></option>
										<?php } ?>
										</select>
									</td>
								</tr>
							</table>
						</div>
						<div class="box">
							<div class="box-title">توضیحات</div>
							<div class="box-content">
								<textarea name="description" class="form-control update-el description" rows="4"></textarea>
							</div>
						</div>
						<br />
						<div class="box">
							<div class="box-title">اطلاعات تکمیلی</div>
							<div class="box-content">
								<table class="table" dir="rtl">
									<tr>
										<td>سال تحصیلی</td>
										<td>
											<select name="tahsili_year" class="form-control update-el tahsili_year" dir="ltr">
											<?php for($i= 1399;$i<1410;$i++){ ?>
											<option value="<?php echo $i;?>"><?php echo $i;?> - <?php echo $i+1;?></option>
											<?php } ?>
											</select>
										</td>
									</tr>
									<tr>
										<th>* محل برگزاری</th>
										<td>
											<select name="placeid" class="form-control update-el placeid">
											<?php foreach($datasupplier as $k=>$v){ ?>
												<optgroup label="<?php echo $suppliertype[$k];?>">
												<?php foreach($v as $k1=>$v1){ ?>
													<option value="<?php echo $v1->id;?>"><?php echo $v1->title;?></option>
												<?php } ?>
												</optgroup>
											<?php } ?>
											</select>
										</td>
									</tr>
									<tr>
										<td>مدیر</td>
										<td>
											<select name="supplierid" class="form-control update-el supplierid">
											<?php foreach($dorehmasters as $k=>$v){ ?>
											<option value="<?php echo $v->id?>"><?php echo $v->title?></option>
											<?php } ?>
											</select>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<hr/>
						<div class="box classes hidden">
							<div class="box-title">کلاسها</div>
							<div class="box-content">
							</div>
						</div>
				</div>	
				<div class="col-md-3">
					<div class="box mb-2">
						<div class="box-title">ذخیره</div>
						<div class="box-content">
							<div class="form-group">
								<button type="button" class="btn btn-primary btn-md col-md-5 pull-right m-1 sample-publish">
									<i class="fa fa-check-circle"></i> <span>ذخیره</span>
								</button>
								<button type="button" class="btn btn-danger btn-md col-md-5 pull-left m-1 sample-unpublish">
									<i class="fa fa-check-circle"></i> <span>عدم نمایش</span>
								</button>
								<div class="clearfix"></div>
								<div class="ajax-result" style="margin-bottom: 20px;"></div>
							</div>
						</div>
					</div>
					<div class="box media-ap">
						<div class="box-title"><i class="fa fa-photo"></i> تصویر</div>
		
						<div class="box-content" style="padding:2px;text-align:center">
							<div class="convert-to-form-el editable-img" form-el-name="data[thumb]" form-el-value="file">
								<div class="media-ap-data replace center" data-thumb="thumb300" id="temp-my-media-ap" align="center">
								</div>
								<div id="temp-add-thumb-img" class="plus add-img"
									 onClick="media('img,1',this,function(){$('#add-thumb-img').hide();})"
									 style="display:inline-block;margin:15px"></div>
								<div class="form-ap-data" style="display:none"></div>
							</div>
						</div>
		
						<div class="box-footer"><i class="fa fa-pencil cu" onClick="media('img,1',this)"></i></div>
						<input type="hidden" name="image" class="update-el image media-ap-input" value="">
					</div>
					<!--div class="box mb-2">
						<div class="box-title">دسته بندی عنوانی درس</div>
						<div class="box-content">
							<div class="form-group">
								
							</div>
						</div>
					</div-->
				</div>
			</div>
			<input type="hidden" name="id" class="update-el id" value="0">
			<input type="hidden" name="published" class="update-el published" value="0">
		</form>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function () {

    });
    function new_doreh() {
        var $html = $('<div/>', {'id': 'edit-comment'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

		var $view = $('.view-sample').clone(true);

		$view.find('.sample-publish').on('click', function () {
			$view.find('.published').val(1);
			save_doreh(this);
		});
		$view.find('.sample-unpublish').on('click', function () {
			$view.find('.published').val(0);
			save_doreh(this);
		});
		$view.find('#temp-add-thumb-img').attr('id','add-thumb-img');
		$view.find('#temp-my-media-ap').attr('id','media-ap');
		$html.html($view);
    }
    function edit_doreh(that,id){
        var $html = $('<div/>', {'id': 'edit-doreh'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

        $.ajax({
            type: "POST",
            url: 'admin/api/getDorehInfo/' + id,
            dataType: "json",
            success: function (data) {

                if (data == "login") {
                    popupScreen('');
                    login(function () {
                        edit_doreh(btn, id)
                    });
                    return;
                }

                if (!data.done) {
                    $html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
                    return;
                }
                var $view = $('.view-sample').clone(true);

                $view.find('.update-el').each(function (i, el) {
                    var val = data.doreh[$(el).attr('name')];
                    $(el).val(val).data('prevdata',val);
                });

                $view.find('.sample-publish').on('click', function () {
                    $view.find('.published').val(1);
					save_doreh(this);
                });
                $view.find('.sample-unpublish').on('click', function () {
                    $view.find('.published').val(0);
					save_doreh(this);
                });
				$view.find('#temp-add-thumb-img').attr('id','add-thumb-img');
				$view.find('#temp-my-media-ap').attr('id','my-media-ap');
				if(data.doreh['image']){
					$thumb = $('<div class="media-ap-data" />');
					var img = $('<img/>',{src:data.doreh['image']}).addClass('convert-this img-responsive');
					$thumb.append(img);
					console.error([data.doreh['image'],$view.find('#my-media-ap').length,$view.find('#my-media-ap')]);
					$view.find('#my-media-ap').html($thumb);
					$view.find('#add-thumb-img').hide();
				}
                $html.html($view);
                $html.find("[name=active]").trigger('change');
            },
            error: function () {
                $html.html('<h3 class="text-warning text-center">Conection Error</h3>');
            }
        });
    }
    function save_doreh(btn){
        $(btn).addClass('l w');
        var form = $(btn).closest('form');
        var data = $(form).serialize();

        $.ajax({
            type: "POST",
            url: 'admin/api/SaveDoreh',
            data: data,
            dataType: "json",
            success: function (data) {
                if (data == "login")
                {
                    login(function () {
                        save_doreh(btn)
                    });
                    return;
                }
                else
                {
                    $(btn).closest('form').find('.ajax-result').html(get_alert(data));
                    notify(data.msg, data.status);
					if(data.status == 0){
						setTimeout(function(){location.reload();},800);
					}
                }
                $(btn).removeClass('l w');
            },
            error: function () {
                $(btn).removeClass('l w');
                notify('خطا در اتصال', 2);
            }
        });
    }
    function delete_doreh(btn,id){
		Confirm({
			url         : "deleteDoreh/"+id,
			Dhtml       : 'به طور کامل حذف می شود .<br/> ادامه می دهید ؟',
			Did         : 'deleterow_'+id,
			success     : function(data){
				$(btn).closest('tr').hide(1000,function(){$(this).remove()});
			}
		});
	}


</script>
