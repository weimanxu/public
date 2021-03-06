<?php
class ProjectClass extends CustomBaseClass {
    
    /**
     * 获取项目列表
     * 
     * @param int  $type [1=>进行中, 2=>即将到来, 3=>已经完成]
     * @param  array    $field
     * @return array
     * @author Ymj Create At 2017年6月28日
     */
    public function listProjectByType ($type, $field = [],$sort = '') {
        $where = '`state` = 1 AND `is_show` = 1';
        if($type == 1){
            //进行中
            $where .= ' AND `begintime` <= ' . time() . ' AND `endtime` > ' . time() . ' AND (`btc_target` != 0 AND `btc_done` < `btc_target` OR `eth_target` != 0 AND `eth_done` < `eth_target`)'; 
        }elseif ($type == 2){
            //即将到来
            $where .= ' AND `begintime` > ' . time();
        }elseif ($type == 3){
            //已经完成
            $where .= ' AND `endtime` <= ' . time() . ' OR (`is_show` = 1 AND NOT (`btc_target` != 0 AND `btc_done` < `btc_target` OR `eth_target` != 0 AND `eth_done` < `eth_target`))';
        }else{
            
        }
        $where .= $sort; 
        $projectList = Mysql::_getInstance('project')->sqlHead($field)
                     ->where($where)
                     ->query()
                     ->getResult();
                     
        return $projectList;
    }
    
    /**
     * 根据用户ID，项目ID获取该用户的所有投资记录
     * 
     * @param  int $uid
     * @param  int $pid
     * @return array
     * @author Ymj Create At 2017年8月8日
     */
    public function listProjectInvestByUid ($uid, $pid) {
        if(!Validator::isInt($uid) || !Validator::isInt($pid)){
            return [];
        }
        $sql = 'SELECT * FROM `user_project_record` WHERE `user_id` = ' . $uid . ' AND `project_id` = ' . $pid . ' ORDER BY `createtime` DESC';
        return Mysql::_getInstance()->sql($sql)->query()->getResult();
    }
}