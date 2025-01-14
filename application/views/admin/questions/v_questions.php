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

$cols['گروه بندی'] =
    array(
        'field_name' => 'dtitle',
        'link' => false,
		'html'=>'[FLD]',
		'th-attr' => 'style="width:20%"'
    );
$cols['متن پشتیبانی'] =
    array(
        'field_name' => 'content',
        'link' => true,
		'html'=>'<div class="wb" style="text-align:right;font-size:13px;"><a href="'.site_url("admin/questions/editQuestion/[ID]").'" id="book_[ID]">[FLD]</a></div>',
		'th-attr' => 'style="width:60%"'
    );

if( $this->user->can('submit_question') )
    $cols['تایید شده'] =
        array(
            'field_name'=>'published',
            'function'=>function($col,$row)
            {

                $id = $row['id'];
                $checked = intval($col)? 'checked':'';
                $col =  '<input id="cmn-tg-'.$id.'" class="cmn-toggle cmrf chk-tg-field" value="'.$id.'"
                         data-t="questions" data-f="published" type="checkbox" '.$checked.'>
                         <label for="cmn-tg-'.$id.'"></label>';
                return $col;
            },
            'td-attr'=>'align="center" style="width:70px;"'
        );
	else
    $cols['تایید شده'] =
        array(
            'field_name'=>'published',
            'function'=>function($col,$row)
            {

                $published =
                    $row['published'] == 1 ?
                        '<i class="fa fa-check-circle fa-lg text-success" title="تایید شده"></i>' :
                        '<i class="fa fa-times-circle fa-lg text-warning" title="تایید نشده"></i>';
                return $published;
            },
            'td-attr'=>'align="center" style="width:70px;"'
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
	"SELECT c.*,d.title AS dtitle
	FROM ci_questions c
	LEFT JOIN ci_catquest d ON c.catid=d.id
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

    function view_question(btn, id) {
        var $html = $('<div/>', {'id': 'edit-question'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

        $.ajax({
            type: "POST",
            url: 'admin/api/getQuestionInfo/' + id,
            dataType: "json",
            success: function (data) {

                if (data == "login") {
                    popupScreen('');
                    login(function () {
                        edit_question(btn, id);
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
					if(typeof(el[i]['content'])=='string'){
						html = (i?"<strong>"+i+". </strong>":"")+el[i]['content'];
						if(el[i]['image'] && el[i]['image'].length)
							html+='<br /><img src="'+el[i]['image']+'" />';
                    	$view.find(parseInt(el[i]['qid'])?'.result':'.update-el').append(html+'<hr />');
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
