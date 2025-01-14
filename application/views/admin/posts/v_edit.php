<?php

defined('BASEPATH') or exit('No direct script access allowed');
$this->load->helper('form');
$thumbs = $this->post->postThumb($post_id, "all");
$icons = $this->post->postThumb($post_id, "all","icon");
global $POST_TYPES;

$postType = @$POST_TYPES[$type];
?>
<style>
    .box:not(:first-child) {
        margin: 30px 0;
    }
</style>

<div id="result"></div>

<form id="postForm" class="editpost">

    <div style="float:right;width:75%;padding-left:10px">

        <input type="hidden" name="id" value="<?php echo @$post['id'] ?>">
        <input type="hidden" name="data[type]" value="<?php echo $type ?>">

        <?php if (in_array('title', $form)) : ?>
            <input type="text" class="input xlarg" id="post-title" style="width:100%" placeholder="عنوان"
                   name="data[title]">
        <?php endif; ?>

        <p></p>

        <?php if (in_array('media', $form)) : ?>
            <input type="button" class="w-btn" value="افزودن رسانه" onClick="media('editor')">
        <?php endif; ?>

        <?php if (in_array('editor', $form)) : ?>

            <textarea id="content" name="data[content]" style="display:none"><?php echo @$post['content'] ?></textarea>
            <div id="textEditor"></div>
            <script src="<?php echo base_url() . "js/ckeditor/ckeditor.js" ?>"></script>
            <script>
                var EDITOR, html = $('#content').val(),
                    config = {
                        contentsCss: [CKEDITOR.basePath + 'contents.css', '<?php echo base_url() ?>style/_master/font.css']
                    };
                $(function () {
                    EDITOR = CKEDITOR.appendTo('textEditor', config, html);
                });
            </script>

        <?php endif; ?>

        <div class="box">
            <div class="box-title"><i class="fa fa-ellipsis-h"></i> اولویت</div>
            <div class="box-content" style="padding:0">
                <select name="data[special]" id="data_special" class="input small">
                    <option value="0" <?php echo @$post['special'] == 0 ? 'selected' : ''; ?>>عادی</option>
                    <option value="1" <?php echo @$post['special'] == 1 ? 'selected' : ''; ?>>پیشنهادی</option>
                    <option value="2" <?php echo @$post['special'] == 2 ? 'selected' : ''; ?>>ویژه</option>
                    <option value="3" <?php echo @$post['special'] == 3 ? 'selected' : ''; ?>>خاص</option>
                </select>
            </div>
            <div class="box-footer"></div>
        </div>
        <?php if (in_array('excerpt', $form)) : ?>
            <div class="box">
                <div class="box-title"><i class="fa fa-ellipsis-h"></i> چکیده و خلاصه</div>
                <div class="box-content" style="padding:0">
                    <textarea class="input" name="data[excerpt]" style="width:100%;margin:0;resize:vertical;" rows="5"
                              placeholder="چکیده"></textarea>
                </div>
                <div class="box-footer"></div>
            </div>
        <?php endif; ?>

        <?php if ($type == 'book') : ?>
            <div class="box">
                <div class="box-title"><i class="fa fa-download"></i> نمونه سوالات</div>
                <div class="box-content">
                    <div class="dl-box-book">

                        <?php if (isset($meta['dl_book']) && trim($meta['dl_book']) != "") : ?>

                            <?php if ($this->tools->isJson($meta['dl_book'])) : ?>

                                <?php $dl = $this->tools->jsonDecode($meta['dl_book']); ?>

                                <?php foreach ($dl as $dl_key => $dl_value) : ?>

                                    <div class="dl-row">
                                        <input readonly name="meta[dlbook][file][]" type="text"
                                               class="input small dl-file"
                                               value="<?php echo $dl_value['file'] ?>">
                                        <input name="meta[dlbook][name][]" type="text" class="input small dl-name"
                                               value="<?php echo $dl_value['name'] ?>">
                                        <i class="fa fa-times fa-lg red" onClick="$(this).parent().remove()"></i>
                                    </div>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        <?php endif; ?>
                    </div>
                    <div class="plus" onClick="media({selected:'files'},this,adDl2)"></div>
                </div>
                <div class="box-footer"></div>
            </div>
        <?php endif; ?>

        <?php if (in_array('dl_box', $form)) : ?>

            <div class="box">
                <div class="box-title"><i class="fa fa-download"></i> فایل های پیوست شده</div>
                <div class="box-content">
                    <div class="dl-box">

                        <?php if (isset($meta['dl_box']) && trim($meta['dl_box']) != "") : ?>

                            <?php if ($this->tools->isJson($meta['dl_box'])) : ?>

                                <?php $dl = $this->tools->jsonDecode($meta['dl_box']); ?>

                                <?php foreach ($dl as $dl_key => $dl_value) : ?>

                                    <div class="dl-row">
                                        <input readonly name="meta[dl][file][]" type="text" class="input small dl-file"
                                               value="<?php echo $dl_value['file'] ?>">
                                        <input name="meta[dl][name][]" type="text" class="input small dl-name"
                                               value="<?php echo $dl_value['name'] ?>">
                                        <i class="fa fa-times fa-lg red" onClick="$(this).parent().remove()"></i>
                                    </div>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        <?php endif; ?>
                    </div>
                    <div class="plus" onClick="media({selected:'files'},this,adDl)"></div>
                </div>
                <div class="box-footer"></div>
            </div>
        <?php endif; ?>

        <?php if (in_array('product_settings', $form)) : ?>

            <div class="box">
                <div class="box-title"><i class="fa fa-cog"></i> اطلاعات محصول</div>
                <div class="box-content">

                    <table cellpadding="" width="100%">
                        <tr>
                            <td><span> نوع محصول </span></td>
                            <td>
                                <select name="meta[product][type]" class="input small"
                                        onChange="handleProductType(this)" style="width:200px">
                                    <option value="file"> فایل قابل دانلود</option>
                                    <option value="object"> کالا</option>
                                </select>

                                &nbsp;

                                <label> <input type="checkbox" value="1" name="meta[product][spacial_offer]">
                                    پیشنهاد ویژه
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td><span>قیمت محصول </span></td>
                            <td>
                                <input name="meta[product][price]" type="text" class="input medium" placeholder="قیمت"
                                       style="width:120px">
                                <input name="meta[product][off]" type="text" class="input medium"
                                       placeholder="قیمت با تخفیف" style="width:120px">
                                <input name="meta[product][pre]" type="text" class="input medium"
                                       placeholder="پیش پرداخت" style="width:120px">
                            </td>
                        </tr>
                        <tr id="product-details" style="display:none">
                            <td><span> مشخصات محصول </span></td>
                            <td>
                                <input name="meta[product][size]" type="text" class="input medium" placeholder="ابعاد"
                                       style="width:120px">
                                <input name="meta[product][volume]" type="text" class="input medium" placeholder="حجم"
                                       style="width:120px">
                                <input name="meta[product][mass]" type="text" class="input medium" placeholder="وزن"
                                       style="width:120px">
                            </td>
                        </tr>
                        <tr>
                            <td><span> توضیحات </span></td>
                            <td>
                                <textarea class="input" name="meta[product][description]" style="width:365px"
                                          rows="3"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td><span> تصاویر </span></td>
                            <td class="media-ap convert-to-form-el product-thumbs" form-el-name="meta[thumb][]"
                                form-el-value="file">
                                <span class="media-ap-data edit-post-thumb-55 editable-img" data-thumb="thumb150">
                                    <?php
                                    if ($thumbs && isset($thumbs['other']))
                                        foreach ($thumbs['other'] as $key => $value) {
                                            echo '<img src="' . base_url() . $value[150] . '" width=45 height=45 file="' . $value['b'] . '" class="convert-this">';
                                        }
                                    ?>
                                </span>
                                <div class="plus larg" onClick="media('img',this)"></div>
                                <div class="form-ap-data" style="display:none"></div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="box-footer"></div>
            </div>
        <?php endif; ?>

        <?php if (in_array('gallery', $form)) : ?>

            <div class="box">
                <div class="box-title"><i class="fa fa-object-ungroup"></i> گالری تصاویر</div>
                <div class="box-content post-gallery">
                    <div class="media-ap convert-to-form-el product-thumbs" form-el-name="meta[thumb][]"
                         form-el-value="file">
                        <span class="media-ap-data edit-post-thumb-55 editable-img" data-thumb="thumb150">
                            <?php
                            if ($thumbs && isset($thumbs['other']))
                                foreach ($thumbs['other'] as $key => $value) {
                                    echo '<img src="' . base_url() . $value[150] . '" width=45 height=45 file="' . $value['b'] . '" class="convert-this"/>';
                                }
                            ?>
                        </span>
                        <div class="plus larg" onClick="media('img',this)"></div>
                        <div class="form-ap-data" style="display:none"></div>
                    </div>
                </div>
                <div class="box-footer"></div>
            </div>
        <?php endif; ?>

        <?php if (in_array('seo', $form)) : ?>
            <div class="box">
                <div class="box-title"><i class="fa fa-star"></i> سئو</div>
                <div class="box-content">
                    <input type="text" class="input" name="data[meta_keywords]"
                           value="<?php echo $post['meta_keywords'] ?>" placeholder="Meta Keywords">
                    <textarea class="input" name="data[meta_description]" placeholder="Meta description"
                              rows="6"><?php echo $post['meta_description'] ?></textarea>
                </div>
                <div class="box-footer"></div>
            </div>
        <?php endif; ?>

        <?php if (isset($postType['meta']) && is_array($postType['meta'])) : ?>

            <div class="box">
                <div class="box-title"><i class="fa fa-list"></i> اطلاعات تکمیلی</div>
                <div class="box-content">
                    <table class="table">
                        <?php foreach ($postType['meta'] as $mik => $mi): $Type = $mi['type']; ?>
                            <tr>
                                <td style="vertical-align:middle;"><?php echo @$mi['name']; ?></td>
                                <td style="vertical-align:middle;">
                                    <?php switch ($Type) :
                                        case 'select'   :
                                            break; ?>

                                        <?php case 'textarea' :
                                            break; ?>
                                        <?php case 'radio' :
                                            $meta[$mik] = isset($meta[$mik]) ? intval($meta[$mik]) : intval(@$mi['default']);
                                            ?>
                                            <label> خیر : <input type="<?php echo $Type ?>"
                                                                 class="input <?php echo @$mi['class'] ?>"
                                                                 name="meta[<?php echo $mik ?>]"
                                                                 value="0" <?php echo intval(@$meta[$mik]) ? '' : 'checked' ?> /></label>
                                            <label> بلی : <input type="<?php echo $Type ?>"
                                                                 class="input <?php echo @$mi['class'] ?>"
                                                                 name="meta[<?php echo $mik ?>]"
                                                                 value="1" <?php echo intval(@$meta[$mik]) ? 'checked' : '' ?> /></label>
                                            <?php break; ?>

                                        <?php case 'checkbox' : ?>
                                            <input type="<?php echo $Type ?>" class="input <?php echo @$mi['class'] ?>"
                                                   name="meta[<?php echo $mik ?>]"
                                                   value="<?php echo @$mi['value'] ?>" <?php echo @$meta[$mik] ? 'checked' : '' ?> />
                                            <?php break; ?>

                                        <?php default : ?>

                                            <input type="<?php echo $Type ?>" class="input <?php echo @$mi['class'] ?>"
                                                   name="meta[<?php echo $mik ?>]" value="<?php echo @$meta[$mik] ?>"
                                                   placeholder="<?php echo @$mi['placeholder'] ?>">

                                            <?php break; ?>
                                        <?php endswitch; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <div class="box-footer"></div>
            </div>
        <?php endif; ?>
        <?php if (isset($postType['nashr']) && is_array($postType['nashr'])) : ?>

            <div class="box">
                <div class="box-title"><i class="fa fa-list"></i> اطلاعات نشر</div>
                <div class="box-content">
                    <table class="table" dir="rtl">
                        <?php foreach ($postType['nashr'] as $mik => $mi): $Type = $mi['type']; ?>
                            <tr>
                                <td style="vertical-align:middle;" width="20%"><?php echo @$mi['name']; ?></td>
                                <td style="vertical-align:middle;">
                                    <?php switch ($Type) :
                                        case 'select'   :
                                            break;
                                        case 'multiselect' :
                                            $options = $this->post->LoadDataTableSelect($mi['table']);
                                            $val = explode(",", @$nashr[$mik]);
                                            echo form_multiselect("nashr[$mik][]", $options, $val);
                                            break;
                                        case 'dropdown' :
                                            $options = $this->post->LoadDataTableSelect($mi['table']);
                                            $val = @$nashr[$mik];
                                            echo form_dropdown("nashr[$mik]", $options, $val);
                                            break;
                                    case 'calendar' :
                                        ?>
                                    <input type="<?php echo $Type ?>" class="input <?php echo @$mi['class'] ?>"
                                           name="nashr[<?php echo $mik ?>]" id="nashr_<?php echo $mik ?>"
                                           value="<?php echo @$nashr[$mik] ?>"
                                           placeholder="<?php echo @$mi['placeholder'] ?>">
                                        <script type="text/javascript">
                                            $(document).ready(function () {
                                                $("#nashr_<?php echo $mik ?>").datepicker({
                                                    dateFormat: "yy-mm-dd",
                                                    defaultDate: '<?php @$nashr[$mik]?>'
                                                });
                                            });
                                        </script>

                                    <?php
                                    break;
                                    case 'textarea' :
                                        break;
                                    case 'radio' :
                                    $meta[$mik] = isset($nashr[$mik]) ? intval($nashr[$mik]) : intval(@$mi['default']);
                                    ?>
                                        <label> خیر : <input type="<?php echo $Type ?>"
                                                             class="input <?php echo @$mi['class'] ?>"
                                                             name="nashr[<?php echo $mik ?>]"
                                                             value="0" <?php echo intval(@$nashr[$mik]) ? '' : 'checked' ?> /></label>
                                        <label> بلی : <input type="<?php echo $Type ?>"
                                                             class="input <?php echo @$mi['class'] ?>"
                                                             name="nashr[<?php echo $mik ?>]"
                                                             value="1" <?php echo intval(@$nashr[$mik]) ? 'checked' : '' ?> /></label>
                                    <?php
                                    break;
                                    case 'checkbox' : ?>
                                    <input type="<?php echo $Type ?>" class="input <?php echo @$mi['class'] ?>"
                                           name="nashr[<?php echo $mik ?>]"
                                           value="<?php echo @$mi['value'] ?>" <?php echo @$nashr[$mik] ? 'checked' : '' ?> />
                                    <?php
                                    break;
                                    default : ?>

                                    <input type="<?php echo $Type ?>" class="input <?php echo @$mi['class'] ?>"
                                           name="nashr[<?php echo $mik ?>]" value="<?php echo @$nashr[$mik] ?>"
                                           placeholder="<?php echo @$mi['placeholder'] ?>">

                                        <?php
                                        break;
                                    endswitch;
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <div class="box-footer"></div>
            </div>
        <?php endif; ?>
    </div>

    <div style="float:right;width:25%;padding-top:10px;">

        <div class="box">
            <div class="box-title"><i class="fa fa-check" style="margin-top:0"></i> ذخیره</div>
            <div class="box-content">

                <div class="publish-post">

                    <p><i class="fa fa-user" title="زمان ایجاد"></i> نویسنده </p>
                    <div>

                        <select name="data[author]" class="input small" style="width:100%;text-align:center">
                            <?php if ($users && is_array($users)) : ?>
                                <?php foreach ($users as $ukey => $user) : ?>
                                    <option value="<?php echo $user['id'] ?>"><?php echo $user['displayname'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>

                        <div class="clear" style="height:15px"></div>
                    </div>

                    <?php $date = strtotime($post['date']);
                    $jdate = explode('/', jdate("Y/m/d/H/i", $date, '', '', 'en')); ?>

                    <p><i class="fa fa-clock-o" title="زمان ایجاد"></i> تاریخ ایجاد </p>
                    <div>

                        <input type="text" maxlength="2" max="60" autocomplete="off" class="input OnlyNum"
                               name="date[i]" value="<?php echo $jdate[4] ?>" title="دقیقه">
                        <span>:</span>
                        <input type="text" maxlength="2" max="23" autocomplete="off" class="input OnlyNum"
                               name="date[H]" value="<?php echo $jdate[3] ?>" title="ساعت">
                        <span>-</span>
                        <input type="text" maxlength="2" max="31" autocomplete="off" class="input OnlyNum"
                               name="date[d]" value="<?php echo $jdate[2] ?>" title="روز" min="01">
                        <input type="text" maxlength="2" max="12" autocomplete="off" class="input OnlyNum"
                               name="date[m]" value="<?php echo $jdate[1] ?>" title="ماه" min="01">
                        <input type="text" maxlength="4" max="1450" autocomplete="off" class="input OnlyNum"
                               name="date[y]" value="<?php echo $jdate[0] == -1 ? '1395' : $jdate[0] ?>" title="سال"
                               style="width:40px" min="1350">
                        <div class="clear" style="height:15px"></div>
                    </div>

                    <p><i class="fa fa-clock-o" title="زمان بروز رسانی"></i> تاریخ به روزرسانی </p>
                    <?php $date = strtotime('now');
                    $jdate = explode('/', jdate("Y/m/d/H/i", $date, '', '', 'en')); ?>

                    <div>
                        <input type="text" maxlength="2" max="60" autocomplete="off" class="input OnlyNum"
                               name="mdate[i]" value="<?php echo $jdate[4] ?>" title="دقیقه">
                        <span>:</span>
                        <input type="text" maxlength="2" max="23" autocomplete="off" class="input OnlyNum"
                               name="mdate[H]" value="<?php echo $jdate[3] ?>" title="ساعت">
                        <span>-</span>
                        <input type="text" maxlength="2" max="31" autocomplete="off" class="input OnlyNum"
                               name="mdate[d]" value="<?php echo $jdate[2] ?>" title="روز" min="01">
                        <input type="text" maxlength="2" max="12" autocomplete="off" class="input OnlyNum"
                               name="mdate[m]" value="<?php echo $jdate[1] ?>" title="ماه" min="01">
                        <input type="text" maxlength="4" max="1450" autocomplete="off" class="input OnlyNum"
                               name="mdate[y]" value="<?php echo $jdate[0] ?>" title="سال" style="width:40px"
                               min="1350">
                        <div class="clear" style="height:15px"></div>
                    </div>

                </div>
                <input type="button" class="btn btn-primary" value="انتشار" onClick="updatePost(this,'publish')">
                <input type="button" class="btn btn-warning" value="پیش نویس" onClick="updatePost(this,'draft')">
                <input type="button" class="btn btn-danger" value="آماده انتشار" onClick="updatePost(this,'test')">
            </div>
            <div class="box-footer" style="min-height:32px"></div>
        </div>

        <?php if (in_array('thumb', $form)) : ?>
            <div class="box media-ap edit-post-thumb">
                <div class="box-title"><i class="fa fa-photo"></i> تصویر عمودی </div>

                <div class="box-content" style="padding:2px;text-align:center">
                    <div class="convert-to-form-el editable-img" form-el-name="data[thumb]" form-el-value="file">
                        <span class="media-ap-data replace" data-thumb="thumb300">

                        <?php $thumbKey = (isset($post['thumb']) && trim($post['thumb']) != "") ?>

                            <?php if ($thumbKey) : ?>
                                <img src="<?php echo base_url() . $thumbs['base'][300] ?>"
                                     file="<?php echo $thumbs['base']['b'] ?>" class="convert-this">
                            <?php endif; ?>

                        </span>
                        <div id="add-thumb-img" class="plus add-img"
                             onClick="media('img,1',this,function(){ $('#add-thumb-img').hide() })"
                             style="display:<?php echo $thumbKey ? 'none' : 'inline-block' ?>;margin:15px"></div>
                        <div class="form-ap-data" style="display:none"></div>
                    </div>
                </div>

                <div class="box-footer"><i class="fa fa-pencil cu" onClick="media('img,1',this)"></i></div>
            </div>
        <?php endif; ?>

        <?php if (in_array('icon', $form)) : ?>
            <div class="box media-ap edit-post-thumb">
                <div class="box-title"><i class="fa fa-photo"></i> تصویر افقی </div>

                <div class="box-content" style="padding:2px;text-align:center">
                    <div class="convert-to-form-el editable-img" form-el-name="data[icon]" form-el-value="file">
                        <span class="media-ap-data replace" data-thumb="thumb300">

                        <?php $iconKey = (isset($post['icon']) && trim($post['icon']) != "") ?>

                            <?php if ($iconKey) : ?>
                                <img src="<?php echo base_url() . $icons['base'][300] ?>"
                                     file="<?php echo $icons['base']['b'] ?>" class="convert-this">
                            <?php endif; ?>

                        </span>
                        <div id="add-icon-img" class="plus add-img"
                             onClick="media('img,1',this,function(){ $('#add-icon-img').hide() })"
                             style="display:<?php echo $iconKey ? 'none' : 'inline-block' ?>;margin:15px"></div>
                        <div class="form-ap-data" style="display:none"></div>
                    </div>
                </div>

                <div class="box-footer"><i class="fa fa-pencil cu" onClick="media('img,1',this)"></i></div>
            </div>
        <?php endif; ?>


        <?php if (in_array('category', $form)) : ?>
            <div class="box">
                <div class="box-title"><i class="fa fa-bookmark"></i> دسته بندی</div>
                <div class="box-content" style="padding:0">

                    <div class="category-select">
                        <?php echo $this->post->getCateoryList($type, 0, 1, $post['category']); ?>
                    </div>

                    <?php if ($this->user->can('category_' . $type)) : ?>
                        <div id="category-add" style="padding:10px;border-top:solid 1px #999">
                            <input id="cat-val" type="text" class="input medium" style="margin:0 0 5px 0"
                                   placeholder="دسته جدید">
                            <select id="cat-parent" class="input small" style="margin:0 0 5px 0;text-align:center;">
                                <option value="0">- مادر -</option>
                                <?php echo $this->post->getCategorySelectMenu($type); ?>
                            </select>
                            <input id="cat-type" type="hidden" value="<?php echo $type ?>">
                            <input type="button" class="w-btn" value="افزودن" onClick="addCategory(this)">
                        </div>
                    <?php endif; ?>

                </div>
                <div class="box-footer"></div>
            </div>
        <?php endif; ?>




        <?php if (isset($postType['single']) && $postType['single'] !== FALSE): ?>
            <div class="box">
                <div class="box-title"><i class="fa fa-wechat"></i> نظرات</div>
                <div class="box-content text-center">
                    <h4>فعال بودن نظرات</h4>
                    <p style="margin-top: 30px">
                        <label><input type="radio" name="data[accept_cm]" value="1">بله</label>
                        &nbsp; &nbsp;
                        <label><input type="radio" name="data[accept_cm]" value="0">خیر</label>
                    </p>
                </div>
                <div class="box-footer"></div>
            </div>
        <?php endif ?>

        <?php if (in_array('tag', $form)) : ?>
            <div class="box">
                <div class="box-title"><i class="fa fa-tags"></i> برچسب ها</div>
                <div class="box-content">
                    <input type="text" class="input medium" id="tag-input" placeholder="برچسب جدید">
                    <div class="post-tags convert-to-form-el" form-el-name="tags[]" form-el-value="tag">
                        <?php $tags = explode('+', $post['tags']); ?>
                        <?php if ($post['tags'] && count($tags) > 0) : ?>
                            <?php foreach ($tags as $tag) : ?>
                                <span class="tag">
                                    <i class="fa fa-times red"></i>
                                    <a class="a convert-this" tag="<?php echo $tag ?>"><?php echo $tag ?></a>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="form-ap-data" style="display:none"></div>
                    </div>
                </div>
                <div class="box-footer"></div>
            </div>
        <?php endif; ?>

    </div>

</form>
<link type="text/css" rel="stylesheet" href="<?php echo base_url() ?>/js/chosen/chosen.min.css"/>
<script src="<?php echo base_url() ?>/js/chosen/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">

    $(document).ready(function () {
        $("select").chosen({width: '100%'});
    });

    var postdata = <?php echo json_encode($post) ?>, productdata = {};

    <?php if( in_array('product_settings', $form) && isset($meta['product_data']) ) :  ?>

    productdata = <?php echo $meta['product_data'] ?>;

    <?php endif; ?>

    $(document).ready(function (e) {

        $('[name="data[accept_cm]"][value="<?php echo $post['accept_cm'] ?>"]').prop('checked', true);

        $.each(postdata, function (i, v) {

            $('[name="data[' + i + ']"]:not(:radio)').val(v).trigger("change blure click");

        });

        $.each(productdata, function (i, v) {

            var el = $('[name="meta[product][' + i + ']"]');
            if ($(el).is("input[type=checkbox]") && v)
                $(el).prop("checked", "checked");
            else
                $(el).val(v).trigger("change blure click");

        });


        $(document).on("click", ".tag .fa", function () {
            $(this).parent().remove()
        });

        $("#tag-input").keyup(function (e) {

            var inp = this, val = $.trim($(inp).val());

            if (e.keyCode == 13 && val != "") {
                $('<span/>', {class: 'tag'})
                    .appendTo($(inp).next())
                    .append($('<i/>', {class: 'fa fa-times red'}))
                    .append($('<a/>', {class: 'a convert-this', tag: val}).html(val));
                $(inp).val('');
            }
        });

        $(document).on("blur", "input[max],input[min]", function () {
            var max = $(this).attr('max'), min = $(this).attr('min'), val = $(this).val();

            if (val > max) $(this).val(max);
            if (val < min) $(this).val(min);

        });

    });

    function addBookPart(btn) {
        var part = '';
    }

    function handleProductType(el) {
        if (el.value == "file")
            $('#product-details').hide();
        else
            $('#product-details').show();
    }

    function addCategory(btn) {

        var nameInp = $('#category-add #cat-val'),
            name = $(nameInp).val(),
            parent = $('#category-add #cat-parent').val(),
            type = $('#category-add #cat-type').val();

        if ($.trim(name) == "") {
            $(nameInp).focus();
            return;
        }

        $(btn).parent().addClass('l h6 blue');

        var data = {name: name, parent: parent, type: type};
        $.ajax({
            type: "POST",
            url: URL + "/category/add",
            data: data,
            dataType: "json",
            success: function (data) {

                if (data.done) {
                    var id = data.data.id;
                    var $div = $('.category-select'), $ul = $($div).find('ul:first');

                    var $li = $('<li/>', {'item-id': id, 'parent': parent, 'name': name});
                    $('<label/>').append(
                        $('<input/>', {value: id, type: 'checkbox', name: 'category[]'})
                    ).append(name).appendTo($li);

                    var $UL = $('<ul/>').append($li);

                    if (!$ul.length) {
                        $($UL).appendTo($div);
                    } else if (parent == 0) {
                        $($li).appendTo($ul);
                    } else {
                        var $LI = $($div).find('li[item-id="' + parent + '"]'),
                            $UL2 = $($LI).find('ul:first');

                        if ($UL2.length) $($li).appendTo($UL2);
                        else $($UL).appendTo($LI);
                    }

                    $('<option/>', {value: id, name: name, parent: parent, 'item-id': id}).html(name)
                        .appendTo($('#category-add #cat-parent'));

                    $(btn).parent().removeClass('l h6 blue');

                } else {
                    dialog_box('افزودن دسته بندی جدید با مشکل مواجه شد');
                    $(btn).parent().removeClass('l h6 blue');
                }
            }
        });
    }

    function updatePost(btn, action) {


        var title = $.trim($('#post-title').val());
        var price = parseInt($('input[name="meta[price]"]').val());
        if (isNaN(price)) {
            $('input[name="meta[price]"]').val(0)
        }
        if (title == "") {
            $('#post-title').focus();
            return;
        }

        convertToFormEl();

        <?php if( in_array('editor', $form) ): ?>
        $('#content').val(EDITOR.getData());
        <?php endif ?>

        <?php if($type == 'book'): ?>
        /*if(!checkBookData())
        {
            //$(btn).removeProp("disabled");
            //$footer.removeClass('l blue');
            return;
        }*/
        <?php endif ?>

        var key = true;
        $('#postForm .need').each(function (i, el) {
            if ($.trim($(el).val()) == '') {
                $('html,body').animate({
                    scrollTop: $(el).offset().top - 120
                }, 300, function () {
                    $(el).focus()
                });
                key = false;
                return false;
            }
        });

        if (!key) return;

        var $footer = $(btn).closest('.box-content').next();
        var type = action == "draft" ? "پیش نویس ذخیره شد" : "منتشر شد";//Alireza Balvardi
        type = action == "test" ? "کتاب آماده انتشار شد" : type;//Alireza Balvardi

        $(btn).prop("disabled", "disabled");
        $footer.addClass('l blue');

        var data = new FormData(document.getElementById('postForm'));

        $.ajax({
            type: "POST",
            url: URL + "/post/save/" + action,
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            timeout: 300000,
            timeOut: 300000,
            success: function (data) {
                $(btn).removeProp("disabled");
                $footer.removeClass('l blue');

                if (data == 'login') {
                    login(function () {
                        updatePost(btn, action);
                    });
                } else {
                    var currentDate = new Date();
                    var time = '<i class="fa fa-clock-o"></i> ' + +currentDate.getHours() + ":" + currentDate.getMinutes();
                    $footer.html(type + ' &nbsp; ' + time);
                    $('#result').html(data);
                }
            },
            error: function (a, b, c) {
                $(btn).removeProp("disabled");
                $footer.removeClass('l blue');
                $footer.html($('<span/>').css("color", "red").html('ارتباط برقرار نیست'));
            }
        });
    }


    function adDl(data, files, btn) {

        var $box = $('.dl-box'),
            $row = $('<div/>').addClass('dl-row'),
            $inName = $('<input/>', {type: 'text', name: 'meta[dl][name][]'}).addClass('input small dl-name'),
            $inFile = $('<input/>', {type: 'text', name: 'meta[dl][file][]', readonly: 'readonly'})
                .addClass('input small dl-file'),
            $fa = $('<i/>', {class: 'fa fa-times fa-lg red cu'}).on("click", function () {
                $(this).closest('.dl-row').remove();
            });

        $(files).each(function (i, file) {

            var name = $(file).data('name'), val = $(file).data('file');

            var r = $($row).clone(true)
                .append($($inFile).clone(true).val(val))
                .append($($inName).clone(true).val(name))
                .append($($fa).clone(true))
                .appendTo($box)

        });
    }

    function adDl2(data, files, btn) {

        var $box = $('.dl-box-book'),
            $row = $('<div/>').addClass('dl-row'),
            $inName = $('<input/>', {type: 'text', name: 'meta[dlbook][name][]'}).addClass('input small dl-name'),
            $inFile = $('<input/>', {type: 'text', name: 'meta[dlbook][file][]', readonly: 'readonly'})
                .addClass('input small dl-file'),
            $fa = $('<i/>', {class: 'fa fa-times fa-lg red cu'}).on("click", function () {
                $(this).closest('.dl-row').remove();
            });

        $(files).each(function (i, file) {

            var name = $(file).data('name'), val = $(file).data('file');

            var r = $($row).clone(true)
                .append($($inFile).clone(true).val(val))
                .append($($inName).clone(true).val(name))
                .append($($fa).clone(true))
                .appendTo($box)

        });
    }

</script>