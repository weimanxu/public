<?php
App::$view->title = '財酷ICO - 個人中心';
App::$view->registerCss('/public/css/home.css');

App::$view->registerJs('/public/js/main/user/home.js');
?>
<div class="user-home-wrap">
    <div class="home-balance">
        <div class="clearfix home-nav">
            <img src="/public/images/toatal-ico.png">
            <div>
                <h2>賬戶總攬</h2>
                <ul>
                    <li><a href="/">主頁</a></li>
                    <li><a>個人賬戶</a></li>
                </ul>
            </div>
        </div>
        <div class="balance-show">
            <p>賬戶餘額</p>
            <div class="content-part">
                <div class="show-box">
                    <img src="/public/images/BTC_ico.png">
                    <div class="show-count">
                        <p>BTC</p>
                        <span><?= Format::formatNumber($userInfo['btc_usable'], 8, false, true, ',') ?></span>
                    </div>
                </div>
                <span></span>
                <div class="show-box">
                    <img src="/public/images/ETH_icon.png">
                    <div class="show-count">
                        <p>ETH</p>
                        <span><?= Format::formatNumber($userInfo['eth_usable'], 8, false, true, ',') ?></span>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <div class="home-my-project">
        <h2>我支持的項目</h2>
    <?php if (empty($user_project)) { ?>
        <p class="text-center"><img src="/public/images/null.png"></p>
        <p class="null">暫無支持的項目，<a href="/project/list">去支持</a></p>
    <?php } else { ?>
        <div class="pcshow">
            <?php foreach ($user_project as $row): ?>
                <div class="project-info clearfix">
                    <div class="left-name">
                        <img src="<?= $row['project_logo'] ?>"/>
                        <a href="/project/detail?id=<?= $row['project_id'] ?>" class="project-name"><?= $row['project_name'] ?></a>
                    </div>
                    <div class="left-amount">投資金額：<?php if ($row['eth_amount'] == null) { ?>
                            <?= Format::formatNumber($row['btc_amount'], 8, false, true, ',') ?> BTC
                        <?php }
                        if ($row['btc_amount'] == null) { ?>
                            <?= Format::formatNumber($row['eth_amount'], 8, false, true, ',') ?> ETH
                        <?php }
                        if ($row['btc_amount'] != null && $row['eth_amount'] != null) { ?>
                            <?= Format::formatNumber($row['btc_amount'], 8, false, true, ',') ?> BTC / <?= Format::formatNumber($row['eth_amount'], 8, false, true, ',') ?> ETH
                        <?php } ?>
                    </div>
                    <div class="right-state">狀態：<?php if ($row['project_state'] == 1) { ?>
                            進行中
                        <?php } else if($row['project_state'] == 2) { ?>
                            已結束
                        <?php } else {?>
                            未开始
                        <?php }?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="phoneshow">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th class="text-center">項目名</th>
                    <th class="text-center">投資金額</th>
                    <th class="text-center">狀態</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($user_project as $row): ?>
                    <tr>
                        <td class="text-center "><?= $row['project_name'] ?></td>
                        <td class="text-center"><?php if ($row['eth_amount'] == null) { ?>
                                <?= Format::formatNumber($row['btc_amount'], 8, false, true, ',') ?> BTC
                            <?php }
                            if ($row['btc_amount'] == null) { ?>
                                <?= Format::formatNumber($row['eth_amount'], 8, false, true, ',') ?> ETH
                            <?php }
                            if ($row['btc_amount'] != null && $row['eth_amount'] != null) { ?>
                                <?= Format::formatNumber($row['btc_amount'], 8, false, true, ',') ?> BTC / <?= Format::formatNumber($row['eth_amount'], 8, false, true, ',') ?> ETH
                            <?php } ?></td>
                        <td class="text-center"><?php if ($row['project_state'] == 1) { ?>
                                進行中
                            <?php } else { ?>
                                已結束
                            <?php } ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

    </div>
</div>

