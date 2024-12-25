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

$cols['نام'] =
    array(
        'field_name' => 'name',
        'link' => true,
        'html' => '<div class="wb" style="font-size:13px">[FLD]</div>',
        'max' => 50
    );

$cols['پیام'] =
    array(
        'field_name' => 'text',
        'link' => true,
        'html' => '<div class="wb" style="font-size:13px;'.($this->user->can('reply_comment') ?'cursor:pointer;" onClick="view_comment(this,[ID])"':'"').'>[FLD]</div>',
        'max' => 60
    );
	
$cols['رای'] =
	array(
		'field_name'=>'rating',
		'function'=>function($col,$row)
		{
			$R = $col;
			$stars = "";
			for($i=1;$i<=5;$i++)
			{
				$cls = $i <= $R ? 'star':'star-o';
				if( $i-1 < $R && $R < $i ) $cls = 'star-half-o';
				$stars .=  '<i class="fa fa-'.$cls.'"></i>' ."\n";
			}
			return $stars;
		},
		'td-attr'=>'align="center" style="width:170px;color:gold"'
	);
/*
if( $this->user->can('submit_comment') )
    $cols['تایید شده'] =
        array(
            'field_name'=>'submitted',
            'function'=>function($col,$row)
            {

                $id = $row['id'];
                $checked = $col == 1 ? 'checked':'';
                $col =  '<input id="cmn-tg-'.$id.'" class="cmn-toggle cmrf chk-tg-field" value="'.$id.'"
                         data-t="comments" data-f="submitted" type="checkbox" '.$checked.'>
                         <label for="cmn-tg-'.$id.'"></label>';
                return $col;
            },
            'td-attr'=>'align="center" style="width:70px;"'
        );
else
    $cols['تایید شده'] =
        array(
            'field_name'=>'submitted',
            'function'=>function($col,$row)
            {

                $submitted =
                    $row['submitted'] == 1 ?
                        '<i class="fa fa-check-circle fa-lg text-success" title="تایید شده"></i>' :
                        '<i class="fa fa-times-circle fa-lg text-warning" title="تایید نشده"></i>';
                return $submitted;
            },
            'td-attr'=>'align="center" style="width:70px;"'
        );
*/

$cols['تاریخ'] =
    array(
        'field_name' => 'date',
        'link' => true,
        'type' => 'date',
        'th-attr' => 'style="width:150px"'
    );


if (count($options) > 0)
    $cols['  '] = array('field_name' => 'id', 'type' => 'op', 'items' => $options, 'td-attr' => 'align="center" width="30px" style="padding:0;width:30px;"');

//echo $searchHtml;

$q =
	"SELECT c.*, r.rating

	FROM ci_comments c
	
	INNER JOIN `ci_comment_rate` `cr` ON `cr`.`comment_id`=`c`.`id` 
	
	INNER JOIN `ci_rates` `r` ON `r`.`id`=`cr`.`rate_id` 
	
	$query";
?>

<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">گزارش</h3>
  </div>
  <div class="panel-body">
	<h3 style="margin:0">
    تعداد کل آرا : <b class="en"><?php echo  $rating->total_rates ?></b>
	<i class="fa fa-minus" style="margin:0 10px"></i>
    میانگین : <b class="en"><?php echo  $rating->app_rating ?></b>
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
                        <p>نام</p>
                        <input type="text" name="name" class="form-control update-el">
                    </div>
                    <div class="form-group">
                        <p>ایمیل</p>
                        <input type="email" name="email" class="form-control update-el">
                    </div>
                    <div class="form-group">
                        <p>نظر</p>
                        <textarea name="text" class="form-control update-el" rows="4"></textarea>
                    </div>
                    <hr/>
                    <div class="ajax-result" style="margin-bottom: 20px;"></div>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary btn-block btn-lg sample-edit">
                            <i class="fa fa-check-circle"></i> <span>ویرایش</span>
                        </button>
                    </div>
                </form>
            </div>	
            <?php if($canReply): ?>
            <div class="col-sm-6">
                <form>
                    <div class="form-group">
                        <p>پاسخ</p>
                        <textarea name="r-text" class="form-control update-el" rows="8"></textarea>
                    </div>
                    <hr/>
                    <div class="ajax-result" style="margin-bottom: 20px;"></div>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary btn-block btn-lg sample-reply">
                            <i class="fa fa-send"></i> <span>ارسال</span>
                        </button>
                    </div>
                </form>
            </div>
            <?php endif ?>
        </div>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function () {

    });

    function view_comment(btn, id) {
        var $html = $('<div/>', {'id': 'edit-comment'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

        $.ajax({
            type: "POST",
            url: 'admin/api/getCommentInfo/' + id,
            dataType: "json",
            success: function (data) {

                if (data == "login") {
                    popupScreen('');
                    login(function () {
                        edit_comment(btn, id);
                    });
                    return;
                }

                if (!data.done) {
                    $html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
                    return;
                }
                var $view = $('.view-sample').clone(true);

                $view.find('.update-el').each(function (i, el) {
                    var val = data.info[$(el).attr('name')];
                    $(el).val(val).data('prevdata',val);
                });

                $view.find('.sample-edit').on('click', function () {
                    update_comment(this, id);
                });
                $view.find('.sample-reply').on('click', function () {
                    reply_comment(this, id);
                });

                $html.html($view);
            },
            error: function () {
                $html.html('<h3 class="text-warning text-center">Conection Error</h3>');
            }
        });
    }

    function update_comment(btn,id)
    {
        $(btn).addClass('l w h6');
        var form = $(btn).closest('form');
        var data = $(form).serialize();

        $.ajax({
            type: "POST",
            url: 'admin/api/updateComment/' + id,
            data: data,
            dataType: "json",
            success: function (data) {
                if (data == "login")
                {
                    login(function () {
                        update_comment(btn,id)
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

    function reply_comment(btn,id)
    {
        $(btn).addClass('l w h6');
        var form = $(btn).closest('form');
        var data = $(form).serialize();

        $.ajax({
            type: "POST",
            url: 'admin/api/replyComment/' + id,
            data: data,
            dataType: "json",
            success: function (data) {
                if (data == "login")
                {
                    login(function () {
                        reply_comment(btn,id)
                    });
                    return;
                }
                else
                {
                    $(btn).closest('form').find('.ajax-result').html(get_alert(data));
                    notify(data.msg, data.status);
                }
                $(btn).removeClass('l w');
                console.log(data);
            },
            error: function () {
                $(btn).removeClass('l w');
                notify('خطا در اتصال', 2);
            }
        });
    }

</script>
