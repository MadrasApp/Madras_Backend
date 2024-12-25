$(document).ready(function(e) {
	


});

function ajax(op,btn,data){
	
	
	ajaxLoading(1,'&nbsp;&nbsp; در حال انجام عملیات ... ',btn);
	
	if(typeof(data)=='undefined')
	var data = $(btn).closest("form").serialize();
	
	$.ajax({
		url:"ajax.php?op="+op,
		type:"POST",
		data:data,
		success: function(result){
			$(btn).parent().find(".ajax-result").html(result);
		}
		,fail:function(){ajax_fail(btn)}
		,error:function(){ajax_fail(btn)}
	});
}

