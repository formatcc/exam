function check_form(obj){
	var $this = $(obj);
	var user = $("#name").val();
	var password = $("#password").val();
	var url = $this.attr('action');
	var random;
	
	$.post(url,{random:1},function(data){
		if(data.error){
			$(".error").html(data.msg);
		}else{
			$.post(url,{name:user,password:$.md5($.md5(password)+data.code)},function(data){
				if(typeof data.error != 'undefined'){
					if(data.error){
						$(".error").html(data.msg);
					}else{
						location.href=data.forward;
					}
				}else{
					$('body').append(data);
				}
			},'json');
		}
	},'json');

	return false;
}