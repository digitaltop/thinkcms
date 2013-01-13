<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2011-7-6 14:51:41 by ZhengYi , 13880273017@139.com
 */
class RoleAction extends GlobalAction {
    /*
     * 初始化
     */

    public function _initialize() {
        parent::_initialize();
    }

    /*
     * 角色管理
     */

    public function listRole() {
        $this->display();
    }

    /*
     * 保存角色
     */

    public function saveRole() {
        $ID = intval($_REQUEST['role_id']);
        $Dao = D('Role');

        $where = array();
        $ac = 1;
        $where['role_name'] = trim($_REQUEST['role_name']);
        $rs = $Dao->where($where)->count();
        if ($ID < 1 && $rs > 0) {
            $this->error('已有一个角色名称为' . trim($_REQUEST['dept_name']) . '的角色，请重新设置。');
            exit();
        }

        if ($Dao->create()) {
            ($ID > 0) ? $Re = $Dao->save() : $Re = $Dao->add();
            if ($Re) {
                $this->assign("jumpUrl", U('Model/listRole'));
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
     * 获取角色列表
     */

    public function getRole() {
        $ac = intval($_REQUEST['ac']); //返回类型0为所有数据，1为下接框数据格式
        $Dao = D('Role');
        $rs = $Dao->order('`listorder` asc,`role_id` desc')->select();
        if ($ac == 1) {
            $res = array();
            foreach ($rs as $k => $v) {
                $res[$k]['InfoTypeName'] = $v['role_name'];
                $res[$k]['InfoTypeID'] = $v['role_id'];
            }
            echo(json_encode($res));
        } else {
            echo json_encode($rs);
        }
    }

    /*
     * 修改角色信息
     */

    public function editRole() {
        $id = intval($_REQUEST['role_id']);
        $Dao = D('Role');
        $rs = $Dao->where('`role_id`=' . $id)->find();
        echo(json_encode($rs));
    }

    /*
     * 删除角色信息
     */

    public function deleteRole() {
        $id = intval($_REQUEST['role_id']);
        $Dao = D('Role');
        $rs = $Dao->delete($id);
        if ($rs) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败:' . $Dao->getError());
        }
    }

}