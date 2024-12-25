	$(document).ready(function(){
		
		$(document).on('click','.delete-part',function(){
			
			var btn = $(this);
			
			if(btn.hasClass('l')) return;
			
			var parts = $('.question-part') , part = $(this).closest('.question-part');
			
			if(!confirm('این پاراگراف حذف شود ؟')) return;
	
			
			function a(){
				if(parts.length > 1) {
					$(part).slideUp(400,function(){
						$(this).remove();
					});
				} else {
					$(part).removeClass('has-description full-grid has-sound has-image').removeAttr('data-id');
					$(part).find('.form-control,input').val('');
					$(part).find('.part-sound audio').remove();
					$(part).find('.part-image img').remove();
				}			
			}
			
			if($(part).is('[data-id]'))
			{
				btn.addClass('l red');
				
				$.ajax({
					type    : "POST",
					url     : URL + "/delete_questionpart/" + $(part).attr('data-id'),
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
	
	
		$(document).on('click','.add-sound',function(){
			var part = $(this).closest('.question-part'), btn = this;
			if($(part).hasClass('has-sound')) {
				if(!confirm('فایل صوتی حذف شود ؟')) return;
				$(part).removeClass('has-sound');
				$(part).find('audio').remove();
				$(part).find('.part-sound input').val('');
				checkPartChanged(part);
			}else{
				media('file,1',btn,function(data,files,button){
					var src = data[0];
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
			var part = $(this).closest('.question-part'), btn = this;
			if($(part).hasClass('has-image')) {
				if(!confirm('تصویر حذف شود ؟')) return;
				$(part).removeClass('has-image');
				$(part).find('img').remove();
				$(part).find('.part-image input').val('');
				checkPartChanged(part);
			}else{
				media('image,1',btn,function(data,files,button){
					var src = data[0];
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
			
			var part = $(this).closest('.question-part');
			if(!$(part).hasClass('has-description')) return;
			$(part).toggleClass('full-grid');
			
		}).on('click','.level-up',function(){
			
			var part = $(this).closest('.question-part'),
				prev = $(part).prev(),
				wt   = $(window).scrollTop(),
				ph   = $(part).outerHeight(true);
			if(!prev.length) return;
			$(window).scrollTop(wt-ph);
			setTimeout(function(){
				$('[data-title]').trigger('mouseleave');
			},100);
			$(part).insertBefore(prev);
			
		}).on('click','.level-down',function(){
			
			var part = $(this).closest('.question-part'),
				next = $(part).next(),
				wt   = $(window).scrollTop(),
				ph   = $(part).outerHeight(true);
			if(!next.length) return;
			$(window).scrollTop(wt+ph);
	
			setTimeout(function(){
				$('[data-title]').trigger('mouseleave');
			},100);
			$(part).insertAfter(next);
		});
	
		$(document).on('click','.question-part .add-part',function(){
			var part = $(this).closest('.question-part'),
				wt   = $(window).scrollTop(),
				ph   = $(part).outerHeight(true);
			//var i = $('.question-part').length + 1;
			var p =
				'<form class="has-feedback book-part question-part row" style="display: none;">'
				+ '<div class="col-xs-1">'
				+ '<ul class="btn-group-vertical list-unstyled">'
				+ '<li class="btn btn-default add-sound" data-title="افزودن یا حذف صدا"><i class="fa fa-play-circle-o"></i></li>'
				+ '<li class="btn btn-default add-image" data-title="افزودن یا حذف تصویر"><i class="fa fa-picture-o"></i></li>'
				+ '<li class="btn btn-default add-part" data-title="افزودن پاراگراف"><i class="fa fa-plus-circle"></i></li>'
				+ '</ul>'
				+ '</div>'
				+ '<div class="part-content col-xs-10">'
				+ '<textarea name="content" class="form-control part-text" placeholder="متن"></textarea>'
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
				+ '<input name="master" class="part-master" type="hidden" value="0">'
				+ '<input name="id" class="part-id" type="hidden" value="0">'
				+ '<input name="qid" class="part-qid" type="hidden" value="{qid}">'
				+ '</div>'
				+ '<div class="col-xs-1">'
				+ '<ul class="btn-group-vertical list-unstyled">'
				+ '<li class="btn btn-default level-up" data-title="انتقال به بالا"><i class="fa fa-angle-up"></i></li>'
				+ '<li class="btn btn-default part-grid" data-title="تغییر نحوه نمایش"><i class="fa fa-th-list"></i></li>'
				+ '<li class="btn btn-default delete-part" data-title="حذف پاراگراف"><i class="fa fa-trash"></i></li>'
				+ '<li class="btn btn-default level-down" data-title="انتقال به پایین"><i class="fa fa-angle-down"></i></li>'
				+ '</ul>'
				+ '</div>'
				+ '<div class="book-save question-save" data-title="ذخیره"></div>'
				+ '</form>';
			p = p.replace('{qid}',questiondata.id);
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
			
			setTimeout(function(){
				$('[data-title]').trigger('mouseleave');
			},100);
		}); 
	
		$(document).on('input','.question-part textarea,.question-part input',function(){
			checkPartChanged($(this).closest('.question-part'));
		});
		
		$(document).on('click','.question-part .toggle-sound',function(){
			var $this = $(this);
			if($this.next().next().is('audio')){
				$this.next().next().remove();
			}else{
				var part = $(this).closest('.question-part');
				var src = $(part).find('[name="file"]').val();			
				var sound = '<audio controls="">'+
							'<source src="'+src+'" type="audio/mpeg">'+
							'Your browser does not support the audio tag.'+
							'</audio>';
				$this.parent().append(sound);
			}
		});	
		$(document).on('click','.question-part .toggle-image',function(){
			var $this = $(this);
			if($this.next().next().is('img')){
				$this.next().next().remove();
			}else{
				var part = $(this).closest('.question-part');
				var src = $(part).find('[name="image"]').val();			
				var image = '<img src="'+src+'" />';
				$this.parent().append(image);
			}
		});	
		/*******************  SAVE *****************/
		$(document).on('click','.question-save',function(){
			var p = $(this).closest('.question-part');
			savePart(p);
		});	
	});

	$(window).load(function(){
		
		$('.question-part').each(function(i, el){
			setPartData(el);
		});
	});


	function saveQuestionAll(btn){
		
		var $changed = $('.question-save.changed'),
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
		} 
	
		next();
	}

	function saveAll(btn){
		
		var $changed = $('.question-save.changed'),
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
			}else{
				savePart($changed[i].closest('.question-part'),success,fail);
			}
		} 
	
		next();
	}

	function savePart(p, success, fail){
		var $p = $(p);
		var btn = $p.find('.question-save');
		
		if(!$p.hasClass('has-description'))
			$p.find('.part-description').val('');
		
		btn.removeClass('changed saved');
		btn.addClass('saving');
		
		var data = new FormData($p[0]);
		$.ajax({
			type    : "POST",
			url     : URL + "/save_questionpart/" + questiondata.id,
			data    : data ,
			cache   : false,
			processData : false,
			contentType : false,
			dataType    : 'json', 
			success     : function (data) {
				btn.removeClass("saving");
				if(data == 'login')
				{
					login(function(){
						savePart(p, success, fail);
					});
				}
				else
				{
					if(!data.done){
						btn.addClass('changed saved');
						alert(data.message);
						return;
					}
					setPartData($p);
					console.error(data.part);
					if(data.part && data.part.id){
						$p.attr('data-id',data.part.id);
						if(parseInt(data.part.master)){
							questiondata.id = data.part.id;
							$('.part-qid').val(data.part.id);
						} else {
							$('.part-qid').val(data.part.qid);
						}
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
	
	if($(el).hassClass('question-part'))
		part = $(el);
	else
		part = $(el).closest('.question-part');
	
	$(part).find('.question-save').addClass('changed');
}

function setPartData(part){
	
	var $p = $(part);
	
	var data = { 
		content     : $.trim($p.find('.part-text').val()),
		file        : $p.find('[name="file"]').val(),
		image       : $p.find('[name="image"]').val()
	};
	
	$p.data(data);
}

function checkPartChanged(part){
	
	var $p = $(part), changed = false , pData = $p.data();
	var data = { 
		content     : $.trim($p.find('.part-text').val()),
		file        : $p.find('[name="file"]').val(), 
		video       : $p.find('[name="video"]').val(), 
		image       : $p.find('[name="image"]').val()
	};
	
	for(var k in data)
	{
		if(k == 'has_dsr' || k == 'has_dsr') continue;
		
		if( pData[k] !=  data[k])
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
		$p.find('.question-save').addClass('changed');
	else
		$p.find('.question-save').removeClass('changed');
}



//===================================//
function checkBookData(){
    var key = true;
    $('.question-part').each(function(i,el){

        var content = $(el).find('.part-text').val();
        if($.trim(content) == '')
        {
            $('html,body').animate({
                scrollTop : $(el).offset().top - 100
            },function(){
                $(el).find('.part-text').focus();
            });
            key = false;
            return false;
        }

        if(!$(el).hasClass('has-description'))
            $(el).find('.part-description').val('');
    });
    return key;
}
