$(function () {
    $('.change').click(function () {
        //更改密碼页面
        var changeCodePage = `<form class="form-horizontal">
							    <div class="form-group">
								<div class="col-sm-12">
							    	<input type="password" class="form-control" id="oldCode" placeholder="舊密碼">
							    </div>
							 </div>
							 <div class="form-group">
							 	<div class="col-sm-12">
							 		<input type="password" class="form-control" id="newCode" placeholder="新密碼">
							 	</div>
							 </div>
							 <div class="form-group">
							 	<div class="col-sm-12">
							 		<input type="password" class="form-control" id="sureCode" placeholder="確認新密碼">
							 	</div>
							 </div>
							 <div class="form-group">
							 	<div class="col-sm-12">
							 		<input type="button" class="form-control text-center submit"  id="changeCodeSubmit" value="完成">
							 	</div>
							 </div>
							</form>`;
        //更改手機號页面
        var phonePage = `<form class="form-horizontal">
					        <div class="form-group">
					        	<div class="col-sm-12">
					            	<input type="text" disabled class="form-control" id="phone" >
					            </div>
					         </div>
					         <div class="form-group">
					         	<div class="col-sm-12">
					         		<input type="text" class="form-control" id="Verification-Code" placeholder="驗證碼">
					         		<input type="button"  class="form-control text-center getCode"  id="message" value="獲取驗證碼">               
					         	</div>
					         </div>
					         <div class="form-group">
					         	<div class="col-sm-12">
					         		<input type="text" class="form-control" id="newPhone" placeholder="新手機號碼">
					         	</div>
					         </div>
					         <div class="form-group">
					         	<div class="col-sm-12">
					         		<input type="text" class="form-control" id="newVerification-Code" placeholder="驗證碼">
					         		<input type="button" class="form-control text-center getCode" id="newmessage" value="獲取驗證碼">
					         	</div>
					         </div>
					         <div class="form-group">
					         	<div class="col-sm-12">
					         		<input type="button" class="form-control text-center submit"  id="newPhoneSubmit" value="完成">
					         	</div>
					         </div>
					     </form>`;
        //綁定手機页面
        var newPhonePage = `<form class="form-horizontal">
						        <div class="form-group">
						        	<div class="col-sm-12">
						            	<input type="text" class="form-control" id="newPhone" placeholder="手機號碼">
						            </div>
						        </div>
						        <div class="form-group">
						        	<div class="col-sm-12">
						            	<input type="text" class="form-control" id="Verification-Code" placeholder="驗證碼">
						            	<input type="button" class="form-control text-center getCode"  id="newmessage" value="獲取驗證碼"> 
						            </div>
						        </div>
						        <div class="form-group">
						        	<div class="col-sm-12">
						            	<input type="button" class="form-control text-center submit" id="phoneSubmit" value="完成">
						            </div>
						        </div>
						     </form>`;
        //更改郵箱页面
        var emailPage = `<form class="form-horizontal">
						        <div class="form-group">
						        	<div class="col-sm-12">
						        		<input type="email" disabled class="form-control" id="email-code"  value=email>
						        	</div>
						        </div>
						        <div class="form-group">
						        	<div class="col-sm-12">
						            	<input type="text" class="form-control" id="Verification-Code" placeholder="驗證碼">
						            	<input type="button" class="form-control text-center getCode"  id="email_message" value="獲取郵箱驗證碼"> 
						            </div>
						        </div>
							    <div class="form-group">
							        <div class="col-sm-12">
							            <input type="email" class="form-control" id="newEmail" placeholder="新郵箱地址">
							        </div>
							    </div>
							    <div class="form-group">
							        <div class="col-sm-12">
							            <input type="text" class="form-control" id="newVerification-Code" placeholder="驗證碼">
							            <input type="button" class="form-control text-center getCode"  id="new_email_message" value="獲取郵箱驗證碼">
							        </div>
							    </div>
							    <div class="form-group">
							        <div class="col-sm-12">
							            <input type="button" class="form-control text-center submit" id="emailSubmit" value="完成">
							        </div>
							    </div>
						   </form>`;
        //綁定資金密碼页面
        var crashPage = `<form class="form-horizontal">
						        <div class="form-group">
						        	<div class="col-sm-12">
						        		<input type="password" class="form-control" id="crash-code" placeholder="資金密碼">
						        	</div>
						        </div>
						        <div class="form-group">
						        	<div class="col-sm-12">
						        		<input type="password" class="form-control" id="code-sure" placeholder="確認資金密碼">
						        	</div>
						        </div>
						        <div class="form-group">
						        	<div class="col-sm-12">
						        		<input type="text" disabled class="form-control" id="phone" placeholder="手機號碼">
						        	</div>
						        </div>
						        <div class="form-group">
						        	<div class="col-sm-12">
						        		<input type="text" class="form-control" id="Verification-Code" placeholder="驗證碼">
						        		<input type="button" class="form-control text-center getCode" id="crash_message" value="獲取驗證碼">
						        	</div>
						        </div>
						        <div class="form-group">
							        <div class="col-sm-12">
							            <input type="button" class="form-control text-center submit" id="crashSubmit" value="完成">
							        </div>
							    </div>
						   </form>`;
        var content = $(this).data('id');
        var phone = $(this).attr('data-info');
        if (content == 2) {
            $('.modal-title').html('修改登錄密碼');
            $('.modal-body').html(changeCodePage);
        }
        else if (content == 1 && phone !== '') {
            $('.modal-title').html('修改手機號');
            $('.modal-body').html(phonePage);
            $('#phone').val(phone);
        }
        else if (content == 1 && phone == '') {
            $('.modal-title').html('綁定手機號');
            $('.modal-body').html(newPhonePage);
        }
        else if (content == 4) {
            $('.modal-title').html('設置資金密碼');
            var phone = $(this).attr('data-phone');
            var email = $(this).attr('data-email');
            $('.modal-body').html(crashPage);
            if (phone == '') {
                $('#phone').val(email);
                $('#crash_message').val('獲取郵箱驗證碼');
            } else {
                $('#phone').val(phone);
            }
        }
        else if (content == 3) {
            $('.modal-title').html('綁定登錄郵箱');
            var email = $(this).attr('data-info');

            $('.modal-body').html(emailPage);
            $('#email-code').val(email);
        }
    });

    $('.modal-body').on('click', '#changeCodeSubmit', function () {  //確認更换密碼
        var code = $('#oldCode').val();
        var newCode = $('#newCode').val();
        var sureCode = $('#sureCode').val();
        if (code == '') {
            layer.tips('舊密碼不能為空', $('#oldCode'), {
                tips: [1, '#d9534f']
            });
            return false;
        } else if (newCode == '') {
            layer.tips('新密碼不能為空', $('#newCode'), {
                tips: [1, '#d9534f']
            });
            return false;
        } else if (sureCode == '') {
            layer.tips('確認密碼不能為空', $('#sureCode'), {
                tips: [1, '#d9534f']
            });
            return false;
        } else if (sureCode !== newCode) {
            layer.tips('密碼不一致', $('#sureCode'), {
                tips: [1, '#d9534f']
            });
            return false;
        }
        commitPost('takeSetLoginPwd', {newPassword: newCode, oldPassword: code});

    }).on('click', '#crashSubmit', function () {				//確認資金密碼
        var phone = $('#phone').val();
        var crash_code = $('#crash-code').val();
        var codesure = $('#code-sure').val();
        var Verif_code = $('#Verification-Code').val();

        if (phone == '') {
            layer.tips('手機號不能為空', $('#phone'), {
                tips: [1, '#d9534f']
            });
            return false;
        } else if (crash_code == '') {
            layer.tips('密碼不能為空', $('#crash-code'), {
                tips: [1, '#d9534f']
            });
            return false;
        } else if (codesure == '') {
            layer.tips('確認密碼不能為空', $('#code-sure'), {
                tips: [1, '#d9534f']
            });
            return false;
        } else if (crash_code !== codesure) {
            layer.tips('密碼不一致', $('#code-sure'), {
                tips: [1, '#d9534f']
            });
            return false;
        } else if (Verif_code == '') {
            layer.tips('请输入驗證碼', $('#Verification-Code'), {
                tips: [1, '#d9534f']
            });
            return false;
        }
        commitPost('takeSetFundPwd', {fundPwd: crash_code, verifyCode: Verif_code});

    }).on('click', '#newPhoneSubmit', function () {					//確認更换新手機
        var code = $('#Verification-Code').val();
        var newCode = $('#newVerification-Code').val();
        var phone = $('#newPhone').val();
        if (code == '') {
            layer.tips('请输入驗證碼', $('#Verification-Code'), {
                tips: [1, '#d9534f']
            });
            return false;
        } else if (phone == '') {
            layer.tips('手機號碼不能為空', $('#newPhone'), {
                tips: [1, '#d9534f']
            });
            return false;
        } else if (newCode == '') {
            layer.tips('请输入驗證碼', $('#newVerification-Code'), {
                tips: [1, '#d9534f']
            });
            return false;
        }
        commitPost('takeSetPhone', {old_code: code, new_code: newCode, new_phone: phone});

    }).on('click', '#phoneSubmit', function () {					//綁定手機
        var code = $('#Verification-Code').val();
        var phone = $('#newPhone').val();

        if (phone == '') {
            layer.tips('手機號碼不能為空', $('#phone'), {
                tips: [1, '#d9534f']
            });
            return false;
        } else if (code == '') {
            layer.tips('请输入驗證碼', $('#Verification-Code'), {
                tips: [1, '#d9534f']
            });
            return false;
        }
        commitPost('takeSetPhone', {new_code: code, new_phone: phone});

    }).on('click', '#emailSubmit', function () {					//確認郵箱
        var code = $('#Verification-Code').val();
        var newCode = $('#newVerification-Code').val();
        var email = $('#newEmail').val();
        if (code == '') {
            layer.tips('请输入驗證碼', $('#Verification-Code'), {
                tips: [1, '#d9534f']
            });
            return false;
        }
        else if (email == '') {
            layer.tips('郵箱不能為空', $('#newEmail'), {
                tips: [1, '#d9534f']
            });
            return false;
        }
        else if (newCode == '') {
            layer.tips('请输入驗證碼', $('#newVerification-Code'), {
                tips: [1, '#d9534f']
            });
            return false;
        }
        commitPost('takeSetEmail', {old_code: code, new_code: newCode, new_email: email});
    }).on('click', '#newmessage', function () {					//向新手機发送驗證碼
        var type = "changePhone";
        var phone = $('#newPhone').val();
        if (phone == '') {
            layer.tips('手機號碼不能為空', $('#newPhone'), {
                tips: [1, '#d9534f']
            });
            return false;
        }
        messagePost('#newmessage', 'sendPhoneCode', {type: type, phone: phone});
    }).on('click', '#message', function () {					//发送手機驗證碼
        messagePost('#message', 'sendPhoneCode', {type: "changePhoneOld"});
    }).on('click', '#email_message', function () {				//向更换郵箱发送驗證碼
        messagePost('#email_message', 'sendEmailCode', {type: "changeEmailOld"});
    }).on('click', '#new_email_message', function () {			//向綁定郵箱发送驗證碼
        var type = "changeEmail";
        var email = $('#newEmail').val();
        if (email == '') {
            layer.tips('郵箱不能為空', $('#newEmail'), {
                tips: [1, '#d9534f']
            });
            return false;
        }
        messagePost('#new_email_message', 'sendEmailCode', {type: type, email: email});
    }).on('click', '#crash_message', function () {
        var phone = $('#crash').attr('data-phone');
        var email = $('#crash').attr('data-email');
        var type = "setFundPwd";
        if (phone == '') {
            var url = "sendEmailCode";
        } else {
            var url = "sendPhoneCode";
        }
        messagePost('#crash_message', url, {type: type});
    })

    function messagePost(id, url, type) {
        $(id).attr("disabled", true);
        $(id).css('background-color', '#e5e5e5');
        var time = 60;
        var timer = setInterval(function () {
            time -= 1;
            $(id).val(time + 's後重新發送');
        }, 1000);
        var timerOut=setTimeout(function () {
            clearInterval(timer);
            $(id).val('獲取驗證碼');
            $(id).css('background-color', '#fff');
            time = 60;
            $(id).removeAttr("disabled");
        }, 60000);
        $.post(url, type, function (res) {
            if (res.success) {
            } else {
                $(id).removeAttr("disabled");
                $(id).css('background-color', '#fff');
                clearInterval(timer);
                clearTimeout(timerOut);
                layer.tips(res.error, $(id), {
                    tips: [1, '#d9534f']
                });
            }
        }, 'json');
    }

    function commitPost(url, type) {
        $.post(url, type, function (res) {
            if (res.success) {
                $('#myModal').modal('hide');
                layer.msg(res.data, {
                    icon: 1,
                    time: 2000
                }, function () {
                    window.location.reload();
                });
            } else {
                layer.tips(res.error, $('.submit'), {
                    tips: [1, '#d9534f']
                });
            }
        }, 'json');
    }
})




