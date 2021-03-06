$(function(){
	$('input, textarea').bind('focus', function(){
		$('#errorTip').hide();
		$(this).removeClass('show-error');
	});
	
	$('#loginBtn').bind('click', function(){
		if($(this).hasClass('disable')){
			return ;
		}
		
		var username = $.trim($('#username').val()),
			password = $.trim($('#password').val());
		
		if(username == ''){
			showError('username', '用戶名不能為空');
			return ;
		}
		if(password == ''){
			showError('password', '登錄密碼不能為空');
			return ;
		}
		var index = layer.load(1, {
			shade: [0.1,'#fff']
		});
		
		$.post('/index/takeLogin', {
			username : username,
			password : password
		}, function(data){
			layer.close(index);
			if (!data.success){
				var errorArr = data.error.split('|');
				if(errorArr.length == 2){
					showError(errorArr[0], errorArr[1]);
				}else{
					showError(undefined, data.error);
				}
				return ;
			}
			window.location.href = data.data;
			
		}, 'json');
		
	});
	
	//重新发送验证邮件
	$('#errorTip').delegate('#reVerify', 'click', function(){
		var email = $(this).data('email');
		var index = layer.load(1, {
			shade: [0.1,'#fff']
		});
		
		$.post('/index/reSendRegisterEmail', {
			email : email
		}, function(data){
			layer.close(index);
			if (!data.success){
				var errorArr = data.error.split('|');
				if(errorArr.length == 2){
					showError(errorArr[0], errorArr[1]);
				}else{
					showError(undefined, data.error);
				}
				return ;
			}
			//重新发送邮件成功
			$('.log-wrap').hide();
			$('.log-verify-wrap').show();
		}, 'json');
	});
	
	//重新登录
	$('#reLogin').bind('click', function(){
		$('#errorTip').hide();
		$('.log-verify-wrap').hide();
		$('.log-wrap').show();
	});
	
	//显示错误
	function showError(id, error){
		if(id){
			$('#' + id).addClass('show-error');
		}
		$('#errorTip').html(error).show();
	}
});