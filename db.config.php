<?php

if (!defined('APP_NAME'))
    exit();
return array(
    //PDO连接方式
    'DB_TYPE' => 'pdo', // 数据库类型
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => '', // 密码
    'DB_PREFIX' => 'kx168_', // 数据库表前缀 
    'DB_DSN' => 'mysql:host=127.0.0.1;dbname=kx168_cn;charset=UTF8',
    //'DB_SQL_BUILD_CACHE' => true, //数据库查询的SQL创建缓存
    //'DB_SQL_BUILD_QUEUE' => 'Memcache', //SQL缓存队列的缓存方式
    //'DB_SQL_BUILD_LENGTH' => 50, //SQL缓存的队列长度
    //'DB_SQL_LOG' => true, //是否开启SQL日志记录
    //'DATA_CACHE_COMPRESS' => true, //数据缓存是否压缩缓存
    //'DATA_CACHE_TYPE' => 'Memcache', //数据缓存类型
    //'DATA_CACHE_CHECK' => false, //数据缓存是否校验缓存
    //'MEMCACHE_HOST' => 'tcp://127.0.0.1:11211',
    //'DATA_CACHE_TIME' => '3600',
);