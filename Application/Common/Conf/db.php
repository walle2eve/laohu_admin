<?php
/**
 * Created by PhpStorm.
 * User: panda
 * Date: 2017/7/26
 * Time: 18:00
 */
return array(
    /* 数据库设置 */
    'DB_TYPE'                => 'mysql', // 数据库类型
    'DB_HOST'                => 'localhost', // 服务器地址
    'DB_PORT'                => '3306', // 端口
    'DB_NAME'                => 'laohu', // 数据库名
    'DB_USER'                => 'root', // 用户名
    'DB_PWD'                 => '312250544', // 密码
    'DB_PREFIX'              => 't_', // 数据库表前缀
    'DB_PARAMS'              => array(), // 数据库连接参数
    'DB_DEBUG'               => false, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'        => true, // 启用字段缓存
    'DB_CHARSET'             => 'utf8', // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE'         => 1, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE'         => true, // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM'          => 1, // 读写分离后 主服务器数量
    'DB_SLAVE_NO'            => '', // 指定从服务器序号

    // log库连接信息
    'DB_LAOHU_LOG_CONFIG' => array(
        'db_type'  => 'mysql',
        'db_host'  => 'root',
        'db_port'  => '3306',
        'db_user'  => 'root',
        'db_pwd'   => '312250544',
        'db_name'  => 'laohu_log',
        'db_charset'=>    'utf8',
        'DB_DEPLOY_TYPE'         => 1,
        'DB_RW_SEPARATE'         => true,
        'DB_MASTER_NUM'          => 1,
        'DB_SLAVE_NO'            => '',
    ),

    //  MONGO-DB
    'DB_TYPE_MONGO_CONFIG'  => array(
        'DB_TYPE'   => 'mongo',
        'DB_HOST'   =>  'localhost',
        'DB_PORT'   =>  '27017',
        'DB_NAME'   =>  'laohu_log',
        'DB_USER'   =>  '',//'laohu_log',
        'DB_PWD'    =>  '',//'ts',
    ),

);