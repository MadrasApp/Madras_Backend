$(document).ready(function(e) {
	
	$(document).on("click",".selectable",function(){
		
		var un = $(this).attr('unique-group');
		if(un)
		$('.selectable[unique-group="'+un+'"]').not(this).removeClass('selected');
		
		$(this).toggleClass('selected');
		
	});

	$(document).on("click",".media-item-option-btn",function(e){
		$('.media-item-option-btn').not(this).next().removeClass('visible');
		$(this).next().toggleClass('visible');
	});	
	
	$(document).on("click",".media-item-options i",function(e){
		
		var op = $(this).attr('op');
		
		console.log(op);
		switch(op){
			
			case 'view':
			break;
			case 'delete':
			
			delete_file($(this).closest('.media-item').data('file')); 
			
			break;
			case 'edit':
			break;
		}
		
	});		
});


function delete_file(file,callback){

	
	var $buttons = $('<div/>')
				.append($('<button/>').attr("action","submit").html('حذف'))
				.append($('<button/>').html('لغو'))
	
	var onSubmit = function(){
		
		$.ajax({
			url: URL+"/deletefile",
			type:"POST",
			dataType:"json",
			data:{file:file},
			success: function(data){
				if(data.done){
					$('.media-item[data-file="'+file+'"]').hide(300,function(){$(this).remove()});
					if(typeof(callback)=='function') callback();
				}
				else dialog_box(data.msg);
			},
			error: function(){ajax_fail()}
		});		
	}
	var $body = $('<p/>').attr("align","center")
	.html('<img src="'+BURL+'style/images/warning.png" style="margin:0px" width="25"> فایل  حذف شود ؟');
	
	var options = {
		id        : 'delete-files-dialog',  
		name      : 'حذف فایل',
		body      : $body ,
		buttons   : $buttons,
		onSubmit  : onSubmit		
	};
	dialog_box(options);
 }
 
/**************************/
var mSettings;
function media(options,button,callback){
	
	var mediaType = 'general';
	$("html,body").css("overflow","hidden");
		
	if(typeof(options)=="string")
	{
		mediaType = options;
		switch(options)
		{
			case 'img'    : options = {include:{images:true,files:false}};break;
			case 'file'   : options = {include:{images:false,files:true},selected:'files'};break;
			case 'img,1'  : options = {include:{images:true,files:false},multiple:false};break;
            case 'img,1,l': options = {include:{images:true,files:false},multiple:false};break;
			case 'file,1' : options = {include:{images:false,files:true},multiple:false,selected:'files'};break;
			case 'editor' : options = {thumbs:true,insert:true };break;
		} 		
	}
	
	mSettings = $.extend({},{
			id        : 'select-media', // The id of media selector
			multiple  : true,           // User can select multiple files ??
			thumbs    : false,          // User can select thumbinail size of images?
			insert    : false,          // The seleted file(s) can insert to text editor ?
			tabs      : {
				upload  : {name:'افزودن', html:''  ,icon:'cloud-upload' ,style:{padding:20}},
				images  : {name:'تصاویر', html:''  ,icon:'photo' ,style:{}},
				files   : {name:'فایل ها',html:'' ,icon:'file-o' ,style:{}},
			},
			selected  : 'images',
			include   : {images:true,files:true},
			btntext   : 'ادامه',
			onClose   : null,
			onSelect  : null,
			onSubmit  : null,
            button    : button,
            callback  : callback
		},options);
		
	var existMedia = $('#'+(mSettings.id));
	if(existMedia.length && $(existMedia).data('type') == mediaType)
	{
		$(existMedia).show();setSize();
		return;
	}
	else
	{
		$(existMedia).remove();
	}
	
	var $media    = $('<div/>').addClass("media-select").data('type',mediaType);
	var $body     = $('<div/>').addClass("media-select-body");
	var $main     = $('<div/>').addClass("media-select-main").appendTo($body);
	var $sidebar  = $('<div/>').addClass("media-select-sidebar").appendTo($body);
	var $header   = $('<div/>').addClass("media-select-main-header").appendTo($main);
	var $content  = $('<div/>').addClass("media-select-content").appendTo($main);
	var $scontent = $('<div/>').addClass("media-select-sidebar-content").appendTo($sidebar);
	var $sfooter  = $('<div/>').addClass("media-select-footer").appendTo($sidebar);
	
	var $closeBtn = $('<div/>').addClass("media-select-close-btn").appendTo($header)
	.append($('<i/>').addClass("fa fa-times-circle fa-2x"))
	.on("click",function(){
		$media.hide();
		$("html,body").css("overflow","");
	});
	
	$('<span/>').addClass("media-select-footer-info").appendTo($sfooter);
	
	$('<i/>').addClass('fa fa-refresh cu media-reload').on("click",updateMedia).appendTo($header);
	$('<i/>').addClass('media-loading loader blue2 h4').appendTo($header);
	
	
	for(var tab in mSettings.tabs){
		var data = mSettings.tabs[tab];
		
		if(tab=='upload' && ! userCan('upload_file') ) continue;
		if(tab=='images' && ! mSettings.include.images) continue;
		if(tab=='files'  && ! mSettings.include.files ) continue;
		
		addTab(tab,data);
	}
	
	if(mSettings.id) $media.attr('id',mSettings.id);
		
	/***********************************/
	$media.append($body);
	
	$('body').append($media);
		
	
	/***********************************/
				
	$media.css({'z-index':(Hz())+1});
	
	var uC = function(){
		createUploader('.media-select-content-tab[tab=upload]', baseUrl + 'admin/media_upload/upload', updateMedia);
	};
	userCan('upload_file',uC);
	
	$media.on("click",".media-item.selectable",function(e){
		
		var element = this;
		
		setTimeout(function(){
			
			var selctedfiles = $('.media-item.selectable.selected');
			
			$('.media-select-footer-info').html(selctedfiles.length+' selectd');
						
			$submit.css("opacity",selctedfiles.length?1:.7);
			
			updateMediaSidbar(element,mSettings.thumbs,mSettings.insert);
			
			if(typeof(mSettings.onSelect)=='function')
                mSettings.onSelect(element,selctedfiles);
			
		},100);
	});	
	
	var $submit = $('<button/>').addClass('media-select-submit-btn small-btn')
	.css({'float':'left','opacity':.7}).html(mSettings.btntext).appendTo($sfooter)
	.on("click",function(e){
		
			var data = getSelctedFiles(),
			files = $('.media-item.selected');
			
			if( ! files.length ) return false;
			
			if(typeof(mSettings.onSubmit)=='function')
                mSettings.onSubmit(data,files);
			
			if(typeof(mSettings.callback)=='function')
                mSettings.callback(data,files,mSettings.button);
			
			if( mediaType == 'editor' )
			{
				insertToEditor(files);
			}
			else if( mSettings.button && mSettings.button.nodeType == 1 )
			{
				var $ap = $(mSettings.button).closest('.media-ap'),
				$input = $ap.find('.media-ap-input'),
				$thumb = $ap.find('.media-ap-data');
				
				$(files).each(function(i, file) {
					var originalFilePath = $(file).data('file');
					var cleanFilePath = originalFilePath.replace('/lexoya/var/www/html/', '');
				
					if ($input.length) {
						$($input[i]).val(cleanFilePath);
					}
				
					if ($thumb.length) {
						var size = $thumb.data('thumb');
						var originalThumbPath = $(file).data(size);
						var cleanThumbPath = originalThumbPath.replace('/lexoya/var/www/html/', '');
				
						var img = $('<img/>', { src: cleanThumbPath, file: cleanFilePath })
							.addClass('convert-this img-responsive');
				
						if ($thumb.hasClass('replace')) {
							$thumb.html(img);
						} else {
							$thumb.append(img);
						}
					}
				});
				
			}
			
			$(files).removeClass('selected');
			
			$("html,body").css("overflow","");
			$media.hide();		
	});
	
	var lastUpdate = false;
	
	function addTab(name,data){
		
		var style = data.style;
		if(!style) style = {};
		var tabbtn = $('<div/>').addClass('media-select-tab-btn')
			.attr({'tab':name}).append($('<i/>').addClass('fa fa-'+data.icon)).append(data.name)
			.on("click",function(){
				$('.media-select-content-tab').hide();
				$('.media-select-content-tab[tab="'+$(this).attr('tab')+'"]').show();
				$('.media-select-tab-btn').removeClass('selected');
				$(this).addClass('selected');
			});
		
		if(name == 'upload')
		$(tabbtn).insertAfter($closeBtn);
		else 
		$(tabbtn).appendTo($header);

		$('<div/>').addClass("media-select-content-tab")
			.attr({'tab':name}).html(data.html).css(style).appendTo($content);		
	}
	
	function getSelctedFiles(){
		
		var selctedfiles = $('.media-item.selectable.selected');
		if( ! selctedfiles.length ) return false;
		
		var files = [];
		 
		$(selctedfiles).each(function(index, el) {
			files.push($(el).data('file'));
		});
		return files;
	}
	
	function updateImages(append){
		
		mediaLoding(1);
				
		var begin,total = 20;
		
		if( ! append ) begin = 0; else begin = $('.media-item.media-images').length;
			
		total = getTotal(begin);
		
		var dir = readCookie('_ad0f');
		
		$.ajax({
			url:URL+"/media/images/"+begin+"/"+total,
			type:"POST",
			data:{selectable:true,multiple:mSettings.multiple,options:false,dir:dir},
			success: function(data){
				
				var $tab = $('.media-select-content-tab[tab=images]');
				
				if( ! append ){
					$tab.html(data);
					MediaData('restore');
				}else
				$tab.append(data);
				
				$tab.find('.media-images img:hidden').each(function(index, element) {
					$(this).load(function(){$(this).fadeIn(200)});
				}); 
				
				updateMediaSidbar();
				
				mediaLoding(0);
			},error: function(){mediaLoding(0)}
		});
	}
	
	function updateFiles(append){
		
		mediaLoding(1);
		
		var sFiles = getSelctedFiles(),begin,total;
		
		if( ! append ) begin = 0; else begin = $('.media-item.media-files').length;
		
		total = getTotal(begin);
		
		var dir = readCookie('_ad0f');
		
		$.ajax({
			url:URL+"/media/files/"+begin+"/"+total,
			type:"POST",
			data:{selectable:true,multiple:mSettings.multiple,options:false,dir:dir},
			success: function(data){
				
				var $tab = $('.media-select-content-tab[tab=files]');
				
				if( ! append ){
					$tab.html(data);
					MediaData('restore','files');
				}
				else $tab.append(data);
				
				updateMediaSidbar();
				
				mediaLoding(0);
				
			},error: function(){mediaLoding(0)}
		});		

	}
	
	function updateMedia(){
		
		/*if( lastUpdate && lastUpdate+5 > strTime() ) return;
		lastUpdate = strTime(); 
		$('.media-reload').addClass('disable');
		setTimeout(function(){$('.media-reload').removeClass('disable')},5000);*/
		MediaData('backup');
		
		if(mSettings.include.images)
		updateImages();
		
		if(mSettings.include.files)
		updateFiles();
		
		$.ajax({
			type:"POST",
			url:URL+"/mediadirlist",
			dataType:"json",
			success: function(data){
				
				$header.find('select,.media-header-select,.select-menu').remove();
				if(data.permission && data.list.length )
				{
					var dir = readCookie('_ad0f') || data.user;
					var $select = $('<select/>').addClass('media-header-select')
					.on("change",function(){
						createCookie('_ad0f',this.value,1);
						updateMedia();
					}).appendTo($header);
					
					$.each(data.list,function(i,v){
						$('<option/>').val(v).html(v).appendTo($select);
					});
					$select.val(dir)
					selectMenu($select,'user');
				}
			}
		});
		
		
				
	}

	function setSize(){
		
		var ww = $(window).width(),wh = $(window).height(),
		mh = $('.media-select-main').height(),
		hh = $('.media-select-main-header').outerHeight(),
		sh = $('.media-select-sidebar').height(),
		fh = $('.media-select-footer').outerHeight();
		
		$('.media-select-sidebar-content').innerHeight(sh-fh);
		$('.media-select-content').innerHeight(mh-hh);
		
		var sminW = 250,fileW = 130,scrollW = 0,
		bW = $('.media-select-body').innerWidth(), remW = bW-sminW-scrollW , 
		items = Math.floor(remW/fileW),mW = (items*fileW)+scrollW;
		
		$('.media-select-main').innerWidth(mW);
		$('.media-select-sidebar').innerWidth(bW-mW-3);
				
	}	
	
	function getTotal(begin){

		var w = $('.media-select-content').width(),
		h = $('.media-select-content').height(),
		col = w /130 , row = Math.ceil(h/130);
				
		if( begin == 0 ) return  2*row*col;
		
		else  return  3*col*row;
		
	}	
	
	$(window).resize(setSize);
	
	setSize();
	
	updateMedia();
	
	$('.media-select-tab-btn[tab="'+mSettings.selected+'"]').trigger("click");
	
	$('.media-select-content').mCustomScrollbar({
		theme: "3d-thick",
		scrollButtons:{enable:true},
		scrollInertia:100,
		//autoHideScrollbar: true,
		//autoExpandScrollbar :true ,
		scrollbarPosition:"outside",
		callbacks:{
			onTotalScroll: function(){
				
				var tab = $('.media-select-tab-btn.selected').attr('tab');
				
				if(tab=='images') updateImages(true);
						
				if(tab=='files') updateFiles(true);
						
			}
		}
	});	
	
	/*$('.media-select-sidebar-content').mCustomScrollbar({
		theme: "3d-thick",
		scrollButtons:{enable:true},
		scrollInertia:100,
		//autoHideScrollbar: true,
		scrollbarPosition:"outside"
	});*/

} 

function updateMediaSidbar(file,thumb,insert){
	
	var sidebar = $('.media-select-sidebar-content');
	
	if(!file){ $(sidebar).html(''); return; }
		

	function addRow(c1,c2){
		if(!c2)
		return $('<tr/>').append($('<td/>').attr("colspan","2").html(c1));
	    return $('<tr/>').append($('<td/>').html(c1)).append($('<td/>').html(c2));
	}
		
	var data = $(file).data(),
		name = data.name,
		size = data.size,
		type = data.type.toUpperCase(),
		ext  = data.type.toLowerCase(),
		date = data.date,
		path = data.file.replace('/lexoya/var/www/html/', ''),
		thumb150  = BURL+data.thumb150,
		thumb300  = BURL+data.thumb300,
		thumb600  = BURL+data.thumb600, 
		eSizebase = data.selfsize,
		eSize150  = data.thumb150size,
		eSize300  = data.thumb300size,   
		eSize600  = data.thumb600size,
		privew  = $(file).find('.media-item-icon').clone(true),
		cls     = 'media-select-sidebar-',
		isImage = $(file).hasClass('media-images') ? true : false;
	
	var $info = $('<div/>',{'dir':'rtl'});
	
	if(ext == 'mp4' || ext == 'webm')
	{
		privew = '<div style="text-align:center"><video controls style="max-width:100%">\
					 <source src="'+path+'" type="video/'+ext+'">\
					  Your browser does not support the video.\
				  </video></div>';	
	}else if(ext == 'mp3')
	{
		privew = '<div style="text-align:center"><audio  controls style="max-width: 100%;">\
				  <source src="'+path+'" type="audio/mpeg" title="'+name+'">\
				  Your browser does not support the audio tag.\
				 </audio></div>';	
	}	
	
	
	var p = $('<div/>').addClass(cls+'preview').append(
				isImage ? 
				$('<img/>').attr('src',path).css('max-width',$(sidebar).width()-17) : 
				$('<div/>').css('padding',20).append(privew) 
			);
			
	var $table = $('<table/>').addClass('table light2');
	
	$table.append($('<tr/>').append($('<th />').attr("colspan","2").html(name).addClass(cls+'name')));
	$table.append($('<tr/>').append($('<td />').attr("colspan","2").html(p).css("padding",0)));
	
	if(isImage)
	$table.append(addRow('ابعاد',eSizebase));
	$table.append(addRow('فرمت ',type));
	$table.append(addRow('حجم',size));
	
	var time = date.split('&');
	var $date = $('<span/>',{class:'relative-date',datestr:time[0],date:time[1],'data-title':time[2]}).html(time[1]);
	$table.append(addRow('آپلود شده در',$date));
	
	var deleteBtn = userCan('delete-file') ?  $('<button/>').addClass('small-btn')
	.html('<i class="fa fa-trash-o"></i> حذف')
	.on("click",function(){
		delete_file(path,updateMediaSidbar);
	}) : '';
	
	var downloadBtn = $('<a/>').attr({href:BURL+path,download:BURL+path}).html(
		$('<button/>').addClass('small-btn')
		.html('<i class="fa fa-cloud-download"></i> دریافت')
	).css('vertical-align','top'); 
	
	var addressBtn = $('<button/>').addClass('small-btn')
	.html('<i class="fa fa-link"></i> آدرس')
	.on("click",function(){
		var div = '<div class=ar style="max-width:500px;text-align:left" dir=ltr>'+BURL+path+'</div>'; $('<div/>').css('max-width',500);
		dialog_box(div);
	});	
	
	$table.append(addRow($('<div/>').append(addressBtn).append(downloadBtn).append(deleteBtn)));

	$table.appendTo($info);
	
	
	if( thumb || insert )
	{
		
		var $table2 = $('<table/>').addClass('table light2');
		
		if( insert )
		{
			var $sel = $('<select/>',{class:cls+'insert input small'})
						.css({'padding':"3px 5px",'margin':0,'width':"100%"})
						.on("change",function(){
							$(file).attr('data-insert',this.value)
						});
			$('<option/>',{value:'link',html:'لینک در متن'}).appendTo($sel);
			$(file).hasClass('can-insert') && $('<option/>',{value:'insert',html:'نمایش در متن'}).appendTo($sel);
			
			var setInsert = $(file).data('insert') || 'link';
			$sel.val(setInsert);
			$(file).attr('data-insert',setInsert)
			$table.append(addRow('استفاده بصورت',$sel));
		}
		if( thumb && eSize150)
		{
			var $sel2 = $('<select/>',{class:cls+'thumb input small'})
						.css({'padding':"3px 5px",'margin':0,'width':"100%"})
						.on("change",function(){
							$(file).attr('data-thumb',this.value)
						});			
			$('<option/>',{value:thumb150  ,html:'کوچک  '  + eSize150}).appendTo($sel2);
			$('<option/>',{value:thumb300  ,html:'متوسط  ' + eSize300}).appendTo($sel2);
			$('<option/>',{value:thumb600  ,html:'بزرگ  '  + eSize600}).appendTo($sel2);
			$('<option/>',{value:BURL+path ,html:'واقعی  ' + eSizebase}).appendTo($sel2);
			
			var setThumb = $(file).data('thumb') || thumb300;
			$(file).attr('data-thumb',setThumb);
			$sel2.val(setThumb);
			$table.append(addRow('اندازه',$sel2));
		}
		if( insert )
		{
			var $infoName = $('<input/>',{class:cls+'thumb input small',type:'text'})
			.css({'padding':"5px",'margin':0,'width':"100%"}).on("blur keyup keydown",function(){
				$(file).attr('data-info-name',this.value)
			});
			
			var $infoDesc = $('<textarea/>',{class:cls+'thumb input small'})
			.css({'padding':"3px 5px",'margin':0,'width':"100%"})
			.on("blur keyup keydown",function(){
				$(file).attr('data-info-desc',this.value)
			});
			
			$infoName.val( $(file).data('info-name') || name );
			$infoDesc.val( $(file).data('info-desc') || name );
			$table.append(addRow('نام',$infoName));
			$table.append(addRow('توضیحات',$infoDesc));
		}		
		
		//$table2.appendTo($info);		
	}
	$(sidebar).html($info);
	updateTime();
} 

var mediaLoadingList = 0;

function mediaLoding(key){
	
	var reload = $('.media-reload'), loading = $('.media-loading');
	
	function off(){ $(reload).css('display','inline-block');$(loading).hide() }
	function  on(){ $(reload).hide();$(loading).css('display','inline-block') }
	
	if(key){
		
		on();
		mediaLoadingList++;
		
	}else{
		mediaLoadingList--;
		if( mediaLoadingList < 0 ) mediaLoadingList = 0;
		
		if( mediaLoadingList == 0 ) off();
	}	
 }
 
var mediaFilesData = [];

function MediaData(op,cls){
	
	if(op)
	switch(op)
	{
		case 'backup':
		mediaFilesData = $('.media-item');
		break;	
		case 'restore':
		$(mediaFilesData).each(function(index, el) {
			var file = $('.media-item[data-file="' + $(el).data('file') + '"]');
			var data = $(el).data();
			$.each(data,function(i,v){
				$(file).data(i,v);
			});
			$(file).attr('class',$(el).attr('class'));
		});
		break;		
	}
	
 }

function insertToEditor(files){

	$(files).each(function(index, file) {
		
		var name   = $.trim($(file).attr("data-info-name")) || $(file).attr("data-name"),	
			title  = $.trim($(file).attr("data-info-desc")) || $(file).attr("data-name"),
			path   = $(file).attr("data-thumb") || BURL + $(file).attr("data-file"),
			insert = $(file).attr("data-insert"),
			ext    = $(file).attr("data-type");
			
		switch (insert){
			case 'link':		
			var a = ' <a title="'+title+'" href="'+path+'">'+name+'</a> ';
			EDITOR.insertHtml(a);
			break;		
			case 'insert':
			
			var html ;
			switch(ext){
				case 'jpeg':case 'jpe':case 'jpg':case 'png':case 'gif':				
				html = '<img src="'+path+'" class="inline-image" file="'+ $(file).data('file') +'" style="max-width:100%" alt="'+title+'" title="'+title+'" />';							
				break;
				
				case 'mp3':case 'ogg':
				html = '<p></p><div style="text-align:center"><audio  controls>\
				  <source src="'+path+'" type="audio/mpeg" title="'+title+'">\
				  Your browser does not support the audio tag.\
				 </audio></div><p></p>';				 
				break;
								
				case 'mp4':case 'webm':case 'mov':
				html = 
				'<p></p><div style="text-align:center"><video controls style="max-width:100%">\
					 <source src="'+path+'" type="video/'+ext+'">\
					  Your browser does not support the video.\
				  </video></div><p></p>';				  
				break;				
			}
			
			EDITOR.insertHtml(html);
			break;
		}
    });
	
 }