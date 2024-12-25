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

$cols['گیرنده'] =
    array(
        'field_name' => 'mobile',
        'link' => true,
        'html' => '<div class="wb" style="font-size:13px">[FLD]</div>',
        'max' => 50
    );

$cols['پیام'] =
    array(
        'field_name' => 'message',
        'link' => true
    );
	
$cols['بخش ارسال کننده'] =
	array(
		'field_name'=>'side',
		'function'=>function($col,$row)
		{
			$section = '<span class="btn-sm col-md-12 btn-danger">نامشخص</span>';
			switch($col){
				case 1:$section = '<span class="btn-sm col-md-12 btn-primary">برنامه موبایل</span>';break;
				case 2:$section = '<span class="btn-sm col-md-12 btn-success">احراز هویت</span>';break;
				case 3:$section = '<span class="btn-sm col-md-12 btn-info">مدیریت</span>';break;
			}
			return $section;
		},
		'td-attr'=>'align="center" style="width:170px;"'
	);

$cols['وضعیت ارسال'] =
	array(
		'field_name'=>'status',
		'function'=>function($col,$row)
		{
			$section = '<span class="btn-sm col-md-12 btn-danger">ارسال نشده</span>';
			switch($col){
				case 1:$section = '<span class="btn-sm col-md-12 btn-success">ارسال شده</span>';break;
			}
			return $section;
		},
		'td-attr'=>'align="center"'
	);

$cols['مدت زمان گذشته'] =
    array(
        'field_name' => 'regdate',
        'link' => true,
        'type' => 'date',
        'th-attr' => 'style="width:150px"'
    );


if (count($options) > 0)
    $cols['  '] = array('field_name' => 'id', 'type' => 'op', 'items' => $options, 'td-attr' => 'align="center" width="30px" style="padding:0;width:30px;"');

$q ="SELECT c.* FROM ci_sended c $query";
?>

<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">
		پیامکهای ارسالی 
		<a class="btn-sm btn-warning pull-left" onclick="new_payamak();">پیامک جدید</a>
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
                        <p>شماره همراه گیرنده</p>
                        <input type="text" dir="ltr" name="mobile" class="form-control update-el">
                    </div>
                    <div class="form-group">
                        <p>متن پیام</p>
                        <textarea name="message" class="form-control update-el" rows="4"></textarea>
                    </div>
                    <hr/>
                    <div class="ajax-result" style="margin-bottom: 20px;"></div>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary btn-block btn-lg sample-edit">
                            <i class="fa fa-check-circle"></i> <span>ارسال</span>
                        </button>
                    </div>
                </form>
            </div>	
        </div>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function () {

    });

    function new_payamak() {
        var $html = $('<div/>', {'id': 'edit-comment'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

		var $view = $('.view-sample').clone(true);

		$view.find('.sample-edit').on('click', function () {
			save_payamak(this);
		});

		$html.html($view);
    }

    function save_payamak(btn)
    {
        $(btn).addClass('l w h6');
        var form = $(btn).closest('form');
        var data = $(form).serialize();

        $.ajax({
            type: "POST",
            url: 'admin/api/SendUserSMS',
            data: data,
            dataType: "json",
            success: function (data) {
                if (data == "login")
                {
                    login(function () {
                        save_payamak(btn)
                    });
                    return;
                }
                else
                {
                    $(btn).closest('form').find('.ajax-result').html(get_alert(data));
                    notify(data.msg, data.status);
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
