<?php
	$this->load->helper('inc');
?>
<form method="post" action="admin/api/addAzmoon/" enctype="multipart/form-data">
	<div class="text-center">
		<h2>ثبت فایل اکسل آزمونهای تستی و تشریحی</h2>
	</div>
	<hr />
	<div class="form-group col-md-4">
	  <input type="file" name="excelfile" id="excelfile" class="input-file" accept="application/msexcel" />
	  <label for="excelfile" class="btn btn-tertiary js-labelFile btn-danger" id="formfile">
		<i class="icon fa fa-check"></i>
		<span class="js-fileName">انتخاب فایل اکسل</span>
	  </label>
	</div>
	<div class="form-group col-md-4">
		<label class="col-md-4 h3" for="exceltype">نوع سوالات : </label>
		<select name="exceltype" id="exceltype" class="form-control col-md-8">
			<option value="tests">تستی</option>
			<option value="tashrihi">تشریحی</option>
		</select>
	</div>
	<div class="col">
		<input type="submit" class="btn btn-success" value="افزودن سوالات" />
	</div>
</form>

<style>
.btn-tertiary {
  padding: 0;
  line-height: 40px;
  width: 300px;
  margin: auto;
  display: block;
  border: 2px solid #555;
}
.btn-tertiary:hover, .btn-tertiary:focus {
  color: #888888;
  border-color: #888888;
}

/* input file style */
.input-file {
  width: 0.1px;
  height: 0.1px;
  opacity: 0;
  overflow: hidden;
  position: absolute;
  z-index: -1;
}
.input-file + .js-labelFile {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  padding: 0 10px;
  cursor: pointer;
}
.input-file + .js-labelFile .icon:before {
  content: "\f093";
}
.input-file + .js-labelFile.has-file .icon:before {
  content: "\f00c";
  color: #5AAC7B;
}

</style>
<script>
$("#excelfile").change(function(){
	$("#formfile").addClass("btn-success").removeClass("btn-danger");
});
</script>