<?php

if (!defined('APP_NAME'))
    exit();
return array(
    //PDO连接方式
    'DB_TYPE' => 'pdo', // 数据库类型
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => '', // 密码
    'DB_PREFIX' => 'kx168_', // 数据库表前缀 
    'DB_DSN' => 'mysql:host=127.0.0.1;dbname=kx168_cn;charset=UTF8'
);