<div class="box media-ap edit-post-icon">
    <div class="box-title"><i class="fa fa-photo"></i> تصویر</div>

    <div class="box-content" style="padding:2px;text-align:center">
        <div class="convert-to-form-el editable-img" form-el-name="data[icon]" form-el-value="file">
                        <span class="media-ap-icon replace" data-icon="icon300">

                        <?php $iconKey = (isset($post['icon']) && trim($post['icon']) != "") ?>

                            <?php if ($iconKey) : ?>
                                <img src="<?php echo  base_url() . $icons['base'][300] ?>" file="<?php echo  $icons['base']['b'] ?>" class="convert-this">
                            <?php endif; ?>

                        </span>
            <div id="add-icon-icon" class="plus add-img"
                 onClick="media('img,1',this,function(){ $('#add-icon-icon').hide() })"
                 style="display:<?php echo  $iconKey ? 'none' : 'inline-block' ?>;margin:15px"></div>
            <div class="form-ap-data" style="display:none"></div>
        </div>
    </div>

    <div class="box-footer"><i class="fa fa-pencil cu" onClick="media('img,1',this)"></i></div>
</div>
