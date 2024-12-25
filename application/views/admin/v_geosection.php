<?php
defined('BASEPATH') or exit('No direct script access allowed');

$geotypes = $this->db->order_by('title', 'asc')->get('geotype')->result();

?>
<div class="btn btn-success text-center col-sm-12">
    <div class="h2">مناطق جغرافیایی</div>
</div>
<style> .media-ap-data img {
        max-height: 150px !important;
    }
</style>

<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td style="vertical-align:top;width:300px">
            <div class="box" id="stick" style="margin-top:15px;max-width:300px;">
                <div class="box-title">
                    <i class="fa fa-bookmark"></i>
                    افزودن مناطق جغرافیایی
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

                            <div>نوع مناطق</div>
                            <select id="cat-gtid" dir="ltr" name="gtid" class="input full-w medium">
                                <?php foreach($geotypes as $k=>$v){ ?>
                                    <option value="<?php echo $v->id?>"><?php echo $v->title?></option>
                                <?php } ?>
                            </select>

                            <div>مجموعه مادر</div>
                            <select id="cat-parent" name="parent" class="input full-w medium">
                                <option value="0" parent="0" item-id="0" pos="0">- مادر -</option>
                                <?php echo $this->geosection->getGeosectionSelectMenu($owner); ?>
                            </select>
                            <div>انتشار</div>
                            <select id="cat-published" name="published" class="input full-w">
                                <option value="0">خیر</option>
                                <option value="1">بلی</option>
                            </select>

                            <div>برگزیده</div>
                            <select id="cat-special" name="special" class="input full-w">
                                <option value="0">خیر</option>
                                <option value="1">بلی</option>
                            </select>

                            <div>عنوان منطقه جغرافیایی</div>
                            <input id="cat-val" name="name" type="text" class="input full-w" placeholder="عنوان منطقه جغرافیایی"
                                   title="عنوان منطقه جغرافیایی" onFocus="$(this).select()">

                            <div>موقعیت</div>
                            <input id="cat-pos" name="position" type="text" class="input full-w OnlyNum"
                                   placeholder="موقعیت" title="موقعیت">

                            <div>مقدار تخفیف به درصد (%)</div>
                            <input id="cat-des" name="description" class="input medium full-w"
                                   placeholder="مقدار تخفیف به درصد (%)" title="مقدار تخفیف به درصد (%)">

                            <div>توضیحات</div>
                            <input id="cat-pos" name="description" type="text" class="input full-w"
                                   placeholder="توضیحات" title="توضیحات">

                            <input id="cat-id" name="id" type="hidden" value="">
                            <div>

                                <input type="button" id="update-category-btn" class="w-btn" value="به روز رسانی"
                                       onClick="updateGeosection(this)" style="display:none">

                                <input type="button" id="add-category-btn" class="w-btn" value="جدید"
                                       onClick="addGeosection(this)">

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
	<li item-id="[ID]" pos="[POS]" parent="[PARENT]" special="[SPECIALVAL]" published="[PUBLISHEDVAL]" gtid="[GTIDVAL]" class="cat-item">
		<div class="cat-item-div">
			<span class="cat-img">
				<img file="[PIC]" thumb150="[PIC-150]" thumb300="[PIC-300]" src="' . site_url() . '[PIC-150]" width=40 height=40/>
			</span>
			<span class="cat-icon fa fa-[ICON]" ic="[ICON]"></span>
			<span class="cat-published  fa-2x p-2 m-1 float-start border fa fa-[PUBLISHED]" title="انتشار"></span>
			<span class="cat-special  fa-2x p-2 m-1 float-start border fa fa-[SPECIAL]" title="برگزیده"></span>
			<span class="cat-name" title="[DES]">[NAME]</span>
			<span class="cat-des" title="[DES]">[DES]</span>	
			<span class="cat-gtid float-end" title="[GTID]">[<span>[GTID]</span>]</span>
			<span class="cat-options">
				<i class="fa fa-times red cu" onClick="deleteGeosection([ID],this)"></i>
				<i class="fa fa-pencil cu" onClick="edit([ID])"></i>
			</span>
		</div>
		[SUB-MENU]
	</li>
	';

                echo "<div class=category-container>";
                echo $this->geosection->getGeosectionList($owner, 0, FALSE, NULL, FALSE, $sample);
                echo "</div>";

                ?>
            </div>

        </td>
    </tr>
</table>

<div class="clear"></div>

<link type="text/css" rel="stylesheet" href="<?php echo base_url() ?>/js/chosen/chosen.min.css"/>
<script src="<?php echo base_url() ?>/js/chosen/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var $geotypes = {<?php
        $dataout = [];
        foreach ($geotypes as $geotype){
            $dataout[] = "$geotype->id:'$geotype->title'";
        }
        echo implode(",",$dataout);
        ?>};
    $(document).ready(function () {
        $("select").chosen({width: '100%'});
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
            if ($.trim($('#cat-id').val()) == "")
                key = false;
            else if (li.length)
                $(li).add($(li).find('li.cat-item')).each(function (index, el) {
                    if ($(el).attr('item-id') == sid) {
                        key = false;
                        return false;
                    }
                });
            $('#update-category-btn').css('display', !key ? 'none' : 'inline-block');
        });
    });

    function newLi(cat) {

        var id = cat.id,
            pos = cat.position,
            gtid = $geotypes[cat.gtid],
            published = cat.published == "1" ? 'check green' : 'close red',
            special = cat.special == "1" ? 'star gold' : 'star-o',
            name = cat.name,
            par = cat.parent,
            des = cat.description,
            pic = cat.pic,
            pic150 = cat.pic150,
            pic300 = cat.pic300,
            icon = cat.icon;

        var $li = $('<li/>', {'item-id': id, class: cat.class || 'cat-item'}).attr({'parent': par, 'pos': pos,'special':cat.special,'published':cat.published,'gtid':cat.gtid})
            .append(
                $('<div/>', {class: 'cat-item-div'})
                    .append(
                        $('<span/>', {class: 'cat-img'}).append(
                            $('<img/>', {width: 40, height: 40, src: BURL + pic150})
                                .attr({file: cat.pic, thumb150: pic, thumb300: cat.pic300})
                        )
                    ).append(
                    $('<span/>', {class: 'cat-icon fa fa-' + icon}).attr('ic', icon)
                ).append(
                    $('<span/>', {class: 'cat-published  fa-2x p-2 m-1 float-start border fa fa-' + published})
                ).append(
                    $('<span/>', {class: 'cat-special  fa-2x p-2 m-1 float-start border fa fa-' + special})
                ).append(
                    $('<span/>', {class: 'cat-name', 'data-title': des}).html(name).css('color', '#096')
                ).append(
                    $('<span/>', {class: 'cat-des', 'data-title': des}).html(des)
                ).append(
                    $('<span/>', {class: 'cat-gtid float-end'}).html('['+gtid+']')
                ).append(
                    $('<span/>', {class: 'cat-options'})
                        .append(
                            $('<i/>', {class: 'fa fa-times red cu'}).attr('onclick', 'deleteGeosection(' + id + ',this)')
                        ).append(
                        $('<i/>', {class: 'fa fa-pencil cu'}).attr('onclick', 'edit(' + id + ')')
                    )
                )
            );
        return $li;
    }

    function sortGeosection(par) {

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
            pic = BURL + cat.pic150;

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

        sortGeosection(par);
    }

    function addGeosection(btn) {

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
            url: URL + "/geosection/add",
            data: data,
            dataType: "json",
            success: function (data) {
                if (data.done) {
                    var cat = data.data;
                    handleNewCat(cat, 'new');
                    $('#cat-parent option:not(:first)').remove();
                    $('#cat-parent').append(cat.menu).val(cat.parent);
                    $('#reset-btn').trigger("click");
                } else {
                    dialog_box('افزودن مناطق جغرافیایی جدید با مشکل مواجه شد');
                    $(btn).parent().removeClass('l h6 blue');
                }
                $(btn).parent().removeClass('l h6 blue');
            },
            error: function () {
                $(btn).parent().removeClass('l h6 blue');
            }
        });
    }

    function updateGeosection(btn) {

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
            url: URL + "/geosection/update",
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
                    sortGeosection(cat.parent);
                    $('#reset-btn').trigger("click");
                } else {
                    dialog_box('ویرایش مناطق جغرافیایی جدید با مشکل مواجه شد');
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
            published = $(li).attr('special'),
            special = $(li).attr('special'),
            gtid = $(li).attr('gtid'),
            pos = $(li).attr('pos'),
            name = $(li).find('.cat-name:first').text(),
            des = $(li).find('.cat-des:first').text(),
            img = $(li).find('.cat-img:first img').attr('file'),
            img300 = $(li).find('.cat-img:first img').attr('thumb300'),
            icon = $(li).find('.cat-icon:first').attr('ic');

        var IMG = $('<img/>', {src: BURL + img300, class: 'convert-this'}).attr('file', img);

        $('#add-thumb-img').hide();

        $('#cat-id').val(id);

        $('.media-ap-data').html(IMG);

        $('#cat-pos').val(pos);

        $('#cat-gtid').val(gtid);

        $('#cat-published').val(published);

        $('#cat-special').val(special);

        $('#cat-des').val(des);

        $('#cat-val').val(name);

        $('#update-category-btn').fadeIn();

        $('#cat-parent').val(parent);

        $('#cat-icon-view').attr('class', 'fa  fa-2x p-2 m-1 fa-' + icon);

        $('#cat-icon').val(icon);

        $("select").chosen({width: '100%'}).trigger("chosen:updated");

    }

    function deleteGeosection(id, btn) {

        var li = $(btn).closest('li'),
            ul = $(li).find('ul');
        var text = ul.length ? " این مناطق جغرافیایی دارای زیرمجموعه است . <br/>" : " ";
        text += 'مناطق جغرافیایی حذف شود ؟'
        var options = {
            url: 'geosection/delete',
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
        $("select").chosen({width: '100%'}).val('').trigger("chosen:updated");
    }
</script>