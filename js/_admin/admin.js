
$(document).ready(function(e) {

	$('body').keyup(function(e){
		if(e.which == 27){
			ClosePopupScreen($('.full-screen'));
		}
	});

    $(document).on('change','.ajax-group-tree select',function(){

        var el     = $(this) ,
            id     = $.trim($(el).val()),
            //pr   = $(el).closest('.ajax-group-tree'),
            holder = $(el).closest('.group-holder');

        var res = $(el).closest('.group-holder').next();

        if( $.trim($(el).val()) == '' )
        {
            $(holder).nextAll('.group-holder').remove();
            return false;
        }

        if( ! $(res).length || ! $(res).hasClass('group-holder') )
        {
            res = $(holder).clone(true).insertAfter(holder);
        }

        $(res).nextAll('.group-holder').remove();

        res = $(res).find('select').parent();
        $(res).html('<i class="l h5 orange"></i>');

        $.ajax({
            type: "POST",
            url : "api/subgroup/"+id,
            data: {
                'name'       : $(el).attr('name'),
                'class'      : $(el).attr('class'),
                'csrf_token' : csrf
            },
            dataType:"json",
            success: function(data){

                $(res).html('');
                if( data.status == 0 )
                {
                    if( data.msg == null )
                        $(res).remove();
                    else
                        $(res).html(data.msg);
                }
                else if(data.msg != '')
                {
                    $(res).html(get_alert(data));
                }
            },
            error: function(){
                $(res).remove();
                notify('خطا در اتصال',2);
            }
        });
    });

	$(document).on("click",".toggle-sub-menu",function(){
		var toslide = $(this).closest('.option').next(),
			li = $(this).closest('li.sidebar-item'),
			up = $(li).hasClass('open') ? 1:0;
		$('#sidebar > ul > li > ul').not(toslide).slideUp("fast",function(){
			$(this).css("display",'');
			$(this).parent().removeClass('open');
		});
		$('#sidebar > ul > li > ul').not(toslide).prev().find('.toggle-sub-menu').removeClass('reverse');
		
		if(!up) $(li).addClass('open');
		
		$(this).toggleClass('reverse');
		$(toslide).slideToggle("fast",function(){
			$(this).css("display",up?'':'block');
			if(up) $(li).removeClass('open');
		});
		return  false;
	});
	
	$(document).on("mouseover",".editable-img img",function(){
		
            var el = this;
			if( $(el).parent().hasClass('img-editor') ) return;
			var editable = $('<div/>').addClass('img-editor');
			$(el).wrap(editable);
			$('<i/>').addClass('fa fa-times fa-lg cu').on("click",function(){
				$(el).closest('.editable-img').find('.add-img').show();
                $(el).closest('.editable-img').find('.media-ap-input').val('');
				$(this).parent().remove();
			}).insertAfter(el);
		
	}).on("mouseleave",".editable-img .img-editor",function(){
		var el = this;
		$(el).replaceWith($(el).find('img'));
	});	
	
	var this_page_sub = $('#sidebar ul ul .this-page');
	if(this_page_sub.length){
		
		$(this_page_sub).closest('ul').prev().find('.toggle-sub-menu').addClass('reverse');
		$(this_page_sub).closest('ul').show();
		$(this_page_sub).closest('ul').prev().addClass('this-page');
		$(this_page_sub).closest('ul').parent().addClass('open');
	}
	
	$(window).on('hashchange', function(e) {
		checkHash();
		set_window_size();	
		e.preventDefault();
		return false;
	});	

	$(window).trigger("hashchange");
	
	$(window).resize(function(e) {
        set_window_size();
    });
		
	set_window_size();
	
	getUserInfo();
	$("#sidebar > ul").stick_in_parent();

    $(document).on('submit','.filter-form',function(e){
        var input = $(this).find('.form-control');
        $(this).find('select').add($(this).find('input')).each(function(i,el){
            if( $(el).val() == '' ) $(el).attr('disabled', true);
        });
    });

    $(document).on("change",".chk-tg-field",function(e){
        toggleField(this,e);
    });
});


var csrf = function(){ return readCookie('csrf_cookie') };

function set_window_size(){
	var ww = $(window).width(),
	wh = $(window).height(),
	hh = $("#header").outerHeight(true),
	fh = $("#footer").outerHeight(true),
	fbh = $("#footer-bottom").outerHeight(true);
	
	var rem = wh-hh-fh-fbh;
	$('#container').css('height',rem);
}

function checkHash(){
	
		var hash = document.location.hash,page="home";
		hash = hash.replace('#','');
		
		if(hash == "")
			page = "home";
		else 
			page = hash;
		
		var c = $('.page.hash:visible').attr('id');
		
		if(c!=page)
		{
			$('.page.hash').hide();
			$('#'+page).show();
		}
}

function Confirm(options){
	
	var options = $.extend({},{
		url         : '',
		data        : {},
		btn         : 'ادامه',
		btn2        : 'لغو',
		success     : null,
		fail        : null,
		loader      : null,
		loadercolor : 'blue',
		Dname       : '',
		Dhtml       : '',
		Did         : '',
		Dimg        : ' <img src="'+BURL+'style/images/warning.png" style="margin:0 0 0 5px" width="25"> '
	},options);
	var $buttons = $('<div/>')
				.append($('<button/>').attr("action","submit").html(options.btn))
				.append($('<button/>').html(options.btn2));
				
	var onSubmit = function()
	{
		if( options.loader ) $(options.loader).addClass('l '+ options.loadercolor );
		$.ajax({
			url: URL + "/" + options.url,
			type:"POST",
			dataType:"json",
			data:options.data,
			success: function(data){
				
				if(data == 'login')
				{
					login(onSubmit);	
				}
				else
				{
					if(data.done)
					{
						if( typeof(options.success) == 'function' )
						options.success(data);						
					}
					else dialog_box(data.msg);					
				}				
				if( options.loader ) $(options.loader).removeClass('l '+ options.loadercolor );
                if(typeof data.msg !== 'undefined'  && typeof data.status !== 'undefined' )
                    notify(data.msg ,data.status);
			},
			error: function(){
				if( typeof(options.error) == 'function') options.fail();
				if( options.loader ) $(options.loader).removeClass('l '+ options.loadercolor );
                notify('خطا در اتصال',2);
			}
		});		
	};
	
	var $body = $('<p/>').attr("align","center").html(options.Dimg + options.Dhtml);
	
	dialog_box({
		id        : options.Did,  
		name      : options.Dname,
		body      : $body ,
		buttons   : $buttons,
		onSubmit  : onSubmit		
	});	
}

 
function createUploader(el,url,callback){

    var auto_optimize_images = readCookie('auto_optimize_images');

    if( auto_optimize_images != 0 && auto_optimize_images != 1)
    {
        auto_optimize_images = 1;
        createCookie('auto_optimize_images',1,100);
    }

    $('<form/>').append(
        $('<div/>',{
            id: 'upload-div'
        }).append(
            $('<div/>').addClass('clear').css('height',20)
        ).append(
            $('<div/>').attr('align','center').append(
                $('<span/>').addClass('fileinput-button button').append(
                    $('<span/>').html('افزودن فایل')
                ).append(
                    $('<input/>',{
                        type:'file',
                        id:'fileupload',
                        class:'fileupload-input',
                        name:'file',
                        multiple:'multiple',
                        'u-url':url
                    })
                )
            )
        ).append(
            $('<div/>').addClass('clear').css('height',30)
        ).append(
            $('<div/>',{id:'dropzone',class:'fade well'}).append(
                $('<div/>').attr('id','upload-roll')
            ).append(
                $('<div/>').addClass('clear')
            )
        ).append(
            $('<div/>',{'class':'text-center clear text-muted'}).css('padding','20px 0').append(

                $('<label/>').append(
                    $('<input/>',{'type':'checkbox','name':'optimize','value':auto_optimize_images})
                        .prop('checked',auto_optimize_images != 0 )
                        .on('change',function(){
                            var val = this.checked ? 1:0;
                            createCookie('auto_optimize_images',val,100);
                            this.value = val;
                        })
                ).append(' بهینه سازی خودکار حجم تصاویر ')
            )
        )
    ).appendTo(el);

	uploader('#fileupload',callback);
	
	/*
	var scripts = [
		'jQuery-Uploader/jquery.ui.widget.js',
		'jQuery-Uploader/jquery.iframe-transport.js',
		'jQuery-Uploader/jquery.fileupload.js',
		'jQuery-Uploader/jquery.fileupload-process.js',
		'jQuery-Uploader/load-image.all.min.js',
		'jQuery-Uploader/canvas-to-blob.min.js',
		'jQuery-Uploader/bootstrap.min.js',
		'jQuery-Uploader/jquery.fileupload-image.js',
		'jQuery-Uploader/jquery.fileupload-audio.js',
		'jQuery-Uploader/jquery.fileupload-video.js',
		'jQuery-Uploader/jquery.fileupload-validate.js'
	];
	
	var sLen = scripts.length,L=0;
	
	$.each(scripts, function( index, value ) {
	 	
		var src = BURL+'js/'+value;
		$.getScript(src, function(){
			L++;
			if(L==sLen){
				// end loading
				console.log('load done ');
				
				
			}
			console.log(L);
		});
	});
	*/
 }
 
/**************************/

function uploader(u,callback){
    
    var uploadUrl = $(u).attr('u-url');
	
	var cancelButton = $('<i/>').hide()
		.addClass('fa fa-close fa-2x cancel')
		.on('click', function (e) {
			
			var $this = $(this),data = $this.data('data') || {};
			
			if (!data.jqXHR) {
				data.errorThrown = 'abort';
				$this._trigger('fail', e, data);			
			} else {
				data.jqXHR.abort();
			}
			$this.closest('.upload-row')
			.hide(300,function(){$(this).remove()});
		});	
		
	var deleteButton = $('<i/>').hide()
		.addClass('fa fa-close fa-2x red delete')
		.on('click', function () {
			var row = $(this).closest('.upload-row');
			row.hide(300,function(){$(row).remove()});
		});	
		
	var checkButton = $('<i/>').hide()
		.addClass('fa fa-check fa-2x check')
		.on('click', function () {
			var row = $(this).closest('.upload-row');
			row.hide(300,function(){$(row).remove()});
		});		
				
	var progressDiv = $('<div/>').addClass('progress').append($('<div/>').addClass('progress-bar'));

	$(u).fileupload({
		
		url        : uploadUrl,
		dataType   : 'json',
		autoUpload : false,
		formData   : {},
		dropZone   : $('#dropzone')
		
	}).on('fileuploadadd', function (e, data) {
		
		
		//console.log(data);

        var Fdata = new FormData() ,
			Form  = data.form || $(u).closest('form');
			//Form = data.form;
        if( Form.length )
        {
            Form = $(Form).clone(true);
            $(Form).find('#fileupload').remove();
            Fdata = new FormData(Form[0]);
        }
        var dir = readCookie('_ad0f');
        if( dir )
            Fdata.append('dir',dir);

        data.formData = Fdata;

        data.context = $('<div/>').addClass('upload-row').appendTo('#upload-roll');
		$.each(data.files, function (index, file) {
			var node = $('<div/>')
					.append($('<span/>').text(file.name).addClass('upload-name'));
			if (!index) {
				node.append(progressDiv.clone(true));
			}
			node.appendTo(data.context);
		});
		
	}).on('fileuploadprocessalways', function (e, data) {
		var index = data.index,
			file = data.files[index],
			node = $(data.context.children()[index]);

		node.prepend($('<div/>').addClass('preview')
		.append(
			file.preview ? file.preview : $('<i/>').addClass('fa fa-cloud-upload fa-4x')
		));
		if (file.error) {
			
			node.append($(deleteButton).clone(true).fadeIn());
			node.closest('.upload-row')
				.addClass('failed')
					.attr('data-title',file.error)
						.find('.cancel').fadeOut();
								
		}else
		if (index + 1 === data.files.length) {
			var xhr = data.submit();
			node.append(cancelButton.clone(true).data('data',{jqXHR: xhr}).fadeIn());
		}
		
		node.append('<div class="clear"></div>');
		
	}).on('fileuploadsubmit', function (e, data) {
		
		var row = data.context;
		//$(row).append(cancelButton.clone(true).data(data).fadeIn());
		
	}).on('fileuploadprogress', function (e, data) {
	
		var progress = parseInt(data.loaded / data.total * 100, 10);
		data.context.find('.progress-bar').css('width',progress + '%');
		
		
	}).on('fileuploaddone', function (e, data) {
		
		var progress = $(data.context).find('.progress');
		
		$(data.context).find('.cancel,.delete').fadeOut();
		 
		var result = $.parseJSON(data.jqXHR.responseText);
		result = result.files;
		
		if(result.action == 'done'){
			
			$(data.context).closest('.upload-row').addClass('done');
			$(checkButton).clone(true).appendTo(data.context).fadeIn();			
			// Stop and hide the progress animation immediately on success
			progress.fadeOut();
			setTimeout(function(){
				$(data.context).hide(1000,function(){$(this).remove()});
			},4000);
			
			if(typeof(callback)=='function')
			callback.call();
			
		}else{
			
			$(data.context).closest('.upload-row').addClass('failed');
			$(deleteButton).clone(true).appendTo(data.context).fadeIn();	
			progress.fadeOut();	
		}
		
		$(data.context).closest('.upload-row').attr('data-title',result.msg);
		
	}).on('fileuploadfail', function (e, data) {
		
		$.each(data.files, function (index) {
			$(data.context.children()[index])
				.append($(deleteButton).clone(true).fadeIn())
					.closest('.upload-row').addClass('failed')
						.find('.cancel').fadeOut();
		});
		
	}).prop('disabled', !$.support.fileInput)
		.parent().addClass($.support.fileInput ? undefined : 'disabled');
		
	
	/******************************/
	
	if($('#dropzone').length){
		
		$(document).bind('dragover', function (e) {
			var dropZone = $('#dropzone'),
				timeout = window.dropZoneTimeout;
			if (!timeout) {
				dropZone.addClass('in');
			} else {
				clearTimeout(timeout);
			}
			var found = false,
				node = e.target;
			do {
				if (node === dropZone[0]) {
					found = true;
					break;
				}
				node = node.parentNode;
			} while (node != null);
			if (found) {
				dropZone.addClass('hover');
			} else {
				dropZone.removeClass('hover');
			}
			window.dropZoneTimeout = setTimeout(function () {
				window.dropZoneTimeout = null;
				dropZone.removeClass('in hover');
			}, 100);
		});
		$(document).bind('drop dragover', function (e) {
			e.preventDefault();
		});		
	}

 }

function delete_row(btn,table,id){

    Confirm({
        url         : "deleteRow/"+table+"/"+id,
        Dhtml       : 'به طور کامل حذف می شود .<br/> ادامه می دهید ؟',
        Did         : 'deleterow_'+id,
        success     : function(data){
            $(btn).closest('tr').hide(1000,function(){$(this).remove()});
        }
    });
}
function toggleField(btn){

    var t=$(btn).data('t'),f=$(btn).data('f'),id=$(btn).val(),dchk=!$(btn).prop('checked');

    $(btn).addClass('chk-loading1');

    $.ajax({
        type: "POST",
        url : AURL + "/toggleField",
        data: {table:t,field:f,id:id,csrf_token:csrf},
        dataType:"json",
        success: function(data){

            if(data == "login")
            {
                login(function(){
                    toggleField(btn)
                });
                return;
            }

            $(btn).removeClass('chk-loading1');
            if( data.status != 0 )
                $(btn).prop('checked',dchk);
        },
        error: function(){
            $(btn).removeClass('chk-loading1');
            $(btn).prop('checked',dchk);
            notify('خطا در اتصال');
        }
    });
}
var USER = {};
function getUserInfo(c){
	$.ajax({
		type:"POST",
		url:URL+"/userinfo",
		dataType:"json",
		success: function(data){
			if(data == 'login')
			login(function(){
				getUserInfo(c) 
			});
			else{ USER = data; if(c) c(); }
		},
		error:function(a,b){setTimeout(function(){getUserInfo(c)},10000)}
	});
}
function userCan(Do,c){
	if(typeof(USER.data)!='undefined')
	{
		Do = "_"+Do;
		var l = USER.data.level;
		if(l== "user")return false;
		else if(l == "admin"){if(c)c(); return true}
		else if(USER.can[Do] == 1){if(c)c(); return true};
	}
	else
	getUserInfo(userCan(Do,c));
}

/*********  posts *************/

function convertToFormEl(){
	$('.convert-to-form-el').each(function(i, el) {
		var name = $(el).attr('form-el-name'),
		selector = $(el).attr('form-el-value'),
		data = $(el).find('.form-ap-data');
		
        $(data).html('');
		$(el).find('.convert-this').each(function(i, Fel) {
			$('<input/>',{type:'hidden',name:name}).val($(Fel).attr(selector)).appendTo(data);
        });
    });
}
function ClosePopupScreen(obj){
	if($(obj).attr('close') == 0)
	{
		var $buttons = $('<div/>')
					.append($('<button/>').attr("action","submit").html('خروج'))
					.append($('<button/>').html('لغو'));
		
		var $body = $('<p/>').attr("align","center")
		.append(' <img src="'+BURL+'style/images/warning.png" style="margin:0 0 0 5px" width="25"> ')
		.append('بعضی از گزینه ها تغییر کرده اند ، <br/> بدون ذخیره خارج می شوید ؟');
		
		dialog_box({
			id        : 'close-popup',  
			name      : '',
			body      : $body ,
			buttons   : $buttons,
			onSubmit  : function(){ popupScreen('') }		
		});						
	}
	else
	{
		popupScreen('');
	}
	}
/************* Quick **************/
function popupScreen(html){
	
	var ex = $('.full-screen'),pId = 1;
	if(ex.length) pId = ex.length+1;
	
	if(html == '')
	{
		$('#popup-screen-'+(pId-1)).remove();
		if( ! $('.full-screen').length )
		$('html,body').css("overflow","");	
	}
	else
	{
		var id = 'popup-screen-'+(pId);
		var $popup   = $('<div/>',{class:'full-screen',id:id}).css('z-index',Hz()+1),
			$close   = $('<div/>',{class:'close-popup'}).append(
				$('<i/>',{class:'fa fa-times-circle fa-2x'})
			).on("click",function(){
				ClosePopupScreen(this);	
			}).appendTo($popup),
			$content = $('<div/>',{class:'content'}).html(html).appendTo($popup);
		$popup.appendTo('body').show();
		
		$('html,body').css("overflow","hidden");
		
		$(document).on("keyup keydown paste change", "#" + id + " .if-change",function(){
			
			$("#" + id + " .close-popup").attr('close',0);
		});
	}
}

function quickEdit(type,id){
	
	popupScreen('<div class="l blue c-c h3"></div>');
	
}

function ic(callback){
	
	var cl = function(){
		var el = this ,ic = $(el).attr('ic-name');
		
		if(callback) callback(ic,el);
		
		$('#ic-selector').hide();
		$("html,body").css("overflow","");		
	}
	
	$("html,body").css("overflow","hidden");
	
	$('#ic-selector .ic-item').unbind('click');
	
	$(document).on("click","#ic-selector .ic-item",cl);
	
	if( $('#ic-selector').length )
	{
		$('#ic-selector').show();
		return;
	}

	var icons = new Array(
	'',
	'glass'                    , 'music'                    , 'search'                   , 'envelope-o',
	'heart'                    , 'star'                     , 'star-o'                   , 'user',
	'film'                     , 'th-large'                 , 'th'                       , 'th-list',
	'check'                    , 'remove'                   , 'close'                    , 'times',
	'search-plus'              , 'search-minus'             , 'power-off'                , 'signal',
	'gear'                     , 'cog'                      , 'trash-o'                  , 'home',
	'file-o'                   , 'clock-o'                  , 'road'                     , 'download',
	'arrow-circle-o-down'      , 'arrow-circle-o-up'        , 'inbox'                    , 'play-circle-o',
	'rotate-right'             , 'repeat'                   , 'refresh'                  , 'list-alt',
	'lock'                     , 'flag'                     , 'headphones'               , 'volume-off',
	'volume-down'              , 'volume-up'                , 'qrcode'                   , 'barcode',
	'tag'                      , 'tags'                     , 'book'                     , 'bookmark',
	'print'                    , 'camera'                   , 'font'                     , 'bold',
	'italic'                   , 'text-height'              , 'text-width'               , 'align-left',
	'align-center'             , 'align-right'              , 'align-justify'            , 'list',
	'dedent'                   , 'outdent'                  , 'indent'                   , 'video-camera',
	'photo'                    , 'image'                    , 'picture-o'                , 'pencil',
	'map-marker'               , 'adjust'                   , 'tint'                     , 'edit',
	'pencil-square-o'          , 'share-square-o'           , 'check-square-o'           , 'arrows',
	'step-backward'            , 'fast-backward'            , 'backward'                 , 'play',
	'pause'                    , 'stop'                     , 'forward'                  , 'fast-forward',
	'step-forward'             , 'eject'                    , 'chevron-left'             , 'chevron-right',
	'plus-circle'              , 'minus-circle'             , 'times-circle'             , 'check-circle',
	'question-circle'          , 'info-circle'              , 'crosshairs'               , 'times-circle-o',
	'check-circle-o'           , 'ban'                      , 'arrow-left'               , 'arrow-right',
	'arrow-up'                 , 'arrow-down'               , 'mail-forward'             , 'share',
	'expand'                   , 'compress'                 , 'plus'                     , 'minus',
	'asterisk'                 , 'exclamation-circle'       , 'gift'                     , 'leaf',
	'fire'                     , 'eye'                      , 'eye-slash'                , 'warning',
	'exclamation-triangle'     , 'plane'                    , 'calendar'                 , 'random',
	'comment'                  , 'magnet'                   , 'chevron-up'               , 'chevron-down',
	'retweet'                  , 'shopping-cart'            , 'folder'                   , 'folder-open',
	'arrows-v'                 , 'arrows-h'                 , 'bar-chart-o'              , 'bar-chart',
	'twitter-square'           , 'facebook-square'          , 'camera-retro'             , 'key',
	'gears'                    , 'cogs'                     , 'comments'                 , 'thumbs-o-up',
	'thumbs-o-down'            , 'star-half'                , 'heart-o'                  , 'sign-out',
	'linkedin-square'          , 'thumb-tack'               , 'external-link'            , 'sign-in',
	'trophy'                   , 'github-square'            , 'upload'                   , 'lemon-o',
	'phone'                    , 'square-o'                 , 'bookmark-o'               , 'phone-square',
	'twitter'                  , 'facebook-f'               , 'facebook'                 , 'github',
	'unlock'                   , 'credit-card'              , 'feed'                     , 'rss',
	'hdd-o'                    , 'bullhorn'                 , 'bell'                     , 'certificate',
	'hand-o-right'             , 'hand-o-left'              , 'hand-o-up'                , 'hand-o-down',
	'arrow-circle-left'        , 'arrow-circle-right'       , 'arrow-circle-up'          , 'arrow-circle-down',
	'globe'                    , 'wrench'                   , 'tasks'                    , 'filter',
	'briefcase'                , 'arrows-alt'               , 'group'                    , 'users',
	'chain'                    , 'link'                     , 'cloud'                    , 'flask',
	'cut'                      , 'scissors'                 , 'copy'                     , 'files-o',
	'paperclip'                , 'save'                     , 'floppy-o'                 , 'square',
	'navicon'                  , 'reorder'                  , 'bars'                     , 'list-ul',
	'list-ol'                  , 'strikethrough'            , 'underline'                , 'table',
	'magic'                    , 'truck'                    , 'pinterest'                , 'pinterest-square',
	'google-plus-square'       , 'google-plus'              , 'money'                    , 'caret-down',
	'caret-up'                 , 'caret-left'               , 'caret-right'              , 'columns',
	'unsorted'                 , 'sort'                     , 'sort-down'                , 'sort-desc',
	'sort-up'                  , 'sort-asc'                 , 'envelope'                 , 'linkedin',
	'rotate-left'              , 'undo'                     , 'legal'                    , 'gavel',
	'dashboard'                , 'tachometer'               , 'comment-o'                , 'comments-o',
	'flash'                    , 'bolt'                     , 'sitemap'                  , 'umbrella',
	'paste'                    , 'clipboard'                , 'lightbulb-o'              , 'exchange',
	'cloud-download'           , 'cloud-upload'             , 'user-md'                  , 'stethoscope',
	'suitcase'                 , 'bell-o'                   , 'coffee'                   , 'cutlery',
	'file-text-o'              , 'building-o'               , 'hospital-o'               , 'ambulance',
	'medkit'                   , 'fighter-jet'              , 'beer'                     , 'h-square',
	'plus-square'              , 'angle-double-left'        , 'angle-double-right'       , 'angle-double-up',
	'angle-double-down'        , 'angle-left'               , 'angle-right'              , 'angle-up',
	'angle-down'               , 'desktop'                  , 'laptop'                   , 'tablet',
	'mobile-phone'             , 'mobile'                   , 'circle-o'                 , 'quote-left',
	'quote-right'              , 'spinner'                  , 'circle'                   , 'mail-reply',
	'reply'                    , 'github-alt'               , 'folder-o'                 , 'folder-open-o',
	'smile-o'                  , 'frown-o'                  , 'meh-o'                    , 'gamepad',
	'keyboard-o'               , 'flag-o'                   , 'flag-checkered'           , 'terminal',
	'code'                     , 'mail-reply-all'           , 'reply-all'                , 'star-half-empty',
	'star-half-full'           , 'star-half-o'              , 'location-arrow'           , 'crop',
	'code-fork'                , 'unlink'                   , 'chain-broken'             , 'question',
	'info'                     , 'exclamation'              , 'superscript'              , 'subscript',
	'eraser'                   , 'puzzle-piece'             , 'microphone'               , 'microphone-slash',
	'shield'                   , 'calendar-o'               , 'fire-extinguisher'        , 'rocket',
	'maxcdn'                   , 'chevron-circle-left'      , 'chevron-circle-right'     , 'chevron-circle-up',
	'chevron-circle-down'      , 'html5'                    , 'css3'                     , 'anchor',
	'unlock-alt'               , 'bullseye'                 , 'ellipsis-h'               , 'ellipsis-v',
	'rss-square'               , 'play-circle'              , 'ticket'                   , 'minus-square',
	'minus-square-o'           , 'level-up'                 , 'level-down'               , 'check-square',
	'pencil-square'            , 'external-link-square'     , 'share-square'             , 'compass',
	'toggle-down'              , 'caret-square-o-down'      , 'toggle-up'                , 'caret-square-o-up',
	'toggle-right'             , 'caret-square-o-right'     , 'euro'                     , 'eur',
	'gbp'                      , 'dollar'                   , 'usd'                      , 'rupee',
	'inr'                      , 'cny'                      , 'rmb'                      , 'yen',
	'jpy'                      , 'ruble'                    , 'rouble'                   , 'rub',
	'won'                      , 'krw'                      , 'bitcoin'                  , 'btc',
	'file'                     , 'file-text'                , 'sort-alpha-asc'           , 'sort-alpha-desc',
	'sort-amount-asc'          , 'sort-amount-desc'         , 'sort-numeric-asc'         , 'sort-numeric-desc',
	'thumbs-up'                , 'thumbs-down'              , 'youtube-square'           , 'youtube',
	'xing'                     , 'xing-square'              , 'youtube-play'             , 'dropbox',
	'stack-overflow'           , 'instagram'                , 'flickr'                   , 'adn',
	'bitbucket'                , 'bitbucket-square'         , 'tumblr'                   , 'tumblr-square',
	'long-arrow-down'          , 'long-arrow-up'            , 'long-arrow-left'          , 'long-arrow-right',
	'apple'                    , 'windows'                  , 'android'                  , 'linux',
	'dribbble'                 , 'skype'                    , 'foursquare'               , 'trello',
	'female'                   , 'male'                     , 'gittip'                   , 'gratipay',
	'sun-o'                    , 'moon-o'                   , 'archive'                  , 'bug',
	'vk'                       , 'weibo'                    , 'renren'                   , 'pagelines',
	'stack-exchange'           , 'arrow-circle-o-right'     , 'arrow-circle-o-left'      , 'toggle-left',
	'caret-square-o-left'      , 'dot-circle-o'             , 'wheelchair'               , 'vimeo-square',
	'turkish-lira'             , 'try'                      , 'plus-square-o'            , 'space-shuttle',
	'slack'                    , 'envelope-square'          , 'wordpress'                , 'openid',
	'institution'              , 'bank'                     , 'university'               , 'mortar-board',
	'graduation-cap'           , 'yahoo'                    , 'google'                   , 'reddit',
	'reddit-square'            , 'stumbleupon-circle'       , 'stumbleupon'              , 'delicious',
	'digg'                     , 'pied-piper'               , 'pied-piper-alt'           , 'drupal',
	'joomla'                   , 'language'                 , 'fax'                      , 'building',
	'child'                    , 'paw'                      , 'spoon'                    , 'cube',
	'cubes'                    , 'behance'                  , 'behance-square'           , 'steam',
	'steam-square'             , 'recycle'                  , 'automobile'               , 'car',
	'cab'                      , 'taxi'                     , 'tree'                     , 'spotify',
	'deviantart'               , 'soundcloud'               , 'database'                 , 'file-pdf-o',
	'file-word-o'              , 'file-excel-o'             , 'file-powerpoint-o'        , 'file-photo-o',
	'file-picture-o'           , 'file-image-o'             , 'file-zip-o'               , 'file-archive-o',
	'file-sound-o'             , 'file-audio-o'             , 'file-movie-o'             , 'file-video-o',
	'file-code-o'              , 'vine'                     , 'codepen'                  , 'jsfiddle',
	'life-bouy'                , 'life-buoy'                , 'life-saver'               , 'support',
	'life-ring'                , 'circle-o-notch'           , 'ra'                       , 'rebel',
	'ge'                       , 'empire'                   , 'git-square'               , 'git',
	'y-combinator-square'      , 'yc-square'                , 'hacker-news'              , 'tencent-weibo',
	'qq'                       , 'wechat'                   , 'weixin'                   , 'send',
	'paper-plane'              , 'send-o'                   , 'paper-plane-o'            , 'history',
	'circle-thin'              , 'header'                   , 'paragraph'                , 'sliders',
	'share-alt'                , 'share-alt-square'         , 'bomb'                     , 'soccer-ball-o',
	'futbol-o'                 , 'tty'                      , 'binoculars'               , 'plug',
	'slideshare'               , 'twitch'                   , 'yelp'                     , 'newspaper-o',
	'wifi'                     , 'calculator'               , 'paypal'                   , 'google-wallet',
	'cc-visa'                  , 'cc-mastercard'            , 'cc-discover'              , 'cc-amex',
	'cc-paypal'                , 'cc-stripe'                , 'bell-slash'               , 'bell-slash-o',
	'trash'                    , 'copyright'                , 'at'                       , 'eyedropper',
	'paint-brush'              , 'birthday-cake'            , 'area-chart'               , 'pie-chart',
	'line-chart'               , 'lastfm'                   , 'lastfm-square'            , 'toggle-off',
	'toggle-on'                , 'bicycle'                  , 'bus'                      , 'ioxhost',
	'angellist'                , 'cc'                       , 'shekel'                   , 'sheqel',
	'ils'                      , 'meanpath'                 , 'buysellads'               , 'connectdevelop',
	'dashcube'                 , 'forumbee'                 , 'leanpub'                  , 'sellsy',
	'shirtsinbulk'             , 'simplybuilt'              , 'skyatlas'                 , 'cart-plus',
	'cart-arrow-down'          , 'diamond'                  , 'ship'                     , 'user-secret',
	'motorcycle'               , 'street-view'              , 'heartbeat'                , 'venus',
	'mars'                     , 'mercury'                  , 'intersex'                 , 'transgender',
	'transgender-alt'          , 'venus-double'             , 'mars-double'              , 'venus-mars',
	'mars-stroke'              , 'mars-stroke-v'            , 'mars-stroke-h'            , 'neuter',
	'genderless'               , 'facebook-official'        , 'pinterest-p'              , 'whatsapp',
	'server'                   , 'user-plus'                , 'user-times'               , 'hotel',
	'bed'                      , 'viacoin'                  , 'train'                    , 'subway',
	'medium'                   , 'yc'                       , 'y-combinator'             , 'optin-monster',
	'opencart'                 , 'expeditedssl'             , 'battery-4'                , 'battery-full',
	'battery-3'                , 'battery-three-quarters'   , 'battery-2'                , 'battery-half',
	'battery-1'                , 'battery-quarter'          , 'battery-0'                , 'battery-empty',
	'mouse-pointer'            , 'i-cursor'                 , 'object-group'             , 'object-ungroup',
	'sticky-note'              , 'sticky-note-o'            , 'cc-jcb'                   , 'cc-diners-club',
	'clone'                    , 'balance-scale'            , 'hourglass-o'              , 'hourglass-1',
	'hourglass-start'          , 'hourglass-2'              , 'hourglass-half'           , 'hourglass-3',
	'hourglass-end'            , 'hourglass'                , 'hand-grab-o'              , 'hand-rock-o',
	'hand-stop-o'              , 'hand-paper-o'             , 'hand-scissors-o'          , 'hand-lizard-o',
	'hand-spock-o'             , 'hand-pointer-o'           , 'hand-peace-o'             , 'trademark',
	'registered'               , 'creative-commons'         , 'gg'                       , 'gg-circle',
	'tripadvisor'              , 'odnoklassniki'            , 'odnoklassniki-square'     , 'get-pocket',
	'wikipedia-w'              , 'safari'                   , 'chrome'                   , 'firefox',
	'opera'                    , 'internet-explorer'        , 'tv'                       , 'television',
	'contao'                   , '500px'                    , 'amazon'                   , 'calendar-plus-o',
	'calendar-minus-o'         , 'calendar-times-o'         , 'calendar-check-o'         , 'industry',
	'map-pin'                  , 'map-signs'                , 'map-o'                    , 'map',
	'commenting'               , 'commenting-o'             , 'houzz'                    , 'vimeo',
	'black-tie'                , 'fonticons'                , 'reddit-alien'             , 'edge',
	'credit-card-alt'          , 'codiepie'                 , 'modx'                     , 'fort-awesome',
	'usb'                      , 'product-hunt'             , 'mixcloud'                 , 'scribd',
	'pause-circle'             , 'pause-circle-o'           , 'stop-circle'              , 'stop-circle-o',
	'shopping-bag'             , 'shopping-basket'          , 'hashtag'                  , 'bluetooth',
	'bluetooth-b'              , 'percent'
	);

	var $ic = $('<div>',{'class':'full-screen ic-selector','id':'ic-selector'});
	var $content =  $('<div>',{'class':'content'}).css('padding',0).appendTo($ic);
	
	var $closeBtn = $('<div/>').addClass("ic-close-btn").appendTo($ic)
	.append($('<i/>').addClass("fa fa-times-circle fa-2x"))
	.on("click",function(){
		$ic.hide();
		$("html,body").css("overflow","");
	});
	
	for(var i in icons)
	{
		var icName = icons[i].toString();
		
		var $Item = $('<div>',{'class':'ic-item'}).attr('ic-name',icName);
	
		$('<i>',{'class':'fa fa-' + icName }).appendTo($Item);
		
		$('<span>',{'class':'name'}).html(icName).appendTo($Item);
		
		$Item.appendTo($content);
	}
	
	$ic.appendTo('body').show();	
}
function thumb(e, t) {
    var a = e.split('.'),
        n = a.pop();
    return n = n === a ? '' : '.' + n,
    a.join('.') + '-' + t + n
}
function selectIcon(btn){

    ic(function(ic,el) {
        $(btn).closest('.icon-append').find('input[type="hidden"]').val(ic);
        $(btn).closest('.icon-append').find('[data-icon-view]').attr('class','fa fa-3x fa-'+ic);
        //$('#cat-icon').val(ic);
        //$('#cat-icon-view').attr('class','fa fa-3x fa-'+ic);
    });
}

formatMoney = function(number){
    return Math.max(0, number).toFixed(0).replace(/(?=(?:\d{3})+$)(?!^)/g, ',');
};
