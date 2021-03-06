$(function(){
	$('input, textarea').bind('focus', function(){
		$('#errorTip').hide();
		$(this).removeClass('show-error');
	});
	
	$('#registerBtn').bind('click', function(){
		if($(this).hasClass('disable')){
			return ;
		}
		var email = $.trim($('#email').val()),
			password = $.trim($('#password').val()),
			repassword = $.trim($('#repassword').val());
		
		if(email == ''){
			showError('email', '登錄郵箱不能為空');
			return ;
		}
		if(password == ''){
			showError('password', '登錄密碼不能為空');
			return ;
		}
		if(password != repassword){
			showError('repassword', '兩次輸入的密碼不一致');
			return ;
		}
		var index = layer.load(1, {
			shade: [0.1,'#fff']
		});
		
		$.post('/index/takeRegister', {
			email : email,
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
			//注册成功，引导用户前往邮箱验证
			$('.reg-wrap').hide();
			$('.reg-success-wrap').show();
		}, 'json');
		
	});
	
	//显示错误
	function showError(id, error){
		if(id){
			$('#' + id).addClass('show-error');
		}
		$('#errorTip').html(error).show();
	}
});