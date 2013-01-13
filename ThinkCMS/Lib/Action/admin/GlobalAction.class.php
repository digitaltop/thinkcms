<?php

/*
 * 全局方法
 */

class GlobalAction extends Action {

    protected $CATEGORYS; //栏目缓存
    protected $UserID; //用户ID
    protected $UserName; //用户名_指登录的用户名
    protected $UserDeptId; //用户部门，字符串，如1,2,3
    protected $UserNickName; //用户真实姓名
    protected $UserThemes; //用户选择的主题
    protected $UserLoginCounts; //用户登录次数
    protected $choseLanguage; //选择的当前语言ID
    protected $Page; //分页情况

    public function _initialize() {
        header('Content-type: text/html; charset=utf-8');
        //初始数据
        if (!(Session::get('USER_CHOSELANGUAGE')))
            Session::set('USER_CHOSELANGUAGE', 1);
        $this->UserID = Session::get('USER_ID'); //用户ID
        $this->UserName = Session::get('USER_NAME'); //用户名(英文)
        $this->UserDeptId = Session::get('USER_DEPT_ID'); //用户主部门ID
        $this->UserNickName = Session::get('USER_NICKNAME'); //用户真实姓名
        $this->UserLoginCounts = Session::get('USER_LOGINCOUNT'); //登录次数
        $this->choseLanguage = Session::get('USER_CHOSELANGUAGE'); //语言ID
        //如果用户未登录，则进入登录页
        if (!($this->UserID) || $this->UserID < 1) {
            $this->redirect('Public/login');
        }

        //如果用户已登录，则判断当前MODEL是否需要验证
        //echo('uid='.Session::get('USER_ID').'            m='.MODULE_NAME.'            a='.ACTION_NAME);exit();
//        if (((MODULE_NAME !== 'Public') || (MODULE_NAME !== 'Global')) && $this->UserID > 0) {
//            if (!(checkSecurity($this->UserID, MODULE_NAME, ACTION_NAME))) {
//                echo('系统未授权您访问该模块：'.MODULE_NAME.'/'.ACTION_NAME.'<br/>请联系您的系统管理员。');
//                exit();
//            }
//        }

        $this->assign('thisURL', __URL__ . '/' . ACTION_NAME);
        $this->CATEGORYS = getCacheCategory();

        //分页情况
        $numPerPage = intval($_REQUEST['rows']);
        if ($numPerPage < 1)
            $numPerPage = 10;
        $pagePaper = C('VAR_PAGE') ? C('VAR_PAGE') : 'page';
        $this->Page['numPerPage'] = $numPerPage; //每页显示多少条
        $this->Page['currentPage'] = !empty($_REQUEST[$pagePaper]) ? intval($_REQUEST[$pagePaper]) : 1; //当前页
        if ($_REQUEST['sort'])
            $this->Page['orderField'] = $_REQUEST['sort'];
        if ($_REQUEST['order'])
            $this->Page['orderDirection'] = $_REQUEST['order'];
        $this->Page['totalCount'] = 0;
        $this->assign('page', $this->Page);

        //查看是否有JS文件
        $filepatch = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . APP_PATH . '/Tpl/' . GROUP_NAME . '/' . MODULE_NAME . '/js/' . ACTION_NAME . '.js');
        if (@file_exists($filepatch)) {
            //$this->assign('jsfile', '<script src="' . U('Public/js', array('src' => MODULE_NAME . '-' . ACTION_NAME)) . '"></script>');
            $this->assign('jsfile', '<script src="' . str_replace('./', '/', APP_PATH) . 'Tpl/' . GROUP_NAME . '/' . MODULE_NAME . '/js/' . ACTION_NAME . '.js"></script>');
        }
        $this->assign('userLoginCounts', $this->UserLoginCounts);
        $this->assign('userNickName', $this->UserNickName);
    }

    /**
      +----------------------------------------------------------
     * 根据查询条件，返回JSON结果
     * 
      +----------------------------------------------------------
     * @access protected
      +----------------------------------------------------------
     * @param string $table 数据对象名称
      +----------------------------------------------------------
     * @param array $condition 查询条件
     * @param array $field 需要查询的字段
     * @param array $order 排序
     * @param array $page 分页
     * @return HashMap
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    protected function search($table, $condition = '', $field = '') {
        $Dao = D($table);
        $count = $Dao->where($condition)->count();
        $order = ($this->Page['orderField'] && $this->Page['orderDirection']) ? $this->Page['orderField'] . ' ' . $this->Page['orderDirection'] : '';
        if ($field == '') {
            $vo = $Dao->where($condition)->page($this->Page['currentPage'], $this->Page['numPerPage'])->order($order)->select();
        } else {
            $vo = $Dao->where($condition)->field($field)->page($this->Page['currentPage'], $this->Page['numPerPage'])->order($order)->select();
        }
        $this->Page['totalCount'] = $count;
        $this->assign('page', $this->Page); //echo($Dao->getLastSql());
        if (is_null($vo))
            $vo = '';
        exit(json_encode(array('total' => $count, 'rows' => $vo)));
    }

    /**
     * AJAX输出
     * @param type $status
     * @param type $message
     * @param type $forwardUrl
     */
    protected function uiReturn($status = TRUE, $message = '操作成功', $forwardUrl = '', $other = '') {
        uiReturn($status, $message, $forwardUrl, $other);
    }

    /**
     * 通用删除方法，适用于有主键的，如果没有主键，则附加ID字段名
     * @param string $table 表名
     * @param array $where需要删除的条件 如果是数组，则直接使用，否则使用 id|1,2,3，如果主键为空，则自动获取主键
     * @param string $url 为返回结果，否则返回详细信息
     */
    protected function _del($table, $where, $url = 0) {
        $db = M($table);
        if (is_array($where)) {
            $condition = $where;
        } else {
            $x = explode('|', $where);
            if (count($x) == 2) {
                $idField = $x[0];
                $ids = $x[1];
            } else {
                $idField = $db->getPk();
                $ids = $x[0];
            }
            $condition = array();
            $condition[$idField] = array('in', $ids);
            if (!$ids) {
                $this->uiReturn(false, '未选择记录');
            }
        }
        $res = $db->where($condition)->delete();//echo($db->getLastSql());
        if ($url == 1) {
            return $res;
        } else if ($res) {
            $this->uiReturn(true, '删除成功！', '', '', '');
        } else {
            $this->uiReturn(false, '删除失败：' . $db->getError());
        }
    }

    //公共添加数据方法
    protected function _add($table, $data_merge, $url) {
        $db = M($table);
        $data = $db->create();
        if (!empty($data_merge)) {
            $data = array_merge($data, $data_merge); //合并data追加的数组数据
        }
        if ($data) {
            //print_r($data);
            $result = $db->add($data);
            //echo(chr(10) . $db->getLastSql());
            if (false !== $result) {
                if ((int) $url == 1) {  //当$url  参数为1时，返回出来  $result的值,进行继续处理
                    return $result;
                } else {
                    $this->uiReturn(true, '添加成功！', $url);
                }
            } else {
                $this->uiReturn(FALSE, '添加失败！' . $db->getDbError(), $url);
            }
        }
    }

    /* 参数说明如下
     * table	  : 数据库表名，必选参数
      $data    :  外界传递过来的条件  id为create 自动获取
     * url	      : 操作成功之后跳转的url，可选参数
     * $reque 1对数组进行叠加 0不叠加
     */

    //公共更新方法
    protected function _update($table, $data, $url, $reque = 1) {
        $db = M($table);
        $vo = $db->create();
        if (!empty($data)) {
            ($reque == 1) ? $vo = array_merge($vo, $data) : $vo = $data; //当 $data不为空时，合并外界传递过来的数组
        }
        if ($vo) {
            $result = $db->save($vo);
            //echo(chr(10) . $db->getLastSql());
            if (false !== $result) {
                if ((int) $url == 1) {  //当$url  参数为1时，返回出来  $result的值,进行继续处理
                    return $result;
                } else {
                    $this->uiReturn(true, '修改成功！', $url);
                }
            } else {
                $this->uiReturn(FALSE, '修改失败！' . $db->getDbError(), $url);
            }
        }
    }

}