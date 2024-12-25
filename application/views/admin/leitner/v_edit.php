<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
		echo "<pre>";
		print_r();
		echo "</pre>";
		die;
*/
?>

<style>.box:not(:first-child){margin: 30px 0;}
div.book-save.leitner-save {
	position: absolute;
	left: 0;
	bottom: 15px;
}
</style>
<script src="<?php echo  base_url() ?>/js/_admin/leitner.js"></script>

<div id="result"></div>


<div style="width:90%;padding-left:10px;margin:auto;">
	
	<h2><?php echo  $_title ?></h2>
	<p></p>
	<div class="box">
		<div class="box-title"><i class="fa fa-leitner"></i> متن </div>
		<div class="box-content" style="padding:0 15px">
					<form class="has-feedback book-part leitner-part row <?php echo  @$leitner->image != '' ? 'has-image':'' ?>">
						<div class="part-content col-xs-12" style="padding:2px;">
							<table class="table">
							<tr>
								<td width="20%">گروه بندی</td>
								<td>
								<select id="catid" class="input small" name="catid">
									<option value="0"<?php echo  intval(@$leitner->catid)==0?" selected":"";?>>نامشخص</option>
									<option value="1"<?php echo  intval(@$leitner->catid)==1?" selected":"";?>>یادداشت</option>
									<option value="2"<?php echo  intval(@$leitner->catid)==2?" selected":"";?>>لغت</option>
									<option value="3"<?php echo  intval(@$leitner->catid)==3?" selected":"";?>>سوال تستی</option>
									<option value="4"<?php echo  intval(@$leitner->catid)==4?" selected":"";?>>سوال تشریحی</option>
								</select>
								</td>
							</tr>
							<tr>
								<td><label for="title">متن : </label></td>
								<td><input type="text" name="title" class="form-control part-text" placeholder="متن" value="<?php echo  @$leitner->title ?>" /></td>
							</tr>
							<tr>
								<td><label for="title">توضیحات : </label></td>
								<td><textarea name="description" id="description" class="form-control part-text" placeholder="توضیحات"><?php echo  @$leitner->description ?></textarea></td>
							</tr>
							</table>
							<input name="master"	class="part-master" type="hidden"	value="1" />
							<input name="id"    	class="part-id"		type="hidden" value="<?php echo  intval(@$leitner->id);?>">
						</div>
						<div class="book-save leitner-save" title="ذخیره"></div>
					</form>
		</div>
	</div>
	<div class="box">
		<div class="box-title"><i class="fa fa-check"></i>ذخیره</div>
		<div class="box-content">
			<div class="save-content">
				<button class="btn btn-success" onClick="saveAll(this)">ذخیره تغییرات</button>
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
    var leitnerdata = <?php echo  json_encode($leitner) ?>, productdata = {};

    $(document).ready(function (e) {
	});
	
    window.onbeforeunload = function () {
		if($('.leitner-save.changed').length)
        return "بعضی از قسمتها ذخیره نشده اند. میخواهید خارج شوید؟";
    }
</script>