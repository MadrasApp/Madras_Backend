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
    'شماره' => array(
        'field_name' => 'id',
        'link' => false,
        'td-attr' => 'align="center" style="width:100px"'
    ),
    'نام' => array(
        'field_name' => 'name',
        'link' => false,
        'html' => '<div class="wb" style="font-size:13px">[FLD]</div>',
        'max' => 50
    ),
);
if (count($options) > 0)
    $cols['  '] = array('field_name' => 'id', 'type' => 'op', 'items' => $options, 'td-attr' => 'align="center" width="30px" style="padding:0;width:30px;"');
?>

<?php if( $this->user->can('edit_group') ): ?>

    <div class="col-sm-12 text-center">
        <button class="btn btn-primary" onclick="$(this).next().slideToggle()">افزودن</button>
        <form style="max-width: 300px;display: none;margin: auto;">
            <hr/>
            <div class="form-group">
                <input type="text" id="group-name" class="form-control" placeholder="نام گروه">
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-info" onclick="add_group(this)" >ثبت</button>
            </div>
            <div class="ajax-result"></div>
        </form>
    </div>

<?php endif ?>

<?php $inc->createTable($cols, $query, 'id="table" class="table light2" ', $tableName, 60); ?>
<script>

    function add_group(btn)
    {
        var name = $.trim($('#group-name').val());

        if( name == '' )
        {
            $('#group-name').focus();
            return;
        }
        $(btn).addClass('l w h6');

        $.ajax({
            type: "POST",
            url: 'admin/api/addGroup',
            data: {name:name},
            dataType: "json",
            success: function (data) {

                if (data == "login")
                {
                    login(function(){add_group(btn)});
                    return;
                }
                if (data.done)
                    location.reload();
                else
                    $(btn).closest('form').find('.ajax-result').html(get_alert(data));
                $(btn).removeClass('l w');
            },
            error: function () {
                $(btn).removeClass('l w');
                notify('خطا در اتصال', 2);
            }
        });
    }

</script>