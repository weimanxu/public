<?php

class ProjectController extends CustomBaseController {
    
    /**
     * 项目列表
     * 
     * @param 
     * @return
     * @author Ymj Create At 2017年6月28日
     */
    public function ListAction () {
        //页面初始化工作
        $this->pageInit();
        
        $projectClass = new ProjectClass();
        $field = ['id', 'name', 'logo', 'intro', 'begintime'];
        //获取进行中ICO
        $goingList = $projectClass->listProjectByType(1, $field);
        //获取即将到来ICO
        $waitList = $projectClass->listProjectByType(2, $field);
        //获取已完成ICO
        $doneList = $projectClass->listProjectByType(3, $field);
        
        App::$view->layout = 'main';
        App::$view->loadView('project/list', [
            'navProject' => true,
            'goingList'  => $goingList,
            'waitList'   => $waitList,
            'doneList'   => $doneList
        ]);
    }
    
    /**
     * 项目详情
     * 
     * @param 
     * @return
     * @author Ymj Create At 2017年6月28日
     */
    public function DetailAction () {
        $id = App::$request->getParam('id');
        if(!Validator::isInt($id)){
            App::$response->gotoPage('/project/list');
        }
        
        //查找数据
        $baseClass = new CustomBaseClass();
        $projectInfo = $baseClass->getInfoByIdWithState('project', $id);
        
        
        if(empty($projectInfo) || $projectInfo['is_show'] == '0'){
            App::$response->gotoPage('/project/list');
        }
        
        //页面初始化工作
        $this->pageInit();
        
        App::$view->layout = 'main';
        App::$view->loadView('project/detail', [
            'projectInfo' => $projectInfo
        ]);
    }
    
    /**
     * 投资项目
     * 
     * @param 
     * @return
     * @author Ymj Create At 2017年6月28日
     */
    public function InvestAction () {
        $id = App::$request->getParam('id');
        if(!Validator::isInt($id)){
            App::$response->gotoPage('/project/list');
        }
        
        //查找数据
        $projectClass = new ProjectClass();
        $projectInfo  = $projectClass->getInfoByIdWithState('project', $id);
        
        if(empty($projectInfo) || $projectInfo['is_show'] == '0'){
            App::$response->gotoPage('/project/list');
        }
        
        //页面初始化工作
        $userInfo = $this->pageInit();
        
        //项目状态(进行中/即将开始/到期/已完成/已暂停)
        $timeStamp = time();
        if($projectInfo['begintime'] <= $timeStamp 
            && $projectInfo['endtime'] > $timeStamp 
            && ($projectInfo['btc_done'] < $projectInfo['btc_target'] || $projectInfo['eth_done'] < $projectInfo['eth_target'])){
            //正在进行中
            if($projectInfo['open_state'] == 0){
                $projectInfo['projectInvestState'] = 5;
            }else{
                $projectInfo['projectInvestState'] = 1;
            }
        }elseif ($projectInfo['begintime'] > $timeStamp){
            //即将开始
            $projectInfo['projectInvestState'] = 2;
        }elseif ($projectInfo['endtime'] <= $timeStamp){
            //到期
            $projectInfo['projectInvestState'] = 3;
        }else{
            //已完成
            $projectInfo['projectInvestState'] = 4;
        }
        
        //投资明细
        $investRecord = $projectClass->listProjectInvestByUid($userInfo['id'], $id);
        
        App::$view->layout = 'main';
        App::$view->loadView('project/invest', [
            'projectInfo'   => $projectInfo,
            'investRecord'  => $investRecord
        ]);
    }
    
    /**
     * 生成充值二维码
     * 
     * @param 
     * @return
     * @author Ymj Create At 2017年6月30日
     */
    public function QrcodeAction () {
        $type = App::$request->getParam('type');
        $address = App::$request->getParam('address');
        if(!in_array($type, App::loadConf('app/currency')) || empty($address)){
            App::$response->simpleJsonError('參數錯誤');
        }
        
        require_once PATH . '/lib/phpqrcode.php';
        
        if($type == 'btc'){
            $url = 'bitcoin:' . $address;
        }else{
            $url = 'ethcoin:' . $address;
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
    * 提交项目投资
    * @param         
    * @return 
    * @author XJW Create At 2017年8月10日
    */
    public function TakeProjectInvestmentAction(){
        //初始页面
        $userInfo = $this->checkLoginAndGetInfo();
        if(empty($userInfo)){
            App::$response->simpleJsonError('請先登錄!');
        }
        
        //获取页面参数
        $projectId = App::$request->getParam('project_id');
        $type      = App::$request->getParam('type');
        $amount    = App::$request->getParam('amount');
        $fundPwd   = App::$request->getParam('fund_password');
        $type      = strtolower($type);
        
        //检验参数的合法性
        if (!Validator::isInt($projectId) ){
            App::$response->simpleJsonError('參數錯誤!');
        }
        if (empty($type)){
            App::$response->simpleJsonError('請輸入貨幣類型!');
        }
        if (!in_array($type, App::loadConf('app/currency'))){
            App::$response->simpleJsonError('貨幣類型參數錯誤!');
        }
        if (!Validator::isHighPrecisionNumber($amount)){
            App::$response->simpleJsonError('輸入投資金額格式有誤!');
        }
        //检查有没有设置资金密码
        if (empty($userInfo['fund_password'])){
            App::$response->simpleJsonError('您沒有設置資金密碼，請先設置資金密碼!');
        }
        if (empty($fundPwd)){
            App::$response->simpleJsonError('請輸入資金密碼!');
        }
        if (sha1($fundPwd) != $userInfo['fund_password']){
            App::$response->simpleJsonError('資金密碼輸入錯誤!');
        }
        
        $mysql = Mysql::_getInstance();
        $timeStamp = time();
        
        //查看project_rate表的代币兑换率计算token数量
        $rateSql  = 'SELECT * FROM `project_rate` WHERE `state` = 1 AND `project_id` = ' 
                  . $projectId . ' AND `type` = "' . $type . '"'
                  . ' AND `begintime` <= ' . $timeStamp . ' AND `endtime` >= ' . $timeStamp;
        $rateInfo = $mysql->sql($rateSql)->queryOne()->getResult();
        if (empty($rateInfo)){
            App::$response->simpleJsonError('投資項目失敗，請刷新頁面再嘗試!');
        }
        //通过否有btc或eth类型的投资记录判断该用户是否第一次投资该项目的btc、eth众筹
        $firstInvest     = false;
        $userProjectSql  = 'SELECT `id` FROM `user_project_record` WHERE `user_id` = '.$userInfo['id']
                           .' AND `type` = "'.addslashes($type).'" AND `project_id` = '.$projectId;
        $userProjectInfo = $mysql->sql($userProjectSql)->query()->getResult();
        if (empty($userProjectInfo)){
            $firstInvest = true;
        }
        
        try {
            $mysql->beginTransaction();
            //获取该该项目信息
            $projectSql  = 'SELECT * FROM `project` WHERE `id` = '.$projectId.' AND `state` = 1 AND `open_state` = 1 FOR UPDATE';
            $projectInfo = $mysql->sql($projectSql)->exec()->masterQueryOne()->getResult();
            if (empty($projectInfo)){
                $mysql->rollBack();
                App::$response->simpleJsonError('該項目不存在或已暫停ICO!');
            }
            
            if ($projectInfo['begintime'] > $timeStamp){
                $mysql->rollBack();
                App::$response->simpleJsonError('該項目還沒有到眾籌時間!');
            }
            if ( $projectInfo['endtime'] < $timeStamp){
                $mysql->rollBack();
                App::$response->simpleJsonError('該項目已結束眾籌!');
            }
            //用户投资金额有没有超过起投金额
            if (bccomp($amount, $projectInfo[$type.'_min'],8) < 0 ){
                $mysql->rollBack();
                App::$response->simpleJsonError('該項目投資金額最低為:'.Format::formatNumber($projectInfo[$type.'_min'],8).' '.strtoupper($type));
            }
            
            $keyTarget = $type . '_target';
            $keyDone   = $type . '_done';
            
            //判断剩余份额是否足够
            $balance = bcsub($projectInfo[$keyTarget], $projectInfo[$keyDone], 8);
            if(bccomp($balance, $amount, 8) === -1){
                $mysql->rollBack();
                App::$response->simpleJsonError('投資金額不能超過眾籌剩余份額');
            }
            
            //查询该用户余额是否充足
            $userSql  = 'SELECT * FROM `user` WHERE `id` = '.$userInfo['id'].' AND `state` = 1 FOR UPDATE';
            $userInfo = $mysql->sql($userSql)->exec()->masterQueryOne()->getResult();
            if (empty($userInfo)){
                $mysql->rollBack();
                App::$response->simpleJsonError('投資項目失敗，請刷新頁面再嘗試!');
            }
            if (bccomp($amount, $userInfo[$type.'_usable']) === 1){
                $mysql->rollBack();
                App::$response->simpleJsonError('您的賬號'.strtoupper($type).'余額不足!');
            }
            
            //修改用户的余额
            //用户剩余的btc或者eth的余额
            $remainUsable = bcsub($userInfo[$type.'_usable'], $amount, 8);
            $userState = $mysql->selectTable('user')->update([
                            'id'            => $userInfo['id'],
                            $type.'_usable' => $remainUsable,
                         ])->getState();
            
            //计算代币数量
            $token = bcmul($amount, $rateInfo['rate'], 8);
            
            //更新use_project表
            //先判断user_project是否有这条记录
            $sql             = 'SELECT * FROM `user_project` WHERE `user_id` = '.$userInfo['id'].' AND `project_id` = '.$projectId.' FOR UPDATE';
            $userProjectInfo = $mysql->sql($sql)->exec()->masterQueryOne()->getResult();
            
            
            if (empty($userProjectInfo)){
                $tokenBalance   = $token;
                $withdrawAmount = 0;
                //插入一条user_project记录
                $userProjectState = $mysql->selectTable('user_project')->insert([
                                            'user_id'       => $userInfo['id'],
                                            'project_id'    => $projectId,
                                            $type.'_amount' => $amount,
                                            'token'         => $token,
                                            'createtime'    => $timeStamp,
                                    ])->getState();
                $userProjectId = $mysql->getLastId();
            }else{
                $tokenBalance   = bcadd($userProjectInfo['token'], $token, 8);
                $withdrawAmount = $userProjectInfo['withdraw_amount'];
                
                //更新user_project记录
                $userProjectState = $mysql->selectTable('user_project')->update([
                                            'id'            => $userProjectInfo['id'],
                                            $type.'_amount' => bcadd($userProjectInfo[$type.'_amount'], $amount,8),
                                            'token'         => $tokenBalance,
                                    ])->getState();
                $userProjectId = $userProjectInfo['id'];
            }
            
            if (empty($userProjectId)){
                $mysql->rollBack();
                App::$response->simpleJsonError('投資項目失敗，請刷新頁面再嘗試!');
            }
            
            //插入一条user_project_record记录
            $recordState = $mysql->selectTable('user_project_record')->insert([
                                    'user_project_id' => $userProjectId,
                                    'user_id'         => $userInfo['id'],
                                    'project_id'      => $projectId,
                                    'type'            => $type,
                                    'amount'          => $amount,
                                    'token'           => $token,
                                    'createtime'      => $timeStamp,    
                            ])->getState();
            
            $recordId = $mysql->getLastId();
            
            if (empty($recordId)){
                $mysql->rollBack();
                App::$response->simpleJsonError('投資項目失敗，請刷新頁面再嘗試!');
            }
            
            //token剩余数量
            $remainToken = bcsub($tokenBalance, $withdrawAmount, 8);
            
            //插入一条fund_detail资金明细记录
            $fundClass        = new FundClass();
            $fundDetailState  = $fundClass->addFundDetail($userInfo['id'], $type, 5, $amount, $remainUsable, $recordId);

            //插入一条token_detail明细记录
            $tokenClass       = new TokenClass();
            $tokenDetailState = $tokenClass->addTokenDetail($userProjectId, $userInfo['id'],$projectId, 1, $token, $remainToken, $recordId);
            
            //修改project表的总投资人数和完成金额
            //如果第一次投资添加投资人数
            $totalPeople = $projectInfo[$type.'_total'];
            if ($firstInvest){
                $totalPeople += 1;
            }
            $projectState = $mysql->selectTable('project')->update([
                                    'id'            => $projectId,
                                    $type.'_done'   => bcadd($projectInfo[$type.'_done'], $amount,8),
                                    $type.'_total'  => $totalPeople,
                            ])->getState();
            
            
            if ($userState 
                && $userProjectState 
                && $recordState 
                && $fundDetailState 
                && $tokenDetailState
                && $projectState){
                $mysql->commit();
                App::$response->simpleJsonSuccessWithData('項目投資成功!');
                
            }else{
                $mysql->rollBack();
                App::$response->simpleJsonError('投資項目失敗，請刷新頁面再嘗試!');
            }
            
        } catch (Exception $e) {
            $mysql->rollBack();
        }
    }
    
}
