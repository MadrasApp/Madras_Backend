<?php  defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
.text-success {
    color: #0d95e0;
}
</style>
<?php
	global $extraData;
	$extraData = $extra;
	$this->load->helper('inc');
	
	$inc = new inc;
	
	$cols[''] = array(
		'field_name'=>'thumb',
		'td-attr'=>'style="padding:0;width:40px;"',
		'function'=>function($col,$row){

			global $CI;

			$col = file_exists($col) ? $col : $CI->settings->data['default_post_thumb'];

			$img  = '<img src="'.base_url().thumb($col,150).'" width="40" height="40">';
			$url  = site_url().'[ID]/preview-post';
			$link = '<a href="'.$url.'" target="_blank">'.$img.'</a>';
			return $img;
		}
	);

	$cols['شماره'] = array(
		'field_name'=>'id',
		'link'=>true,
		'th-attr' => 'style="width:50px"',
	);
	
	$cols['عنوان'] = array(
		'field_name'=>'title',
		'link'=>true,
		'html'=>'<div class="wb" style="font-size:13px" id="book_[ID]">[FLD]</div>',
		'th-attr' => 'style="width:50px"',
	);
		
	if($type == 'book')
	{
		$cols['اولویت'] = array(
            'field_name'=>'special',
            'link'=>true,
            'th-attr' => 'style="width:70px"',
			'function'=>function($col,$row){
					$special = array(
						"0"=>"عادی",
						"1"=>"پیشنهادی",
						"2"=>"ویژه",
						"3"=>"خاص",
						);
				
				return $special[$row["special"]];
			}
		);
		$cols['دسته بندی'] = array(
            'field_name'=>'category_name',
            'link'=>false,
			'function'=>function($col,$row){
				global $extraData;
				$html = isset($extraData['category_name'][$row["id"]])?$extraData['category_name'][$row["id"]]:'نامشخص';
				return $html;
			}
		);
		
		$cols['حجم کتاب'] = array(
			'field_name'=>'size',
			'link'=>false,
			'td-attr'=>'class="text-center en"',
			'type' => 'filesize'
		);
		
		$cols['قیمت'] = array(
			'field_name'=>'price',
			'link'=>false,
			'th-attr' => 'style="width:100px"',
			'function'=>function($col,$row){
				global $extraData;
				$count = $row["price"];
				$html = '<div id="price_'.$row["id"].'" data-price="'.$count.'">'.number_format($count,0,'.',',').'</div>';
				return $html;
			}
		);
		
		$cols['تعداد صفحه'] = array(
			'field_name'=>'pages',
			'link'=>false,
			'th-attr' => 'style="width:70px"'
		);
		if($readown)
		$cols['فهرست'] = array(
            'field_name'=>'fehrest',
            'link'=>false,
			'html'=>'<a title="فهرست کتاب" style="display:block;padding:10px" href="'.site_url("admin/posts/fehrestBook/[ID]").'"><i class="fa fa-lg fa-th-list"></i></a>',
            'th-attr' => 'style="width:70px"',
			'td-attr' => 'style="padding:0"',
		);//Alireza Balvardi

		$cols['تعداد پاراگراف'] = array(
			'field_name'=>'part_count',
			'link'=>false,
			'th-attr' => 'style="width:70px"',
			'td-attr' => 'style="padding:0"',
			'function'=>function($col,$row){
				global $extraData;
				$count = $row["part_count"];
				$html = $extraData['readown']?'<a title="ویرایش محتوای کتاب" style="display:block;padding:10px" href="'.site_url("admin/posts/editBook/".$row["id"]).'">'.$count.'</a>':$count;
				return $html;
			}
			
		);
		
		$cols['دارای شرح'] = array(
			'field_name'=>'has_description',
			'link'=>false,
			'count'=>'has_description',
			'th-attr' => 'style="width:50px"',
			'function'=>function($col,$row){
				global $extraData;
				$count = $row["has_description"];
				$html = '<i class="fa fa-lg fa-align-center '.($count?'text-success':'text-muted').'"></i><br /> '.$count;
				return $html;
			}
		);
		
		$cols['دارای صوت'] = array(
			'field_name'=>'has_sound',
			'link'=>false,
			'count'=>'has_sound',
			'th-attr' => 'style="width:50px"',
			'function'=>function($col,$row){
				global $extraData;
				$count = $row["has_sound"];
				$html = '<i class="fa fa-lg fa-volume-up '.($count?'text-success':'text-muted').'"></i><br /> '.$count;
				return $html;
			}
		);
		$cols['دارای ویدئو'] = array(
			'field_name'=>'has_video',
			'link'=>false,
			'count'=>'has_video',
			'th-attr' => 'style="width:50px"',
			'function'=>function($col,$row){
				global $extraData;
				$count = $row["has_video"];
				$html = '<i class="fa fa-lg fa-file-movie-o '.($count?'text-success':'text-muted').'"></i><br /> '.$count;
				return $html;
			}
		);
		$cols['دارای تصویر'] = array(
			'field_name'=>'has_image',
			'link'=>false,
			'count'=>'has_image',
			'th-attr' => 'style="width:50px"',
			'function'=>function($col,$row){
				global $extraData;
				$count = $row["has_image"];
				$html = '<i class="fa fa-lg fa-picture-o '.($count?'text-success':'text-muted').'"></i><br /> '.$count;
				return $html;
			}
		);
	    if(isset($user_can_edit) && $user_can_edit){
			$cols['دارای آزمون تستی'] = array(
				'field_name'=>'has_test',
				'link'=>false,
				'count'=>'has_test',
				'th-attr' => 'style="width:50px"',
				//'text-success', 'text-muted'
				'function'=>function($col,$row){
					global $extraData;
					$count = isset($extraData['has_test'][$row["id"]])?$extraData['has_test'][$row["id"]]:0;
					if(!$row["has_test"]){
						$data = array("has_test"=>$count);
						$this->db->where('id',$row["id"])->update('posts',$data);
					}
					$html = '<div style="cursor: pointer" onclick="showTest(this,'.$row["id"].')"><i class="fa fa-lg fa-file-text-o '.($count?'text-success':'text-muted').'"></i><br /> '.$count.' </div>';
					return $html;
				}
			);
			$cols['دارای آزمون تشریحی'] = array(
				'field_name'=>'has_tashrihi',
				'link'=>false,
				'count'=>'has_tashrihi',
				'th-attr' => 'style="width:50px"',
				'function'=>function($col,$row){
					global $extraData;
					$count = isset($extraData['has_tashrihi'][$row["id"]])?$extraData['has_tashrihi'][$row["id"]]:0;
					if(!$row["has_tashrihi"]){
						$data = array("has_tashrihi"=>$count);
						$this->db->where('id',$row["id"])->update('posts',$data);
					}
					$html = '<div style="cursor: pointer" onclick="showTashrihi(this,'.$row["id"].')"><i class="fa fa-lg fa-file-text-o '.($count?'text-success':'text-muted').'"></i><br /> '.$count.' </div>';
					return $html;
				}
			);
		}
		$cols['دانلود'] = array(
			'field_name'=>'has_download',
			'link'=>false,
			'count'=>'has_download',
			'th-attr' => 'style="width:50px"',
			//'text-success', 'text-muted'
			'function'=>function($col,$row){
				global $extraData;
				$count = isset($extraData['has_download'][$row["id"]])?$extraData['has_download'][$row["id"]]:0;
				if(!$row["has_download"]){
					$data = array("has_download"=>$count);
					$this->db->where('id',$row["id"])->update('posts',$data);
				}
				$html = '<div style="padding:10px;cursor: pointer" onclick="showDownloaded(this,'.$row["id"].','.$count.')"><i class="fa fa-lg fa-download '.($count?'text-success':'text-muted').'"></i><br />'.$count.'</div>';
				return $html;
			}
		);
	}

		
	$cols['نویسنده'] = array(
		'field_name'=>'author',
		'link'=>true,
		'th-attr' => 'style="width:100px"',
	);
	
	$cols['تاریخ به روزرسانی'] = array(
		'field_name'=>'date_modified',
		'link'=>true,
		'type'=>'date',
		'th-attr' => 'style="width:150px"'
	);

	$cols[' '] = array(
		'field_name'=>'id',
		'type'=>'op',
		'items'=> $options,
		'td-attr'=>'align="center" style="padding:0;width:30px;"'
	);

    if(isset($user_can_edit) && $user_can_edit)
    {
        $cols['عنوان'] = array(
            'field_name'=>'title',
            'link'=>true,
            'html'=>'<div class="wb" style="font-size:13px"><a href="'.site_url("admin/{$type}/edit/[ID]").'" id="book_[ID]">[FLD]</a></div>',
            'max'=>200
        );
    }

	if( isset( $_tabs ) )
	foreach( $_tabs as $tab=>$tab_data )
	{
		$href = site_url( "admin/$type/".$tab );
		$class = $this->uri->segment(3) === $tab ? "w-btn active":"w-btn";
		
		echo "<a href='$href' class='$class'>".$tab_data['name'].
			 " (<span class='row-count row-$tab'>".$tab_data['count']."</span>)</a>";
		
	}
	
	$q =
	"SELECT p.*
	,(SELECT displayname FROM ci_users WHERE id=p.author) AS author ";
	
	if($type == 'book')
	{
		
		$q .= //Alireza Balvardi
		",1 AS `fehrest` 
		,0 AS category_parent_id
		,0 AS category_name";		
	}
	
	$q .= " FROM ci_posts p $query ";
	echo $searchHtml;
	$inc->createTable($cols,$q,'id="table" class="table light2" ',$tableName,60);
?>
<div class="hidden">
    <div class="view-peyorder"><!-- Alireza Balvardi -->
				<center>
					<h2>لیست فروش کتاب <span class="displaynametag"></span></h2>
				</center>
					<table class="table">
					<thead>
						<tr>
							<th>ردیف</th>
							<th>شماره فاکتور</th>
							<th>تاریخ فروش</th>
							<th>کد تخفیف</th>
							<th>مبلغ کتاب</th>
							<th>مبلغ پرداختی</th>
							<th>نتیجه</th>
							<th>یادداشت</th>
						</tr>
					</thead>
					<tbody class="mobilebody">
					</tbody>
					</table>
	</div>
    <div class="view-test-book"><!-- Alireza Balvardi -->
        <div class="row">
            <form class="clearfix">
				<center>
					<h2>لیست سوالات آزمون تستی کتاب <span class="bookname"></span></h2>
					<a class="btn btn-success m-2" onclick="SaveTestQuestions()">ذخیره سوالات</a>
					<a class="btn btn-danger m-2" onclick="DeleteTestQuestions()">حذف همه سوالات</a>
					<hr />
					<div class="box">
						<div class="box-title"><i class="fa fa-book"></i> آزمون اول</div>
						<div class="box-content" style="padding:0 15px">
							<div id="bookbody1"></div>
							<div class="text-center" style="padding:20px">
								<div class="plus add-test" data-category="1"></div>
							</div>
						</div>
						<div class="box-footer">
							<div class="deleted-tests"></div>
						</div>
					</div>
					<a class="btn btn-success" onclick="SaveTestQuestions()">ذخیره سوالات</a>
					<a class="btn btn-danger m-2" onclick="DeleteTestQuestions()">حذف همه سوالات</a>
					<div class="box">
						<div class="box-title"><i class="fa fa-book"></i> آزمون دوم</div>
						<div class="box-content" style="padding:0 15px">
							<div id="bookbody2"></div>
							<div class="text-center" style="padding:20px">
								<div class="plus add-test" data-category="2"></div>
							</div>
						</div>
						<div class="box-footer"></div>
					</div>			
					<a class="btn btn-success" onclick="SaveTestQuestions()">ذخیره سوالات</a>
					<a class="btn btn-danger m-2" onclick="DeleteTestQuestions()">حذف همه سوالات</a>
					<div class="box">
						<div class="box-title"><i class="fa fa-book"></i> آزمون کل کتاب</div>
						<div class="box-content" style="padding:0 15px">
							<div id="bookbody3"></div>
							<div class="text-center" style="padding:20px">
								<div class="plus add-test" data-category="3"></div>
							</div>
						</div>
						<div class="box-footer"></div>
					</div>			
					<a class="btn btn-success" onclick="SaveTestQuestions()">ذخیره سوالات</a>
					<a class="btn btn-danger m-2" onclick="DeleteTestQuestions()">حذف همه سوالات</a>
				</center>
			</form>
		</div>
	</div>
    <div class="view-tashrihi-book"><!-- Alireza Balvardi -->
        <div class="row">
            <form class="clearfix">
				<center>
					<h2>لیست سوالات آزمون تشریحی کتاب <span class="bookname"></span></h2>
					<a class="btn btn-success" onclick="SaveTashrihiQuestions()">ذخیره سوالات</a>
					<a class="btn btn-danger" onclick="DeleteTashrihiQuestions()">حذف همه سوالات</a>
					<hr />
					<div class="box">
						<div class="box-title"><i class="fa fa-book"></i> آزمون اول</div>
						<div class="box-content" style="padding:0 15px">
							<div id="bookbody1"></div>
							<div class="text-center" style="padding:20px">
								<div class="plus add-tashrihi" data-category="1"></div>
							</div>
						</div>
						<div class="box-footer">
							<div class="deleted-tashrihis"></div>
						</div>
					</div>
					<a class="btn btn-success" onclick="SaveTashrihiQuestions()">ذخیره سوالات</a>
					<a class="btn btn-danger" onclick="DeleteTashrihiQuestions()">حذف همه سوالات</a>
					<div class="box">
						<div class="box-title"><i class="fa fa-book"></i> آزمون دوم</div>
						<div class="box-content" style="padding:0 15px">
							<div id="bookbody2"></div>
							<div class="text-center" style="padding:20px">
								<div class="plus add-tashrihi" data-category="2"></div>
							</div>
						</div>
						<div class="box-footer"></div>
					</div>			
					<a class="btn btn-success" onclick="SaveTashrihiQuestions()">ذخیره سوالات</a>
					<a class="btn btn-danger" onclick="DeleteTashrihiQuestions()">حذف همه سوالات</a>
					<div class="box">
						<div class="box-title"><i class="fa fa-book"></i> آزمون کل کتاب</div>
						<div class="box-content" style="padding:0 15px">
							<div id="bookbody3"></div>
							<div class="text-center" style="padding:20px">
								<div class="plus add-tashrihi" data-category="3"></div>
							</div>
						</div>
						<div class="box-footer"></div>
					</div>			
					<a class="btn btn-success" onclick="SaveTashrihiQuestions()">ذخیره سوالات</a>
					<a class="btn btn-danger" onclick="DeleteTashrihiQuestions()">حذف همه سوالات</a>
				</center>
			</form>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(e) {
		$('select[name="category_parent_id"]').change(function(e) {
			$('select[name="category"]').val("");
			$('select[name="category"] option').remove();
			val = $(this).val();
			if(val.length){
				$.ajax({
					type: "POST",
					url: 'admin/api/LoadSubCategories/' + val,
					dataType: "json",
					success: function (data) {
						if(data.done){
							msg = data.msg;
							for(i=0;i<msg.length;i++){
								$('select[name="category"]').append('<option value="'+msg[i]['id']+'">'+msg[i]['name']+'</option>');
							}
						}
					}
				});
			} else {
				$('select[name="category"]').append('<option value="">بدون انتخاب</option>');
			}
		});
	});
	
	
	function _pdtd(btn,op,id){
		
		var text;
		
		switch(op){
			case 'draft': text = 'این مطلب غیر قابل مشاهده برای همه خواهد بود . <br/> ادامه می دهید ؟'; break;
			case 'publish': text = 'این مطلب قابل مشاهده برای همه خواهد بود . <br/> ادامه می دهید ؟'; break;
			case 'trash'  : text = 'این مطلب به زباله دان انتقال می یابد . <br/> ادامه می دهید ؟'; break;
			case 'delete' : text = 'این مطلب حذف می شود . <br/> ادامه می دهید ؟'; break;		
		}
		
		var options = {
			url :'post/'+op+'/'+id,
			data:{id:id},
			success:function(data){	
				$(btn).closest('tr').remove();
				var tabs = data.data;
				for(var tab in tabs)
				$('.row-count.row-'+tab).html(tabs[tab]);
			},
			//loader:$(btn).closest(ul.length?'.cat-item-div':'li'),
			Dhtml:text,
			Did:'_pdtd'+id
		};
		Confirm(options);
	}
//=============================================
	function LoadAgainTestControl(){
			$('.test-grid').unbind().on('click',function(){
				$(this).closest('.book-test').toggleClass('full-grid');
			});
			$('.add-test').unbind().on('click',function(){
				
				var j = $('.book-test').length + 1, a = '';
				
				for(var i=1;i<=4;i++)
					a += '<div class="input-group">'
					+'<label class="input-group-addon">'
					+'<input type="radio" value="'+i+'" name="book[test]['+j+'][answer]" '+(i==1?'checked':'')+'> پاسخ '+i
					+'</label>'
					+'<input type="text" class="form-control need" name="book[test]['+j+'][answer_'+i+']" placeholder="پاسخ '+i+'">'
					+'</div>';
						
				
				
				var t =  '<div class="book-test row" style="display:none">'
						+'<div class="col-xs-1">'
						+'<ul class="btn-group-vertical list-unstyled">'
						+'<li class="btn btn-default test-grid" title="تغییر نحوه نمایش"><i class="fa fa-th-list"></i></li>'
						+'<li class="btn btn-default delete-test" title="حذف"><i class="fa fa-trash"></i></li>'
						+'</ul>'
						+'</div>'
						+'<div class="test-content col-xs-11">'
						+'<div class="input-group col-sm-2 pull-right">'
						+'<label class="input-group-addon">'
						+'ترم:'
						+'</label>'
						+'<input type="text" name="book[test]['+j+'][term]" class="form-control input-lg need test-text" placeholder="ترم" title="ترم">'
						+'</div>'
						+'<div class="input-group col-sm-2 pull-right">'
						+'<label class="input-group-addon">'
						+'صفحه کتاب:'
						+'</label>'
						+'<input type="text" name="book[test]['+j+'][page]" class="form-control input-lg need" placeholder="صفحه کتاب" title="صفحه کتاب">'
						+'</div>'
						+'<div class="input-group col-sm-2 pull-right">'
						+'<label class="input-group-addon">'
						+'شماره آزمون:'
						+'</label>'
						+'<input type="text" name="book[test]['+j+'][testnumber]" class="form-control input-lg need" placeholder="شماره آزمون" title="شماره آزمون">'
						+'</div>'
						+'<div class="input-group col-sm-12 pull-right">'
						+'<label class="input-group-addon">'
						+'سوال:'
						+'</label>'
						+'<input type="text" name="book[test]['+j+'][question]" class="form-control input-lg need" placeholder="سوال" title="سوال">'
						+'</div>'
						+'<div class="answers">'
						+ a
						+'</div>'
						+'</div>'
						+'<input type="hidden" name="book[test]['+j+'][id]" value="0">'
						+'<input type="hidden" name="book[test]['+j+'][category]" value="'+($(this).attr('data-category'))+'">'
						+'</div>';
				
				var $t = $(t);
				
				$t.insertBefore(this.parentNode);
				LoadAgainTestControl();
				
				$t.slideDown(400,function(){
					$('#edit-testi').animate({
						scrollTop : '+=' + $t.outerHeight()
					},function () {
						$t.find('.test-text').focus();
					});
				});
			});
			$('.delete-test').unbind().on('click',function(){
				
				if(!confirm('این آزمون حذف شود ؟')) return;
				
				var test = $(this).closest('.book-test');
				
				if($(test).is('[data-id]'))
					$('.deleted-tests').append('<input type="hidden" name="book[deleted_test][]" value="'+($(test).attr('data-id'))+'">');
				
				$(test).slideUp(400,function(){
					$(this).remove();
				});
			});
	}
	function showTest(btn,id){
			var $html = $('<div/>', {'id': 'edit-testi'});
			$html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
			popupScreen($html);
			bookname = $('#book_'+id).html();
			$testhtml='<div class="book-test row" data-id="{testid}">'+
			'<div class="col-xs-1">'+
			'<ul class="btn-group-vertical list-unstyled">'+
			'<li class="btn btn-default test-grid" title="تغییر نحوه نمایش"><i class="fa fa-th-list"></i></li>'+
			'<li class="btn btn-default delete-test" title="حذف"><i class="fa fa-trash"></i></li>'+
			'</ul>'+
			'</div>'+
			'<div class="test-content col-xs-11">'+
			'<div class="input-group col-sm-2 pull-right">'+
				'<label class="input-group-addon">'+
					'ترم:'+
				'</label>'+
				'<input type="text" name="book[test][{tcount}][term]" class="form-control input-lg need" placeholder="ترم" title="ترم" value="{term}">'+
			'</div>'+
			'<div class="input-group col-sm-2 pull-right">'+
				'<label class="input-group-addon">'+
					'صفحه کتاب:'+
				'</label>'+
				'<input type="text" name="book[test][{tcount}][page]" class="form-control input-lg need" placeholder="صفحه کتاب" title="صفحه کتاب" value="{page}">'+
			'</div>'+
			'<div class="input-group col-sm-2 pull-right">'+
				'<label class="input-group-addon">'+
					'شماره آزمون:'+
				'</label>'+
				'<input type="text" name="book[test][{tcount}][testnumber]" class="form-control input-lg need" placeholder="شماره آزمون" title="شماره آزمون" value="{testnumber}">'+
			'</div>'+
			'<div class="input-group col-sm-12 pull-right">'+
				'<label class="input-group-addon">'+
					'سوال:'+
				'</label>'+
				'<input type="text" name="book[test][{tcount}][question]" class="form-control input-lg need" placeholder="سوال" title="سوال" value="{question}">'+
			'</div>'+
			'<div class="answers">'+
			'<div class="input-group">'+
			'<label class="input-group-addon">'+
			'<input type="radio" value="1" name="book[test][{tcount}][answer]" {checked1} /> پاسخ 1 '+
			'</label>'+
			'<input type="text" class="form-control need" name="book[test][{tcount}][answer_1]" placeholder="پاسخ 1" value="{answer_1}">'+
			'</div>'+
			'<div class="input-group">'+
			'<label class="input-group-addon">'+
			'<input type="radio" value="2" name="book[test][{tcount}][answer]" {checked2} /> پاسخ 2 '+
			'</label>'+
			'<input type="text" class="form-control need" name="book[test][{tcount}][answer_2]" placeholder="پاسخ 2" value="{answer_2}">'+
			'</div>'+
			'<div class="input-group">'+
			'<label class="input-group-addon">'+
			'<input type="radio" value="3" name="book[test][{tcount}][answer]" {checked3} /> پاسخ 3 '+
			'</label>'+
			'<input type="text" class="form-control need" name="book[test][{tcount}][answer_3]" placeholder="پاسخ 3" value="{answer_3}">'+
			'</div>'+
			'<div class="input-group">'+
			'<label class="input-group-addon">'+
			'<input type="radio" value="4" name="book[test][{tcount}][answer]" {checked4} /> پاسخ 4 '+
			'</label>'+
			'<input type="text" class="form-control need" name="book[test][{tcount}][answer_4]" placeholder="پاسخ 4" value="{answer_4}">'+
			'</div>'+
			'</div>'+
			'<input type="hidden" name="book[test][{tcount}][id]" value="{testid}">'+
			'<input type="hidden" name="book[test][{tcount}][category]" value="{category}">'+
			'</div>'+
			'</div>';
			$.ajax({
				type: "POST",
				url: 'admin/api/getBookTest/' + id,
				dataType: "json",
				success: function (data) {
	
					if (data == "login") {
						popupScreen('');
						login(function () {
							edit_user(btn, id)
						});
						return;
					}
	
					if (!data.done) {
						$html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
						return;
					}
					var $view = $('.view-test-book').clone(true);
	
					$view.find('.bookname').html(bookname);
	
					$view.find('.update-el').each(function (i, el) {
						id = $(this).attr("id");
						id = id.replace('{id}',1265);
						$(this).attr("id",id);
					});
					$view.find('.update-el').on('input',function () {
						var val = $(this).val();
						getBooks(val,$(this));
					});
					$view.find('form').attr("id",'postForm');
					$view.find('form').append('<input type="hidden" name="bookid" id="bookid" value="'+id+'" />');
					$view.find('#bookbody1').html("");
					$view.find('#bookbody2').html("");
					for(i=0;i < data.test.length;i++){
						el = data.test[i];
						testhtml = $testhtml;
						category = el.category;
						testhtml = testhtml.replace(/{tcount}/g,i);
						testhtml = testhtml.replace(/{answer_1}/g,el.answer_1);
						testhtml = testhtml.replace(/{answer_2}/g,el.answer_2);
						testhtml = testhtml.replace(/{answer_3}/g,el.answer_3);
						testhtml = testhtml.replace(/{answer_4}/g,el.answer_4);
						testhtml = testhtml.replace('{checked'+el.true_answer+'}','checked="checked"');
						testhtml = testhtml.replace(/{checked1}/g,'');
						testhtml = testhtml.replace(/{checked2}/g,'');
						testhtml = testhtml.replace(/{checked3}/g,'');
						testhtml = testhtml.replace(/{checked4}/g,'');
						testhtml = testhtml.replace(/{question}/g,el.question);
						testhtml = testhtml.replace(/{testid}/g,el.id);
						testhtml = testhtml.replace(/{term}/g,el.term);
						testhtml = testhtml.replace(/{page}/g,el.page);
						testhtml = testhtml.replace(/{testnumber}/g,el.testnumber);
						testhtml = testhtml.replace(/{category}/g,category);
						$view.find('#bookbody'+category).append(testhtml);
					}
	
					$html.html($view);
					LoadAgainTestControl();
				},
				error: function () {
					$html.html('<h3 class="text-warning text-center">Conection Error</h3>');
				}
			});
	}
	function SaveTestQuestions(){
		var key = true;
		$('#postForm .need').each(function(i,el){
			if($.trim($(el).val()) == '')
			{
				$('#edit-testi').animate({
					scrollTop : $(el).offset().top - 120
				},300,function(){
					$(el).focus()
				});
				key = false;
				return false;
			}
		});
		if(!key) return;
        $data = $('#postForm').serialize();
		$.ajax({
			type: "POST",
			url: 'admin/api/saveBookTest/',
			dataType: "json",
			data: $data,
			success: function (data) {
				alert(data.msg);
				if(!data.status){
					$('.close-popup').click();
					location.reload();
				}
			}
		});
	}
	function DeleteTestQuestions(){
		if(!confirm("آیا مطمئن هستید که می خواهید کلیه سوالات تستی را حذف نمایید"))
			return;
        $data = $('#postForm').serialize();
		$.ajax({
			type: "POST",
			url: 'admin/api/deleteBookTest/',
			dataType: "json",
			data: $data,
			success: function (data) {
				alert(data.msg);
				if(!data.status){
					$('.close-popup').click();
					location.reload();
				}
			}
		});
	}
//=============================================
	function LoadAgainTashrihiControl(){
			$('.tashrihi-grid').unbind().on('click',function(){
				$(this).closest('.book-tashrihi').toggleClass('full-grid');
			});
			$('.add-tashrihi').unbind().on('click',function(){
				
				var j = $('.book-tashrihi').length + 1;
				
				var t =  '<div class="book-tashrihi row" style="display:none">'
						+'<div class="col-xs-1">'
						+'<ul class="btn-group-vertical list-unstyled">'
						+'<li class="btn btn-default tashrihi-grid" title="تغییر نحوه نمایش"><i class="fa fa-th-list"></i></li>'
						+'<li class="btn btn-default delete-tashrihi" title="حذف"><i class="fa fa-trash"></i></li>'
						+'</ul>'
						+'</div>'
						+'<div class="tashrihi-content col-xs-11">'
						+'<div class="input-group col-sm-2 pull-right">'
						+'<label class="input-group-addon">'
						+'ترم:'
						+'</label>'
						+'<input type="text" name="book[tashrihi]['+j+'][term]" class="form-control input-lg need tashrihi-text" placeholder="ترم" title="ترم">'
						+'</div>'
						+'<div class="input-group col-sm-2 pull-right">'
						+'<label class="input-group-addon">'
						+'صفحه کتاب:'
						+'</label>'
						+'<input type="text" name="book[tashrihi]['+j+'][page]" class="form-control input-lg need" placeholder="صفحه کتاب" title="صفحه کتاب">'
						+'</div>'
						+'<div class="input-group col-sm-2 pull-right">'
						+'<label class="input-group-addon">'
						+'بارم سوال:'
						+'</label>'
						+'<input type="text" name="book[tashrihi]['+j+'][barom]" class="form-control input-lg need" placeholder="بارم سوال" title="بارم سوال">'
						+'</div>'
						+'<div class="input-group col-sm-2 pull-right">'
						+'<label class="input-group-addon">'+
						+'شماره آزمون:'
						+'</label>'
						+'<input type="text" name="book[tashrihi]['+j+'][testnumber]" class="form-control input-lg need" placeholder="شماره آزمون" title="شماره آزمون">'
						+'</div>'
						+'<div class="input-group col-sm-12 pull-right">'
						+'<label class="input-group-addon">'
						+'سوال:'
						+'</label>'
						+'<input type="text" name="book[tashrihi]['+j+'][question]" class="form-control input-lg need" placeholder="سوال" title="سوال">'
						+'</div>'
						+'<div class="input-group col-sm-12">'
						+'<label class="input-group-addon">'
						+' پاسخ '
						+'</label>'
						+'<textarea class="form-control need" name="book[tashrihi]['+j+'][answer]" rows="8" placeholder="پاسخ"></textarea>'
						+'</div>'
						+'</div>'
						+'<input type="hidden" name="book[tashrihi]['+j+'][id]" value="0">'
						+'<input type="hidden" name="book[tashrihi]['+j+'][category]" value="'+($(this).attr('data-category'))+'">'
						+'</div>'
						+'<div class="clearfix"></div><hr />';
				
				var $t = $(t);
				
				$t.insertBefore(this.parentNode);
				LoadAgainTashrihiControl();
				
				$t.slideDown(400,function(){
					$('#edit-tashrihi').animate({
						scrollTop : '+=' + $t.outerHeight()
					},function () {
						$t.find('.tashrihi-text').focus();
					});
				});
			});
			$('.delete-tashrihi').unbind().on('click',function(){
				
				if(!confirm('این آزمون حذف شود ؟')) return;
				
				var tashrihi = $(this).closest('.book-tashrihi');
				
				if($(tashrihi).is('[data-id]'))
					$('.deleted-tashrihis').append('<input type="hidden" name="book[deleted_tashrihi][]" value="'+($(tashrihi).attr('data-id'))+'">');
				
				$(tashrihi).slideUp(400,function(){
					$(this).remove();
				});
			});
	}
	function showTashrihi(btn,id){
			var $html = $('<div/>', {'id': 'edit-tashrihi'});
			$html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
			popupScreen($html);
			bookname = $('#book_'+id).html();
			$tashrihihtml='<div class="book-tashrihi row" data-id="{tashrihiid}">'+
			'<div class="col-xs-1">'+
			'<ul class="btn-group-vertical list-unstyled">'+
			'<li class="btn btn-default tashrihi-grid" title="تغییر نحوه نمایش"><i class="fa fa-th-list"></i></li>'+
			'<li class="btn btn-default delete-tashrihi" title="حذف"><i class="fa fa-trash"></i></li>'+
			'</ul>'+
			'</div>'+
			'<div class="tashrihi-content col-xs-11">'+
			'<div class="input-group col-sm-2 pull-right">'+
				'<label class="input-group-addon">'+
					'ترم:'+
				'</label>'+
				'<input type="text" name="book[tashrihi][{tcount}][term]" class="form-control input-lg need" placeholder="ترم" title="ترم" value="{term}">'+
			'</div>'+
			'<div class="input-group col-sm-2 pull-right">'+
				'<label class="input-group-addon">'+
					'صفحه کتاب:'+
				'</label>'+
				'<input type="text" name="book[tashrihi][{tcount}][page]" class="form-control input-lg need" placeholder="صفحه کتاب" title="صفحه کتاب" value="{page}">'+
			'</div>'+
			'<div class="input-group col-sm-2 pull-right">'+
				'<label class="input-group-addon">'+
					'شماره آزمون:'+
				'</label>'+
				'<input type="text" name="book[tashrihi][{tcount}][testnumber]" class="form-control input-lg need" placeholder="شماره آزمون" title="شماره آزمون" value="{testnumber}">'+
			'</div>'+
			'<div class="input-group col-sm-2 pull-right">'+
				'<label class="input-group-addon">'+
					'بارم سوال:'+
				'</label>'+
				'<input type="text" name="book[tashrihi][{tcount}][barom]" class="form-control input-lg need" placeholder="بارم سوال" title="بارم سوال" value="{barom}">'+
			'</div>'+
			'<div class="input-group col-sm-12 pull-right">'+
				'<label class="input-group-addon">'+
					'سوال:'+
				'</label>'+
				'<input type="text" name="book[tashrihi][{tcount}][question]" class="form-control input-lg need" placeholder="سوال" title="سوال" value="{question}">'+
			'</div>'+
			'<div class="input-group col-sm-12">'+
			'<label class="input-group-addon">'+
			' پاسخ '+
			'</label>'+
			'<textarea class="form-control need" name="book[tashrihi][{tcount}][answer]" rows="8" placeholder="پاسخ">{answer}</textarea>'+
			'</div>'+
			'<input type="hidden" name="book[tashrihi][{tcount}][id]" value="{tashrihiid}">'+
			'<input type="hidden" name="book[tashrihi][{tcount}][category]" value="{category}">'+
			'</div>'+
			'</div>'+
			'<div class="clearfix"></div><hr />';
			$.ajax({
				type: "POST",
				url: 'admin/api/getBookTashrihi/' + id,
				dataType: "json",
				success: function (data) {
	
					if (data == "login") {
						popupScreen('');
						login(function () {
							edit_user(btn, id)
						});
						return;
					}
	
					if (!data.done) {
						$html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
						return;
					}
					var $view = $('.view-tashrihi-book').clone(true);
	
					$view.find('.bookname').html(bookname);
	
					$view.find('.update-el').each(function (i, el) {
						id = $(this).attr("id");
						id = id.replace('{id}',1265);
						$(this).attr("id",id);
					});
					$view.find('.update-el').on('input',function () {
						var val = $(this).val();
						getBooks(val,$(this));
					});
					$view.find('form').attr("id",'postForm');
					$view.find('form').append('<input type="hidden" name="bookid" id="bookid" value="'+id+'" />');
					$view.find('#bookbody1').html("");
					$view.find('#bookbody2').html("");
					for(i=0;i < data.tashrihi.length;i++){
						el = data.tashrihi[i];
						tashrihihtml = $tashrihihtml;
						category = el.category;
						tashrihihtml = tashrihihtml.replace(/{tcount}/g,i);
						tashrihihtml = tashrihihtml.replace(/{answer}/g,el.answer);
						tashrihihtml = tashrihihtml.replace(/{question}/g,el.question);
						tashrihihtml = tashrihihtml.replace(/{tashrihiid}/g,el.id);
						tashrihihtml = tashrihihtml.replace(/{term}/g,el.term);
						tashrihihtml = tashrihihtml.replace(/{page}/g,el.page);
						tashrihihtml = tashrihihtml.replace(/{barom}/g,el.barom);
						tashrihihtml = tashrihihtml.replace(/{testnumber}/g,el.testnumber);
						tashrihihtml = tashrihihtml.replace(/{category}/g,category);
						$view.find('#bookbody'+category).append(tashrihihtml);
					}
	
					$html.html($view);
					LoadAgainTashrihiControl();
				},
				error: function () {
					$html.html('<h3 class="text-warning text-center">Conection Error</h3>');
				}
			});
	}
	function SaveTashrihiQuestions(){
		var key = true;
		$('#postForm .need').each(function(i,el){
			if($.trim($(el).val()) == '')
			{
				$('#edit-tashrihi').animate({
					scrollTop : $(el).offset().top - 120
				},300,function(){
					$(el).focus()
				});
				key = false;
				return false;
			}
		});
		if(!key) return;
		$data = $('#postForm').serialize();
		$.ajax({
			type: "POST",
			url: 'admin/api/saveBookTashrihi/',
			dataType: "json",
			data: $data,
			success: function (data) {
				alert(data.msg);
				if(!data.status){
					$('.close-popup').click();
					location.reload();
				}
			}
		});
		/*
		*/
	}
	function DeleteTashrihiQuestions(){
		if(!confirm("آیا مطمئن هستید که می خواهید کلیه سوالات تشریحی را حذف نمایید"))
			return;
		$data = $('#postForm').serialize();
		$.ajax({
			type: "POST",
			url: 'admin/api/deleteBookTashrihi/',
			dataType: "json",
			data: $data,
			success: function (data) {
				alert(data.msg);
				if(!data.status){
					$('.close-popup').click();
					location.reload();
				}
			}
		});
		/*
		*/
	}
//=============================================
	function showDownloaded(btn, id , c) {
		if(c == 0)
			return;
        var $html = $('<div/>');
        $html.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($html);
        $.ajax({
            type: "POST",
            url: 'admin/api/getBookPayment/' + id,
            dataType: "json",
            success: function (data) {

                if (data == "login") {
                    popupScreen('');
                    login(function () {
                        edit_user(btn, id)
                    });
                    return;
                }

                if (!data.done) {
                    $html.html('<h3 class="text-warning text-center">' + data.msg + '</h3>');
                    return;
                }
                var $view = $('.view-peyorder').clone(true);
				var price = $('#price_'+id).html();
				var mainprice = price.length?parseInt(price.replace(/,/g,'')):0;
				$view.find('.displaynametag').html($('#book_'+id).html());
				$view.find('.mobilebody').html("");
				for(i=0;i < data.result.length;i++){
					el = data.result[i];
					fprice= parseInt(el["fprice"]);
					factorid = el["factorid"];
					code = el["code"];
					percent = el["percent"];
					fee = el["fee"];
					note = el["note"];
					cdate = el["cdate"];
					state = el["state"];
					price = mainprice;
					if(percent && percent.length){
						percent = parseInt(percent);
						price= mainprice - parseInt((percent*mainprice)/100);
					} else {
						if(fee && fee.length){
							fee = parseInt(fee);
							price=mainprice - fee;
							if(price < 0)
								price = 0;
						}
					}
					price = fprice?price:0;
					html = 
					'<tr id="mobile_'+el.id+'">'+
						'<th>'+(i+1)+'</th>'+
						'<th>'+factorid+'</th>'+
						'<th>'+cdate+'</th>'+
						'<th>'+code+'</th>'+
						'<th>'+mainprice+'</th>'+
						'<th>'+price+'</th>'+
						'<th>'+state+'</th>'+
						'<th>'+note+'</th>'+
					'</tr>';
					html = html.replace(/null/g,'');
					$view.find('.mobilebody').append(html);
                }

                $html.html($view);
            },
            error: function () {
                $html.html('<h3 class="text-warning text-center">Conection Error</h3>');
            }
        });
    }//Alireza Balvardi
//=============================================
</script>	