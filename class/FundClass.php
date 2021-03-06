<?php
class FundClass extends CustomBaseClass{
    /**
    * 新增资金明细记录
    * @param int $uid        
    * @param string $type     
    * @param int $opt_type        
    * @param float $userBalance        
    * @param float $balance        
    * @param int $relateId        
    * @return false || array
    * @author XJW Create At 2017年8月9日
    */
    public function addFundDetail($uid,$type,$opt_type,$balance,$userBalance,$relateId){
        if (!Validator::isInt($uid) || !Validator::isInt($opt_type) || !Validator::isInt($relateId)){
            return false;
        }
        if (!in_array($type, App::loadConf('app/currency'))){
            return false;
        }
        if ($balance != 0){
            if (!Validator::isHighPrecisionNumber($balance)){
                return false;
            }
        }
        if ($userBalance != 0){
            if (!Validator::isHighPrecisionNumber($userBalance)){
                return false;
            }
        }
        if ($opt_type == 1 || $opt_type == 3 ||$opt_type == 4){
            $balance = -$balance;
        }
        //新增资金明细记录
        $fundDetailState = Mysql::_getInstance('fund_detail')->insert([
            'user_id'    => $uid,
            'type'       => $type,
            'opt_type'   => $opt_type,
            'amount'     => -$balance,
            'balance'    => $userBalance,
            'relate_id'  => $relateId,
            'createtime' => time(),
        ])->getState();
        
        return $fundDetailState;
        
    }
}