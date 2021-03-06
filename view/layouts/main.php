<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <title><?= App::$view->title?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="renderer" content="webkit"> 
    <?= App::$view->head()?>
    <?= App::$view->cssHtml('/public/css/common/bootstrap.min.css');?>
    <?= App::$view->cssHtml('/public/css/common/font-awesome.min.css');?>
    <?= App::$view->cssHtml('/public/css/common/base.css');?>
    <?= App::$view->loadStyles()?>
</head>
<body class="<?=isset($bodyClass) ? $bodyClass : ''?>">
	<div class="wrapper">
        <div class="banner-market clearfix <?= App::$request->getOriginPath() == '/' ? null : 'hide'?>">
            <div class="fl">
                <span class="currency">BTC/CNY: <span class="currency-price" id="btcPrice"></span></span>
                <span class="currency">LTC/CNY: <span class="currency-price" id="ltcPrice"></span></span>
                <span class="currency">ETH/CNY: <span class="currency-price" id="ethPrice"></span></span>
            </div>
            <div class="fr">
                <a href="javascript:;">
                    <img src="/public/images/flag-china.png">
                </a>
                <a href="javascript:;">
                    <img src="/public/images/flag-hongkong.png">
                </a>
            </div>
        </div>
        <div class="navbar_border">
            <div class="header navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="/" class="navbar-brand <?= App::$request->getOriginPath() == '/' ? 'index-logo' : 'logo'?>"></a>
            </div>
            <!-- 手机端下拉导航 -->
            <div class="collapse navbar-collapse in" id="caiku">
                <ul class="nav navbar-nav navbar-right">
                <?php if(!empty($userInfo)){?>
                    <li class="account">
                        <a href="/user/home" trigger="hover" data-toggle="popover" data-placement="bottom" title="账户余额" data-content="BTC : <?=$userInfo['btc_usable'] ?> / ETH : <?=$userInfo['eth_usable'] ?>">
                            <i class="fa fa-user"></i>
                            <?= $userInfo['email'] ?>
                        </a>
                    </li>
                    <li>
                        <a href="/logout" class="logout">
                            <i class="fa fa-sign-out"></i>
                            註銷
                        </a>
                    </li>
                <?php }else{?>
                    <li>
                        <a href="/login" class="login">
                            <i class="fa fa-sign-out"></i>
                            登錄
                        </a>
                    </li>
                <?php }?>
                </ul>
                <ul class="nav navbar-nav">
                    <li class="<?= isset($navProject) && $navProject  ? 'active' : '';?> special" >
                        <a href="/project/list">
                            <i class="fa fa-bars"></i>
                            ICO項目
                        </a>
                    </li>
                    <?php if(!empty($userInfo)){?>
                    <li class="<?= isset($navUser) && $navUser  ? 'active' : '';?> pcshow">
                        <a href="/user/home">
                            <i class="fa fa-bars"></i>
                            個人中心
                        </a>
                    </li>
                    <li class="<?= isset($navUser) && $navUser  ? 'active' : '';?> phoneshow">
                        <a href="/user/home" class="dropdown-toggle" aria-expanded="false" data-toggle="dropdown">
                            <i class="fa fa-bars"></i>
                            個人賬戶
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="/user/home">賬戶總覽</a></li>
                            <li><a href="/user/identity">實名認證</a></li>
                            <li><a href="/user/security">安全設置</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-bars"></i>
                            項目管理
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="/user/support">我支持的項目</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-bars"></i>
                            資金管理
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="/fund/recharge">資金充值</a></li>
                            <li><a href="/fund/withdrawal">資金提現</a></li>
                            <li><a href="/fund/capital">資金明細</a></li>
                        </ul>
                    </li>
                    <?php }?>
                </ul>
            </div>
        </div>
		<div class="main">
			<?= $content?>
        </div>
        <div class="index-foot <?= App::$request->getOriginPath() == '/' ? null : 'hide'?>">
            <div class="foot-wrap clearfix">
                <div class="about-us">
                    <h2>關於我們</h2>
                    <p>財酷ICO所提供的各項服務的所有權和運營權均歸財酷ICO網站所有，財酷ICO用戶通過訪問或者使用本網站，即表示接受同意網站協議的所有條件和條款。</p>
                </div>
                <div class="connect-us">
                    <h2>聯繫我們</h2>
                    <div class="connect-us-icon">
                        <span class="connect-hover">客服QQ： 2081079436</span>
                        <span class="connect-hover mg-l56">客服WeChat： caiku321</span>
                    </div>
                    <div class="connect-us-icon">
                        <span class="connect-hover">合作QQ： 85267589</span>
                    </div>
                </div>
            </div>            
        </div>
		<div class="footer <?= App::$request->getOriginPath() == '/' ? 'index-foot-text' : null?>">©2017 CAIKUICO.com. All Rights Reserved 
		| <a href="http://www.miitbeian.gov.cn/publish/query/indexFirst.action" target="_blank"  style="color: #595959;">粤ICP备17085171号-2</a>
		| 法律支持：埃森科爾(深圳)法律諮詢公司
		</div>
		<div class="sliderbar">
        	<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin=2081079436&amp;site=qq&amp;menu=yes">
            	<div class="item qq">
                    <div class="cover"></div>
            		<div class="inner">
                		<img src="/public/images/qq.png">
                		<p>聯繫客服</p>
            		</div>
            	</div>
        	</a>
        	<a target="_blank" href="//shang.qq.com/wpa/qunwpa?idkey=7c84329425a9106f34c1d3499f72d9e8f8f93db401fa3b13975c092674969b23">
            	<div class="item qq-group">
            		<div class="cover"></div>
            		<div class="inner">
                		<img src="/public/images/qq-group.png">
                		<p>官方Q群</p>
            		</div>
            	</div>
            </a>
            <a href="#">
            	<div class="item qq-group">
            		<div class="cover"></div>
            		<div class="inner">
                		<img src="/public/images/back-top.png">
                		<p>返回頂部</p>
            		</div>
            	</div>
        	</a>
        </div>
   	</div>
    <?= App::$view->jsHtml(['/public/js/lib/jquery-1.12.4.min.js', '/public/js/lib/tool.js', '/public/js/lib/layer/layer.js', '/public/js/lib/bootstrap.min.js']);?>
    <?= App::$view->loadScripts()?>
    <script>
        var _hmt = _hmt || [];
        (function() {
          var hm = document.createElement("script");
          hm.src = "https://hm.baidu.com/hm.js?6b5e7721c18eaeb46e4c93052338a15d";
          var s = document.getElementsByTagName("script")[0]; 
          s.parentNode.insertBefore(hm, s);
        })();
        //手机页面导航栏按钮
        $('button[data-toggle="collapse"]').click(function(){
            var asideleft = $("#caiku").offset().left;
            if(asideleft==document.body.clientWidth){
                $("#caiku").addClass('left-in');
            }else{
                $("#caiku").removeClass('left-in');
            }
        });
    </script>
</body>
</html>