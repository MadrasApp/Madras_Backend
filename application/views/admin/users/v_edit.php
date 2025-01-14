<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="row">
	<form class="clearfix" action="admin/api/addUser" method="post">
		<div class="col-sm-6">
			<div class="row">
				<style scope>.editable-img img {max-width: 120px !important;max-height: 120px !important;}</style>
				<div class="box-content media-ap col-sm-6">
					<div class="editable-img text-center">
						<p>تصویر پروفایل</p>
						<span class="media-ap-data replace" data-thumb="thumb150" style="display: inline-block">
							<img class="convert-this img-responsive update-avatar" src="">
						</span>
						<div class="plus add-img add-thumb-img-2" onclick="media('img,1',this,function(){ $('.add-thumb-img-2').hide() })" style="margin:15px">
						</div>
						<div class="form-ap-data" style="display:none"></div>
						<input type="hidden" class="media-ap-input update-el" name="avatar">
					</div>
				</div>
			
				<div class="col-md-6">
					<div class="form-group">
						<p>نام کاربری</p>
						<input type="text" name="username" class="form-control update-el">
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="form-group">
						<p>نام </p>
						<input type="text" name="displayname" class="form-control update-el">
					</div>
				</div>
				
				<!--<div class="col-md-6">
					<div class="form-group">
						<p>نام</p>
						<input type="text" name="name" class="form-control update-el">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<p>نام خانوادگی</p>
						<input type="text" name="family" class="form-control update-el">
					</div>
				</div>-->
				
				<div class="col-md-6">
					<div class="form-group">
						<p>ایمیل</p>
						<input type="email" name="email" class="form-control update-el en">
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="form-group">
						<p>موبایل</p>
						<input type="text" name="tel" class="form-control update-el en">
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="form-group">
						<p>کد ملی</p>
						<input type="text" name="national_code" class="form-control update-el en">
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="form-group">
						<p>تاریخ تولد</p>
						<input type="text" name="birthday" class="form-control update-el en">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<p>کشور</p>
						<input type="text" name="country" class="form-control update-el">
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="form-group">
						<p>استان</p>
						<input type="text" name="state" class="form-control update-el">
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="form-group">
						<p>شهر</p>
						<input type="text" name="city" class="form-control update-el">
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="form-group">
						<p>کد پستی</p>
						<input type="text" name="postal_code" class="form-control update-el en">
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="form-group">
						<p>سن</p>
						<input type="text" name="age" class="form-control update-el en"/>
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="form-group">
						<p>جنسیت</p>
						<select class="form-control update-el" name="gender">
							<option value="1">مرد</option>
							<option value="0">زن</option>
						</select>
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="form-group">
						<p>رمز</p>
						<input type="text" name="password" class="form-control" placeholder="برای تغییر نکردن رمز، این فیلد را خالی بگذارید">
					</div>
				</div>
				
				
			</div>

		</div>
		<div class="col-sm-6">
		
			<div class="row">
				
				<?php if ($this->user->can('edit_user_role')): ?>
					<div class="col-md-6">
						<div class="form-group">
							<p>نقش</p>
							<select class="form-control update-el" name="level">
								<?php $levels = $this->db->where('level_key', 'level_name')->get('user_level')->result() ?>
								<?php foreach ($levels as $level): ?>
									<option value="<?php echo  $level->level_id ?>"><?php echo  $level->level_value ?></option>
								<?php endforeach ?>
								<option value="user">کاربر</option>
								<option value="admin">ادمین</option>
								<option value="teacher">استاد</option>
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<p>پشتیبان</p>
							<select class="form-control update-el" name="support">
								<option value="1">بلی</option>
								<option value="0">خیر</option>
							</select>
						</div>
					</div>
				<?php endif ?>
				
				<!--<div class="box-content media-ap col-sm-8">
					<div class="editable-img ">
						<p>تصویر کاور</p>
						<span class="media-ap-data replace" data-thumb="thumb300" style="display: inline-block">
							<img class="convert-this img-responsive update-cover" src="">
						</span>
						<div class="plus add-img add-thumb-img-1"
							 onclick="media('img,1',this,function(){ $('.add-thumb-img-1').hide() })" style="display:none;margin:15px">
						</div>
						<div class="form-ap-data" style="display:none"></div>
						<input type="hidden" class="media-ap-input update-el" name="cover">
					</div>
				</div>-->
				<div class="col-sm-6">
					<div class="form-group">
						<p>وضعیت حساب کاربری</p>
						<select name="active" class="form-control update-el" onchange="$(this).parent().next().css('display',$(this).val()==1 ?'none':'block')">
							<option value="1">فعال</option>
							<option value="0">مسدود</option>
						</select>
					</div>
					<div class="form-group">
						<p>دلیل مسدود کردن حساب کاربری</p>
						<textarea type="text" name="pending_reason" class="form-control update-el" rows="4" style="height:75px"></textarea>
					</div>
				</div>
				
				<div class="col-md-12">
					<div class="form-group">
						<p>آدرس</p>
						<textarea name="address" class="form-control update-el"></textarea> 
					</div>
				</div>
				
				
				<div class="clearfix"></div>
			</div>

			<hr/>
			
			<div class="form-group">
				<button type="button" class="btn btn-primary btn-block btn-lg sample-send"><i class="fa fa-check-circle"></i> <span>ذخیره</span></button>
			</div>
			<div class="ajax-result" style="margin-bottom: 20px;"></div>
		</div>
	</form>
</div>
<script type="text/javascript">

    $(document).ready(function () {
		jQuery('.sample-send').on('click', function () {
			addUser(this);
		});
		function addUser(btn){
			$(btn).addClass('l w h6');
	
			var form = $(btn).closest('form');
	
			/*$(form).find('select').add($(form).find('input')).each(function(i,el){
				if( $(el).val() == $(el).data('prevdata') ) $(el).attr('disabled', true);
			});*/
	
			var data = $(form).serialize();
	
			$.ajax({
				type: "POST",
				url: 'admin/api/addUser',
				data: data,
				dataType: "json",
				success: function (data) {
					if (data == "login")
					{
						login(function () {
							addUser(btn)
						});
						return;
					}
					else
					{
						$(btn).closest('form').find('.ajax-result').html(get_alert(data));
					}
					$(btn).removeClass('l w');
					if(data.done)
						window.setTimeout('location.reload();',1000);
				},
				error: function (a,b,c) {
					$(btn).removeClass('l w');
					notify('خطا در اتصال', 2);
				}
			});
		}
    });
</script>
