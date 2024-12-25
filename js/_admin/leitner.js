	$(document).ready(function(){
		
		$(document).on('click','.part-grid',function(){
			
			var part = $(this).closest('.leitner-part');
			if(!$(part).hasClass('has-description')) return;
			$(part).toggleClass('full-grid');
			
		}).on('click','.level-up',function(){
			
			var part = $(this).closest('.leitner-part'),
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
			
			var part = $(this).closest('.leitner-part'),
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
	
		$(document).on('input','.leitner-part textarea,.leitner-part input',function(){
			checkPartChanged($(this).closest('.leitner-part'));
		});
		
		/*******************  SAVE *****************/
		$(document).on('click','.leitner-save',function(){
			var p = $(this).closest('.leitner-part');
			savePart(p);
		});	
	});

	$(window).load(function(){
		
		$('.leitner-part').each(function(i, el){
			setPartData(el);
		});
	});


	function saveQuestionAll(btn){
		
		var $changed = $('.leitner-save.changed'),
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
		
		var $changed = $('.leitner-save.changed'),
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
				savePart($changed[i].closest('.leitner-part'),success,fail);
			}
		} 
	
		next();
	}

	function savePart(p, success, fail){
		var $p = $(p);
		var btn = $p.find('.leitner-save');
		
		if(!$p.hasClass('has-description'))
			$p.find('.part-description').val('');
		
		btn.removeClass('changed saved');
		btn.addClass('saving');
		
		var data = new FormData($p[0]);
		$.ajax({
			type    : "POST",
			url     : URL + "/save_leitnerpart/" + leitnerdata.id,
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
							leitnerdata.id = data.part.id;
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
	
	if($(el).hassClass('leitner-part'))
		part = $(el);
	else
		part = $(el).closest('.leitner-part');
	
	$(part).find('.leitner-save').addClass('changed');
}

function setPartData(part){
	
	var $p = $(part);
	
	var data = { 
		title        : $p.find('[name="title"]').val(),
		description  : $p.find('[name="description"]').val()
	};
	
	$p.data(data);
}

function checkPartChanged(part){
	
	var $p = $(part), changed = false , pData = $p.data();
	var data = { 
		title        : $p.find('[name="title"]').val(),
		description  : $p.find('[name="description"]').val()
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
		$p.find('.leitner-save').addClass('changed');
	else
		$p.find('.leitner-save').removeClass('changed');
}



//===================================//
function checkBookData(){
    var key = true;
    $('.leitner-part').each(function(i,el){

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
