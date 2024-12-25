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
global $mecatX, $teachernameX, $ClassAccountsX, $ClassStudentsX;
$mecatX = $mecat;
$teachernameX = $teachername;
$ClassAccountsX = $ClassAccounts;
$ClassStudentsX = $ClassStudents;
$inc = new inc;

$cols['شماره'] =
    array(
        'field_name' => 'id',
        'th-attr' => 'style="width:50px"',
        'link' => true
    );
$cols['نام کلاس آنلاین'] =
    array(
        'field_name' => 'title',
        'link' => true,
        'type' => 'string',
        'th-attr' => 'style="width:150px"'
    );
$cols['دسته بندی موضوعی'] =
    array(
        'field_name' => 'mecatid',
        'link' => false,
        'function' => function ($col, $row) {
            global $supplierX, $mecatX;
            return @$mecatX[$row["mecatid"]];
        }
    );
$cols['استاد'] =
    array(
        'field_name' => 'teachername',
        'link' => false,
        'function' => function ($col, $row) {
            global $teachernameX, $mecatX;
            return @$teachernameX[$row["teachername"]];
        }
    );
$cols['قیمت'] =
    array(
        'field_name' => 'price',
        'link' => false,
        'function' => function ($col, $row) {
            return $row["price"] . " [تخفیف : " . $row["discount"] . "]";
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

$cols['اکانت های کلاس'] =
    array(
        'field_name' => 'id',
        'link' => false,
        'th-attr' => 'style="width:150px"',
        //'html' => '<i class="fa fa-share-alt fa-2x text-danger cu" onClick="ShowClassAccounts([ID])"></i>',
        'function' => function ($col, $row) {
            global $ClassAccountsX;
            $C = intval(@$ClassAccountsX[$row["id"]]);
            return '<i class="fa fa-share-alt fa-2x text-' . ($C ? 'success' : 'danger') . ' cu" onClick="ShowClassAccounts(' . $row["id"] . ',0)"> ' . $C . ' </i>';
        }
    );
$cols['دانشجویان'] =
    array(
        'field_name' => 'id',
        'link' => false,
        'th-attr' => 'style="width:150px"',
        //'html' => '<i class="fa fa-share-alt fa-2x text-danger cu" onClick="ShowClassAccounts([ID])"></i>',
        'function' => function ($col, $row) {
            global $ClassStudentsX;
            $C = intval(@$ClassStudentsX[$row["id"]]);
            return '<i class="fa fa-share-alt fa-2x text-' . ($C ? 'primary' : 'danger') . ' cu" onClick="ShowClassAccounts(' . $row["id"] . ',1)"> ' . $C . ' </i>';
        }
    );
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
        'html' => '<i class="fa fa-edit fa-2x text-success cu" onClick="edit_classonline(this,[ID])"></i>'
    );
$cols['حذف'] =
    array(
        'field_name' => 'id',
        'link' => false,
        'th-attr' => 'style="width:50px"',
        'html' => '<i class="fa fa-trash fa-2x text-danger cu" onClick="delete_classonline(this,[ID])"></i>'
    );

$q = "SELECT c.* FROM ci_classonline c $query";
$mecats = $this->db->order_by('name', 'asc')->get('mecat')->result();
echo $searchHtml;
?>

<div class="panel panel-danger">
    <div class="panel-heading">
        <h3 class="panel-title">
            کلاس های آنلاین
            <a class="btn-sm btn-primary pull-left" onclick="new_classonline();">کلاس آنلاین جدید</a>
            <div class="clearfix"></div>
        </h3>
    </div>
</div>


<?php $inc->createTable($cols, $q, 'id="table" class="table light2" ', $tableName, 60); ?>

<?php $canReply = false ?>

<div class="hidden">
    <div class="view-sample">
        <h2 align="center">ثبت کلاس آنلاین</h2>
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
                    <div class="form-group border border-success">
                        <h3 class="btn-success text-center">کلاس</h3>
                        <div class="box">
                            <div class="box-title">نام کلاس آنلاین</div>
                            <div class="box-content">
                                <input required name="title" class="form-control update-el title"/>
                            </div>
                        </div>
                        <div class="box">
                            <div class="box-title">قیمت</div>
                            <div class="box-content">
                                <input required name="price" class="form-control update-el price"/>
                            </div>
                        </div>
                        <div class="box">
                            <div class="box-title">درصد تخفیف</div>
                            <div class="box-content">
                                <input required name="discount" class="form-control update-el discount"/>
                            </div>
                        </div>
                        <div class="box">
                            <div class="box-title">مدت زمان دوره</div>
                            <div class="box-content">
                                <input required name="classtime" class="form-control update-el classtime"/>
                            </div>
                        </div>
                        <div class="box">
                            <div class="box-title">تاریخ شروع کلاس</div>
                            <div class="box-content">
                                <input required name="startdateclass"
                                       class="form-control update-el startdateclass dateFormat"/>
                            </div>
                        </div>
                        <div class="box">
                            <div class="box-title">مهلت ثبت نام</div>
                            <div class="box-content">
                                <input required name="regdatedeadline"
                                       class="form-control update-el regdatedeadline dateFormat"/>
                            </div>
                        </div>
                        <div class="box">
                            <div class="box-title">تاریخ اتمام کلاس</div>
                            <div class="box-content">
                                <input required name="enddateclass"
                                       class="form-control update-el enddateclass dateFormat"/>
                            </div>
                        </div>
                        <div class="box">
                            <div class="box-title">لینک کلاس</div>
                            <div class="box-content">
                                <input type="text" dir="ltr" name="classlink" class="form-control update-el classlink"/>
                            </div>
                        </div>
                        <div class="box">
                            <div class="box-title">توضیحات</div>
                            <div class="box-content">
                                <textarea name="description" class="form-control update-el description"
                                          rows="4"></textarea>
                            </div>
                        </div>
                    </div>
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
                    <!--div class="box mb-2">
                        <div class="box-title">دسته بندی عنوانی درس</div>
                        <div class="box-content">
                            <div class="form-group">

                            </div>
                        </div>
                    </div-->
                    <br/>
                    <div class="box classes hidden">
                        <div class="box-title">کلاس آنلاینها</div>
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
                    <div class="box media-ap pb-1">
                        <h2 class="btn-success mt-0 mb-1 text-center">برنامه هفتگی</h2>
                        <div class="weekdataday"></div>
                    </div>
                    <div class="box mt-2 border border-primary">
                        <h3 class="btn-primary text-center">استاد</h3>
                        <div class="box">
                            <div class="box-title">نام استاد</div>
                            <div class="box-content">
                                <select name="teachername" class="form-control update-el teachername">
                                    <?php foreach ($teachername as $key => $value) { ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="box">
                            <div class="box-title">توضیحات استاد</div>
                            <div class="box-content">
                                <textarea name="teacherdescription" class="form-control update-el teacherdescription"
                                          rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="box mt-2 media-ap">
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
                    <?php
                    /*
                    <div class="box media-ap">
                        <h2 class="btn-primary mt-0 mb-1 text-center">اشتراک</h2>
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
<style>
    .notify-par {
        z-index: 1000000 !important;
    }
    .disabled{
        background-color: rgba(128, 128, 128, 0.25);
        color: darkred;
    }
</style>
<script type="text/javascript">
    var weekdataday = '<table class="table table-striped">' +
        '<tr>' +
        '<th class="addweekday">' +
        '<div class="btn btn-success btn-xs"><i class="fa fa-plus"></i></div>' +
        '</th>' +
        '<th class="text-center">روز</th>' +
        '<th class="text-center">ساعت شروع</th>' +
        '<th class="text-center">ساعت پایان</th>' +
        '</tr>' +
        '</table>';
    var classaccount = ['<form>' +
        '<input type="hidden" name="id" value="{ID}">' +
        '<table class="table table-striped classaccountdata text-center">' +
        '<tr>' +
        '<td colspan="9"><h2 class="text-success">' +
        'لیست اکانتهای موجود' +
        '</h2></td>' +
        '</tr>' +
        '<tr>' +
        '<th class="addclassaccount text-center">' +
        '<div class="btn btn-success btn-xs"><i class="fa fa-plus"></i></div>' +
        '</th>' +
        '<th class="text-center">نام کاربری</th>' +
        '<th class="text-center">رمز عبور</th>' +
        '<th class="text-center">لینک دسترسی</th>' +
        '<th class="text-center">وضعیت</th>' +
        '<th class="text-center">تاریخ ثبت</th>' +
        '<th class="text-center">تاریخ انتساب</th>' +
        '<th class="text-center">کاربر</th>' +
        '<th class="text-center">حذف</th>' +
        '</tr>' +
        '</table>' +
        '<div class="text-center"><div class="btn btn-primary" onclick="saveAccounts(this);"> ذخیره </div></div>' +
        '<div class="ajax-result"></div>' +
        '</form>',
            '<table class="table table-striped classaccountdata text-center">' +
            '<tr>' +
            '<td colspan="8"><h2 class="text-primary">' +
            'لیست دانشجویان موجود' +
            '</h2></td>' +
            '</tr>' +
            '<tr>' +
            '<th class="text-center">نام کاربری</th>' +
            '<th class="text-center">رمز عبور</th>' +
            '<th class="text-center">لینک دسترسی</th>' +
            '<th class="text-center">وضعیت</th>' +
            '<th class="text-center">تاریخ ثبت</th>' +
            '<th class="text-center">تاریخ انتساب</th>' +
            '<th class="text-center">کاربر</th>' +
            '<th class="text-center">حذف</th>' +
            '</tr>' +
            '</table>'
        ]
    ;
    var $booksdata = {<?php
        $booksdata = [];
        foreach ($Books as $Book) {
            $booksdata[] = "$Book->id:'$Book->title'";
        }
        echo implode(',', $booksdata);
        ?>};

    function new_classonline() {
        var $html = $('<div/>', {'id': 'edit-comment'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

        var $view = $('.view-sample').clone(true);

        $view.find('.sample-publish').on('click', function () {
            $view.find('.published').val(1);
            save_classonline(this);
        });
        $view.find('.sample-unpublish').on('click', function () {
            $view.find('.published').val(0);
            save_classonline(this);
        });
        $view.find('.weekdataday').html(weekdataday);
        $view.find('#temp-add-thumb-img').attr('id', 'add-thumb-img');
        $view.find('#temp-my-media-ap').attr('id', 'media-ap');
        $html.html($view);
        $html.find("select").chosen({width: '100%'});
        $('.dateFormat').datepicker({dateFormat: 'yy-mm-dd'});
        setTimeout(function () {
            $('.full-screen').css('z-index',1);
        },200);
    }

    function edit_classonline(that, id) {
        var $html = $('<div/>', {'id': 'edit-classonline'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

        $.ajax({
            type: "POST",
            url: 'admin/api/getClassOnlineInfo/' + id,
            dataType: "json",
            success: function (data) {

                if (data == "login") {
                    popupScreen('');
                    login(function () {
                        edit_classonline(btn, id)
                    });
                    return;
                }

                if (!data.done) {
                    $html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
                    return;
                }
                var $view = $('.view-sample').clone(true);

                $view.find('.update-el').each(function (i, el) {
                    var val = data.classonline[$(el).attr('name')];
                    $(el).val(val).data('prevdata', val);
                });

                $view.find('.sample-publish').on('click', function () {
                    $view.find('.published').val(1);
                    save_classonline(this);
                });
                $view.find('.sample-unpublish').on('click', function () {
                    $view.find('.published').val(0);
                    save_classonline(this);
                });
                $view.find('#temp-add-thumb-img').attr('id', 'add-thumb-img');
                $view.find('#temp-my-media-ap').attr('id', 'my-media-ap');
                if (data.classonline['image']) {
                    $thumb = $('<div class="media-ap-data" />');
                    var img = $('<img/>', {src: data.classonline['image']}).addClass('convert-this img-responsive');
                    $thumb.append(img);
                    $view.find('#my-media-ap').html($thumb);
                    $view.find('#add-thumb-img').hide();
                }
                $view.find('.weekdataday').html(weekdataday);
                newdata = data.data;
                newpages = data.pages;
                var $detailhtml = '';
                for (const [item, index] of Object.entries(newdata)) {
                    $view.find('#data_type_' + item).val(index);
                    switch (item) {
                        case 'book':
                            for (const bookid of index) {
                                $detailhtml += '<tr id="book_' + bookid + '" class="book_detail" data-id="' + bookid + '">';
                                $detailhtml += '<td>';
                                $detailhtml += $booksdata[bookid];
                                $detailhtml += '</td>';
                                $detailhtml += '<td>';
                                $detailhtml += '<input type="hidden" name="data_type[startpage][' + bookid + ']" value="' + newpages[item][bookid]['startpage'] + '" />'
                                $detailhtml += '</td>';
                                $detailhtml += '<td>';
                                $detailhtml += '<input type="hidden" name="data_type[endpage][' + bookid + ']" value="' + newpages[item][bookid]['endpage'] + '" />'
                                $detailhtml += '</td>';
                                $detailhtml += '</tr>';
                            }
                            break;
                        case 'dayofweek':
                            var dayofweek, starttime, endtime, tr;
                            var dayofweekvalue = 0;
                            for (const id of index) {
                                dayofweekvalue = newpages[item][id]['dayofweek'];
                                dayofweek = '<select name="data_type[dayofweek][]" required>' +
                                    '<option value="0"' + (dayofweekvalue == 0 ? ' selected' : '') + '>' + 'شنبه' + '</option>' +
                                    '<option value="1"' + (dayofweekvalue == 1 ? ' selected' : '') + '>' + 'یکشنبه' + '</option>' +
                                    '<option value="2"' + (dayofweekvalue == 2 ? ' selected' : '') + '>' + 'دوشنبه' + '</option>' +
                                    '<option value="3"' + (dayofweekvalue == 3 ? ' selected' : '') + '>' + 'سه شنبه' + '</option>' +
                                    '<option value="4"' + (dayofweekvalue == 4 ? ' selected' : '') + '>' + 'چهارشنبه' + '</option>' +
                                    '<option value="5"' + (dayofweekvalue == 5 ? ' selected' : '') + '>' + 'پنجشنبه' + '</option>' +
                                    '<option value="6"' + (dayofweekvalue == 6 ? ' selected' : '') + '>' + 'جمعه' + '</option>' +
                                    '</select>';
                                starttime = '<input type="text" class="col-sm-12 text-center" maxlength="5" required name="data_type[starttime][]" value="' + newpages[item][id]['starttime'] + '" />';
                                endtime = '<input type="text" class="col-sm-12 text-center" maxlength="5" required name="data_type[endtime][]" value="' + newpages[item][id]['endtime'] + '" />';
                                tr = '<tr><td class="removeme"><div class="btn btn-danger btn-xs"><i class="fa fa-close"></i></div></td><td>' + dayofweek + '</td><td>' + starttime + '</td><td>' + endtime + '</td></tr>';
                                $view.find('.weekdataday table').append(tr);
                            }
                            break;
                    }
                }
                $html.html($view);
                $html.find('#data_type_book_detail').append($detailhtml);
                $html.find("[name=active]").trigger('change');
                $("#edit-classonline select").chosen({width: '100%'}).trigger("chosen:updated");
                $('.dateFormat').each(function () {
                    var defaultDate = $(this).val();
                    $(this).datepicker({dateFormat: 'yy-mm-dd', defaultDate: defaultDate});
                });
                setTimeout(function () {
                    $('.full-screen').css('z-index',1);
                },200);
            },
            error: function () {
                $html.html('<h3 class="text-warning text-center">Conection Error</h3>');
            }
        });
    }

    function save_classonline(btn) {
        $(btn).addClass('l w');
        var form = $(btn).closest('form');
        var data = $(form).serialize();

        $.ajax({
            type: "POST",
            url: 'admin/api/SaveClassOnline',
            data: data,
            dataType: "json",
            success: function (data) {
                if (data == "login") {
                    login(function () {
                        save_classonline(btn)
                    });
                    return;
                } else {
                    $(btn).closest('form').find('.ajax-result').html(get_alert(data));
                    notify(data.msg, data.status,3000);
                    setTimeout(function () {
                        $('.tooltip').remove();
                    },3000);
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
                notify('خطا در اتصال', 2,3000);
                setTimeout(function () {
                    $('.tooltip').remove();
                },3000);
            }
        });
    }

    function delete_classonline(btn, id) {
        Confirm({
            url: "deleteClassOnline/" + id,
            Dhtml: 'به طور کامل حذف می شود .<br/> ادامه می دهید ؟',
            Did: 'deleterow_' + id,
            success: function (data) {
                $(btn).closest('tr').hide(1000, function () {
                    $(this).remove()
                });
            }
        });
    }

    function ShowClassAccounts(id, action) {
        var $html = $('<div/>', {'id': 'edit-classonline-accounts'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

        $.ajax({
            type: "POST",
            url: 'admin/api/getClassAccounts/' + id,
            data: {action: action},
            dataType: "json",
            success: function (data) {

                if (data == "login") {
                    popupScreen('');
                    login(function () {
                        edit_classonline(btn, id)
                    });
                    return;
                }

                if (!data.done) {
                    $html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
                    return;
                }
                var $view = $('<div/>', {'class': 'classaccount'});
                $view.html(classaccount[action].replace('{ID}', id));

                var result = data.result;
                var table = $view.find('table.classaccountdata');
                for (const [index, item] of Object.entries(result)) {
                    var dataid = '<input type="hidden" name="data_type[id][]" value="' + item["id"] + '" /><input type="hidden" name="data_type[regdate][]" value="' + item["regdate"] + '" />';
                    var useronline = action ? item["useronline"] : '<input type="text" class="col-sm-12 text-center required disabled"' + (parseInt(item["user_id"]) ? '  readonly="true"' : '') + ' name="data_type[useronline][]" value="' + item["useronline"] + '" />';
                    var userpass = action ? item["userpass"] : '<input type="text" class="col-sm-12 text-center required disabled"' + (parseInt(item["user_id"]) ? '  readonly="true"' : '') + ' name="data_type[userpass][]" value="' + item["userpass"] + '" />';
                    var accessslink = action ? item["accessslink"] : '<input type="text" class="col-sm-12 text-center required disabled"' + (parseInt(item["user_id"]) ? '  readonly="true"' : '') + ' name="data_type[accessslink][]" value="' + item["accessslink"] + '" />';
                    var statusdata = parseInt(item["user_id"]) ? (action?'فاکتور : '+item["factor_id"]:'استفاده شده') : 'آزاد';
                    var upddate = item["upddate"];
                    var regdate = item["regdate"];
                    var userdata = parseInt(item["user_id"]) ? item["udata"] : '';
                    var destid = item["destid"];
                    var tr = '<tr>' +
                        (action ? '' : '<td class="' + (parseInt(item["user_id"]) ? '' : 'removeme') + '">' + (parseInt(item["user_id"]) ? '' : '<div class="btn btn-danger btn-xs"><i class="fa fa-close"></i></div>') + '' + dataid + '</td>') +
                        '<td>' + useronline + '</td>' +
                        '<td>' + userpass + '</td>' +
                        '<td>' + accessslink + '</td>' +
                        '<td>' + statusdata + '</td>' +
                        '<td>' + regdate + '</td>' +
                        '<td>' + upddate + '</td>' +
                        '<td>' + userdata + '<input type="hidden" name="data_type[user_id][]" value="'+item["user_id"]+'" class="userdataid"></td>' +
                        '<td class="text-center">' + (parseInt(item["user_id"]) ? '<div class="btn pt-0 pb-0 btn-danger removeclassaccount" data-id="' + destid + '">' + 'حذف' + '</div>' : '') + '</td>' +
                        '</tr>';
                    table.append(tr);
                }

                $html.html($view);
                setTimeout(function () {
                    $('.full-screen').css('z-index',1);
                },200);
            },
            error: function () {
                $html.html('<h3 class="text-warning text-center">Conection Error</h3>');
            }
        });
    }

    function saveAccounts(btn) {
        $(btn).addClass('l w');
        var Error = 0;
        var form = $(btn).closest('form');
        form.find('.required').each(function () {
            if(!Error && $(this).val() == ""){
                Error++;
                $(this).focus();
            }
        });
        if(Error){
            notify("لطفا اطلاعات را درست وارد نمایید",1,3000);
            setTimeout(function () {
                $('.tooltip').remove();
            },3000);
            return;
        }
        var data = $(form).serialize();

        $.ajax({
            type: "POST",
            url: 'admin/api/SaveClassAccounts',
            data: data,
            dataType: "json",
            success: function (data) {
                if (data == "login") {
                    login(function () {
                        save_classonline(btn)
                    });
                    return;
                } else {
                    $(btn).closest('form').find('.ajax-result').html(get_alert(data));
                    notify(data.msg, data.status,3000);
                    setTimeout(function () {
                        $('.tooltip').remove();
                    },3000);
                    if (data.status == 0) {
                        setTimeout(function () {
                            //location.reload();
                        }, 800);
                    }
                }
                $(btn).removeClass('l w');
            },
            error: function () {
                $(btn).removeClass('l w');
                notify('خطا در اتصال', 2,3000);
                setTimeout(function () {
                    $('.tooltip').remove();
                },3000);
            }
        });
    }

    function getStudents(value, elm , that) {
        if (value.length < 2)
            return;
        $.ajax({
            type: "POST",
            url: 'admin/api/getStudents/' + value,
            dataType: "json",
            success: function (data) {
                result = data.result;
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
        $('.box-content').on('change', '#data_type_book', function () {
            var $detailhtml = '';
            $val = $(this).val();
            if (typeof $val !== "undefined" && $val && $val.length) {
                var $allowed = [];
                for (var bookid of $val) {
                    $allowed.push(bookid);
                    if (!$('#book_' + bookid).length) {
                        $detailhtml = '<tr id="book_' + bookid + '" class="book_detail" data-id="' + bookid + '">';
                        $detailhtml += '<td>';
                        $detailhtml += $booksdata[bookid];
                        $detailhtml += '</td>';
                        $detailhtml += '<td>';
                        $detailhtml += '<input type="text" class="text-center" name="data_type[startpage][' + bookid + ']" value="0" />'
                        $detailhtml += '</td>';
                        $detailhtml += '<td>';
                        $detailhtml += '<input type="text" class="text-center" name="data_type[endpage][' + bookid + ']" value="0" />'
                        $detailhtml += '</td>';
                        $detailhtml += '</tr>';
                        $('#popup-screen-1 #data_type_book_detail').append($detailhtml);
                    }
                }
                $('.book_detail').each(function () {
                    var $id = $(this).data("id");
                    if ($.inArray($id + "", $allowed) == -1) {
                        $('#data_type_book_detail tr#book_' + $id).remove();
                    }
                });
            } else {
                $('#data_type_book_detail tr').remove();
            }
        });
        $('body').on('click', '.removeme', function () {
            $(this).closest('tr').remove();
        });
        $('body').on('click', '.addweekday', function () {
            var table = $(this).closest('table');
            var dayofweek = '<select name="data_type[dayofweek][]">' +
                '<option value="0">شنبه</option>' +
                '<option value="1">یکشنبه</option>' +
                '<option value="2">دوشنبه</option>' +
                '<option value="3">سه شنبه</option>' +
                '<option value="4">چهارشنبه</option>' +
                '<option value="5">پنجشنبه</option>' +
                '<option value="6">جمعه</option>' +
                '</select>';
            var starttime = '<input type="text" class="col-sm-12 text-center" maxlength="5" name="data_type[starttime][]" />';
            var endtime = '<input type="text" class="col-sm-12 text-center" maxlength="5" name="data_type[endtime][]" />';
            var tr = '<tr><td class="removeme"><div class="btn btn-danger btn-xs"><i class="fa fa-close"></i></div></td><td>' + dayofweek + '</td><td>' + starttime + '</td><td>' + endtime + '</td></tr>';
            table.append(tr);
        });
        $('body').on('click', '.addclassaccount', function () {
            var table = $(this).closest('table');
            var dataid = '<input type="hidden" name="data_type[id][]" value="0" /><input type="hidden" name="data_type[regdate][]" value="0" />';
            var useronline = '<input type="text" class="col-sm-12 text-center required" name="data_type[useronline][]" />';
            var userpass = '<input type="text" class="col-sm-12 text-center required" name="data_type[userpass][]" />';
            var accessslink = '<input type="text" class="col-sm-12 text-center required" name="data_type[accessslink][]" />';
            var userdata = '<div class="userdata"><input type="text" placeholder="نام کاربر را وارد نمایید" class="col-sm-12 text-center userdatatext" /><input type="hidden" name="data_type[user_id][]" class="userdataid"></div>';
            var tr = '<tr>' +
                '<td class="removeme">' +
                '<div class="btn btn-danger btn-xs"><i class="fa fa-close"></i>' + dataid + '</div>' +
                '</td>' +
                '<td>' + useronline + '</td>' +
                '<td>' + userpass + '</td>' +
                '<td>' + accessslink + '</td>' +
                '<td colspan="3">' +
                userdata +
                '</td>' +
                '</tr>';
            table.append(tr);
        });
        $('body').on('click', '.removeclassaccount', function () {
            if (confirm('آیا مطمئن هستید که می خواهید کاربر انتخاب شده را حذف نمایید؟')) {
                $(this).after('<i class="myspinner spin fa fa-spinner fa-lg text-muted ms-2 me-2 progress-bar-animated" title="در انتظار"></i>');
                var id = $(this).data('id');
                var delappendmembership = $(this).closest('tr');
                $.ajax({
                    type: "POST",
                    data: {id: id},
                    url: 'admin/api/deleteClassAccount',
                    dataType: "json",
                    success: function (data) {

                        if (data == "login") {
                            popupScreen('');
                            login(function () {
                                edit_user(btn, id)
                            });
                            return;
                        }

                        if (!data.done) {
                            delappendmembership.after('<tr id="' + dest + id + '"><td colspan="8"><h3 class="text-warning text-center">' + data.msg + '</h3></td></tr>');
                            delappendmembership.find('.myspinner').remove();
                            setTimeout(function () {
                                $('#' + dest + id).slideToggle('slow');
                            }, 2000);
                            return;
                        }
                        delappendmembership.slideToggle('slow');
                    },
                    error: function () {
                        delappendmembership.after('<tr id="' + dest + id + '" colspan="8"><td><h3 class="text-warning text-center">Conection Error</h3></td></tr>');
                        delappendmembership.find('.myspinner').remove();
                        setTimeout(function () {
                            $('#' + dest + id).slideToggle('slow');
                        }, 2000);
                    }
                });
            }
        });
        $('body').on('input','.userdata input.userdatatext', function () {
            var val = $(this).val();
            var that = $(this).parent().find('input.userdataid');
            getStudents(val, $(this),that);
        });
    });
</script>
