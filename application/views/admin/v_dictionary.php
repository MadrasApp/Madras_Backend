<?php
/**
 * Created by Talkhabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 12:05 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('inc');

$inc = new inc;

$cols['زبان اصلی'] =
    array(
        'field_name' => 'fromlangX',
        'link' => true
    );

$cols['زبان ترجمه'] =
    array(
        'field_name' => 'tolangX',
        'link' => true
    );

$cols['لغت'] =
    array(
        'field_name' => 'kalameh',
        'link' => true
    );

$cols['ترجمه'] =
    array(
        'field_name' => 'translate',
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
			'html'    => '<i class="fa fa-edit text-success cu" onClick="edit_row(this,[ID])"></i>'
		);
$cols['حذف'] =
    array(
			'field_name' => 'id',
			'link'    => false,
			'html'    => '<i class="fa fa-trash text-danger cu" onClick="delete_row(this,\'dictionary\',[ID])"></i>'
		);
$cols['پیشنهاد'] =
    array(
        'field_name' => 'offer',
        'link' => true,
		'html'    => '<i class="fa fa-key text-success cu" onClick="edit_offer(this,[ID])"> [FLD] </i>'
    );


$q ="SELECT c.*,d.title fromlangX,e.title tolangX FROM ci_dictionary c INNER JOIN ci_diclang d ON d.id=c.fromlang INNER JOIN ci_diclang e ON e.id=c.tolang $query";
$diclangs = $this->db->order_by('title','asc')->get('diclang')->result();
	echo $searchHtml;
?>

<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">
		لغتنامه 
		<a class="btn-sm btn-warning pull-left" onclick="new_dictionary();">لغت جدید</a>
		<div class="clearfix"></div>
	</h3>
  </div>
</div>


<?php $inc->createTable($cols, $q, 'id="table" class="table light2" ', $tableName, 60); ?>

<?php $canReply = false ?>

<div class="hidden">

    <div class="view-offer">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
				<table class="table btn-success">
				<tr>
					<th>لغت</th>
					<th>زبان اصلی</th>
					<th>زبان ترجمه</th>
				</tr>
				<tr>
					<th class="kalameh h3"></th>
					<th class="fromlang h3"></th>
					<th class="tolang h3"></th>
				</tr>
				<tr>
					<th>ترجمه فعلی</th>
					<th colspan="2" class="translate h3"></th>
				</tr>
				</table>
				<hr />
				<table class="table">
					<thead>
						<tr>
							<th class="text-center center">#</th>
							<th>ترجمه</th>
							<th class="text-center center">جایگزین</th>
							<th class="text-center center">حذف</th>
						</tr>
					</thead>
					<tbody class="resultdata">
					
					</tbody>
				</table>
            </div>	
        </div>
    </div>

    <div class="view-sample">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <form class="clearfix">
                    <div class="form-group col-sm-6">
                        <p>زبان اصلی</p>
                        <select dir="ltr" name="fromlang" class="form-control update-el fromlang">
						<?php foreach($diclangs as $k=>$v){ ?>
						<option value="<?php echo $v->id?>"><?php echo $v->title?></option>
						<?php } ?>
						</select>
                    </div>
                    <div class="form-group col-sm-6">
                        <p>زبان ترجمه</p>
                        <select dir="ltr" name="tolang" class="form-control update-el tolang">
						<?php foreach($diclangs as $k=>$v){ ?>
						<option value="<?php echo $v->id?>"><?php echo $v->title?></option>
						<?php } ?>
						</select>
                    </div>
                    <div class="form-group">
                        <p>لغت</p>
                        <input type="text" dir="ltr" name="kalameh" class="form-control update-el kalameh">
                    </div>
                    <div class="form-group">
                        <p>ترجمه</p>
                        <textarea name="translate" class="form-control update-el translate" rows="4"></textarea>
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

<script type="text/javascript">

    $(document).ready(function () {

    });

    function new_dictionary() {
        var $html = $('<div/>', {'id': 'edit-comment'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

		var $view = $('.view-sample').clone(true);

		$view.find('.sample-edit').on('click', function () {
			save_dictionary(this);
		});
		$html.html($view);
    }
    function edit_row(that,id){
		$tr = $(that).closest('tr');
		$td0 = $($tr.find('td')[2]).html();
		$td1 = $($tr.find('td')[3]).html();
        var $html = $('<div/>', {'id': 'edit-comment'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

		var $view = $('.view-sample').clone(true);

		$view.find('.sample-edit').on('click', function () {
			save_dictionary(this);
		});

		$view.find('.kalameh').val($td0);
		$view.find('.translate').val($td1);
		$view.find('.id').val(id);
		$html.html($view);
    }
    function edit_offer(that,id){
		$tr = $(that).closest('tr');
		$td1 = $($tr.find('td')[0]).html();
		$td2 = $($tr.find('td')[1]).html();
		$td3 = $($tr.find('td')[2]).html();
		$td4 = $($tr.find('td')[3]).html();
        var $html = $('<div/>');
        popupScreen($html);
		var $view = $('.view-offer').clone(true);

		$view.find('.fromlang').html($td1);
		$view.find('.tolang').html($td2);
		$view.find('.kalameh').html($td3);
		$view.find('.translate').html($td4);
		var resultdata = $view.find('.resultdata');
		$.ajax({
			type: "POST",
			url: 'admin/api/LoadDicOffer/' + id,
			dataType: "json",
			success: function (data) {
				if(data.done){
					msg = data.msg;
					for(i=0;i<msg.length;i++){
						resultdata.append('<tr id="datarow-'+msg[i]['id']+'"><td>'+(i+1)+'</td><td>'+msg[i]['translate']+'</td><td><a onclick="ReplaceOffer('+msg[i]['id']+');"><i class="col-md-12 btn btn-success fa fa-check cu"></i></a></td><td><a onclick="DeleteOffer('+msg[i]['id']+');"><i class="fa fa-trash col-md-12 btn btn-danger cu"></i></a></td></tr>');
					}
				}
			}
		});
		/*
		*/
		$html.html($view);
    }
    function DeleteOffer(id){
		$.ajax({
			type: "POST",
			url: 'admin/api/DeleteOffer/' + id,
			dataType: "json",
			success: function (data) {
				if(data.done){
					$('#datarow-'+id).addClass('btn-info');
					$('#datarow-'+id).find('td').each(function(index, element) {
						$(this).animate({
							'height': '0px',
							'padding-top': '0px',
							'padding-bottom': '0px'
						}, function() {
							$(this).children().remove();
						});
					});
					$('#datarow-'+id).hide(400);
				}
			}
		});
    }
    function ReplaceOffer(id){
		$('#datarow-'+id).addClass('btn-info');
		if(!confirm('آیا می خواهید ترجمه انتخابی را جایگزین ترجمه فعلی کنید؟')){
			$('#datarow-'+id).removeClass('btn-info');
			return;
		}
		$.ajax({
			type: "POST",
			url: 'admin/api/ReplaceOffer/' + id,
			dataType: "json",
			success: function (data) {
				alert(data.msg);
				if(data.done){
					$('.close-popup').click();
					location.reload();
				}
			}
		});
    }
    function save_dictionary(btn){
        $(btn).addClass('l w h6');
        var form = $(btn).closest('form');
        var data = $(form).serialize();

        $.ajax({
            type: "POST",
            url: 'admin/api/SaveDictionary',
            data: data,
            dataType: "json",
            success: function (data) {
                if (data == "login")
                {
                    login(function () {
                        save_dictionary(btn)
                    });
                    return;
                }
                else
                {
                    $(btn).closest('form').find('.ajax-result').html(get_alert(data));
                    notify(data.msg, data.status);
					if(data.status == 0)
					location.reload();
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
