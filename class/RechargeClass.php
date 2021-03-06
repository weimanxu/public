<?php
class RechargeClass extends CustomBaseClass{
    
    /**
     * 根据uid，type获取充值地址
     * 
     * @param  int      $uid
     * @param  string   $type
     * @return array
     * @author Ymj Create At 2017年7月27日
     */
    public function getAddressByUidAndType ($uid, $type) {
        if (!Validator::isInt($uid)){
            return null;
        }
        if (!in_array($type, App::loadConf('app/currency'))){
            return null;
        }
        $addressInfo=Mysql::_getInstance()
                ->sql('SELECT * FROM `recharge_address` WHERE `user_id` = '.$uid.' AND `type` = "'.$type.'" AND `isuse` = 1 AND `state` = 1')
                ->queryOne()
                ->getResult();
        return $addressInfo;
    }
    
    /**
     * 分配充值地址
     *
     * @param  int $uid
     * @return bool
     * @author Ymj Create At 2017年8月10日
     */
    public function allocateAddress ($uid, $type) {
        if (!Validator::isInt($uid)){
            return false;
        }
        if (!in_array($type, App::loadConf('app/currency'))){
            return false;
        }
    
        $mysql = Mysql::_getInstance();
        //查找是否已存在充值地址记录
        $record = $this->getAddressByUid($uid, $type);
        if(!empty($record)){
            return false;
        }
    
        $state = $mysql->sql('UPDATE `recharge_address` SET `isuse` = 1,`user_id` = ' . $uid)
               ->where('`type` = "' . $type . '" AND `isuse` = 0 AND `state` = 1 LIMIT 1')
               ->exec()
               ->getState();
        
        if($state){
            $result = $this->getAddressByUid($uid, $type);
        }else{
            $result = [];
        }
               
        return $result;
    }
    
    /**
     * 根据uid查找对应的充值地址记录
     *
     * @param  int $uid
     * @return array || null
     * @author Ymj Create At 2017年7月27日
     */
    public function getAddressByUid ($uid, $type) {
        if (!Validator::isInt($uid)){
            return null;
        }
        if (!in_array($type, App::loadConf('app/currency'))){
            return null;
        }
        
        $mysql = Mysql::_getInstance();
        $record = $mysql->sql('SELECT * FROM `recharge_address` WHERE `user_id` = ' . $uid . ' AND `type` = "' . $type . '" AND `isuse` = 1 AND `state` = 1 LIMIT 1')
                ->queryOne()
                ->getResult();
    
        return $record;
    }
}