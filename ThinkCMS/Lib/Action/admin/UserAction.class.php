<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2011-7-6 14:59:02 by ZhengYi , 13880273017@139.com
 */
class UserAction extends GlobalAction {
    /*
     * 初始化
     */

    public function _initialize() {
        parent::_initialize();
    }

    /*
     * 用户管理
     */

    public function listUser() {
        $this->display();
    }

    /*
     * 保存用户信息
     */

    public function saveUser() {
        $ID = intval($_REQUEST['user_id']);
        $Dao = D('User');

        $where = array();
        $ac = 1;
        $where['user_name'] = trim($_REQUEST['user_name']);
        $rs = $Dao->where($where)->count();
        if ($ID < 1 && $rs > 0) {
            $this->error('已有一个ID=' . $ID . '用户名为' . trim($_REQUEST['user_name']) . '的用户，请重新设置。');
            exit();
        }

        //如果密码不为空
        if (trim($_REQUEST['password']) !== '') {
            $auto = array(
                array('password', 'passWd', 3, 'function')
            );
            $Dao->setProperty('_auto', $auto);
        } elseif ($ID > 0) {
            $rs = $Dao->where('`user_id`=' . $ID)->field('`password`')->find();
            $oldPasswd = $rs['password'];
        }

        if ($Dao->create()) {
            if ($ID > 0 && trim($_REQUEST['password']) == '') {
                $Dao->password = $oldPasswd;
            }
            ($ID > 0) ? $Re = $Dao->save() : $Re = $Dao->add();
            //if ($Re) {
            //如果保存，则取输入ID，否则取上次插入的ID
            ($ID > 0) ? $userID = $ID : $userID = $Dao->getLastInsID();

            //保存辅助角色时，首先清除原来的角色信息
            cleanUserRole($userID);
            if (strlen(trim($_REQUEST['user_role'])) > 0) {
                //需要添加辅助角色
                $a = insertUserRole($userID, trim($_REQUEST['user_role']));
            }

            //保存其它所属部门时，首先清除原来的其它所属部门信息
            cleanUserDepartment($userID);
            if (strlen(trim($_REQUEST['user_department'])) > 0) {
                //需要添加辅助角色
                insertUserDepartment($userID, trim($_REQUEST['user_department']));
            }

            $this->assign("jumpUrl", U('Model/listUser'));
            $this->success('保存成功');
            //} else {
            //    $this->error('数据未做任何修改或保存时发生错误：' . $Dao->getError());
            // }
        } else {
            //dump($_REQUEST);
            $this->error($Dao->getError());
        }
    }

    /*
     * 获取用户列表
     */

    public function getUser() {
        $Dao = D('User');
        $rs = $Dao->order('`listorder` asc,`user_id` desc')->select();
        echo json_encode($rs);
    }

    /*
     * 修改用户信息
     */

    public function editUser() {
        $id = intval($_REQUEST['user_id']);
        $Dao = D('User');
        $rs = $Dao->where('`user_id`=' . $id)->find();

        //辅助角色
        $user_role = '';
        $supportingRole = '';
        if ($rs['user_id']) {
            $Dao = D('UserRoleView');
            $userRole = $Dao->where('User_role.`user_id`=' . $id)->select();
            //echo($Dao->getLastSql());
            foreach ($userRole as $k => $v) {
                $user_role.=',' . $v['user_id'];
                $supportingRole.=',' . $v['role_name'];
            }
        }
        (strlen($user_role) > 1) ? $specifiedSecondaryValue = 1 : $specifiedSecondaryValue = 0;
        $rs['user_role'] = $user_role;
        $rs['supportingRole'] = $supportingRole;
        $rs['specifiedSecondaryValue'] = $specifiedSecondaryValue;

        //其它部门
        $user_department = '';
        $supportingDepartment = '';
        if ($rs['user_id']) {
            $Dao = D('UserDepartmentView');
            $userRole = $Dao->where('User_department.`user_id`=' . $id)->select();
            //echo($Dao->getLastSql());
            foreach ($userRole as $k => $v) {
                $user_department.=',' . $v['dept_id'];
                $supportingDepartment.=',' . $v['dept_name'];
            }
        }
        (strlen($user_department) > 1) ? $specifiedSecondaryDepartmentValue = 1 : $specifiedSecondaryDepartmentValue = 0;
        $rs['user_department'] = $user_department;
        $rs['supportingDepartment'] = $supportingDepartment;
        $rs['specifiedSecondaryDepartmentValue'] = $specifiedSecondaryDepartmentValue;
        //dump($rs);
        echo(json_encode($rs));
    }

    /*
     * 删除用户信息
     */

    public function deleteUser() {
        $id = intval($_REQUEST['user_id']);
        $Dao = D('User');
        $data = array();
        $data['user_id'] = $id;
        $data['status'] = 0;
        $rs = $Dao->data($data)->save();
        if ($rs) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败:' . $Dao->getError());
        }
    }

}