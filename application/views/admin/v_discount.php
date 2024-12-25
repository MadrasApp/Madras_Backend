<?php
$this->load->helper('inc');
$inc = new inc;
$cols = array(
    /*'  ' => array(
        'field_name' => 'id',
        'th-attr' => 'style="width:40px;"',
        'td-attr' => 'style="padding:7px" align="center"',
        'link' => false,
        'html' => '<i class="fa fa-info-circle fa-2x text-info" data-id="[ID]"></i>'
    ),	*/
    'شماره' => array(
        'field_name' => 'id',
        'th-attr' => 'style="width:60px;"',
        'td-attr' => 'align="center" class="en"',
    ),
    'کد' => array(
        'field_name' => 'code',
        'td-attr' => 'style="font-weight:bold;padding:0" align="center" class="en"',
        'function' => function ($col, $row) {

            $bg = $row['used'] == 1 ? '888' : '00cc76';
            $bg = (!is_null($row['expdate']) && $row['expdate'] < time()) ? 'f00' : $bg;
            return "<div style=\"color:#fff;background-color:#{$bg};padding:4px;font-size:20px\">{$col}</div>";
        }
    ),
    'درصد تخفیف' => array(
        'field_name' => 'percent',
        'td-attr' => 'align="center" class="en"',
        'function' => function ($col, $row) {
            if ($col < 90)
                return $col . '%';
            else
                return '<b class="text-danger">' . $col . '%</b>';
        }
    ),
    'هزینه ثابت تخفیف' => array(
        'field_name' => 'fee',
        'td-attr' => 'align="center" class="en"'
    ),
    'سطح' => array(
        'field_name' => 'level',
        'td-attr' => ' align="center"',
        'function' => function ($col, $row) {
            $out = $col;
            switch ($row['category_id']) {
                case -1:
                    $book = $this->db
                        ->where('id', $row['bookid'])
                        ->get('posts')->row();
                    $out = $col . '( ' . @$book->title . ' )';
                    break;
                case -5:
                    $doreh = $this->db
                        ->where('d.id', $row['bookid'])
                        ->join('ci_tecat t', 't.id=d.tecatid', 'inner', FALSE)
                        ->get('doreh d')->row();
                    $out = $col . '( ' . @$doreh->name . ' )';
                    break;
                case -6:
                    $dorehclass = $this->db
                        ->where('dc.id', $row['bookid'])
                        ->join('ci_tecat t', 't.id=d.tecatid', 'inner', FALSE)
                        ->join('ci_dorehclass dc', 'dc.dorehid=d.id', 'inner', FALSE)
                        ->join('ci_classroom c', 'c.id=dc.classid', 'inner', FALSE)
                        ->get('doreh d')->row();
                    $out = $col . '( ' . @$dorehclass->name . ' / ' . @$dorehclass->title . ' )';
                    break;
                case -9:
                case -7:
                    $out = $col;
                    break;
                case -81:
                case -83:
                case -84:
                case -812:
                    $category = $this->db
                        ->where('d.id', $row['bookid'])
                        ->get('category d')->row();
                    $out = $col . '( ' . @$category->name . ' )';
                    break;
            }
            return $out;
        }
    ),
    'یادداشت' => array(
        'field_name' => 'price',
        'td-attr' => ' align="center"'
    ),
    'سقف استفاده' => array(
        'field_name' => 'maxallow',
        'td-attr' => 'align="center"',
        'function' => function ($col, $row) {
            return $row['maxallow'];
        }
    ),
    'وضعیت استفاده' => array(
        'field_name' => 'used',
        'td-attr' => 'align="center"',
        'function' => function ($col, $row) {
            if ($col == $row['maxallow']) return '<i class="fa fa-check-circle-o fa-lg text-success"></i> ';
            if ($col && $col < $row['maxallow']) return '<i class="fa fa-check-circle-o fa-lg text-warning"></i> ';
            if ($col == 0) return '<i class="fa fa-ban fa-lg text-muted"></i> ';
        }
    ),
    'تعداد استفاده' => array(
        'field_name' => 'used',
        'td-attr' => 'align="center"',
        'function' => function ($col, $row) {
            return $row['used'];
        }
    ),
    'شماره فاکتور' => array(
        'field_name' => 'factor_id',
        'link' => true,
        'function' => function ($col, $row) {

            if ($col == '') return '---';

            return '<a target="_blank" href="' . site_url('admin/payment') . '?f.id=' . $col . '">' . $col . '</a>';
        }
    ),
    'تاریخ ایجاد' => array('field_name' => 'cdate', 'link' => true, 'type' => 'strtime', 'td-attr' => 'align="center"'),
    'تاریخ انقضا' => array('field_name' => 'expdate', 'link' => true, 'type' => 'strtime', 'td-attr' => 'align="center"'),
    'تاریخ استفاده' => array('field_name' => 'udate', 'link' => true, 'type' => 'strtime', 'td-attr' => 'align="center"'),
    '   ' => array(
        'field_name' => 'id',
        'link' => false,
        'html' => '<i class="fa fa-trash text-danger cu" onClick="delete_row(this,\'discounts\',[ID])"></i>'
    ),
);


$q = "SELECT d.* , 
           CASE
                WHEN d.category_id = -81 THEN 'اشتراک یک ماهه'
                WHEN d.category_id = -83 THEN 'اشتراک سه ماهه'
                WHEN d.category_id = -86 THEN 'اشتراک شش ماهه'
                WHEN d.category_id = -812 THEN 'اشتراک یک ساله'
                WHEN d.category_id = -7 THEN 'اشتراک'
                WHEN d.category_id = -9 THEN 'کلاس آنلاین'
                WHEN d.category_id = 0 THEN 'همه جا'
                WHEN d.category_id = -1 THEN 'کتاب'
                WHEN d.category_id = -2 THEN 'همه کتابها'
                WHEN d.category_id = -5 THEN 'یک دوره'
                WHEN d.category_id = -6 THEN 'یک دوره کلاس'
                ELSE c.name
            END
               AS level 
			FROM ci_discounts d 
			LEFT JOIN `ci_category` `c` ON `c`.`id`=`d`.`category_id` 
		$query";
echo $searchHtml;
?>
<div class="text-center" style="margin-bottom:20px">
    <?php if ($subtitle) { ?>
        <h2><?php echo $subtitle; ?></h2>
    <?php } ?>
    <button type="button" class="btn btn-success pull-left" onclick="LoadCalendar($('#add-div').html());">
        <i class="fa fa-plus-circle"></i>
        <span>افزودن</span>
    </button>
</div>

<?php $inc->createTable($cols, $q, 'id="table" class="table light2" ', $tableName, 60); ?>


<div id="add-div" class="hidden">
    <form class="clearfix">
        <div class="row" style="margin-top:70px">
            <div class="col-md-6 col-md-offset-3">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" class="form-control en" name="code" placeholder="کد تخفیف به لاتین"
                                       required/>
                                <span class="input-group-addon en">کد</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="number" min="1" class="form-control en" name="maxallow"
                                       placeholder="تعداد کاربری که می توانند استفاده کنند" required/>
                                <span class="input-group-addon en">سقف استفاده</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="number" class="form-control en discount OnlyNum" name="percent" min="0"
                                       max="<?php echo $ownerpercent; ?>" placeholder="?" required/>
                                <p class="text-muted form-control" style="font-size:10px">حداکثر سقف تخفیف مجاز برای شما
                                    : <span dir="ltr"><?php echo $ownerpercent; ?>%</span></p>
                                <span class="input-group-addon en">تخفیف %</span>
                            </div>
                            <?php if ($ownerpercent == 100) { ?>
                                <div class="input-group">
                                    <input type="number" class="form-control en discountfee OnlyNum" name="fee"
                                           placeholder="?" required value="0"/>
                                    <span class="input-group-addon en">مبلغ ثابت</span>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" class="form-control {dateFormat}" name="expdate" id="{expdate}"
                                       placeholder="تاریخ انقضا" required/>
                                <span class="input-group-addon en">تاریخ انقضا</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <input type="hidden" class="form-control en discount" name="catmembership"/>
                            <input type="text" class="form-control" name="price" placeholder="یادداشت"/>
                            <p class="text-muted" style="font-size:10px">این فیلد هیچ کاربردی ندارد و فقط جهت به خاطر
                                سپردن می باشد</p>
                            <div class="input-group hide bookdivid">
                                <input type="text" class="form-control en bookinputid" placeholder="?"/>
                                <input type="hidden" class="form-control en discount" name="bookid" placeholder="?"/>
                                <span class="input-group-addon en">نام کتاب</span>
                            </div>
                            <div class="input-group hide categorydivid">
                                <input type="text" class="form-control en categoryinputid"
                                       placeholder="شماره ID ها را با کاما جدا کنید"/>
                                <input type="hidden" class="form-control en discount" name="categoryid" placeholder=""/>
                                <span class="input-group-addon en">ID گروه</span>
                            </div>
                            <div class="input-group hide multibookdivid">
                                <input type="text" class="form-control en multibookinputid"
                                       placeholder="شماره ID ها را با کاما جدا کنید"/>
                                <input type="hidden" class="form-control en discount" name="multibookid"
                                       placeholder=""/>
                                <span class="input-group-addon en">ID کتاب</span>
                            </div>
                            <div class="input-group hide dorehdivid">
                                <input type="text" class="form-control en dorehinputid" placeholder="?"/>
                                <input type="hidden" class="form-control en discount" name="dorehid" placeholder="?"/>
                                <span class="input-group-addon en">نام دوره</span>
                            </div>
                            <div class="input-group hide dorehclassdivid">
                                <input type="text" class="form-control en dorehclassinputid" placeholder="?"/>
                                <input type="hidden" class="form-control en discount" name="dorehclassid"
                                       placeholder="?"/>
                                <span class="input-group-addon en">نام دوره کلاس</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <select class="form-control level" name="level[]" required multiple="multiple">
                                <option value="" disabled selected> انتخاب سطح</option>
                                <option value="0" data-price="0">همه جا</option>
                                <option value="-1" data-price="0">یک کتاب</option>
                                <option value="-2" data-price="0">همه کتابها</option>
                                <option value="-3" data-price="0">تک تک کتابهای یک گروه</option>
                                <option value="-4" data-price="0">چند کتاب</option>
                                <optgroup label="تخفیف کتابها" style="color: #105b06">
                                    <?php foreach ($categories as $cat) { ?>
                                        <option value="<?php echo $cat->id ?>"
                                                data-price="<?php echo (int)$cat->price ?>"><?php echo $cat->name ?></option>
                                    <?php } ?>
                                </optgroup>
                                <optgroup label="تخفیف دوره">
                                    <option value="-5" data-price="0">یک دوره</option>
                                    <option value="-6" data-price="0">یک دوره کلاس</option>
                                    <option value="-7" data-price="0">اشتراک</option>
                                    <option value="-9" data-price="0">کلاس آنلاین</option>
                                </optgroup>
                                <optgroup label="تخفیف اشتراک دسته بندی" style="color: #0a53be">
                                    <?php foreach ($catmemberships as $cat) { ?>
                                        <option value="-81" data-price="<?php echo (int)$cat->membership1 ?>"
                                                data-bookid="<?php echo $cat->id ?>">
                                            اشتراک یکماهه <?php echo $cat->name ?>
                                        </option>
                                        <option value="-83" data-price="<?php echo (int)$cat->membership3 ?>"
                                                data-bookid="<?php echo $cat->id ?>">
                                            اشتراک سه ماهه <?php echo $cat->name ?>
                                        </option>
                                        <option value="-86" data-price="<?php echo (int)$cat->membership6 ?>"
                                                data-bookid="<?php echo $cat->id ?>">
                                            اشتراک شش ماهه <?php echo $cat->name ?>
                                        </option>
                                        <option value="-812" data-price="<?php echo (int)$cat->membership12 ?>"
                                                data-bookid="<?php echo $cat->id ?>">
                                            اشتراک یکساله <?php echo $cat->name ?>
                                        </option>
                                    <?php } ?>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group calc-result"></div>

                <hr/>
                <div class="ajax-result" style="margin-bottom: 20px;"></div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block btn-lg sample-send">
                        <i class="fa fa-check-circle"></i> <span>افزودن</span>
                    </button>
                </div>
            </div>
        </div>
        <script>$('#expdate').datepicker({dateFormat: 'yy-mm-dd', defaultDate: ''});</script>
    </form>
</div>

<script type="text/javascript">
    var bookprice = 0;
    var bookname = '';
    $(document).ready(function () {
        $(document).on('input', '.full-screen .discount', calc_price);
        $(document).on('input', '.full-screen .discountfee', calc_price);
        $(document).on('change', '.full-screen .level', calc_price);

        $(document).on('submit', '.full-screen form', function (e) {
            e.preventDefault();
            submitForm();
        });
    });

    function LoadCalendar(html) {
        html = html.replace('{dateFormat}', 'dateFormat');
        html = html.replace('{expdate}', 'expdate');
        popupScreen(html);
        $('.close-popup').click(function () {
            location.reload();
        });
        $('#popup-screen-1').css("zIndex", 3);

        $('.categorydivid input.categoryinputid').on('input', function () {
            val = $(this).val();
            getCategoryBooks(val);
        });
        $('.bookdivid input.bookinputid').on('input', function () {
            val = $(this).val();
            getBooks(val, $(this));
        });

        $('.multibookdivid input.multibookinputid').on('input', function () {
            val = $(this).val();
            getMultiBooks(val);
        });
        $('.dorehdivid input.dorehinputid').on('input', function () {
            val = $(this).val();
            getDorehs(val, $(this));
        });

        $('.dorehclassdivid input.dorehclassinputid').on('input', function () {
            val = $(this).val();
            getDorehClass(val, $(this));
        });
    }

    function calc_price() {
        var $discount = $('.full-screen .discount'),
            $level = $('.full-screen .level option:selected'),
            $result = $('.full-screen .calc-result');
        $result.html("");

        $('.bookdivid').removeClass('hide');
        $('.multibookdivid').addClass('hide');
        $('.categorydivid').addClass('hide');

        if ($level.val() == "-1") {
            $('.bookdivid').removeClass('hide');
        } else {
            $('.bookdivid').addClass('hide');
        }

        if ($level.val() == "-5") {
            $('.dorehdivid').removeClass('hide');
        } else {
            $('.dorehdivid').addClass('hide');
        }

        if ($level.val() == "-6") {
            $('.dorehclassdivid').removeClass('hide');
        } else {
            $('.dorehclassdivid').addClass('hide');
        }

        if ($level.val() == "-3") {
            $('.categorydivid').removeClass('hide');
            var val = $('.categorydivid input.categoryinputid').last().val();
            getCategoryBooks(val);
            return;
        } else {
            $('.categorydivid').addClass('hide');
        }

        if ($level.val() == "-4") {
            $('.multibookdivid').removeClass('hide');
            var val = $('.multibookdivid input.multibookinputid').last().val();
            getMultiBooks(val);
            return;
        } else {
            $('.multibookdivid').addClass('hide');
        }

        $keys = Object.keys($level);
        $('input[name="catmembership"]').val("");
        $catmembership = "";
        $level.each(function (i, j) {
            textlevel = $(this).text();
            $('input[name="catmembership"]').val("");
            if ($level.val() == "-1") {
                textlevel = bookname;
            }
            if ($level.val() == "-5") {
                textlevel = bookname;
            }
            if ($level.val() == "-6") {
                textlevel = bookname;
            }
            if ($level.val() == "-81" || $level.val() == "-83" || $level.val() == "-86" || $level.val() == "-812") {
                $membership = $('.level option:selected');
                $bookid = $(this).attr("data-bookid");
                $catmembership = $catmembership.length?$catmembership+","+$bookid:$bookid+"";
                console.error($catmembership);
                $('input[name="catmembership"]').val($catmembership);
            }


            var discount = parseInt($discount.val()),
                price = parseInt($(this).attr('data-price'));
            discountfee = parseInt($('.full-screen form').find('input[name="fee"]').val());
            discountfinal = price * discount / 100;
            if (discountfee) {
                discountfinal = discountfee;
            }

            if (isNaN(price) || isNaN(discount)) {
                $result.html('---');
            } else if (discount > 100 || discount < 0) {
                $result.html('<h3 class="text-danger">درصد تخفیف باید بین 0 تا 100 باشد</h3>');
            } else {
                $result.append(
                    '<h3> قیمت ' + textlevel + ': <b class="en">' + formatMoney(price) + '</b>  &nbsp; &nbsp; \
				قیمت با اعمال تخفیف : <b class="en">' + formatMoney(price - discountfinal) + '</b> \
				</h3>'
                );
            }
        });
    }

    function submitForm() {
        var $form = $('.full-screen form'),
            $btn = $form.find('[type="submit"]'),
            $html = $form.find('.ajax-result'),
            data = $form.serialize();

        $btn.addClass('l w').prop('disabled', true);
        $html.html('');

        $.ajax({
            type: "POST",
            url: 'admin/api/addDiscount/',
            data: data,
            dataType: "json",
            success: function (data) {
                if (data == "login")
                    login(submitForm);
                else {
                    $html.html(get_alert(data));
                    setTimeout(function () {
                        location.reload();
                    }, 500);
                }

                $btn.removeClass('l w').prop('disabled', false);
            },
            error: function (a, b, c) {
                $btn.removeClass('l w').prop('disabled', false);
                notify('خطا در اتصال', 2);
            }
        });
    }

    function getDorehs(value, elm) {
        if (value.length < 1)
            return;
        $.ajax({
            type: "POST",
            url: 'admin/api/getDorehs/' + value,
            dataType: "json",
            success: function (data) {
                result = data.result;
                if (result.length) {
                    elm.autocomplete({
                        //appendTo: elm.parent(),
                        source: result,
                        select: function (event, ui) {
                            bookneed = ui.item.idx;
                            bookprice = ui.item.price;
                            bookname = ui.item.title;
                            $('.full-screen .level option:selected').attr('data-price', bookprice);
                            $('.dorehdivid input[name="dorehid"]').val(bookneed);
                            calc_price();
                        }
                    });
                }
            }
        });
    }

    function getDorehClass(value, elm) {
        if (value.length < 1)
            return;
        $.ajax({
            type: "POST",
            url: 'admin/api/getDorehClass/' + value,
            dataType: "json",
            success: function (data) {
                result = data.result;
                if (result.length) {
                    elm.autocomplete({
                        //appendTo: elm.parent(),
                        source: result,
                        select: function (event, ui) {
                            console.error(ui.item);
                            bookneed = ui.item.idx;
                            bookprice = ui.item.price;
                            bookname = ui.item.title;
                            $('.full-screen .level option:selected').attr('data-price', bookprice);
                            $('.dorehclassdivid input[name="dorehclassid"]').val(bookneed);
                            calc_price();
                        }
                    });
                }
            }
        });
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
                        //appendTo: elm.parent(),
                        source: result,
                        select: function (event, ui) {
                            bookneed = ui.item.idx;
                            bookprice = ui.item.price;
                            bookname = ui.item.title;
                            $('.full-screen .level option:selected').attr('data-price', bookprice);
                            $('.bookdivid input[name="bookid"]').val(bookneed);
                            calc_price();
                        }
                    });
                }
            }
        });
    }

    function getMultiBooks(value) {
        if (value.length < 1)
            return;
        $.ajax({
            type: "POST",
            url: 'admin/api/getBooks/' + value,
            dataType: "json",
            success: function (data) {
                result = data.result;
                var $discount = $('.full-screen .discount'),
                    $level = $('.full-screen .level option:selected'),
                    $result = $('.full-screen .calc-result'),
                    discount = parseInt($discount.val()),
                    discountfee = parseInt($('.full-screen form').find('input[name="fee"]').val());
                if (result.length) {
                    $result.html("");
                    $result.append('<table class="table"></table>');
                    $result.find('table').append('<tr><th>#</th><th>کتاب</th><th>قیمت کتب</th><th>قیمت با تخفیف</th></tr>');
                    var multibookid = [];
                    for (var i = 0; i < result.length; i++) {
                        ui = result[i];
                        if ($.inArray(ui.idx, multibookid) == -1)
                            multibookid.push(ui.idx);
                        multibookneed = ui.idx;
                        multibookprice = parseInt(ui.price);
                        multibookname = ui.title;
                        price = parseInt(multibookprice);
                        discountfinal = price * discount / 100;
                        if (discountfee)
                            discountfinal = discountfee;
                        $result.find('table').append(
                            '<tr><td>' + (i + 1) + '</td><td>' + multibookname + '</td><td>' + formatMoney(multibookprice) + '</td><td>' + formatMoney(price - discountfinal) + '</td></tr>');
                    }
                    $('.multibookdivid input[name="multibookid"]').val(multibookid.join(','));
                }
            }
        });
    }

    function getCategoryBooks(value) {
        if (value.length < 1)
            return;
        $.ajax({
            type: "POST",
            url: 'admin/api/getCategoryBooks/' + value,
            dataType: "json",
            success: function (data) {
                result = data.result;
                var $discount = $('.full-screen .discount'),
                    $level = $('.full-screen .level option:selected'),
                    $result = $('.full-screen .calc-result'),
                    discount = parseInt($discount.val()),
                    discountfee = parseInt($('.full-screen form').find('input[name="fee"]').val());
                if (result.length) {
                    $result.html("");
                    $result.append('<table class="table"></table>');
                    $result.find('table').append('<tr><th>#</th><th>کتاب</th><th>قیمت کتب</th><th>قیمت با تخفیف</th></tr>');
                    var categoryid = [];
                    for (var i = 0; i < result.length; i++) {
                        ui = result[i];
                        if ($.inArray(ui.cid, categoryid) == -1)
                            categoryid.push(ui.cid);
                        categoryneed = ui.idx;
                        categoryprice = parseInt(ui.price);
                        categoryname = ui.title;
                        price = parseInt(categoryprice);
                        discountfinal = price * discount / 100;
                        if (discountfee)
                            discountfinal = discountfee;
                        $result.find('table').append(
                            '<tr><td>' + (i + 1) + '</td><td>' + categoryname + '</td><td>' + formatMoney(categoryprice) + '</td><td>' + formatMoney(price - discountfinal) + '</td></tr>');
                    }
                    $('.categorydivid input[name="categoryid"]').val(categoryid.join(','));
                }
            }
        });
    }

</script>
