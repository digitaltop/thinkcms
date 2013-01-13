<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2011-6-29 12:19:06 by ZhengYi , 13880273017@139.com
 */
class DepartmentModel extends Model {

    protected $trueTableName = 'system_department';
    
    protected $_validate = array(
        array('dept_name', 'require', '部门名称必填！'),
    );
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
    protected $IDS = '';

    //取得大类，递归
    protected function _getThisClassName($ID) {
        $condition = array();
        $condition['parentid'] = $ID;
        $Q = $this->where($condition)->order('`listorder` asc,`dept_id` asc')->select();

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
                        $where['dept_id'] = $UPID;
                        $cate = $this->where($where)->select();
                        $UPID = $cate[0]['parentid'];
                    }
                }
                $cat['fulltitle'] = $this->padding[$listorder];
                $cat['color'] = $this->Color[$listorder];
                $this->list[] = $cat;
                $this->_getThisClassName($cat['dept_id']);
            }
        }
    }

    //取得大类下所有子类
    //返回：包括子类的所有数据
    public function getClassName($class_ID) {
        $class_ID = intval($class_ID);
        $this->list = array(); //首先清除原数据
        $this->_getThisClassName($class_ID);
        $list = &$this->list;
        return $list;
    }

    //取得ID子集，递归
    protected function _getThisClassID($ID) {
        $condition = array();
        $condition['parentid'] = $ID;
        $Q = $this->where($condition)->field('`dept_id`')->order('`listorder` asc,`dept_id` asc')->select();
        if (false === $Q)
            return false;
        foreach ($Q as $id) {
            $thisid = $id['dept_id'];
            $this->IDS.=$thisid . ',';
            $this->_getThisClassID($thisid);
        }
    }

    //取得子类下所有ID
    //返回：子类
    public function getClassID($action_title) {
        //exit(dump($action_title));
        $condition = array();
        is_numeric($action_title) ? $condition['dept_id'] = $action_title : $condition['dept_name'] = $action_title;
        $Q = $this->where($condition)->field('`dept_id`')->find();
        //exit(dump($Q['id']));
        if ($Q) {
            $id = $Q['dept_id'];
            $this->IDS.=$id . ',';
            //exit(dump($id));
            $this->_getThisClassID($id);
        }
        if ($this->IDS !== '') {
            $this->IDS = substr($this->IDS, 0, strlen($this->IDS) - 1);
        }//exit(dump($this->IDS));
        return $this->IDS;
        //exit(dump($this->IDS));
    }

}