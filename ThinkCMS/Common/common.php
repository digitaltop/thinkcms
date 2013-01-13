<?php

/*
 * 加解密字符串
 */

function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    $ckey_length = 32;

    $key = md5($key ? $key : AD_KEY );
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), - $ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0 ) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey [$i] = ord($cryptkey [$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box [$i] + $rndkey [$i]) % 256;
        $tmp = $box [$i];
        $box [$i] = $box [$j];
        $box [$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box [$a]) % 256;
        $tmp = $box [$a];
        $box [$a] = $box [$j];
        $box [$j] = $tmp;
        $result .= chr(ord($string [$i]) ^ ($box [($box [$a] + $box [$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

/**
 * Encoding : UTF-8
 * Created on : 2011-6-15 9:47:53 by ZhengYi , 13880273017@139.com
 */
/*
 * 作用：检测用户IP是否允许登录，只要有一个匹配则不再检测，默认允许登录 参数：用户ID 返回：true允许登录 false禁止登录
 */
function checkUserIP($UserID) {
    if ($UserID > 0) {
        $ClientIP = getClientIP();
        // 检测所有用户的规则
        $where = '`status`=1 and (`user_id`=-1 or `user_id`=' . $UserID . ')';
        $order = '`listorder` asc';
        $Dao = D('Acl');
        $Data = $Dao->where($where)->order($order)->select();
        if (false == $Data) {
            return true;
        } else { // 无全局规则限制
            foreach ($Data as $NewData) {
                if ($NewData ['isnot'] == 1 && matchCIDR($ClientIP, $NewData ['ip'])) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    } else {
        return false;
    }
}

/*
 * 作用：匹配IP地址是否在同一网段 参数：源IP,目标IP/子网 返回：true or false
 */

function matchCIDR($addr, $cidr) {
    list ( $ip, $mask ) = explode('/', $cidr);
    return (ip2Long($addr) >> (32 - $mask) == ip2Long($ip) >> (32 - $mask));
}

/*
 * 作用：生成密码 参数：明文密码 返回：加密后的密码
 */

function passWd($password) {
    return crypt($password, md5($password));
}

/*
 * 获取用户部门 返回：部门ID
 */

function getUserDepartment($UserID) {
    if ($UserID > 0) {
        $Dao = D('User');
        $rs = $Dao->where('`user_id`=' . $UserID)->field('`main_department_id`')->find();
        $ids = $rs ['main_department_id'] . ',';
        $Dao = D('User_department');
        $F = $Dao->field('`dept_id`')->where('`user_id`=' . $UserID)->select();
        foreach ($F as $k => $v) {
            $ids .= $v ['dept_id'] . ',';
        }
        $ids = array(
            'in',
            substr($ids, 0, strlen($ids) - 1)
        );
        return $ids;
    } else {
        return false;
    }
}

/*
 * 获取用户角色 返回：角色ID
 */

function getUserRole($UserID) {
    if ($UserID > 0) {
        // 主角色
        $Dao = D('User');
        $rs = $Dao->where('`user_id`=' . $UserID)->field('`main_role_id`')->find();
        $ids = $rs ['main_role_id'] . ',';
        // 辅助角色
        $Dao = D('User_role');
        $F = $Dao->field('`role_id`')->where('`user_id`=' . $UserID)->select();
        foreach ($F as $k => $v) {
            $ids .= $v ['role_id'] . ',';
        }
        $ids = array(
            'in',
            substr($ids, 0, strlen($ids) - 1)
        );
        return $ids;
    } else {
        return false;
    }
}

/*
 * 获取用户自定义组，也包括管理员定义的 返回：自定义组的ID
 */

function getUserCustomize($UserID) {
    if ($UserID > 0) {
        $Dao = D('User_customize');
        $F = $Dao->field('`customize_id`')->where('`user_id`=' . $UserID . ' or `user_id`=0')->select();
        if (!$F) {
            return false;
        } else {
            foreach ($F as $k => $v) {
                $ids .= ',' . $v ['customize_id'];
            }
            $ids = array(
                'in',
                substr($ids, 1, strlen($ids))
            );
            return $ids;
        }
    } else {
        return false;
    }
}

/*
 * 获取用户主题，如果没用，则使用默认值
 */

function getUserThemes($UserID) {
    $default = C('DEFAULT_USER_THEME');
    if ($UserID > 0) {
        $Dao = D('User');
        $F = $Dao->where('`user_id`=' . $UserID)->field('`themes`')->find();
        if (!$F) {
            return $default;
        } elseif ($F ['themes'] == '') {
            return $default;
        } else {
            return $F ['themes'];
        }
    } else {
        return $default;
    }
}

/*
 * 判断用户ID是否正确
 */

function checkUserID($UserID) {
    if ($UserID > 0) {
        $Dao = D('User');
        $F = $Dao->where('`user_id`=' . $UserID)->field('`user_id`')->find();
        if (!$F) {
            return false;
        } else {
            return true;
        }
    } else {
        return false;
    }
}

/*
 * 按用户认证模块 返回：true or false
 */

function userPermisAuth($UserID, $ActinId) {
    $Dao = D('User_menu_permis');
    $condition = array();
    $condition ['user_id'] = $UserID;
    $condition ['menu_id'] = $ActinId;
    $rs = $Dao->where($condition)->count();
    if ($rs > 0) {
        return true;
    } else {
        return false;
    }
}

/*
 * 按部门认证模块
 */

function departmentPermisAuth($Dept_ID, $ActionID) {
    $Dao = D('Depart_menu_permis');
    $condition = array();
    $condition ['dept_id'] = $Dept_ID;
    $condition ['menu_id'] = $ActionID;
    $rs = $Dao->where($condition)->count();
    // echo('<br><br/>' . $Dao->getLastSql() . '<br>的结果有' . $rs .
    // '条记录<br/><br/>');
    if ($rs > 0) {
        return true;
    } else {
        return false;
    }
}

/*
 * 按角色认证模块
 */

function rolePermisAuth($RoleID, $ActionID) {
    $Dao = D('Role_menu_permis');
    $condition = array();
    $condition ['role_id'] = $RoleID;
    $condition ['menu_id'] = $ActionID;
    $rs = $Dao->where($condition)->count();
    if ($rs > 0) {
        return true;
    } else {
        return false;
    }
}

/*
 * 判断指定用户是否对功能项拥有权限
 */

function checkAction($UserID, $ActionID) {
    if (checkUserID($UserID)) {
        $condition = array();
        $Dao = D('Menu');
        $condition ['status'] = 1;
        $condition ['menu_id'] = $ActionID;
        $rs = $Dao->field('`permis`,`menu_id`')->where($condition)->find();
        if (!$rs) {
            // 无效的功能项
            return false;
        } else {
            // 功能项正确
            if (intval($rs ['permis']) == 0) {
                // 当前为不用强制认证的功能项,直接返回拥有的权限，不再判断
                return true;
            } else {
                // 需要认证
                // 按用户认证
                $ckUser = userPermisAuth($UserID, $ActionID);
                // echo('<br/>按用户' . $UserID . '认证' . $ActionID . ' 结果：' .
                // $ckUser);
                // 按部门认证
                $Dept_ID = getUserDepartment($UserID); // 获取用户部门
                $ckDept = departmentPermisAuth($Dept_ID, $ActionID);
                // echo('<br/>按部门' . $Dept_ID . '认证' . $ActionID . ' 结果：' .
                // $ckDept);
                // 按角色认证
                $Role_ID = getUserRole($UserID); // 获取用户角色
                $ckPosi = rolePermisAuth($Role_ID, $ActionID);
                // echo('<br/>按角色' . $Position_ID . '认证' . $ActionID . ' 结果：' .
                // $ckPosi);
                if ($ckUser || $ckDept || $ckPosi) {
                    // echo('<br/>最后结果:真<br/><br/>');
                    return true;
                } else {
                    // echo('<br/>最后结果:假<br/><br/>');
                    return false;
                }
            }
        }
    } else {
        return false;
    }
}

/*
 * 作用：根据用户ID是否拥有该功能的操作权限 参数：用户ID，功能名称 返回：false不拥有，true拥有
 */

function checkSecurity($UserID, $module, $action) {
    if ($UserID == 1)
        return true; //如果是管理员，ID=1,不判断权限
    if (checkUserID($UserID)) {
        $Dao = D('Menu');
        $condition = array();
        $condition ['menu_name'] = $module;
        $condition ['status'] = 1;
        $rs = $Dao->field('`menu_id`')->where($condition)->select();
        // echo($Dao->getLastSql());
        if (!$rs) {
            return false;
        } else {
            // 同一类名下可能有多个方法
            $mid = '';
            foreach ($rs as $k => $v) {
                $mid .= $v ['menu_id'] . ',';
            }
            $mid = substr($mid, 0, strlen($mid) - 1);

            $condition = array();
            $condition ['menu_name'] = $action;
            $condition ['parentid'] = array('in', $mid);
            $condition ['status'] = 1;
            $rs = $Dao->field('`parentid`,`menu_id`')->where($condition)->find();
            // echo($Dao->getLastSql());
            if (!$rs) {
                return false;
            } else {
                $model_id = $rs ['parentid'];
                $action_id = $rs ['menu_id'];
            }
        }

        if (checkAction($UserID, $model_id) && checkAction($UserID, $action_id)) {
            return true;
        } else {
            return false;
        }
    }
}

/*
 * 更新登录用户信息 作用：更新用户登录IP和时间 参数：用户ID 返回：true 修改成功 false 失败
 */

function updataLogin($UserID) {
    Import('ORG.Util.HashMap');
    $map = array();
    $data = array();
    $Dao = D('User');
    $Dao->setInc('login_count', '`user_id`=' . $UserID);
    $data ['update_time'] = date('Y-m-d H:i:s', time());
    $data ['last_login_ip'] = getClientIP();
    $map ['user_id'] = $UserID;
    return $Dao->where($map)->save($data);
}

/**
 * +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
 * +----------------------------------------------------------
 *
 * @param string $len
 *        	长度
 * @param string $type
 *        	字串类型
 *        	0 字母 1 数字 其它 混合
 * @param string $addChars
 *        	额外字符
 *        	+----------------------------------------------------------
 * @return string +----------------------------------------------------------
 */
function rand_string($len = 6, $type = '', $addChars = '') {
    $str = '';
    switch ($type) {
        case 0 :
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 1 :
            $chars = str_repeat('0123456789', 3);
            break;
        case 2 :
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
            break;
        case 3 :
            $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 4 :
            $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借" . $addChars;
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
            break;
    }
    if ($len > 10) { // 位数过长重复字符串一定次数
        $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
    }
    if ($type != 4) {
        $chars = str_shuffle($chars);
        $str = substr($chars, 0, $len);
    } else {
        // 中文随机字
        for ($i = 0; $i < $len; $i++) {
            $str .= substr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
        }
    }
    return $str;
}

/*
 * 检测值是否大于0
 */

function _checkVal($val) {
    if ($val > 0) {
        return true;
    } else {
        return false;
    }
}

/*
 * 加密字符串
 */

function safe_b64encode($string) {
    $data = base64_encode($string);
    $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
    return $data;
}

/*
 * 解密字符串
 */

function safe_b64decode($string) {
    $data = str_replace(array('-', '_'), array('+', '/'), $string);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    return base64_decode($data);
}

/*
 * 读取附件 参数：附件ID（多个ID之间用,分开）：用户ID： 返回：数组(显示的文件名,附件上传时间，附件ID,上传人ID)
 */

function getAttachments($attIds, $userid) {
    // 去除无效的分隔符号
    $str = preg_replace('/[,]{2,}/', ',', $attIds);
    $str = preg_replace("/\s/", "", $str);
    $a = explode(",", trim($str, ','));
    $b = array_unique($a);
    $attIds = '';
    foreach ($b as $k) {
        $attIds .= $k . ',';
    }
    $attIds = substr($attIds, 0, strlen($attIds) - 1);
    // 首先读取该功能模块有多少个附件
    $attachments = array(); // 需返回的结果
    $i = 0;
    $condition = array();
    $Dao = D('Attachments');
    $condition ['attachments_id'] = array(
        'in',
        $attIds
    );
    $rs = $Dao->where($condition)->order('`listorder` asc,`attachments_id` desc')->select();
    $apDao = D('Attachments_permissions');
    foreach ($rs as $v) {
        $condition = array();
        $condition ['attachments_id'] = $v ['attachments_id'];
        $condition ['user_id'] = $userid;
        $aprs = $apDao->where($condition)->order('`permissions_id` desc')->find();
        if ($aprs) {
            // 已针对该用户设置了附件权限
            if ($aprs ['permissions'] == 1) {
                // 只读附件，不返回附件url
                $attachments [$i] ['attachments_id'] = $v ['attachments_id'];
                $attachments [$i] ['filename'] = $v ['attachments_name'];
                $attachments [$i] ['create_time'] = $v ['create_time'];
                $attachments [$i] ['fileid'] = '';
                $attachments [$i] ['userid'] = $v ['user_id'];
                $i++;
            } elseif ($aprs ['permissions'] == 2) {
                // 允许下载
                $attachments [$i] ['attachments_id'] = $v ['attachments_id'];
                $attachments [$i] ['filename'] = $v ['attachments_name'];
                $attachments [$i] ['create_time'] = $v ['create_time'];
                $attachments [$i] ['fileid'] = $v ['attachments_id'];
                $attachments [$i] ['userid'] = $v ['user_id'];
                $i++;
            } else {
                // 不允许下载，所以不显示
            }
        } else {
            // 未针对该用户设置权限
            $attachments [$i] ['attachments_id'] = $v ['attachments_id'];
            $attachments [$i] ['filename'] = $v ['attachments_name'];
            $attachments [$i] ['create_time'] = $v ['create_time'];
            $attachments [$i] ['fileid'] = $v ['attachments_id'];
            $attachments [$i] ['userid'] = $v ['user_id'];
            $i++;
        }
    }
    return $attachments;
}

/*
 * 取得当前时间o
 */

function getNowTime() {
    return date('Y-m-d H:i:s');
}

/*
 * 查询功能顶
 */

function _verChildrenAction($id) {
    $Dao = D('Menu');
    $rs = $Dao->where('`parentid`=' . $id . ' and `status`=1 and `menu`=1')->count();
    if ($rs > 0) {
        return true;
    } else {
        return false;
    }
}

/*
 * 根据ActionID返回ModelName
 */

function getModelName($id) {
    $Dao = D('Menu');
    $rs = $Dao->where('`menu_id`=' . $id)->field('menu_name')->find();
    return $rs ['menu_name'];
}

/*
 * 清除用户与角色的关系
 */

function cleanUserRole($UserID) {
    if (checkUserID($UserID)) {
        $Dao = D('User_role');
        $res = $Dao->where('`user_id` = ' . $UserID)->delete();
        return $res;
    } else {
        return false;
    }
}

/*
 * 添加用户与角色的关系
 */

function insertUserRole($UserID, $RoleID) {
    if (checkUserID($UserID)) {
        $Dao = D('User_role');
        $arr = explode(',', $RoleID);
        for ($i = 0; $i < count($arr); $i++) {
            if ($arr [$i] > 0) {
                $data = array();
                $data ['user_id'] = $UserID;
                $data ['role_id'] = $arr [$i];
                $Dao->add($data);
            }
        }
        return true;
    } else {
        return false;
    }
}

/*
 * 清除用户与部门的关系
 */

function cleanUserDepartment($UserID) {
    if (checkUserID($UserID)) {
        $Dao = D('User_department');
        $res = $Dao->where('`user_id` = ' . $UserID)->delete();
        return $res;
    } else {
        return false;
    }
}

/*
 * 添加用户与部门的关系
 */

function insertUserDepartment($UserID, $DepartmentID) {
    if (checkUserID($UserID)) {
        $Dao = D('User_department');
        $arr = explode(',', $DepartmentID);
        for ($i = 0; $i < count($arr); $i++) {
            if ($arr [$i] > 0) {
                $data = array();
                $data ['user_id'] = $UserID;
                $data ['dept_id'] = $arr [$i];
                $Dao->add($data);
            }
        }
        return true;
    } else {
        return false;
    }
}

/*
 * 清除角色与权限对应
 */

function cleanRoleAuthorization($roleid) {
    $Dao = D('Role_menu_permis');
    $rs = $Dao->where('`role_id`=' . $roleid)->delete();
    if ($rs) {
        return false;
    } else {
        return false;
    }
}

/*
 * 清除部门与权限对应
 */

function cleanDeptAuthorization($deptid) {
    $Dao = D('Depart_menu_permis');
    $rs = $Dao->where('`dept_id`=' . $deptid)->delete();
    if ($rs) {
        return false;
    } else {
        return false;
    }
}

/*
 * 清除用户与权限对应
 */

function cleanUserAuthorization($user_id) {
    $Dao = D('User_menu_permis');
    $rs = $Dao->where('`user_id`=' . $user_id)->delete();
    if ($rs) {
        return false;
    } else {
        return false;
    }
}

/*
 * 根据IP返回IP整数
 */

function ip2int($ip) {
    list ( $ip1, $ip2, $ip3, $ip4 ) = explode(".", $ip);
    return $ip1 * pow(256, 3) + $ip2 * pow(256, 2) + $ip3 * 256 + $ip4;
}

// //获得访客浏览器类型
function getClientBrowser() {
    if (!empty($_SERVER ['HTTP_USER_AGENT'])) {
        $br = $_SERVER ['HTTP_USER_AGENT'];
        if (preg_match('/MSIE/i', $br)) {
            $br = 'MSIE';
        } elseif (preg_match('/Firefox/i', $br)) {
            $br = 'Firefox';
        } elseif (preg_match('/Chrome/i', $br)) {
            $br = 'Chrome';
        } elseif (preg_match('/Safari/i', $br)) {
            $br = 'Safari';
        } elseif (preg_match('/Opera/i', $br)) {
            $br = 'Opera';
        } else {
            $br = 'Other';
        }
        return $br;
    } else {
        return "获取浏览器信息失败！";
    }
}

// //获得访客浏览器语言
function getClientLang() {
    if (!empty($_SERVER ['HTTP_ACCEPT_LANGUAGE'])) {
        $lang = $_SERVER ['HTTP_ACCEPT_LANGUAGE'];
        $lang = substr($lang, 0, 5);
        if (preg_match("/zh-cn/i", $lang)) {
            $lang = "简体中文";
        } elseif (preg_match("/zh/i", $lang)) {
            $lang = "繁体中文";
        } else {
            $lang = "English";
        }
        return $lang;
    } else {
        return "获取浏览器语言失败！";
    }
}

// //获取访客操作系统
function getClientOs() {
    if (!empty($_SERVER ['HTTP_USER_AGENT'])) {
        $OS = $_SERVER ['HTTP_USER_AGENT'];
        if (preg_match('/win/i', $OS)) {
            $OS = 'Windows';
        } elseif (preg_match('/mac/i', $OS)) {
            $OS = 'MAC';
        } elseif (preg_match('/linux/i', $OS)) {
            $OS = 'Linux';
        } elseif (preg_match('/unix/i', $OS)) {
            $OS = 'Unix';
        } elseif (preg_match('/bsd/i', $OS)) {
            $OS = 'BSD';
        } else {
            $OS = 'Other';
        }
        return $OS;
    } else {
        return "获取访客操作系统信息失败！";
    }
}

// //获得访客真实ip
function getClientIP() {
    if ($_SERVER["HTTP_X_FORWARDED_FOR"])
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    else if ($_SERVER["HTTP_CLIENT_IP"])
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    else if ($_SERVER["REMOTE_ADDR"])
        $ip = $_SERVER["REMOTE_ADDR"];
    else if (getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else
        $ip = "Unknown";
    return $ip;
}

// //获得本地真实IP
function getClientOnlineip() {
    $mip = file_get_contents("http://city.ip138.com/city0.asp");
    if ($mip) {
        preg_match("/\[.*\]/", $mip, $sip);
        $p = array(
            "/\[/",
            "/\]/"
        );
        return preg_replace($p, "", $sip [0]);
    } else {
        return "获取本地IP失败！";
    }
}

// //根据ip获得访客所在地地名，0按本地数据库搜索，1按网络搜索 2按纯真IP搜索
function getClientAddress($ip = '', $cType = 1) {
    if (empty($ip)) {
        $ip = getClientIP();
    }
    switch ($cType) {
        case 1 :
            $area = json_decode(file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip), true);
            $r = array(
                'country' => $area ['data'] ['country'],
                'province' => $area ['data'] ['region'],
                'city' => $area ['data'] ['city']
            );
            break;
        case 2 :
            import('ORG.Net.IpLocation'); // 导入IpLocation类
            $Data = new IpLocation('qqwry.dat'); // 实例化类 参数表示IP地址库文件
            $area = $Data->getlocation($ip);
            $r = array(
                'country' => iconv('GB2312', 'UTF-8', $area ['country']),
                'area' => iconv('GB2312', 'UTF-8', $area ['area'])
            );
            break;
        default :
            $Dao = D('Area');
            $condition = array();
            $condition ['start'] = array(
                'elt',
                ip2int($ip)
            );
            $rs = $Dao->where($condition)->order('`start` desc')->find();
            $r = array(
                'country' => '中国',
                'province' => $rs ['province'],
                'city' => $rs ['city']
            );
            break;
    }
    return $r;
}

// 获取来自搜索引擎入站时的关键词
function get_keyword($url, $kw_start) {
    $start = stripos($url, $kw_start);
    $url = substr($url, $start + strlen($kw_start));
    $start = stripos($url, '&');
    if ($start > 0) {
        $start = stripos($url, '&');
        $s_s_keyword = substr($url, 0, $start);
    } else {
        $s_s_keyword = substr($url, 0);
    }
    return $s_s_keyword;
}

// 返回客户端使用的搜索引擎及关键词
function getClientSearchEngine() {
    $url = isset($_SERVER ['HTTP_REFERER']) ? $_SERVER ['HTTP_REFERER'] : ''; // 获取入站url。
    $search_1 = "google.com"; // q= utf8
    $search_2 = "baidu.com"; // wd= gbk
    $search_3 = "yahoo.cn"; // q= utf8
    $search_4 = "sogou.com"; // query= gbk
    $search_5 = "soso.com"; // w= gbk
    $search_6 = "bing.com"; // q= utf8
    $search_7 = "youdao.com"; // q= utf8

    $google = preg_match("/\b{$search_1}\b/", $url); // 记录匹配情况，用于入站判断。
    $baidu = preg_match("/\b{$search_2}\b/", $url);
    $yahoo = preg_match("/\b{$search_3}\b/", $url);
    $sogou = preg_match("/\b{$search_4}\b/", $url);
    $soso = preg_match("/\b{$search_5}\b/", $url);
    $bing = preg_match("/\b{$search_6}\b/", $url);
    $youdao = preg_match("/\b{$search_7}\b/", $url);
    $s_s_keyword = "";
    $bul = $_SERVER ['HTTP_REFERER'];
    // 获取没参数域名
    preg_match('@^(?:http://)?([^/]+)@i', $bul, $matches);
    $burl = $matches [1];
    // 匹配域名设置
    $curl = $_SERVER['SERVER_NAME'];
    $r = array(
        'searchName' => '',
        'searchKeyword' => ''
    );
    if ($burl != $curl) {
        if ($google) { // 来自google
            $s_s_keyword = get_keyword($url, 'q='); // 关键词前的字符为"q="。
            $s_s_keyword = urldecode($s_s_keyword);
            $urlname = "谷歌";
            $r ['searchName'] = $urlname;
            $r ['searchKeyword'] = $s_s_keyword;
            // $s_s_keyword=iconv("GBK","UTF-8",$s_s_keyword);//引擎为gbk
        } else if ($baidu) { // 来自百度
            $s_s_keyword = get_keyword($url, 'wd='); // 关键词前的字符为"wd="。
            $s_s_keyword = urldecode($s_s_keyword);
            $s_s_keyword = iconv("GBK", "UTF-8", $s_s_keyword); // 引擎为gbk
            $urlname = "百度";
            $r ['searchName'] = $urlname;
            $r ['searchKeyword'] = $s_s_keyword;
        } else if ($yahoo) { // 来自雅虎
            $s_s_keyword = get_keyword($url, 'q='); // 关键词前的字符为"q="。
            $s_s_keyword = urldecode($s_s_keyword);
            // $s_s_keyword=iconv("GBK","UTF-8",$s_s_keyword);//引擎为gbk
            $urlname = "雅虎";
            $r ['searchName'] = $urlname;
            $r ['searchKeyword'] = $s_s_keyword;
        } else if ($sogou) { // 来自搜狗
            $s_s_keyword = get_keyword($url, 'query='); // 关键词前的字符为"query="。
            $s_s_keyword = urldecode($s_s_keyword);
            $s_s_keyword = iconv("GBK", "UTF-8", $s_s_keyword); // 引擎为gbk
            $urlname = "搜狗";
            $r ['searchName'] = $urlname;
            $r ['searchKeyword'] = $s_s_keyword;
        } else if ($soso) { // 来自搜搜
            $s_s_keyword = get_keyword($url, 'w='); // 关键词前的字符为"w="。
            $s_s_keyword = urldecode($s_s_keyword);
            $s_s_keyword = iconv("GBK", "UTF-8", $s_s_keyword); // 引擎为gbk
            $urlname = "搜搜";
            $r ['searchName'] = $urlname;
            $r ['searchKeyword'] = $s_s_keyword;
        } else if ($bing) { // 来自必应
            $s_s_keyword = get_keyword($url, 'q='); // 关键词前的字符为"q="。
            $s_s_keyword = urldecode($s_s_keyword);
            // $s_s_keyword=iconv("GBK","UTF-8",$s_s_keyword);//引擎为gbk
            $urlname = "必应";
            $r ['searchName'] = $urlname;
            $r ['searchKeyword'] = $s_s_keyword;
        } else if ($youdao) { // 来自有道
            $s_s_keyword = get_keyword($url, 'q='); // 关键词前的字符为"q="。
            $s_s_keyword = urldecode($s_s_keyword);
            // $s_s_keyword=iconv("GBK","UTF-8",$s_s_keyword);//引擎为gbk
            $urlname = "有道";
            $r ['searchName'] = $urlname;
            $r ['searchKeyword'] = $s_s_keyword;
        } else {
            $urlname = $burl;
            $s_s_keyword = "";
            $r ['searchName'] = $urlname;
            $r ['searchKeyword'] = $s_s_keyword;
        }
        $s_urlname = $urlname;
        $s_urlkey = $s_s_keyword;
    } else {
        $s_urlname = $r ['searchName'];
        $s_urlkey = $r ['searchKeyword'];
    }
    return $r;
}

// 返回汉字的第一个字母
function getFirstChar($s0) {
    $firstchar_ord = ord(strtoupper($s0 {0}));
    if (($firstchar_ord >= 65 and $firstchar_ord <= 91) or ($firstchar_ord >= 48 and $firstchar_ord <= 57))
        return $s0 {0};
    $s = iconv("UTF-8", "gb2312", $s0);
    $asc = ord($s {0}) * 256 + ord($s {1}) - 65536;
    if ($asc >= - 20319 and $asc <= - 20284)
        return "A";
    if ($asc >= - 20283 and $asc <= - 19776)
        return "B";
    if ($asc >= - 19775 and $asc <= - 19219)
        return "C";
    if ($asc >= - 19218 and $asc <= - 18711)
        return "D";
    if ($asc >= - 18710 and $asc <= - 18527)
        return "E";
    if ($asc >= - 18526 and $asc <= - 18240)
        return "F";
    if ($asc >= - 18239 and $asc <= - 17923)
        return "G";
    if ($asc >= - 17922 and $asc <= - 17418)
        return "H";
    if ($asc >= - 17417 and $asc <= - 16475)
        return "J";
    if ($asc >= - 16474 and $asc <= - 16213)
        return "K";
    if ($asc >= - 16212 and $asc <= - 15641)
        return "L";
    if ($asc >= - 15640 and $asc <= - 15166)
        return "M";
    if ($asc >= - 15165 and $asc <= - 14923)
        return "N";
    if ($asc >= - 14922 and $asc <= - 14915)
        return "O";
    if ($asc >= - 14914 and $asc <= - 14631)
        return "P";
    if ($asc >= - 14630 and $asc <= - 14150)
        return "Q";
    if ($asc >= - 14149 and $asc <= - 14091)
        return "R";
    if ($asc >= - 14090 and $asc <= - 13319)
        return "S";
    if ($asc >= - 13318 and $asc <= - 12839)
        return "T";
    if ($asc >= - 12838 and $asc <= - 12557)
        return "W";
    if ($asc >= - 12556 and $asc <= - 11848)
        return "X";
    if ($asc >= - 11847 and $asc <= - 11056)
        return "Y";
    if ($asc >= - 11055 and $asc <= - 10247)
        return "Z";
    return null;
}

/**
 * 生成广告
 * @param type $id  广告ID
 * @return type string 广告JS代码
 */
function getAd($id) {
    return '<script type="text/javascript">
  var u_key = \'' . $id . '\';
  		document.write("<sc" + "ript lan" + "guage=\'javascript\' ty" + "pe=\'text/javascript\' sr" + "c=\'http://union.kx168.cn/Cpro/c\'><" + "/sc" + "ript>");
</script>';
}

/**
 * 生成标题样式
 * @param $style   样式
 * @param $html    是否显示完整的STYLE
 */
function title_style($style, $html = 1) {
    //以下为修改后的代码 20120801
    if (!empty($style) && $style !== ';') {
        $str = '';
        if ($html)
            $str = ' style="';
        $style_arr = explode(';', $style);
        if (!empty($style_arr[0]))
            $str .= 'color:' . $style_arr[0] . ';';
        if (!empty($style_arr[1]))
            $str .= 'font-weight:' . $style_arr[1] . ';';
        if ($html)
            $str .= '"';
        return $str;
    }
}

/**
 * 字符截取 支持UTF8/GBK
 * @param $string
 * @param $length
 * @param $dot
 */
function str_cut($string, $length, $dot = '...') {
    $strlen = strlen($string);
    if ($strlen <= $length)
        return $string;
    $string = str_replace(array(' ', '&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵', ' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
    $strcut = '';
    if (strtolower(C('DEFAULT_CHARSET')) == 'utf-8') {
        $length = intval($length - strlen($dot) - $length / 3);
        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {
            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t <= 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }
            if ($noc >= $length) {
                break;
            }
        }
        if ($noc > $length) {
            $n -= $tn;
        }
        $strcut = substr($string, 0, $n);
        $strcut = str_replace(array('∵', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array(' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
    } else {
        $dotlen = strlen($dot);
        $maxi = $length - $dotlen - 1;
        $current_str = '';
        $search_arr = array('&', ' ', '"', "'", '“', '”', '—', '<', '>', '·', '…', '∵');
        $replace_arr = array('&amp;', '&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;', ' ');
        $search_flip = array_flip($search_arr);
        for ($i = 0; $i < $maxi; $i++) {
            $current_str = ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
            if (in_array($current_str, $search_arr)) {
                $key = $search_flip[$current_str];
                $current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
            }
            $strcut .= $current_str;
        }
    }
    return $strcut . $dot;
}

/**
 * 批量替换
 * @param $search 需要替换的字符串
 * @param $replace 原始字符串
 * @param $subject 目标字符串
 * @param int $limit 替换次数
 */
function str_replace_limit($search, $replace, $subject, $limit = -1) {
    if (is_array($search)) {
        foreach ($search as $k => $v) {
            $search[$k] = '`' . preg_quote($search[$k], '`') . '`';
        }
    } else {
        $search = '`' . preg_quote($search, '`') . '`';
    }
    return preg_replace($search, $replace, $subject, $limit);
}

/**
 * 根据字段显示排序标识
 */
function orderField($orderDirection, $trueField, $viewField) {
    if ($trueField == $viewField) {
        return ' class="' . $orderDirection . '"';
    } else {
        return;
    }
}

/**
 * 生成缩略图函数
 * @param  $imgurl 图片路径
 * @param  $width  缩略图宽度
 * @param  $height 缩略图高度
 */
function thumb($imgurl, $width = 150, $height = 150) {
    $smallpic = isset($smallpic) ? $smallpic : 'nopic.gif';
    global $image;
    $upload_url = './upload/goods/';
    $upload_path = './upload/goods/';
    if (empty($imgurl))
        return './upload/goods/' . $smallpic;
    $imgurl_replace = str_replace($upload_url, '', $imgurl);

    if (!extension_loaded('gd') || strpos($imgurl_replace, '://'))
        return $imgurl;
    if (!file_exists($upload_path . $imgurl_replace))
        return $upload_url . $smallpic;

    list($width_t, $height_t, $type, $attr) = getimagesize($upload_path . $imgurl_replace);
    if ($width >= $width_t && $height >= $height_t)
        return $upload_url . $imgurl_replace;

    $newimgurl = 'thumb_' . $width . '_' . $height . '_' . basename($imgurl_replace);

    if (file_exists($upload_path . $newimgurl))
        return $upload_url . $newimgurl;

    if (!is_object($image)) {

        $image = new ThumbImage(1, 0);
    }
    return $image->thumb($upload_path . $imgurl_replace, $upload_path . $newimgurl, $width, $height, '', $autocut) ? $upload_url . $newimgurl : $imgurl;
}

//AJAX输出
function uiReturn($status = TRUE, $message = '操作成功', $forwardUrl = '', $other = '') {
    $info = array();
    $info['statusCode'] = ($status || $status == 1) ? 1 : 0;
    $info['message'] = $message;
    $info['forwardUrl'] = $forwardUrl;
    if ($other !== '')
        $info['attributes'] = $other;
    $data = $info;
    // 返回JSON数据格式到客户端 包含状态信息
    header('Content-Type:application/json; charset=utf-8');
    exit(json_encode($data));
}

/**
 * 导入栏目相关信息
 * @return array CATEGORYS 栏目相关信息
 */
function getCacheCategory($c = 0) {
    if (@file_exists('category.config.php')) {
        include_once 'category.config.php';
    } else {
        $c = 1;
    }
    if ($c == 1) {
        //栏目相关信息
        $category = createCategoryCache(0);
        if (!$handle = fopen('category.config.php', 'w+')) {
            exit('不能写入文件！');
        }
        $cont = '<?php $category = ' . var_export($category, ture) . ';';
        if (fwrite($handle, $cont) === FALSE) {
            exit('不能写入文件！');
        }
        fclose($handle);
        include_once 'category.config.php';
    }
    return $category;
}

/**
 * 生成栏目缓存文件
 */
function createCategoryCache($pid) {
    $Dao = M('Category');
    static $category = array();
    $rs = $Dao->where('`parentid`=' . $pid . ' and `isdelete`=0')->select();
    if ($pid == 0)
        $category[$pid]['arrparentid'] = '0';
    foreach ($rs as $k => $v) {
        (checkisSubCategory($v['catid'])) ? $isChild = 1 : $isChild = 0;
        $category[$v['catid']] = array();
        $category[$v['catid']]['modeid'] = $v['modeid'];
        $category[$v['catid']]['lanid'] = $v['lanid'];
        $category[$v['catid']]['parentid'] = $v['parentid'];
        $category[$v['catid']]['catname'] = $v['catname'];
        $category[$v['catid']]['viewlocation'] = $v['viewlocation'];
        $category[$v['catid']]['catdir'] = $v['catdir'];
        $category[$v['catid']]['ico'] = $v['ico'];
        $category[$v['catid']]['url'] = $v['url'];
        $category[$v['catid']]['target'] = $v['target'];
        $category[$v['catid']]['hits'] = $v['hits'];
        $category[$v['catid']]['rocords'] = $v['rocords'];
        $category[$v['catid']]['arrparentid'] = $category[$pid]['arrparentid'] . ',' . $v['catid'];
        $category[$v['catid']]['ischild'] = $isChild;
        if (1 === $isChild) {
            createCategoryCache($v['catid']);
        }
    }
    return $category;
}

/**
 * 根据文章ID，返回文章栏目ID
 */
function getNewsCategory($newsId) {
    $D = M('Article');
    $rs = $D->where('`id`=' . $newsId)->field('`catid`')->find();
    return $rs['catid'];
}

/**
 * 获取子栏目
 * @param $parentid 父级id
 * @param $self 是否包含本身 0为不包含
 * @return array 子栏目的数组
 */
function subCat($parentid = 0, $self = 0) {
    $Dao = M('Category');
    $rs = $Dao->where('`catid`=' . $parentid)->field('arrchildid')->find();
    if (!$rs) {
        return '0';
    }
    $r = explode(',', $rs['arrchildid']);
    if ($self == 0)
        array_shift($r);
    return $r;
}

/**
 * 输出纯新闻列表，无缩略图 
 * @param  array $list 所有文章的数组
 * @param int $TitleLine 显示的行数
 * @param int $coumLine 分栏行数，达到设置值时，会调用UL分栏
 * @param int $isCategory 分栏后，是否显示栏目链接
 * @param int $TitleLen 分行条件，当文章字数超过本值则会分行
 * @return  string 文章
 */
function getNewsList($list = array(), $TitleLine = 10, $coumLine = 10, $isCategory = 1, $TitleLen = 66) {
    $CATEGORYS = getCacheCategory();
    $x = 0;
    $hs = 0;
    $newsList = '<ul>';
    foreach ($list as $vo) {
        if ($hs <= $TitleLine) {
            if (!($tt))
                $tt = 0;
            if (($tt > $TitleLen) || ($x == 2) || ($x == 0))
                $hs++;
            $x++;
            if ($hs <= $TitleLine) {
                if ($coumLine > 0 && $hs == ($coumLine + 1) && $x !== 2)
                    $newsList.='</ul><ul class="list">';
                if ($x !== 2)
                    $newsList.='<li>';
                if ($hs > $coumLine && $x !== 2 && $isCategory == 1)
                    $newsList.='<a target="_blank" href="' . $CATEGORYS[$vo['catid']]['url'] . '">【' . $CATEGORYS[$vo['catid']]['catname'] . '】</a><span class="s"> | </span>';
                $newsList.=' <a href="' . $vo['url'] . '"' . title_style($vo['style']) . ' target="_blank" title="' . $vo['title'] . '">' . $vo['title'] . '</a>';
            }$tt = strlen($vo['title']);
            if (($tt > $TitleLen) || ($x == 2))
                $x = 0;
            if ($x == 2)
                $newsList.='</li>';
        }
    }
    return $newsList . '</ul>';
}

/**
 * 文章排行
 * @param int $num 排行数量
 * @param string $order 排序方式  d天排行 w周排行 m月排行 t总排行
 */
function getTopNews($num, $order = 'w') {
    switch ($order) {
        case 'd':
            $order = '`dayviews`';
            break;
        case 'w':
            $order = '`weekviews`';
            break;
        case 'm':
            $order = '`monthviews`';
            break;
        case 't':
        default:
            $order = '`views`';
            break;
    }
    $Dao = D('HitsView');
    $condition = array();
    $condition['status'] = 99;
    return $Dao->where($condition)->order($order . ' DESC')->limit($num)->select();
}

/**
 * 输出纯新闻列表，有缩略图
 * @param  array $list 所有文章的数组
 * @param int $TitleLine 显示的行数
 * @param int $coumLine 分栏行数，达到设置值时，会调用UL分栏
 * @param int $isCategory 分栏后，是否显示栏目链接
 * @param int $TitleLen 分行条件，当文章字数超过本值则会分行
 * @return  string 文章
 */
function getThumbNewsList($list = array(), $TitleLine = 10, $coumLine = 5, $isCategory = 0, $TitleLen = 66) {
    $CATEGORYS = getCacheCategory();
    $x = 0;
    $hs = 0;
    $newsList = '';
    foreach ($list as $vo) {
        if ($hs <= $TitleLine) {
            if (!($tt))
                $tt = 0;
            if (($tt > $TitleLen) || ($x == 2) || ($x == 0))
                $hs++;
            $x++;
            if ($hs == 1 && $x == 1) {
                $newsList.='<ul class="fl"><div class="news_in_pic"><div class="news_inpic_text"><a href="' . $vo['url'] . '" target="_blank"' . title_style($vo['style']) . ' title="' . $vo['title'] . '">' . str_cut($vo['title'], 30, '') . '</a></div><div class="news_inpic_tmdiv"></div><a href="' . $vo['url'] . '" target="_blank"><img src="' . thumb($vo['thumb'], 139, 102) . '" width="139" height="102" alt="' . $vo['title'] . '" /></a></div>';
                $x = 2;
            } else {
                if ($hs <= $TitleLine) {
                    if ($coumLine > 0 && $hs == ($coumLine + 1) && $x !== 2)
                        $newsList.='</ul><ul>';
                    if ($x !== 2)
                        $newsList.='<li>';
                    if ($hs > $coumLine && $x !== 2 && $isCategory == 1)
                        $newsList.='<a target="_blank" href="' . $CATEGORYS[$vo['catid']]['url'] . '">【' . $CATEGORYS[$vo['catid']]['catname'] . '】</a><span class="s"> | </span>';
                    $newsList.=' <a href="' . $vo['url'] . '"' . title_style($vo['style']) . ' target="_blank" title="' . $vo['title'] . '">' . $vo['title'] . '</a>';
                }
            }
            $tt = strlen($vo['title']);
            if (($tt > $TitleLen) || ($x == 2))
                $x = 0;
            if ($x == 2)
                $newsList.='</li>';
        }
    }
    return $newsList . '</ul>';
}

/**
 * 当前路径
 * 返回指定栏目路径层级
 * @param $catid 栏目id
 * @param $symbol 栏目间隔符
 */
function catpos($catid, $symbol = ' > ') {
    $category_arr = getCacheCategory();
    $pos = '';
    $siteurl = $category_arr[$catid]['url'];
    $arrparentid = array_filter(explode(',', $category_arr[$catid]['arrparentid'] . ',' . $catid));
    foreach ($arrparentid as $catid) {
        $url = $category_arr[$catid]['url'];
        if (strpos($url, '://') === false)
            $url = $siteurl . $url;
        $pos .= '<a href="' . $url . '">' . $category_arr[$catid]['catname'] . '</a>' . $symbol;
    }
    return $pos;
}

/**
 * 返回指定模型模板
 */
function getCMSModelName($mid) {
    $Dao = M('Model');
    $rs = $Dao->field('tablename')->where('`modelid`=' . $mid)->find();
    return $rs['tablename'];
}

/**
 * 查询栏目是否有子栏目
 */
function checkisSubCategory($catid) {
    $Dao = M('Category');
    $rs = $Dao->where('`parentid`=' . $catid)->count('catid');
    if ($rs > 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * 取当前栏目的属性，默认取栏目ID
 */
function getCategoryDir($catid, $field = 'catid') {
    $Dao = M('Category');
    $rs = $Dao->where('`catid`=' . $catid)->field($field)->find();
    return $rs[$field];
}

/**
 * 分页函数
 * 
 * @param $num 信息总数
 * @param $curr_page 当前分页
 * @param $pageurls 链接地址
 * @return 分页
 */
function content_pages($num, $curr_page, $pageurls) {
    $multipage = '';
    $page = 11;
    $offset = 4;
    $pages = $num;
    $from = $curr_page - $offset;
    $to = $curr_page + $offset;
    $more = 0;
    if ($page >= $pages) {
        $from = 2;
        $to = $pages - 1;
    } else {
        if ($from <= 1) {
            $to = $page - 1;
            $from = 2;
        } elseif ($to >= $pages) {
            $from = $pages - ($page - 2);
            $to = $pages - 1;
        }
        $more = 1;
    }
    if ($curr_page > 0) {
        $perpage = $curr_page == 1 ? 1 : $curr_page - 1;
        $multipage .= '<a class="a1" href="' . $pageurls[$perpage][0] . '">上一页</a>';
        if ($curr_page == 1) {
            $multipage .= ' <span>1</span>';
        } elseif ($curr_page > 6 && $more) {
            $multipage .= ' <a href="' . $pageurls[1][0] . '">1</a>..';
        } else {
            $multipage .= ' <a href="' . $pageurls[1][0] . '">1</a>';
        }
    }
    for ($i = $from; $i <= $to; $i++) {
        if ($i != $curr_page) {
            $multipage .= ' <a href="' . $pageurls[$i][0] . '">' . $i . '</a>';
        } else {
            $multipage .= ' <span>' . $i . '</span>';
        }
    }
    if ($curr_page < $pages) {
        if ($curr_page < $pages - 5 && $more) {
            $multipage .= ' ..<a href="' . $pageurls[$pages][0] . '">' . $pages . '</a> <a class="a1" href="' . $pageurls[$curr_page + 1][0] . '">下一页</a>';
        } else {
            $multipage .= ' <a href="' . $pageurls[$pages][0] . '">' . $pages . '</a> <a class="a1" href="' . $pageurls[$curr_page + 1][0] . '">下一页</a>';
        }
    } elseif ($curr_page == $pages) {
        $multipage .= ' <span>' . $pages . '</span> <a class="a1" href="' . $pageurls[$curr_page][0] . '">下一页</a>';
    }
    return $multipage;
}

/**
 * 相关文章
 * @param  $newsid 文章ID或关键词
 * @param int $catid 栏目ID
 * @param int $limit 取结果的数量
 * @param $language 语言，可以是语言ID，也可以是简写
 */
function getRelation($newsid, $limit = 10, $lang = 'cn', $catid = 0) {
    //语言的自动识别
    if (!is_numeric($lang)) {
        $L = M('Language')->field('`id`')->where('`lang`=\'' . $lang . '\'')->find();
        if ($L) {
            $lang = $L['id'];
        } else {
            return FALSE;
        }
    }
    //判断是输入的文章ID还是关键词
    if (is_numeric($newsid)) {
        //指定了源文章
        $Dao = M('ArticleRelation');
        $condition = array();
        $condition['sourceid'] = $newsid;
        $rs = $Dao->field('`targetid`')->limit($limit)->select();
        if ($rs) {
            //建立了主动关联
            $Dao = M('Article');
            $condition = array();
            $order = '`listorder` ASC,`id` DESC';
            $condition['status'] = 99;
            $condition['lanid'] = $lang;
            $condition['id'] = array('in', $rs);
            $rs = $Dao->where($condition)->field('`title`,`style`,`inputtime`,`url`')->limit($limit)->order($order)->select();
            return $rs;
        } else {
            //未建立主动关联，根据关键词来设置
            $Dao = M('Article');
            $condition = array();
            $condition['id'] = $newsid;
            $condition['status'] = 99;
            $condition['lanid'] = $lang;
            $rs = $Dao->where($condition)->field('`keywords`')->find();
            if ($rs) {
                $keywords = $rs['keywords'];
            } else {
                return FALSE;
            }
        }
    } else {
        $keywords = $newsid;
    }
    //根据关键词来自动匹配相关文章
    $Dao = M('ArticleKeywords');
    $condition = array();
    $condition['keywords'] = array(explode(',', $keywords), 'or');
    $rs = $Dao->where($condition)->field('`newsid`')->select();
    if ($ids) {



        $condition['id'] = array('in', $ids);
    } else {
        $condition['catid'] = intval($catid);
        $condition['status'] = 99;
        if ($keywords) {
            $kw = array();
            foreach ($keywords as $v) {
                $kw[] = '%' . $v . '%';
            }
            $condition['keywords'] = array('like', $kw, 'OR');
            $order = '`listorder` DESC,`id` DESC';
        } else {
            $order = 'RAND()';
        }
        $rs = $Dao->where($condition)->field('`title`,`style`,`inputtime`,`url`')->limit($limit)->order($order)->select();
        return $rs;
    }
}

/**
 * 文章点击记数
 * 
 */
function setNewsHits($newsId, $catid = 0, $modelId = '') {
    $Dao = M('Hits');
    $data = array();
    $data['newsid'] = $newsId;
    $data['catid'] = $catid;
    $data['modelid'] = $modelId;
    $where = $data;
    $old = $Dao->field('`views`,`yesterdayviews`,`dayviews`,`weekviews`,`monthviews`,`updatetime`')->where($where)->find();
    if ($old == null) {
        $data['views'] = 1;
        $data['yesterdayviews'] = 1;
        $data['dayviews'] = 1;
        $data['weekviews'] = 1;
        $data['monthviews'] = 1;
        $data['updatetime'] = time();
        return $Dao->data($data)->add();
    }
    import('ORG.Util.Date');
    $Date = new Date(date('Y-m-d H:i:s', $old['updatetime']));
    $d = $Date->dateDiff(date('Y-m-d H:i:s'));
    if ($d < 1)
        $data['dayviews'] = $old['dayviews'] + 1;
    if ($d >= 1 && $d < 2) {
        $data['dayviews'] = 1;
        $data['yesterdayviews'] = $old['yesterdayviews'] + 1;
    }
    $w = $Date->dateDiff(date('Y-m-d H:i:s'), 'w');
    if ($w < 1) {
        $data['weekviews'] = $old['weekviews'] + 1;
    } else {
        $data['weekviews'] = 1;
    }
    $M = $Date->dateDiff(date('Y-m-d H:i:s'), 'M');
    if ($M < 1) {
        $data['monthviews'] = $old['monthviews'] + 1;
    } else {
        $data['monthviews'] = 1;
    }
    $data['views'] = $old['views'] + 1;
    $data['updatetime'] = time();
    $data['catid'] = $catid;
    return $Dao->data($data)->where($where)->save();
}