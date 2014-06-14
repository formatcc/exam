jQuery.msg = function(){
	var url = "?m=msg&a=check_new";
	var timeout = 0;
	var interval = 3000; //时间间隔
	check_new = function(){
		$.ajax({
			timeout:timeout,
			url: url,
			success: function($data){
				alert($data);
				setTimeout(check_new, interval);
			},
			error: function(){
				setTimeout(check_new, interval);
			}
		});
	}
	
//	check_new()
}