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
global $placeX,$ostadX,$dorehX,$classroomX,$dorehclassX;
$ostadX=$ostad;
$dorehX=$doreh;
$placeX=$place;
$classroomX = $classroom;
$dorehclassX = $dorehclass;
$inc = new inc;

$cols['شماره'] =
	array(
		'field_name' => 'id',
		'th-attr' => 'style="width:50px"',
		'link' => true
	);
$cols['تایید شده'] =
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
$cols['کلاس دوره'] =
	array(
		'field_name' => 'title',
		'function'=>function($col,$row)
		{
			global $placeX,$ostadX,$dorehX,$classroomX,$dorehclassX;
			return @$dorehclassX[$row["dorehclassid"]];
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
$cols['شماره جلسه'] =
	array(
		'field_name' => 'title',
	);
$cols['بخشهای جلسه'] =
	array(
		'field_name' => 'subjalase',
		'link' => false,
		'function'=>function($col,$row)
		{
			global $placeX,$ostadX,$dorehX,$classroomX;
			return '<a  onclick="subjalasat(this,'.$row["id"].')"><i class="fa fa-tags"></i> '.$col.'</a>';
		}
	);
$cols['تاریخ'] =
	array(
		'field_name' => 'startdate',
		'type' => 'date',
		'th-attr' => 'style="width:150px"'
	);
$cols['ساعت'] =
	array(
		'field_name' => 'starttime',
		'th-attr' => 'style="width:150px"'
	);
$cols['تصویر'] =
	array(
		'field_name' => 'image',
		'th-attr' => 'style="width:50px"',
		'function'=>function($col,$row)
		{
			return '<i class="fa fa-'.($col?'check-circle-o text-success':'times-circle-o text-danger').' cu"></i> '.$col;
		}
	);
$cols['pdf'] =
	array(
		'field_name' => 'pdf',
		'th-attr' => 'style="width:50px"',
		'function'=>function($col,$row)
		{
			return '<i class="fa fa-'.($col?'check-circle-o text-success':'times-circle-o text-danger').' cu"></i> '.$col;
		}
	);
$cols['صوت'] =
	array(
		'field_name' => 'audio',
		'th-attr' => 'style="width:50px"',
		'function'=>function($col,$row)
		{
			return '<i class="fa fa-'.($col?'check-circle-o text-success':'times-circle-o text-danger').' cu"></i> '.$col;
		}
	);
$cols['ویدئو'] =
	array(
		'field_name' => 'video',
		'th-attr' => 'style="width:50px"',
		'function'=>function($col,$row)
		{
			return '<i class="fa fa-'.($col?'check-circle-o text-success':'times-circle-o text-danger').' cu"></i> '.$col;
		}
	);
$cols['تاریخ ثبت'] =
	array(
		'field_name' => 'createdate',
		'link' => true,
		'type' => 'date',
		'th-attr' => 'style="width:100px"'
	);
$cols['ویرایش'] =
	array(
			'field_name' => 'id',
			'link'    => false,
			'th-attr' => 'style="width:50px"',
			'html'    => '<i class="fa fa-edit text-success cu" onclick="edit_jalasat(this,[ID])"></i>'
		);
$cols['حذف'] =
	array(
			'field_name' => 'id',
			'link'    => false,
			'th-attr' => 'style="width:50px"',
			'html'    => '<i class="fa fa-trash text-danger cu" onclick="delete_jalasat(this,[ID])"></i>'
		);

$q ="SELECT c.*,d.dorehid,d.placeid,d.ostadid,d.classid FROM ci_jalasat c LEFT JOIN ci_dorehclass  d ON d.id=c.dorehclassid $query";
	echo $searchHtml;
?>

<div class="panel panel-primary">
  <div class="panel-heading">
	<h3 class="panel-title">
		جلسات کلاسهای دوره ها 
		<a class="btn-sm btn-warning pull-left" onclick="new_jalasat();">جلسات کلاسهای دوره جدید</a>
		<div class="clearfix"></div>
	</h3>
  </div>
</div>


<?php $inc->createTable($cols, $q, 'id="table" class="table light2" ', $tableName, 60); ?>

<?php $canReply = false ?>

<div class="hidden">
	<div class="view-subjalasat">
		<h2 align="center">ثبت زیرجلسات کلاسهای دوره</h2>
		<form class="clearfix" dir="ltr">
			<div class="col-md-12">
				<div class="form-group">
					<div class="h2 text-success text-center">
							<span class="pull-left">
								<span class="pull-left col-md-9"><select class="list-subjalasat"></select></span>
								<span class="btn btn-primary col-md-3" onclick="ShowSectionSelected()"><i class="fa fa-plus fa-2x"></i></span>
							</span>
							<span>عنوان جلسه : <span class="jalase_title"></span></span>
							<span class="pull-right">
								<span  class="btn btn-success" onclick="sevSubJalasat(this)"><i class="fa fa-save fa-2x"></i></span>
							</span>
					</div>
					<div class="clearfix m-2"></div>
					<div class="resultsubjalasat"></div>
				</div>
			</div>
		</form>
	</div>
	<div class="view-sample">
		<h2 align="center">ثبت جلسات کلاسهای دوره</h2>
		<form class="clearfix">
			<div class="row col-md-offset-2">
				<div class="col-md-8">
						<div class="form-group">
							<table class="table" dir="rtl">
								<tr>
									<th width="120">* انتخاب کلاس دوره</th>
									<td>
										<select name="dorehclassid" class="form-control update-el dorehclassid allowdisable" onchange="ChangeMasterSelect(this);">
										<option value="">بدون انتخاب</option>
										<?php foreach($dorehclass as $k=>$v){ ?>
										<option value="<?php echo $k?>"><?php echo $v?></option>
										<?php } ?>
										</select>
									</td>
								</tr>
								<tr>
									<th width="120">* شماره جلسه</th>
									<td>
										<input type="text" name="title" class="form-control update-el classid">
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
									<thead>
									<tr>
										<th width="180">* کتاب</th>
										<th width="400">* پاراگراف</th>
										<th>تصویر</th>
										<th>PDF</th>
										<th>صوت</th>
										<th>فیلم</th>
									</tr>
									</thead>
									<tbody class="jalasatcontroller">
									</tbody>
									<?php /*
									<tr class="book-part">
										<td>
											<select name="data[bookid][]" class="form-control update-el dorehid">
											</select>
										</td>
										<td>
											<select name="data[paragraphid][]" class="form-control update-el placeid">
											</select>
										</td>
										<td class="section-image">
											<div class="btn add-image" data-type="image" data-rel="image" title="افزودن یا حذف تصویر"><i class="fa fa-2x fa-picture-o"></i></div>
											<input type="hidden" name="data[image][]" />
											<span class="resultdata"></span>
										</td>
										<td class="section-pdf">
											<div class="btn add-pdf" data-type="pdf" data-rel="file" title="افزودن یا حذف صدا"><i class="fa fa-2x fa-file-pdf-o"></i></div>
											<input type="hidden" name="data[pdf][]" />
											<span class="resultdata"></span>
										</td>
										<td class="section-sound">
											<div class="btn add-audio" data-type="audio" data-rel="file" title="افزودن یا حذف صدا"><i class="fa fa-2x fa-file-audio-o"></i></div>
											<input type="hidden" name="data[audio][]" />
											<span class="resultdata"></span>
										</td>
										<td class="section-video">
											<div class="btn add-video" data-type="video" data-rel="file" title="افزودن یا حذف صدا"><i class="fa fa-2x fa-file-video-o"></i></div>
											<input type="hidden" name="data[video][]" />
											<span class="resultdata"></span>
										</td>
									</tr>
									*/?>
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
									<i class="fa fa-check-circle"></i> <span>بایگانی</span>
								</button>
								<div class="clearfix"></div>
								<div class="ajax-result" style="margin-bottom: 20px;"></div>
							</div>
						</div>
					</div>
					<div class="oldresultjalasat"></div>
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
	var subjalasatid = 0;
	$(document).ready(function () {

	});
	function ChangeMasterSelect(obj) {
		var $html = $('#edit-jalasat');
		var $view = $('.view-sample');
		id = $(obj).val();
		$('#edit-jalasat .jalasatcontroller').find('tr').remove();
		$('#edit-jalasat .oldresultjalasat').html('');
		$.ajax({
			type: "POST",
			url: 'admin/api/getNewJalasatInfo/' + id,
			dataType: "json",
			success: function (data) {

				if (data == "login") {
					popupScreen('');
					login(function () {
						edit_jalasat(btn, id)
					});
					return;
				}

				if (!data.done) {
					$html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
					return;
				}

				createData($view,data);
				$html.find('.chosen-container').trigger("chosen:updated");
				$html.find("select").chosen({width: '100%'});
				$html.find('.oldresultjalasat').html(data.oldjalasat);
				LoadBase();
			},
			error: function () {
				$html.html('<h3 class="text-warning text-center">Conection Error</h3>');
			}
		});
	}
	function new_jalasat() {
		var $html = $('<div/>', {'id': 'edit-jalasat'});
		$html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
		popupScreen($html);

		var $view = $('.view-sample').clone(true);

		$view.find('.sample-publish').on('click', function () {
			$view.find('.published').val(1);
			save_jalasat(this);
		});
		$view.find('.sample-unpublish').on('click', function () {
			$view.find('.published').val(0);
			save_jalasat(this);
		});
		$view.find('#temp-add-thumb-img').attr('id','add-thumb-img');
		$view.find('#temp-my-media-ap').attr('id','media-ap');
		$html.html($view);
		$html.find('.startdate').datepicker({dateFormat: 'yy-mm-dd'});
		$html.find("select").chosen({width: '100%'});
		LoadBase();
	}
	function edit_jalasat(that,id){
		var $html = $('<div/>', {'id': 'edit-jalasat'});
		$html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
		popupScreen($html);
		$.ajax({
			type: "POST",
			url: 'admin/api/getJalasatInfo/' + id,
			dataType: "json",
			success: function (data) {

				if (data == "login") {
					popupScreen('');
					login(function () {
						edit_jalasat(btn, id)
					});
					return;
				}

				if (!data.done) {
					$html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
					return;
				}
				var $view = $('.view-sample').clone(true);

				$view.find('.update-el').each(function (i, el) {
					var val = data.jalasat[$(el).attr('name')];
					$(el).val(val).data('prevdata',val);
				});

				$view.find('.sample-publish').on('click', function () {
					$view.find('.published').val(1);
					save_jalasat(this);
				});
				$view.find('.sample-unpublish').on('click', function () {
					$view.find('.published').val(0);
					save_jalasat(this);
				});
				createData($view,data);
				$view.find('.allowdisable').attr('disabled',true);
				$html.html($view);
				$html.find("[name=active]").trigger('change');
				$html.find('.startdate').datepicker({dateFormat: 'yy-mm-dd',defaultDate:data.startdate});
				$html.find("select").chosen({width: '100%'});
				$html.find('.oldresultjalasat').html(data.oldjalasat);
				LoadBase();
			},
			error: function () {
				$html.html('<h3 class="text-warning text-center">Conection Error</h3>');
			}
		});
	}
	function save_jalasat(btn){
		$(btn).addClass('l w');
		var form = $(btn).closest('form');
		var data = $(form).serialize();

		$.ajax({
			type: "POST",
			url: 'admin/api/SaveJalasat',
			data: data,
			dataType: "json",
			success: function (data) {
				if (data == "login")
				{
					login(function () {
						save_jalasat(btn)
					});
					return;
				}
				else
				{
					$(btn).closest('form').find('.ajax-result').html(get_alert(data));
					notify(data.msg, data.status);
					if(data.status == 0){
						setTimeout(function(){location.reload();},100);
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
	function delete_jalasat(btn,id){
		Confirm({
			url         : "deleteJalasat/"+id,
			Dhtml       : 'به طور کامل حذف می شود .<br/> ادامه می دهید ؟',
			Did         : 'deleterow_'+id,
			success     : function(data){
				$(btn).closest('tr').hide(1000,function(){$(this).remove()});
			}
		});
	}
	function LoadBase(){
		$('.add-image,.add-pdf,.add-audio,.add-video').on('click',function(){
			var part = $(this), btn = this;
			var rel = $(this).data("rel");
			var type = $(this).data("type");
			if($(part).parent().parent().hasClass('has-'+type)) {
				if(!confirm('فایل ضمیمه حذف شود ؟')) return;
				$(part).parent().parent().removeClass('has-'+type);
				$(part).parent().find('.resultdata').html('');
				$(part).parent().find('input').val('');
			}else{
				media(rel+',1',btn,function(data,files,button){
					var src = data[0];
					$(part).parent().find('input').val(src);
					var image = 
						'<a href="'+src+'" download="'+src+'">'
						+ '<i class="fa fa-2x fa-2x fa-download" title="دریافت"></i>'
						+ '</a>';
					
					$(part).parent().find('.resultdata').html(image);
					$(part).parent().parent().addClass('has-'+type);
				});
			}
		});
	}
	function subjalasat(that,id){
		var $html = $('<div/>', {'id': 'subjalasat'});
		subjalasatid = id;
		$html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
		popupScreen($html);
		$.ajax({
			type: "POST",
			url: 'admin/api/getSubJalasatInfo/' + id,
			dataType: "json",
			success: function (data) {

				if (data == "login") {
					popupScreen('');
					login(function () {
						edit_jalasat(btn, id)
					});
					return;
				}

				if (!data.done) {
					$html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
					return;
				}
				var $view = $('.view-subjalasat').clone(true);

				$view.find('.update-el').each(function (i, el) {
					var val = data.jalasat[$(el).attr('name')];
					$(el).val(val).data('prevdata',val);
				});

				$view.find('.sample-publish').on('click', function () {
					$view.find('.published').val(1);
					save_jalasat(this);
				});
				$view.find('.sample-unpublish').on('click', function () {
					$view.find('.published').val(0);
					save_subjalasat(this);
				});
				createSubData($view,data);
				$view.find('.allowdisable').attr('disabled',true);
				$html.html($view);
				$html.find("[name=active]").trigger('change');
				$html.find('.startdate').datepicker({dateFormat: 'yy-mm-dd',defaultDate:data.startdate});
				LoadBase();
				MultiRange();
				LoadDuration(data.subjalasat);
			},
			error: function () {
				$html.html('<h3 class="text-warning text-center">Conection Error</h3>');
			}
		});
	}

	function createData($view,data){
		var jalasat_data = data.jalasat_data;
		var bookdata = data.bookdata;
		var paragraphdata = data.paragraphdata;
		
		var i,j,k,jvalue,selected,resultdata,jdata = [],pdata = [],pxdata = [] ,data_image = [],data_pdf = [],data_audio = [],data_video = [],data_id = [],pages = [],data_page = [];
		for(i=0;i<bookdata.length;i++){
			pdata[bookdata[i].id] = [];
			pages[bookdata[i].id] = bookdata[i].pages.split(',');
			data_page[bookdata[i].id] = [];
		}
		for(i=0;i<jalasat_data.length;i++){
			jdata[jalasat_data[i].bookid] = jalasat_data[i].pages.split(',');
			data_image[jalasat_data[i].bookid] = jalasat_data[i].image;
			data_pdf[jalasat_data[i].bookid] = jalasat_data[i].pdf;
			data_audio[jalasat_data[i].bookid] = jalasat_data[i].audio;
			data_video[jalasat_data[i].bookid] = jalasat_data[i].video;
			data_id[jalasat_data[i].bookid] = jalasat_data[i].id;
		}
		for(i=0;i<paragraphdata.length;i++){
			for(j = 0;j<pages[paragraphdata[i].data_id].length;j++){
				k = parseInt(pages[paragraphdata[i].data_id][j]);
				if(paragraphdata[i].order <= k && typeof(data_page[paragraphdata[i].data_id][paragraphdata[i].order])=='undefined'){
					data_page[paragraphdata[i].data_id][paragraphdata[i].order] = j+1;
				}
			}
			pdata[paragraphdata[i].data_id].push(paragraphdata[i]);
		}
		var html = '<tr class="book-part{has-image}{has-pdf}{has-audio}{has-video}">'+
			'<td><input type="hidden" name="data[id][{bookid}]" value="{id}" /><input type="hidden" name="data[bookid][{bookid}]" value="{bookid}" />{booktext}</td>'+
			'<td><select multiple name="data[pages][{bookid}][]" class="form-control update-el placeid">{paragraphdata}</select></td>'+
			'<td class="section-image"><div class="btn add-image" data-type="image" data-rel="image" title="افزودن یا حذف تصویر"><i class="fa fa-2x fa-picture-o"></i></div>'+
			'<input type="hidden" name="data[image][{bookid}]" value="{image}" />'+
			'<span class="resultdata">{resultdataimage}</span></td>'+
			'<td class="section-pdf"><div class="btn add-pdf" data-type="pdf" data-rel="file" title="افزودن یا حذف صدا"><i class="fa fa-2x fa-file-pdf-o"></i></div>'+
			'<input type="hidden" name="data[pdf][{bookid}]" value="{pdf}" />'+
			'<span class="resultdata">{resultdatapdf}</span></td>'+
			'<td class="section-sound"><div class="btn add-audio" data-type="audio" data-rel="file" title="افزودن یا حذف صدا"><i class="fa fa-2x fa-file-audio-o"></i></div>'+
			'<input type="hidden" name="data[audio][{bookid}]" value="{audio}" />'+
			'<span class="resultdata">{resultdataaudio}</span></td>'+
			'<td class="section-video"><div class="btn add-video" data-type="video" data-rel="file" title="افزودن یا حذف صدا"><i class="fa fa-2x fa-file-video-o"></i></div>'+
			'<input type="hidden" name="data[video][{bookid}]" value="{video}" />'+
			'<span class="resultdata">{resultdatavideo}</span></td>'+
			'</tr>';
		for(i=0;i<bookdata.length;i++){
			htmrow = html;
			htmrow = htmrow.replace(/{bookid}/g,bookdata[i].id);
			htmrow = htmrow.replace('{booktext}',bookdata[i].title);
			value = typeof(jdata[bookdata[i].id])=='undefined'?'':jdata[bookdata[i].id];
			paragraphdata = '';
			pxdata = typeof(pdata[bookdata[i].id])=='undefined'?[]:pdata[bookdata[i].id];
			pages = data_page[bookdata[i].id];
			for(j=0;j<pxdata.length;j++){
				selected = $.inArray(pxdata[j].order,value) > -1 ?' selected':''
				paragraphdata+='<option value="'+pxdata[j].order+'"'+selected+'>صفحه '+pages[pxdata[j].order]+' | '+pxdata[j].text+' ... ['+pxdata[j].id+']</option>';
			}
			if(typeof(data_image[bookdata[i].id])!='undefined'){
				image = data_image[bookdata[i].id];
				pdf = data_pdf[bookdata[i].id];
				audio = data_audio[bookdata[i].id];
				video = data_video[bookdata[i].id];
				id = data_id[bookdata[i].id];
				htmrow = htmrow.replace('{image}',image);
				htmrow = htmrow.replace('{pdf}',pdf);
				htmrow = htmrow.replace('{audio}',audio);
				htmrow = htmrow.replace('{video}',video);
				htmrow = htmrow.replace(/{id}/g,id);
				htmrow = htmrow.replace('{has-image}',image.length?' has-image':'');
				htmrow = htmrow.replace('{has-pdf}',pdf.length?' has-pdf':'');
				htmrow = htmrow.replace('{has-audio}',audio.length?' has-audio':'');
				htmrow = htmrow.replace('{has-video}',video.length?' has-video':'');
				resultdata = '';
				if(image.length){
						resultdata = '<a href="'+image+'" download="'+image+'">'
						+ '<i class="fa fa-2x fa-2x fa-download" title="دریافت"></i>'
						+ '</a>';
				}
				htmrow = htmrow.replace('{resultdataimage}',resultdata);
				resultdata = '';
				if(pdf.length){
						resultdata = '<a href="'+pdf+'" download="'+pdf+'">'
						+ '<i class="fa fa-2x fa-2x fa-download" title="دریافت"></i>'
						+ '</a>';
				}
				htmrow = htmrow.replace('{resultdatapdf}',resultdata);
				resultdata = '';
				if(audio.length){
						resultdata = '<a href="'+audio+'" download="'+audio+'">'
						+ '<i class="fa fa-2x fa-2x fa-download" title="دریافت"></i>'
						+ '</a>';
				}
				htmrow = htmrow.replace('{resultdataaudio}',resultdata);
				resultdata = '';
				if(video.length){
						resultdata = '<a href="'+video+'" download="'+video+'">'
						+ '<i class="fa fa-2x fa-2x fa-download" title="دریافت"></i>'
						+ '</a>';
				}
				htmrow = htmrow.replace('{resultdatavideo}',resultdata);
			} else {
				htmrow = htmrow.replace('{image}','');
				htmrow = htmrow.replace('{pdf}','');
				htmrow = htmrow.replace('{audio}','');
				htmrow = htmrow.replace('{video}','');
				htmrow = htmrow.replace('{id}','0');
				htmrow = htmrow.replace('{has-image}{has-pdf}{has-audio}{has-video}','');

				htmrow = htmrow.replace('{resultdataimage}','');
				htmrow = htmrow.replace('{resultdatapdf}','');
				htmrow = htmrow.replace('{resultdataaudio}','');
				htmrow = htmrow.replace('{resultdatavideo}','');
			}
			htmrow = htmrow.replace('{paragraphdata}',paragraphdata)
			$view.find('.jalasatcontroller').append(htmrow);
		}
	}
	function createSubData($view,data){
		var jalasat_data = data.jalasat_data;
		var bookdata = data.bookdata;
		var paragraphdata = data.paragraphdata;
		
		var i,jvalue,selected,resultdata,jdata = [],pxdata = [] ,data_image = [],data_pdf = [],data_audio = [],data_video = [],data_id = [];
		var keys = Object.keys(paragraphdata);
		jalase_title = paragraphdata[keys[0]][0].title;
		$view.find('.jalase_title').html(jalase_title);
		var slider = '<div class="container-player" id="player-{bookid}-{paragraphid}" data-counter="{count}">' +
			'<img src="style/images/play.png" id="play-{count}" class="play"  onclick="playAudio({count})"/>' +
			'<img src="style/images/pause.png" id="pause-{count}" class="pause none" onclick="pauseAudio({count});" />' +
			'<audio data-id="{count}" id="myAudio{count}">' +
			'<source src="{mp3}" type="audio/mp3">' +
			'</audio>' +
			'<div slider="true" id="slider-distance{count}">' +
			'<div>' +
			'<div inverse-left="true" id="inverse-left-{count}" style="width:{startLeft}%;"></div>' +
			'<div inverse-right="true" id="inverse-right-{count}" style="width:{endRight}%;"></div>' +
			'<div range="true" id="range-{count}" style="left:{startLeft}%;right:{endRight}%;"></div>' +
			'<span thumb="true" id="thumb-left-{count}" style="left:{startTmbLeft}%;"></span>' +
			'<div sign="true" id="sign-left-{count}" style="left:{startLeft}%;"><span id="value-left-{count}">{startLeft}</span></div>' +
			'<span thumb="true" id="thumb-right-{count}" style="left:{endTmbRight}%;"></span>' +
			'<div sign="true" id="sign-right-{count}" style="left:{startRight}%;"><span id="value-right-{count}">{startRight}</span></div>' +
			'</div>' +
			'<progress data-id="{count}" id="progress-bar-{count}" value="0" max="1000"></progress>' +
			'<input type="range" id="range_0_{count}" tabindex="0" value="{startLeftP}" max="1000" min="0" step="1" oninput="CalcLeftHandle(this,{count});" />' +
			'<input type="range" id="range_1_{count}" tabindex="0" value="{startRightP}" max="1000" min="0" step="1" oninput="CalcRightHandle(this,{count});" />' +
			'</div>' +
			'</div>';
		var html = 
			'<div class="box rounded mb-1 hidden" id="book-{bookid}">'+
				'<div class="p-2 box-title"><div class="h3">بخش های جلسه کتاب <strong class="text-danger">{book}</strong></div></div>'+
				'<div class="box-content">{content}</div>'+
			'</div>';
		var content = 
			'<div id="paragraph-{bookid}-{paragraphid}" class="hidden">'+
					'<div class="mb-2 col-md-6">'+
						'<div class="box rounded ml-2">'+
							'{slider}'+
							'<textarea name="description[{bookid}][{paragraphid}]" id="description-{bookid}-{paragraphid}" class="border rounded col-md-12 m-1" rows="3" dir="rtl" placeholder="توضیحات زیرجلسه"></textarea>'+
							'<input type="hidden" name="startPos[{bookid}][{paragraphid}]" id="startPos-{count}" />'+
							'<input type="hidden" name="endPos[{bookid}][{paragraphid}]" id="endPos-{count}" />'+
							'<input type="hidden" name="duration[{bookid}][{paragraphid}]" id="duration-{count}" />'+
							'<input type="hidden" name="save[{bookid}][{paragraphid}]" id="save-{count}" />'+
						'</div>'+
					'</div>'+
					'<div class="mb-2 col-md-6">'+
						'<div class="box rounded mb-2">'+
							'<div class="p-2 box-title"><strong class="text-primary h4">صفحه {page} {paragraphtitle}</strong></div>'+
							'<div class="btn btn-danger pull-left" onclick="HideSectionSelected(\'{bookid}-{paragraphid}\')"><i class="fa fa-2x fa-trash"></i></div>'+
							'<div class="box-content h-controll">'+
								'{contentdata}'+
							'</div>'+
						'</div>'+
					'</div>'+
					
			'</div>'+
					'<div class="clearfix"></div>';
		var listSubjalasat = '<option value="">انتخاب یک بخش</option>';
		for(i = 0;i<keys.length;i++){
			var htmldata = html;
			var contentsection = '';
			Section = paragraphdata[keys[i]];
			listSubjalasat+='<optgroup label="کتاب '+Section[0].book+'">';
			for(j=0;j<Section.length;j++){
				var htmlcontent = content;
				var slidercontent = slider;
				//console.error([keys[i],Section[j]]);
				htmldata = htmldata.replace(/{book}/g,Section[j].book);
				htmldata = htmldata.replace(/{bookid}/g,keys[i]);
				htmlcontent = htmlcontent.replace('{page}',Section[j].page);
				htmlcontent = htmlcontent.replace('{paragraphtitle}',Section[j].paragraphtitle);
				htmlcontent = htmlcontent.replace('{contentdata}',Section[j].text);
				slidercontent=slidercontent.replace(/{count}/g,i*100+j+1);
				slidercontent=slidercontent.replace('{mp3}',Section[j].audio);
				slidercontent=slidercontent.replace(/{startLeft}/g,0);
				slidercontent=slidercontent.replace(/{startTmbLeft}/g,0);
				slidercontent=slidercontent.replace(/{endRight}/g,0);
				slidercontent=slidercontent.replace(/{startRight}/g,100);
				slidercontent=slidercontent.replace(/{endTmbRight}/g,100 - 100*0.04);
				slidercontent=slidercontent.replace(/{startLeftP}/g,0);
				slidercontent=slidercontent.replace(/{startRightP}/g,1000);
				htmlcontent = htmlcontent.replace('{slider}',slidercontent);
				htmlcontent = htmlcontent.replace(/{bookid}/g,keys[i]);
				htmlcontent = htmlcontent.replace(/{paragraphid}/g,Section[j].paragraphid);
				htmlcontent = htmlcontent.replace(/{count}/g,i*100+j+1);
				contentsection+=htmlcontent;
				listSubjalasat+='<option value="'+keys[i]+'-'+Section[j].paragraphid+'">صفحه '+Section[j].page+' - '+Section[j].paragraphtitle+'</option>';
			}
			listSubjalasat+='</optgroup>';
			htmldata = htmldata.replace('{content}',contentsection);
			$view.find('.resultsubjalasat').append(htmldata);
			$view.find('.list-subjalasat').html(listSubjalasat);
		}
	}
	function getCurTime(id) {
		var dest = $('#subjalasat');
		var aid = document.getElementById("myAudio"+id);
		//console.error([aid.currentTime, aid.duration]);
	}

	function setCurTime(id , position) {
		var dest = $('#subjalasat');
		var aid = document.getElementById("myAudio"+id);
		aid.currentTime = position;
	}

	function playAudio(id) {
		var dest = $('#subjalasat');
		var aid = document.getElementById("myAudio"+id);
		$('.pause').each(function(){
			$(this).click();
		});
		var player = dest.find("#play-"+id);
		var duration = aid.duration;
		player.addClass("none");
		var pauser = dest.find("#pause-"+id);
		pauser.removeClass("none");
		position = parseInt(dest.find("#range_0_"+id).val());
		position = (position/1000) * duration;

		end = parseInt(dest.find("#range_1_"+id).val());
		end = (end/1000) * duration;

		//console.error(['position',id,position,duration,aid]);
		if(aid.currentTime >= end){
			aid.currentTime = position;
		} else if(position > aid.currentTime){
			//console.error(['position',position]);
			aid.currentTime = position;
		}
		aid.play();
	}

	function pauseAudio(id) {
		var dest = $('#subjalasat');
		var aid = document.getElementById("myAudio"+id);
		var player = dest.find("#play-"+id);
		player.removeClass("none");
		var pauser = dest.find("#pause-"+id);
		pauser.addClass("none");
		aid.pause();
	}
	function updateProgressBar(player){
		var dest = $('#subjalasat');
		player = player.target;
		var id = player.getAttribute("data-id");
		var duration = player.duration;
		var progressBar = dest.find("#progress-bar-"+id);
		var percentage = Math.floor((1000 / player.duration) * player.currentTime)/10;
		progressBar.val(percentage * 10);
		position = parseInt(dest.find("#range_1_"+id).val());
		position = (position/10);
		//console.error(['percentage',percentage,position]);
		if(percentage >= position)
			pauseAudio(id);
	}

	function MultiRange() {
		var dest = $('#subjalasat');
		var allaudio = dest.find("audio");
		//console.error(allaudio);
		var id,aid,player,progressBar,ids;
		for(var i =0;i<allaudio.length;i++){
			player = allaudio[i];
			player.addEventListener('timeupdate', updateProgressBar, false);
		}
		var progresses = dest.find("progress");
		for(var i =0;i<progresses.length;i++){
			progress = progresses[i];
			progress.duration = allaudio[i].duration;
			progress.addEventListener('click', function (e) {
				var id = this.getAttribute("data-id");
					position = (e.offsetX*this.duration/this.offsetWidth) + 1;
					//position = Math.floor(10000*position*this.duration)/100;
				//console.error([position,e.pageX,'duration',this.duration,this.offsetWidth]);
				setCurTime(id , position);
			});
		}
	}

	function CalcLeftHandle(obj,count) {
		//console.error(['CalcLeftHandle 0', obj.value, obj.parentNode, obj.parentNode.childNodes]);
		var aid = document.getElementById("myAudio"+count);
		duration = aid.duration;
		value = parseFloat(document.getElementById("range_0_"+count).value);
		obj.value = Math.min(obj.value, value - 1);
		var value = (1000 / (parseInt(obj.max) - parseInt(obj.min))) * parseInt(obj.value) - (1000 / (parseInt(obj.max) - parseInt(obj.min))) * parseInt(obj.min);
		telorance = value*0.004;
		var children = [];
		children[1] = document.getElementById("inverse-left-"+count);
		children[5] = document.getElementById("range-"+count);
		children[7] = document.getElementById("thumb-left-"+count);
		children[11] = document.getElementById("sign-left-"+count);
		
		children[1].style.width = (value / 10 + '%');
		children[5].style.left = (value / 10 + '%');
		children[7].style.left = ((value / 10 - telorance)+ '%');
		children[11].style.left = ((value / 10 - telorance + 1)+ '%');
		children[11].childNodes[0].innerHTML = parseInt((obj.value / 100)*duration)/10;

		$('#startPos-'+count).val(parseInt((obj.value / 100)*duration)/10);
		$('#duration-'+count).val(duration);
	}

	function CalcRightHandle(obj,count) {
		//console.error(['CalcRightHandle 0', obj.value, obj.parentNode.childNodes[3].value - (-1)]);
		var aid = document.getElementById("myAudio"+count);
		duration = aid.duration;
		obj.value = Math.max(obj.value, obj.parentNode.childNodes[3].value - (-1));
		var value = (1000 / (parseInt(obj.max) - parseInt(obj.min))) * parseInt(obj.value) - (1000 / (parseInt(obj.max) - parseInt(obj.min))) * parseInt(obj.min);
		telorance = value*0.004;
		var children = [];
		children[3] = document.getElementById("inverse-right-"+count);
		children[5] = document.getElementById("range-"+count);
		children[9] = document.getElementById("thumb-right-"+count);
		children[13] = document.getElementById("sign-right-"+count);
		

		children[3].style.width = ((1000 - value + 20) / 10 + '%');
		children[5].style.right = ((1000 - value) / 10 + '%');
		children[9].style.left = ((value / 10 - telorance)+ '%');
		children[13].style.left = ((value / 10 - telorance + 1)+ '%');
		children[13].childNodes[0].innerHTML = parseInt((obj.value / 100)*duration)/10;

		$('#endPos-'+count).val(parseInt((obj.value / 100)*duration)/10);
		$('#duration-'+count).val(duration);
	}
	
	function LoadDuration(data){
		setTimeout(function(){
		//console.error(data);
		if(data.length){
			for(var i=0;i<data.length;i++){
				//console.error(data[i]);
				ShowSectionSelected(data[i].bookid+'-'+data[i].paragraphid,data[i].description,data[i].startPos,data[i].endPos,data[i].duration);
			}
		}
		},100);
	}
	
	function HideSectionSelected(val){
		var $subjalasat = $('#subjalasat');
		$subjalasat.find('#paragraph-'+val).addClass('hidden');
		$subjalasat.find('.list-subjalasat option[value="'+val+'"]').prop('disabled', false);
	}
	function ShowSectionSelected(val,text,startPos,endPos,$duration){
		var $subjalasat = $('#subjalasat');
		val = typeof(val)=='undefined'?$subjalasat.find('.list-subjalasat').val():val;
		if(val.length==0){
			return;
		}
		var xval = val.split('-');
		$subjalasat.find('#book-'+xval[0]).removeClass('hidden');
		$subjalasat.find('#paragraph-'+xval[0]+'-'+xval[1]).removeClass('hidden');
		$subjalasat.find('.list-subjalasat option[value="'+val+'"]').prop('disabled', true);
		$subjalasat.find('.list-subjalasat').val("");
		$subjalasat.find('#description-'+xval[0]+'-'+xval[1]).text(text);
		$player = $subjalasat.find('#player-'+xval[0]+'-'+xval[1]);
		$counter = $player.data('counter');
		if($duration == 0){
			var axid = document.getElementById("myAudio"+id);
			$duration = axid.duration;
			console.error(axid);
		}
		console.error([$duration,'#myAudio'+$counter]);
		$subjalasat.find('#save-'+$counter).val(1);
		if(typeof(text)!='undefined'){

			$subjalasat.find('#startPos-'+xval[0]+'-'+xval[1]).val(startPos);
			$subjalasat.find('#endPos-'+xval[0]+'-'+xval[1]).val(endPos);
			
			startPercent = 100*startPos/$duration;
			startPercent = startPercent.toFixed(1);

			endPercent = 100*endPos/$duration;
			endPercent = endPercent.toFixed(1);

			$subjalasat.find('#inverse-left-'+$counter).css('width',startPercent+'%');
			$subjalasat.find('#thumb-left-'+$counter).css('left',(startPercent - startPercent*0.04)+'%');
			$subjalasat.find('#value-left-'+$counter).html(startPos);
			$subjalasat.find('#value-left-'+$counter).css('left',(startPercent - startPercent*0.04 + 1)+'%');
			$subjalasat.find('#sign-left-'+$counter).css('left',(startPercent - startPercent*0.04 + 1)+'%');

			$subjalasat.find('#range-'+$counter).css({'left':startPercent+'%','right':(100 - endPercent)+'%'});

			$subjalasat.find('#inverse-right-'+$counter).css('width',(100 - endPercent)+'%');
			$subjalasat.find('#thumb-right-'+$counter).css('left',(endPercent - endPercent*0.04)+'%');
			$subjalasat.find('#value-right-'+$counter).html(endPos);
			$subjalasat.find('#value-right-'+$counter).css('left',(endPercent*$duration/100)+'%');
			$subjalasat.find('#sign-right-'+$counter).css('left',(endPercent - 1)+'%');

			$subjalasat.find('#range_0_'+$counter).val(startPercent*10);
			$subjalasat.find('#range_1_'+$counter).val(endPercent*10);

			$('#startPos-'+$counter).val(startPos);
			$('#endPos-'+$counter).val(endPos);
			$subjalasat.find('#duration-'+$counter).val($duration);

		}
	}
	function sevSubJalasat(btn){
		var $subjalasat = $('#subjalasat');
		$subjalasat.find('.hidden').remove();
		$(btn).addClass('l w');
		var form = $(btn).closest('form');
		var data = $(form).serialize();

		$.ajax({
			type: "POST",
			url: 'admin/api/SaveSubJalasat/'+subjalasatid,
			data: data,
			dataType: "json",
			success: function (data) {
				if (data == "login")
				{
					login(function () {
						save_jalasat(btn)
					});
					return;
				}
				else
				{
					$(btn).closest('form').find('.ajax-result').html(get_alert(data));
					notify(data.msg, data.status);
					if(data.status == 0){
						setTimeout(function(){location.reload();},100);
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
<style>
	.book-part.has-image .add-image,
	.book-part.has-pdf .add-pdf,
	.book-part.has-audio .add-audio,
	.book-part.has-video .add-video
	{
		color: #0BB0E7;
	}
	.rounded{border-radius:5px;}
	.borderTop{border-top:1px solid #CCCCCC;}
	.ml-2 {margin-left: 0.5rem !important;}
	.h-controll{height:100px;overflow:auto;}
	audio {
		display: none;
	}
	progress {
		width: 100%;
		position: absolute;
		z-index: 1;
		left: 0;
		top: 15px;
		cursor:pointer;
	}
	.container-player{
		border:1px solid #C3C3C3;
		height:45px;
		width:100%;
		position:relative;
	}
	.container-player img{
		width: 35px;
		height: 35px;
		float:left;
		margin: 5px;
		cursor:pointer;
	}
	.container-player img.none{
		display:none;
	}
	.container-player [slider="true"] {
		
	}
	[slider="true"] {
		position: relative;
		height: 14px;
		border-radius: 10px;
		text-align: left;
		margin: 10px 0 10px 45px;
		right: 0;
		width: calc(100% - 50px);
		background-color: blue;
	}
	
	[slider="true"] > div {
		position: relative;
		height: 14px;
		margin-right: 36px;
		width:100%;
	}
	
	[slider="true"] > div > [inverse-left="true"] {
	  position: absolute;
	  left: 0;
	  height: 14px;
	  border-radius: 10px;
	  background-color: #CCC;
	  margin: 0;
	}
	
	[slider="true"] > div > [inverse-right="true"] {
	  position: absolute;
	  right: 0;
	  height: 14px;
	  border-radius: 10px;
	  background-color: #CCC;
	  margin: 0;
	}
	
	[slider="true"] > div > [range="true"] {
	  position: absolute;
	  left: 0;
	  height: 14px;
	  border-radius: 14px;
	  background-color: #C14343;
	}
	
	[slider="true"] > div > [thumb="true"] {
	  position: absolute;
	  top: -7px;
	  z-index: 2;
	  height: 28px;
	  width: 28px;
	  text-align: left;
	  margin-left: 0;
	  cursor: pointer;
	  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.4);
	  background-color: #FFF;
	  border-radius: 50%;
	  outline: none;
	}
	
	[slider="true"] > input[type="range"] {
	  position: absolute;
	  pointer-events: none;
	  -webkit-appearance: none;
	  z-index: 3;
	  height: 14px;
	  top: -2px;
	  width: 100%;
	  -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
	  filter: alpha(opacity=0);
	  -moz-opacity: 0;
	  -khtml-opacity: 0;
	  opacity: 0;
	  left:0;
	  cursor:pointer;
	}
	
	div[slider="true"] > input[type="range"]::-ms-track {
	  -webkit-appearance: none;
	  background: transparent;
	  color: transparent;
	}
	
	div[slider="true"] > input[type="range"]::-moz-range-track {
	  -moz-appearance: none;
	  background: transparent;
	  color: transparent;
	}
	
	div[slider="true"] > input[type="range"]:focus::-webkit-slider-runnable-track {
	  background: transparent;
	  border: transparent;
	}
	
	div[slider="true"] > input[type="range"]:focus {
	  outline: none;
	}
	
	div[slider="true"] > input[type="range"]::-ms-thumb {
	  pointer-events: all;
	  width: 28px;
	  height: 28px;
	  border-radius: 0px;
	  border: 0 none;
	  background: red;
	}
	
	div[slider="true"] > input[type="range"]::-moz-range-thumb {
	  pointer-events: all;
	  width: 28px;
	  height: 28px;
	  border-radius: 0px;
	  border: 0 none;
	  background: red;
	}
	
	div[slider="true"] > input[type="range"]::-webkit-slider-thumb {
	  pointer-events: all;
	  width: 28px;
	  height: 28px;
	  border-radius: 0px;
	  border: 0 none;
	  background: red;
	  -webkit-appearance: none;
	}
	
	div[slider="true"] > input[type="range"]::-ms-fill-lower {
	  background: transparent;
	  border: 0 none;
	}
	
	div[slider="true"] > input[type="range"]::-ms-fill-upper {
	  background: transparent;
	  border: 0 none;
	}
	
	div[slider="true"] > input[type="range"]::-ms-tooltip {
	  display: none;
	}
	
	[slider="true"] > div > [sign="true"] {
	  opacity: 0;
	  position: absolute;
	  margin-left: -11px;
	  top: -39px;
	  z-index:3;
	  background-color: #C14343;
	  color: #fff;
	  width: 28px;
	  height: 28px;
	  border-radius: 28px;
	  -webkit-border-radius: 28px;
	  align-items: center;
	  -webkit-justify-content: center;
	  justify-content: center;
	  text-align: center;
	}
	
	[slider="true"] > div > [sign="true"]:after {
	  position: absolute;
	  content: '';
	  left: 0;
	  border-radius: 16px;
	  top: 19px;
	  border-left: 14px solid transparent;
	  border-right: 14px solid transparent;
	  border-top-width: 16px;
	  border-top-style: solid;
	  border-top-color: #C14343;
	}
	
	[slider="true"] > div > [sign="true"] > span {
	  font-size: 12px;
	  font-weight: 700;
	  line-height: 28px;
	}
	
	[slider="true"]:hover > div > [sign="true"] {
	  opacity: 1;
	}
</style>
