<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <title><?= App::$view->title?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <?= App::$view->head()?>
    <?= App::$view->cssHtml('/public/css/common/font-awesome.min.css');?>
    <?= App::$view->cssHtml('/public/css/common/base.css');?>
    <?= App::$view->loadStyles()?>
</head>
<body class="page-logreg">
	<div class="page-logreg-wrap box">
		<?= $content?>
	</div>
    <?= App::$view->jsHtml(['/public/js/lib/jquery-1.12.4.min.js', '/public/js/lib/tool.js', '/public/js/lib/layer/layer.js']);?>
    <?= App::$view->loadScripts()?>
    <script>
        var _hmt = _hmt || [];
        (function() {
          var hm = document.createElement("script");
          hm.src = "https://hm.baidu.com/hm.js?6b5e7721c18eaeb46e4c93052338a15d";
          var s = document.getElementsByTagName("script")[0]; 
          s.parentNode.insertBefore(hm, s);
        })();
    </script>
</body>
</html>