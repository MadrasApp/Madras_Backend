<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
		echo "<pre>";
		print_r();
		echo "</pre>";
		die;
*/
global $POST_TYPES;

$postType = @$POST_TYPES[$type];

?>

<style>
.box:not(:first-child){margin: 30px 0;}
.pagination li.page-item a {
	margin:1px;
}
.pagination li.page-item:not(.active) a {
	background-color: #A4A4A4;
	color:#000066;
}
</style>
<script src="<?php echo  base_url() ?>/js/_admin/book.js"></script>

<div id="result"></div>


<div style="width:90%;padding-left:10px;margin:auto;">
	
	<h2><?php echo  $post['title'] ?></h2>

	<p></p>
	
	<div class="full-screen" id="select-index">
		<div class="content" style="padding:50px 100px">
			<div class="row">
				<div class="col-xs-12 col-sm-3">
					<h2 class="text-center" style="margin: 0 0 30px 0"> متصل کردن به فهرست <span class="result"></span> </h2>
					<!--hr style="margin: 20px 0"/>
					<div class="index-book"><?php echo  $indexes ?></div>
					<hr style="margin: 20px 0"-->
					<button type="button" class="btn btn-info btn-block btn-xlg" onClick="$(this).closest('.full-screen').hide()">بستن این صفحه</button>
				</div>
				<div class="col-xs-12 col-sm-9 indexes-content">
				</div>
			</div>
		</div>
		<?php if(isset($group_book_id)): ?>
			<script>
				$(window).load(function(){
					$('select.index-select').val(<?php echo  $group_book_id ?>);
					setTimeout(function(){
						$('select.index-select').trigger('change');
					},500);
				});
			</script>
		<?php endif ?>
	</div>		
	<ul class="pagination pagination-lg"></ul>	
	<div class="box">
		<div class="box-title"><i class="fa fa-book"></i> متن و محتویات </div>
		<div class="box-content" style="padding:0 15px">

			<?php if(isset($parts) && is_array($parts) && !empty($parts)): ?>
				<?php $bookPages = isset($meta['pages']) ? explode(',',$meta['pages']):[]; ?>
				<?php foreach ($parts as $pk=>$p): ?>
					<form class="book-part row hidden <?php echo  $p->sound != '' ? 'has-sound':'' ?> <?php echo  $p->image != '' ? 'has-image':'' ?> <?php echo  $p->description != '' ? 'has-description':'' ?> <?php echo  $p->index ? 'has-index':'' ?>" data-id="<?php echo  $p->id ?>">
						<div class="col-xs-1">
							<ul class="btn-group-vertical list-unstyled">
								<li class="btn btn-default add-index" title="<?php echo  $p->index_name != '' ? $p->index_name:'متصل کرد به فهرست'?>"><i class="fa fa-list"></i></li>
								<li class="btn btn-default add-sound" title="افزودن یا حذف صدا"><i class="fa fa-play-circle-o"></i></li>
								<li class="btn btn-default add-image" title="افزودن یا حذف تصویر"><i class="fa fa-picture-o"></i></li>
								<li class="btn btn-default add-description" title="افزودن یا حذف شرح"><i class="fa fa-comment-o"></i></li>
								<li class="btn btn-default add-part" title="افزودن پاراگراف"><i class="fa fa-plus-circle"></i></li>
							</ul>
						</div>

						<div class="part-content col-xs-10">
							<textarea name="text" class="form-control part-text" placeholder="متن"><?php echo  $p->text ?></textarea>
							<textarea name="description" class="form-control part-description" placeholder="شرح"><?php echo  $p->description ?></textarea>
							<div class="part-sound pull-left">
								<input name="file" type="hidden" value="<?php echo  $p->sound ?>">
								<i class="fa fa-volume-up toggle-sound" title="مشاهده یا پنهان کردن فایل صوتی"></i>
								<a href="<?php echo  $p->sound ?>" download="<?php echo  $p->sound ?>"><i class="fa fa-download" title="دریافت فایل صوتی"></i></a>
							</div>
							<div class="part-image pull-left">
								<input name="image" type="hidden" value="<?php echo  $p->image ?>">
								<i class="fa fa-picture-o toggle-image" title="مشاهده یا پنهان کردن تصویر"></i>
								<a href="<?php echo  $p->image ?>" download="<?php echo  $p->image ?>"><i class="fa fa-download" title="دریافت تصویر"></i></a>
							</div>
							<label class="col-md-12">لینک ویدئو : <input name="video" class="form-control part-text" dir="ltr" type="text" value="<?php echo  $p->video ?>"></label>
							<input name="order" class="part-order" type="hidden" value="<?php echo  $p->order ?>">
							<input name="index" class="part-index" type="hidden" value="<?php echo  $p->index ?>">
							<input name="id"    class="part-id"    type="hidden" value="<?php echo  $p->id ?>">
						</div>
						<div class="col-xs-1">
							<ul class="btn-group-vertical list-unstyled">
								<li class="btn btn-default level-up"    title="انتقال به بالا"><i class="fa fa-angle-up"></i></li>
								<li class="btn btn-default part-grid"   title="تغییر نحوه نمایش"><i class="fa fa-th-list"></i></li>
								<li class="btn btn-default delete-part" title="حذف پاراگراف"><i class="fa fa-trash"></i></li>
								<li class="btn btn-default level-down"  title="انتقال به پایین"><i class="fa fa-angle-down"></i></li>
							</ul>
						</div>
						<div class="book-page <?php echo  in_array($pk,$bookPages) ? 'active':'' ?>"></div>
						<div class="book-save" title="ذخیره"></div>
					</form>
				<?php endforeach ?>
			<?php else : ?>
				<form class="book-part row">
					<div class="col-xs-1">
						<ul class="btn-group-vertical list-unstyled">
							<li class="btn btn-default add-index" title="متصل کرد به فهرست"><i class="fa fa-list"></i></li>
							<li class="btn btn-default add-sound" title="افزودن یا حذف صدا"><i class="fa fa-play-circle-o"></i></li>
							<li class="btn btn-default add-image" title="افزودن یا حذف تصویر"><i class="fa fa-picture-o"></i></li>
							<li class="btn btn-default add-description" title="افزودن یا حذف شرح"><i class="fa fa-comment-o"></i></li>
							<li class="btn btn-default add-part" title="افزودن پاراگراف"><i class="fa fa-plus-circle"></i></li>
						</ul>
					</div>

					<div class="part-content col-xs-10">
						<textarea name="text" class="form-control part-text" placeholder="متن"></textarea>
						<textarea name="description" class="form-control part-description" placeholder="شرح"></textarea>
						<div class="part-sound pull-left">
							<input name="file" type="hidden" value="">
							<i class="fa fa-volume-up toggle-sound" title="مشاهده یا پنهان کردن فایل صوتی"></i>
							<a href="#" download="#"><i class="fa fa-download" title="دریافت فایل صوتی"></i></a>
						</div>
						<div class="part-image pull-left">
							<input name="image" type="hidden" value="">
							<i class="fa fa-picture-o toggle-image" title="مشاهده یا پنهان کردن تصویر"></i>
							<a href="#" download="#"><i class="fa fa-download" title="دریافت تصویر"></i></a>
						</div>
						<label class="col-md-12">لینک ویدئو : <input name="video" class="form-control part-text" dir="ltr" type="text"></label>
						<input name="order" class="part-order" type="hidden" value="">
						<input name="index" class="part-index" type="hidden" value="">
					</div>
					<div class="col-xs-1">
						<ul class="btn-group-vertical list-unstyled">
							<li class="btn btn-default level-up" title="انتقال به بالا"><i class="fa fa-angle-up"></i></li>
							<li class="btn btn-default part-grid" title="تغییر نحوه نمایش"><i class="fa fa-th-list"></i></li>
							<li class="btn btn-default delete-part" title="حذف پاراگراف"><i class="fa fa-trash"></i></li>
							<li class="btn btn-default level-down" title="انتقال به پایین"><i class="fa fa-angle-down"></i></li>
						</ul>
					</div>
					<div class="book-page"></div>
					<div class="book-save" title="ذخیره"></div>
				</form>
				
			<?php endif ?>
		</div>
		<div class="box-footer">
			<div class="deleted-parts"></div>
			<input type="hidden" id="book-pages" name="meta[pages]" value="<?php echo  @$meta['pages'] ?>">
		</div>
	</div>
	<ul class="pagination pagination-lg"></ul>	
	<div class="box">
		<div class="box-title"><i class="fa fa-check"></i>ذخیره</div>
		<div class="box-content">
		
			<div class="save-content">
				<button class="btn btn-success" onClick="saveAll(this)">ذخیره تغییرات</button>
				<button class="btn btn-info save-pages-btn" onClick="savePages(this)">ذخیره صفحه بندی</button>
				<span class="pages-status"></span>
				<h4 class="save-status en pull-left"></h4>
				<div class="progress bs-progress" style="margin: 15px 0 0;display:none;">
					<div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">...</div>
				</div>
			</div>
			
		</div>
		<div class="box-footer"></div>
	</div>
	
</div>

<script type="text/javascript">
    var postdata = <?php echo  json_encode($post) ?>, productdata = {};
	var $c = 0;
	function ControllPagination(){
		$('.pagination li a').unbind().click(function(){
			$('.pagination li').removeClass("active");
			$c = parseInt($(this).data("page"));
			$('.page'+$c).addClass("active");
			$('form.book-part').addClass('hidden');
			var $i = 0;
			var $j = 0;
			var $k = 0;
			$('form.book-part').each(function(){
				$class=$c==$j?'hidden':'';
				$i++;
				$j = parseInt($i/100);
				//console.error([$i,$j,$c,$class]);
				if($k!=$j){
					$k=$j;
				}
				$(this).removeClass($class);
			});
		})
	}
	function LoadPagination(){
		var $i = 0;
		var $j = 0;
		var $k = 0;
				$('.pagination').append('<li class="page-item'+($k?'':' active')+' page'+$j+'"><a class="page-link" data-page="'+$j+'">'+($j+1)+'</a></li>');
		$('form.book-part').each(function(){
			$class=$c==$j?'hidden':'';
			$i++;
			$j = parseInt($i/100);
			if($k!=$j){
				$('.pagination').append('<li class="page-item page'+$j+'"><a class="page-link" data-page="'+$j+'">'+($j+1)+'</a></li>');
				$k=$j;
			}
			$(this).removeClass($class);
		});
		ControllPagination();
	}
    $(document).ready(function (e) {
        $(document).on("blur", "input[max],input[min]", function () {
            var max = $(this).attr('max'), min = $(this).attr('min'), val = $(this).val();

            if (val > max) $(this).val(max);
            if (val < min) $(this).val(min);

        });
		//Load Fehrest
        var loader = $('.full-screen .result') , content = $('.indexes-content');
        var f = $(this).closest('.full-screen') , part = $(f).data('part');
        $(loader).addClass('loader h5 blue');
        $.ajax({
            type: "GET",
            url:  "api/subGroupLi/" + postdata.id ,
            dataType:"json",
            success: function(data)
            {
                $(loader).removeClass('loader h5 blue');
                $(content).html(data.msg);
            },
            error: function(){
                $(loader).removeClass('loader h5 blue').html('خطا در اتصال');
            }
        });
		LoadPagination();
    });
	
    window.onbeforeunload = function () {
		if($('.book-save.changed').length)
        return "بعضی از قسمتها ذخیره نشده اند. میخواهید خارج شوید؟";
    }
	
    $(document).on('click','.add-index',function(){
        var part = $(this).closest('.book-part'), btn = this;

        var f = $('#select-index');
        $(f).data('part',part).fadeIn();
        $(f).find('.remove-index').hide();

        $(f).find('.index-ul li').removeClass('disable selected');
        $(f).find('.index-ul li[data-id]').each(function(i,li){
            var pid  = $(li).attr('data-part-id');
			var part = $(li).data('part');
			
            if(pid != '') 
				part = $('.book-part[data-id="' + pid + '"]');
			
			if($(part).hasClass('has-index'))
				$(li).addClass('disable');
        });
		
        if($(part).hasClass('has-index'))
        {
            $(f).find('.remove-index').show();
            var id = $(part).find('.part-index').val();
            $(f).find('.index-ul li[data-id="'+id+'"]').addClass('selected').removeClass('disable');
        }
        $('select.index-select').prop('disabled',$('.index-ul li.selected,.index-ul li.disable').length > 0);
    });
</script>