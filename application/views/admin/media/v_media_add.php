<div class="page-title">افزودن رسانه</div>

<div id="upload-div">

	<div class="clear" style="height:20px"></div>
    
    <div align="center">
      <span class="fileinput-button button">
        <span>افزودن فایل</span>
        <input id="fileupload" class="fileupload-input" name="file" type="file" multiple 
        u-url="<?php echo base_url('api/media_upload/upload'); ?>">
      </span>
    </div>
    
	<div class="clear" style="height:30px"></div>
    
    <div id="dropzone" class="fade well">
        <div id="upload-roll"></div>
        <div class="clear"></div>
	</div>
    
</div>

<div style="display:none">
    <form action="<?php echo base_url()."admin/upload/" ?>" method="post" enctype="multipart/form-data">
    <input  name="file" type="file" multiple >
    <input type="submit" value="send">
    </form>
</div>

<script>
$(document).ready(function(e) {
    uploader('#fileupload');
});
</script>