<div class="levels-div">
    <?php

    //var_dump($levels);

    $data_arr = array(
        'اطلاعات کلی وآمار سایت' => array(
            '_read_site_info'     => 'آمار',
            '_site_local_info'    => 'آمار کلی',
            '_site_visits'        => 'آمار بازدید',
            '_site_users_info'    => 'آمار کاربران',
            '_site_tools_info'    => 'آمار ابزارها',
            '_site_missions_info' => 'آمار ماموریت ها',
        ),
    );


    $post_type_sample = array(
        '_read_' => 'مشاهده',
        '_readown_' => 'فقط کتابهای خودش',
        '_creat_' => 'نوشتن',
        '_submit_' => 'تایید',
        '_suspend_' => 'انتقال به زباله دان',
        '_edit_' => 'ویرایش',
        '_delete_' => 'حذف',
        '_category_' => 'دسته بندی'
    );

    global $POST_TYPES;

    if (isset($POST_TYPES)) {
        foreach ($POST_TYPES as $type_name => $type) {
            foreach ($post_type_sample as $per => $per_name) {
                if ($per == '_category_' && !in_array('category', $type['support'])) continue;
                $data_arr['دسترسی به ' . $type['g_name']][$per . $type_name] = $per_name;
            }
            if (isset($type['menu'])) {
                foreach ($type['menu'] as $menu_name => $menu)
                    $data_arr['دسترسی به ' . $type['g_name']]['_' . $menu_name . '_' . $type_name] = $menu['name'];
            }
        }
    }

    $data_arr1 = array(

        'دسترسی به نظرات' => array(
            '_read_comment' => 'مشاهده',
            '_submit_comment' => 'تایید',
            '_reply_comment' => 'پاسخ دادن',
            //'_suspend_comment' => 'انتقال به زباله دان',
            '_delete_comment' => 'حذف',
        ),
        'دسترسی به پیام ها' => array(
            '_read_msg' => 'مشاهده',
            //'_suspend_msg'=>'انتقال به زباله دان',
            '_reply_msg' => 'پاسخ دادن',
            '_delete_msg' => 'حذف',
        ),
        'دسترسی به گروه ها' => array(
            '_read_group'   => 'مشاهده',
            '_edit_group'   => 'ویرایش ',
            '_delete_group' => 'حذف',
        ),
        'دسترسی به ابزارها' => array(
            '_read_tools'   => 'مشاهده',
            '_submit_tools' => 'تایید',
            '_edit_tools'   => 'ویرایش',
            '_delete_tools' => 'حذف',
        ),
        'دسترسی به ماموریت ها' => array(
            '_read_missions'   => 'مشاهده',
            '_submit_missions' => 'تایید',
            '_delete_missions' => 'حذف',
        ),
        'دسترسی به فایل ها' => array(
            '_access_admin_file' => 'دسترسی به فایل های مدیر',
            '_upload_file' => 'آپلود',
            '_delete_file' => 'حذف',
        ),
        'دسترسی به بخش کاربران' => array(
            '_manage_users' => 'دسترسی به بخش کاربران',
            '_edit_user_levels' => 'سطوح دسترسی',
            '_creat_user' => 'افزودن',
            '_edit_user' => 'ویرایش',
            '_edit_user_role' => 'تغییر نقش',
            '_delete_user' => 'حذف',
        ),
        'دسترسی به موارد مالی' => array(
            '_manage_payment' => 'مشاهده بخش پرداخت ها',
            '_manage_salereport' => 'مشاهده بخش گزارش فروش',
            '_manage_gozaresh' => 'مشاهده بخش گزارش مالی',
        ),
        'دسترسی به کدهای تخفیف' => array(
            '_manage_discount' => 'مشاهده بخش کدهای تخفیف',
        ),
        'دسترسی به تنظیمات' => array(
            '_change_settings' => 'دسترسی به  بخش تنظیمات',
        ),
        'عرضه کننده می باشد' => array(
            '_is_supplier' => 'بلی',
        ),
    );

    $data_arr = array_merge($data_arr, $data_arr1);

    function printLevel($title, $level, $data)
    {
        ?>
        <tr>
        <td><?php echo $title ?></td>
        <td style="text-align:right"><?php

            foreach ($data as $field => $name) {
                $checked = @$level[$field] == 1 ? "checked" : "";
                ?>
                <label class="n-sel">
                    <input class="<?php echo $field ?>" type="checkbox"
                           name="<?php echo $field ?>" <?php echo $checked ?> value="1"><?php echo $name ?>
                </label> &nbsp;
                <?php
            }
            ?></td></tr><?php

    }

    foreach ($levels as $level_id => $level) {
        ?>
        <form>
            <div class="clear" style="height:25px"></div>

            <table class="table light2 user-levels-table " item="<?php echo $level_id ?>">
                <tr>
                    <th colspan="2">
                        <i class="fa fa-times fa-2x cu red" onClick="deleteUserLevel(this)" style="float:left"
                           title="حذف این سطح"></i>
                        <i class="fa fa-check fa-2x cu" onClick="updateUserLevel(this)"
                           style="float:left;margin-left:15px;color:#04B104;" title="ذخیره تغییرات"></i>
                        <span style="font-size:16px"><?php echo $level['level_name'] ?></span>
                    </th>
                </tr>
                <tr>
                    <td>
                        نام نقش
                    </td>
                    <td style="text-align:right">
                        <input name="level_name" type="text" class="input" value="<?php echo $level['level_name'] ?>"
                               style="margin:0px; width:40%">
                    </td>
                </tr>
                <?php

                foreach ($data_arr as $title => $data)
                    printLevel($title, $level, $data);

                ?>
            </table>
        </form>

        <?php
    }

    ?>

</div>

<div align="center" style="margin:15px 0 "><input type="button" class="button" value="افزودن"
                                                  onClick="$('#new-level').show()"/></div>

<div align="left" class="ar ajx"></div>


<div class="full-screen" id="new-level">

    <div class="content" style="padding:20px">
        <div class="form">
            <form>
                <div class="clear" style="height:25px"></div>

                <table class="table light2 user-levels-table " item="">
                    <tr>
                        <th colspan="2">
                            <i class="fa fa-times fa-2x cu red" onClick="deleteUserLevel(this)"
                               style="float:left;display:none" title="حذف این سطح"></i>
                            <i class="fa fa-check fa-2x cu" onClick="updateUserLevel(this)"
                               style="float:left;margin-left:15px;color:#04B104;display:none" title="ذخیره تغییرات"></i>
                            <span style="font-size:16px">نقش جدید</span>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            نام نقش
                        </td>
                        <td style="text-align:right">
                            <input name="level_name" type="text" class="input" value="نقش جدید"
                                   style="margin:0px; width:40%">
                        </td>
                    </tr>
                    <?php

                    foreach ($data_arr as $title => $data)
                        printLevel($title, [], $data);

                    ?>
                </table>
            </form>
        </div>
        <div>
            <input type="button" class="button" value="افزودن" onClick="addUserLevel(this.parentNode)"/>
            <input type="button" class="button" value="لغو" onClick="$('#new-level').hide()"/>
        </div>
    </div>

</div>

<div class="full-screen" id="delete-level">

    <div class="content" style="padding:100px">
        <div class="append col-sm-6 col-sm-offset-3"></div>
        <div class="col-sm-6 col-sm-offset-3">
            <input type="button" class="btn btn-warning" value="ادامه" id="delete-btn"/>
            <input type="button" class="btn btn-info" value="لغو" onClick="$('#delete-level').hide()"/>
        </div>
    </div>

</div>

<script>
    $(document).ready(function (e) {

        $(document).on("click", "[class^='_read_'],[class^='_manage_users']", function (e) {
            checkLevel(this, 'read');
        });

        $(document).on("click", "._access_admin_file", function (e) {
            checkLevel(this, 'file');
        });

        $("[class^='_read_'],[class^='_manage_users']").each(function (index, el) {
            checkLevel(el, 'read');
        });
        $('._access_admin_file').each(function (index, el) {
            checkLevel(el, 'file');
        });

    });

    function checkLevel(el, op) {

        switch (op) {
            case 'read':
                var $chk = $(el).closest('tr')
                    //.find("[class^='_creat_'],[class^='_submit_'],[class^='_suspend_'],[class^='_edit_'],[class^='_delete_'],[class^='_reply_']");
                    .find("input[type=checkbox]").not(':first');
                $chk.prop("disabled", $(el).is(":checked") ? "" : "disabled");
                break;

            case 'file':
                var upload = $(el).closest('tr').find('._upload_file');
                if (!$(el).is(":checked"))
                    $(upload).prop("checked", "checked").prop("disabled", "disabled");
                else
                    $(upload).prop("disabled", "");
                break;
        }
    }

    function addUserLevel(btn) {

        $(btn).addClass('l blue');

        var id = 0;
        $('.levels-div table').each(function (index, el) {
            if ($(el).attr('item') > id)
                id = $(el).attr('item');
        });

        id++;

        var form = $('#new-level .content form'), data = getUserLevelFormData(form);
        $.ajax({
            url: URL + "/adduserlevel/" + id,
            type: "POST",
            data: {level: data},
            dataType: "json",
            success: function (data) {

                if (data == 'login')
                    login(function () {
                        addUserLevel(btn);
                    });
                else {
                    if (data.done == 1) {
                        $('#new-level').fadeOut();
                        var div = $('#new-level .content .form ').clone(true);
                        $(div).find('table').attr('item', id).find('.fa').show();
                        $(div).appendTo('.levels-div');
                    }
                    else
                        dialog_box('خطا در ایجاد نقش جدید');
                }
                $(btn).removeClass('l bule');
            }
        });
    }

    function updateUserLevel(btn) {

        var data = getUserLevelFormData($(btn).closest('form')), id = $(btn).closest('table').attr('item');

        $(btn).css('color', '#ccc').addClass('l');

        $.ajax({
            url: URL + "/updateuserlevel/" + id,
            type: "POST",
            data: {level: data},
            dataType: "json",
            success: function (data) {
                if (data == 'login')
                    login(function () {
                        updateUserLevel(btn);
                    });
                else {
                    if (data.done != 1)
                        dialog_box('بروز رسانی صورت نگرفت');
                }
                $(btn).css('color', '#04B104').removeClass('l');
            }
        });
    }

    function deleteUserLevel(btn) {

        var form = $(btn).closest('form'), id = $(form).find('table').attr('item');

        var $sel = $('<select/>').addClass('form-control')
            .append($('<option/>').html('کاربر').val('user'));

        $('.levels-div .user-levels-table').not($(btn).closest('table')).each(function (index, el) {

            var id = $(el).attr('item'), name = $(el).find('input[name="level_name"]').val();

            $('<option/>').html(name).val(id).appendTo($sel);
        });

        var $div = $('<div/>')
            .append($('<h1/>').html('با کاربران ثبت شده با این نقش چه کنیم ؟'))
            .append('<hr/>')
            .append('<p>اگر کاربری با این نقش وجود دارد ، تبدیل شود به : </p>')
            .append($sel)
            .append('<hr/>');

        $('#delete-level').show().find('.content .append').html($div);

        $('#delete-btn').on("click", function () {

            var p = $(this).parent(), Replace = $($sel).val();

            $(p).addClass('l bule');
            $.ajax({
                url: URL + "/deleteuserlevel/" + id + '/' + Replace,
                type: "POST",
                data: {},
                dataType: "json",
                success: function (data) {

                    if (data == 'login')
                        login(function () {
                            deleteUserLevel(btn);
                        });
                    else {
                        if (data.done == 1)
                            $('#delete-level').fadeOut(300, function () {
                                $(form).slideUp(1000, function () {
                                    $(form).remove();
                                    set_window_size()
                                });
                            });
                        else
                            dialog_box('خطا در حذف');
                    }
                    $(p).removeClass('l bule');
                }
            });
        });
    }


    function getUserLevelFormData(form) {

        var data = {};

        $(form).find('input').each(function (index, el) {

            var name = el.name, val = el.value;

            if ($(el).attr("type") == "checkbox") {

                if ($(el).is(":disabled"))
                    val = $(el).hasClass('_access_admin_file') ? 1 : 0;
                else
                    val = $(el).is(":checked") ? 1 : 0;

            }
            data[name] = val;
        });
        return data;
    }

</script>