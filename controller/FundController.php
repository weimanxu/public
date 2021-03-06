<?php

class FundController extends CustomBaseController {
    
    /**
     * 进入资金充值页面
     * @param
     * @return
     * @author XJW Create At 2017年7月25日
     */
    public function RechargeAction(){
        //初始页面
        $userInfo=$this->pageInit();
        if(empty($userInfo)){
            App::$response->gotoPage("/login");
            
        }
        //获取页面的当前页面
        $page = App::$request->getParam('page');
        //获取页面的当前显示的货币类型记录
        $type = App::$request->getParam('type');
    
        //判断参数是否合法
        if(!Validator::isInt($page) || $page < 1){
            $page = 1;
        }
        $currencyList = App::loadConf('app/currency');
        $type         = strtolower($type);
        if (!in_array($type, $currencyList)){
            $type = $currencyList[0];
        }
        
        //获取当前用户的充值地址
        $rechargeClass = new RechargeClass();
        $addressInfo   = $rechargeClass->getAddressByUidAndType($userInfo['id'], $type);
        //如果地址为空，重新分配地址
        if(empty($addressInfo)){
            $addressInfo = $rechargeClass->allocateAddress($userInfo['id'], $type);
        }
        
        
        //充值记录
        //获取总记录数
        $where      = 'WHERE `user_id` = '.$userInfo['id'].' AND `type` = "' . $type . '" AND `state` = 1 AND `confirm_state` = 1';
        $countSql   = 'SELECT COUNT(*) AS `count` FROM `recharge_record` ' . $where;
        $count      = Mysql::_getInstance()->sql($countSql)->queryOne()->getResult();
        //充值记录的总记录数
        $count      = $count['count'];
        //每一页显示的记录数
        $pageSize   = App::loadConf('app/pageSize');
        
        //充值记录的总页数
        $countPage  = ceil($count / $pageSize);
        if ($count == 0){
            $countPage = 1;
            
        }
        //判断page是否超范围
        if($page > $countPage){
            $page = $countPage;
        }
        $index = ($page - 1) * $pageSize;
        //获取记录
        $resultSql = 'SELECT * FROM `recharge_record` '.$where.' ORDER BY `id` DESC LIMIT '.$index.','.$pageSize;
        $result    = Mysql::_getInstance()->sql($resultSql)->query()->getResult();
        foreach ($result as &$v){
            $v['amount'] = Format::formatNumber($v['amount'],8,false,true);
            $v['fee']    = Format::formatNumber($v['fee'],8,false,true);
        }
        
        App::$view->layout = 'user';
        App::$view->loadView('fund/recharge', [
            'addressInfo'       => $addressInfo,
            'result'            => $result,
            'type'              => $type,
            'pageInfo'          => [
                'page'      => $page,
                'pageSize'  => $pageSize,
                'pageCount' => $countPage
            ]
        ]);
    }
    
    /**
     * 生成充值二维码
     *
     * @param
     * @return
     * @author XJW Create At 2017年7月25日
     */
    public function QrcodeAction () {
        $type = App::$request->getParam('type');
        $address = App::$request->getParam('address');
        if(!in_array($type, App::loadConf('app/currency')) || empty($address)){
            App::$response->simpleJsonError('參數錯誤');
        }
        require_once PATH . '/lib/phpqrcode.php';
        if($type == 'btc'){
            $url = $address;
        }else{
            $url = $address;
        }
        ob_start();
        QRcode::png($url, false, 'M', 10, 0);
        $qrcodeString = ob_get_contents();
        ob_end_clean();
        header('Content-type: image/png');
        echo $qrcodeString;
        exit;
    }
    
    /**
     * 进入资金明细页面
     * @param
     * @return
     * @author XJW Create At 2017年8月1日
     */
    public function CapitalAction(){
        //初始页面
        $userInfo=$this->pageInit();
        if(empty($userInfo)){
            App::$response->gotoPage("/login");
        }
    
        //获取页面的需要跳转页数
        $page = App::$request->getParam('page');
        $type = App::$request->getParam('type');
        //校验参数
        if (!Validator::isInt($page) || $page < 1){
            $page = 1;
        }
        $currencyList = App::loadConf('app/currency');
        $type         = strtolower($type);
        if (!in_array($type, $currencyList)){
            $type = $currencyList[0];
        }
        //获取总记录数
        $where      = 'WHERE `user_id` = '.$userInfo['id'].' AND `state` = 1';
        if(!empty($type)){
            $where .= ' AND `type` = "' . $type . '"';
        }
        $countSql   = 'SELECT COUNT(*) AS `count` FROM `fund_detail` ' . $where;
        $count      = Mysql::_getInstance()->sql($countSql)->queryOne()->getResult();
        //充值记录的总记录数
        $count      = $count['count'];
        //每一页显示的记录数
        $pageSize   = 15 ;
        
        //总页数
        $countPage  = ceil($count / $pageSize);
        if ($count == 0){
            $countPage = 1;
        }
        //判断page是否超范围
        if($page > $countPage){
            $page = $countPage;
        }
        $index = ($page - 1) * $pageSize;
        //获取记录
        $resultSql = 'SELECT * FROM `fund_detail` ' . $where . ' ORDER BY `id` DESC LIMIT ' . $index . ',' .$pageSize;
        $result    = Mysql::_getInstance()->sql($resultSql)->query()->getResult();
        foreach ($result as &$v){
            $v['typeText']  = ['', '充值', '提現', '提現撤銷', '提現失敗', '投資'][$v['opt_type']];
            $v['amount']    = Format::formatNumber($v['amount'], 8, false, true);
            $v['balance']   = Format::formatNumber($v['balance'], 8, false, true);
            //整数变动金额添加 +
            if ($v['amount'] > 0){
                $v['amount'] = '+'.$v['amount'];
            }
        }
        
        App::$view->layout = 'user';
        App::$view->loadView('fund/capital', [
            'recordInfos'  => $result,
            'type'         => $type,
            'pageInfo'     => [
                'page'      => $page,
                'pageSize'  => $pageSize,
                'pageCount' => $countPage
            ]
        ]);
    }
    

    /**
     * 进入资金提现页面
     * @param
     * @return
     * @author XJW Create At 2017年7月26日
     */
    public function WithdrawalAction(){
        //初始页面
        $userInfo = $this->pageInit();
        if(empty($userInfo)){
            App::$response->gotoPage('/login');
        }
        //获取页面的当前页数
        $page = App::$request->getParam("page");
        //获取页面的当前显示的货币类型记录
        $type = App::$request->getParam('type');
        
        
        //判断参数是否合法
        if(!Validator::isInt($page) || $page < 1){
            $page = 1;
        }
        $currencyList = App::loadConf('app/currency');
        $type         = strtolower($type);
        if(!in_array($type, $currencyList)){
            $type = $currencyList[0];
        }
    
        //提现记录
        //获取总记录数
        $where      = 'WHERE `user_id` = ' . $userInfo['id'] . ' AND `state` = 1 AND `type` = "' . $type . '"';
        $countSql   = 'SELECT COUNT(*) AS `count` FROM `withdraw_record` ' . $where;
        $count      = Mysql::_getInstance()->sql($countSql)->queryOne()->getResult();
        //提取记录的总记录数
        $count      = (int)$count['count'];
        //每一页显示的记录数
        $pageSize   = App::loadConf('app/pageSize');
        //提取记录的总页数
        $countPage  = ceil($count / $pageSize);
        if($countPage == 0){
            $countPage = 1;
        }
        //判断page是否超范围
        if($page > $countPage){
            $page = $countPage;
        }
        $index = ($page - 1) * $pageSize;
        //获取记录
        $resultSql = 'SELECT * FROM `withdraw_record` ' . $where
        . ' ORDER BY `id` DESC'
            . ' LIMIT ' . $index . ', ' . $pageSize;
    
        $result = Mysql::_getInstance()->sql($resultSql)->query()->getResult();
        foreach ($result as &$v){
            $v['amount'] = Format::formatNumber($v['amount'],8,false,true);
            $v['fee']    = Format::formatNumber($v['fee'],8,false,true);
        }
    
        //最低手续费
        $minFee     = App::loadConf('app/' . $type . 'MinFee');
        //最低取币数量
        $minAmount  = App::loadConf('app/' . $type . 'MinAmount');
        //每日最高提取数量
        $maxAmount  = App::loadConf('app/' . $type . 'MaxAmount');
        //当日最高提现数量
        $date = strtotime(date('Y/m/d'));
        $sql =  'SELECT SUM(`amount` + `fee`) AS `amount` FROM `withdraw_record` WHERE `state` = 1 AND `type` = "'
             . $type . '" AND `createtime` >= ' . $date
             . ' AND `withdraw_state` IN (1,2,4) And `user_id` = '.$userInfo['id'];
        
        $withdrawIn = Mysql::_getInstance()->sql($sql)->queryOne()->getResult();
        $dayAmount  = bcsub($maxAmount, $withdrawIn['amount'], 8);
    
        //获取当前用户的提现地址
        $withdrawClass = new WithdrawClass();
        $addressInfo   = $withdrawClass->getAddressByUidAndType($userInfo['id'], $type);
    
        App::$view->layout = 'user';
        App::$view->loadView('fund/withdrawal', [
            'addressInfo'       => $addressInfo,
            'result'            => $result,
            'type'              => $type,
            'minFee'            => $minFee,
            'minAmount'         => $minAmount,
            'maxAmount'        =>  $maxAmount,
            'dayAmount'        =>  Format::formatNumber($dayAmount, 8),
            'pageInfo'          => [
                'page'      => $page,
                'pageSize'  => $pageSize,
                'pageCount' => $countPage
            ]
        ]);
    
    }
    
    /**
     * 增加或者修改提现地址
     * @param
     * @return
     * @author XJW Create At 2017年7月26日
     */
    public function RefreshAddressAction(){
        //初始页面
        $userInfo=$this->pageInit();;
        if(empty($userInfo)){
            App::$response->simpleJsonError('請先登錄!');
        }
    
        //获取页面参数
        $withdrawAddress = App::$request->getParam('withdraw_address');
        $fundPassword    = App::$request->getParam('fund_password');
        $type            = App::$request->getParam('type');
        $type            = strtolower($type);
    
        //验证数据的合法性
        if(empty($withdrawAddress)){
            App::$response->simpleJsonError('請輸入提現地址!');
        }
        if (strlen($withdrawAddress)>64){
            App::$response->simpleJsonError('輸入的提現地址長度不能超過64位!');
        }
        if (!is_string($withdrawAddress)){
            App::$response->simpleJsonError('輸入的提現地址必須是字符串!');
        }
        if ($userInfo['fund_password'] == ""){
            App::$response->simpleJsonError("您沒有設置資金密碼，請先設置資金密碼!");
        }
        if(empty($fundPassword)){
            App::$response->simpleJsonError('請輸入資金密碼!');
        }
        if (!Validator::isValidPassword($fundPassword)){
            App::$response->simpleJsonError('資金密碼為6-32位，區分大小寫!');
        }
        if(empty($type)){
            App::$response->simpleJsonError('請輸入貨幣類型!');
        }
        if (!in_array($type, App::loadConf('app/currency'))){
            App::$response->simpleJsonError('貨幣類型輸入格式有誤!');
        }
        //判断资金密码是否正确
        if($userInfo['fund_password'] !== sha1($fundPassword)){
            App::$response->simpleJsonError('資金密碼輸入錯誤!');
        }
        //获取当前用户提现地址信息
        $userClass = new UserClass();
        $withdrawAddressInfo = $userClass->getInfoByIdAndType('withdraw_address', $type, $userInfo['id']);
    
        //判断提现地址是否存在
        if (!empty($withdrawAddressInfo)){
            //先修改提现地址状态state为0
            $sql = ' UPDATE `withdraw_address` SET `state` = 0 '
                .'WHERE `user_id` = '.$userInfo['id']
                .' AND `type` = "'.addslashes($type).'"';
            $addressState=Mysql::_getInstance()
            ->sql($sql)
            ->exec()
            ->getState();
            if (!$addressState){
                App::$response->simpleJsonError('添加/修改提現地址失敗,請重新刷新頁面再嘗試!');
            }
        }
    
        //插入提现地址
        $state=Mysql::_getInstance('withdraw_address')
        ->insert([
            'user_id'    => $userInfo['id'],
            'address'    => $withdrawAddress,
            'type'       => $type,
            'createtime' => time()
        ])->getState();
        if (!$state){
            App::$response->simpleJsonError('添加/修改提現地址失敗,請重新刷新頁面再嘗試!');
        }
        App::$response->simpleJsonSuccessWithData("操作成功!");
    
    }
    
   
     
    /**
     * 提交确认提现
     * @param
     * @return
     * @author XJW Create At 2017年7月27日
     */
    public function TakeWithdrawAction(){
        //判断用户有没有登陆
        $userInfo = $this->checkLoginAndGetInfo();
        if(empty($userInfo)){
            App::$response->simpleJsonError('請先登錄!');
        }
        //先实名验证才能提现
        $sql    = 'SELECT * FROM verify_record WHERE `user_id` ='.$userInfo['id'].' AND `state` =1 AND `verify_state` = 2';
        $result = Mysql::_getInstance()->sql($sql)->exec()->queryOne()->getResult();
        if (empty($result)){
            App::$response->simpleJsonError('請先實名驗證後才能提現!');
        }
    
        //判断用户有没有绑定、修改手机、更换验证方式以及更改资金密码后24小时内不允许提币
        if (time() < $userInfo['lock_time']){
            App::$response->simpleJsonError("在綁定、修改手機、更換驗證方式以及更改資金密碼後24小時內不允許提幣("
                .date('Y/m/d H:i:s',$userInfo['lock_time'])."後可以提幣)");;
        }
    
        //获取页面参数
        $amount       = App::$request->getParam('amount');
        $type         = App::$request->getParam('type');
        $fee          = App::$request->getParam('fee');
        $fundPassword = App::$request->getParam('fund_password');
        $type         = strtolower($type);
        //变动金额
        $balance     = bcadd($amount, $fee,8);
        //验证数据合法性
        if (empty($amount)){
            App::$response->simpleJsonError("輸入提現數量不能為空或者為零!");
        }
        if (empty($type)){
            App::$response->simpleJsonError("請輸入貨幣類型!");
        }
        if ($userInfo['fund_password'] == ""){
            App::$response->simpleJsonError("您沒有設置資金密碼，請先設置資金密碼!");
        }
        if (empty($fundPassword)){
            App::$response->simpleJsonError("請輸入資金密碼!");
        }
        //当用户没有填手续费，默认手续费为0
        if (empty($fee)){
            $fee = 0;
        }else{
            if(!Validator::isHighPrecisionNumber($fee)){
                App::$response->simpleJsonError('手續費輸入格式有誤!');
            }
        }
        if(!in_array($type, App::loadConf('app/currency'))){
            App::$response->simpleJsonError('貨幣類型輸入錯誤!');
        }
        if(!Validator::isHighPrecisionNumber($amount)){
            App::$response->simpleJsonError('提現數量輸入格式有誤!');
        }
    
        if (!Validator::isValidPassword($fundPassword)){
            App::$response->simpleJsonError('資金密碼為6-32位，區分大小寫!');
        }
        //判断提币数量是否大于每日最高可提现 数量或者大于当前用户的余额
        $date = strtotime(date('Y/m/d'));
        $sql =  'SELECT SUM(`amount` + `fee`) AS `amount` FROM `withdraw_record` WHERE `state` = 1 AND `type` = "'
                . $type . '" AND `createtime` >= ' . $date
                . ' AND `withdraw_state` IN (1,2,4) And `user_id` = '.$userInfo['id'];
        
        $withdrawIn = Mysql::_getInstance()->sql($sql)->queryOne()->getResult();
        //当日提现总数量
        $total = $withdrawIn['amount'];
        
        //每天最多允许提现数量
        $maxAmount = App::loadConf('app/'.$type.'MaxAmount');
        if ($total + $balance > $maxAmount){
            App::$response->simpleJsonError('提現數量不能超過每日最高可提現數量!');
        }
        if ($balance > $userInfo[$type.'_usable']){
            App::$response->simpleJsonError('提現數量不能超過您的可用'.strtoupper($type).'余額!');
        }
        //判断提币数量是否小于最低提币数量
        if ($amount < App::loadConf('app/'.$type.'MinAmount')){
            App::$response->simpleJsonError('提現數量不能少於最低提現數量!');
        }
        //手续费不能大于用户余额
        if ($fee > $userInfo[$type.'_usable']){
            App::$response->simpleJsonError('手續費不能超過您的可用'.strtoupper($type).'余額!!');
        }
        //手续费必须大于等于最低网络手续费
        if ($fee < App::loadConf('app/'.$type.'MinFee')){
            App::$response->simpleJsonError('網絡手續費不能低於'.App::loadConf('app/'.$type.'MinFee').strtoupper($type));
        }
        //判断资金密码是否正确
        if ($userInfo['fund_password'] !== sha1($fundPassword)){
            App::$response->simpleJsonError('資金密碼輸入錯誤!');
        }
        //获取当前用户提现地址
        $withdrawClass   = new WithdrawClass();
        $withdrawAddress = $withdrawClass->getAddressByUidAndType($userInfo['id'], $type);
        if ($withdrawAddress['address'] == ''){
            App::$response->simpleJsonError('提現地址不存在,請添加提現地址!');
        }
        
        //采用事务处理提现情况
        $mysql = Mysql::_getInstance();
        try {
            $mysql->beginTransaction();
            $userSql  = 'SELECT * FROM `user` WHERE `state` = 1 AND `id` = '.$userInfo['id'].' FOR UPDATE';
            $userInfo = $mysql->sql($userSql)->masterQueryOne()->getResult();
            if (empty($userInfo)){
                $mysql->rollBack();
                App::$response->simpleJsonError('提現操作失敗，請重新刷新頁面再嘗試!');
            }
            
            
            //新增提现记录
            $withdrawRecordState = $mysql->selectTable('withdraw_record')
                            ->insert([
                                'user_id'             => $userInfo['id'],
                                'withdraw_address_id' => $withdrawAddress['id'],
                                'withdraw_address'    => $withdrawAddress['address'],
                                'type'                => $type,
                                'amount'              => $amount,
                                'fee'                 => $fee,
                                'createtime'          => time()
                            ])->getState();
            $withdrawRecordId = $mysql->getLastId();
            if (empty($withdrawRecordId)){
                $mysql->rollBack();
                App::$response->simpleJsonError('提現操作失敗，請重新刷新頁面再嘗試!');
            }
            
            //修改用户余额
            //用户余额
            $userBalance = bcsub($userInfo[$type.'_usable'], $balance,8);
            $userState = $mysql->selectTable('user')->update([
                            'id'            => $userInfo['id'],
                            $type.'_usable' => $userBalance,
                        ])->getState();
            
            //新增资金明细记录
            $funcClass       = new FundClass();
            $fundDetailState = $funcClass->addFundDetail($userInfo['id'],$type,2,$balance,$userBalance,$withdrawRecordId);
            
            if ($userState && $withdrawRecordState && $fundDetailState){
                $mysql->commit();
                App::$response->simpleJsonSuccessWithData("提現操作成功!");
            }else{
                $mysql->rollBack();
                App::$response->simpleJsonError('提現操作失敗，請重新刷新頁面再嘗試');
            }
        } catch (Exception $e) {
            $mysql->rollBack();
        }
        
    }
    
    /**
     * 撤回申请中的提现记录
     * @param
     * @return
     * @author XJW Create At 2017年7月27日
     */
    public function RevocationAction(){
        //判断用户有没有登陆
        $userInfo = $this->checkLoginAndGetInfo();
        if(empty($userInfo)){
            App::$response->simpleJsonError('請先登錄!');
        }
        //获取页面参数
        $withdrawId = App::$request->getParam('id');
        //验证参数合法性
        if(!Validator::isInt($withdrawId)){
            App::$response->simpleJsonError('撤回操作失敗!');
        }
    
        //判断该提现记录是否存在
        $userClass      = new UserClass();
        $withdrawRecord = $userClass->getWithdrawRecordByIdAndUserId('withdraw_record', $withdrawId,$userInfo['id']);
        if (empty($withdrawRecord)){
            App::$response->simpleJsonError('該提現記錄不存在!');
        }
    
        //采用事务处理撤销提现记录
        $mysql = Mysql::_getInstance();
        try {
            //开启事务
            $mysql->beginTransaction();
            $userSql  = 'SELECT * FROM `user` WHERE `state` = 1 AND `id` = '.$userInfo['id'].' FOR UPDATE';
            $userInfo = $mysql->sql($userSql)->masterQueryOne()->getResult();
            if (empty($userInfo)){
                $mysql->rollBack();
                App::$response->simpleJsonError('撤回操作失敗，請重新刷新頁面再嘗試!');
            }

            //修改提现记录的提现状态为0(提现取消)
            $revocationSql = 'UPDATE `withdraw_record` SET `withdraw_state` = 0 WHERE `user_id` = '.
                                $userInfo['id'].' AND `id` = '.$withdrawId.' AND `state` = 1';
            $withdrawRecordState = $mysql->sql($revocationSql)->exec()->getState();
            
            
            //修改用户余额
            //获取该提现记录信息
            $withdrawSql = 'SELECT * FROM `withdraw_record` WHERE `state` = 1 AND `id` = '.
                            $withdrawId.' AND `user_id` = '.$userInfo['id'].' FOR UPDATE';
            $revocationResult=$mysql->sql($withdrawSql)->exec()->masterQueryOne()->getResult();
            
            if (empty($revocationResult)){
                $mysql->rollBack();
                App::$response->simpleJsonError('撤回操作失敗，請重新刷新頁面再嘗試!');
            }
            $type = $revocationResult['type'];
            
            //提现记录的金额
            $balance     = bcadd($revocationResult['amount'], $revocationResult['fee'],8);
            //用户余额
            $userBalance = bcadd($userInfo[$type.'_usable'], $balance,8);
            //修改用户余额
            $userState = $mysql->selectTable('user')->update([
                'id'            => $userInfo['id'],
                $type.'_usable' => $userBalance,
            ])->getState();
            
            
            //新增资金明细记录
            $fundClass       = new FundClass();
            $fundDetailState = $fundClass->addFundDetail($userInfo['id'], $type, 3, $balance, $userBalance, $revocationResult['id']);
            
            if ($userState && $withdrawRecordState && $fundDetailState){
                $mysql->commit();
                App::$response->simpleJsonSuccessWithData("撤回操作成功!");
            }else{
                $mysql->rollBack();
                App::$response->simpleJsonError('撤回操作失敗，請重新刷新頁面再嘗試!');
            }
        }catch (Exception $e){
            $mysql->rollBack();
        }
        
    }
    
    
}
