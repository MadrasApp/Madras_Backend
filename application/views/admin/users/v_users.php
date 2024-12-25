<?php

$this->load->helper('inc');

$inc = new inc;

$cols = array(
    '' => array(
        'field_name' => 'avatar',
        //'td-attr' => 'style="padding:0;width:40px;" width="40px" align="center"',
        'html' => '<img src="[FLD]" width="40" height="40" onclick="edit_user(this,[ID])" style="cursor:pointer">',
        'function' => function ($col, $row) {
            global $CI;
            $col = thumb($col, 150);
            $col = file_exists($col) ? $col : $CI->settings->data['default_user_avatar'];
            return base_url() . $col;
        }
    ),
    'نام نمایشی' => array(
        'field_name' => 'displayname',
        'link' => true,
        'html' => '<div class="wb" onclick="edit_user(this,[ID])" style="cursor:pointer">[FLD]</div>',
        //'td-attr' => 'align="center"'
    ),
    'نام کاربری' => array(
        'field_name' => 'username',
        'link' => true,
        'html' => '<div class="wb">[FLD]</div>',
        //'td-attr' => 'align="center"'
    ),
    'تلفن همراه' => array(
        'field_name' => 'tel',
        'link' => false,
    ),
    'پشتیبان' => array(
        'field_name' => 'support',
        'link' => true,
        //'td-attr' => 'align="center"',
        'function' => function ($col, $row) {
            if ($col == 1)
                $html = '<i class="fa fa-check-circle-o fa-lg text-success" title="فعال"></i>';
            else
                $html = '<i class="fa fa-ban fa-lg text-danger" title="مسدود"></i>';

            return $html;
        }
    ),
    'نقش' => array(
        'field_name' => 'level',
        'link' => true,
        //'td-attr' => 'align="center"',
        'type' => 'user-level'),
    'ایمیل' => array(
        'field_name' => 'email',
        'link' => true,
        'html' => '<span class="ar">[FLD]</span>',
        //'td-attr' => 'align="center"'
    ),
    'فعال/مسدود' => array(
        'field_name' => 'active',
        'link' => true,
        //'td-attr' => 'align="center"',
        'function' => function ($col, $row) {
            if ($col == 1)
                $html = '<i class="fa fa-check-circle-o fa-lg text-success" title="فعال"></i>';
            else
                $html = '<i class="fa fa-ban fa-lg text-danger" title="مسدود"></i>';

            return $html;
        }
    ),
    'تاریخ ثبت نام' => array(
        'field_name' => 'date',
        'link' => true,
        'type' => 'date',
        //'td-attr' => 'align="center"'
    ),
);

if ($this->user->can('edit_user')) {
    $cols['ویرایش'] = array(
        'field_name' => 'id',
        'link' => false,
        //'td-attr' => 'align="center" style="width:30px;padding:0"',
        'html' => '<div style="padding:10px;cursor: pointer" onclick="edit_user(this,[ID])"><i class="fa fa-pencil"></i></div>'
    );
}

if ($this->user->can('delete_user')) {
    $cols['حذف'] = array(
        'field_name' => 'id',
        'link' => false,
        //'td-attr' => 'align="center" style="width:30px;padding:0"',
        'html' => '<div style="padding:10px;cursor: pointer" onclick="delete_row(this,\'users\',[FLD])"><i0 class="fa fa-trash"></i></div>'
    );
}

$cols['کتابهای کاربر'] = array(
    'field_name' => 'userbooks',
    'link' => false,
    //'td-attr' => 'align="center" style="width:30px;padding:0"',
    'html' => '<div style="padding:10px;cursor: pointer" onclick="showbookuser(this,[ID])"><i class="fa fa-book"></i></div><center><strong class="text-danger">[FLD]</strong></center>',
    'function' => function ($col, $row) {
        $user_id = $row['id'];
        $O = $this->db->select("id")->where("user_id", $user_id)->count_all_results('user_books');
        return $O;
    }
);

$cols['یادداشتهای کاربر'] = array(
    'field_name' => 'userhighlights',
    'link' => false,
    //'td-attr' => 'align="center" style="width:30px;padding:0"',
    'html' => '<div style="padding:10px;cursor: pointer" onclick="showhighlightuser(this,[ID])"><i class="fa fa-pencil"></i></div><center><strong class="text-danger">[FLD]</strong></center>',
    'function' => function ($col, $row) {
        $user_id = $row['id'];
        $O = $this->db->select("id")->where("user_id", $user_id)->count_all_results('highlights');
        return $O;
    }
);

$cols['دستگاههای کاربر'] = array(
    'field_name' => 'usermobiles',
    'link' => false,
    //'td-attr' => 'align="center" style="width:30px;padding:0"',
    'html' => '<div style="padding:10px;cursor: pointer" onclick="showmobileuser(this,[ID])"><i class="fa fa-2x fa-mobile"></i></div><center><strong class="text-danger">[FLD]</strong></center>',
    'function' => function ($col, $row) {
        $user_id = $row['id'];
        $O = $this->db->select("id")->where("user_id", $user_id)->count_all_results('user_mobile');
        return $O;
    }
);
$cols['اشتراک'] = array(
    'field_name' => 'hasmembership',
    'link' => false,
    'count' => 'hasmembership',
    'th-attr' => 'style="width:50px"',
    //'text-success', 'text-muted'
    'html' => '<div style="padding:10px;cursor: pointer" onclick="showMembership(this,[ID],1)"><i class="fa fa-2x fa-share-alt"></i></div><center><strong class="text-danger">[FLD]</strong></center>',
    'function' => function ($col, $row) {
        $user_id = $row['id'];
        $X = $this->db->where("user_id", $user_id)->count_all_results('user_catmembership');
        $Y = $this->db->where("user_id", $user_id)->count_all_results('user_membership');
        return $X + $Y;
    }
);

echo $searchHtml;
$inc->createTable($cols, $query, 'id="table" class="table light2" ', $tableName, 60);


?>


<div class="hidden">
    <div class="view-book"><!-- Alireza Balvardi -->
        <div class="row">
            <form class="clearfix">
                <center>
                    <h2>لیست کتابهای کاربر <span class="usernametag"></span> <span class="displaynametag"></span></h2>

                    <table class="table light2">
                        <thead>
                        <tr>
                            <th colspan="3">افزودن کتاب جدید</th>
                        </tr>
                        <tr>
                            <td class="h3">نام کتاب (آی دی کتاب)</td>
                            <td><input type="text" name="bookneed{id}" id="bookneed{id}"
                                       class="form-control update-el"/></td>
                            <td><input type="button" value="افزودن کتاب جاری"
                                       class="btn btn-primary btn-block sample-send" onclick="addUserBooks();"/></td>
                        </tr>
                        </thead>
                    </table>
                    <hr/>
                    <table class="table light2">
                        <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>حذف</th>
                            <th>عنوان</th>
                            <th>دسته بندی</th>
                        </tr>
                        </thead>
                        <tbody class="bookbody">
                        </tbody>
                    </table>
                </center>

            </form>
        </div>
    </div>
    <div class="view-sample">
        <div class="row">
            <form class="clearfix">
                <div class="col-sm-6">
                    <div class="row">
                        <style scope>.editable-img img {
                                max-width: 120px !important;
                                max-height: 120px !important;
                            }</style>
                        <div class="box-content media-ap col-sm-6">
                            <div class="editable-img text-center">
                                <p>تصویر پروفایل</p>
                                <span class="media-ap-data replace" data-thumb="thumb150"
                                      style="display: inline-block">
                                    <img class="convert-this img-responsive update-avatar" src="">
                                </span>
                                <div class="plus add-img add-thumb-img-2"
                                     onclick="media('img,1',this,function(){ $('.add-thumb-img-2').hide() })"
                                     style="display:none;margin:15px">
                                </div>
                                <div class="form-ap-data" style="display:none"></div>
                                <input type="hidden" class="media-ap-input update-el" name="avatar">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <p>نام کاربری</p>
                                <input type="text" name="username" class="form-control update-el" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <p>نام </p>
                                <input type="text" name="displayname" class="form-control update-el">
                            </div>
                        </div>

                        <!--<div class="col-md-6">
                            <div class="form-group">
                                <p>نام</p>
                                <input type="text" name="name" class="form-control update-el">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <p>نام خانوادگی</p>
                                <input type="text" name="family" class="form-control update-el">
                            </div>
                        </div>-->

                        <div class="col-md-6">
                            <div class="form-group">
                                <p>ایمیل</p>
                                <input type="email" name="email" class="form-control update-el en">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <p>موبایل</p>
                                <input type="text" name="tel" class="form-control update-el en">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <p>کد ملی</p>
                                <input type="text" name="national_code" class="form-control update-el en">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <p>تاریخ تولد</p>
                                <input type="text" name="birthday" class="form-control update-el en">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <p>کشور</p>
                                <input type="text" name="country" class="form-control update-el">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <p>استان</p>
                                <input type="text" name="state" class="form-control update-el">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <p>شهر</p>
                                <input type="text" name="city" class="form-control update-el">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <p>کد پستی</p>
                                <input type="text" name="postal_code" class="form-control update-el en">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <p>سن</p>
                                <input type="text" name="age" class="form-control update-el en"/>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <p>جنسیت</p>
                                <select class="form-control update-el" name="gender">
                                    <option value="1">مرد</option>
                                    <option value="0">زن</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <p>رمز</p>
                                <input type="text" name="password" class="form-control"
                                       placeholder="برای تغییر نکردن رمز، این فیلد را خالی بگذارید">
                            </div>
                        </div>


                    </div>

                </div>
                <div class="col-sm-6">

                    <div class="row">

                        <?php if ($this->user->can('edit_user_role')): ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <p>نقش</p>
                                    <select class="form-control update-el" name="level">
                                        <?php $levels = $this->db->where('level_key', 'level_name')->get('user_level')->result() ?>
                                        <?php foreach ($levels as $level): ?>
                                            <option value="<?php echo $level->level_id ?>"><?php echo $level->level_value ?></option>
                                        <?php endforeach ?>
                                        <option value="user">کاربر</option>
                                        <option value="admin">ادمین</option>
                                        <option value="teacher">استاد</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <p>پشتیبان</p>
                                    <select class="form-control update-el" name="support">
                                        <option value="1">بلی</option>
                                        <option value="0">خیر</option>
                                    </select>
                                </div>
                            </div>
                        <?php endif ?>

                        <!--<div class="box-content media-ap col-sm-8">
                            <div class="editable-img ">
                                <p>تصویر کاور</p>
                                <span class="media-ap-data replace" data-thumb="thumb300" style="display: inline-block">
                                    <img class="convert-this img-responsive update-cover" src="">
                                </span>
                                <div class="plus add-img add-thumb-img-1"
                                     onclick="media('img,1',this,function(){ $('.add-thumb-img-1').hide() })" style="display:none;margin:15px">
                                </div>
                                <div class="form-ap-data" style="display:none"></div>
                                <input type="hidden" class="media-ap-input update-el" name="cover">
                            </div>
                        </div>-->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <p>وضعیت حساب کاربری</p>
                                <select name="active" class="form-control update-el"
                                        onchange="$(this).parent().next().css('display',$(this).val()==1 ?'none':'block')">
                                    <option value="1">فعال</option>
                                    <option value="0">مسدود</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <p>دلیل مسدود کردن حساب کاربری</p>
                                <textarea type="text" name="pending_reason" class="form-control update-el" rows="4"
                                          style="height:75px"></textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <p>آدرس</p>
                                <textarea name="address" class="form-control update-el"></textarea>
                            </div>
                        </div>


                        <div class="clearfix"></div>
                    </div>

                    <hr/>

                    <div class="ajax-result" style="margin-bottom: 20px;"></div>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary btn-block btn-lg sample-send"><i
                                    class="fa fa-check-circle"></i> <span>ویرایش</span></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="view-highlights"><!-- Alireza Balvardi -->
        <center>
            <h2>لیست یادداشتهای کاربر <span class="usernametag"></span> <span class="displaynametag"></span></h2>
        </center>
        <table class="table">
            <tbody class="highlightbody">
            </tbody>
        </table>
    </div>
    <div class="view-mobiles"><!-- Alireza Balvardi -->
        <center>
            <h2>لیست دستگاههای کاربر <span class="usernametag"></span> <span class="displaynametag"></span></h2>
        </center>
        <table class="table">
            <tbody class="mobilebody">
            </tbody>
        </table>
    </div>
    <div class="view-membership"><!-- Alireza Balvardi -->
        <div class="DATA-ADD-MEMBERSHIP">
            <h2 class="text-center">
                لیست اشتراکهای کاربر <span class="displaynametag"></span>
                <div class="float-end h4 btn btn-success APPENDMEMBERSHIP" data-user="">افزودن اشتراک</div>
            </h2>
            <div class="d-none mt-2 DIVAPPENDMEMBERSHIP">
                <h3 class="text-center text-danger p-1 border shadow">
                    افزودن اشتراک جدید
                    <div class="btn btn-danger ESCAPEMEMBERSHIP float-end">بازگشت</div>
                    <div class="clearfix"></div>
                </h3>
                <div class="NEWAPPENDMEMBERSHIP">
                    <table class="table">
                        <tr>
                            <th>ردیف</th>
                            <th>نوع اشتراک</th>
                            <th>عنوان</th>
                            <th>مدت به ماه</th>
                            <th>هزینه اشتراک</th>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="TBLAPPENDMEMBERSHIP">
                <table class="table">
                    <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>نوع اشتراک</th>
                        <th>عنوان</th>
                        <th>مدت به ماه</th>
                        <th>تاریخ شروع</th>
                        <th>تاریخ انقضا</th>
                        <th>توضیحات</th>
                        <th class="text-center col-md-1">حذف</th>
                    </tr>
                    </thead>
                    <tbody class="MEMBERSHIP">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .gray {
        background-color: #CCCCCC;
        border-radius: 5px;
        padding: 2px;
        position: relative;
        padding-right: 35px;
        overflow: hidden;
        border: 1px solid #CCC;
    }

    .xbtn {
        background-color: #fff5cc;
        position: absolute;
        right: 0;
        top: -3px;
        bottom: -3px;
        padding: 3px;
        cursor: pointer;
        line-height: 40px;
    }
    .spin{
        animation: spin 1s infinite;
    }
    @keyframes spin{
        0%{
            -webkit-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }

        100%{
            -webkit-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }

</style>
<script type="text/javascript">
    var bookdata = {};
    let prevbtn = {};
    var bookneed = 0;
    var bookneedtitle = '';
    var userid = 0;
    var username = 0;
    var color = ['#000', '#F00', '#200', '#0F0', '#005', '#000', '#F00', '#200', '#0F0', '#005'];

    function DeleteUBook(id) {
        title = bookdata[id];
        if (confirm("آیا مطمئن هستید که قصد حذف کتاب " + title + " را از کاربر جاری دارید")) {
            $.ajax({
                type: "POST",
                url: 'admin/api/deleteUserBooks/' + id,
                dataType: "json",
                success: function (data) {
                    jQuery("#book_" + id).remove();
                    alert("کتاب " + title + " از کاربر جاری حذف شد");
                },
                error: function () {
                    alert("خطا : کتاب " + title + " از کاربر جاری حذف نشد");
                }
            });
        }
        return false;
    }

    function getBooks(value, elm) {
        if (value.length < 1)
            return;
        $.ajax({
            type: "POST",
            url: 'admin/api/getBooks/' + value,
            dataType: "json",
            success: function (data) {
                result = data.result;
                if (result.length) {
                    elm.autocomplete({
                        appendTo: elm.parent(),
                        source: result,
                        select: function (event, ui) {
                            bookneed = ui.item.idx;
                            bookneedtitle = ui.item.title;
                        }
                    });
                }
            }
        });
    }

    function addUserBooks() {
        if (confirm("آیا شما مطئن هستید که می خواهید کتاب " + bookneedtitle + " را به کاربر " + username + " انتساب دهید ؟")) {
            if(bookneed == 0){
                bookneed = $("#bookneed1265").val();
                if(isNaN(bookneed)){
                    bookneed = 0;
                }
            }
            $("#bookneed1265").val("");
            $.ajax({
                type: "POST",
                url: 'admin/api/addUserBooks/',
                data: {bid: bookneed, uid: userid},
                dataType: "json",
                success: function (data) {
                    result = parseInt(data.status);
                    if (result == 0) {
                        alert("کتاب " + bookneedtitle + " به لیست کتابهای کاربر " + username + " اضافه گردید");
                        $('.bookbody').html("");
                        for (i = 0; i < data.books.length; i++) {
                            el = data.books[i];
                            bookdata[el.ubid] = el.title;
                            $('.bookbody').append('<tr id="book_' + el.ubid + '"><td>' + (i + 1) + '</td><td><a class="fa fa-trash" onclick="DeleteUBook(' + el.ubid + ');"></a></td><td>' + el.title + '</td><td>' + el.cname + '</td></tr>');
                        }
                    }
                }
            });
        }
    }

    function showbookuser(btn, id) {
        var $html = $('<div/>', {'id': 'edit-user'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

        $.ajax({
            type: "POST",
            url: 'admin/api/getUserBooks/' + id,
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
                    $html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
                    return;
                }
                username = data.user["username"];
                userid = data.user["id"];
                var $view = $('.view-book').clone(true);

                $view.find('.usernametag').html(data.user["username"]);
                if (data.user["displayname"].length) {
                    $view.find('.displaynametag').html("(" + data.user["displayname"] + ")");
                }

                $view.find('.update-el').each(function (i, el) {
                    id = $(this).attr("id");
                    id = id.replace('{id}', 1265);
                    $(this).attr("id", id);
                });
                $view.find('.update-el').on('input', function () {
                    var val = $(this).val();
                    getBooks(val, $(this));
                });
                $view.find('.bookbody').html("");
                for (i = 0; i < data.books.length; i++) {
                    el = data.books[i];
                    bookdata[el.ubid] = el.title;
                    $view.find('.bookbody').append('<tr id="book_' + el.ubid + '"><td>' + (i + 1) + '</td><td><a class="fa fa-trash" onclick="DeleteUBook(' + el.ubid + ');"></a></td><td>' + el.title + '</td><td>' + el.cname + '</td></tr>');
                }

                $view.find('.update-cover').attr('src', thumb(data.user.cover, 300));
                $view.find('.update-avatar').attr('src', thumb(data.user.avatar, 150));
                $html.html($view);
            },
            error: function () {
                $html.html('<h3 class="text-warning text-center">Conection Error</h3>');
            }
        });
    }//Alireza Balvardi
    function edit_user(btn, id) {
        var $html = $('<div/>', {'id': 'edit-user'});
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);

        $.ajax({
            type: "POST",
            url: 'admin/api/getUserInfo/' + id,
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
                    $html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
                    return;
                }
                var $view = $('.view-sample').clone(true);

                $view.find('.update-el').each(function (i, el) {
                    var val = data.user[$(el).attr('name')];
                    $(el).val(val).data('prevdata', val);
                });

                $view.find('.update-cover').attr('src', thumb(data.user.cover, 300));
                $view.find('.update-avatar').attr('src', thumb(data.user.avatar, 150));

                $view.find('.sample-send').on('click', function () {
                    update_user(this, id);
                });
                $html.html($view);
                $html.find("[name=active]").trigger('change');
            },
            error: function () {
                $html.html('<h3 class="text-warning text-center">Conection Error</h3>');
            }
        });
    }

    function update_user(btn, id) {
        $(btn).addClass('l w h6');

        var form = $(btn).closest('form');

        /*$(form).find('select').add($(form).find('input')).each(function(i,el){
            if( $(el).val() == $(el).data('prevdata') ) $(el).attr('disabled', true);
        });*/

        var data = $(form).serialize();

        $.ajax({
            type: "POST",
            url: 'admin/api/updateUser/' + id,
            data: data,
            dataType: "json",
            success: function (data) {
                if (data == "login") {
                    login(function () {
                        update_user(btn, id)
                    });
                    return;
                } else {
                    $(btn).closest('form').find('.ajax-result').html(get_alert(data));
                    notify(data.msg, data.status);
                }
                $(btn).removeClass('l w');
                console.log(data);
            },
            error: function (a, b, c) {
                console.log(a, b, c);
                $(btn).removeClass('l w');
                notify('خطا در اتصال', 2);
            }
        });
    }

    function showhighlightuser(btn, id) {
        var $html = $('<div/>');
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);
        $.ajax({
            type: "POST",
            url: 'admin/api/getUserHL/' + id,
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
                    $html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
                    return;
                }
                username = data.user["username"];
                userid = data.user["id"];
                var $view = $('.view-highlights').clone(true);

                $view.find('.usernametag').html(data.user["username"]);
                if (data.user["displayname"].length) {
                    $view.find('.displaynametag').html("(" + data.user["displayname"] + ")");
                }

                $view.find('.highlightbody').html("");
                for (i = 0; i < data.result.length; i++) {
                    el = data.result[i];
                    html =
                        '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td style="text-align:justify">' +
                        '<h2>' + el.bookname + '</h2>' +
                        '<h4>' + el.highlight_text + '</h4>' +
                        '<h3 class="gray" style="border-left:2px solid ' + color[el.highlight_color] + '">' + el.highlight_description + '</h3>' +
                        '</td>' +
                        '</tr>';
                    $view.find('.highlightbody').append(html);
                }

                $html.html($view);
            },
            error: function () {
                $html.html('<h3 class="text-warning text-center">Conection Error</h3>');
            }
        });
    }//Alireza Balvardi
    function showmobileuser(btn, id) {
        var $html = $('<div/>');
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);
        $.ajax({
            type: "POST",
            url: 'admin/api/getUserMobiles/' + id,
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
                    $html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
                    return;
                }
                username = data.user["username"];
                userid = data.user["id"];
                var $view = $('.view-mobiles').clone(true);

                $view.find('.usernametag').html(data.user["username"]);
                if (data.user["displayname"].length) {
                    $view.find('.displaynametag').html("(" + data.user["displayname"] + ")");
                }

                $view.find('.mobilebody').html("");
                for (i = 0; i < data.result.length; i++) {
                    el = data.result[i];
                    html =
                        '<tr id="mobile_' + el.id + '">' +
                        '<td style="text-align:justify">' +
                        '<h2 class="gray" dir="ltr"> ' + (i + 1) + '.' + el.mobilemodel + '( OS : ' + el.android + ' ) [ AppVer : ' + el.AppVer + ' ]<span class="xbtn pull-right"><i class="fa fa-close text-danger" data-title="حذف" onclick="DeleteMobile(' + el.id + ');"></i></span></h2>' +
                        '</td>' +
                        '</tr>';
                    $view.find('.mobilebody').append(html);
                }

                $html.html($view);
            },
            error: function () {
                $html.html('<h3 class="text-warning text-center">Conection Error</h3>');
            }
        });
    }//Alireza Balvardi
    function DeleteMobile(id) {
        if (!confirm("آیا مطمئن هستید ؟"))
            return;
        $.ajax({
            type: "POST",
            url: 'admin/api/DeleteUserMobile/' + id,
            dataType: "json",
            success: function (data) {
                if (data.done) {
                    $('#mobile_' + id).slideUp('slow');
                } else {
                    alert(data.msg);
                }
            },
            error: function () {
                $html.html('<h3 class="text-warning text-center">Conection Error</h3>');
            }
        });
    }//Alireza Balvardi
    function showMembership(btn, id, isNew) {
        let $html, $view;
        if (isNew) {
            prevbtn = btn;
            $html = $('<div/>');
            $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
            popupScreen($html);
            $view = $('.view-membership').clone(true);
            $view = $view.removeClass('view-membership').addClass('BaseMembership');
            $view.find('.TBLAPPENDMEMBERSHIP').removeClass('TBLAPPENDMEMBERSHIP').addClass('tblappendmembership');
            $view.find('.DATA-ADD-MEMBERSHIP').removeClass('DATA-ADD-MEMBERSHIP').addClass('data-add-membership');
            $view.find('.DIVAPPENDMEMBERSHIP').removeClass('DIVAPPENDMEMBERSHIP').addClass('divappendmembership');
            $view.find('.NEWAPPENDMEMBERSHIP').removeClass('NEWAPPENDMEMBERSHIP').addClass('newappendmembership');
            $view.find('.ESCAPEMEMBERSHIP').removeClass('ESCAPEMEMBERSHIP').addClass('escapemembership');
            $view.find('.MEMBERSHIP').removeClass('MEMBERSHIP').addClass('membership');
            $view.find('.APPENDMEMBERSHIP').removeClass('APPENDMEMBERSHIP').addClass('appendmembership');
        } else {
            btn = prevbtn;
            $html = $('.tblappendmembership');
            $view = $('.membership');
            $view.html("");
        }
        $.ajax({
            type: "POST",
            url: 'admin/api/getDataMembership/' + id,
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
                    $html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
                    return;
                }
                if (isNew && data.user["displayname"].length) {
                    $view.find('.displaynametag').html("(" + data.user["displayname"] + ")");
                    $view.find('.appendmembership').data('user', data.user["id"]);

                }
                for (i = 0; i < data.result.length; i++) {
                    el = data.result[i];
                    html =
                        '<tr>' +
                        '<th>' + (i + 1) + '</th>' +
                        '<th>' + el[0] + '</th>' +
                        '<th>' + el[1] + '</th>' +
                        '<th>' + el[2] + '</th>' +
                        '<th>' + el[3] + '</th>' +
                        '<th>' + el[4] + '</th>' +
                        '<th>' + el[5] + '</th>' +
                        '<th class="text-center"><div class="btn pt-0 pb-0 btn-danger removemembership" data-dest="' + el[6] + '" data-id="' + el[7] + '">'+'حذف'+'</div></th>' +
                        '</tr>';
                    html = html.replace(/null/g, '');
                    if (isNew) {
                        $view.find('.membership').append(html);
                    } else {
                        $view.append(html);
                    }
                }
                if (isNew) {
                    $html.html($view);
                }
            },
            error: function () {
                $html.html('<h3 class="text-warning text-center">Conection Error</h3>');
            }
        });
    }//Alireza Balvardi
    $(function () {
        $('body').on('click', '.appendmembership', function () {
            $('.divappendmembership,.tblappendmembership').slideToggle('slow');
            let newappendmembership = $(this).closest('.data-add-membership').find('.newappendmembership table');
            if (newappendmembership.find('tr').length < 2) {
                var id = $(this).data('user');
                $.ajax({
                    type: "POST",
                    url: 'admin/api/getAllMembership/' + id,
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
                            newappendmembership.html('<tr><td><h3 class="text-warning text-center">' + data.msg + '</h3></td></tr>');
                            return;
                        }
                        var section = {"membership": "عضویت", "category": "دسته بندی"};
                        let j = 1;
                        for (i = 0; i < data.result.length; i++) {
                            el = data.result[i];
                            html = '';
                            switch (el[0]) {
                                case "membership":
                                    html +=
                                        '<tr>' +
                                        '<td>' + (j++) + '</td>' +
                                        '<td>' + section[el[0]] + '</td>' +
                                        '<td>' + el[1].title + '</td>' +
                                        '<td>' + el[1].allowmonths + '</td>' +
                                        '<td>' + el[1].price + '</td>' +
                                        '<td><div class="btn btn-primary setnewmembership" data-src="' + id + ':' + el[0] + ':' + el[1].id + ':' + el[1].allowmonths + '">' + 'افزودن' + '</div></td>' +
                                        '</tr>';
                                    break;
                                case "category":
                                    html +=
                                        '<tr>' +
                                        '<td>' + (j++) + '</td>' +
                                        '<td>' + section[el[0]] + '</td>' +
                                        '<td>' + el[1].name + '</td>' +
                                        '<td>' + 1 + '</td>' +
                                        '<td>' + el[1].membership1 + '</td>' +
                                        '<td><div class="btn btn-primary setnewmembership" data-src="' + id + ':' + el[0] + ':' + el[1].id + ':1">' + 'افزودن' + '</div></td>' +
                                        '</tr>';
                                    html +=
                                        '<tr>' +
                                        '<td>' + (j++) + '</td>' +
                                        '<td>' + section[el[0]] + '</td>' +
                                        '<td>' + el[1].name + '</td>' +
                                        '<td>' + 3 + '</td>' +
                                        '<td>' + el[1].membership3 + '</td>' +
                                        '<td><div class="btn btn-primary setnewmembership" data-src="' + id + ':' + el[0] + ':' + el[1].id + ':3">' + 'افزودن' + '</div></td>' +
                                        '</tr>';
                                    html +=
                                        '<tr>' +
                                        '<td>' + (j++) + '</td>' +
                                        '<td>' + section[el[0]] + '</td>' +
                                        '<td>' + el[1].name + '</td>' +
                                        '<td>' + 6 + '</td>' +
                                        '<td>' + el[1].membership6 + '</td>' +
                                        '<td><div class="btn btn-primary setnewmembership" data-src="' + id + ':' + el[0] + ':' + el[1].id + ':6">' + 'افزودن' + '</div></td>' +
                                        '</tr>';
                                    html +=
                                        '<tr>' +
                                        '<td>' + (j++) + '</td>' +
                                        '<td>' + section[el[0]] + '</td>' +
                                        '<td>' + el[1].name + '</td>' +
                                        '<td>' + 12 + '</td>' +
                                        '<td>' + el[1].membership12 + '</td>' +
                                        '<td><div class="btn btn-primary setnewmembership" data-src="' + id + ':' + el[0] + ':' + el[1].id + ':12">' + 'افزودن' + '</div></td>' +
                                        '</tr>';
                                    break;
                            }
                            html = html.replace(/null/g, '');
                            newappendmembership.append(html);
                        }
                    },
                    error: function () {
                        newappendmembership.html('<tr><td><h3 class="text-warning text-center">Conection Error</h3></td></tr>');
                    }
                });
            }
        });
        $('body').on('click', '.escapemembership', function () {
            $('.divappendmembership,.tblappendmembership').slideToggle('slow');
        });
        $('body').on('click', '.setnewmembership', function () {
            let src = $(this).data('src');
            src = src.split(":");
            $.ajax({
                type: "POST",
                data: {src: src},
                url: 'admin/api/saveAdminMembership/' + src[0],
                dataType: "json",
                success: function (data) {
                    let newappendmembership = $(this).closest('.data-add-membership').find('.newappendmembership');

                    if (data == "login") {
                        popupScreen('');
                        login(function () {
                            edit_user(btn, id)
                        });
                        return;
                    }

                    if (!data.done) {
                        newappendmembership.html('<tr><td><h3 class="text-warning text-center">' + data.msg + '</h3></td></tr>');
                        return;
                    }
                    $('.divappendmembership,.tblappendmembership').slideToggle('slow');
                    showMembership(null, src[0], 0)
                },
                error: function () {
                    newappendmembership.html('<tr><td><h3 class="text-warning text-center">Conection Error</h3></td></tr>');
                }
            });
        });
        $('body').on('click', '.removemembership', function () {
            if(confirm('آیا مطمئن هستید که می خواهید اشتراک انتخاب شده را حذف نمایید؟')) {
                $(this).after('<i class="myspinner spin fa fa-spinner fa-lg text-muted ms-2 me-2 progress-bar-animated" title="در انتظار"></i>');
                var dest = $(this).data('dest');
                var id = $(this).data('id');
                var delappendmembership = $(this).closest('tr');
                $.ajax({
                    type: "POST",
                    data: {src: [dest, id]},
                    url: 'admin/api/deleteAdminMembership',
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
                            delappendmembership.after('<tr id="'+dest+id+'"><td colspan="8"><h3 class="text-warning text-center">' + data.msg + '</h3></td></tr>');
                            delappendmembership.find('.myspinner').remove();
                            setTimeout(function () {
                                $('#'+dest+id).slideToggle('slow');
                            },2000);
                            return;
                        }
                        delappendmembership.slideToggle('slow');
                    },
                    error: function () {
                        delappendmembership.after('<tr id="'+dest+id+'" colspan="8"><td><h3 class="text-warning text-center">Conection Error</h3></td></tr>');
                        delappendmembership.find('.myspinner').remove();
                        setTimeout(function () {
                            $('#'+dest+id).slideToggle('slow');
                        },2000);
                    }
                });
            }
        });
    });
</script>
