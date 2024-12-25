<div class="page-title">رسانه</div>

<div id="upload-div">

	<div class="clear" style="height:20px"></div>
    
    <div align="center">
      <span class="fileinput-button button">
        <span>افزودن فایل</span>
        <input id="fileupload" class="fileupload-input" name="file" type="file" multiple 
        u-url='<?php echo base_url()."admin/upload/" ?>'>
      </span>
    </div>
    
	<div class="clear" style="height:30px"></div>
    <div id="upload-roll"></div>
    <div class="clear" style="height:20px"></div>
    
</div>

<div id="media">
	
    <div class="pics"><span class="l blue"></span></div>
    
    <div class="clear" style="height:20px"></div>
    
    <div class="files"><span class="l blue"></span></div>

</div>

<script>
$(document).ready(function(e) {
    uploader('#fileupload',update);
	update();	
});

function update(){
	
	$.ajax({
		url:URL+"/media/images/0/20",
		type:"POST",
		data:{selectable:true,multiple:true,options:true},
		success: function(data){
			$('#media .pics').html(data)
			.find('.media-images img:hidden').each(function(index, element) {
                   $(this).load(function(){$(this).fadeIn(200)});
             });
		}
	});	
	
	$.ajax({
		url:URL+"/media/files/0/20",
		type:"POST",
		data:{selectable:true,multiple:true,options:true},
		success: function(data){
			$('#media .files').html(data);
		}
	});		
}

</script>