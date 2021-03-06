<?php
	App::$view->title = '财酷ICO - 资金充值';
    App::$view->registerCss('/public/css/funds.css');
	App::$view->registerJs('/public/js/main/fund/funds.js');
?>
<div class="user-founds-wrap">
    <div class="user-center-head clearfix">
        <div class="left">
            <img src="/public/images/recharge-<?= $type?>.png">
        </div>
        <div class="left">
            <h2><?= strtoupper($type)?>充值</h2>
            <ul class="clearfix">
                <li class="active"><a href="/">主頁</a></li>
                <li><a>資金管理</a></li>
            </ul>
        </div>
    </div>
    <div class="founds-body">
        <ul class="founds-body-nav clearfix">
            <li><a class="<?= $type == 'btc' ? 'active' : ''?>" href="/fund/recharge?type=btc">BTC充值</a></li>
            <li><a class="<?= $type == 'eth' ? 'active' : ''?>" href="/fund/recharge?type=eth">ETH充值</a></li>
        </ul>
        <div class="notice">
            <h2>充值須知</h2>
            <p>1、禁止向<?= strtoupper($type)?>地址充值除<?= strtoupper($type)?>之外的資產，任何充入<?= strtoupper($type)?>地址的非<?= strtoupper($type)?>資產將不可找回。</p>
            <p>2、因<?= strtoupper($type)?>交易量大，網絡確認時間長，為了您的交易更快被確認，建議您增加網絡手續費。</p>
            <p>3、使用<?= strtoupper($type)?>地址充值需要1個網絡確認才能到賬，使用站內帳戶間轉帳無需網絡確認，可實时到帳。</p>
        </div>
        <div>
        	<?php if(App::loadConf('app/openRecharge')){?>
        	<!-- 开放充值 -->
        	<span class="address-title">充值地址：</span>
            <span class="address-content"><?= $addressInfo['address'] ?></span>
            <div class="qrcode">
                <img src="/fund/qrcode?type=<?= $type?>&address=<?= $addressInfo['address'] ?>" />
            </div>
        	<?php }else{?>
        	<!-- 关闭充值 -->
        	<div class="close-recharge-tip">當前系統已關閉<?= strtoupper($type)?>充值</div>
        	<?php }?>
        </div>
        <div class="record">
            <h2>充值記錄</h2>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="phone-hidden">充值地址</th>
                        <th>充值金額</th>
                        <th>到賬時間</th>
                        <th>明細</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($result)) {?>
                        <tr><td colspan="4">您還沒充值過呢，趕緊充一筆吧~~</td></tr>
                    <?php }else {?>
                        <?php foreach ($result as $row):?>
                            <tr>
                                <td class="phone-hidden"><?= $row['recharge_address']?></td>
                                <td><?= $row['amount']?> <?= strtoupper($type)?></td>
                                <td><?= date('Y/m/d H:i:s',$row['confirm_time'])?></td>
                                <td>
                                	<a target="_blank" href="<?= $row['type'] == 'btc' ? 'https://blockchain.info/tx/' : 'https://etherscan.io/tx/' ?><?= $row['txid']?>">查看</a>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    <?php }?>
                </tbody>
            </table>
            <!--分页-->
            <div class="pages">
            	<?php echo Pagination::create($pageInfo['page'], $pageInfo['pageCount']);?>
            </div>
        </div>
    </div>
</div>
 
