<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2011-7-6 11:59:19 by ZhengYi , 13880273017@139.com
 */
class DepartmentAction extends GlobalAction {
    /*
     * 初始化
     */

    public function _initialize() {
        parent::_initialize();
    }

    /*
     * 部门管理
     */

    public function listDepartment() {
        $this->display();
    }

    /*
     * 保存部门信息
     */

    public function saveDepartment() {
        $ID = intval($_REQUEST['dept_id']);
        $Dao = D('Department');

        $where = array();
        $ac = 1;
        $where['dept_name'] = trim($_REQUEST['dept_name']);
        $where['parentid'] = intval($_COOKIE['parentid']);
        $rs = $Dao->where($where)->count();
        if ($ID < 1 && $rs > 0) {
            $this->error('在当前部门下，已有一个部门名称为' . trim($_REQUEST['dept_name']) . '的部门，请重新设置。');
            exit();
        }

        if ($Dao->create()) {
            ($ID > 0) ? $Re = $Dao->save() : $Re = $Dao->add();
            if ($Re) {
                $this->assign("jumpUrl", U('Model/listDepartment'));
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        } else {
            //dump($_REQUEST);
            $this->error($Dao->getError());
        }
    }

    /*
     * 获取部门树
     */

    public function getDepartment() {
        $pid = intval($_REQUEST['pid']);
        $ac = intval($_REQUEST['ac']); //1，只显示部门ID及部门名称,0显示全部信息
        $Dao = D('Department');
        $rs = $Dao->getClassName($pid);
        if ($ac == 1) {
            $i = 0;
            $re = array();
            if ($pid == 0) {
                $re[$i]['InfoTypeName'] = '最上级';
                $re[$i]['InfoTypeID'] = '0';
            }
            foreach ($rs as $v) {
                $re[$i]['InfoTypeName'] = str_replace('&nbsp;', ' ', $v['fulltitle']) . $v['dept_name'];
                $re[$i]['InfoTypeID'] = $v['dept_id'];
                $i++;
            }
            echo json_encode($re);
            exit();
        } else {
            echo json_encode($rs);
        }
    }

    /*
     * 编辑部门
     */

    public function editDepartment() {
        $id = intval($_REQUEST['dept_id']);
        $Dao = D('Department');
        $rs = $Dao->where('`dept_id`=' . $id)->find();
        echo(json_encode($rs));
    }

    /*
     * 删除部门信息
     */

    public function deleteDepartment() {
        $id = intval($_REQUEST['dept_id']);
        $Dao = D('Department');
        $rs = $Dao->delete($id);
        if ($rs) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败:' . $Dao->getError());
        }
    }

}