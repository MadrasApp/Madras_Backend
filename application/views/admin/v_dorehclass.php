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
global $placeX,$ostadX,$dorehX,$classroomX,$favoriteX;
$ostadX=$ostad;
$dorehX=$doreh;
$placeX=$place;
$classroomX = $classroom;
$favoriteX=$favorite;
$inc = new inc;

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
$cols['پیشنهادی'] =
	array(
		'field_name'=>'offer',
		'function'=>function($col,$row)
		{

			$offer =
				$row['offer'] == 1 ?
					'<i class="fa fa-check-circle fa-lg text-success" title="پیشنهاد شده"></i>' :
					'<i class="fa fa-times-circle fa-lg text-danger" title="پیشنهاد نشده"></i>';
			return $offer;
		},
		'td-attr'=>'align="center" style="width:70px;"'
	);
$cols['اسم دوره'] =
    array(
        'field_name' => 'dorehid',
        'link' => false,
		'function'=>function($col,$row)
		{
			global $placeX,$ostadX,$dorehX,$classroomX;
			return @$dorehX[$row["dorehid"]];
		}
    );
$cols['کلاسها'] =
    array(
        'field_name' => 'classid',
        'link' => false,
		'function'=>function($col,$row)
		{
			global $placeX,$ostadX,$dorehX,$classroomX;
			return @$classroomX[$row["classid"]];
		}
    );
$cols['محل برگزاری'] =
    array(
        'field_name' => 'placeid',
        'link' => false,
		'function'=>function($col,$row)
		{
			global $placeX,$ostadX,$dorehX,$classroomX;
			return @$placeX[$row["placeid"]];
		}
    );

$cols['استاد'] =
    array(
        'field_name' => 'ostadid',
        'link' => false,
		'function'=>function($col,$row)
		{
			global $placeX,$ostadX,$dorehX,$classroomX;
			return @$ostadX[$row["ostadid"]];
		}
    );
$cols['تعداد جلسات'] =
    array(
        'field_name' => 'jalasat',
        'link' => true,
		'th-attr' => 'style="width:50px"',
		'function'=>function($col,$row)
		{
			global $placeX,$ostadX,$dorehX,$classroomX;
			return '<a href="admin/jalasat?c.dorehclassid='.$row["id"].'">'.$col.'</a>';
		}
    );
$cols['تاریخ شروع'] =
    array(
        'field_name' => 'startdate',
        'type' => 'date',
        'th-attr' => 'style="width:150px"'
    );
$cols['ساعت شروع'] =
    array(
        'field_name' => 'starttime',
        'th-attr' => 'style="width:150px"'
    );
$cols['مبلغ'] =
    array(
        'field_name' => 'price',
        'type' => 'price',
        'th-attr' => 'style="width:150px"'
    );
$cols['تاریخ ثبت'] =
    array(
        'field_name' => 'createdate',
        'link' => true,
        'type' => 'date',
        'th-attr' => 'style="width:100px"'
    );
$cols['محبوبیت'] =
    array(
        'field_name' => 'favorite',
        'link' => false,
		'function'=>function($col,$row)
		{
			global $favoriteX;
			return intval(@$favoriteX[$row["id"]]);
		}
    );
$cols['ویرایش'] =
    array(
			'field_name' => 'id',
			'link'    => false,
			'th-attr' => 'style="width:50px"',
			'html'    => '<i class="fa fa-edit text-success cu" onClick="edit_dorehclass(this,[ID])"></i>'
		);
$cols['حذف'] =
    array(
			'field_name' => 'id',
			'link'    => false,
			'th-attr' => 'style="width:50px"',
			'html'    => '<i class="fa fa-trash text-danger cu" onClick="delete_dorehclass(this,[ID])"></i>'
		);

$q ="SELECT c.*,0 AS favorite FROM ci_dorehclass c $query";
	echo $searchHtml;
?>

<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">
		کلاسهای دوره ها 
		<a class="btn-sm btn-warning pull-left" onclick="new_dorehclass();">کلاس دوره جدید</a>
		<div class="clearfix"></div>
	</h3>
  </div>
</div>


<?php $inc->createTable($cols, $q, 'id="table" class="table light2" ', $tableName, 60); ?>

<?php $canReply = false ?>

<div class="hidden">
    <div class="view-sample">
		<h2 align="center">ثبت کلاس دوره</h2>
		<form class="clearfix">
			<div class="row col-md-offset-2">
				<div class="col-md-6">
						<div class="form-group">
							<table class="table" dir="rtl">
								<!--tr>
									<th width="120">* عنوان</th>
									<td>
										<input type="text" name="title" class="form-control update-el classid">
									</td>
								</tr-->
								<tr>
									<th width="120">* نام کلاس</th>
									<td>
										<select name="classid" class="form-control update-el classid">
										<option value="">بدون انتخاب</option>
										<?php foreach($classroom as $k=>$v){ ?>
										<option value="<?php echo $k?>"><?php echo $v?></option>
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
										<th>پیشنهادی</th>
										<td>
											<input name="offer" type="checkbox" class="form-control update-el offer" value="1" />
										</td>
									</tr>
									<tr>
										<th width="120">* نام دوره</th>
										<td>
											<select name="dorehid" class="form-control update-el dorehid">
											<option value="">بدون انتخاب</option>
											<?php foreach($doreh as $k=>$v){ ?>
											<option value="<?php echo $k?>"><?php echo $v?></option>
											<?php } ?>
											</select>
										</td>
									</tr>
									<tr>
										<th>* محل برگزاری</th>
										<td>
											<select name="placeid" class="form-control update-el placeid">
											<option value="">بدون انتخاب</option>
											<?php foreach($place as $k=>$v){ ?>
											<option value="<?php echo $k?>"><?php echo $v?></option>
											<?php } ?>
											</select>
										</td>
									</tr>
									<tr>
										<th>* استاد</th>
										<td>
											<select name="ostadid" class="form-control update-el ostadid">
											<option value="">بدون انتخاب</option>
											<?php foreach($ostad as $k=>$v){ ?>
											<option value="<?php echo $k?>"><?php echo $v?></option>
											<?php } ?>
											</select>
										</td>
									</tr>
									<tr>
										<th>* تاریخ شروع</th>
										<td>
											<input type="text" class="form-control startdate dateFormat update-el"  name="startdate">
										</td>
									</tr>
									<tr>
										<th>* ساعت شروع</th>
										<td>
											<select name="starttime" class="form-control update-el starttime">
											<?php for($i=4;$i<22;$i++){ ?>
											<option value="<?php echo $i?>:00"><?php echo $i?>:00</option>
											<option value="<?php echo $i?>:15"><?php echo $i?>:15</option>
											<option value="<?php echo $i?>:30"><?php echo $i?>:30</option>
											<option value="<?php echo $i?>:45"><?php echo $i?>:45</option>
											<?php } ?>
											</select>
										</td>
									</tr>
									<tr>
										<th>* مبلغ</th>
										<td>
											<input type="text" class="form-control price dateFormat update-el"  name="price">
										</td>
									</tr>
								</table>
							</div>
						</div>
						<hr/>
						<div class="box classes hidden">
							<div class="box-title">کلاسها</div>
							<div class="box-content">
							AAAAAAAAAA
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
<link type="text/css" rel="stylesheet" href="<?php echo  base_url() ?>/js/chosen/chosen.min.css" />
<script src="<?php echo  base_url() ?>/js/chosen/chosen.jquery.min.js" type="text/javascript"></script>

<script type="text/javascript">

    $(document).ready(function () {

    });
    function new_dorehclass() {
        var $html = $('<div/>', {'id': 'edit-dorehclass'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

		var $view = $('.view-sample').clone(true);

		$view.find('.sample-publish').on('click', function () {
			$view.find('.published').val(1);
			save_dorehclass(this);
		});
		$view.find('.sample-unpublish').on('click', function () {
			$view.find('.published').val(0);
			save_dorehclass(this);
		});
		$view.find('#temp-add-thumb-img').attr('id','add-thumb-img');
		$view.find('#temp-my-media-ap').attr('id','media-ap');
		$html.html($view);
		$html.find('.startdate').datepicker({dateFormat: 'yy-mm-dd'});
		$html.find("select").chosen({width: '100%'});
    }
    function edit_dorehclass(that,id){
        var $html = $('<div/>', {'id': 'edit-dorehclass'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

        $.ajax({
            type: "POST",
            url: 'admin/api/getDorehClassInfo/' + id,
            dataType: "json",
            success: function (data) {

                if (data == "login") {
                    popupScreen('');
                    login(function () {
                        edit_dorehclass(btn, id)
                    });
                    return;
                }

                if (!data.done) {
                    $html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
                    return;
                }
                var $view = $('.view-sample').clone(true);

                $view.find('.update-el').each(function (i, el) {
                    var val = data.dorehclass[$(el).attr('name')];
					if($(el).attr('type')=='checkbox'){
	                    $(el).prop("checked", val==1).data('prevdata',val);
					} else {
	                    $(el).val(val).data('prevdata',val);
					}
                });

                $view.find('.sample-publish').on('click', function () {
                    $view.find('.published').val(1);
					save_dorehclass(this);
                });
                $view.find('.sample-unpublish').on('click', function () {
                    $view.find('.published').val(0);
					save_dorehclass(this);
                });
				$view.find('#temp-add-thumb-img').attr('id','add-thumb-img');
				$view.find('#temp-my-media-ap').attr('id','my-media-ap');
				if(data.dorehclass['image']){
					$thumb = $('<div class="media-ap-data" />');
					var img = $('<img/>',{src:data.dorehclass['image']}).addClass('convert-this img-responsive');
					$thumb.append(img);
					$view.find('#my-media-ap').html($thumb);
					$view.find('#add-thumb-img').hide();
				}
				
                $html.html($view);
                $html.find("[name=active]").trigger('change');
				$html.find('.startdate').datepicker({dateFormat: 'yy-mm-dd',defaultDate:data.startdate});
				$html.find("select").chosen({width: '100%'});
            },
            error: function () {
                $html.html('<h3 class="text-warning text-center">Conection Error</h3>');
            }
        });
    }
    function save_dorehclass(btn){
        $(btn).addClass('l w');
        var form = $(btn).closest('form');
        var data = $(form).serialize();
        $.ajax({
            type: "POST",
            url: 'admin/api/SaveDorehClass',
            data: data,
            dataType: "json",
            success: function (data) {
                if (data == "login")
                {
                    login(function () {
                        save_dorehclass(btn)
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
    function delete_dorehclass(btn,id){
		Confirm({
			url         : "deleteDorehClass/"+id,
			Dhtml       : 'به طور کامل حذف می شود .<br/> ادامه می دهید ؟',
			Did         : 'deleterow_'+id,
			success     : function(data){
				$(btn).closest('tr').hide(1000,function(){$(this).remove()});
			}
		});
	}

</script>
