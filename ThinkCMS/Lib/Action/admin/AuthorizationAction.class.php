<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2011-7-6 14:57:46 by ZhengYi , 13880273017@139.com
 */
class AuthorizationAction extends GlobalAction {
    /*
     * 初始化
     */

    public function _initialize() {
        parent::_initialize();
    }

    /*
     * 按角色授权
     */

    public function roleAuthorization() {
        $action = trim($_REQUEST['action']);
        $role_id = intval($_REQUEST['role_id']);
        if ($action == 'save') {
//保存授权
//清除授权信息
            cleanRoleAuthorization($role_id);
            $Dao = D('Role_menu_permis');
            $perid = explode(',', $_REQUEST['mdata']);
//保存
            foreach ($perid as $k => $v) {
                $data = array();
                $data['role_id'] = $role_id;
                $data['menu_id'] = $v;
                $Dao->data($data)->add();
            }
            $this->success('保存成功');
        } else {
//授权列表
            if ($role_id > 0) {
                $ra = array();
                $rai = array();
                $Dao = D('Menu');
                $rs = $Dao->getClassName(0, 0, 1);
                $ra['total'] = count($rs);
                foreach ($rs as $k => $v) {
                    $rai[$k]['menu_index'] = $k;
                    $rai[$k]['menu_title'] = $v['fulltitle'] . $v['menu_title'];
                    $rai[$k]['menu_id'] = $v['menu_id'];
                    if (rolePermisAuth($role_id, $v['menu_id'])) {
                        $rai[$k]['menu_checked'] = '1';
                    } else {
                        $rai[$k]['menu_checked'] = '0';
                    }
                }
                $ra['rows'] = $rai;
                echo(json_encode($ra));
            } else {

                $this->display();
            }
        }
    }

    /*
     * 按部门授权
     */

    public function departmentAuthorization() {
        $action = trim($_REQUEST['action']);
        $dept_id = intval($_REQUEST['dept_id']);
        if ($action == 'save') {
//保存授权
//清除授权信息
            cleanDeptAuthorization($dept_id);
            $Dao = D('Depart_menu_permis');
            $perid = explode(',', $_REQUEST['mdata']);
//保存
            foreach ($perid as $k => $v) {
                $data = array();
                $data['dept_id'] = $dept_id;
                $data['menu_id'] = $v;
                $Dao->data($data)->add();
            }
            $this->success('保存成功');
        } else {
//授权列表
            if ($dept_id > 0) {
                $ra = array();
                $rai = array();
                $Dao = D('Menu');
                $rs = $Dao->getClassName(0, 0, 1);
                $ra['total'] = count($rs);
                foreach ($rs as $k => $v) {
                    $rai[$k]['menu_index'] = $k;
                    $rai[$k]['menu_title'] = $v['fulltitle'] . $v['menu_title'];
                    $rai[$k]['menu_id'] = $v['menu_id'];
                    if (departmentPermisAuth($dept_id, $v['menu_id'])) {
                        $rai[$k]['menu_checked'] = '1';
                    } else {
                        $rai[$k]['menu_checked'] = '0';
                    }
                }
                $ra['rows'] = $rai;
                echo(json_encode($ra));
            } else {

                $this->display();
            }
        }
    }

    /*
     * 按用户授权
     */

    public function userAuthorization() {
        $action = trim($_REQUEST['action']);
        $user_id = intval($_REQUEST['user_id']);
        if ($action == 'save') {
//保存授权
//清除授权信息
            cleanUserAuthorization($user_id);
            $Dao = D('User_menu_permis');
            $perid = explode(',', $_REQUEST['mdata']);
//保存
            foreach ($perid as $k => $v) {
                $data = array();
                $data['user_id'] = $user_id;
                $data['menu_id'] = $v;
                $Dao->data($data)->add();
            }
            $this->success('保存成功');
        } else {
//授权列表
            if ($user_id > 0) {
                $ra = array();
                $rai = array();
                $Dao = D('Menu');
                $rs = $Dao->getClassName(0, 0, 1);
                $ra['total'] = count($rs);
                foreach ($rs as $k => $v) {
                    $rai[$k]['menu_index'] = $k;
                    $rai[$k]['menu_title'] = $v['fulltitle'] . $v['menu_title'];
                    $rai[$k]['menu_id'] = $v['menu_id'];
                    if (userPermisAuth($user_id, $v['menu_id'])) {
                        $rai[$k]['menu_checked'] = '1';
                    } else {
                        $rai[$k]['menu_checked'] = '0';
                    }
                }
                $ra['rows'] = $rai;
                echo(json_encode($ra));
            } else {

                $this->display();
            }
        }
    }

}