$(document).ready(function(e) {
	
	$.ajaxSetup({
		beforeSend: function(jqXHR, settings) {
			var csrf = readCookie('csrf_cookie'); 
			/*
			console.log('b:'+settings.data);
			
			if( typeof(settings.data) != 'undefined' && settings.data.split('=').length > 2)
			settings.data += "&csrf_token="+csrf;
			else
			settings.data = "csrf_token="+csrf;

			console.log('a:'+settings.data);*/
		}
	});
	
	$(document).on("click","html",function(e) {
        if(!$(e.target).closest('.bell').length && !$(e.target).closest('.project ').length){
			toogle_p(0,50);
		}
    });
	
	tooltip();
	
    $(document).on('keydown','.OnlyNum', function (e) {
		var key = e.charCode || e.keyCode || 0;
		return (
			key == 8 || 
			key == 9 ||
			key == 13 ||
			key == 46 ||
			key == 110 ||
			key == 190 ||
			(key >= 35 && key <= 40) ||
			(key >= 48 && key <= 57) ||
			(key >= 96 && key <= 105));
	});
	
	$(document).on("click",".checkbox",function(){
		
		if($(this).hasClass('checked'))
		$(this).uncheck();
		else
		$(this).check();
		
	});	
	
	$(document).on("mouseover",".header-op li",function(){
		$(this).find('.w-icon:first').addClass('hover');
	}).on("mouseleave",".header-op li",function(){
		$(this).find('.w-icon:first').removeClass('hover');
	});
	
	$(document).on("click",".fail,.success,.danger",function(){
		$(this).fadeOut(2000,function(){$(this).remove();});
	})
	
	
	$(document).on("click",".bell",function(){
		toogle_p();
	});

    $(document).on("mouseover",".captcha-img",function(){
        var el = this;
        var editable = $('<span/>').addClass('cap-editor');
        $(el).wrap(editable);
        $('<i/>').addClass('fa fa-refresh fa-lg cu').on("click",function(){
            refresh_captcha(el);
        }).insertAfter(el);

    }).on("mouseleave",".cap-editor",function(){
        var el = this;
        $(el).replaceWith($(el).find('img'));
    });
	
	
	updateOnlines();
	updateTime();
	
});


function Merge(obj1, obj2) {

  for (var p in obj2) {
    try {
      if ( obj2[p].constructor==Object ) {
        obj1[p] = MergeRecursive(obj1[p], obj2[p]);
      } else {
        obj1[p] = obj2[p];
      }
    } catch(e) {
      obj1[p] = obj2[p];
    }
  }
  return obj1;
}



var p_queue = 0;
function toogle_p(key,speed){
			
	if(p_queue) return;
	p_queue = 1;
	
	var p = $('.project')[0];
	var items;
	
	if(typeof(speed)=='undefined')
	var speed = 100;
	
	if($(p).is(':visible') || key==0){
		
		function b(el){
			$(el).animate({marginLeft:-250},speed,function(){
				if($(this).prev().length)
				b($(this).prev());
				else
				$(p).slideUp(400,function(){p_queue = 0});
			});
		}
		items = $(p).find('.anim-item:last');			
		b(items);
		
	}else if(!$(p).is(':visible') || key==1){
		
		items = $(p).find('.anim-item:first');
		$(p).slideDown(400,function(){
			function a(el){
				$(el).animate({marginLeft:0},speed,function(){
					if($(this).next().length)
					a($(this).next());
					else
					p_queue = 0;
				});
			}
			$(this).addClass('active');
			a(items);			
		});	
	}
	
}

function dialog_box(options){
	
	if(typeof(options)=="string")
	options = {body:options};
	
	var settings = $.extend({},{
			id        : '', 
			name      : 'اعلان',
			body      : '',
			style     : {},
			footer    : true,
			buttonVal : 'بستن',
			buttons   : $('<button/>').html(options.buttonVal || 'بستن'),
			onClose   : null,
			onSubmit  : null
		},options);
	
	var style = $.extend({
			width   : 'auto',
			height  : 'auto',
			padding : '20px 40px'
		},settings.style); 
	
	var existDialog = $('#'+(settings.id));
	if(existDialog.length){
		$(existDialog).fadeTo(100,.8,function(){
			$(this).fadeTo(100,1,function(){
				$(this).fadeTo(100,.8,function(){
					$(this).fadeTo(100,1);
				});
			});
		});
		return;
	}
	
	var $dialog = $('<div/>').addClass("dialog active");
	var $header = $('<div/>').addClass("dialog-header").html(settings.name);
	var $body   = $('<div/>').addClass("dialog-body").css(style).html(settings.body);
	
	if(settings.id)
	$dialog.attr('id',settings.id);
	
	$dialog.append($header);
	$dialog.append($body);
	
	if(settings.footer){
		
		$footer = $('<div/>').addClass("dialog-footer").append(settings.buttons);
		$dialog.append($footer);
		
	}else{
		
		$('<div/>').addClass('hr').css("margin-bottom",0).appendTo($dialog);
		$('<div/>').css({"padding":5}).append(settings.buttons).appendTo($dialog);
	}
	
	$('.dialog').removeClass("active");
	
	var $other = $('.dialog');
	$('body').append($dialog);
	
	var w  = $dialog.width(), h = $dialog.height() ,
		ww = $(window).width(),wh = $(window).height(),
		left = (ww/2)-(w/2),top = (wh/2)-(h/2);
		
	$dialog.css({'z-index':(Hz())+1,'left':left,'top':top});
	
	$dialog.draggable({
		
		containment:'window',
		scroll: false,
		handle:'.dialog-header,.dialog-footer'
		
	}).on("mousedown",function(){
		
		$('.dialog').removeClass("active");
		$(this).addClass("active");
		
		var hz = Hz(),zx = parseInt($(this).css("z-index"),10);
		if(zx!=hz)
		$(this).css("z-index",hz+1);
		
	}).find('button').on("click",function(e){
		
		if($(this).attr("close")!="0"){
			
			$dialog.fadeOut("fast",function(){$dialog.remove()});
			if(typeof(settings.onClose)=='function')
			settings.onClose(e,this);
		}
		
		if($(this).attr("action")=="submit" && typeof(settings.onSubmit)=='function')
		settings.onSubmit(e,this);
		
	});
	
}

function Hz(){
	var index_highest = 0;   
	$("body *").not('.tooltip').each(function(index ,el) {
		{
			var index_current = parseInt($(this).css("zIndex"), 10);
			if(index_current > index_highest)
			index_highest = index_current;
		}
	});
	return index_highest;
}
function Alert(el,delay,rep){
	
	var i = 0;
	rep *= 2;
	
	var int = setInterval(function(){
		
		if(i == rep)
		return;
		
		if(i%2 == 1)
		$(el).addClass('cant-empty');
		else
		$(el).removeClass('cant-empty');

		i++;
	},delay);
	
}
function ajaxLoading(key,text,btn){
	var span = $(".ajax-result");
	
	if(typeof(key)=='undefined')
	var key = true;
	
	if(typeof(text)=='undefined')
	var text = '';
	
	if(typeof(btn)!='undefined')
	span = $(btn).parent().find(".ajax-result");
	
	var pr = String(location.pathname).match("panel") ? '../':'';
	
	if(key)
	$(span).html('<img src="'+pr+'style/images/loader.gif" style="vertical-align: middle;" width="20px"> '+text);
	else
	$(span).html('');
}

function ajax_fail(btn){
	var $body = $('<p/>').attr("align","center").html(' خطا در اتصال به سرور ');
	
	var options = {
			name  : 'اتصال برقرار نیست',
			body  : $body ,
			style : {padding:'20px 50px'}
		};
	dialog_box(options);
	
	if(btn)
	ajaxLoading(0,'',btn);
}

function setGoTopButton(){
	var btn = '<div title="رفتن به بالا" class="go-top-btn" onClick="$(\'html,body\').animate({scrollTop:0},500)"></div>';
	$(btn).appendTo("body");
	$(window).scroll(function(e) {
        if($(this).scrollTop()>100)
		$('.go-top-btn').fadeIn();
		else
		$('.go-top-btn').fadeOut();
    });
}

var hoverTimeOut;
function tooltip(){
	$('[title]').each(function()
    {
        var title = $(this).attr('title');
        $(this).attr('data-title', title).removeAttr('title');
    });
	
	var div = 
	'<div class="tooltip yekan f13" style="display:none" dir="auto">\
      <div style="position:relative" class="black">\
        <div class="text"></div>\
        <span class="corner"></span>\
      </div>\
    </div>';
	$("body").append(div);
	$(document).mousemove(function(e) {
		$('.tooltip:visible').hide();
		
		var ww = $(window).width(),wh = $(window).height(),st = $(window).scrollTop(),
			t = e.pageY-st<100 ? e.pageY+30:'auto',
			b = e.pageY-st<100 ? 'auto':wh - e.pageY+10,
			cls1 =  e.pageY-st<100 ? '-t-':'-b-',
			cls2 =  e.pageX < ww/2 ? 'l':'r',
			cls = "c"+cls1+cls2;
		if(e.pageX < ww/2)
		$('.tooltip').css({"top":t,"bottom":b,"left":e.pageX-13,"right":""});
		else
		$('.tooltip').css({"top":t,"bottom":b,"right":ww-e.pageX-13,"left":""});
		$('.tooltip .corner').removeClass('c-b-r c-t-r c-t-l c-b-l').addClass(cls);
		
    });
	$(document).on("mouseover","[data-title]",function(e) {

		var el = this;
		clearTimeout(hoverTimeOut);
		hoverTimeOut = setTimeout(function(){
			var t = $.trim($(el).attr("data-title"));
			if( t != ""){
				$('.tooltip .text').html(t);
				$('.tooltip').show();
			}
		},500);

    }).on("mouseleave click","[data-title]",function(){
		clearTimeout(hoverTimeOut);	
		$('.tooltip:visible').hide();
	});
	
}

function tooltip_destroy(){
	
	$('[data-title]').each(function(){
		
        var title = $(this).attr('data-title');
        $(this).attr('title', title).removeAttr('data-title');
    });
	$('.tooltip').remove();
}


function tooltip_refresh(){
	tooltip_destroy();
	tooltip();
}

$.fn.check = function(){
	this.each(function(index, el) {
		$(el).addClass("checked").fadeTo(150,0,function(){
			
			$(el).html("&#9745;").fadeTo(150,1);
			
		})
	});		
}; 
$.fn.uncheck = function(){
	
	this.each(function(index, el) {
		$(el).removeClass("checked").fadeTo(150,0,function(){
			
			$(el).html("&#9744;").fadeTo(150,1);
			
		})
	});		
};


function setImageViewer(){
	
	var viewer =
	'<div class="image-viewer">\
		<table width="100%" height="100%" cellpadding="0" cellspacing="0">\
		  <tr align="center" valign="middle"  width="100%" height="100%">\
			<td width="100%" height="100%" align="center" valign="middle">\
			  <div class="image">\
			  </div>\
			</td>\
		  </tr>\
		</table>\
		<div class="close-viewer" onClick="$(this).closest(\'.image-viewer\').fadeOut()"></div>\
		<div class="image-viewer-options">\
		  <div class="image-viewer-name"><div></div></div>\
		  <div class="image-viewer-size"></div>\
		  <div class="image-viewer-prev"> &laquo; </div>\
		  <div class="image-viewer-next"> &raquo; </div>\
		  <div class="image-viewer-info"></div>\
		  <div class="image-viewer-ajax"></div>\
		</div>\
	 </div>\
	 <div class="loader"></div>';
	 
	if(!$('.image-viewer').length){
		
		$("body").append(viewer);
	}
	
	$('.image-viewer-use').remove();
		
	$(document).on("click",".file .file-image",function(){
		
		$('.image-viewer-ajax').html('');
		$('.image-viewer .image').css({"width":"","height":""});
		
		var el = this,index;
		
		index = $(el).closest('.file').index();
		
		showImage(index,'.file');
				
	});
	
	$(document).on("click",".image-viewer-ajax",function(){
		
		$(this).html('');
		
	});	
	
}
var imageViewerImg = new Image();

function showImage(index,files){
	
	var imageSrc, el, originalPath, fileName, total,thumb,
	prevBtn = $(".image-viewer-prev"),
	nextBtn = $(".image-viewer-next"),
	info    = $(".image-viewer-info");
	
	files = $(files);
	
	$(files)[index];
	
	el = $(files)[index];
	total = $(files).length;
	
	var file = $(el).closest(".file");
	originalPath = $(file).attr("file-path");
	thumb = $(file).attr("file-thumb-600");
	
	$(".image-viewer-name div").html($(file).attr("file-name")).attr("data-title",$(file).attr("file-name")); 
	$(".image-viewer-size").html($(file).attr("file-size")); 
	$('.ivts-side').data('path',originalPath);
			
	
	//imageSrc = 'includes/thumb.php?op=max&max=600&img=../'+originalPath;
	imageSrc = thumb;	
		
	if(index == 0)
	$(prevBtn).css("color","#ccc").attr("onclick","");
	else
	$(prevBtn).css("color","").attr("onclick","showImage("+(index-1)+",'.file')");
	
	if(index+1 == total)
	$(nextBtn).css("color","#ccc").attr("onclick","");
	else
	$(nextBtn).css("color","").attr("onclick","showImage("+(index+1)+",'.file')");
	
	
	$(info).html((index+1)+'/'+total);
	
	$('.loader')
	.css({
		"left":$(window).width()/2-20,
		"top":$(window).height()/2-20
	})
	.add('.image-viewer:hidden')
	.fadeIn("fast");
	
	$('.image-viewer .image').html('');
	
	$(imageViewerImg).unbind("load");
	imageViewerImg.src = imageSrc;
	$(imageViewerImg).data({"src":imageSrc,"path":originalPath});
	
	$(imageViewerImg).load(function(e) {
		
		if($(this).data('src') != imageSrc) return false;
		
		$('.loader').fadeOut("fast");
		
		var iw = imageViewerImg.width,
			ih = imageViewerImg.height,
			ww = $(window).width()-75,
			wh = $(window).height()-75;
		
		if(iw>=ww || ih>=wh){
			
			if(iw>=ww){
				
				ih = (ih*ww)/iw;
				iw = ww;
				
			}else{
				iw = (iw*wh)/ih;
				ih = wh;
			}
		}
		
		$(imageViewerImg).css({"width":iw,"height":ih,"border":"solid 1px #ddd"});
		
		var a = $('<a/>').attr({"href":originalPath,"target":"_blank"});
		
		$(a).html(imageViewerImg);
		
		if($('.image-viewer .image').width() != iw || $('.image-viewer .image').height() != ih)
		$('.image-viewer .image')
		.animate({height:ih},300,function(){
			
			$(this).animate({width:iw},300,function(){
				$(this).html(a);
			});
			
		});
		else
		$('.image-viewer .image').html(a);
		
	}).error(function(){
		$('.loader').fadeOut("fast");
		$('.image-viewer .image span')
		.html('<span class="fail" style="margin:130px auto">خطا در بارگذاری تصویر</span>');
	});
		
} 

function login(callback){
	
	if($('.jlogin-cover').length) return;
	
	var $cover = $('<div/>').addClass('cover jlogin-cover').css("z-index",(Hz())+3);
	$('html,body').css("overflow","hidden");
	$('body').append($cover);
	
	
	var $button = $('<button/>').attr({"action":"submit","close":0}).html('ورود');
	
	var $btndiv = $('<div/>').append($button).append($('<span/>').addClass('ajax-result'));
	
	var $form = $('<form/>').addClass('jlogin').append("<p>لطفا دوباره وارد شوید</p>");
	
	var $loginInp = 
	$('<input/>').attr({'type':'text','class':'input ar','placeholder':'شناسه','name':'username','value':''})
	.css({"display":"block","width":200,"margin-bottom":7}).appendTo($form);
	
	
	var $passInp = 
	$('<input/>').attr({'type':'password','class':'input','placeholder':'گذرواژه','name':'password','value':''})
	.css({"display":"block","width":200,"margin-bottom":7}).appendTo($form);
	
	$('<label/>').append($('<input/>').attr({'type':'checkbox','name':'stay','value':'true','class':'vm'}))
	.append("مرا به خاطر بسپار").appendTo($form);
	
	
	var onSubmit = function(e,btn){
		
		if( $.trim($loginInp.val()) == "")
		{
			$loginInp.focus();
			return;
		}
		if( $.trim($passInp.val()) == "")
		{
			$passInp.focus();
			return;
		}		
		
		$(btn).parent().addClass('l gray');
		
		var data = $('.jlogin').serialize();
		
		$.ajax({
			url:URL+"/login",
			type:"POST",
			data:data,
			dataType:"json",
			success: function(data)
			{
				$(btn).parent().removeClass('l gray');
				if(data.done == 1)
				{
					$(btn).attr({'close':1,'action':''}).trigger("click");
					$cover.remove();
					$('html,body').css("overflow","");
					if(typeof(callback)=='function')
					callback.call();
				}
				$(btn).next().html(data.msg);
			}
		});		
	}
	
	var options = {
			name      : 'ورود به سیستم',
			body      : $form ,
			style     : {padding:'0 40px 15px 40px'},
			buttons   : $btndiv,
			onSubmit  : onSubmit		
	};
	dialog_box(options);
}

var select_menu_bind = false;

function selectMenu(el,icon){
	var id = 0;
	function create_menu(sel){
		
		//var selected = $(sel).find(':selected');
		var div = jQuery('<div/>', {class : 'select-menu'})
		.append($('<i/>').addClass('fa fa-'+icon));
		var span = jQuery('<span/>', {class : 'select-menu-span',text:$(sel).val()});	
		var ul = jQuery('<ul/>');	
		$(sel).find('option').each(function(index, op) {
			
			var li = jQuery('<li/>',{
				text: $(op).text(),
				value : $(op).attr('value'),
				class : $(op).is(":disabled")?'disable-op':'enable-op'
			}).appendTo(ul);
        });
		$(span).appendTo(div);
		$('<i/>').addClass('fa fa-sort-desc').appendTo(div);
		$(ul).appendTo(div);
		$(sel).attr('sel-num','sel-num-'+id).css("display","none");
		$(div).attr('sel-num','sel-num-'+id).insertBefore(sel);
		id ++;
	}
	
	create_menu(el);		
	
	if( select_menu_bind  ) return;

	select_menu_bind = true;
	
	$(document).on("click","body",function(e){
		var el = e.target;
		if($(el).hasClass('select-menu')){
			$('.select-menu:visible').not(el).find('ul').hide();
			$(el).find("ul").toggle();
		}else if($(el).hasClass('select-menu-span')){
			var div = $(el).parent();
			$('.select-menu:visible').not(div).find('ul').hide();
			$(el).parent().find("ul").toggle();
		}else
		$('.select-menu').find("ul").hide();
	});
	
	$(document).on("click",".select-menu > ul > li",function(e){
		if($(this).hasClass('disable-op')){
			e.preventDefault();
			return false;
		}	
		var div = $(this).closest('.select-menu');
		var text = $(this).text();
		var val = $(this).attr("value");
		$(div).find('span').html(text);
		var id = $(div).attr('sel-num')
		$('select[sel-num="'+id+'"]').val(val).trigger("change");
	});	
}

function ByteToSize(bytes) {
   if(bytes == 0) return '0 Byte';
   var k = 1024;
   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
   var i = Math.floor(Math.log(bytes) / Math.log(k));
   return (bytes / Math.pow(k, i)).toPrecision(3) + ' ' + sizes[i];
}

function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
 }

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
 }

function eraseCookie(name) {
    createCookie(name,"",-1);
}

function strTime() {
	return Math.round(new Date().getTime() / 1000);
}
/***************************/

var updateOnlinesT = false;
function updateOnlines() {

	/*$.ajax({
		url: BURL+"api/onlines",
		type:"POST",
		data:{},
		dataType:"json",
		success: function(data){
			//console.log(data);
			clearTimeout(updateOnlinesT);
			updateOnlinesT = setTimeout(updateOnlines,60*1000);
		},
		error: function (data,xhr){
			clearTimeout(updateOnlinesT);
			updateOnlinesT = setTimeout(updateOnlines,10*1000)
		} 
	});*/
}

var updateTimeInterval = false;
function updateTime(){
	function a(){
		var date =  new Date(),
		Ctime = Math.floor(date.getTime()/1000),
		Coffset = date.getTimezoneOffset()*(-60),
		client = Ctime - Coffset;
		
		$('.relative-date').each(function(index, el) {
			
			var time = client - $(el).attr('datestr');			
			if(time>=0 && time <86400)
			{
				if(time>=0 && time <60)
				re = 'لحظاتی قبل';
				else
				if(time>=60 && time<3600)
				{
					time = Math.floor(time/60);
					re = time+' دقیقه';	
				}else
				if(time>=3600 && time<86400)
				{
					var h = Math.floor(time/3600);
					re = h+' ساعت ';
				}
				$(el).text(re);
			}else{
				$(el).text($(el).attr('date'));
				$(el).removeClass("relative-date");
			}
		});		
	}
	a();
	clearInterval(updateTimeInterval);
	updateTimeInterval= setInterval(a,15000);
}

Array.prototype.keySort = function(keys) {

	keys = keys || {};
	
	var obLen = function(obj) {
		var size = 0, key;
		for (key in obj) {
			if (obj.hasOwnProperty(key))
				size++;
		}
		return size;
	};
	
	var obIx = function(obj, ix) {
		var size = 0, key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				if (size == ix)
					return key;
				size++;
			}
		}
		return false;
	};
	
	var keySort = function(a, b, d) {
		d = d !== null ? d : 1;
		// a = a.toLowerCase(); // this breaks numbers
		// b = b.toLowerCase();
		if (a == b)
			return 0;
		return a > b ? 1 * d : -1 * d;
	};
	
	var KL = obLen(keys);
	
	if (!KL)
		return this.sort(keySort);
	
	for ( var k in keys) {
		keys[k] = 
				keys[k] == 'desc' || keys[k] == -1  ? -1 
			  : (keys[k] == 'skip' || keys[k] === 0 ? 0 
			  : 1);
	}
	
	this.sort(function(a, b) {
		var sorted = 0, ix = 0;
	
		while (sorted === 0 && ix < KL) {
			var k = obIx(keys, ix);
			if (k) {
				var dir = keys[k];
				sorted = keySort(a[k], b[k], dir);
				ix++;
			}
		}
		return sorted;
	});
	return this;
}
function notify(text,status,delay)
{
    if( ! text ) return;
    if( ! delay ) delay = 4000;

    var Class;
    switch( status )
    {
        case 0 : Class = "success"; break;
        case 1 : Class = "danger";  break;
        case 2 : Class = "warning"; break;
    }

    function show(txt,dly)
    {
        if( ! $('.notify-par').length )
        {
            var $p = $('<div>',{'class':'notify-par'}),
                $n = $('<div>',{'class':'notify fade'}).html(txt);
            $p.append($n).appendTo('body');
            setTimeout(function(){
                $n.addClass('in');
            },100);
        }
        else
        {
            var $n = $('.notify');
            $n.html(txt).addClass('in');
        }

        $n.removeClass('success danger warning').addClass(Class);

        setTimeout(function(){
            $n.removeClass('in');
        },dly);
    }
    show(text,delay);
}
function get_alert(data){

    var Class;
    switch( data.status )
    {
        case 0 : Class = "success"; break;
        case 1 : Class = "danger";  break;
        case 2 : Class = "warning"; break;
    }
    var $res   = $('<div/>',{'class':"alert alert-dismissible alert-" + Class})
        .css('margin',"10px 0 0")
        .append(data.msg);

    var $close = $('<button/>',{type:'button','class':'close'})
        .attr('data-dismiss','alert')
        .attr('aria-label','Close')
        .prependTo($res);

    $('<span>').attr('aria-hidden','true').html('&times;').appendTo($close);
	console.error(data);
    return $res;
}

function refresh_captcha(img)
{
    var src = $(img).attr('src').split('?');
    src = src[0] +'?t='+ new Date().getTime();
    $(img).attr('src',src);
}