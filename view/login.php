<?php
	App::$view->title = '財酷ICO - 登錄';
	
	App::$view->registerJs('/public/js/main/login.js');
?>
<div class="log-wrap">
    <h1>登錄</h1>
    <h4>請輸入您的用戶名或郵箱登錄到財酷ICO.</h4>
    <form>
    	<div class="item">
    		<div class="key">用戶名</div>
    		<div class="value">
    			<input type="text" name="username" id="username" placeholder="用戶名或郵箱" class="inp inp-block" maxlength="32" />
    		</div>
    	</div>
    	<div class="item">
    		<div class="key">密碼</div>
    		<div class="value">
    			<input type="password" name="password" id="password" placeholder="密碼" class="inp inp-block" maxlength="32" />
    		</div>
    	</div>
    	<div class="item-error" id="errorTip"></div>
    	<div class="item mt30">
    		<a href="javascript:;" id="loginBtn" class="btn btn-major btn-block">登錄</a>
    	</div>
    	<div class="item-p">
    		<p><a href="/forget" class="link-btn">忘記密碼？</a></p>
    		<p>還沒有賬戶？</p>
    	</div>
    	<a href="/register" class="btn btn-hollow btn-block">創建一個賬戶</a>
    </form>
</div>
<div class="log-verify-wrap">
	<h1>發送成功</h1>
    <h4>一封確認郵件已經發往您的郵箱，請點擊內含的鏈接完成註冊.</h4>
    <a id="reLogin" href="javascript:;" class="btn btn-hollow btn-block mt30">登錄</a>
</div>
<div class="cpy">©2017 CAIKUICO.com. All Rights Reserved</div>
