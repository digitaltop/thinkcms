<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2011-7-5 14:39:54 by ZhengYi , 13880273017@139.com
 */
class MenuModel extends Model {

    protected $trueTableName = 'system_menu';
    //检验表单
    protected $_validate = array(
        //array('parentid', '_checkVal', '必须选择一个菜单！', 1, 'function'), //顶级菜单必须手工添加数据库
        array('menu_name', 'require', '后台使用的菜单名称必填！'),
    );
    //自动完成
    protected $_auto = array(
        array('create_time', 'getNowTime', 1, 'function'),
    );
    protected $padding = array(
        0 => '',
        1 => '&nbsp;&nbsp;├',
        2 => '&nbsp;&nbsp;&nbsp;&nbsp;├',
        3 => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├',
        4 => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├',
        5 => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├');
    protected $Color = array(
        0 => 'FFA00D',
        1 => '55555',
        2 => '999999',
        3 => 'FFA00D',
        4 => '55555',
        5 => '999999');
    private $list = array();
    private $IDS = '';

    //取得大类，递归
    protected function _getThisClassName($ID, $status, $per) {
        $condition = array();
        $condition['parentid'] = $ID;
        if ($status == 1) {
            $condition['status'] = $status;
        }
        if ($per == 1) {
            $condition['permis'] = 1;
        }
        $Q = $this->where($condition)->order('`listorder` asc,`menu_id` asc')->select();

        if (!$Q) {
            return;
        } else {
            foreach ($Q as $cat) {
                $listorder = 0;
                if ($cat['parentid'] > 0) {
                    $UPID = $cat['parentid'];
                    while (!($UPID == 0)) {
                        $listorder++;
                        $where = array();
                        $where['menu_id'] = $UPID;
                        if ($status == 1) {
                            $where['status'] = $status;
                        }
                        if ($per == 1) {
                            $where['permis'] = 1;
                        }
                        $cate = $this->where($where)->select();
                        $UPID = $cate[0]['parentid'];
                    }
                }
                $cat['fulltitle'] = $this->padding[$listorder];
                $cat['color'] = $this->Color[$listorder];
                $this->list[] = $cat;
                $this->_getThisClassName($cat['menu_id'], $status, $per);
            }
        }
    }

    //取得大类下所有子类
    //参数：上级菜单ID，是否显示所有数据，默认显示,是否需要授权:默认不需要
    //返回：包括子类的所有数据
    public function getClassName($class_ID, $status = 0, $per = 0) {
        $class_ID = intval($class_ID);
        $status = intval($status);
        $this->list = array(); //首先清除原数据
        $this->_getThisClassName($class_ID, $status, $per);
        $list = &$this->list;
        return $list;
    }

    //取得ID子集，递归
    //参数：上级菜单ID，是否显示所有数据，默认显示
    protected function _getThisClassID($ID, $status = 0) {
        $condition = array();
        $condition['parentid'] = $ID;
        if ($status == 1) {
            $condition['status'] = $status;
        }
        $Q = $this->where($condition)->field('`menu_id`')->order('`listorder` asc,`menu_id` asc')->select();
        if (false === $Q)
            return false;
        foreach ($Q as $id) {
            $thisid = $id['menu_id'];
            $this->IDS.=$thisid . ',';
            $this->_getThisClassID($thisid, $status);
        }
    }

    //取得子类下所有ID
    //返回：子类
    //参数：上级菜单ID，是否显示所有数据，默认显示，是否包含自身，默认包含
    public function getClassID($action_title, $status = 0, $myself = 0) {
        //exit(dump($action_title));
        $condition = array();
        is_numeric($action_title) ? $condition['menu_id'] = $action_title : $condition['action_title'] = $action_title;
        if ($status == 1) {
            $condition['status'] = $status;
        }
        $Q = $this->where($condition)->field('`menu_id`')->find();
        if ($Q) {
            $id = $Q['menu_id'];
            if ($myself == 1) {
                $this->IDS.=$id . ',';
            }
            //exit(dump($id));
            $this->_getThisClassID($id, $status);
        }
        if ($this->IDS !== '') {
            $this->IDS = substr($this->IDS, 0, strlen($this->IDS) - 1);
        }//exit(dump($this->IDS));
        return $this->IDS;
        //exit(dump($this->IDS));
    }

}