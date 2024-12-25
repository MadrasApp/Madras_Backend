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

$cols = array(
    '    ' => array(
        'field_name' => 'visited',
        'link' => true,
        'function' => function ($col, $row) {
            $html = $col == 0 ? '<i class="fa fa-envelope fa-lg text-info"></i>' : '';
            $html = '<span class="not-visited-msg" title="جدید">' . $html . '</span>';

            $replyed =
                $row['ansver'] != '' ?
                    '<i class="fa fa-check-circle fa-lg text-success" title="پاسخ داده شده"></i>' :
                    '<i class="fa fa-times-circle fa-lg text-warning" title="پاسخ داده نشده"></i>';
            return $html . ' &nbsp <span class="replyed-msg">' . $replyed . '</span>';
        },
        'max' => 100
    ),
    'نام' => array(
        'field_name' => 'name',
        'link' => true,
        'html' => '<div class="wb" style="font-size:13px">[FLD]</div>',
        'max' => 50
    ),
    'ایمیل' => array(
        'field_name' => 'email',
        'link' => true,
        'html' => '<div class="wb" style="font-size:13px"><a href="mailto:[FLD]">[FLD]</a></div>',
    ),
    'موضوع' =>
        array(
            'field_name' => 'subject',
            'link' => true,
            'html' => '<div class="wb" style="font-size:13px;cursor: pointer" onclick="view_msg(this,[ID])">[FLD]</div>',
            'max' => 100
        ),
    'پیام' => array(
        'field_name' => 'message',
        'link' => true,
        'html' => '<div class="wb" style="font-size:13px;">[FLD]</div>',
        'max' => 100
    ),

    'تاریخ' => array(
        'field_name' => 'date',
        'link' => true,
        'type' => 'date',
        'th-attr' => 'style="width:150px"'
    ),
);
if (count($options) > 0)
    $cols['  '] = array('field_name' => 'id', 'type' => 'op', 'items' => $options, 'td-attr' => 'align="center" width="30px" style="padding:0;width:30px;"');

echo $searchHtml;

if (isset($_tabs))
    foreach ($_tabs as $tab => $tab_data) {
        $href = site_url("admin/messages/index/" . $tab);
        $class = $this->uri->segment(4) == $tab ? "active" : "";

        echo "<a href='$href' class='btn btn-primary $class'>
              <span>  " . $tab_data['name'] . " </span> &nbsp
              <span class='badge row-count row-$tab'>" . $tab_data['count'] . "</span>
              </a>";

    }

$inc->createTable($cols, $query, 'id="table" class="table light2" ', $tableName, 60);
?>
<?php $canReply = $this->user->can('reply_msg') ?>
<div class="hidden">
    <div id="view-sample">
        <div class="row">
            <form class="clearfix">
                <div class="col-sm-<?php echo  $canReply ? 6 : 12 ?>">
                    <h4 class="sample-name text-overflow col-sm-6"></h4>
                    <h5 class="sample-email text-muted en col-sm-6"></h5>
                    <div class="clearfix"></div>

                    <div class="col-sm-12">
                        <hr/>
                        <h3 class="sample-subject"></h3>
                        <hr/>
                        <div class="sample-message"></div>
                    </div>

                </div>
                <?php if ($canReply): ?>
                    <div class="col-sm-6">
                        <input type="text" name="subject" value="<?php echo  $title ?> ::: پاسخ ایمیل"
                               class="form-control sample-subject if-change" placeholder="موضوع" title="موضوع">
                        <hr/>
                        <textarea name="ansver" id="msg-editor" class="sample-ansver if-change"></textarea>
                        <hr/>
                        <div class="ajax-result" style="margin-bottom: 20px;"></div>
                        <div style="margin-bottom: 20px;">
                            <button type="button" class="btn btn-primary btn-block btn-lg sample-send"><i
                                    class="fa fa-reply-all"></i> <span>ارسال پیام</span></button>
                        </div>
                    </div>
                <?php endif ?>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function (e) {


    });

    function view_msg(btn, id) {
        var $html = $('<div/>', {'id': 'view-msg'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

        $.ajax({
            type: "POST",
            url: 'admin/api/messageInfo/' + id,
            data: {},
            dataType: "json",
            success: function (data) {

                if (data == "login")
                {
                    popupScreen('');
                    login(function () {
                        view_msg(btn, id)
                    });
                    return;
                }

                if (!data.done) {
                    $html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
                    return;
                }
                var $view = $('#view-sample').clone(true);

                $view.find('.sample-name').html(data.info.name);
                $view.find('.sample-email').html(data.info.email);
                $view.find('.sample-subject').html(data.info.subject);
                $view.find('.sample-message').html(data.info.message);
                $view.find('.sample-ansver').html(data.info.ansver).attr('id', 'msg_editor');
                $view.find('.sample-send').on('click', function () {
                    var tr = $(btn).closest('tr');
                    reply_msg(this, tr, id);
                });
                $html.html($view);

                CKEDITOR.replace('msg_editor', {
                    toolbar: [
                        {name: 'document', items: ['NewPage', 'Preview']},
                        {
                            name: 'clipboard',
                            items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']
                        },
                        {name: 'insert', items: ['Image', 'Flash', 'Table', 'HorizontalRule', 'SpecialChar']},
                        '/',
                        {name: 'styles', items: ['Styles', 'Format']},
                        {name: 'basicstyles', items: ['Bold', 'Italic', 'Strike', '-']},
                        {
                            name: 'paragraph',
                            items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']
                        },
                        {name: 'links', items: ['Link', 'Unlink']},
                        {name: 'tools', items: ['Maximize', '-', 'About']}
                    ]
                });

                CKEDITOR.instances.msg_editor.on('change', function (e) {
                    CKEDITOR.instances.msg_editor.updateElement();
                    $('#msg_editor').trigger('change');
                });

                $('.full-screen').css('z-index', '100');

                $(btn).closest('tr').find('.not-visited-msg').remove();
            },
            error: function () {
                $html.html('<h3 class="text-warning text-center">Conection Error</h3>');
            }
        });
    }

    function reply_msg(btn, tr, id) {
        $(btn).addClass('l w h6');
        CKEDITOR.instances.msg_editor.updateElement();
        var data = $(btn).closest('form').serialize();

        $.ajax({
            type: "POST",
            url: 'admin/api/replyMsg/' + id,
            data: data,
            dataType: "json",
            success: function (data) {

                if (data == "login") 
                {
                    login(function () {
                        reply_msg(btn, tr, id)
                    });
                    return;
                }
                if (data.done) {
                    $(btn).closest('.full-screen').fadeOut(300, function () {
                        $(this).remove();
                    });
                    $(tr).find('.replyed-msg').html('<i data-title="پاسخ داده شده" class="fa fa-check-circle fa-lg text-success"></i>');
                    notify(data.msg, data.status);
                }
                else {
                    $(btn).closest('form').find('.ajax-result').html(get_alert(data));
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