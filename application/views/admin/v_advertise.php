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
$cols['عنوان تبلیغ'] =
    array(
        'field_name' => 'title',
        'link' => true,
        'type' => 'string',
        'td-attr' => 'style="text-align: right;"'
    );

$cols['بخش'] =
    array(
        'field_name' => 'section',
        'link' => false,
        'type' => 'string',
        'td-attr' => 'style="text-align: right;"',
        'function' => function ($col, $row) {
            $out[""] = "";
            $out["category"] = "دسته بندی";
            $out["classonline"] = "کلاس آنلاین";
            $out["classroom"] = "کلاس عادی";
            $out["tecat"] = "دسته بندی عنوانی";
            $out["supplier"] = "عرضه کنندگان";
            $out["membership"] = "اشتراک";
            $out["link"] = "آدرس وب";
            return $out[$col];
        }
    );

$cols['موضوع'] =
    array(
        'field_name' => 'link',
        'link' => false,
        'type' => 'string',
        'td-attr' => 'style="text-align: right;"',
        'function' => function ($col, $row) {
            $out = $col;
            if($row["section"] == "membership"){
                $row["section"] = "category";
            }
            switch ($row["section"]) {
                case "tecat":
                case "category":
                    $select = "id AS value,name AS text";
                    $order_by = "name";
                    break;
                case "classroom":
                case "supplier":
                case "post":
                case "classonline":
                    $select = "id AS value,title AS text";
                    $order_by = "title";
                    break;
                default:
                    return $out;
            }
            $col = intval($col);
            $this->db->select($select);
            $this->db->order_by($order_by, 'asc');
            $this->db->where("id",$col);
            $result = $this->db->get($row["section"])->row();
            if(is_object($result)){
                $out = $result->text;
            }
            return $out;
        }
    );

$cols['اولویت'] =
    array(
        'field_name' => 'priority',
        'link' => true,
        'type' => 'string',
        'th-attr' => 'style="width:150px"'
    );
$cols['تعداد بازدید'] =
    array(
        'field_name' => 'showed',
        'link' => true,
        'type' => 'string',
        'th-attr' => 'style="width:150px"'
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
        'link' => false,
        'th-attr' => 'style="width:50px"',
        'html' => '<i class="fa fa-edit text-success cu" onClick="edit_advertise(this,[ID])"></i>'
    );
$cols['حذف'] =
    array(
        'field_name' => 'id',
        'link' => false,
        'th-attr' => 'style="width:50px"',
        'html' => '<i class="fa fa-trash text-danger cu" onClick="delete_advertise(this,[ID])"></i>'
    );

$q = "SELECT c.* FROM ci_advertise c $query";
echo $searchHtml;
?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            تبلیغ ها
            <a class="btn-sm btn-warning pull-left" onclick="new_advertise();">تبلیغ جدید</a>
            <div class="clearfix"></div>
        </h3>
    </div>
</div>


<?php $inc->createTable($cols, $q, 'id="table" class="table light2" ', $tableName, 60); ?>

<?php $canReply = false ?>

<div class="hidden">
    <div class="view-sample">
        <h2 align="center">ثبت تبلیغ</h2>
        <form class="clearfix">
            <div class="row col-md-offset-2">
                <div class="col-md-6">
                    <div class="box">
                        <div class="box-title">اولویت</div>
                        <div class="box-content">
                            <select name="priority" class="form-control update-el priority">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                    </div>
                    <div class="box">
                        <div class="box-title">عنوان تبلیغ</div>
                        <div class="box-content">
                            <input name="title" class="form-control update-el title"/>
                        </div>
                    </div>
                    <div class="box">
                        <div class="box-title">بخش</div>
                        <div class="box-content USERDATAsection">
                            <select name="section" class="form-control update-el section">
                                <option value="category">دسته بندی</option>
                                <option value="classonline">کلاس آنلاین</option>
                                <option value="classroom">کلاس عادی</option>
                                <option value="tecat">دسته بندی عنوانی</option>
                                <option value="supplier">عرضه کنندگان</option>
                                <option value="membership">اشتراک</option>
                                <option value="link">آدرس وب</option>
                            </select>
                        </div>
                    </div>
                    <div class="box">
                        <div class="box-title">موضوع</div>
                        <div class="box-content USERDATA">
                            <input class="form-control update-el title userdatatext"/>
                            <input type="hidden" name="link" class="userdataid">
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

    function new_advertise() {
        var $html = $('<div/>', {'id': 'edit-comment'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

        var $view = $('.view-sample').clone(true);

        $view.find('.sample-publish').on('click', function () {
            $view.find('.published').val(1);
            save_advertise(this);
        });
        $view.find('.sample-unpublish').on('click', function () {
            $view.find('.published').val(0);
            save_advertise(this);
        });
        $view.find('#temp-add-thumb-img').attr('id', 'add-thumb-img');
        $view.find('#temp-my-media-ap').attr('id', 'media-ap');
        $view.find('.USERDATAsection').addClass('userdatasection');
        $view.find('.USERDATA').addClass('userdata');
        $html.html($view);
        $html.find("select").chosen({width: '100%'});
    }

    function edit_advertise(that, id) {
        var $html = $('<div/>', {'id': 'edit-advertise'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

        $.ajax({
            type: "POST",
            url: 'admin/api/getAdvertiseInfo/' + id,
            dataType: "json",
            success: function (data) {

                if (data == "login") {
                    popupScreen('');
                    login(function () {
                        edit_advertise(btn, id)
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
                    save_advertise(this);
                });
                $view.find('.sample-unpublish').on('click', function () {
                    $view.find('.published').val(0);
                    save_advertise(this);
                });
                $view.find('#temp-add-thumb-img').attr('id', 'add-thumb-img');
                $view.find('#temp-my-media-ap').attr('id', 'my-media-ap');
                if (data.data['image']) {
                    $thumb = $('<div class="media-ap-data" />');
                    var img = $('<img/>', {src: data.data['image']}).addClass('convert-this img-responsive');
                    $thumb.append(img);
                    $view.find('#my-media-ap').html($thumb);
                    $view.find('#add-thumb-img').hide();
                }
                newdata = data.data;
                for (const [item, index] of Object.entries(newdata)) {
                    $view.find('#data_type_' + item).val(index);
                }
                $view.find('.USERDATAsection').addClass('userdatasection');
                $view.find('.USERDATA').addClass('userdata');
                $html.html($view);
                $html.find("[name=active]").trigger('change');
                $("#edit-advertise select").chosen({width: '100%'}).trigger("chosen:updated");
            },
            error: function () {
                $html.html('<h3 class="text-warning text-center">Conection Error</h3>');
            }
        });
    }

    function save_advertise(btn) {
        $(btn).addClass('l w');
        var form = $(btn).closest('form');
        var data = $(form).serialize();

        $.ajax({
            type: "POST",
            url: 'admin/api/SaveAdvertise',
            data: data,
            dataType: "json",
            success: function (data) {
                if (data == "login") {
                    login(function () {
                        save_advertise(btn)
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

    function delete_advertise(btn, id) {
        Confirm({
            url: "deleteAdvertise/" + id,
            Dhtml: 'به طور کامل حذف می شود .<br/> ادامه می دهید ؟',
            Did: 'deleterow_' + id,
            success: function (data) {
                $(btn).closest('tr').hide(1000, function () {
                    $(this).remove()
                });
            }
        });
    }

    function getSectionData(section, value, elm, that) {
        if (value.length < 2) {
            return;
        }
        var data = {
            section: section,
            value: value
        };
        $.ajax({
            type: "POST",
            url: 'admin/api/getSectionData',
            data: data,
            dataType: "json",
            success: function (result) {
                result = result.result;
                if (result.length) {
                    elm.autocomplete({
                        appendTo: elm.parent(),
                        source: result,
                        select: function (event, ui) {
                            studentneed = ui.item.idx;
                            that.val(studentneed);
                        }
                    });
                }
            }
        });
    }

    $(document).ready(function () {
        $('body').on('input', '.userdata input.userdatatext', function () {
            var val = $(this).val();
            var that = $('.userdata input.userdataid');
            var section = $('.userdatasection select.section').val();
            console.error(that,val);
            if (section != "link") {
                getSectionData(section, val, $(this), that);
            } else {
                that.val(val);
            }
        });
        $('body').on('change', '.section', function () {
            $('.userdataid').val("");
            $('.userdatatext').val("");
        });
    });

</script>
