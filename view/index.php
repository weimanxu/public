<?php
	App::$view->title = '财酷ICO';
	//日历插件
	//App::$view->registerCss('/public/fullcalendar/fullcalendar.min.css');
	//App::$view->registerCss('/public/fullcalendar/fullcalendar.print.min.css', null, [
	//    'media' => 'print'
	//]);
	//App::$view->registerJs('/public/fullcalendar/moment.min.js');
	//App::$view->registerJs('/public/fullcalendar/fullcalendar.min.js');
	//App::$view->registerJs('/public/fullcalendar/locale-all.js');
	
	//行情
	App::$view->registerJs('/public/js/lib/pako.js');
	
	App::$view->registerJs('/public/js/main/index.js');

	App::$view->registerCss('/public/css/index.css');
?>
<div class="page-index">
	<div class="banner-pic-wrap">
        <div id="carousel" class="carousel slide" data-ride="carousel">
			<?php if(!empty($sponsor_info)){?>
			<ol class="carousel-indicators">
			<?php foreach ($sponsor_info as $k=>$v):?>
				<li data-target="#carousel" data-slide-to="<?= $k ?>" class="<?= $k == 0 ? 'active' : null ?>"></li>
			<?php endforeach;?>
            </ol>
            <div class="carousel-inner" role="listbox">
				<?php foreach ($sponsor_info as $k=>$v):?>
                <div class="item <?= $k == 0 ? 'active' : null ?>">
					<a href="<?= $v['url'] ?>"><img src="<?= $v['pic'] ?>" alt="<?= $v['name'] ?>"></a>
				</div>
				<?php endforeach;?>
			</div>
			<?php }?>
        </div>
	</div>
	<?php if(!empty($announ_info)){?>
    <div class="site-notice body-wrap">
		<div class="notice-list">
			<span class="notice-icon"></span>
			<div id="site-notice" class="carousel slide" data-ride="carousel">
				<div class="carousel-inner" role="listbox">
					<?php foreach ($announ_info as $k=>$v):?>
					<a href="/website/announcementDetail?id=<?= $v['id'] ?>" target="_blank" class="item <?= $k == 0 ? 'active' : null ?>"><?= $v['type'].'：'.$v['title'] ?></a>
					<?php endforeach;?>
				</div>
			</div>
		</div>
		<span class="notice-close">╳</span>
	</div>
	<hr>
	<?php }?>
	<?php if(!empty($goingList)){?>
	<div class="site-project body-wrap">
		<div class="head">
			<h2>進行中的項目</h2>
			<p>Ongoing project</p>
		</div>
		<div class="body">
			<?php foreach ($goingList as $res):?>
			<div class="item">
				<div class="item-banner">
					<a href="/project/detail?id=<?= $res['id'] ?>" target="_blank">
						<img src="<?= $res['project_banner'] ?>">
					</a>
					<div class="subscript">
						<img src="/public/images/subscript-processing.png">
					</div>
				</div>
				<div class="item-content">
					<div class="intro">
						<a href="/project/detail?id=<?= $res['id'] ?>" target="_blank"><?= $res['name'] ?></a>
						<p><?= $res['intro'] ?></p>
					</div>
					<div class="schedule">
						<?php if($res['btc_target']>0){?>
						<div class="schedule-type">
							<span class="btc-text"><?= 'BTC '.Format::formatNumber($res['btc_done'], 8, false, true, ',') ?></span>
							<span class="percentage"><?= Format::formatNumber($res['btc_done'] / $res['btc_target']* 100, 1) ?>%</span>
						</div>
						<div class="progress">
							<div class="progress-bar btc-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: <?= $res['btc_done'] / $res['btc_target']* 100 ?>%;"></div>
						</div>
						<?php }?>
						<?php if($res['eth_target']>0){?>
						<div class="schedule-type">
							<span class="eth-text"><?= 'ETH '.Format::formatNumber($res['eth_done'], 8, false, true, ',') ?></span>
							<span class="percentage"><?= Format::formatNumber($res['eth_done'] / $res['eth_target']* 100, 1) ?>%</span>
						</div>
						<div class="progress">
							<div class="progress-bar eth-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: <?= $res['eth_done'] / $res['eth_target']* 100 ?>%;"></div>
						</div>
						<?php }?>
					</div>
					<div class="foot">
						<div class="text-left">
							<h3>目標金額</h3>
							<p>
								<?php if($res['btc_target']>0){?>
								<?= 'BTC '.Format::formatNumber($res['btc_target'], 8, false, true, ',') ?>
								<?php }?>
								<?php if($res['btc_target']>0 && $res['eth_target']>0){?>
								<br>
								<?php }?>
								<?php if($res['eth_target']>0){?>
								<?= 'ETH '.Format::formatNumber($res['eth_target'], 8, false, true, ',') ?>
								<?php }?>
							</p>
						</div>
						<div>
							<h3>剩餘時間</h3>
							<p><?= Format::residueTime($res['endtime']) ?></p>
						</div>
						<div>
							<h3>總投資者</h3>
							<p><?= Format::formatNumber($res['btc_total']+$res['eth_total'], 8, false, true, ',') ?></p>
						</div>
					</div>
				</div>
			</div>
			<?php endforeach;?>
		</div>
	</div>
	<hr>
	<?php }?>
	<?php if(!empty($waitList)){?>
	<div class="site-project body-wrap">
		<div class="head">
			<h2>未開始的項目</h2>
			<p>project not started</p>
		</div>
		<div class="body">
			<?php foreach ($waitList as $res):?>
			<div class="item">
				<div class="item-banner">
					<a href="/project/detail?id=<?= $res['id'] ?>" target="_blank">
						<img src="<?= $res['project_banner'] ?>">
					</a>
					<div class="subscript">
						<img src="/public/images/subscript-notstarted.png">
					</div>
				</div>
				<div class="item-content">
					<div class="intro">
						<a href="/project/detail?id=<?= $res['id'] ?>" target="_blank"><?= $res['name'] ?></a>
						<p><?= $res['intro'] ?></p>
					</div>
					<div class="schedule">
						<?php if($res['btc_target']>0){?>
						<div class="schedule-type">
							<span class="btc-text"><?= 'BTC '.Format::formatNumber($res['btc_done'], 8, false, true, ',') ?></span>
							<span class="percentage"><?= Format::formatNumber($res['btc_done'] / $res['btc_target']* 100, 1) ?>%</span>
						</div>
						<div class="progress">
							<div class="progress-bar btc-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: <?= $res['btc_done'] / $res['btc_target']* 100 ?>%;"></div>
						</div>
						<?php }?>
						<?php if($res['eth_target']>0){?>
						<div class="schedule-type">
							<span class="eth-text"><?= 'ETH '.Format::formatNumber($res['eth_done'], 8, false, true, ',') ?></span>
							<span class="percentage"><?= Format::formatNumber($res['eth_done'] / $res['eth_target']* 100, 1) ?>%</span>
						</div>
						<div class="progress">
							<div class="progress-bar eth-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: <?= $res['eth_done'] / $res['eth_target']* 100 ?>%;"></div>
						</div>
						<?php }?>
					</div>
					<div class="foot">
						<div class="text-left">
							<h3>目標金額</h3>
							<p>
								<?php if($res['btc_target']>0){?>
								<?= 'BTC '.Format::formatNumber($res['btc_target'], 8, false, true, ',') ?>
								<?php }?>
								<?php if($res['btc_target']>0 && $res['eth_target']>0){?>
								<br>
								<?php }?>
								<?php if($res['eth_target']>0){?>
								<?= 'ETH '.Format::formatNumber($res['eth_target'], 8, false, true, ',') ?>
								<?php }?>
							</p>
						</div>
						<div>
							<h3>開始時間</h3>
							<p><?= date('Y-m-d H:i:s', $res['begintime'])?></p>
						</div>
						<div>
							<h3>總投資者</h3>
							<p><?= Format::formatNumber($res['btc_total']+$res['eth_total'], 8, false, true, ',') ?></p>
						</div>
					</div>
				</div>
			</div>
			<?php endforeach;?>
		</div>
	</div>
	<hr>
	<?php }?>
	<?php if(!empty($doneList)){?>
	<div class="site-project body-wrap">
		<div class="head">
			<h2>已結束的項目</h2>
			<p>Completed project</p>
		</div>
		<div class="body">
			<?php foreach ($doneList as $res):?>
			<div class="item">
				<div class="item-banner">
					<a href="/project/detail?id=<?= $res['id'] ?>" target="_blank">
						<img src="<?= $res['project_banner'] ?>">
					</a>
					<div class="subscript">
						<img src="/public/images/subscript-completed.png">
					</div>
				</div>
				<div class="item-content">
					<div class="intro">
						<a href="/project/detail?id=<?= $res['id'] ?>" target="_blank"><?= $res['name'] ?></a>
						<p><?= $res['intro'] ?></p>
					</div>
					<div class="schedule">
						<?php if($res['btc_target']>0){?>
						<div class="schedule-type">
							<span class="btc-text"><?= 'BTC '.Format::formatNumber($res['btc_done'], 8, false, true, ',') ?></span>
							<span class="percentage"><?= Format::formatNumber($res['btc_done'] / $res['btc_target']* 100, 1) ?>%</span>
						</div>
						<div class="progress">
							<div class="progress-bar btc-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: <?= $res['btc_done'] / $res['btc_target']* 100 ?>%;"></div>
						</div>
						<?php }?>
						<?php if($res['eth_target']>0){?>
						<div class="schedule-type">
							<span class="eth-text"><?= 'ETH '.Format::formatNumber($res['eth_done'], 8, false, true, ',') ?></span>
							<span class="percentage"><?= Format::formatNumber($res['eth_done'] / $res['eth_target']* 100, 1) ?>%</span>
						</div>
						<div class="progress">
							<div class="progress-bar eth-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: <?= $res['eth_done'] / $res['eth_target']* 100 ?>%;"></div>
						</div>
						<?php }?>
					</div>
					<div class="foot">
						<div class="text-left">
							<h3>目標金額</h3>
							<p>
								<?php if($res['btc_target']>0){?>
								<?= 'BTC '.Format::formatNumber($res['btc_target'], 8, false, true, ',') ?>
								<?php }?>
								<?php if($res['btc_target']>0 && $res['eth_target']>0){?>
								<br>
								<?php }?>
								<?php if($res['eth_target']>0){?>
								<?= 'ETH '.Format::formatNumber($res['eth_target'], 8, false, true, ',') ?>
								<?php }?>
							</p>
						</div>
						<div>
							<h3>剩餘時間</h3>
							<p>已結束</p>
						</div>
						<div>
							<h3>總投資者</h3>
							<p><?= Format::formatNumber($res['btc_total']+$res['eth_total'], 8, false, true, ',') ?></p>
						</div>
					</div>
				</div>
			</div>
			<?php endforeach;?>
		</div>
	</div>
	<?php }?>
	<div class="site-commitment">
		<div class="commitment-content  body-wrap">
			<div class="head">
				<h2>為什麼選擇我們</h2>
				<p>Why choose us</p>
			</div>
			<div class="body">
				<div class="item">
					<div class="commitment-pic">
						<img src="/public/images/commitment01.png">
					</div>
					<div class="commitment-text">
						<h2>快速便捷</h2>
						<p>ICO平台交易快速</p>
						<p>用户体验流畅便捷</p>
					</div>
				</div>
				<div class="item">
					<div class="commitment-pic">
						<img src="/public/images/commitment02.png">
					</div>
					<div class="commitment-text">
						<h2>公平公正</h2>
						<p>ICO平台公平公开</p>
						<p>去中心化机会平等</p>
					</div>
				</div>
				<div class="item">
					<div class="commitment-pic">
						<img src="/public/images/commitment03.png">
					</div>
					<div class="commitment-text">
						<h2>一站式投資管理</h2>
						<p>各类操作透明可信</p>
						<p>真实可靠互相监督</p>
					</div>
				</div>
				<div class="item">
					<div class="commitment-pic">
						<img src="/public/images/commitment04.png">
					</div>
					<div class="commitment-text">
						<h2>專業的ICO服務</h2>
						<p>財酷ICO服務專業</p>
						<p>使用安全用户放心</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="site-partner body-wrap">
		<div class="head">
			<h2>合作夥伴</h2>
			<p>Cooperative partner</p>
		</div>
		<div class="body">
			<a href="https://www.hebi.com" target="_blank" class="partner-hebi"></a>
			<a href="http://mvs.live" target="_blank" class="partner-yuanjie"></a>
			<a href="https://www.biduobao.com" target="_blank" class="partner-biduobao"></a>
			<a href="https://www.szzc.com" target="_blank" class="partner-haifengteng"></a>
			<a href="http://www.iconlive.net" target="_blank" class="partner-icolive"></a>
			<a href="http://www.jinse.com" target="_blank" class="partner-jinsecaijing"></a>
		</div>
	</div>
</div>