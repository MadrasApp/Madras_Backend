<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style> .media-ap-data img {
        max-height: 150px !important;
    } </style>

<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td style="vertical-align:top;width:300px">
            <div class="box" id="stick" style="margin-top:15px;max-width:300px;">
                <div class="box-title">
                    <i class="fa fa-bookmark"></i>
                    افزودن دسته بندی
                </div>
                <div class="box-content" style="padding:0;width:300px">
                    <form id="category-form">
                        <div id="category-add" style="padding:10px;border-top:solid 1px #999">

                            <div class="media-ap" style="text-align:center;">
                                <div class="convert-to-form-el editable-img" form-el-name="pic" form-el-value="file">
                                    <div class="media-ap-data replace" data-thumb="thumb300" style="width:100%;"></div>

                                    <div id="add-thumb-img" class="add-img photo-btn"
                                         onClick="media('img,1',this,function(){ $('#add-thumb-img').hide() })"
                                         style="margin:15px;" title="افزودن تصویر"></div>
                                    <div class="form-ap-data" style="display:none"></div>
                                </div>
                            </div>

                            <div class="icon-append" style="text-align:center">
                                <span class="ic-btn" onClick="selectIcon(this)" title="افزودن آیکن"></span>
                                <i id="cat-icon-view" data-icon-view="" style="color:#666"></i>
                                <input id="cat-icon" type="hidden" name="icon">
                            </div>

                            <select id="cat-parent" name="parent" class="input full-w medium"
                                    style="text-align:center;">
                                <option value="0" parent="0" item-id="0" pos="0">- مادر -</option>
                                <?php echo $this->post->getCategorySelectMenu($type); ?>
                            </select>
                            <div>عنوان دسته</div>
                            <input id="cat-val" name="name" type="text" class="input full-w" placeholder="عنوان دسته"
                                   value="" onFocus="$(this).select()">
                            <div>موقعیت</div>
                            <input id="cat-pos" name="position" type="text" class="input full-w OnlyNum"
                                   placeholder="موقعیت" value="">


                            <?php if ($type == 'book'): ?>
                                <div>مقدار تخفیف به درصد (%)</div>
                                <input id="cat-description" name="description" class="input medium full-w"
                                       placeholder="مقدار تخفیف به درصد (%)">
                                <div>قیمت اشتراک یک ماهه</div>
                                <input id="membership-1-month" name="membership1" class="input medium full-w"
                                       placeholder="قیمت اشتراک یک ماهه">
                                <div>مقدار تخفیف اشتراک یک ماهه</div>
                                <input id="discount-membership-1-month" name="discountmembership1"
                                       class="input medium full-w"
                                       placeholder="مقدار تخفیف اشتراک یک ماهه">
                                <div>قیمت اشتراک سه ماهه</div>
                                <input id="membership-3-month" name="membership3" class="input medium full-w"
                                       placeholder="قیمت اشتراک سه ماهه">
                                <div>مقدار تخفیف اشتراک سه ماهه</div>
                                <input id="discount-membership-3-month" name="discountmembership3"
                                       class="input medium full-w"
                                       placeholder="مقدار تخفیف اشتراک سه ماهه">
                                <div>قیمت اشتراک شش ماهه</div>
                                <input id="membership-6-month" name="membership6" class="input medium full-w"
                                       placeholder="قیمت اشتراک شش ماهه">
                                <div>مقدار تخفیف اشتراک شش ماهه</div>
                                <input id="discount-membership-6-month" name="discountmembership6"
                                       class="input medium full-w"
                                       placeholder="مقدار تخفیف اشتراک شش ماهه">
                                <div>قیمت اشتراک یک ساله</div>
                                <input id="membership-12-month" name="membership12" class="input medium full-w"
                                       placeholder="قیمت اشتراک یک ساله">
                                <div>مقدار تخفیف اشتراک یک ساله</div>
                                <input id="discount-membership-12-month" name="discountmembership12"
                                       class="input medium full-w"
                                       placeholder="مقدار تخفیف اشتراک یک ساله">
                            <?php else : ?>
                                <div>توضیحات</div>
                                <textarea id="cat-description" name="description" class="input medium full-w"
                                          placeholder="توضیحات" style="resize:vertical"
                                          onFocus="$(this).select()"></textarea>
                            <?php endif ?>

                            <input id="cat-type" name="type" type="hidden" value="<?php echo $type ?>">

                            <input id="cat-id" name="id" type="hidden" value="">

                            <div>

                                <input type="button" id="update-category-btn" class="w-btn" value="به روز رسانی"
                                       onClick="updateCategory(this)" style="display:none">

                                <input type="button" id="add-category-btn" class="w-btn" value="جدید"
                                       onClick="addCategory(this)">

                                <input id="reset-btn" type="reset" class="w-btn" value="ریست" onClick="resetForm()"
                                       style="float:left">
                            </div>
                        </div>

                    </form>
                </div>
                <div class="box-footer"></div>
            </div>
        </td>
        <td width="70%" style="vertical-align:top;width:70%">
            <div class="category-table" style="margin-top:15px">
                <?php

                $sample =
                    '
	<li item-id="[ID]" pos="[POS]" parent="[PARENT]" class="cat-item">

		<div class="cat-item-div">
			<span class="cat-img">
				<img file="[PIC]" thumb150="[PIC-150]" thumb300="[PIC-300]" src="' . site_url() . '[PIC-150]" width=40 height=40/>
			</span>
			<span class="cat-icon fa fa-[ICON]" ic="[ICON]"></span>
			<span class="cat-name" title="[DES]">[NAME]</span>	
			<span class="d-none cat-description" title="[DES]">[DES]</span>
			<span class="cat-id">[ID]</span>
			<span class="cat-options">
				<i class="fa fa-times red cu" onClick="deleteCategory([ID],this)"></i>
				<i class="fa fa-pencil cu" onClick="edit([ID])"></i>
			</span>
			<span class="d-none membership1" data-title="[MEMBERSHIP1]">[MEMBERSHIP1]</span>	
			<span class="d-none discountmembership1" data-title="[DISCOUNTMEMBERSHIP1]">[DISCOUNTMEMBERSHIP1]</span>	
			<span class="d-none membership3" data-title="[MEMBERSHIP3]">[MEMBERSHIP3]</span>	
			<span class="d-none discountmembership3" data-title="[DISCOUNTMEMBERSHIP3]">[DISCOUNTMEMBERSHIP3]</span>	
			<span class="d-none membership6" data-title="[MEMBERSHIP6]">[MEMBERSHIP6]</span>	
			<span class="d-none discountmembership6" data-title="[DISCOUNTMEMBERSHIP6]">[DISCOUNTMEMBERSHIP6]</span>	
			<span class="d-none membership12" data-title="[MEMBERSHIP12]">[MEMBERSHIP12]</span>	
			<span class="d-none discountmembership12" data-title="[DISCOUNTMEMBERSHIP12]">[DISCOUNTMEMBERSHIP12]</span>
		</div>
		[SUB-MENU]
	</li>
	';

                echo "<div class=category-container>";
                echo $this->post->getCateoryList($type, 0, FALSE, NULL, FALSE, $sample);
                echo "</div>";

                ?>
            </div>

        </td>
    </tr>
</table>

<div class="clear"></div>

<script type="text/javascript">
    function CheckStatusCat(section) {
        var val = parseInt($('#cat-parent').val());
        if (val) {
            $('#membership-1-month,#membership-3-month,#membership-6-month,#membership-12-month,#discount-membership-1-month,#discount-membership-3-month,#discount-membership-6-month,#discount-membership-12-month').val(0).attr('disabled', true)
        } else {
            $('#membership-1-month,#membership-3-month,#membership-6-month,#membership-12-month,#discount-membership-1-month,#discount-membership-3-month,#discount-membership-6-month,#discount-membership-12-month').attr('disabled', false)
        }
    }

    $(document).ready(function (e) {
        $('#reset-btn').click(function () {
            setTimeout(function () {
                CheckStatusCat(3);
            }, 200);
        });
        $("#category-form").on('input change', '#cat-parent', function () {
            CheckStatusCat(1);
        });
        <?php if($Cat): ?>

        $("#cat-parent").val(<?php echo $Cat->parent ?>);

        <?php endif; ?>

        $(document).on("submit", "#category-form", function (event) {
            event.prevenDefault;
            return false;
        });

        $(document).on("click", ".cat-item-div", function (e) {
            var ul = $(this).next();
            if (ul.length && !$(e.target).hasClass('fa')) {
                $(ul).slideToggle();
                $(this).parent().toggleClass('closed');
            }
        });

        $("#stick").stick_in_parent();

        $(document).on("change", "#cat-parent", function () {
            var id = $('#cat-id').val(),
                li = $('li.cat-item[item-id="' + id + '"]'),
                sid = this.value,
                key = true;
            if ($.trim($('#cat-id').val()) == "") {
                key = false;
            }else if (li.length) {
                $(li).add($(li).find('li.cat-item')).each(function (index, el) {
                    if ($(el).attr('item-id') == sid) {
                        key = false;
                        return false;
                    }
                });
            }
            $('#update-category-btn').css('display', !key ? 'none' : 'inline-block');
        });
    });

    function newLi(cat) {
        var id = cat.id,
            pos = cat.position,
            name = cat.name,
            par = cat.parent,
            membership1 = cat.membership1,
            discountmembership1 = cat.discountmembership1,
            membership3 = cat.membership3,
            discountmembership3 = cat.discountmembership3,
            membership6 = cat.membership6,
            discountmembership6 = cat.discountmembership6,
            membership12 = cat.membership12,
            discountmembership12 = cat.discountmembership12,
            des = cat.description,
            pic = cat.pic,
            pic150 = cat.pic150,
            pic300 = cat.pic300,
            type = cat.type,
            icon = cat.icon;

        var $li = $('<li/>', {'item-id': id, class: cat.class || 'cat-item'}).attr({'parent': par, 'pos': pos})
            .append(
                $('<div/>', {class: 'cat-item-div'})
                    .append(
                        $('<span/>', {class: 'cat-img'}).append(
                            $('<img/>', {width: 40, height: 40, src: BURL + pic150})
                                .attr({file: cat.pic, thumb150: pic, thumb300: cat.pic300})
                        )
                    )
                    .append(
                        $('<span/>', {class: 'cat-icon fa fa-' + icon}).attr('ic', icon)
                    ).append(
                    $('<span/>', {class: 'cat-name', 'data-title': des}).html(name).css('color', '#096')
                ).append(
                    $('<span/>', {class: 'd-none membership1', 'data-title': membership1}).html(membership1)
                ).append(
                    $('<span/>', {
                        class: 'd-none discountmembership1',
                        'data-title': discountmembership1
                    }).html(discountmembership1)
                ).append(
                    $('<span/>', {class: 'd-none membership3', 'data-title': membership3}).html(membership3)
                ).append(
                    $('<span/>', {
                        class: 'd-none discountmembership3',
                        'data-title': discountmembership3
                    }).html(discountmembership3)
                ).append(
                    $('<span/>', {class: 'd-none membership6', 'data-title': membership6}).html(membership6)
                ).append(
                    $('<span/>', {
                        class: 'd-none discountmembership6',
                        'data-title': discountmembership6
                    }).html(discountmembership6)
                ).append(
                    $('<span/>', {class: 'd-none membership12', 'data-title': membership12}).html(membership12)
                ).append(
                    $('<span/>', {
                        class: 'd-none discountmembership12',
                        'data-title': discountmembership12
                    }).html(discountmembership12)
                ).append(
                    $('<span/>', {class: 'd-none cat-description', 'data-title': des}).html(des)
                ).append(
                    $('<span/>', {class: 'cat-options'})
                        .append(
                            $('<i/>', {class: 'fa fa-times red cu'}).attr('onclick', 'deleteCategory(' + id + ',this)')
                        ).append(
                        $('<i/>', {class: 'fa fa-pencil cu'}).attr('onclick', 'edit(' + id + ')')
                    )
                )
            );
        return $li;
    }

    function sortCategory(par) {

        var list = $('.cat-item[parent="' + par + '"]');

        if (!list.length) return;

        list = $(list).sort(function (a, b) {
            var Aid = $(a).attr('item-id') * 1, Bid = $(b).attr('item-id') * 1,
                Apos = $(a).attr('pos') * 1, Bpos = $(b).attr('pos') * 1;
            return Apos - Bpos || Aid - Bid;
        });


        if (par == 0)
            $('.category-container > ul').html(list);
        else
            $('.cat-item[item-id="' + par + '"] > ul').html(list);

    }

    function handleNewCat(cat, op, UL) {
        var id = cat.id,
            name = cat.name,
            par = cat.parent,
            des = cat.description,
            pic = BURL + cat.pic150,
            type = cat.type;

        var $li = newLi(cat);

        if (UL) $li.append(UL);

        var $ul = $('<ul/>').append($li),
            ul = $('.category-container > ul');

        if (!ul.length) {
            $('.category-container').append($('<ul/>'));
            ul = $('.category-container > ul');
        }

        if (par == 0) {
            $(ul).append($li);
        } else {
            var $parent = $('.category-container li[item-id="' + par + '"]');
            var subul = $parent.find('ul:first');
            if (subul.length)
                $(subul).append($li);
            else
                $parent.append($ul);
        }

        sortCategory(par);
    }

    function addCategory(btn) {
        var name = $.trim($('#cat-val').val());

        if (name == "") {
            $("#cat-val").focus();
            return;
        }

        $(btn).parent().addClass('l h6 blue');

        convertToFormEl();
        var data = $('#category-form').serialize();

        $.ajax({
            type: "POST",
            url: URL + "/category/add",
            data: data,
            dataType: "json",
            success: function (data) {
                if (data.done) {
                    var cat = data.data;
                    handleNewCat(cat, 'new');
                    $('#cat-parent option:not(:first)').remove();
                    $('#cat-parent').append(cat.menu).val(cat.parent);
                } else {
                    dialog_box('افزودن دسته بندی جدید با مشکل مواجه شد');
                    $(btn).parent().removeClass('l h6 blue');
                }
                $(btn).parent().removeClass('l h6 blue');
            },
            error: function () {
                $(btn).parent().removeClass('l h6 blue');
            }
        });
    }

    function updateCategory(btn) {
        var name = $.trim($('#cat-val').val());
        if (name == "") {
            $("#cat-val").focus();
            return;
        }
        $(btn).parent().addClass('l h6 blue');
        convertToFormEl();
        var data = $('#category-form').serialize();

        $.ajax({
            type: "POST",
            url: URL + "/category/update",
            data: data,
            dataType: "json",
            success: function (data) {
                if (data.done) {
                    var cat = data.data,
                        li = $('.category-container li[item-id="' + (cat.id) + '"]'),
                        par = $(li).attr('parent'),
                        UL = $(li).find('ul:first');
                    UL = UL.length ? $(UL).clone(true) : '';
                    cat.class = $(li).attr('class');

                    if (par != cat.parent) {
                        $(li).remove();
                        handleNewCat(cat, 'update', UL);
                    } else {
                        var $li = newLi(cat);
                        $(li).replaceWith($li.append(UL));
                    }
                    $('#cat-parent option:not(:first)').remove();
                    $('#cat-parent').append(cat.menu).val(cat.parent);
                    sortCategory(cat.parent);
                } else {
                    dialog_box('ویرایش دسته بندی جدید با مشکل مواجه شد');
                    $(btn).parent().removeClass('l h6 blue');
                }
                $(btn).parent().removeClass('l h6 blue');
            },
            error: function () {
                $(btn).parent().removeClass('l h6 blue');
            }
        });
    }

    function edit(id) {
        var li = $('li.cat-item[item-id="' + id + '"]');

        if (!li.length) return;

        var parent = $(li).attr('parent'),
            pos = $(li).attr('pos'),
            name = $(li).find('.cat-name:first').text(),
            des = $(li).find('.cat-description:first').text(),
            img = $(li).find('.cat-img:first img').attr('file'),
            img300 = $(li).find('.cat-img:first img').attr('thumb300'),
            icon = $(li).find('.cat-icon:first').attr('ic');
        var membership1 = $(li).find('.membership1').data("title"),
            discountmembership1 = $(li).find('.discountmembership1').data("title"),
            membership3 = $(li).find('.membership3').data("title"),
            discountmembership3 = $(li).find('.discountmembership3').data("title"),
            membership6 = $(li).find('.membership6').data("title"),
            discountmembership6 = $(li).find('.discountmembership6').data("title"),
            membership12 = $(li).find('.membership12').data("title"),
            discountmembership12 = $(li).find('.discountmembership12').data("title")
        ;

        var IMG = $('<img/>', {src: BURL + img300, class: 'convert-this'}).attr('file', img);

        $('#add-thumb-img').hide();

        $('#cat-id').val(id);

        $('.media-ap-data').html(IMG);

        $('#cat-pos').val(pos);

        $('#cat-description').val(des);

        $('#cat-val').val(name);

        $('#update-category-btn').fadeIn();

        $('#cat-parent').val(parent);

        $('#cat-icon-view').attr('class', 'fa fa-3x fa-' + icon);

        $('#cat-icon').val(icon);
        $('#membership-1-month').val(membership1);
        $('#discount-membership-1-month').val(discountmembership1);
        $('#membership-3-month').val(membership3);
        $('#discount-membership-3-month').val(discountmembership3);
        $('#membership-6-month').val(membership6);
        $('#discount-membership-6-month').val(discountmembership6);
        $('#membership-12-month').val(membership12);
        $('#discount-membership-12-month').val(discountmembership12);
        CheckStatusCat(2);
    }

    function deleteCategory(id, btn) {
        var li = $(btn).closest('li'),
            ul = $(li).find('ul');
        var text = ul.length ? " این دسته بندی دارای زیرمجموعه است . <br/>" : " ";
        text += 'دسته بندی حذف شود ؟'
        var options = {
            url: 'category/delete',
            data: {id: id},
            success: function (data) {
                $('#cat-parent option[value="' + id + '"]').remove();
                $(li).find('li').each(function (index, el) {
                    $('#cat-parent option[value="' + $(el).attr('item-id') + '"]').remove();
                });
                $(li).fadeOut(500, function () {
                    $(this).remove()
                });

                if ($('#cat-id').val() == id)
                    $('#reset-btn').trigger("click");
            },
            loader: $(btn).closest(ul.length ? '.cat-item-div' : 'li'),
            Dhtml: text,
            Did: 'delete-category-d',
        };
        Confirm(options);
    }

    function resetForm() {
        $('.media-ap-data img').remove();
        $('#add-thumb-img').show();
        $('#update-category-btn').hide();
        $('#cat-id').val('');
        $('#cat-icon').val('');
        $('#cat-icon-view').attr('class', '');
    }
</script>
<style>
    input[disabled="disabled"] {
        background-color: #a1a1a1;
    }
    .cat-item-div {
        min-width: 90% !important;
    }
    .cat-name {
        width: calc( 100% - 100px ) !important;
    }
    .cat-id {
        border: 1px solid #d0c9c9;
        width: 30px;
        float: left;
        text-align: center;
        margin-left: 20px;
        height: 36px;
        padding-top: 9px;
        margin-top: 2px;
    }
</style>