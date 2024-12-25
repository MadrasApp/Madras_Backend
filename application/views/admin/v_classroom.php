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
global $mecatX;
$mecatX = $mecat;
$inc = new inc;

$cols['شماره'] =
    array(
        'field_name' => 'id',
        'th-attr' => 'style="width:50px"',
        'link' => true
    );
$cols['نام کلاس'] =
    array(
        'field_name' => 'title',
        'link' => true,
        'type' => 'string'
    );
$cols['دسته بندی موضوعی'] =
    array(
        'field_name' => 'mecatid',
        'link' => false,
        'th-attr' => 'style="width:200px"',
        'function' => function ($col, $row) {
            global $supplierX, $mecatX;
            return @$mecatX[$row["mecatid"]];
        }
    );
/*
$cols['اشتراک'] =
    array(
        'field_name' => 'membership1',
        'link' => false,
        'function' => function ($col, $row) {
            return
                'اشتراک یک ماهه :'.$row["membership1"] . " [تخفیف : " . $row["discountmembership1"] . "]<br>".
                'اشتراک سه ماهه :'.$row["membership3"] . " [تخفیف : " . $row["discountmembership3"] . "]<br>".
                'اشتراک شش ماهه :'.$row["membership6"] . " [تخفیف : " . $row["discountmembership6"] . "]<br>".
                'اشتراک یک ساله :'.$row["membership12"] . " [تخفیف : " . $row["discountmembership12"] . "]";
        }
    );
*/
$cols['تاریخ ثبت'] =
    array(
        'field_name' => 'createdate',
        'link' => true,
        'type' => 'date',
        'th-attr' => 'style="width:150px"'
    );
$cols['انتشار'] =
    array(
        'field_name' => 'published',
        'link' => false,
        'th-attr' => 'style="width:50px"',
        'function' => function ($col, $row) {
            return $row["published"] ? '<i class="fa fa-check text-success fa-2x"></i>' : '<i class="fa fa-close text-danger fa-2x"></i>';
        }
    );
$cols['ویرایش'] =
    array(
        'field_name' => 'id',
        'link' => false,
        'th-attr' => 'style="width:50px"',
        'html' => '<i class="fa fa-edit fa-2x text-success cu" onClick="edit_classroom(this,[ID])"></i>'
    );
$cols['حذف'] =
    array(
        'field_name' => 'id',
        'link' => false,
        'th-attr' => 'style="width:50px"',
        'html' => '<i class="fa fa-trash fa-2x text-danger cu" onClick="delete_classroom(this,[ID])"></i>'
    );

$q = "SELECT c.* FROM ci_classroom c $query";
$mecats = $this->db->order_by('name', 'asc')->get('mecat')->result();
echo $searchHtml;
?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            کلاس ها
            <a class="btn-sm btn-warning pull-left" onclick="new_classroom();">کلاس جدید</a>
            <div class="clearfix"></div>
        </h3>
    </div>
</div>


<?php $inc->createTable($cols, $q, 'id="table" class="table light2" ', $tableName, 60); ?>

<?php $canReply = false ?>

<div class="hidden">
    <div class="view-sample">
        <h2 align="center">ثبت کلاس</h2>
        <form class="clearfix">
            <div class="row col-md-offset-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <table class="table" dir="rtl">
                            <tr>
                                <th width="120">* دسته بندی موضوعی</th>
                                <td>
                                    <select name="mecatid" class="form-control update-el mecatid">
                                        <?php foreach ($mecats as $k => $v) { ?>
                                            <option value="<?php echo $v->id ?>"><?php echo $v->name ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="box">
                        <div class="box-title">نام کلاس</div>
                        <div class="box-content">
                            <input name="title" class="form-control update-el title"/>
                        </div>
                    </div>
                    <div class="box">
                        <div class="box-title">توضیحات</div>
                        <div class="box-content">
                            <textarea name="description" class="form-control update-el description" rows="4"></textarea>
                        </div>
                    </div>
                    <br/>
                    <div class="box">
                        <div class="box-title">اطلاعات تکمیلی</div>
                        <div class="box-content">
                            <div>کتاب / کتابهای اصلی</div>
                            <select id="data_type_book" name="data_type[book][]" class="input full-w" title="کتاب"
                                    multiple>
                                <?php foreach ($Books as $Book) { ?>
                                    <option value="<?php echo $Book->id; ?>"><?php echo $Book->title; ?></option>
                                <?php } ?>
                            </select>
                            <div class="border mt-1 mb-1">
                                <table class="table table-striped border">
                                    <thead>
                                    <tr>
                                        <th>کتاب</th>
                                        <th>از صفحه</th>
                                        <th>تا صفحه</th>
                                    </tr>
                                    </thead>
                                    <tbody id="data_type_book_detail">

                                    </tbody>
                                </table>
                            </div>
                            <div>کتاب / کتابهای مرتبط</div>
                            <select id="data_type_hamniaz" name="data_type[hamniaz][]" class="input full-w"
                                    title="کتاب  / کتابهای مرتبط" multiple>
                                <?php foreach ($Books as $Book) { ?>
                                    <option value="<?php echo $Book->id; ?>"><?php echo $Book->title; ?></option>
                                <?php } ?>
                            </select>
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
                    <br/>
                    <div class="box classes hidden">
                        <div class="box-title">کلاسها</div>
                        <div class="box-content">
                            AAAAAAAAAA
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
                    <?php
                    /*
                    ?>
                    <div class="box media-ap">
                        <div>قیمت اشتراک یک ماهه</div>
                        <input id="membership-1-month" name="membership1" class="input medium update-el full-w"
                               placeholder="قیمت اشتراک یک ماهه">
                        <div>مقدار تخفیف اشتراک یک ماهه</div>
                        <input id="discount-membership-1-month" name="discountmembership1"
                               class="input medium update-el full-w"
                               placeholder="مقدار تخفیف اشتراک یک ماهه">
                        <div>قیمت اشتراک سه ماهه</div>
                        <input id="membership-3-month" name="membership3" class="input medium update-el full-w"
                               placeholder="قیمت اشتراک سه ماهه">
                        <div>مقدار تخفیف اشتراک سه ماهه</div>
                        <input id="discount-membership-3-month" name="discountmembership3"
                               class="input medium update-el full-w"
                               placeholder="مقدار تخفیف اشتراک سه ماهه">
                        <div>قیمت اشتراک شش ماهه</div>
                        <input id="membership-6-month" name="membership6" class="input medium update-el full-w"
                               placeholder="قیمت اشتراک شش ماهه">
                        <div>مقدار تخفیف اشتراک شش ماهه</div>
                        <input id="discount-membership-6-month" name="discountmembership6"
                               class="input medium update-el full-w"
                               placeholder="مقدار تخفیف اشتراک شش ماهه">
                        <div>قیمت اشتراک یک ساله</div>
                        <input id="membership-12-month" name="membership12" class="input medium update-el full-w"
                               placeholder="قیمت اشتراک یک ساله">
                        <div>مقدار تخفیف اشتراک یک ساله</div>
                        <input id="discount-membership-12-month" name="discountmembership12"
                               class="input medium update-el full-w"
                               placeholder="مقدار تخفیف اشتراک یک ساله">
                    </div>
                    */
                    ?>
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
    var $booksdata = {<?php
        $booksdata = [];
        foreach ($Books as $Book) {
            $booksdata[] = "$Book->id:'$Book->title'";
        }
        echo implode(',',$booksdata);
        ?>};
    $(document).ready(function () {
        $('.box-content').on('change','#data_type_book',function () {
            var $detailhtml = '';
            $val = $(this).val();
            if(typeof $val !== "undefined" && $val && $val.length){
                var $allowed = [];
                for (var bookid of $val){
                    $allowed.push(bookid);
                    if(!$('#book_'+bookid).length) {
                        $detailhtml = '<tr id="book_' + bookid + '" class="book_detail" data-id="' + bookid + '">';
                        $detailhtml += '<td>';
                        $detailhtml += $booksdata[bookid];
                        $detailhtml += '</td>';
                        $detailhtml += '<td>';
                        $detailhtml += '<input type="hidden1" name="data_type[startpage][' + bookid + ']" value="0" />'
                        $detailhtml += '</td>';
                        $detailhtml += '<td>';
                        $detailhtml += '<input type="hidden1" name="data_type[endpage][' + bookid + ']" value="0" />'
                        $detailhtml += '</td>';
                        $detailhtml += '</tr>';
                        $('#popup-screen-1 #data_type_book_detail').append($detailhtml);
                    }
                }
                $('.book_detail').each(function () {
                    var $id = $(this).data("id");
                    if($.inArray($id+"",$allowed) === -1){
                        $('#data_type_book_detail tr#book_'+$id).remove();
                    }
                });
            } else {
                $('#data_type_book_detail tr').remove();
            }
        });
    });

    function new_classroom() {
        var $html = $('<div/>', {'id': 'edit-comment'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

        var $view = $('.view-sample').clone(true);

        $view.find('.sample-publish').on('click', function () {
            $view.find('.published').val(1);
            save_classroom(this);
        });
        $view.find('.sample-unpublish').on('click', function () {
            $view.find('.published').val(0);
            save_classroom(this);
        });
        $view.find('#temp-add-thumb-img').attr('id', 'add-thumb-img');
        $view.find('#temp-my-media-ap').attr('id', 'media-ap');
        $html.html($view);
        $html.find("select").chosen({width: '100%'});
    }

    function edit_classroom(that, id) {
        var $html = $('<div/>', {'id': 'edit-classroom'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

        $.ajax({
            type: "POST",
            url: 'admin/api/getClassRoomInfo/' + id,
            dataType: "json",
            success: function (data) {

                if (data == "login") {
                    popupScreen('');
                    login(function () {
                        edit_classroom(btn, id)
                    });
                    return;
                }

                if (!data.done) {
                    $html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
                    return;
                }
                var $view = $('.view-sample').clone(true);

                $view.find('.update-el').each(function (i, el) {
                    var val = data.classroom[$(el).attr('name')];
                    $(el).val(val).data('prevdata', val);
                });

                $view.find('.sample-publish').on('click', function () {
                    $view.find('.published').val(1);
                    save_classroom(this);
                });
                $view.find('.sample-unpublish').on('click', function () {
                    $view.find('.published').val(0);
                    save_classroom(this);
                });
                $view.find('#temp-add-thumb-img').attr('id', 'add-thumb-img');
                $view.find('#temp-my-media-ap').attr('id', 'my-media-ap');
                if (data.classroom['image']) {
                    $thumb = $('<div class="media-ap-data" />');
                    var img = $('<img/>', {src: data.classroom['image']}).addClass('convert-this img-responsive');
                    $thumb.append(img);
                    $view.find('#my-media-ap').html($thumb);
                    $view.find('#add-thumb-img').hide();
                }
                newdata = data.data;
                newpages = data.pages;
                var $detailhtml = '';
                for (const [item, index] of Object.entries(newdata)) {
                    $view.find('#data_type_' + item).val(index);
                    if (item === 'book') {
                        for (const bookid of index) {
                            $detailhtml += '<tr id="book_' + bookid + '" class="book_detail" data-id="' + bookid + '">';
                            $detailhtml += '<td>';
                            $detailhtml += $booksdata[bookid];
                            $detailhtml += '</td>';
                            $detailhtml += '<td>';
                            $detailhtml += '<input type="hidden1" name="data_type[startpage][' + bookid + ']" value="' + newpages[item][bookid]['startpage'] + '" />'
                            $detailhtml += '</td>';
                            $detailhtml += '<td>';
                            $detailhtml += '<input type="hidden1" name="data_type[endpage][' + bookid + ']" value="' + newpages[item][bookid]['endpage'] + '" />'
                            $detailhtml += '</td>';
                            $detailhtml += '</tr>';
                        }
                    }
                }
                $html.html($view);
                $html.find('#data_type_book_detail').append($detailhtml);
                $html.find("[name=active]").trigger('change');
                $("#edit-classroom select").chosen({width: '100%'}).trigger("chosen:updated");
            },
            error: function () {
                $html.html('<h3 class="text-warning text-center">Conection Error</h3>');
            }
        });
    }

    function save_classroom(btn) {
        $(btn).addClass('l w');
        var form = $(btn).closest('form');
        var data = $(form).serialize();

        $.ajax({
            type: "POST",
            url: 'admin/api/SaveClassRoom',
            data: data,
            dataType: "json",
            success: function (data) {
                if (data == "login") {
                    login(function () {
                        save_classroom(btn)
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

    function delete_classroom(btn, id) {
        Confirm({
            url: "deleteClassRoom/" + id,
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
