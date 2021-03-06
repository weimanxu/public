<?php
class UserController extends CustomBaseController{
    /**
    * 进入个人中心首页
    * @param         
    * @return 
    * @author XJW Create At 2017年7月25日
    */
    public function HomeAction(){
        //初始页面
        $userInfo = $this->pageInit();;
        if(empty($userInfo)){
            App::$response->gotoPage("/login");
        }
        
        
        
        //获取用户项目信息
        $userClass   = new UserClass();
        $userProject = $userClass->getInfosByUid('user_project', $userInfo['id']);
        foreach ($userProject as $k => &$v){
            $timeStamp = time();
            $projectInfo=Mysql::_getInstance('project')
                        ->sqlHead()
                        ->where('`id` = '.$v['project_id'].' AND `state` = 1')
                        ->queryOne()
                        ->getResult();
            if (!empty($projectInfo)){
                $v['project_logo'] = $projectInfo['logo'];
                $v['project_name'] = $projectInfo['name'];
                if ($projectInfo['begintime'] <= $timeStamp && $timeStamp <= $projectInfo['endtime']){
                    $v['project_state'] = 1;
                }elseif ($timeStamp > $projectInfo['endtime']){
                    $v['project_state'] = 2;
                }else{
                    $v['project_state'] = 3;
                }
            }
            
            $v['btc_amount'] = Format::formatNumber($v['btc_amount'],8,false,true);
            $v['eth_amount'] = Format::formatNumber($v['eth_amount'],8,false,true);
        }
        
        
        App::$view->layout = 'user';
        App::$view->loadView('user/home', [
            'user_project'  => $userProject,
        ]);
    }
    
    /**
    * 进入实名验证的页面
    * @param         
    * @return 
    * @author XJW Create At 2017年7月27日
    */
    public function IdentityAction(){
        //初始页面
        $userInfo=$this->pageInit();;
        if(empty($userInfo)){
            App::$response->gotoPage("/login");
        }
        //获取身份验证记录
        $where = ' WHERE `user_id` = '.$userInfo['id'].' AND `state` = 1';
        $sql   = 'SELECT * FROM `verify_record`'.$where;
        
        $verifyRecord = Mysql::_getInstance()
                ->sql($sql)
                ->queryOne()
                ->getResult();
        App::$view->layout = 'user';
        App::$view->loadView('user/identity', [
            'verifyRecord'  => $verifyRecord
        ]);
    }
    
    /**
    * 提交实名验证信息
    * @param         
    * @return 
    * @author XJW Create At 2017年7月31日
    */
    public function TakeIdentityInfoAction(){
        //判断用户有没有登陆
        $userInfo = $this->checkLoginAndGetInfo();
        if(empty($userInfo)){
            App::$response->simpleJsonError('請先登錄!');
        }
        //获取页面参数
        $name       = App::$request->getParam('name');
        $cardno     = App::$request->getParam('cardno');
        $pic_hand   = App::$request->getParam('pic_hand');
        $pic_front  = App::$request->getParam('pic_front');
        $pic_back   = App::$request->getParam('pic_back');
        //检验数据的合法性
        if (empty($name)){
            App::$response->simpleJsonError('請輸入姓名!');
        }
        if (empty($cardno)){
            App::$response->simpleJsonError('請輸入身份證號!');
        }
        if (empty($pic_hand)){
            App::$response->simpleJsonError('手持身份證正面照片不能為空!');
        }
        if (empty($pic_front)){
            App::$response->simpleJsonError('身份證正面照片不能為空!');
        }
        if (empty($pic_back)){
            App::$response->simpleJsonError('身份證背面照片不能為空!');
        }
        if (!is_string($name)){
            App::$response->simpleJsonError('姓名必須是字符串!');
        }
        if (strlen($name)>16){
            App::$response->simpleJsonError('姓名不能超過16位!');
        }
        //验证身份证号码是否合法
        if (!file_exists(PATH_WEB.$pic_hand)){
            App::$response->simpleJsonError('手持身份證正面照片不存在!');
        }
        if (!file_exists(PATH_WEB.$pic_front)){
            App::$response->simpleJsonError('身份證正面照片不存在!');
        }
        if (!file_exists(PATH_WEB.$pic_back)){
            App::$response->simpleJsonError('身份證背面照片不存在!');
        }
        //先把之前的记录的state设置为0
        $sql = 'UPDATE `verify_record` SET `state` = 0 WHERE `user_id` ='.$userInfo['id'];
        $state=Mysql::_getInstance()
                 ->sql($sql)
                 ->exec()
                 ->getState();     
        //插入数据进验证记录
        $verifyRecordId=Mysql::_getInstance('verify_record')
                          ->insert([
                               'user_id'    => $userInfo['id'],
                               'name'       => $name,
                               'cardno'     => $cardno,
                               'pic_front'  => $pic_front,
                               'pic_hand'   => $pic_hand,
                               'pic_back'   => $pic_back,
                               'createtime' => time(),
                          ])->getLastId();
        if (empty($verifyRecordId)){
            App::$response->simpleJsonError('提交失敗,請刷新頁面重新嘗試!');
        }
                
        App::$response->simpleJsonSuccessWithData("提交成功!");      
        
    }
    
    /**
    * 进入我支持的项目页面
    * @param         
    * @return 
    * @author XJW Create At 2017年7月27日
    */
    public function SupportAction(){
        //初始页面
        $userInfo=$this->pageInit();;
        if(empty($userInfo)){
            App::$response->gotoPage("/login");
        }
        //获取用户项目信息
        $userClass = new UserClass();
        $userProject = $userClass->getInfosByUid('user_project', $userInfo['id']);
        foreach ($userProject as $k => &$v){
            //获取项目信息
            $projectInfo=Mysql::_getInstance('project')
            ->sqlHead()
            ->where('`id` = '.$v['project_id'].' AND `state` = 1')
            ->queryOne()
            ->getResult();
            if (!empty($projectInfo)){
                $v['project_name']  = $projectInfo['name'];
                $v['project_logo']  = $projectInfo['logo'];
                if ($projectInfo['begintime'] <= time() && time() <= $projectInfo['endtime']){
                    $v['project_state'] = 1;
                }
                if (time() > $projectInfo['endtime']){
                    $v['project_state'] = 2;
                }
            }
            $remainToken          = bcsub($v['token'], $v['withdraw_amount'],8);
            $v['remainToken']     = Format::formatNumber($remainToken,8,false,true);
            $v['btc_amount']      = Format::formatNumber($v['btc_amount'],8,false,true);
            $v['eth_amount']      = Format::formatNumber($v['eth_amount'],8,false,true);
            $v['withdraw_amount'] = Format::formatNumber($v['withdraw_amount'],8,false,true);
            $v['token']           = Format::formatNumber($v['token'],8,false,true);
        }
        
        App::$view->layout = 'user';
        App::$view->loadView('user/support', [
             'user_project'  => $userProject,
        ]);
    }
    
    /**
    * 进入我锁定的项目页面
    * @param         
    * @return 
    * @author XJW Create At 2017-8-25
    */
    /* public function LockProjectAction(){
        //初始页面
        $userInfo=$this->pageInit();;
        if(empty($userInfo)){
            App::$response->gotoPage("/login");
        }
        //查找没有开始的项目信息
        $timeStamp = time();
        $sql         = 'SELECT * FROM `project` WHERE `state`= 1 AND `begintime` > '.$timeStamp;
        $projectInfo = Mysql::_getInstance()->sql($sql)->query()->getResult();
        
        //查找当前用户锁定的项目
        $sql             = 'SELECT * FROM `user_lock_project` WHERE `user_id` = '.$userInfo['id']
                            .' AND `state` = 1 AND `invest_state` = 1';
        $lockProjectInfo = Mysql::_getInstance()->sql($sql)->query()->getResult();
        
        App::$view->layout = 'user';
        App::$view->loadView('user/lockProject', [
            'projectInfo'      => $projectInfo,
            'lockProjectInfo'  => $lockProjectInfo,
        ]);
    } */

    
    /**
    * 进入安全设置页面
    * @param         
    * @return 
    * @author XJW Create At 2017年7月25日
    */
    public function SecurityAction(){
        //初始页面
        $userInfo=$this->pageInit();;
        if(empty($userInfo)){
            App::$response->gotoPage("/login");
        }
        $sql = 'SELECT * FROM `login_log` WHERE `user_id`='.$userInfo['id'].' ORDER BY `createtime` DESC LIMIT 10';
        $loginInfos=Mysql::_getInstance()
            ->sql($sql)
            ->query()
            ->getResult();

        //查询实名验证状态
        $verifySql   = 'SELECT `verify_state` FROM `verify_record` WHERE `user_id` = '.$userInfo['id'].' AND `state` = 1';
        $verifyState = Mysql::_getInstance()->sql($verifySql)->queryOne()->getResult();
        
        
        App::$view->layout = 'user';
        App::$view->loadView('user/security', [
            'login_infos'  => $loginInfos,
            'verify_state' => $verifyState['verify_state'],
        ]);
    }
    
    /**
    * 发送手机验证码
    * @param         
    * @return 
    * @author XJW Create At 2017年8月2日
    */
    public function SendPhoneCodeAction(){
        //初始页面
        $userInfo = $this->checkLoginAndGetInfo();
        if(empty($userInfo)){
            App::$response->simpleJsonError('請先登錄!');
        }
        //获取参数
        $type  = App::$request->getParam('type');
        $phone = App::$request->getParam('phone');
        $type  = trim($type);
        
        $phoneSession = Session::_getInstance('modifyPhone');
        $smsClass = new SMS();
        
        if ($type === "changePhone"){
            if (!Validator::isPhoneNumber($phone)){
                App::$response->simpleJsonError('手機號碼格式不正確，請重新輸入!');
            }
            //新手机与原手机是否重复
            if ($phone == $userInfo['phone']){
                App::$response->simpleJsonError('手機號已被使用!');
            } 
            $sendPhone = $phone;
        }elseif ($type === "changePhoneOld"){
            $sendPhone = $userInfo['phone'];
        }elseif ($type === "setFundPwd"){
            $sendPhone = $userInfo['phone'];
        }else{
            App::$response->simpleJsonError('參數錯誤!');
        }
        
        
        //判断是否允许发送验证码
        $phoneSessionItem = $phoneSession->getSession($type);
        
         if(!empty($phoneSessionItem)
            && $phoneSessionItem['phone'] == $sendPhone
            && $phoneSessionItem['time'] + 50 > time()){
            //不允许发送
            App::$response->simpleJsonError('驗證碼發送太頻繁，請稍後再試!');
        }
        //电话号码前加86（中国地区）才能接受到验证码  
        $realSendPhone = '+86'.$sendPhone;
        $result = $smsClass->sendSMS($realSendPhone);
        
        if ($result){
            //保存code进session
            $uid = $phoneSession->getSession('uid');
            if(empty($uid)){
                $phoneSession->setSession('uid', $userInfo['id']);
            }
            $phoneSession->setSession($type, [
                'phone' => $sendPhone,
                'code'  => $result,
                'time'  => time()
            ]);
            $phoneSession->setTimeOut(null);
            $phoneSession->sendSession();
            App::$response->simpleJsonSuccessWithData('發送成功!');
        }else {
            App::$response->simpleJsonError('獲取驗證碼失敗,請重新獲取!');
        }
    }
    
    /**
     * 发送邮箱验证码
     * @param
     * @return
     * @author XJW Create At 2017年8月2日
     */
    public function SendEmailCodeAction(){
        //初始页面
        $userInfo = $this->checkLoginAndGetInfo();
        if(empty($userInfo)){
            App::$response->simpleJsonError('請先登錄!');
        }
        //获取参数
        $type  = App::$request->getParam('type');
        $email = App::$request->getParam('email');
        $type  = trim($type);
        
        $emailSession = Session::_getInstance('modifyEmail');
        $userClass = new UserClass(); 
        
        if ($type === "changeEmail"){
            if (!Validator::isEmail($email)){
                App::$response->simpleJsonError('郵箱格式不正確，請重新輸入!');
            }
            //判断用户表是否已存在该邮箱
            $userInfo = $userClass->getUserInfoByEmail($email);
            if(!empty($userInfo)){
                App::$response->simpleJsonError('該郵箱已經被使用!');
            }
            $sendEmail = $email;
        }elseif ($type === "changeEmailOld"){
            $sendEmail = $userInfo['email'];
        }elseif ($type === "setFundPwd"){
            $sendEmail = $userInfo['email'];
        }else{
            App::$response->simpleJsonError('參數錯誤!');
        }
    
        //判断是否允许发送验证码
        $emailSessionItem = $emailSession->getSession($type);
        if(!empty($emailSessionItem)
            && $emailSessionItem['email'] == $sendEmail
            && $emailSessionItem['time'] + 50 > time()){
            //不允许发送
            App::$response->simpleJsonError('驗證碼發送太頻繁，請稍後再試!');
        }
    
        $result = $userClass->sendEmailVelifyCode($sendEmail, $userInfo['id']);
        
        if ($result['success']){
            //保存code进session
            $uid = $emailSession->getSession('uid');
            if(empty($uid)){
                $emailSession->setSession('uid', $userInfo['id']);
            }
            $emailSession->setSession($type, [
                'email' => $sendEmail,
                'code'  => $result['rand'],
                'time'  => time()
            ]);
            $emailSession->setTimeOut(null);
            $emailSession->sendSession();
            App::$response->simpleJsonSuccessWithData('驗證郵件發送成功，請到您的郵箱查看驗證碼!');
        }else {
            App::$response->simpleJsonError('發送失敗，請刷新頁面再嘗試!');
        } 
    }
    
    
    /**
    * 提交设置手机号
    * @param         
    * @return 
    * @author XJW Create At 2017年8月1日
    */
    public function TakeSetPhoneAction(){
        //初始页面
        $userInfo = $this->checkLoginAndGetInfo();
        if(empty($userInfo)){
            App::$response->simpleJsonError('請先登錄!');
        }
        
        //修改手机号码情况
        //获取参数
        $phone    = App::$request->getParam('new_phone');
        $newCode  = App::$request->getParam('new_code');
        
        $session      = Session::_getInstance('modifyPhone');
        $phoneSession = $session->getSession();
        if (empty($phoneSession)){
            App::$response->simpleJsonError('請先獲取驗證碼!');
        }
        if ($phoneSession['uid'] != $userInfo['id']){
            App::$response->simpleJsonError('請關閉瀏覽器重新打開網站操作!');
        }
        //绑定手机号码情况
        if (!empty($userInfo['phone'])){
            //获取旧号码验证码
            $oldCode = App::$request->getParam('old_code');
            if (!empty($phoneSession['changePhoneOld'])){
                $oldPhoneData = $phoneSession['changePhoneOld'];
                if ($oldPhoneData['time'] + 60*30 < time()){
                    App::$response->simpleJsonError('驗證碼已超過30分鐘，請重新獲取!');
                }
                if (empty($oldCode)){
                    App::$response->simpleJsonError('請輸入原手機驗證碼!');
                }
                if ($oldCode != $oldPhoneData['code']){
                    App::$response->simpleJsonError('原手機驗證碼輸入錯誤!');
                }
            }else {
                App::$response->simpleJsonError('請先獲取原手機驗證碼!');
            }
            
        }
        //校验参数合法性
        if (empty($phone)){
            App::$response->simpleJsonError('請輸入新手機號碼!');
        }
        if (!empty($phoneSession['changePhone'])){
            $newPhoneData = $phoneSession['changePhone'];
            if ($newPhoneData['time'] + 60*30 < time()){
                App::$response->simpleJsonError('驗證碼已超過30分鐘，請重新獲取!');
            }
            if (empty($newCode)){
                App::$response->simpleJsonError('請輸入新手機驗證碼!');
            }
            if ($newCode != $newPhoneData['code']){
                App::$response->simpleJsonError('新手機驗證碼輸入錯誤!');
            }
        }else {
            App::$response->simpleJsonError('請先獲取新手機驗證碼!');
        }
        if (!Validator::isPhoneNumber($phone)){
            App::$response->simpleJsonError('請輸入正確的手機號碼!');
        }
        //判断获取验证码的新手机与输入的手机是否一致
        if ($phone != $newPhoneData['phone']){
            App::$response->simpleJsonError('獲取驗證碼的手機號碼與輸入的手機號碼不壹致!');
        }
        $updateRecord = [
            'id'        => $userInfo['id'],
            'phone'     => $phone
        ];
        
        //是否第一次设置手机号码
        if(!empty($userInfo['phone'])){
            $updateRecord['lock_time'] = time() + 3600*24;
        }
        
        $state = Mysql::_getInstance('user')->update($updateRecord)->getState();
                    
        if (!$state){
            App::$response->simpleJsonError('提交失敗,請刷新頁面重新嘗試!');
        }
        //修改手机号码成功后清除modifyphone的Session
        $session->cleanSession();
        $session->sendSession();
        App::$response->simpleJsonSuccessWithData('綁定/修改手機號碼成功!');
    }
    
    /**
    * 提交修改登陆邮箱
    * @param         
    * @return 
    * @author XJW Create At 2017年8月1日
    */
    public function TakeSetEmailAction(){
        //初始页面
        $userInfo = $this->checkLoginAndGetInfo();
        if(empty($userInfo)){
            App::$response->simpleJsonError('請先登錄!');
        }
        //获取页面参数
        $oldCode = App::$request->getParam('old_code');
        $newCode = App::$request->getParam('new_code');
        $email   = App::$request->getParam('new_email');
        
        $session      = Session::_getInstance('modifyEmail');
        $emailSession = $session ->getSession();
        if (empty($emailSession['changeEmailOld'])){
            App::$response->simpleJsonError('請先獲取原郵箱驗證碼!');
        }
        if (empty($emailSession['changeEmail'])){
            App::$response->simpleJsonError('請先獲取新郵箱驗證碼!');
        }
        if ($emailSession['uid'] != $userInfo['id']){
            App::$response->simpleJsonError('請關閉瀏覽器重新打開網站操作!');
        }
        
        $oldEmailData = $emailSession['changeEmailOld'];
        $newEmailData = $emailSession['changeEmail'];
        
        //验证数据的合法性
        if (empty($oldCode)){
            App::$response->simpleJsonError('請輸入原郵箱驗證碼!');
        } 
        if (empty($newCode)){
            App::$response->simpleJsonError('請輸入新郵箱驗證碼!');
        } 
        if (empty($email)){
            App::$response->simpleJsonError('請輸入新郵箱!');
        }
        
        if ($oldEmailData['time'] + 60*30 < time()){
            App::$response->simpleJsonError('原郵箱驗證碼已超過30分鐘，請重新獲取!');
            }
        if ($newEmailData['time'] + 60*30 < time()){
            App::$response->simpleJsonError('新郵箱驗證碼已超過30分鐘，請重新獲取!');
        }
        if ($oldCode != $oldEmailData['code']){
            App::$response->simpleJsonError('原郵箱驗證碼輸入錯誤!');
        }
        
        if ($newCode != $newEmailData['code']){
            App::$response->simpleJsonError('新郵箱驗證碼輸入錯誤!');
        }
    
        if (!Validator::isEmail($email)){
            App::$response->simpleJsonError('請輸入正確的郵箱!');
        }
        //判断输入的新邮箱与获取验证码的邮箱是否一致
        if ($email != $newEmailData['email']){
            App::$response->simpleJsonError('獲取驗證碼的郵箱與輸入的郵箱不壹致!');
        }
        
        
        $state = Mysql::_getInstance('user')
                        ->update([
                            'id'        => $userInfo['id'],
                            'email'     => $email,
                            'lock_time' => time() + 3600*24,
                        ])->getState();
        if (!$state){
            App::$response->simpleJsonError('提交失敗,請刷新頁面重新嘗試!');
        }      
        //修改邮箱成功后清除modifyphone的Session
        $session->cleanSession();
        $session->sendSession();
        
        App::$response->simpleJsonSuccessWithData('修改郵箱成功!');          
    }
    
    /**
    * 提交设置登陆密码
    * @param         
    * @return 
    * @author XJW Create At 2017年8月9日
    */
    public function TakeSetLoginPwdAction(){
        //初始页面
        $userInfo = $this->checkLoginAndGetInfo();
        if(empty($userInfo)){
            App::$response->simpleJsonError('請先登錄!');
        }
        //获取页面参数
        $oldPassword  = App::$request->getParam('oldPassword');
        $newPassword  = App::$request->getParam('newPassword');
        
        //验证参数的合法性
        if (empty($oldPassword)){
            App::$response->simpleJsonError('請輸入舊密碼!');
        }
        if (empty($newPassword)){
            App::$response->simpleJsonError('請輸入新密碼!');
        }
        if (!Validator::isValidPassword($oldPassword) || !Validator::isValidPassword($newPassword)){
            App::$response->simpleJsonError('密碼為6-32位，區分大小寫!');
        }
        if ($userInfo['password'] != sha1($oldPassword)){
            App::$response->simpleJsonError('舊密碼輸入不正確');
        }
        
        $userSate = Mysql::_getInstance('user')->update([
                    'id'       => $userInfo['id'],
                    'password' => sha1($newPassword),
                ])->getState();
        if (!$userSate){
            App::$response->simpleJsonError('修改密碼不成功，請刷新頁面再嘗試!');
        }
        Session::_getInstance('uSession')->cleanSession()->sendSession();
        App::$response->simpleJsonSuccessWithData('修改密碼成功!');
    }
    
    /**
    * 提交设置资金密码
    * @param         
    * @return 
    * @author XJW Create At 2017年7月28日
    */
    public function TakeSetFundPwdAction(){
        //初始页面
        $userInfo = $this->checkLoginAndGetInfo();
        if(empty($userInfo)){
            App::$response->simpleJsonError('請先登錄!');
        }
        //获取页面参数
        $fundPwd     = App::$request->getParam('fundPwd');
        $verifyCode  = App::$request->getParam('verifyCode');
        
        //用户绑定手机号获取手机验证码，没有绑定手机就获取邮箱验证码
        if (!empty($userInfo['phone'])){
            $session      = Session::_getInstance('modifyPhone');
            $sessionData = $session->getSession();
        }else {
            $session      = Session::_getInstance('modifyEmail');
            $sessionData = $session->getSession();
        }
        if (empty($sessionData)){
            App::$response->simpleJsonError('請先獲取驗證碼!');
        }
        if ($sessionData['uid'] != $userInfo['id']){
            App::$response->simpleJsonError('請關閉瀏覽器重新打開網站操作!');
        }
        
        //验证参数的合法性
        if (empty($fundPwd)){
            App::$response->simpleJsonError('請輸入資金密碼!');
        }
        if (empty($verifyCode)){
            App::$response->simpleJsonError('請輸入驗證碼!');
        }
        if (!Validator::isValidPassword($fundPwd)){
            App::$response->simpleJsonError('資金密碼為6-32位，區分大小寫!');
        }
        //判断验证码是否正确
        if (!empty($sessionData['setFundPwd'])){
            $fundPhoneData = $sessionData['setFundPwd'];
            if ($fundPhoneData['time'] + 60*30 < time()){
                App::$response->simpleJsonError('驗證碼已超過30分鐘，請重新獲取!');
            }
            if ($verifyCode != $fundPhoneData['code']){
                App::$response->simpleJsonError('驗證碼輸入錯誤!');
            }
        }else {
            App::$response->simpleJsonError('請先獲取手機驗證碼!');
        }
        
        $updateRecord = [
            'id'            => $userInfo['id'],
            'fund_password' => sha1($fundPwd)
        ];
        
        //是否第一次设置资金资金密码
        if(!empty($userInfo['fund_password'])){
            $updateRecord['lock_time'] = time() + 3600*24;
        }
        
        $state = Mysql::_getInstance('user')->update($updateRecord)->getState();
        if (!$state){
            App::$response->simpleJsonError('設置資金密碼失敗,請刷新頁面重新嘗試!');
        }
        //修改资金密码成功后清除modifyphone的Session
        $session->cleanSession();
        $session->sendSession();
        
        App::$response->simpleJsonSuccessWithData('設置資金密碼成功!');
    }
    
  
    
    /**
    * 增加或者修改代币提现地址
    * @param         
    * @return 
    * @author XJW Create At 2017年8月7日
    */
    public function TokenRefreshAddressAction(){
        //初始页面
        $userInfo=$this->pageInit();;
        if(empty($userInfo)){
            App::$response->simpleJsonError('請先登錄!');
        }
        
        //获取页面参数
        $withdrawAddress = App::$request->getParam('withdraw_address');
        $fundPassword    = App::$request->getParam('fund_password');
        $id              = App::$request->getParam('id');
        
        //验证数据的合法性
        if(empty($withdrawAddress)){
            App::$response->simpleJsonError('請輸入代幣提現地址!');
        }
        if (strlen($withdrawAddress)>64){
            App::$response->simpleJsonError('輸入的代幣提現地址長度不能超過64位!');
        }
        if (!is_string($withdrawAddress)){
            App::$response->simpleJsonError('輸入的代幣提現地址必須是字符串!');
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
        if(!Validator::isInt($id) || $id<1){
            App::$response->simpleJsonError('參數有誤!');
        }
        //判断资金密码是否正确
        if($userInfo['fund_password'] !== sha1($fundPassword)){
            App::$response->simpleJsonError('資金密碼輸入錯誤!');
        }
        //判断输入地址跟保存的地址是否一样
        $sql    = 'SELECT * FROM `user_project` WHERE `id` = '.$id.' AND `user_id` ='.$userInfo['id'];
        $result = Mysql::_getInstance()->sql($sql)->exec()->queryOne()->getResult();
        
        if ($result['withdraw_address'] == $withdrawAddress){
            App::$response->simpleJsonError('代幣提現地址已存在!');
        }
        
        //更新代币提现地址
        $sql = 'UPDATE `user_project` SET `withdraw_address` = "'.addslashes($withdrawAddress).
               '" WHERE `id` = '.$id.' AND `user_id` = '.$userInfo['id'];
        
        $state=Mysql::_getInstance()->sql($sql)->exec()->getState();
        if (!$state){
            App::$response->simpleJsonError('添加/修改代幣提現地址失敗,請重新刷新頁面再嘗試!');
        }
        App::$response->simpleJsonSuccessWithData("操作成功!");
    }
    
    /**
    * 提交确认代币提现
    * @param         
    * @return 
    * @author XJW Create At 2017年8月7日
    */
    public function TokenTakeWithdrawAction(){
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
        $id           = App::$request->getParam('id');
        $fundPassword = App::$request->getParam('fund_password');
        
        //验证数据合法性
        if (empty($amount)){
            App::$response->simpleJsonError("輸入提現數量不能為空或者為零!");
        }
        if (empty($id) || !Validator::isInt($id) || $id < 1){
            App::$response->simpleJsonError("參數有誤!");
        }
        if ($userInfo['fund_password'] == ""){
            App::$response->simpleJsonError("您沒有設置資金密碼，請先設置資金密碼!");
        }
        if (empty($fundPassword)){
            App::$response->simpleJsonError("請輸入資金密碼!");
        }
        if(!Validator::isHighPrecisionNumber($amount)){
            App::$response->simpleJsonError('提幣數量輸入格式有誤!');
        }
        //判断资金密码是否正确
        if ($userInfo['fund_password'] !== sha1($fundPassword)){
            App::$response->simpleJsonError('資金密碼輸入錯誤!');
        }
        
        //采用事务处理提现情况
        $mysql = Mysql::_getInstance();
        try {
            $mysql->beginTransaction();
            //获取该项目可提现的代币数量和代币提现地址
            $sql    = 'SELECT * FROM `user_project` WHERE `id` = '.$id.' AND `user_id` ='.$userInfo['id'].' FOR UPDATE';
            $result = Mysql::_getInstance()->sql($sql)->exec()->masterQueryOne()->getResult();
            //判断该id的用户项目是否存在
            if (empty($result)){
                $mysql->rollBack();
                App::$response->simpleJsonError('您支持的該項目不存在!');
            }
            //查看当前项目是否开放提币
            $projectClass = new ProjectClass();
            $projectInfo  = $projectClass->getInfoByIdWithState('project', $result['project_id']);
            if(empty($projectInfo) || $projectInfo['open_withdraw'] == '0'){
                $mysql->rollBack();
                App::$response->simpleJsonError('該項目還沒有開放提幣!');
            }
            
            //判断是否有提现地址
            if ($result['withdraw_address'] == ''){
                $mysql->rollBack();
                App::$response->simpleJsonError('提現地址不存在,請添加提現地址!');
            }
            //代提币数量
            $remain = bcsub($result['token'], $result['withdraw_amount'],8);
            //手续费
            $fee    = App::loadConf('app/tokenFee');
            //提币数量与手续费总数
            $allAmount = bcadd($amount, $fee,8);
            //判断提现数额是否大于可代币提现数额
            if (bccomp($allAmount, $remain,8) != 0){
                App::$response->simpleJsonError('只能提現全部代幣數量!');
            }
            
            $userSql  = 'SELECT * FROM `user` WHERE `state` = 1 AND `id` = '.$userInfo['id'].' FOR UPDATE';
            $userInfo = $mysql->sql($userSql)->masterQueryOne()->getResult();
            if (empty($userInfo)){
                $mysql->rollBack();
                App::$response->simpleJsonError('代幣提現操作失敗，請重新刷新頁面再嘗試!');
            }
            
            
            //新增代币提现记录
            $TokenWithdrawState = $mysql->selectTable('token_withdraw')
            ->insert([
                    'user_project_id'  => $id,
                    'user_id'          => $userInfo['id'],
                    'project_id'       => $result['project_id'],
                    'address'          => $result['withdraw_address'],
                    'amount'           => $amount,
                    'fee'              => $fee,
                    'createtime'       => time(),
            ])->getState();
            $TokenWithdrawId = $mysql->getLastId();
            if (empty($TokenWithdrawId)){
                $mysql->rollBack();
                App::$response->simpleJsonError('代幣提現操作失敗，請重新刷新頁面再嘗試!');
            }
            
            
            //修改用户项目代币余额
            //代币变动金额
            $token            = bcadd($amount, $fee,8);
            //用户该项目代币已提现数量
            $withdrawAmount   = bcadd($result['withdraw_amount'], $token,8);
            
            $sql              = 'UPDATE `user_project` SET `withdraw_amount` = '
                              .$withdrawAmount.' WHERE `id` = '.$id
                              . ' AND `user_id` = '.$userInfo['id'];
            $userProjectState = $mysql->sql($sql)->exec()->getState();
            //用户该项目代币剩余余额
            $balance          = bcsub($result['token'], $withdrawAmount,8);
            //新增代币明细记录
            $tokenClass       = new TokenClass();
            $fundDetailState = $tokenClass->addTokenDetail($id, $userInfo['id'], $result['project_id'], 2, $token, $balance, $TokenWithdrawId);
            
            
            if ($TokenWithdrawState && $userProjectState && $fundDetailState){
                $mysql->commit();
                App::$response->simpleJsonSuccessWithData("代幣提現操作成功!");
            }else{
                $mysql->rollBack();
                App::$response->simpleJsonError('代幣提現操作失敗，請重新刷新頁面再嘗試!');
            }
        } catch (Exception $e) {
            $mysql->rollBack();
        }
        
    }
  
    /**
     * 撤回申请中的代币提现记录
     * @param
     * @return
     * @author XJW Create At 2017年7月27日
     */
    public function TokenRevocationAction(){
        //判断用户有没有登陆
        $userInfo = $this->checkLoginAndGetInfo();
        if(empty($userInfo)){
            App::$response->simpleJsonError('請先登錄!');
        }
        //获取页面参数
        $withdrawId = App::$request->getParam('id');
        //验证参数合法性
        if(!Validator::isInt($withdrawId)){
            App::$response->simpleJsonError('代幣提現撤回操作失敗!');
        }
    
        //判断该提现记录是否存在
        $userClass      = new UserClass();
        $withdrawRecord = $userClass->getWithdrawRecordByIdAndUserId('token_withdraw', $withdrawId,$userInfo['id']);
        if (empty($withdrawRecord)){
            App::$response->simpleJsonError('該代幣提現記錄不存在!');
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
                App::$response->simpleJsonError('代幣提現撤回操作失敗，請重新刷新頁面再嘗試!');
            }
    
            //修改代币提现记录的提现状态为0(提现取消)
            $revocationSql       = 'UPDATE `token_withdraw` SET `withdraw_state` = 0 WHERE `user_id` = '.
                                    $userInfo['id'].' AND `id` = '.$withdrawId.' AND `state` = 1';
            $withdrawRecordState = $mysql->sql($revocationSql)->exec()->getState();
            
            //修改用户项目代币余额
            //获取该代币提现记录信息
            $withdrawSql = 'SELECT * FROM `token_withdraw` WHERE `state` = 1 AND `id` = '.
                            $withdrawId.' AND `user_id` = '.$userInfo['id'].'  FOR UPDATE';
            $revocationResult=$mysql->sql($withdrawSql)->exec()->masterQueryOne()->getResult();
    
            if (empty($revocationResult)){
                $mysql->rollBack();
                App::$response->simpleJsonError('代幣提現撤回操作失敗，請重新刷新頁面再嘗試!');
            }
            //代币提现记录的变动数量
            $token     = bcadd($revocationResult['amount'], $revocationResult['fee'],8);
            
            //获取该id的用户项目记录
            $sql               = 'SELECT * FROM `user_project` WHERE `user_id` = '.$userInfo['id']
                                 .' AND `id` = '.$revocationResult['user_project_id'];
            $userProjectResult = $mysql->sql($sql)->queryOne()->getResult();
            if (empty($userProjectResult)){
                $mysql->rollBack();
                App::$response->simpleJsonError('代幣提現撤回操作失敗，請重新刷新頁面再嘗試!');
            }
            //该项目代币已提现数量
            $amount  = bcsub($userProjectResult['withdraw_amount'], $token,8);
            //该项目代币剩余数量
            $balance = bcsub($userProjectResult['token'], $amount,8);
            //修改用项目表代币余额
            $userProjectState = $mysql->selectTable('user_project')->update([
                'id'               => $revocationResult['user_project_id'],
                'withdraw_amount'  => $amount,
            ])->getState();
            
            //新增代币明细记录
            $tokenClass       = new TokenClass();
            $tokenDetailState = $tokenClass->addTokenDetail($revocationResult['user_project_id'], $userInfo['id'], $revocationResult['project_id'], 3, $token, $balance, $revocationResult['id']);
    
            if ($userProjectState && $withdrawRecordState && $tokenDetailState){
                $mysql->commit();
                App::$response->simpleJsonSuccessWithData("代幣提現撤回操作成功!");
            }else{
                $mysql->rollBack();
                App::$response->simpleJsonError('代幣提現撤回操作失敗，請重新刷新頁面再嘗試!');
            }
        }catch (Exception $e){
            $mysql->rollBack();
        }
    
    }    
    
  
    /**
    * 上传身份证图片
    * @param         
    * @return 
    * @author XJW Create At 2017年7月31日
    */
    public function UploadIdentityImgAction(){
        //判断用户有没有登陆
        $userInfo = $this->checkLoginAndGetInfo();
        if(empty($userInfo)){
            App::$response->simpleJsonError('請先登錄!');
        }
        //获取页面参数
        if ($_FILES['picture']['error']){
            App::$response->simpleJsonError($_FILES['picture']['error']);
        }
        $identityImage  = $_FILES['picture'];
        $filename_info  = pathinfo($_FILES['picture']['name']);
        //保存图片路径
        $path           = App::loadConf('app/identitySavePath').date('Ym')."/";
        //图片绝对路径
        $savePath       = PATH_WEB . $path;
        //最大照片上传大小
        $upFileSize     = App::loadConf('app/upFileSize');
        //照片名称
        $saveFileName   = time().rand(0,999999).".".$filename_info['extension'];
        //拼接绝对路径
        $filePath       = $path . $saveFileName;
        $img            = new Image();
        $savection      = $img->setupFileSize($upFileSize)->setsaveName($saveFileName)->upfile('picture',$savePath);
        if (!$savection){
            App::$response->simpleJsonError($img->showErrmsg());
        }
        App::$response->simpleJsonSuccessWithData($filePath);
    }
    
  
    
    /**
    * 代币提现记录详情
    * @param         
    * @return 
    * @author XJW Create At 2017年8月7日
    */
    public function TokenWithdrawDetailsAction(){
        //判断用户有没有登陆
        $userInfo = $this->checkLoginAndGetInfo();
        if(empty($userInfo)){
            App::$response->simpleJsonError('請先登錄!');
        }
        //获取页面参数
        $id = App::$request->getParam('id');
        //检验数据的合法性
        if (!Validator::isInt($id)){
            App::$response->simpleJsonError('獲取代幣提現記錄失敗!');
        }
        //编写sql语句
        $sql    = 'SELECT * FROM `token_withdraw` WHERE `user_project_id` = '
                    .$id.' AND `user_id` = '.$userInfo['id'].' AND `state` = 1 ORDER BY `id` DESC';
        //获取代币
        $result = Mysql::_getInstance()->sql($sql)->exec()->query()->getResult();
        
        
        foreach ($result as &$v){
            $v['createtime'] = date('Y/m/d H:i:s',$v['createtime']); 
            $v['fee']        = Format::formatNumber($v['fee'],8,false,true);
            $v['amount']     = Format::formatNumber($v['amount'],8,false,true);
         }
        App::$response->simpleJsonSuccessWithData($result);
    }
    
    /**
     * 投资详情
     * @param
     * @return
     * @author XJW Create At 2017年8月4日
     */
    public function InvestmentDetailsAction(){
        //判断用户有没有登陆
        $userInfo = $this->checkLoginAndGetInfo();
        if(empty($userInfo)){
            App::$response->simpleJsonError('請先登錄!');
        }
        //获取页面参数
        $id = App::$request->getParam('id');
        //检验数据的合法性
        if (!Validator::isInt($id)){
            App::$response->simpleJsonError('獲取項目投資詳情失敗!');
        }
        $sql    = 'SELECT * FROM `user_project_record` WHERE `user_project_id` = '
            .$id.' AND `user_id` = '.$userInfo['id'].' ORDER BY `id` DESC';
        $result = Mysql::_getInstance()->sql($sql)->exec()->query()->getResult();
        foreach ($result as &$v){
            $v['createtime'] = date('Y/m/d H:i:s',$v['createtime']);
            $v['type']       = strtoupper($v['type']);
            $v['amount']     = Format::formatNumber($v['amount'],8,false,true);
            $v['token']      = Format::formatNumber($v['token'],8,false,true);
        }
    
        App::$response->simpleJsonSuccessWithData($result);
    }
}