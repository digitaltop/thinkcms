<?php

if (!defined('APP_NAME'))
    die('not in system');
$database = require ('./db.config.php');
$config = array(
    'APP_GROUP_LIST' => 'default,admin',
    'DEFAULT_GROUP' => 'default',
    'APP_FILE_CASE' => TRUE, //是否检查文件的大小写 对Windows平台有效
    'SHOW_ERROR_MSG' => FALSE, //是否显示错误信息
//    'HTML_CACHE_ON' => TRUE,
//    'HTML_CACHE_TIME' => '60',
//    'HTML_FILE_SUFFIX' => '.html',
//    'HTML_CACHE_RULES' => array(
//        '*' => array('{catname}{$_SERVER.REQUEST_URI}', '86400')
//    ),
    'DATA_CACHE_TYPE' => 'Memcache',
    'MEMCACHE_HOST' => 'tcp://127.0.0.1:11211',
    'DATA_CACHE_TIME' => '60',
    'URL_HTML_SUFFIX' => 'html', //URL伪静态后缀设置
    'URL_MODEL' => 2, //REWRITE  模式
    'DEFAULT_CHARSET' => 'utf-8', //模板模板编码
    'OUTPUT_CHARSET' => 'utf-8', //默认输出编码
    'APP_AUTOLOAD_PATH' => 'ORG.Util',
    'BASE_URL' => 'http://www.kx168.cn/',
    'APP_TITLE' => '开心健康网',
    'JS_PATH' => 'http://js.kx168.cn/',
    'CSS_PATH' => 'http://css.kx168.cn/',
    'ATTH_PATH' => 'http://u.kx168.cn/',
    'IMG_PATH' => 'http://img.kx168.cn/',
    'SEO' => array(
        'url_model'=>4,//前台URL样式　　０http://www.xx.com/index.php?m=Index&a=index  1http://www.xx.com/module-action-id-1.html   2http://www.xx.com/model/action/id/1  3http://www.xx.com/?s=/module/action/id/1/  4http://www.xx.com/category/2013/0112/id.html
        'name' => '开心健康网',
        'site_title' => '开心健康网,有开心,就有健康',
        'keywords' => '开心健康网,CC120,开心健康信息网,两性健康,不孕不育,性保健,月经不调,性知识,夫妻性生活',
        'description' => '开心健康网,CC120-开心健康信息网,中国三大健康网,是男性健康、女性健康的两性教育平台。专业提供中国健康信息,两性健康,两性生活,两性视频,两性教育,健康饮食,健康避孕,心理健康,健康知识等内容为主体。cc120健康网是广大青年男女普及性知识的良师益友。'),
    'APP_SUB_DOMAIN_DEPLOY' => 1,
    'APP_SUB_DOMAIN_RULES' => array(
        'admin'=>array('admin/'),
        'buyunbuyu' => array('default/Index', 'a=content&catid=6&modelid=1&catname=buyunbuyu&child=1'),
        'fuke' => array('default/Index', 'a=content&catid=22&modelid=1&catname=fuke&child=1'),
        'ganbing' => array('default/Index', 'a=content&catid=92&modelid=1&catname=ganbing&child=1'),
        'jingzhuibing' => array('default/Index', 'a=content&catid=141&modelid=1&catname=jingzhuibing&child=1'),
        'shenbing' => array('default/Index', 'a=content&catid=161&modelid=1&catname=shenbing&child=1'),
        'shimianyiyu' => array('default/Index', 'a=content&catid=205&modelid=1&catname=shimianyiyu&child=1'),
        'tangniaobing' => array('default/Index', 'a=content&catid=217&modelid=1&catname=tangniaobing&child=1'),
        'weibing' => array('default/Index', 'a=content&catid=259&modelid=1&catname=weibing&child=1'),
        'xizangbing' => array('default/Index', 'a=content&catid=301&modelid=1&catname=xizangbing&child=1'),
        'yaojianpan' => array('default/Index', 'a=content&catid=332&modelid=1&catname=yaojianpan&child=1'),
        'gugutouhuaisi' => array('default/Index', 'a=content&catid=353&modelid=1&catname=gugutouhuaisi&child=1'),
        'gangchang' => array('default/Index', 'a=content&catid=366&modelid=1&catname=gangchang&child=1'),
        'miniao' => array('default/Index', 'a=content&catid=379&modelid=1&catname=miniao&child=1'),
        'yanke' => array('default/Index', 'a=content&catid=399&modelid=1&catname=yanke&child=1'),
        'zhongliu' => array('default/Index', 'a=content&catid=421&modelid=1&catname=zhongliu&child=1'),
        'xingbing' => array('default/Index', 'a=content&catid=441&modelid=1&catname=xingbing&child=1'),
        'fengshi' => array('default/Index', 'a=content&catid=457&modelid=1&catname=fengshi&child=1'),
        'zhongfeng' => array('default/Index', 'a=content&catid=475&modelid=1&catname=zhongfeng&child=1'),
        'nanke' => array('default/Index', 'a=content&catid=496&modelid=1&catname=nanke&child=1'),
        'xinxueguan' => array('default/Index', 'a=content&catid=516&modelid=1&catname=xinxueguan&child=1'),
        'jingshenbing' => array('default/Index', 'a=content&catid=542&modelid=1&catname=jingshenbing&child=1'),
        'kouqiang' => array('default/Index', 'a=content&catid=565&modelid=1&catname=kouqiang&child=1'),
        'gaoxueya' => array('default/Index', 'a=content&catid=598&modelid=1&catname=gaoxueya&child=1'),
        'pifu' => array('default/Index', 'a=content&catid=628&modelid=1&catname=pifu&child=1'),
        'guke' => array('default/Index', 'a=content&catid=645&modelid=1&catname=guke&child=1'),
        'erke' => array('default/Index', 'a=content&catid=670&modelid=1&catname=erke&child=1'),
        'renliu' => array('default/Index', 'a=content&catid=678&modelid=1&catname=renliu&child=1'),
        'niupixuan' => array('default/Index', 'a=content&catid=688&modelid=1&catname=niupixuan&child=1'),
        'baidianfeng' => array('default/Index', 'a=content&catid=701&modelid=1&catname=baidianfeng&child=1'),
        'jijiu' => array('default/Index', 'a=content&catid=714&modelid=1&catname=jijiu&child=1'),
        'huli' => array('default/Index', 'a=content&catid=751&modelid=1&catname=huli&child=1'),
        'pianfang' => array('default/Index', 'a=content&catid=841&modelid=1&catname=pianfang&child=1'),
        'zhongyi' => array('default/Index', 'a=content&catid=935&modelid=1&catname=zhongyi&child=1'),
        'yaopin' => array('default/Index', 'a=content&catid=1033&modelid=1&catname=yaopin&child=1'),
        'nanxing' => array('default/Index', 'a=content&catid=1057&modelid=1&catname=nanxing&child=1'),
        'meirong' => array('default/Index', 'a=content&catid=1107&modelid=1&catname=meirong&child=1'),
        'xinli' => array('default/Index', 'a=content&catid=1223&modelid=1&catname=xinli&child=1'),
        'muying' => array('default/Index', 'a=content&catid=1316&modelid=1&catname=muying&child=1'),
        'jianfei' => array('default/Index', 'a=content&catid=1386&modelid=1&catname=jianfei&child=1'),
        'sex' => array('default/Index', 'a=content&catid=1458&modelid=1&catname=sex&child=1'),
        'baojian' => array('default/Index', 'a=content&catid=1572&modelid=1&catname=baojian&child=1'),
        'yinshi' => array('default/Index', 'a=content&catid=1644&modelid=1&catname=yinshi&child=1'),
        'jianshen' => array('default/Index', 'a=content&catid=1700&modelid=1&catname=jianshen&child=1'),
        'jiankangtupu' => array('default/Index', 'a=content&catid=1768&modelid=2&catname=jiankangtupu&child=1'),
        'tijian' => array('default/Index', 'a=content&catid=1877&modelid=1&catname=tijian&child=1'),
        'dianxian' => array('default/Index', 'a=content&catid=1900&modelid=1&catname=dianxian&child=1'),
        'bagua' => array('default/Index', 'a=content&catid=1915&modelid=1&catname=bagua&child=1'),
        'about' => array('default/Index', 'a=content&catid=1935&modelid=1&catname=about&child=1'),
    ),
);
return array_merge($database, $config);