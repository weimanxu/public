<?php
class TokenClass extends CustomBaseClass{
    /**
    * 新增代币明细记录
    * @param int $userProjectId        
    * @param int $uid        
    * @param int $projectId        
    * @param int $opt_type        
    * @param float $token        
    * @param float $balance        
    * @param int $relateId        
    * @return false || array
    * @author XJW Create At 2017年8月9日
    */
    public function addTokenDetail($userProjectId,$uid,$projectId,$opt_type,$token,$balance,$relateId){
        if (!Validator::isInt($userProjectId) 
            || !Validator::isInt($uid) 
            || !Validator::isInt($projectId)
            || !Validator::isInt($opt_type) 
            || !Validator::isInt($relateId)){
            return false;
        }
        if ($token != 0 ){
            if (!Validator::isHighPrecisionNumber($token)){
                return false;
            }
        }
        if ($balance != 0 ){
            if (!Validator::isHighPrecisionNumber($balance)){
                return false;
            }
        }
        if ($opt_type == 1 || $opt_type == 3 || $opt_type ==4){
            $token = -$token;
        }
        
        //新增资金明细记录
        $fundDetailState = Mysql::_getInstance('token_detail')->insert([
            'user_project_id'    => $userProjectId,
            'user_id'    => $uid,
            'project_id'       => $projectId,
            'opt_type'   => $opt_type,
            'token'     => -$token,
            'balance'    => $balance,
            'relate_id'  => $relateId,
            'createtime' => time(),
        ])->getState();
        
        return $fundDetailState;
        
    }
}