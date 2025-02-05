	$(document).ready(function(){
		
		$(document).on('click','.delete-part',function(){
			
			var btn = $(this);
			
			if(btn.hasClass('l')) return;
			
			var parts = $('.book-part') , part = $(this).closest('.book-part');
			
			if(!confirm('این پاراگراف حذف شود ؟')) return;
	
			
			function a(){
				if(parts.length > 1) {
					$(part).slideUp(400,function(){
						$(this).remove();
						setPages();
						setOrders();
					});
				} else {
					$(part).removeClass('has-description full-grid has-sound has-image has-index').removeAttr('data-id');
					$(part).find('.form-control,input').val('');
					$(part).find('.part-sound audio').remove();
					$(part).find('.part-image img').remove();
					setPages();
					setOrders();
				}			
			}
			
			if($(part).is('[data-id]'))
			{
				btn.addClass('l red');
				
				$.ajax({
					type    : "POST",
					url     : URL + "/delete_part/" + $(part).attr('data-id'),
					data    : {} ,
					//dataType    : 'json', 
					success     : function (data) {
					
						btn.removeClass('l red');
	
						if(data == 'login')
						{
							login(function(){
								btn.trigger('click');
							});
						}else a();
					},
					error: function (a,b,c) {
						btn.removeClass('l red');
					}
				});
			} else a();
		});
	
	
		$(document).on('click','.index-ul li:not(.disable)',function(){
			var f = $(this).closest('.full-screen') , part = $(f).data('part'), btn = this;
	
			if($(btn).hasClass('selected'))
			{
				$(btn).attr('data-part-id','').data('part','').removeClass('selected');
				$(part).removeClass('has-index').find('.part-index').val('');
				$(part).find('.add-index').attr('data-title','متصل کرد به فهرست');
			}
			else
			{
				$('.index-ul li.selected').attr('data-part-id','').data('part','').removeClass('selected');
				$(btn).attr('data-part-id',$(part).attr('data-id')).data('part',part).addClass('selected');
				$(part).addClass('has-index').find('.part-index').val($(btn).attr('data-id'));
				$(part).find('.add-index').attr('data-title',$(btn).text());
				//$(f).hide();
			}
			$('select.index-select').prop('disabled',$('.index-ul li.selected,.index-ul li.disable').length > 0);
			checkPartChanged(part);
		});
		
		$(document).on('click','.index-ul li.disable',function(e){
			e.preventDefault();
			var btn  = $(this),
				f    = btn.closest('.full-screen'),
				id   = btn.attr('data-part-id'),
				part = $('.book-part[data-id="'+id+'"]');
				
			if(id == '') part = btn.data('part');	
				
			if(!part.length) return;
			
			var top  = $(part).offset().top - 100;
			
			$('html,body').animate({
				scrollTop : top,
			},function(){
				$(part).addClass('highlighted');
				setTimeout(function(){
					$(part).removeClass('highlighted');
				},2500);
			});
			$(f).hide();
		});	
	
		$(document).on('click','.add-description',function(){
			$(this).closest('.book-part').toggleClass('has-description');
			checkPartChanged($(this).closest('.book-part'));
		});
		$(document).on('click','.add-sound',function(){
			var part = $(this).closest('.book-part'), btn = this;
			if($(part).hasClass('has-sound')) {
				if(!confirm('فایل صوتی حذف شود ؟')) return;
				$(part).removeClass('has-sound');
				$(part).find('audio').remove();
				$(part).find('.part-sound input').val('');
				checkPartChanged(part);
			}else{
				media('file,1',btn,function(data,files,button){
					var originalSrc = data[0];
					var src = originalSrc.replace('/lexoya/var/www/html/', '');
					var sound = 
						'<input name="file" type="hidden" value="'+src+'">'
						+ '<i class="fa fa-volume-up toggle-sound" title="مشاهده یا پنهان کردن فایل صوتی"></i>'
						+ '<a href="'+src+'" download="'+src+'"><i class="fa fa-download" title="دریافت فایل صوتی"></i></a>';
					
					$(part).find('.part-sound').html(sound);
					$(part).addClass('has-sound');
					checkPartChanged(part);
				});
			}
		});
		$(document).on('click','.add-image',function(){
			var part = $(this).closest('.book-part'), btn = this;
			if($(part).hasClass('has-image')) {
				if(!confirm('تصویر حذف شود ؟')) return;
				$(part).removeClass('has-image');
				$(part).find('img').remove();
				$(part).find('.part-image input').val('');
				checkPartChanged(part);
			}else{
				media('image,1',btn,function(data,files,button){
					var originalSrc = data[0];
					var src = originalSrc.replace('/lexoya/var/www/html/', '');
					var image = 
						'<input name="image" type="hidden" value="'+src+'">'
						+ '<i class="fa fa-picture-o toggle-image" title="مشاهده یا پنهان کردن تصویر"></i>'
						+ '<a href="'+src+'" download="'+src+'"><i class="fa fa-download" title="دریافت تصویر"></i></a>';
					
					$(part).find('.part-image').html(image);
					$(part).addClass('has-image');
					checkPartChanged(part);
				});
			}
		});
		$(document).on('click','.part-grid',function(){
			
			var part = $(this).closest('.book-part');
			if(!$(part).hasClass('has-description')) return;
			$(part).toggleClass('full-grid');
			
		});
		$(document).on('click','.level-up',function(){
			
			var part = $(this).closest('.book-part'),
				prev = $(part).prev(),
				wt   = $(window).scrollTop(),
				ph   = $(part).outerHeight(true);
			if(!prev.length) return;
			$(window).scrollTop(wt-ph);
			setTimeout(function(){
				$('[data-title]').trigger('mouseleave');
			},100);
			$(part).insertBefore(prev);
			setPages();
			setOrders();
			
		});
		$(document).on('click','.level-down',function(){
			
			var part = $(this).closest('.book-part'),
				next = $(part).next(),
				wt   = $(window).scrollTop(),
				ph   = $(part).outerHeight(true);
			if(!next.length) return;
			$(window).scrollTop(wt+ph);
	
			setTimeout(function(){
				$('[data-title]').trigger('mouseleave');
			},100);
			$(part).insertAfter(next);
			setPages();
			setOrders();
		});
	
		$(document).on('click','.book-part .add-part',function(){
			var part = $(this).closest('.book-part'),
				wt   = $(window).scrollTop(),
				ph   = $(part).outerHeight(true);
			//var i = $('.book-part').length + 1;
			var p =
				'<form class="book-part row" style="display: none;">'
				+ '<div class="col-xs-1">'
				+ '<ul class="btn-group-vertical list-unstyled">'
				+ '<li class="btn btn-default add-index" data-title="متصل کرد به فهرست"><i class="fa fa-list"></i></li>'
				+ '<li class="btn btn-default add-sound" data-title="افزودن یا حذف صدا"><i class="fa fa-play-circle-o"></i></li>'
				+ '<li class="btn btn-default add-image" data-title="افزودن یا حذف تصویر"><i class="fa fa-picture-o"></i></li>'
				+ '<li class="btn btn-default add-description" data-title="افزودن یا حذف شرح"><i class="fa fa-comment-o"></i></li>'
				+ '<li class="btn btn-default add-part" data-title="افزودن پاراگراف"><i class="fa fa-plus-circle"></i></li>'
				+ '</ul>'
				+ '</div>'
				+ '<div class="part-content col-xs-10">'
				+ '<textarea name="text" class="form-control part-text" placeholder="متن"></textarea>'
				+ '<textarea name="description" class="form-control part-description" placeholder="شرح"></textarea>'
				+ '<div class="part-sound pull-left">'
				+ '<input name="file" type="hidden" value="">'
				+ '<i class="fa fa-volume-up toggle-sound" title="مشاهده یا پنهان کردن فایل صوتی"></i>'
				+ '<a href="#" download="#"><i class="fa fa-download" title="دریافت فایل صوتی"></i></a>'
				+ '</div>'
				+ '<div class="part-image pull-left">'
				+ '<input name="image" type="hidden" value="">'
				+ '<i class="fa fa-picture-o toggle-image" title="مشاهده یا پنهان کردن تصویر"></i>'
				+ '<a href="#" download="#"><i class="fa fa-download" title="دریافت تصویر"></i></a>'
				+ '</div>'
				+ '<label class="col-md-12">لینک ویدئو : <input name="video" class="form-control" dir="ltr" type="text"></label>'
				+ '<input name="order" class="part-order" type="hidden" value="'+($(part).index()+1)+'">'
				+ '<input name="index" class="part-index" type="hidden" value="">'
				+ '</div>'
				+ '<div class="col-xs-1">'
				+ '<ul class="btn-group-vertical list-unstyled">'
				+ '<li class="btn btn-default level-up" data-title="انتقال به بالا"><i class="fa fa-angle-up"></i></li>'
				+ '<li class="btn btn-default part-grid" data-title="تغییر نحوه نمایش"><i class="fa fa-th-list"></i></li>'
				+ '<li class="btn btn-default delete-part" data-title="حذف پاراگراف"><i class="fa fa-trash"></i></li>'
				+ '<li class="btn btn-default level-down" data-title="انتقال به پایین"><i class="fa fa-angle-down"></i></li>'
				+ '</ul>'
				+ '</div>'
				+ '<div class="book-page"></div>'
				+ '<div class="book-save" data-title="ذخیره"></div>'
				+ '</form>';
	
			var $p = $(p);
	
			$p.insertAfter(part);
			setPartData($p);
			$p.slideDown(400,function(){
				$('html,body').animate({
					scrollTop : wt+ph
				},function () {
					$p.find('.part-text').focus();
				});
			});
			
			$(part).find('.book-page.active').removeClass(function(){
				$p.find('.book-page').addClass('active');
				return 'active';
			});
			setPages();
			setOrders();
			setTimeout(function(){
				$('[data-title]').trigger('mouseleave');
			},100);
		}); 
	
		$(document).on('input','.book-part textarea,.book-part input',function(){
			checkPartChanged($(this).closest('.book-part'));
		});
		
		$(document).on('click','.book-part .toggle-sound',function(){
			var $this = $(this);
			if($this.next().next().is('audio')){
				$this.next().next().remove();
			}else{
				var part = $(this).closest('.book-part');
				var src = $(part).find('[name="file"]').val();			
				var sound = '<audio controls="">'+
							'<source src="'+src+'" type="audio/mpeg">'+
							'Your browser does not support the audio tag.'+
							'</audio>';
				$this.parent().append(sound);
			}
		});	
		$(document).on('click','.book-part .toggle-image',function(){
			var $this = $(this);
			if($this.next().next().is('img')){
				$this.next().next().remove();
			}else{
				var part = $(this).closest('.book-part');
				var src = $(part).find('[name="image"]').val();			
				var image = '<img src="'+src+'" />';
				$this.parent().append(image);
			}
		});	
		
		/*******************  PAGES *****************/
		//if($('.book-page.active').length) 
			setPages();
			
		$(document).on('click','.book-page',function(){
			$(this).toggleClass('active');
			setPages();
		});
		
		/*******************  SAVE *****************/
		$(document).on('click','.book-save',function(){
			var p = $(this).closest('.book-part');
			savePart(p);
		});	
	});

	$(window).load(function(){
		
		$('.book-part').each(function(i, el){
			setPartData(el);
		});
	});



	function saveAll(btn){
		
		var $changed = $('.book-save.changed'),
			$btn     = $(btn) ,
			$status  = $('.save-status'),
			$prg     = $('.progress'),
			$prgb    = $('.progress .progress-bar');
		
		var i = 0,
			done  = 0, 
			fails = 0,
			total = $changed.length;
		
		if(total == 0) return;
		
		$btn.addClass('l').prop('disabled',true);
		
		function updateStatus()
		{
			var percent = Math.round(i*100/total);
			var status = 
			"Total: " + total + " &nbsp; \
			<span class=text-success>Success: " +  done + "</span> &nbsp; \
			<span class=text-danger>Fail: " + fails + "</span>";
			
			$status.html(status);
			
			$prg.fadeIn();
			$prgb.css('width', percent + '%').html(percent+'%');
			
			if(percent == 100){
				$prgb.removeClass('progress-bar-striped active progress-bar-warning').addClass('progress-bar-info');
			}else{
				$prgb.addClass('progress-bar-striped active progress-bar-warning').removeClass('progress-bar-info');
			}
		}
		
		var success = function($part,data){
			done++; i++;
			next();
		}
		
		var fail = function($part){
			fails++; i++;
			next();
		}
		
		function next(){
			
			updateStatus();
			if(i == total){
				$btn.removeClass('l').prop('disabled',false);
				savePages('.save-pages-btn');
			}else{
				savePart($changed[i].closest('.book-part'),success,fail);
			}
		} 
	
		next();
	}

	function savePages(btn){
		
		var $btn = $(btn);
		
		$btn.addClass('l').prop('disabled',true);
		$('.pages-status').html('...').removeClass('text-danger text-success');
		
		$.ajax({
			type    : "POST",
			url     : URL + "/save_pages/" + postdata.id,
			data    : {
				pages : $('#book-pages').val()
			} ,
			dataType : 'json', 
			success  : function (data) {
				console.log(data);
				$btn.removeClass('l').prop('disabled',false);
				if(data == 'login'){
					login(function(){
						savePages(btn);
					});
				}else{
					$('.pages-status').html('صفحه بندی ذخیره شد').addClass('text-success');
				}
			},
			error: function (a,b,c) {
				$btn.removeClass('l').prop('disabled',false);
				$('.pages-status').html('صفحه بندی ذخیره نشد').addClass('text-danger');
			}
		});
	}

	function savePart(p, success, fail){
		
		var $p = $(p);
		
		var btn = $p.find('.book-save');
		
		if(!$p.hasClass('has-description'))
			$p.find('.part-description').val('');
		
		btn.removeClass('changed saved');
		btn.addClass('saving');
		
		
		
		var data = new FormData($p[0]);
	
		data.append('order',$p.index()); 
		
		$.ajax({
			type    : "POST",
			url     : URL + "/save_part/" + postdata.id,
			data    : data ,
			cache   : false,
			processData : false,
			contentType : false,
			dataType    : 'json', 
			success     : function (data) {
	
				console.log(data);
			
				btn.removeClass("saving");
	
				if(data == 'login')
				{
					login(function(){
						savePart(p, success, fail);
					});
				}
				else
				{
					setPartData($p);
					
					if(data.part && data.part.id){
						$p.attr('data-id',data.part.id);
						
						var idInp = $p.find('.part-id');
						
						if(idInp.length)
							idInp.val(data.part.id);
						else
							$p.find('.part-content').append('<input name="id" class="part-id" type="hidden" value="'+data.part.id+'">');
					}
					if(typeof success == 'function') success($p,data);
				}
			},
			error: function (a,b,c) {
				btn.removeClass('saving').addClass('fail');
				
				if(typeof fail == 'function') fail($p);
			}
		});
	}

function makePartChanged(el){
	
	var part;
	
	if($(el).hassClass('book-part'))
		part = $(el);
	else
		part = $(el).closest('.book-part');
	
	$(part).find('.book-save').addClass('changed');
}

function setPartData(part){
	
	var $p = $(part);
	
	var data = { 
		text        : $.trim($p.find('.part-text').val()),
		has_dsr     : $p.hasClass('has-description'),
		description : $.trim($p.find('.part-description').val()),
		file        : $p.find('[name="file"]').val(), 
		video       : $p.find('[name="video"]').val(), 
		image       : $p.find('[name="image"]').val(), 
		order       : $p.find('[name="order"]').val(), 
		index       : $p.find('[name="index"]').val()
	};
	
	$p.data(data);
}

function checkPartChanged(part){
	
	var $p = $(part), changed = false , pData = $p.data();
	
	var data = { 
		text        : $.trim($p.find('.part-text').val()),
		has_dsr     : $p.hasClass('has-description'),
		description : $.trim($p.find('.part-description').val()), 
		file        : $p.find('[name="file"]').val(), 
		video       : $p.find('[name="video"]').val(), 
		image       : $p.find('[name="image"]').val(), 
		order       : $p.find('[name="order"]').val(), 
		index       : $p.find('[name="index"]').val()
	};
	
	for(var k in data)
	{
		if(k == 'has_dsr' || k == 'has_dsr') continue;
		
		if( typeof(pData[k])!="undefined" && pData[k] !=  data[k])
		{
			changed = true;
			break;
		}
	}
	
	if(data.has_dsr){
		if(pData.has_dsr){
			if(data.description != pData.description) changed = true;
		}else{
			if(data.description != '') changed = true;
		}
	}else{
		if(pData.has_dsr) changed = true;
	}
	if(changed) 
		$p.find('.book-save').addClass('changed');
	else
		$p.find('.book-save').removeClass('changed');
}



//===================================//
function setOrders(){
	
	$('.book-part').each(function(i,el){
		
		var input = $(el).find('.part-order');
		var order = input.val();
		//if(order != i) $(el).find('.book-save').addClass('changed');
		input.val(i);
		
		checkPartChanged(el);
	});
}




//======== PAGES =========//

function setPages(){
	
	$('.book-page:last').addClass('active');
	
	var pgs = [], pg = 1, total = $('.book-page.active').length;
	
	$('.book-page.active').each(function(i,el){
		
		$(el).html('صفحه ' + pg + ' از ' + total);
		
		var index = $(el).closest('.book-part').index();
		
		pgs.push(index);
		
		pg++;
	});
	$('.book-page:not(.active)').each(function(i,el){
		$(el).html('');
	});
	
	$('#book-pages').val(pgs.join(','));
}

function checkBookData(){
    var key = true;
    $('.book-part').each(function(i,el){

        var text = $(el).find('.part-text').val();
        if($.trim(text) == '')
        {
            $('html,body').animate({
                scrollTop : $(el).offset().top - 100
            },function(){
                $(el).find('.part-text').focus();
            });
            key = false;
            return false;
        }
        $(el).find('.part-order').val(i);

        if(!$(el).hasClass('has-description'))
            $(el).find('.part-description').val('');
    });
	if(key) setPages();
    return key;
}
