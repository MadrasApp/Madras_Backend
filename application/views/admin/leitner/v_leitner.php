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

$cols['کاربر'] =
    array(
        'field_name' => 'username',
        'link' => false,
		'html'=>'[FLD]',
    );
$cols['نام جعبه'] =
    array(
        'field_name' => 'ltitle',
        'link' => false,
		'html'=>'[FLD]',
    );
$cols['گروه بندی'] =
    array(
        'field_name' => 'catid',
		'function'=>function($col,$row)
		{
			$id = $row['id'];
			$fields['1'] = 'یادداشت';
			$fields['2'] = 'لغت';
			$fields['3'] = 'سوال تستی';
			$fields['4'] = 'سوال تشریحی';
			return isset($fields[$id])?$fields[$id]:'نامشخثص';
		},
    );
$cols['متن'] =
    array(
        'field_name' => 'title',
        'link' => true,
		'html'=>'<div class="wb" style="text-align:right;font-size:13px;"><a href="'.site_url("admin/leitner/editLeitner/[ID]").'" id="book_[ID]">[FLD]</a></div>',
		'th-attr' => 'style="width:60%"'
    );

$cols['تاریخ'] =
    array(
        'field_name' => 'regdate',
        'link' => true,
        'type' => 'date',
        'th-attr' => 'style="width:150px"'
    );


if (count($options) > 0)
    $cols['  '] = array('field_name' => 'id', 'type' => 'op', 'items' => $options, 'td-attr' => 'align="center" width="30px" style="padding:0;width:30px;"');

echo $searchHtml;

$q =
	"SELECT c.*,d.title AS ltitle,u.username AS username
	FROM ci_leitner c
	LEFT JOIN ci_leitbox d ON c.lid=d.id
	LEFT JOIN ci_users u ON c.user_id=u.id
	$query";
?>
<?php $inc->createTable($cols, $q, 'id="table" class="table light2" ', $tableName, 60); ?>

<?php $canReply = false ?>

<div class="hidden">
    <div class="view-sample">
        <div class="row">
			<div class="form-group">
				<div data-field="title" class="update-el"></div>
			</div>
			<p>پاسخ ها</p>
			<hr/>
				<div class="result"></div>
			</div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function () {

    });

    function view_leitner(btn, id) {
        var $html = $('<div/>', {'id': 'edit-leitner'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

        $.ajax({
            type: "POST",
            url: 'admin/api/getLeitnerInfo/' + id,
            dataType: "json",
            success: function (data) {

                if (data == "login") {
                    popupScreen('');
                    login(function () {
                        edit_leitner(btn, id);
                    });
                    return;
                }

                if (!data.done) {
                    $html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
                    return;
                }
                var $view = $('.view-sample').clone(true);
                $view.find('.update-el').each(function (i, el) {
                    var val = data.info[i][$(el).data('field')];
					//alert($(el).data('field')+":"+val);
                    $(el).html(val);
                });
				var el = data.info;
				for(i=0;i < el.length;i++){
					console.error(el[i]);
                    //var val = data.info[i][$(el).data('field')];
					if(typeof(el[i]['title'])=='string'){
						html = (i?"<strong>"+i+". </strong>":"")+el[i]['title'];
                    	$view.find('.update-el').append(html+'<hr />');
					}
                }

                $html.html($view);
            },
            error: function () {
                $html.html('<h3 class="text-warning text-center">Conection Error</h3>');
            }
        });
    }

</script>
