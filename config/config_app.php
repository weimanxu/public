<?php
/**
 * 项目自定义配置文件
 * 
 */
return [
    //当前系统支持货币类型
    'currency'          => ['btc', 'eth'],
    //分页中每页所显示数量
    'pageSize'          =>10,
    //btc最低手续费
    'btcMinFee'         =>0.002,
    //eth最低手续费
    'ethMinFee'         =>0.01,
    //btc最低提现数量
    'btcMinAmount'      =>0.02,
    //eth最低提现数量
    'ethMinAmount'      =>0.5,
    //每日btc最高提现数量
    'btcMaxAmount'      =>20,
    //每日eth最高提现数量
    'ethMaxAmount'      =>200,
    //身份证照片保存路径
    'identitySavePath'  =>'/attachment/identity/',
    //身份证照片上传大小 (字节)
    'upFileSize'        =>1024*1024,
    //代币提现手续费                           
    'tokenFee'          =>0,
    
    //是否开放充值
    'openRecharge'      =>false,
    
    //美联软通短信配置
    'meilian' => [
        'username'          => 'bite888',
        'password_md5'      => 'meilian123',
        'apikey'            => '65b2fb1350a2ce0c4ed343833f5cd80d',
        'website'           => '【财酷ICO】'
    ],       
    
    //七牛云存储配置  lib/Qiniu
    'qiniu' => [
        'access' => '',
        'secret' => ''
    ],
    
    //阿里大于
    'alidayu' => [
        'appkey' => '',
        'secret' => ''
    ]
];