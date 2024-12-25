<?php
/**
 * Created by Talkhabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 12:05 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('inc');
global $extraData;
$inc = new inc;
$extraData = $extra;
$cols['شماره'] =
    array(
        'field_name' => 'id',
        'link' => true
    );
$cols['تصویر'] =
    array(
        'field_name' => 'image',
		'th-attr' => 'style="width:50px"',
        'link' => false,
		'function'=>function($col,$row){
			return $col?'<img style="max-width:50px;max-height:50px" src="'.$col.'" />':'';
		}
    );
$cols['عنوان'] =
    array(
        'field_name' => 'title',
        'link' => true
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
$cols['حقیقی یا حقوقی'] =
    array(
        'field_name' => 'optype',
        'link' => true,
		'function'=>function($col,$row){
			return $col==1?'حقیقی':'حقوقی';
		}
    );

$cols['نوع'] =
    array(
        'field_name' => 'stypeX',
        'link' => true,
		'function'=>function($col,$row){
			global $extraData;
			return isset($extraData['supplierrules'][$col])?implode(' , ',$extraData['supplierrules'][$col]):'';
		}
    );
$cols['تلفن ثابت'] =
    array(
        'field_name' => 'phone',
        'link' => true
    );
$cols['تلفن همراه'] =
    array(
        'field_name' => 'mobile',
        'link' => true
    );

$cols['سازمان بالادستی'] =
    array(
        'field_name' => 'smtypeX',
        'link' => true
    );

$cols['درصد مالکیت'] =
    array(
        'field_name' => 'ownerpercent',
        'link' => true
    );

$cols['تاریخ ثبت'] =
    array(
        'field_name' => 'regdate',
        'link' => true,
        'type' => 'date',
        'th-attr' => 'style="width:150px"'
    );
$cols['ویرایش'] =
    array(
			'field_name' => 'id',
			'link'    => false,
			'html'    => '<i class="fa fa-edit text-success cu" onClick="edit_supplier(this,[ID])"></i>'
		);
$cols['حذف'] =
    array(
			'field_name' => 'id',
			'link'    => false,
			'html'    => '<i class="fa fa-trash text-danger cu" onClick="delete_row(this,\'supplier\',[ID])"></i>'
		);

if(stristr($query,'type_id')==FALSE){
	$q ="SELECT c.*,c.id stypeX,e.title smtypeX FROM ci_supplier c LEFT JOIN ci_supplier e ON e.id=c.smtype $query";
} else {
	$q ="SELECT c.*,c.id stypeX,e.title smtypeX FROM ci_supplier c LEFT JOIN ci_supplier e ON e.id=c.smtype LEFT JOIN ci_supplierrules d ON c.id=d.sup_id $query";
}
$suppliertypes = $this->db->order_by('title','asc')->get('suppliertype')->result();
$suppliermasters = $this->db->order_by('title','asc')->get('supplier')->result();
$topmaster = new stdClass;
$topmaster->id = 0;
$topmaster->title = "ندارد";
$suppliermasters = array_merge(array($topmaster),$suppliermasters);
	echo $searchHtml;
?>

<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">
		عرضه کنندگان 
		<a class="btn-sm btn-warning pull-left" onclick="new_supplier();">عرضه کننده جدید</a>
		<div class="clearfix"></div>
	</h3>
  </div>
</div>


<?php $inc->createTable($cols, $q, 'id="table" class="table light2" ', $tableName, 60); ?>

<?php $canReply = false ?>

<div class="hidden">

    <div id="edit-supplier">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <form class="clearfix">
                    <div class="form-group">
                        <input type="text" dir="rtl" name="title" class="form-control update-el title" placeholder="عنوان" />
                    </div>
					<div class="col-md-8 box">
						<div class="box-title">توضیحات</div>
						<div class="box-content">
							<textarea name="description" class="form-control update-el description" rows="4"></textarea>
						</div>
					</div>
					<div class="col-md-4 box media-ap">
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
					<div class="clearfix"></div>
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
									<th width="40%">حقیقی یا حقوقی</th>
									<td>
										<select name="optype" class="form-control update-el optype">
											<option value="1">حقیقی</option>
											<option value="2">حقوقی</option>
										</select>
									</td>
								</tr>
								<tr>
									<th>نوع</th>
									<td>
										<select name="stype[]" class="form-control update-el stype" multiple>
										<?php foreach($suppliertypes as $k=>$v){ ?>
										<option value="<?php echo $v->id?>"><?php echo $v->title?></option>
										<?php } ?>
										</select>
									</td>
								</tr>
								<tr>
									<th>تلفن ثابت</th>
									<td><input type="text" name="phone" class="form-control update-el phone"></td>
								</tr>
								<tr>
									<th>تلفن همراه</th>
									<td><input type="text" name="mobile" class="form-control update-el mobile"></td>
								</tr>
								<tr>
									<th>سازمان بالادستی</th>
									<td>
										<select name="smtype" class="form-control update-el smtype">
										<?php foreach($suppliermasters as $k=>$v){ ?>
										<option value="<?php echo $v->id?>"><?php echo $v->title?></option>
										<?php } ?>
										</select>
									</td>
								</tr>
								<tr>
									<th>آدرس</th>
									<td><input type="text" name="address" class="form-control update-el address"></td>
								</tr>
								<tr>
									<th>درصد مالکیت</th>
									<td><input type="text" name="ownerpercent" class="form-control update-el ownerpercent"></td>
								</tr>
							</table>
						</div>
					</div>
                    <hr/>
                    <div class="ajax-result" style="margin-bottom: 20px;"></div>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary btn-block btn-lg sample-edit">
                            <i class="fa fa-check-circle"></i> <span>تایید</span>
                        </button>
                    </div>
					<input type="hidden" name="id" class="form-control update-el id" value="0">
                </form>
            </div>	
        </div>
    </div>
</div>

<link type="text/css" rel="stylesheet" href="<?php echo  base_url() ?>/js/chosen/chosen.min.css" />
<script src="<?php echo  base_url() ?>/js/chosen/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">

    $(document).ready(function () {

    });

    function new_supplier() {
        var $html = $('<div/>', {'id': 'edit-comment'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

		var $view = $('#edit-supplier').clone(true);

		$view.find('.sample-edit').on('click', function () {
			save_supplier(this);
		});
		$html.html($view);
		$html.find('.chosen-container').trigger("chosen:updated");
		$html.find("select").chosen({width: '100%'});
    }
    function edit_supplier(that,id){
        var $html = $('<div/>', {'id': 'edit-supplier'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

        $.ajax({
            type: "POST",
            url: 'admin/api/getSupplierInfo/' + id,
            dataType: "json",
            success: function (data) {

                if (data == "login") {
                    popupScreen('');
                    login(function () {
                        edit_supplier(btn, id)
                    });
                    return;
                }

                if (!data.done) {
                    $html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
                    return;
                }
                var $view = $('#edit-supplier').clone(true);
                $view.find('.update-el').each(function (i, el) {
					$name = $(el).attr('name');
					$name = $name.replace('[]','');
                    var val = data.supplier[$name];
					if($(el).attr('type')=='checkbox'){
	                    $(el).prop("checked", val==1).data('prevdata',val);
					} else {
	                    $(el).val(val).data('prevdata',val);
					}
                });

				$view.find('#temp-my-media-ap').attr('id','my-media-ap');
				if(data.supplier['image']){
					$thumb = $('<div class="media-ap-data" />');
					var img = $('<img/>',{src:data.supplier['image']}).addClass('convert-this img-responsive');
					$thumb.append(img);
					$view.find('#my-media-ap').html($thumb);
					$view.find('#add-thumb-img').hide();
				}

                $view.find('.sample-edit').on('click', function () {
                    save_supplier(this, id);
                });
                $html.html($view);
                $html.find("[name=active]").trigger('change');
				$html.find('.chosen-container').trigger("chosen:updated");
				$html.find("select").chosen({width: '100%'});
            },
            error: function () {
                $html.html('<h3 class="text-warning text-center">Conection Error</h3>');
            }
        });
    }
    function save_supplier(btn){
        $(btn).addClass('l w h6');
        var form = $(btn).closest('form');
        var data = $(form).serialize();

        $.ajax({
            type: "POST",
            url: 'admin/api/SaveSupplier',
            data: data,
            dataType: "json",
            success: function (data) {
                if (data == "login")
                {
                    login(function () {
                        save_supplier(btn)
                    });
                    return;
                }
                else
                {
                    $(btn).closest('form').find('.ajax-result').html(get_alert(data));
                    notify(data.msg, data.status);
					if(data.status == 0) {
                        location.reload();
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

</script>
