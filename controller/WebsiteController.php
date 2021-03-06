<?php

class WebsiteController extends CustomBaseController {
    
    /**
     * 进入公告详情页面
     * @param
     * @return
     * @author XJW Create At 2017-8-31
     */
    public function AnnouncementDetailAction(){
        //页面初始化工作
        $userInfo = $this->pageInit();
    
        //获取参数
        $id = App::$request->getParam('id');
    
        //检验参数的合法性
        if (!Validator::isInt($id)){
            App::$response->gotoPage('/');
        }
        //查找该公告是否存在
       $annouInfo = Mysql::_getInstance('announcement')
                    ->sqlHead()->where(['state' => 1,'id' => $id,'open_state' => 1 ])
                    ->queryOne()->getResult();
       if (empty($annouInfo)){
           App::$response->gotoPage('/');
       }
            
        App::$view->layout = 'main';
        App::$view->loadView('website/announcementDetail',[
                'annou_info' => $annouInfo,
        ]);
    }
    
}
