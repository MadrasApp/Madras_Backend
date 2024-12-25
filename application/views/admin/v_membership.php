<?php
/**
 * echo "<pre>";
 * print_r();
 * echo "</pre>";
 * Created by Talkhabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 12:05 PM
 */
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->helper('inc');
$inc = new inc;

$cols['شماره'] =
    array(
        'field_name' => 'id',
        'th-attr' => 'style="width:50px"',
        'link' => true
    );
$cols['عنوان اشتراک'] =
    array(
        'field_name' => 'title',
        'link' => true,
        'type' => 'string',
        'td-attr' => 'style="text-align: right;"'
    );

$cols['هزینه اشتراک'] = array(
    'field_name' => 'price',
    'link' => true,
    'th-attr' => 'style="width:100px"',
    'function' => function ($col, $row) {
        global $extraData;
        $count = $row["price"];
        $html = '<div id="price_' . $row["id"] . '" data-price="' . $count . '">' . number_format($count, 0, '.', ',') . '</div>';
        return $html;
    }
);

$cols['مدت اشتراک'] =
    array(
        'field_name' => 'allowmonths',
        'link' => true,
        'type' => 'string',
        'th-attr' => 'style="width:100px"',
        'function' => function ($col, $row) {
            global $extraData;
            $count = $row["allowmonths"];
            $html = '<div id="allowmonths_' . $row["id"] . '" data-price="' . $count . '">' . "$count ماه " . '</div>';
            return $html;
        }
    );

$cols['تعداد مشترک'] = array(
    'field_name' => 'countmember',
    'link' => true,
    'th-attr' => 'style="width:100px"',
    'function' => function ($col, $row) {
        global $extraData;
        $count = $row["countmember"];
        $html = '<div id="countmember_' . $row["id"] . '" data-countmember="' . $count . '">' . number_format($count, 0, '.', ',') . '</div>';
        return $html;
    }
);

$cols['تاریخ ثبت'] =
    array(
        'field_name' => 'regdate',
        'link' => true,
        'type' => 'date',
        'th-attr' => 'style="width:150px"'
    );
$cols['انتشار'] =
    array(
        'field_name' => 'published',
        'link' => false,
        'th-attr' => 'style="width:50px"',
        'function'=>function($col,$row)
        {
            return $row["published"]?'<i class="fa fa-check text-success fa-2x"></i>':'<i class="fa fa-close text-danger fa-2x"></i>';
        }
    );
$cols['ویرایش'] =
    array(
        'field_name' => 'id',
        'link' => false,
        'th-attr' => 'style="width:50px"',
        'html' => '<i class="fa fa-edit text-success cu" onClick="edit_membership(this,[ID])"></i>'
    );
$cols['حذف'] =
    array(
        'field_name' => 'id',
        'link' => false,
        'th-attr' => 'style="width:50px"',
        'html' => '<i class="fa fa-trash text-danger cu" onClick="delete_membership(this,[ID])"></i>'
    );

$q = "SELECT c.* FROM ci_membership c $query";
echo $searchHtml;
?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            اشتراک ها
            <a class="btn-sm btn-warning pull-left" onclick="new_membership();">اشتراک جدید</a>
            <div class="clearfix"></div>
        </h3>
    </div>
</div>


<?php $inc->createTable($cols, $q, 'id="table" class="table light2" ', $tableName, 60); ?>

<?php $canReply = false ?>

<div class="hidden">
    <div class="view-sample">
        <h2 align="center">ثبت اشتراک</h2>
        <form class="clearfix">
            <div class="row col-md-offset-2">
                <div class="col-md-6">
                    <div class="box">
                        <div class="box-title">مدت اشتراک</div>
                        <div class="box-content">
                            <select name="allowmonths" class="form-control update-el allowmonths">
                                <option value="1">1 ماهه</option>
                                <option value="2">2 ماهه</option>
                                <option value="3">3 ماهه</option>
                                <option value="4">4 ماهه</option>
                                <option value="5">5 ماهه</option>
                                <option value="6">6 ماهه</option>
                                <option value="12">یک ساله</option>
                            </select>
                        </div>
                    </div>
                    <div class="box">
                        <div class="box-title">عنوان اشتراک</div>
                        <div class="box-content">
                            <input name="title" class="form-control update-el title"/>
                        </div>
                    </div>
                    <div class="box">
                        <div class="box-title">هزینه</div>
                        <div class="box-content">
                            <input name="price" class="form-control update-el price"/>
                        </div>
                    </div>
                    <div class="box">
                        <div class="box-title">توضیحات</div>
                        <div class="box-content">
                            <textarea name="description" class="form-control update-el description" rows="4"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="box mb-2">
                        <div class="box-title">ذخیره</div>
                        <div class="box-content">
                            <div class="form-group">
                                <button type="button"
                                        class="btn btn-primary btn-md col-md-5 pull-right m-1 sample-publish">
                                    <i class="fa fa-check-circle"></i> <span>ذخیره</span>
                                </button>
                                <button type="button"
                                        class="btn btn-danger btn-md col-md-5 pull-left m-1 sample-unpublish">
                                    <i class="fa fa-check-circle"></i> <span>بایگانی</span>
                                </button>
                                <div class="clearfix"></div>
                                <div class="ajax-result" style="margin-bottom: 20px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="box media-ap">
                        <div class="box-title"><i class="fa fa-photo"></i> تصویر</div>

                        <div class="box-content" style="padding:2px;text-align:center">
                            <div class="convert-to-form-el editable-img" form-el-name="data[thumb]"
                                 form-el-value="file">
                                <div class="media-ap-data replace center" data-thumb="thumb300" id="temp-my-media-ap"
                                     align="center">
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
                    <!--div class="box mb-2">
                        <div class="box-title">دسته بندی عنوانی درس</div>
                        <div class="box-content">
                            <div class="form-group">

                            </div>
                        </div>
                    </div-->
                </div>
            </div>
            <input type="hidden" name="id" class="update-el id" value="0">
            <input type="hidden" name="published" class="update-el published" value="0">
        </form>
    </div>
</div>
<link type="text/css" rel="stylesheet" href="<?php echo base_url() ?>/js/chosen/chosen.min.css"/>
<script src="<?php echo base_url() ?>/js/chosen/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">

    $(document).ready(function () {
    });

    function new_membership() {
        var $html = $('<div/>', {'id': 'edit-comment'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

        var $view = $('.view-sample').clone(true);

        $view.find('.sample-publish').on('click', function () {
            $view.find('.published').val(1);
            save_membership(this);
        });
        $view.find('.sample-unpublish').on('click', function () {
            $view.find('.published').val(0);
            save_membership(this);
        });
        $view.find('#temp-add-thumb-img').attr('id', 'add-thumb-img');
        $view.find('#temp-my-media-ap').attr('id', 'media-ap');
        $html.html($view);
        $html.find("select").chosen({width: '100%'});
    }

    function edit_membership(that, id) {
        var $html = $('<div/>', {'id': 'edit-membership'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

        $.ajax({
            type: "POST",
            url: 'admin/api/getMembershipInfo/' + id,
            dataType: "json",
            success: function (data) {

                if (data == "login") {
                    popupScreen('');
                    login(function () {
                        edit_membership(btn, id)
                    });
                    return;
                }

                if (!data.done) {
                    $html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
                    return;
                }
                var $view = $('.view-sample').clone(true);

                $view.find('.update-el').each(function (i, el) {
                    var val = data.data[$(el).attr('name')];
                    $(el).val(val).data('prevdata', val);
                });

                $view.find('.sample-publish').on('click', function () {
                    $view.find('.published').val(1);
                    save_membership(this);
                });
                $view.find('.sample-unpublish').on('click', function () {
                    $view.find('.published').val(0);
                    save_membership(this);
                });
                $view.find('#temp-add-thumb-img').attr('id', 'add-thumb-img');
                $view.find('#temp-my-media-ap').attr('id', 'my-media-ap');
                if (data.data['image']) {
                    $thumb = $('<div class="media-ap-data" />');
                    var img = $('<img/>', {src: data.data['image']}).addClass('convert-this img-responsive');
                    $thumb.append(img);
                    console.error([data.data['image'], $view.find('#my-media-ap').length, $view.find('#my-media-ap')]);
                    $view.find('#my-media-ap').html($thumb);
                    $view.find('#add-thumb-img').hide();
                }
                newdata = data.data;
                for (const [item, index] of Object.entries(newdata)) {
                    $view.find('#data_type_' + item).val(index);
                }
                $html.html($view);
                $html.find("[name=active]").trigger('change');
                $("#edit-membership select").chosen({width: '100%'}).trigger("chosen:updated");
            },
            error: function () {
                $html.html('<h3 class="text-warning text-center">Conection Error</h3>');
            }
        });
    }

    function save_membership(btn) {
        $(btn).addClass('l w');
        var form = $(btn).closest('form');
        var data = $(form).serialize();

        $.ajax({
            type: "POST",
            url: 'admin/api/SaveMembership',
            data: data,
            dataType: "json",
            success: function (data) {
                if (data == "login") {
                    login(function () {
                        save_membership(btn)
                    });
                    return;
                } else {
                    $(btn).closest('form').find('.ajax-result').html(get_alert(data));
                    notify(data.msg, data.status);
                    if (data.status == 0) {
                        setTimeout(function () {
                            location.reload();
                        }, 800);
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

    function delete_membership(btn, id) {
        Confirm({
            url: "deleteMembership/" + id,
            Dhtml: 'به طور کامل حذف می شود .<br/> ادامه می دهید ؟',
            Did: 'deleterow_' + id,
            success: function (data) {
                $(btn).closest('tr').hide(1000, function () {
                    $(this).remove()
                });
            }
        });
    }


</script>
