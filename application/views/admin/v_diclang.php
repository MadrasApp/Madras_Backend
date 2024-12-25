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

$cols['زبان'] =
    array(
        'field_name' => 'title',
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
			'html'    => '<i class="fa fa-trash text-danger cu" onClick="delete_row(this,\'diclang\',[ID])"></i>'
		);


$q ="SELECT c.* FROM ci_diclang c $query";
?>

<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">
		زبانهای ترجمه 
		<a class="btn-sm btn-warning pull-left" onclick="new_diclang();">زبان جدید</a>
		<div class="clearfix"></div>
	</h3>
  </div>
</div>


<?php $inc->createTable($cols, $q, 'id="table" class="table light2" ', $tableName, 60); ?>

<?php $canReply = false ?>

<div class="hidden">
    <div class="view-sample">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <form class="clearfix">
                    <div class="form-group">
                        <p>زبان</p>
                        <input type="text" dir="ltr" name="title" class="form-control update-el title">
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

    function new_diclang() {
        var $html = $('<div/>', {'id': 'edit-comment'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

		var $view = $('.view-sample').clone(true);

		$view.find('.sample-edit').on('click', function () {
			save_diclang(this);
		});
		$html.html($view);
    }
    function edit_row(that,id){
		$tr = $(that).closest('tr');
		$td0 = $($tr.find('td')[0]).html();
		$td1 = $($tr.find('td')[1]).html();
        var $html = $('<div/>', {'id': 'edit-comment'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

		var $view = $('.view-sample').clone(true);

		$view.find('.sample-edit').on('click', function () {
			save_diclang(this);
		});

		$view.find('.title').val($td0);
		$view.find('.id').val(id);
		$html.html($view);
    }
    function save_diclang(btn){
        $(btn).addClass('l w h6');
        var form = $(btn).closest('form');
        var data = $(form).serialize();

        $.ajax({
            type: "POST",
            url: 'admin/api/SaveDiclang',
            data: data,
            dataType: "json",
            success: function (data) {
                if (data == "login")
                {
                    login(function () {
                        save_diclang(btn)
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
